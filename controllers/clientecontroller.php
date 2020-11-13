<?php

class ClienteController extends ApplicationGeneral {

    /**
     * Vista Inicial de Clientes:
     */
    public function index() {
    
    }

    function listavistageneral() {
        $cliente = $this->AutoLoadModel('cliente');
        $zona = $this->AutoLoadModel('zona');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_Cliente'] = "";
        $data['Cliente'] = $cliente->listaClientesPaginado($_REQUEST['id']);
        for ($i = 0; $i < count($data['Cliente']); $i++) {
            if ($data['Cliente'][$i]['zona'] != '' && $data['Cliente'][$i]['zona'] != 0) {
                $data['Cliente'][$i]['zona'] = $zona->nombrexid($data['Cliente'][$i]['zona']);
            }
        }
        $paginacion = $cliente->paginadoClientes();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/cliente/listavistageneral.phtml", $data);
    }

    function listavistageneralelegir() {
        $id = $_REQUEST['id'];
        $cliente = $this->AutoLoadModel('cliente');
        $zona = $this->AutoLoadModel('zona');
        $Cliente = $cliente->listaClientesPaginado(1, $id);
        $Cliente[0]['zona'] = $zona->nombrexid($Cliente[0]['zona']);
        echo "<tr>";
        echo    "<td>" . $Cliente[0]['razonsocial'] . "</td>";
        echo    "<td>" . $Cliente[0]['dni'] . "</td>";
        echo    "<td>" . $Cliente[0]['ruc'] . "</td>";
        echo    "<td>" . $Cliente[0]['zona'] . "</td>";
        echo    "<td>" . $Cliente[0]['direccion'] . "</td>";
        echo    "<td>" . $Cliente[0]['email'] . "</td>";
        echo    "<td>" . $Cliente[0]['telefono'] . "</td>";
        echo    "<td>" . $Cliente[0]['celular'] . "</td>";
        echo    "<td><a href='#' class='verDetalle' data-id='" . $Cliente[0]['idcliente'] . "'><b>VER</b></a></td>";
        echo "</tr>";
    }

    function listavistageneralver() {
        //if ($_SESSION['nivelacceso']==1) {
        $id = $_REQUEST['id'];
        $cliente = new Cliente();
        $transporte = new Transporte();
        $clientezona = $this->AutoLoadModel('clientezona');
        $clienteVendedor = $this->AutoLoadModel('clientevendedor');
        $vendedor = $this->AutoLoadModel('actor');
        $Cliente = $cliente->buscaCliente($id);
        $TipoCliente = $this->tipoCliente();
        $DatosGenerales = "";
        if (count($Cliente) > 0) {
            $distrito = new Distrito();
            $provincia = new Provincia();
            $departamento = new Departamento();
            $dataDistrito = $distrito->buscarxid($Cliente[0]['iddistrito']);
            $listDepartamento = $departamento->listado();
            $listProvincia = $provincia->listado($dataDistrito[0]['codigodepto']);
            $DatosGenerales .= "<div class='CuadroCliente'>" .
                                "<h4>Datos Principales: </h4>" .
                                "<ul>" .
                                    "<li><label>Codigo: </label><span>" . $Cliente[0]['codcliente'] . "</span></li>" .
                                    "<li><label>C. Dakkars: </label><span>" . $Cliente[0]['codantiguo'] . "</span></li>" .
                                    "<li><label>Tipo Cliente: </label><span>" . $TipoCliente[$Cliente[0]['tipocliente']] . "</span></li>";
            if ($Cliente[0]['tipocliente'] == 1) {
                $DatosGenerales .= "<br>" .
                                    "<li><label>Nombres y Apellidos: </label><span>" . $Cliente[0]['nombrecli'] . " " . $Cliente[0]['apellido1'] . " " . $Cliente[0]['apellido2'] . "</span></li>" .
                                    "<li><label>DNI: </label><span>" . $Cliente[0]['dni'] . "</span></li>";
            }
            $DatosGenerales .= "<br>" .
                                "<li><label>Razon Social: </label><span>" . $Cliente[0]['razonsocial'] . "</span></li>" .
                                "<li><label>R.U.C.: </label><span>" . (!empty($Cliente[0]['ruc']) ? $Cliente[0]['ruc'] : "_____") . "</span></li>";
            $DatosGenerales .= "<br>" .
                                "<li><label>Nombre Comercial: </label><span>" . (!empty($Cliente[0]['nombrecomercial']) ? $Cliente[0]['nombrecomercial'] : "_______________") . "</span></li>";
            if (!empty($Cliente[0]['nombre_contacto'])) {
                $DatosGenerales .= "<br>" .
                        "<li><label>Nombre Contacto: </label><span>" . $Cliente[0]['nombre_contacto'] . "</span></li>";
            }
            $dataJason[0]['idCliente'] = $id;
            $dataJason[0]['DatosTelefono'] = $Cliente[0]['telefono'];
            $dataJason[0]['DatosCelular'] = $Cliente[0]['celular'];
            $dataJason[0]['DatosEmail'] = (!empty($Cliente[0]['email']) ? $Cliente[0]['email'] : '') . (!empty($Cliente[0]['email2']) ? " / " . $Cliente[0]['email2'] : (empty($Cliente[0]['email']) ? '' : ''));
            $DatosGenerales .= "<br>" .
                                "<li><label>Fijo: </label><span>" . (!empty($Cliente[0]['telefono']) ? $Cliente[0]['telefono'] : '_____') . "</span></li>" .
                                "<li><label>Celular: </label><span>" . (!empty($Cliente[0]['celular']) ? $Cliente[0]['celular'] : '_____') . "</span></li>";
            if (!empty($Cliente[0]['paginaweb'])) {
                $DatosGenerales .= "<br>" .
                                    "<li><label>Pagina Web: </label><span><a href='" . $Cliente[0]['paginaweb'] . "' target='_blank'>" . $Cliente[0]['paginaweb'] . "</a></span></li>";
            }
            $DatosGenerales .= "<br>";
            if (!empty($Cliente[0]['horarioatencion'])) {
                $DatosGenerales .= "<li><label>Atencion: </label><span>" . $Cliente[0]['horarioatencion'] . "</span></li>";
            }
            $DatosGenerales .= "<li><label>Email: </label><span>" . (!empty($Cliente[0]['email']) ? $Cliente[0]['email'] : '') . (!empty($Cliente[0]['email2']) ? " / " . $Cliente[0]['email2'] : (empty($Cliente[0]['email']) ? '_______' : '')) . "</span></li>";
            $DatosGenerales .= "</ul>" .
                            "</div>" .
                            "<div class='CuadroCliente'>" .
                            "<h4>Datos de Ubicación: </h4>" .
                            "<ul>" .
                            "<li><label>Dirección: </label><span>" . $Cliente[0]['direccion'] . "</span></li><br>" .
                            "<li><label>Dirección Despacho: </label><span>" . $Cliente[0]['direccion_despacho_cliente'] . "</span></li><br>" .
                            "<li><label>Departamento - Provincia - Distrito: </label><span>";
            for ($i = 0; $i < count($listDepartamento); $i++) {
                if ($dataDistrito[0]['codigodepto'] == $listDepartamento[$i]['iddepartamento']) {
                    $DatosGenerales .= $listDepartamento[$i]['nombredepartamento'] . " - ";
                }
            }
            for ($i = 0; $i < count($listProvincia); $i++) {
                if ($dataDistrito[0]['idprovincia'] == $listProvincia[$i]['idprovincia']) {
                    $DatosGenerales .= $listProvincia[$i]['nombreprovincia'] . " - ";
                }
            }
            for ($i = 0; $i < count($dataDistrito); $i++) {
                if ($Cliente[0]['iddistrito'] == $dataDistrito[$i]['iddistrito']) {
                    $DatosGenerales .= $dataDistrito[$i]['nombredistrito'];
                }
            }
            $DatosGenerales .= "</span></li><br>";
            $zona = $this->AutoLoadModel('zona');
            $Zona = $zona->listado();
            /*
              for($i=0;$i<count($Zona);$i++){
              if($Cliente[0]['zona']==$Zona[$i]['idzona']){
              $DatosGenerales .= "<li><label>Zona: </label><span>" . $Zona[$i]['nombrezona'] . "</span></li><br>";
              }
              } */
            $DatosGenerales .= '<li><label>Zona: </label>' .
                                '<select id="lstZona">' .
                                '<option value="">Zona</option>';
            for ($i = 0; $i < count($Zona); $i++) {
                if ($Cliente[0]['zona'] == $Zona[$i]['idzona']) {
                    $DatosGenerales .= '<option value="' . $Zona[$i]['idzona'] . '" selected>' . $Zona[$i]['nombrezona'];
                } else {
                    $DatosGenerales .= '<option value="' . $Zona[$i]['idzona'] . '">' . $Zona[$i]['nombrezona'];
                }
            }
            $DatosGenerales .= '</select>'
                                . '<span id="imgchk" style="display: none;"> <img width="18" heigth="18" src="/imagenes/check.png"></span>'
                                . '<a id="grabarZona" style="display: none;" href="#"><img width="21" heigth="21" src="/imagenes/grabar.gif"></a>'
                                . '</li><br>';

            $Vendedor = $vendedor->listadoVendedoresTodos();
            $clienteVendedor = $clienteVendedor->buscarxid($id);
            for ($i = 0; $i < count($Vendedor); $i++) {
                if ($Vendedor[$i]['idactor'] == $clienteVendedor[0]['idvendedor']) {
                    $DatosGenerales .= "<li><label>Vendedor: </label><span>" . $Vendedor[$i]['nombreconcat'] . "</span></li><br>";
                }
            }
            $DatosGenerales .=      "</ul>" .
                                "</div>";
            $Sucursal = $clientezona->listaxidcliente($id);
            $cantidad = count($Sucursal);
            if ($cantidad > 0) {
                $DatosGenerales .= "<div class='CuadroCliente'>" .
                        "<h4>Sucursales: </h4>";
                for ($i = 0; $i < $cantidad; $i++) {
                    $DatosGenerales .= "<ul class='CuadroCliente'>";
                    if (!empty($Sucursal[$i]['nombresucursal']) || !empty($Sucursal[$i]['direccion_fiscal']))
                        $DatosGenerales .= "<li><label><i>Sucursal " . ($i + 1) . ": </i></label></li><br>";
                    if (!empty($Sucursal[$i]['nombresucursal']))
                        $DatosGenerales .= "<li><label>Nombre: </label><span>" . $Sucursal[$i]['nombresucursal'] . "</span></li><br>";
                    if (!empty($Sucursal[$i]['direccion_fiscal']))
                        $DatosGenerales .= "<li><label>Direccion Fiscal: </label><span>" . $Sucursal[$i]['direccion_fiscal'] . "</span></li><br>";
                    $DatosGenerales .= "<li><a href='#' class='btnDxFacturacion' data-id='" . $Sucursal[$i]['idclientezona'] . "'>[Datos de Facturacion]</a> - <a href='#' class='btnDxDespacho' data-id='" . $Sucursal[$i]['idclientezona'] . "'>[Datos de Despacho]</a> - <a href='#' class='btnCerrar' data-id='" . $Sucursal[$i]['idclientezona'] . "'>[Cerrar]</a></li>";
                    $DatosGenerales .= "<br><div class='bloqueCuadro-2' id='bloque_" . $Sucursal[$i]['idclientezona'] . "'></div>"
                                        . "</ul><br>";
                }
                $DatosGenerales .= "</div>";
            }
        } else {
            $DatosGenerales .= "";
        }
        $dataJason[0]['DatosGenerales'] = $DatosGenerales;
        echo json_encode($dataJason[0]);
    }

