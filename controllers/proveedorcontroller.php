<?php

class ProveedorController extends ApplicationGeneral {

    function listado() {
        $proveedor = $this->AutoLoadModel("proveedor");
        $data['proveedor'] = $proveedor->listaProveedoresPaginado($_REQUEST['id']);
        $data['paginacion'] = $proveedor->paginadoProveedor();
        $this->view->show("proveedor/listado.phtml", $data);
    }

    function nuevo() {
        $departamento = new Departamento();
        $transporte = new Transporte();
        $data['Departamento'] = $departamento->listado();
        $data['Transporte'] = $transporte->listaTodo();
        $data['TipoProveedor'] = $this->tipoCliente();
        $this->view->show("proveedor/nuevo.phtml", $data);
    }

    function graba() {
        $data = $_REQUEST['Proveedor'];
        $data['estado'] = 1;
        $proveedor = new Proveedor();
        $actorRol = new ActorRol();
        $idProveedor = $proveedor->grabaProveedor($data);
        if ($idProveedor) {
            $ruta['ruta'] = "/proveedor/editar/" . $idProveedor;
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function personagrabaedita() {
        $data['contacto'] = $_REQUEST['contacto'];
        $idproveedorpersona = $_REQUEST['idproveedorpersona'];
        $data['idproveedor'] = $_REQUEST['idproveedor'];
        $data['cargo'] = $_REQUEST['cargo'];
        $data['email'] = $_REQUEST['email'];
        $data['telefono'] = $_REQUEST['telefono'];
        $TempResultados = "";
        $Proveedorpersona = new Proveedorpersona();
        if (!empty($idproveedorpersona)) {
            $Proveedorpersona->actualiza($data, $idproveedorpersona);
        } else {
            $Proveedorpersona->graba($data);
        }
        $Personas = $Proveedorpersona->listadoxProveedor($data['idproveedor']);
        $tam = count($Personas);
        for ($i = 0; $i < $tam; $i++) {
            $TempResultados .= '<tr>' .
                    '<td>' . $Personas[$i]['contacto'] . '</td>' .
                    '<td>' . $Personas[$i]['cargo'] . '</td>' .
                    '<td>' . $Personas[$i]['telefono'] . '</td>' .
                    '<td>' . $Personas[$i]['email'] . '</td>' .
                    '<td>'
                    . '<a href="#" data-id="' . $Personas[$i]['idproveedorpersona'] . '" class="btnEditarPersona" href="#"><img src="/imagenes/editar.gif"></a>'
                    . '<a href="#" data-id="' . $Personas[$i]['idproveedorpersona'] . '" style="margin-left:5px;" class="btnEliminarPersona" href="#"><img src="/imagenes/eliminar.gif"></a>'
                    . '</td>' .
                    '</tr>';
        }
        $dataPersonas[0]['resultados'] = $TempResultados;
        echo json_encode($dataPersonas[0]);
    }

    function persona() {
        $idproveedorpersona = $_REQUEST['idproveedorpersona'];
        $Proveedorpersona = new Proveedorpersona();
        $Persona = $Proveedorpersona->buscaPersona($idproveedorpersona);
        $dataPersona[0]['estado'] = 0;
        if (count($Persona) > 0) {
            $dataPersona[0]['estado'] = 1;
            $dataPersona[0]['contacto'] = html_entity_decode($Persona[0]['contacto'], ENT_QUOTES, 'UTF-8');
            $dataPersona[0]['idproveedorpersona'] = $Persona[0]['idproveedorpersona'];
            $dataPersona[0]['cargo'] = html_entity_decode($Persona[0]['cargo'], ENT_QUOTES, 'UTF-8');
            $dataPersona[0]['email'] = $Persona[0]['email'];
            $dataPersona[0]['telefono'] = $Persona[0]['telefono'];
        }
        echo json_encode($dataPersona[0]);
    }

    function eliminapersona() {
        $id = $_REQUEST['id'];
        $ruta['ruta'] = "/proveedor/listado";
        if (!empty($id)) {
            $Proveedorpersona = new Proveedorpersona();
            $dataPersona = $Proveedorpersona->buscaPersona($id);
            if (count($dataPersona) > 0) {
                $data['estado'] = 0;
                $Proveedorpersona->actualiza($data, $id);
                $ruta['ruta'] = "/proveedor/editar/" . $dataPersona[0]['idproveedor'];
            }
        }
        $this->view->show("ruteador.phtml", $ruta);
    }

    function editar() {
        $id = $_REQUEST['id'];
        $proveedor = new Proveedor();
        $distrito = new Distrito();
        $provincia = new Provincia();
        $departamento = new Departamento();
        $dataProveedor = $proveedor->buscaProveedor($id);
        $data['Proveedor'] = $dataProveedor;
        $data['TipoProveedor'] = $this->tipoCliente();
        $this->view->show("proveedor/editar.phtml", $data);
        //*/
    }

    function actualiza() {
        $data = $_REQUEST['Proveedor'];
        $id = $_REQUEST['idProveedor'];
        $proveedor = new Proveedor();
        $exito = $proveedor->actualizaProveedor($data, "idproveedor=" . $id);
        $this->view->show("ruteador.phtml", $ruta);
        if ($exito) {
            $ruta['ruta'] = "/proveedor/listado";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function eliminar() {
        $id = $_REQUEST['id'];
        $proveedor = new Proveedor();
        $estado = $proveedor->cambiaEstadoProveedor($id);
        if ($estado) {
            $ruta['ruta'] = "/proveedor/listado";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function busqueda() {
        $texto = $_REQUEST['txtBusqueda'];
        $proveedor = $this->AutoLoadModel("proveedor");
        $data['proveedor'] = $proveedor->buscarxnombre(0, 10, $texto);
        $this->view->show("proveedor/listado.phtml", $data);
    }

    function provincia() {
        $id = $_REQUEST['lstDepartamento'];
        $provincia = new Proveedor();
        $data['Provincia'] = $provincia->listadoProvincia($id);
    }

    function autocomplete() {
        $id = $_REQUEST['id'];
        $proveedor = new Proveedor();
        $data = $proveedor->autocomplete($id);
        echo json_encode($data);
    }

    function buscar() {
        $proveedor = New Proveedor();
        $datos = $proveedor->listadoProveedores();
        $objeto = $this->formatearparakui($datos);
        header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        echo json_encode($objeto);
    }

    function grabaJason() {
        $linea = $this->AutoLoadModel('proveedor');
        $data['razonsocialp'] = $_REQUEST['razsocProveedor'];
        $data['rlegal'] = $_REQUEST['repreProveedor'];
        $data['contacto'] = $_REQUEST['percontactoProveedor'];
        $data['direccionp'] = $_REQUEST['direccionProveedor'];
        $data['descripcionp'] = $_REQUEST['descripcionProveedor'];
        $data['rucp'] = $_REQUEST['rucProveedor'];
        $data['emailp'] = $_REQUEST['emailPrincipalProveedor'];
        $data['emailp2'] = $_REQUEST['emailAltenativoProveedor'];
        $data['webp'] = $_REQUEST['paginaProveedor'];
        $data['telefonop'] = $_REQUEST['telefonoprincipalProveedor'];
        $data['telefonop2'] = $_REQUEST['telefonoalternativoProveedor'];
        $data['faxp'] = $_REQUEST['faxProveedor'];
        $data['estado'] = 1;
        $data['idpais'] = 46;
        $data['tipoEmpresa'] = 2;
        $exito = $linea->grabaProveedor($data);
        if ($exito) {
            $dataResp['valid'] = true;
            $dataResp['resp'] = 'Dato Agregado';
            $dataResp['idProveedor'] = $exito;
            echo json_encode($dataResp);
        } else {
            $dataResp['valid'] = false;
            $dataResp['resp'] = 'No se pudo Agregar';
            echo json_encode($dataResp);
        }
    }

}

?>