    function listavistageneral_guardar() {
        $idcliente = $_REQUEST['idcliente'];
        $tipo = $_REQUEST['tipo'];
        $cadena = $_REQUEST['cadena'];
        $cliente = $this->AutoLoadModel('cliente');
        if ($tipo == 'Celular') {
            $data['celular'] = $cadena;
        } else if ($tipo == 'Telefono') {
            $data['telefono'] = $cadena;
        } else if ($tipo == 'Email') {
            $data['email'] = $cadena;
        } else if ($tipo == 'Zona') {
            $data['zona'] = $cadena;
        }
        $dataJason[0]['rspta'] = $cliente->actualizaCliente($data, "idcliente='$idcliente'");
        echo json_encode($dataJason[0]);
    }

    function buscaclientegeneral() {
        $cliente = $this->AutoLoadModel('cliente');
        $zona = $this->AutoLoadModel('zona');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_Cliente'];
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_Cliente'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_Cliente'];
        $paginacion = $cliente->paginadoClientesxnombre($parametro);
        $data['retorno'] = $parametro;
        $data['Cliente'] = $cliente->listaClientesPaginadoxnombre($_REQUEST['id'], $parametro);
        for ($i = 0; $i < count($data['Cliente']); $i++) {
            if ($data['Cliente'][$i]['zona'] != '' && $data['Cliente'][$i]['zona'] != 0) {
                $data['Cliente'][$i]['zona'] = $zona->nombrexid($data['Cliente'][$i]['zona']);
            }
        }
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = count($cliente->buscaxnombre($parametro));
        $this->view->show("/cliente/listavistageneral.phtml", $data);
    }

    function lista() {
        $cliente = $this->AutoLoadModel('cliente');
        $zona = $this->AutoLoadModel('zona');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_Cliente'] = "";
        $data['Cliente'] = $cliente->listaClientesPaginado($_REQUEST['id']);
        for ($i = 0; $i < count($data['Cliente']); $i++) {
            if ($data['Cliente'][$i]['zona'] != '' && $data['Cliente'][$i]['zona'] != 0) {
                $data['Cliente'][$i]['zona'] = $zona->nombrexid($data['Cliente'][$i]['zona']);
            }
        }
        $paginacion = $cliente->paginadoClientes();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/cliente/lista.phtml", $data);
    }

    function busca() {
        $cliente = $this->AutoLoadModel('cliente');
        $zona = $this->AutoLoadModel('zona');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_Cliente'] = $_REQUEST['txtBusqueda'];
        /* if (!empty($_REQUEST['txtBusqueda'])) {
          $_SESSION['P_Cliente'] = $_REQUEST['txtBusqueda'];
          }
          $parametro = $_SESSION['P_Cliente']; */

        $parametro = $_REQUEST['txtBusqueda'];
        $filtroCliente['txtIdentificador'] = $_REQUEST['txtIdentificador'];
        $filtroCliente['txtRazonSocial'] = $_REQUEST['txtRazonSocial'];
        $filtroCliente['txtDni'] = $_REQUEST['txtDni'];
        $filtroCliente['txtRuc'] = $_REQUEST['txtRuc'];
        $filtroCliente['txtDireccion'] = $_REQUEST['txtDireccion'];
        $filtroCliente['txtEmail'] = $_REQUEST['txtEmail'];
        $filtroCliente['txtTelefono'] = $_REQUEST['txtTelefono'];
        $filtroCliente['txtCelular'] = $_REQUEST['txtCelular'];
        $data['filtroCliente'] = $filtroCliente;
        $paginacion = $cliente->paginadoClientesxnombreymasfiltro($parametro, $filtroCliente);
        $data['retorno'] = $parametro;
        $data['Cliente'] = $cliente->listaClientesPaginadoxnombremasfiltro($_REQUEST['id'], $parametro, $filtroCliente);
        for ($i = 0; $i < count($data['Cliente']); $i++) {
            if ($data['Cliente'][$i]['zona'] != '' && $data['Cliente'][$i]['zona'] != 0) {
                $data['Cliente'][$i]['zona'] = $zona->nombrexid($data['Cliente'][$i]['zona']);
            }
        }
        /*
          $paginacion = $cliente->paginadoClientesxnombre($parametro);
          $data['retorno'] = $parametro;
          $data['Cliente'] = $cliente->listaClientesPaginadoxnombre($_REQUEST['id'], $parametro);
          for ($i = 0; $i < count($data['Cliente']); $i++) {
          if ($data['Cliente'][$i]['zona'] != '' && $data['Cliente'][$i]['zona'] != 0) {
          $data['Cliente'][$i]['zona'] = $zona->nombrexid($data['Cliente'][$i]['zona']);
          }
          }
         */
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = count($cliente->buscaxnombre($parametro));
        $this->view->show("/cliente/busca.phtml", $data);
    }

    /**
     * ACCIONES MASIVAS PARA CLIENTES:
     */
    /**
     * GeneraCodigos	:	Crea Codigos a todos los clientes en funcion a la cantidad que existen.	
     *
     */
    function Codigos() {
        $ObjCliente = New Cliente();
        $ObjCliente->GeneraCodigoTodos();
    }

    /**
     * Nuevo: Muestra el formulario pra crear nuevos clientes.
     *
     */
    function nuevo() {
        $departamento = new Departamento();
        $transporte = new Transporte();
        $zona = $this->AutoLoadModel('zona');
        $vendedor = $this->AutoLoadModel('actor');
        $linea = new Linea();
        $sublinea = new sublinea();
        $datos['Departamento'] = $departamento->listado();
        $datos['Transporte'] = $transporte->listaTodo();
        $datos['TipoCliente'] = $this->tipoCliente();
        $datos['Zona'] = $zona->listado();
        $datos['Vendedor'] = $vendedor->listadoVendedoresTodos();
        $datos['Linea'] = $linea->listadoLineas();
        $datos['sublinea'] = $sublinea->listaSublinea('idpadre!=0');
        $this->view->show("cliente/nuevo.phtml", $datos);
    }

    function graba() {
        if (!empty($_SERVER['HTTP_REFERER']) || $_SESSION['nivelacceso'] == 1) {
            $dataCliente = $_REQUEST['Cliente'];
            $idTransporte = $_REQUEST['idTransporte'];
            $dataTransporte = $_REQUEST['Transporte'];
            if (empty($dataCliente['razonsocial'])) {
                $dataCliente['razonsocial'] = $dataCliente['nombrecli'] . ' ' . $dataCliente['apellido1'] . ' ' . $dataCliente['apellido2'];
            }
            $dataCliente['estado'] = 1;
            $cliente = new Cliente();
            $clienteTransporte = new ClienteTransporte();
            $clienteZona = $this->AutoLoadModel('clientezona');
            //$clienteSucursal=$this->AutoLoadModel('clienteSucursal');
            $clienteVendedor = $this->AutoLoadModel('clientevendedor');
            $idCliente = $cliente->grabaCliente($dataCliente);
            $dataClienteZona['idcliente'] = $idCliente;
            $dataClienteZona['idzona'] = $dataCliente['zona'];
            $dataClienteZona['idcliente'] = $idCliente;
            $dataClienteZona['direccion_fiscal'] = $dataCliente['direccion'];
            $dataClienteZona['direccion_despacho_contacto'] = $dataCliente['direccion_despacho_cliente'];
            $dataClienteZona['nomcontacto'] = $dataCliente['nombre_contacto'];
            $dataClienteZona['nombresucursal'] = $dataCliente['nombre_contacto'];
            $dataClienteVendedor['idvendedor'] = $_REQUEST['valorVendedor'];
            $dataClienteVendedor['idcliente'] = $idCliente;
            if ($idCliente) {
                $cliente->GeneraCodigoNuevo($idCliente);
                //$exito4=$clienteSucursal->grabaClienteSucursal($dataClienteSucursal);
                $exito3 = $clienteZona->grabaCliente($dataClienteZona);
                $exito2 = $clienteTransporte->grabaClienteTransporte(array("idcliente" => $idCliente, "idtransporte" => $idTransporte));
                $exito5 = $clienteVendedor->grabaClienteVendedor($dataClienteVendedor);
                if ($exito2 && $exito3 && $exito5) {
                    $ruta['ruta'] = "/cliente/lista/";
                    $this->view->show("ruteador.phtml", $ruta);
                }
            }
        } else {
            echo "no tiene acceso";
        }
    }

    function agregaTransporte() {
        $idCliente = $_REQUEST['id'];
        $dataTransporte = $_REQUEST['Transporte'];
        $transporte = new Transporte();
        $clienteTransporte = new ClienteTransporte();
        if ($dataTransporte['idtransporte'] == "") {
            $dataTransporte['estado'] = 1;
            $idTransporte = $transporte->grabar($dataTransporte);
        } else {
            $idTransporte = $dataTransporte['idtransporte'];
        }
        if ($idTransporte and $idCliente) {
            $exito = $clienteTransporte->grabaClienteTransporte(array("idcliente" => $idCliente, "idtransporte" => $idTransporte, "estado" => 1));
            if ($exito) {
                $listadoTransporte = $transporte->buscarxCliente($idCliente);
                echo '<option value="">' . "-- Transportes --";
                for ($i = 0; $i < count($listadoTransporte); $i++) {
                    if ($listadoTransporte[$i]['idtransporte'] == $idTransporte) {
                        echo '<option value="' . $listadoTransporte[$i]['idclientetransporte'] . '" selected>' . $listadoTransporte[$i]['trazonsocial'];
                    } else {
                        echo '<option value="' . $listadoTransporte[$i]['idclientetransporte'] . '">' . $listadoTransporte[$i]['trazonsocial'];
                    }
                }
            }
        } else {
            $data = $transporte->listatodo();
            echo '<option value="">' . "-- Transportes --";
            for ($i = 0; $i < count($data); $i++) {
                if ($data[$i]['idtransporte'] == $idTransporte) {
                    echo '<option value="' . $data[$i]['idtransporte'] . '" selected>' . $data[$i]['trazonsocial'];
                } else {
                    echo '<option value="' . $data[$i]['idtransporte'] . '">' . $data[$i]['trazonsocial'];
                }
            }
        }
    }

    function editar() {
        //if ($_SESSION['nivelacceso']==1) {
        $id = $_REQUEST['id'];
        $cliente = new Cliente();
        $distrito = new Distrito();
        $provincia = new Provincia();
        $departamento = new Departamento();
        $transporte = new Transporte();
        $clientezona = $this->AutoLoadModel('clientezona');
        $zona = $this->AutoLoadModel('zona');
        $clienteTransporte = $this->AutoLoadModel('clientetransporte');
        $clienteVendedor = $this->AutoLoadModel('clientevendedor');
        $vendedor = $this->AutoLoadModel('actor');
        $dataCliente = $cliente->buscaCliente($id);
        $dataDistrito = $distrito->buscarxid($dataCliente[0]['iddistrito']);
        $linea = new Linea();
        $sublinea = new Sublinea();
        if (!empty($dataCliente[0]['idlinea'])) {
            $idLinea = $linea->buscaLineaPorSublinea($dataCliente[0]['idlinea']);
        }
        $data['Departamento'] = $departamento->listado();
        $data['Provincia'] = $provincia->listado($dataDistrito[0]['codigodepto']);
        $data['Distrito'] = $distrito->listado($dataDistrito[0]['idprovincia']);
        $data['Cliente'] = $cliente->buscaCliente($id);
        $data['ClienteTransporte'] = $transporte->buscarxCliente($id);
        $data['TipoCliente'] = $this->tipoCliente();
        $data['Transporte'] = $transporte->listaTodo();
        $data['Zona'] = $zona->listado();
        $data['Sucursal'] = $clientezona->listaxidcliente($id);
        $data['Vendedor'] = $vendedor->listadoVendedoresTodos();
        $data['clienteVendedor'] = $clienteVendedor->buscarxid($id);
        $data['Linea'] = $linea->listadoLineas();
        $data['Sublinea'] = $sublinea->listadoSublinea($idLinea);
        $this->view->show("cliente/editar.phtml", $data);
    }

    function actualiza() {
        if (!empty($_SERVER['HTTP_REFERER']) || $_SESSION['nivelacceso'] == 1) {
            $dataCliente = $_REQUEST['Cliente'];
            $idClienteTransporte = $_REQUEST['idclientetransporte'];
            $idcliente = $_REQUEST['idCliente'];
            //$dataTransporte['idcliente']=$_REQUEST['idCliente'];
            $idTransporte = $_REQUEST['idtransporte'];
            $dataTransporte['idtransporte'] = $idTransporte;
            $cliente = new Cliente();
            $clientetransporte = new ClienteTransporte();
            //$clienteZona=$this->AutoLoadModel('clientezona');
            $clienteVendedor = $this->AutoLoadModel('clientevendedor');
            if ($dataCliente['tipocliente'] == 1) {
                $dataCliente['razonsocial'] = $dataCliente['nombrecli'] . ' ' . $dataCliente['apellido1'] . ' ' . $dataCliente['apellido2'];
                $dataCliente['nombrecomercial'] = $dataCliente['nombrecli'] . ' ' . $dataCliente['apellido1'] . ' ' . $dataCliente['apellido2'];
            }
            $exito1 = $cliente->actualizaCliente($dataCliente, "idcliente=" . $idcliente);
            $dataClienteZona['idzona'] = $dataCliente['zona'];
            $dataClienteSucursal = $_REQUEST['Sucursal'];
            $dataClienteVendedor['idvendedor'] = $_REQUEST['valorVendedor'];
            //echo $dataClienteVendedor.' '.$idcliente;
            //exit;
            if ($exito1) {
                //$exito3=$clienteZona->actualizaCliente($dataClienteZona,"idcliente=".$idcliente);
                //buscamos si tiene un vendedor asiganado 
                $dataBusqueda = $clienteVendedor->buscarxid($idcliente);
                if (count($dataBusqueda) > 0) {
                    $exito5 = $clienteVendedor->actualizaClienteVendedor($idcliente, $dataClienteVendedor);
                } else {
                    $dataClienteVendedor['idcliente'] = $idcliente;
                    $dataClienteVendedor['estado'] = 1;
                    $exito5 = $clienteVendedor->grabaClienteVendedor($dataClienteVendedor);
                }
                if (!empty($idClienteTransporte) && !empty($idTransporte)) {
                    $exito2 = $clientetransporte->actualizaClienteTransporte($idClienteTransporte, $dataTransporte);
                    if ($exito2 && $exito5) {
                        $ruta['ruta'] = "/cliente/lista/";
                        $this->view->show("ruteador.phtml", $ruta);
                    }
                } else {
                    if ($exito5) {
                        $ruta['ruta'] = "/cliente/lista/";
                        $this->view->show("ruteador.phtml", $ruta);
                    }
                }
            }
        } else {
            echo "no tiene acceso";
        }
    }

    function eliminar() {
        $id = $_REQUEST['id'];
        $cliente = new Cliente();
        $estado = $cliente->cambiaEstadoCliente($id);
        if ($estado) {
            $ruta['ruta'] = "/cliente/lista";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function provincia() {
        $id = $_REQUEST['lstDepartamento'];
        $provincia = new Cliente();
        $data['Provincia'] = $provincia->listadoProvincia($id);
    }

    function autocomplete() {
        $id = $_REQUEST['id'];
        $cliente = new Cliente();
        $data = $cliente->autocomplete($id);
        echo json_encode($data);
    }

    function autocomplete2() {
        $id = $_REQUEST['term'];
        $cliente = new Cliente();
        $data = $cliente->buscaAutocomplete($id);
        echo json_encode($data);
    }

    function autocompletexordenventa() {
        $id = $_REQUEST['term'];
        $cliente = new Cliente();
        $data = $cliente->buscaAutocompletexordenventa($id);
        echo json_encode($data);
    }

    function autocomplete3() {
        $id = $_REQUEST['term'];
        $filtro = $_REQUEST['filtro'];
        $cliente = new Cliente();
        $data = $cliente->buscaAutocomplete_actualizacion($id, $filtro);
        echo json_encode($data);
    }

    function buscarClienteUnificar() {
        $id = $_REQUEST['term'];
        $cliente = new Cliente();
        $data = $cliente->buscaAutocomplete2($id);
        echo json_encode($data);
    }

    function DxClienteUnificar() {
        $id = $_REQUEST['txtid'];
        $Cliente = new Cliente();
        $dataCliente = $Cliente->buscaCliente($id);
        $TipoCliente = $this->tipoCliente();
        $natural = "";
        for ($i = 1; $i <= count($TipoCliente); $i++) {
            if ($dataCliente[0]['tipocliente'] == $i) {
                $natural = $TipoCliente[$i];
            }
        }
        echo "<center><b><h1 style='color: #990000;'>" . $dataCliente[0]['razonsocial'];
        if (strcmp($natural, "Natural") == 0) {
            if (!empty($dataCliente[0]['codcliente'])) {
                echo " [DNI: " . $dataCliente[0]['dni'] . "]";
            }
        }
        echo "</h1></b></center>";
        echo "<br>";
        echo "<table>";
        echo    "<thead>";
        echo        "<tr>";
        echo            "<th style='background:#B4D1F7;color:#830E0E;' colspan='6'><b>DATOS PERSONALES</b></th>";
        echo        "</tr>";
        echo    "</thead>";
        echo    "<tbody>";
        echo        "<tr>";
        echo            "<th style='color:black;background:#C6DCF9;'><b>Código:<b></th>";
        echo            "<td>" . $dataCliente[0]['codcliente'] . "</td>";
        echo            "<th style='color:black;background:#C6DCF9;'><b>Código Dakkar:<b></th>";
        echo            "<td>" . $dataCliente[0]['codantiguo'] . "</td>";
        echo            "<th style='color:black;background:#C6DCF9;'><b>Tipo:<b></th>";
        echo            "<td>" . $natural . "</td>";
        echo        "</tr>";
        if (strcmp($natural, "Natural") == 0) {
            echo "<tr>";
            echo    "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Apellido Paterno:<b></th>";
            echo    "<td>" . $dataCliente[0]['apellido1'] . "</td>";
            echo    "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Apellido Materno:<b></th>";
            echo    "<td>" . $dataCliente[0]['apellido2'] . "</td>";
            echo    "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Nombres:<b></th>";
            echo    "<td>" . $dataCliente[0]['nombrecli'] . "</td>";
            echo "</tr>";
        }//falta dni
        echo    "<tr>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Nombre Comercial:<b></th>";
        echo        "<td>" . $dataCliente[0]['nombrecomercial'] . "</td>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>R.U.C.:<b></th>";
        echo        "<td>" . $dataCliente[0]['ruc'] . "</td>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Contacto:<b></th>";
        echo        "<td>" . $dataCliente[0]['nombre_contacto'] . "</td>";
        echo    "</tr>";
        echo    "<tr>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Página Web:<b></th>";
        echo        "<td>" . $dataCliente[0]['paginaweb'] . "</td>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Fijo:<b></th>";
        echo        "<td>" . $dataCliente[0]['telefono'] . "</td>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Celular<b></th>";
        echo        "<td>" . $dataCliente[0]['celular'] . "</td>";
        echo    "</tr>";
        echo    "<tr>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>FEX:<b></th>";
        echo        "<td>" . $dataCliente[0]['fax'] . "</td>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>E-Mail:<b></th>";
        echo        "<td>" . $dataCliente[0]['email'] . "</td>";
        echo        "<th class='pnatural' style='color:black;background:#C6DCF9;'><b>Horario de Atención:<b></th>";
        echo        "<td>" . $dataCliente[0]['horarioatencion'] . "</td>";
        echo    "</tr>";
        echo "</tbody>";
        echo "</table>";
    }

    function autocompleteClienteZona() {
        $id = $_REQUEST['term'];
        $cliente = new Cliente();
        $data = $cliente->buscaAutocompleteClienteZona($id);
        echo json_encode($data);
    }

    function autocompleteConSucursal() {
        $id = $_REQUEST['term'];
        $cliente = new Cliente();
        $data = $cliente->buscaAutocompleteConSucursal($id);
        echo json_encode($data);
    }

    function buscar() {
        $cli = New Cliente();
        $datos = $cli->listadoClientes();
        $objeto = $this->formatearparakui($datos);
        header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        echo json_encode($objeto);
    }

    function datosclientexid() {
        $id = $_REQUEST['idcliente'];
        $cliente = new Cliente();
        $datos = $cliente->buscacliente($id);
        echo "<ul class='inline-block'>";
        echo    "<li><label>Nombres/RazonSocial:</label> " . $datos[0]['nombrecli'] . " " . $datos[0]['apellido1'] . " " . $datos[0]['apellido2'] . " " . $datos[0]['razonsocial'] . "</li>";
        echo    "<li><label>RUC:</label> " . $datos[0]['ruc'] . "</li>";
        echo    "<li><label>Direccion:</label> " . $datos[0]['direccion'] . "</li>";
        echo    "<li><label>Antiguo Codigo:</label> " . $datos[0]['codantiguo'] . "</li>";
        echo "</ul>";
    }

    function buscarclientezona() {
        $clienteZona = new ClienteZona();
        $cliente = new Cliente();
        $zona = new Zona();
        $datos = $clienteZona->listadoclientezona();
        /* $total=count($datos);
          for($i=0;$i<$total;$i++){
          $datos[$i]['nombrecli']=$cliente->nombrexid($datos[$i]['idcliente']);
          $datos[$i]['nombrezona']=$zona->nombrexid($datos[$i]['idzona']);
          } */
        $objeto = $this->formatearparakui($datos);
        header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        echo json_encode($objeto);
    }

    function validarCodigo() {
        $cliente = $this->AutoLoadModel('cliente');
        $condicion = $_REQUEST['codigo'];
        $cantidad = $cliente->verificaCodigo($condicion);
        if (empty($condicion) || $condicion == 0) {
            $data['error'] = 'No Ingrese valores nulos';
            $data['verificado'] = false;
            echo json_encode($data);
        } else if ($cantidad > 0) {
            $data['error'] = 'El codigo ya existe';
            $data['verificado'] = false;
            echo json_encode($data);
        } else {
            $data['error'] = 'Codigo Aceptado';
            $data['verificado'] = true;
            echo json_encode($data);
        }
    }

    function historialcrediticia() {
        $this->view->show("/cliente/posicioncrediticia.phtml");
    }

    function detalleposicion() {
        $idCliente = $_REQUEST['id'];
        $cliente = New Cliente();
        $dataPosicionCliente = $cliente->detalleposicion($idCliente);
        $tamanio = count($dataPosicionCliente);
        echo "<tr>";
        echo    "<th>Nro:</th>";
        echo    "<th>Linea de Crédito:</th>";
        echo    "<th>Saldo disponible:</th>";
        echo    "<th>Calificación crediticia:</th>";
        echo    "<th>Observaciones:</th>";
        echo    "<th>Estado:</th>";
        echo "</tr>";
        for ($i = 0; $i < $tamanio; $i++) {
            $situacion = ($dataPosicionCliente[$i]['estado'] == 1) ? "ACTIVO" : "Historico";
            echo "<tr>";
            echo    "<td>" . ($i + 1) . "</td>";
            echo    "<td>" . $dataPosicionCliente[$i]['simbolo'] . "  " . number_format($dataPosicionCliente[$i]['lineacredito'], 2) . "</td>";
            echo    "<td>" . $dataPosicionCliente[$i]['simbolo'] . "  " . number_format($dataPosicionCliente[$i]['saldo'], 2) . "</td>";
            switch ($dataPosicionCliente[$i]['calificacion']) {
                case '1': $formacobro = "Cliente A1";
                    break;
                case '2': $formacobro = "Buen cliente";
                    break;
                case '3': $formacobro = "Cliente en Observación";
                    break;
                case '4': $formacobro = "Cliente moroso";
                    break;
                case '5': $formacobro = "Cliente incobrable";
                    break;
            }
            echo    "<td>" . $formacobro . "</td>";
            echo    "<td>" . $dataPosicionCliente[$i]['observacion'] . "</td>";
            if ($situacion == "ACTIVO") {
                echo    "<th>" . $situacion . "</th>";
                echo        "<input type=\"hidden\" id=\"lineacreditoactiva\" value=\"" . number_format($dataPosicionCliente[$i]['lineacredito'], 2) . "\">";
                echo        "<input type=\"hidden\" id=\"idposicioncliente\" value=\"" . $dataPosicionCliente[$i]['idposicioncliente'] . "\">";
                echo        "<input type=\"hidden\" id=\"saldoactivo\" value=\"" . number_format($dataPosicionCliente[$i]['saldo'], 2) . "\">";
            } else {
                echo    "<td>" . $situacion . "</td>";
            }
            echo "</tr>";
        }
    }

    function posicionordenventa() {
        $idCliente = $_REQUEST['id'];
        $lineacredito = New Lineacredito();
        $cliente = New Cliente();
        $dataLC = $lineacredito->ultimalineacreditoXcliente($idCliente);
        $Deudas = $cliente->deudarealCliente($idCliente);
        $deuda = 0;
        if (count($Deudas) > 0) {
            if (isset($Deudas[1])) {
                $deuda = $Deudas[1];
            }
            if (isset($Deudas[2])) {
                $tipocambio = New TipoCambio();
                $tccompra = $tipocambio->tipocambiocompraultimo();
                $deuda += ($tccompra * $Deudas[2]);
            }
        }
        $valorLC = 0;
        $disponibleLC = 0;
        if (count($dataLC) > 0) {
            $valorLC = $dataLC[0]['lineacredito'];
            $disponibleLC = $valorLC - $deuda;
            $textCondicion = '';
            if ($dataLC[0]['contado'] == 1) {
                $textCondicion .= '[Contado] ';
            }
            if ($dataLC[0]['credito'] == 1) {
                $textCondicion .= '[Credito] ';
            }
            if ($dataLC[0]['letras'] == 1) {
                $textCondicion .= '[Letras]';
            }
            echo "<label>Condición de Venta: </label><input type=\"text\" readonly disabled value=\"" . $textCondicion . "\">";
            echo "<label>Linea de Crédito S/: </label><input type=\"text\" readonly disabled value=\"" . round($valorLC, 2) . "\">";
            echo "<label>Linea Utilizada S/: </label><input type=\"text\" readonly disabled value=\"" . round($deuda, 2) . "\">";
            echo "<label>Saldo Disponible S/: </label><input type=\"text\" id=\"idsaldo\" readonly disabled value=\"" . round($disponibleLC, 2) . "\">";
        } else {
            echo "<label>Linea Utilizada S/: </label><input type=\"text\" readonly disabled value=\"" . round($deuda, 2) . "\">";
            echo "<label>Saldo Disponible S/: </label><input type=\"text\" id=\"idsaldo\" readonly disabled value=\"" . round($deuda * -1, 2) . "\">";
            echo "<label style='color: red;'>[No tiene linea de crédito asignada] </label>";
        }
    }

    function datosdeudaOrdenVentas() {
        $idCliente = $_REQUEST['id'];
        $cliente = New Cliente();
        $deuda = $cliente->deudaOrdenVenta($idCliente);
        $ultimadeuda = $deuda[0];
        echo "<label>Ultima Orden: </label><input type=\"text\" readonly disabled value=\"" . $ultimadeuda['codigov'] . "\">";
        echo "<label>Monto Ultima Orden: </label><input type=\"text\" readonly disabled value=\"" . number_format($ultimadeuda['importeordencobro'], 2) . "\">";
        echo "<label>Deuda Ultima Orden: </label><input type=\"text\" readonly disabled value=\"" . number_format($ultimadeuda['saldo'], 2) . "\">";
    }

    function datosdeudaTotalOrdenVentas() {
        $idCliente = $_REQUEST['id'];
        $cliente = New Cliente();
        $deuda = $cliente->deudaOrdenVenta($idCliente);
        $total = count($deuda);
        for ($i = 0; $i < $total; $i++) {
            $deudatotal += $deuda[$i]['saldo'];
        }
        echo "<label>Deuda Total: </label><input type=\"text\" class=\"important\"  id=\"deudatotal\" readonly disabled value=\"" . number_format($deudatotal, 2) . "\">";
    }

    function registrarposicion() {
        $idCliente = $_REQUEST['idcliente'];
        $cliente = $_REQUEST['Cliente'];
        $cliente['idcliente'] = $idCliente;
        $cliente['idmoneda'] = 2;
        $ClientePosicion = New ClientePosicion();
        $dataActual = $ClientePosicion->datosPosicion($idCliente);
        $cliente['saldo'] = $dataActual[0]['saldo'] + ($cliente['lineacredito'] - $dataActual[0]['lineacredito']);
        $actualiza = $ClientePosicion->actualizaPosicion($idCliente);
        $exito = $ClientePosicion->grabaPosicion($cliente);
        $ruta['ruta'] = "/cliente/historialcrediticia/";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function vistaGlobal() {
        if ($_REQUEST['idcliente']) {
            $id = $_REQUEST['idcliente'];
            $ordenventa = new OrdenVenta();
            $data['data'] = $ordenventa->listaOrdenVentaxIdCliente($id);
            $data['nroOrdenes'] = count($data['data']);
        } else {
            $data['data'] = "";
        }
        $this->view->show("/cliente/vistaglobal.phtml", $data);
    }

    function clientevistaglobal() {
        if ($_REQUEST['idcliente']) {
            $id = $_REQUEST['idcliente'];
            $ordenventa = new OrdenVenta();
            $data['data'] = $ordenventa->listaOrdenVentaxIdCliente($id);
            $data['nroOrdenes'] = count($data['data']);
        } else {
            $data['data'] = "";
        }
        $this->view->show("/cliente/clientevistaglobal.phtml", $data);
    }

    function cargaSucursales() {
        $cliente = New Cliente();
        $sucursal = $this->AutoLoadModel('ClienteSucursal');
        $dataCliente = $cliente->consultaClientes();
        $TotalClientes = count($dataCliente);
        for ($i = 0; $i < $TotalClientes; $i++) {
            $dataClienteSucursal['idcliente'] = $dataCliente[$i]['idcliente'];
            $dataClienteSucursal['tipooficina'] = $dataClienteSucursal['estado'] = 1;
            $dataClienteSucursal['direccion'] = html_entity_decode($dataCliente[$i]['direccion'], ENT_QUOTES, 'UTF-8');
            $dataClienteSucursal['distrito'] = $dataCliente[$i]['iddistrito'];
            $exito = $sucursal->grabaClienteSucursal($dataClienteSucursal);
        }
    }

    function cargaTransportePower() {
        $cliente = New Cliente();
        $sucursal = $this->AutoLoadModel('ClienteTransporte');
        $dataCliente = $cliente->consultaClientes();
        $TotalClientes = count($dataCliente);
        for ($i = 0; $i < $TotalClientes; $i++) {
            $dataClienteTransporte['idcliente'] = $dataCliente[$i]['idcliente'];
            $dataClienteTransporte['estado'] = 1;
            $dataClienteTransporte['idtransporte'] = 35;
            $exito = $sucursal->grabaClienteTransporte($dataClienteTransporte);
        }
    }

    function direccion_despacho() {
        $idcliente = $_REQUEST['idcliente'];
        //echo $idcliente;
        $clientezona = $this->AutoLoadModel('clientezona');
        $dataClienteZona = $clientezona->buscaCliente($idcliente);
        $cantidad = count($dataClienteZona);
        $dato = "<option value=''>Direcciones Despacho</option>";
        for ($i = 0; $i < $cantidad; $i++) {
            $dato .= "<option value='" . $dataClienteZona[$i]['idclientezona'] . "'>" . (html_entity_decode($dataClienteZona[$i]['direccion_despacho_contacto'], ENT_QUOTES, 'UTF-8')) . "</option>";
        }
        echo $dato;
    }

    function direccion_fiscal() {
        $idcliente = $_REQUEST['idcliente'];
        //echo $idcliente;
        $clientezona = $this->AutoLoadModel('clientezona');
        $dataClienteZona = $clientezona->buscaCliente($idcliente);
        $cantidad = count($dataClienteZona);
        $dato = "<option value=''>Direcciones </option>";
        for ($i = 0; $i < $cantidad; $i++) {
            $dato .= "<option value='" . $dataClienteZona[$i]['idclientezona'] . "'>" . (html_entity_decode($dataClienteZona[$i]['direccion_fiscal'], ENT_QUOTES, 'UTF-8')) . "</option>";
        }
        echo $dato;
    }

    function contactos() {
        $idcliente = $_REQUEST['idcliente'];
        //echo $idcliente;
        $clientezona = $this->AutoLoadModel('clientezona');
        $dataClienteZona = $clientezona->buscaCliente($idcliente);
        $cantidad = count($dataClienteZona);
        $dato = "<option value=''>Contactos</option>";
        for ($i = 0; $i < $cantidad; $i++) {
            $dato .= "<option value='" . $dataClienteZona[$i]['idclientezona'] . "'>" . $dataClienteZona[$i]['nomcontacto'] . "</option>";
        }
        echo $dato;
    }

    function actualizarSucursal_2() {
        $clientezona = $this->AutoLoadModel('clientezona');
        $id = $_REQUEST['idclienteZona'];
        $data['idzona'] = $_REQUEST['idzona'];
        $filtro = "idclientezona='$id'";
        $dataRespuesta['rspta'] = $clientezona->actualizaCliente($data, $filtro);
        echo json_encode($dataRespuesta);
    }

    function cargaSucursal() {
        $idclientezona = $_REQUEST['idclientesucursal'];
        $clientezona = $this->AutoLoadModel('clientezona');
        $dataSucursal = $clientezona->buscaClienteZona($idclientezona);
        $dataSucursal[0]['nombresucursal'] = html_entity_decode($dataSucursal[0]['nombresucursal'], ENT_QUOTES, 'UTF-8');
        $dataSucursal[0]['nomcontacto'] = html_entity_decode($dataSucursal[0]['nomcontacto'], ENT_QUOTES, 'UTF-8');
        $dataSucursal[0]['direccion_fiscal'] = html_entity_decode($dataSucursal[0]['direccion_fiscal'], ENT_QUOTES, 'UTF-8');
        $dataSucursal[0]['direccion_despacho_contacto'] = html_entity_decode($dataSucursal[0]['direccion_despacho_contacto'], ENT_QUOTES, 'UTF-8');
        $dataSucursal[0]['nombrecontactodespacho'] = html_entity_decode($dataSucursal[0]['nombrecontactodespacho'], ENT_QUOTES, 'UTF-8');
        $zona = $this->AutoLoadModel('zona');
        $Zona = $zona->listado();
        $comboZona .= '<select class="lstClienteZona" data-id="' . $idclientezona . '">' .
                        '<option value="">Zona</option>';
        for ($i = 0; $i < count($Zona); $i++) {
            if ($dataSucursal[0]['idzona'] == $Zona[$i]['idzona']) {
                $comboZona .= '<option value="' . $Zona[$i]['idzona'] . '" selected>' . $Zona[$i]['nombrezona'];
            } else {
                $comboZona .= '<option value="' . $Zona[$i]['idzona'] . '">' . $Zona[$i]['nombrezona'];
            }
        }
        $comboZona .= '</select>'
                        . '<span id="imgchk_' . $idclientezona . '" style="display: none;"> <img width="18" heigth="18" src="/imagenes/check.png"></span>'
                        . '<a class="grabarClienteZona" data-id="' . $idclientezona . '" style="display: none;" href="#"><img width="21" heigth="21" src="/imagenes/grabar.gif"></a>';
        $dataSucursal[0]['comboClienteZona'] = $comboZona;
        echo json_encode($dataSucursal[0]);
    }

    function grabarSucursal() {
        $clientezona = $this->AutoLoadModel('clientezona');
        $data['nombresucursal'] = $_REQUEST['nombresucursal'];
        $data['nomcontacto'] = $_REQUEST['nomcontacto'];
        $data['dnicontacto'] = $_REQUEST['dnicontacto'];
        $data['telcontac'] = $_REQUEST['telcontac'];
        $data['movilcontac'] = $_REQUEST['movilcontac'];
        $data['direccion_fiscal'] = $_REQUEST['direccion_fiscal'];
        $data['direccion_despacho_contacto'] = $_REQUEST['direccion_despacho_contacto'];
        $data['horarioatencion'] = $_REQUEST['horarioatencion'];
        $data['nombrecontactodespacho'] = $_REQUEST['nombrecontactodespacho'];
        $data['dnidespacho'] = $_REQUEST['dnidespacho'];
        $data['telcontacdespacho'] = $_REQUEST['telcontacdespacho'];
        $data['movilcontacdespacho'] = $_REQUEST['movilcontacdespacho'];
        $data['horarioatenciondespacho'] = $_REQUEST['horarioatenciondespacho'];
        $data['idcliente'] = $_REQUEST['idCliente'];
        $data['idzona'] = $_REQUEST['idzona'];
        $data['tipooficina'] = 1;
        $exito = $clientezona->grabaCliente($data);
        if ($exito) {
            $dataRespuesta['idsucursal'] = $exito;
            $dataRespuesta['validacion'] = true;
        } else {
            $dataRespuesta['validacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function actualizarSucursal() {
        $clientezona = $this->AutoLoadModel('clientezona');
        $id = $_REQUEST['id'];
        $data['nombresucursal'] = $_REQUEST['nombresucursal'];
        $data['nomcontacto'] = $_REQUEST['nomcontacto'];
        $data['dnicontacto'] = $_REQUEST['dnicontacto'];
        $data['telcontac'] = $_REQUEST['telcontac'];
        $data['movilcontac'] = $_REQUEST['movilcontac'];
        $data['direccion_fiscal'] = $_REQUEST['direccion_fiscal'];
        $data['direccion_despacho_contacto'] = $_REQUEST['direccion_despacho_contacto'];
        $data['horarioatencion'] = $_REQUEST['horarioatencion'];
        $data['nombrecontactodespacho'] = $_REQUEST['nombrecontactodespacho'];
        $data['dnidespacho'] = $_REQUEST['dnidespacho'];
        $data['telcontacdespacho'] = $_REQUEST['telcontacdespacho'];
        $data['movilcontacdespacho'] = $_REQUEST['movilcontacdespacho'];
        $data['horarioatenciondespacho'] = $_REQUEST['horarioatenciondespacho'];
        $data['idcliente'] = $_REQUEST['idCliente'];
        $data['idzona'] = $_REQUEST['idzona'];
        $data['tipooficina'] = 1;
        $filtro = "idclientezona='$id'";
        $exito = $clientezona->actualizaCliente($data, $filtro);
        if ($exito) {
            $dataRespuesta['idsucursal'] = $exito;
            $dataRespuesta['nombresucursal'] = $_REQUEST['nombresucursal'];
            $dataRespuesta['direccion_fiscal'] = $_REQUEST['direccion_fiscal'];
            $dataRespuesta['validacion'] = true;
        } else {
            $dataRespuesta['validacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function eliminarSucursal() {
        $id = $_REQUEST['idclientesucursal'];
        $clientezona = $this->AutoLoadModel('clientezona');
        $exito = $clientezona->cambiaEstadoClienteZona($id);
        if ($exito) {
            $dataRespuesta['validacion'] = true;
        } else {
            $dataRespuesta['validacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function llenarZonasInicial() {
        $cliente = $this->AutoLoadModel('cliente');
        $clientezona = $this->AutoLoadModel('clientezona');
    }

    function bucaZonasxCliente() {
        $idCliente = $_REQUEST['idCliente'];
        $clienteZona = $this->AutoLoadModel('clientezona');
        $dataClienteZona = $clienteZona->buscaCliente($idCliente);
        $cantidad = count($dataClienteZona);
        for ($i = 0; $i < $cantidad; $i++) {
            $dato .= "<option value='" . $dataClienteZona[$i]['idclientezona'] . "'>" . (html_entity_decode($dataClienteZona[$i]['nombresucursal'], ENT_QUOTES, 'UTF-8')) . "</option>";
        }
        echo $dato;
    }

    function unificarcliente() {
        if ($_REQUEST['txtorigen'] && $_REQUEST['txtdestino']) {
            $cliente = new Cliente();
            $dataCliente['idcliente'] = $_REQUEST['txtdestino'];
            $cliente->unificarCliente($dataCliente, "idcliente=" . $_REQUEST['txtorigen']);
        }
        $this->view->show("/cliente/unificarcliente.phtml");
    }

    function actualizar($id = "") {
        $cliente = new Cliente();
        if ($_POST['txtdistrito'] && $_POST['txtdireccion'] && $_POST['txtid']) {
            $id = $_POST['txtid'];
            $dataCliente['actualizado'] = 1;
            $dataCliente['distrito'] = $_POST['txtdistrito'];
            $dataCliente['direccioncar'] = $_POST['txtdireccion'];
            $dataCliente['referenciacar'] = $_POST['txtreferencia'];
            $dataCliente['cel'] = $_POST['txtcelular'];
            $dataCliente['telf'] = $_POST['txttelefono'];
            $cliente->actualizaCliente($dataCliente, "idcliente=" . $id);
        } else if (!empty($_REQUEST['id'])) {
            $id = $_REQUEST['id'];
            $idComp = explode('-', $id);
            if (count($idComp) > 1) {
                if ($idComp[1] == 1) {
                    $dataCliente['actualizado'] = 2;
                    $cliente->actualizaCliente($dataCliente, "idcliente=" . $idComp[0]);
                    $id = "";
                } else if ($idComp[1] == 0) {
                    $dataCliente['actualizado'] = 0;
                    $dataCliente['distrito'] = '';
                    $dataCliente['direccioncar'] = '';
                    $dataCliente['referenciacar'] = '';
                    $dataCliente['telf'] = '';
                    $dataCliente['cel'] = '';
                    $cliente->actualizaCliente($dataCliente, "idcliente=" . $idComp[0]);
                }
            }
        }
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['Dir1'] = $archivoConfig['Dir1'];
        $combodir2 = $archivoConfig['Dir2'];
        $data['Dir2'] = $combodir2;
        $combodir3 = $archivoConfig['Dir3'];
        $data['Dir3'] = $combodir3;
        $distrito = new Distrito();
        $provincia = new Provincia();
        $departamento = new Departamento();
        $dataCliente = $cliente->buscaClienteSinActualizar($id);
        $Dir1Select = '';
        $Dir2Select = '';
        $Dir3Select = '';
        if (count($dataCliente) > 0) {
            $Dir1Select = "(1)";
            $data['clienteExsite'] = 1;
            $dataDistrito = $distrito->buscarxid($dataCliente[0]['iddistrito']);
            $data['Departamento'] = $departamento->listado();
            $data['Provincia'] = $provincia->listado($dataDistrito[0]['codigodepto']);
            $data['Distrito'] = $distrito->listado($dataDistrito[0]['idprovincia']);
            $data['Cliente'] = $dataCliente;
            $data['TipoCliente'] = $this->tipoCliente();
            if ($dataCliente[0]['actualizado'] == 1) {
                $Dir1Select = explode(" ", $dataCliente[0]['direccioncar']);
                $Dir1Select = $Dir1Select[0];
                $direccionpura = substr($dataCliente[0]['direccioncar'], strlen($Dir1Select) + 1);
                $dirnro = '';
                $Tempdireccion2 = '';
                if ($Dir1Select != 'MERCADO' && $Dir1Select != 'C.C.') {
                    $tempdireccionpura = explode(" NRO. ", $direccionpura);
                    if (count($tempdireccionpura) > 1) {
                        $direccionpura = $tempdireccionpura[0];
                        $Tempdireccion2 = substr($tempdireccionpura[1], 0);
                    } else {
                        $direccionpura = explode(" S/N ", $direccionpura);
                        $Tempdireccion2 = substr($direccionpura[1], 0);
                        $direccionpura = $direccionpura[0];
                        $dirnro = 'S/N';
                    }
                } else {
                    for ($i = 1; $i <= count($combodir2); $i++) {
                        $arrayDir2 = explode(" " . $combodir2[$i] . " ", $direccionpura);
                        if (count($arrayDir2) > 1) {
                            $direccionpura = $arrayDir2[0];
                            $Dir2Select = $combodir2[$i];
                            $Tempdireccion2 = $arrayDir2[1];
                            $i = count($combodir2) + 1;
                        }
                    }
                }
                $direccion2 = $Tempdireccion2;
                if (!empty($dirnro)) {
                    $Dir2Select = explode(" ", $Tempdireccion2);
                    $Dir2Select = $Dir2Select[0];
                    $direccion2 = substr($direccion2, strlen($Dir2Select) + 1);
                } else {
                    for ($i = 1; $i <= count($combodir2); $i++) {
                        $arrayDir2 = explode(" " . $combodir2[$i] . " ", $Tempdireccion2);
                        if (count($arrayDir2) > 1) {
                            $Dir2Select = $combodir2[$i];
                            $dirnro = $arrayDir2[0];
                            $i = count($combodir2) + 1;
                            $direccion2 = substr($direccion2, strlen($dirnro) + 1 + strlen($Dir2Select) + 1);
                        }
                    }
                    if (empty($dirnro)) {
                        if ($Dir1Select != 'MERCADO' && $Dir1Select != 'C.C.') {
                            $dirnro = $direccion2;
                            $direccion2 = "";
                        }
                    }
                }
                $Tempdireccion3 = '';
                if (!empty($direccion2)) {
                    $Tempdireccion3 = $direccion2;
                    for ($i = 1; $i <= count($combodir3); $i++) {
                        $arrayDir3 = explode(" " . $combodir3[$i] . " ", $Tempdireccion3);
                        if (count($arrayDir3) > 1) {
                            $Dir3Select = $combodir3[$i];
                            $direccion2 = $arrayDir3[0];
                            $Tempdireccion3 = $arrayDir3[1];
                        }
                    }
                    if (empty($Dir3Select)) {
                        $Tempdireccion3 = '';
                    }
                }
                $direccion3 = $Tempdireccion3;
                $data['direccionpura'] = $direccionpura;
                $data['dirnumero'] = $dirnro;
                $data['direccionpura2'] = $direccion2;
                $data['direccionpura3'] = $direccion3;
            }
        } else {
            $data['clienteExsite'] = 0;
        }
        $data['Dir1Select'] = $Dir1Select;
        $data['Dir2Select'] = $Dir2Select;
        $data['Dir3Select'] = $Dir3Select;
        $this->view->show("/cliente/actualizar.phtml", $data);
    }

    function desactualizados() {
        $cliente = $this->AutoLoadModel('cliente');
        $zona = $this->AutoLoadModel('zona');
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
            $filtro = 0;
        } else {
            $datos = explode('.', $_REQUEST['id']);
            $filtro = $datos[0];
            if ($filtro < 0 || $filtro > 2)
                $filtro = 3;
            if (count($datos) == 2) {
                $pagina = $datos[1];
            } else {
                $pagina = 1;
            }
        }
        session_start();
        $_SESSION['P_ClienteNew'] = "";
        $data['Cliente'] = $cliente->listaClientesPaginado_actualizacion($filtro, $pagina);
        for ($i = 0; $i < count($data['Cliente']); $i++) {
            if ($data['Cliente'][$i]['zona'] != '' && $data['Cliente'][$i]['zona'] != 0) {
                $data['Cliente'][$i]['zona'] = $zona->nombrexid($data['Cliente'][$i]['zona']);
            }
        }
        $paginacion = $cliente->paginadoClientes_actualizacion($filtro);
        $data['filtro'] = $filtro;
        $data['pagina'] = $pagina;
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/cliente/desactualizados.phtml", $data);
    }

    function descargar() {
        $descarga = new Descarga();
        $data['datos'] = $descarga->ultimaDescarga();
        $this->view->show("/cliente/descargar.phtml", $data);
    }

    function registrados() {
        $zona = $this->AutoLoadModel('zona');
        $linea = $this->AutoLoadModel('linea');
        $data['linea'] = $linea->listaLineas();
        $data['categoriaPrincipal'] = $zona->listaCategoriaPrincipal();
        $this->view->show("/cliente/registrados.phtml", $data);
    }

    function destacados() {
        $zona = $this->AutoLoadModel('zona');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['hijo'] = $zona->listacategoriaHijo();
        $data['zona'] = $zona->listadoTotalZona();
        $this->view->show('/cliente/clientesdestacados.phtml', $data);
    }

    function evaluacioncrediticia() {
        $zona = $this->AutoLoadModel('zona');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $cliente = $this->AutoLoadModel('cliente');
        $data['padre'] = $zona->listaCategoriaPrincipal();
        $data['tipocobranza'] = $tipo->lista();
        $data['listaCalificaciones'] = $cliente->listaCalificaciones();
        $this->view->show('/cliente/evaluacioncrediticia.phtml', $data);
    }

    function listaDataLineacredito() {
        if ($_REQUEST['id']) {
            $cliente = $this->AutoLoadModel('cliente');
            $listaDataLineacredito = $cliente->listaDataLineacredito($_REQUEST['id']);
            foreach ($listaDataLineacredito as $value) {
                $lineacreditoactual = $value['lineacreditototal'];
                $deudatotal = $value['deudatotal'];
                $lineacreditodisponible = $value['lineacreditodisponible'];
            }
            $seccion = "<span class='letraResaltada'>LINEA DE CREDITO DISPONIBLE </span><span>" . 'S/. ' . $lineacreditodisponible . "</span>";
            $seccion .= "<span style='margin-left:20px;' class='letraResaltada'>LINEA DE CREDITO </span><span>" . 'S/. ' . $lineacreditoactual . "</span>";
            $seccion .= "<span style='margin-left:20px;' class='letraResaltada'>LINEA UTLIZADA </span><span>" . 'S/. ' . $deudatotal . "</span>";
            echo $seccion;
        }
    }

    function lineadecredito() {
        $id = $_REQUEST['id'];
        $cliente = new Cliente();
        $tipocambio = new TipoCambio();
        $lineacredito = new Lineacredito();
        $data['lineacredito'] = $lineacredito->historiallineacreditoXcliente($id);
        $data['Calificaciones'] = $cliente->listaCalificaciones();
        $data['Cliente'] = $cliente->buscaCliente($id);
        $data['Deudas'] = $cliente->deudarealCliente($id);
        $data['tccompra'] = $tipocambio->tipocambiocompraultimo();
        $this->view->show('/cliente/lineadecredito.phtml', $data);
    }

    function grabalineadecredito() {
        if ($_REQUEST['txtidcliente'] > 0) {
            $idciente = $_REQUEST['txtidcliente'];
            $txtlineacredito = $_REQUEST['txtlineacredito'];
            $cmbCalificacion = $_REQUEST['cmbCalificacion'];
            $chkContado = $_REQUEST['chkContado'];
            $chkCredito = $_REQUEST['chkCredito'];
            $chkLetra = $_REQUEST['chkLetra'];
            $taObservaciones = $_REQUEST['taObservaciones'];
            $lineacredito = new Lineacredito();
            $dataUltimaLC = $lineacredito->ultimalineacreditoXcliente($idciente);
            if (count($dataUltimaLC) > 0) {
                $cliente = new Cliente();
                $tipocambio = new TipoCambio();
                $Deudas = $cliente->deudarealCliente($idciente);
                $tccompra = $tipocambio->tipocambiocompraultimo();
                $tam = count($Deudas);
                $LineaUtilizada = 0;
                if ($tam > 0) {
                    if (isset($Deudas[1])) {
                        $LineaUtilizada = $Deudas[1];
                    }
                    if (isset($Deudas[2])) {
                        $LineaUtilizada += ($tccompra * $Deudas[2]);
                    }
                }
                $dataAct['deuda'] = round($LineaUtilizada, 2);
                $dataAct['lineadisponible'] = $dataUltimaLC[0]['lineacredito'] - $LineaUtilizada;
                $lineacredito->actualizaLineacredito($dataAct, $dataUltimaLC[0]['idlineacredito']);
            }
            $data['idcliente'] = $idciente;
            $data['idmoneda'] = 1;
            $data['fregistro'] = date('Y-m-d');
            $data['calificacion'] = $cmbCalificacion;
            $data['lineacredito'] = $txtlineacredito;
            $data['contado'] = ($chkContado ? 1 : 0);
            $data['credito'] = ($chkCredito ? 1 : 0);
            $data['letras'] = ($chkLetra ? 1 : 0);
            $data['observacion'] = $taObservaciones;
            $lineacredito->guardaLineacredito($data);
            $ruta['ruta'] = "/cliente/lineadecredito/" . $idciente;
            $this->view->show("ruteador.phtml", $ruta);
        } else {
            $ruta['ruta'] = "/cliente/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function eliminarlineadecredito() {
        $id = $_REQUEST['id'];
        $lineacredito = new Lineacredito();
        $dataLC = $lineacredito->buscaLineacredito($id);
        if (count($dataLC) > 0) {
            $dataUltimaLC = $lineacredito->ultimalineacreditoXcliente($dataLC[0]['idcliente']);
            if (count($dataUltimaLC) > 0) {
                if ($dataUltimaLC[0]['idlineacredito'] == $id) {
                    $dataAct['estado'] = 0;
                    $lineacredito->actualizaLineacredito($dataAct, $dataUltimaLC[0]['idlineacredito']);
                    $dataUltimaLC2 = $lineacredito->ultimalineacreditoXcliente($dataLC[0]['idcliente']);
                    if (count($dataUltimaLC2) > 0) {
                        $dataAct2['deuda'] = '';
                        $dataAct2['lineadisponible'] = '';
                        $lineacredito->actualizaLineacredito($dataAct2, $dataUltimaLC2[0]['idlineacredito']);
                    }
                }
            }
            $ruta['ruta'] = "/cliente/lineadecredito/" . $dataLC[0]['idcliente'];
            $this->view->show("ruteador.phtml", $ruta);
        } else {
            $ruta['ruta'] = "/cliente/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function listalineadecredito() {
        $zona = $this->AutoLoadModel('zona');
        $cliente = new Cliente();
        $data['Calificaciones'] = $cliente->listaCalificaciones();
        $data['categoriaPrincipal'] = $zona->listaCategoriaPrincipal();
        $this->view->show('/cliente/listalineadecredito.phtml', $data);
    }

}

?>
