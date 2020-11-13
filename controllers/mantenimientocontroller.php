<?php

class MantenimientoController extends ApplicationGeneral {

    private $mostrar = 5;
    
    function almacen() {
        $tamanio = 10;
        $id = $_REQUEST['id'];
        $url = "/" . $_REQUEST['url'];
        $dataAlmacen = New Almacen();
        $opciones = new general();
        $datos['Opcion'] = $opciones->buscaOpcionexurl($url);
        $datos['Modulo'] = $opciones->buscaModulosxurl($url);
        $datos['almacen'] = $dataAlmacen->listadoAlmacen($id, $tamanio);
        $datos['Paginacion'] = 1;
        $datos['Pagina'] = 1;
        $this->view->show("mantenimiento/almacen.phtml", $datos);
    }
    
    function proveedor() {
        $tamanio = 10;
        $proveedor = new Proveedor();
        $opciones = new general();
        $url = "/" . $_REQUEST['url'];
        $data['Opcion'] = $opciones->buscaOpcionexurl($url);
        $data['Modulo'] = $opciones->buscaModulosxurl($url);
        $data['Proveedor'] = $proveedor->listadoProveedores();
        $data['Paginacion'] = $proveedor->Paginacion($tamanio);
        $data['Pagina'] = 1;
        $this->view->show("mantenimiento/proveedor.phtml");
    }

    function cliente() {
        $dataCliente = New Cliente();
        $opciones = new general();
        $zona = new Zona();
        $cli = new Cliente();
        $url = "/" . $_REQUEST['url'];
        $data['Opcion'] = $opciones->buscaOpcionexurl($url);
        $data['Modulo'] = $opciones->buscaModulosxurl($url);
        $id = $_REQUEST['id'];
        $tamanio = 10;
        $data['cliente'] = $dataCliente->listadoCliente($id, $tamanio);
        $total = count($data['cliente']);
        for ($i = 0; $i < $total; $i++) {
            if ($data['cliente'][$i]['idzona'] != '' && $data['cliente'][$i]['idzona'] != 0) {
                $data['cliente'][$i]['nombrezona'] = $zona->nombrexid($data['cliente'][$i]['idzona']);
            }
        }
        $data['Paginacion'] = $cli->paginacion($tamanio, "");
        ;
        $data['Pagina'] = 1;
        $this->view->show("mantenimiento/cliente.phtml");
    }

    function clientezona() {
        $dataClienteZona = New ClienteZona();
        $opciones = new general();
        $zona = new Zona();
        $cliente = new Cliente();
        $url = "/" . $_REQUEST['url'];
        $id = $_REQUEST['id'] != '' ? $_REQUEST['id'] : 1;
        $data['Opcion'] = $opciones->buscaOpcionexurl($url);
        $data['Modulo'] = $opciones->buscaModulosxurl($url);
        $tamanio = 10;
        $data['ClienteZona'] = $dataClienteZona->listado($id, $tamanio);
        $total = count($data['ClienteZona']);
        for ($i = 0; $i < $total; $i++) {
            if ($data['ClienteZona'][$i]['idcliente'] != '' && $data['ClienteZona'][$i]['idcliente'] != 0) {
                $data['ClienteZona'][$i]['nombrecli'] = $cliente->nombrexid($data['ClienteZona'][$i]['idcliente']);
            }
            if ($data['ClienteZona'][$i]['idzona'] != '' && $data['ClienteZona'][$i]['idzona'] != 0) {
                $data['ClienteZona'][$i]['nombrezona'] = $zona->nombrexid($data['ClienteZona'][$i]['idzona']);
            }
        }
        $data['Paginacion'] = 1;
        $data['Pagina'] = 1;
        $this->view->show("/mantenimiento/clientezona.phtml");
    }

    function producto() {
        $producto = new Producto();
        $opciones = new general();
        $tamanio = 10;
        $id = $_REQUEST['id'];
        $url = "/" . $_REQUEST['url'];
        $data['Producto'] = $producto->buscarxnombre($id, $tamanio, "");
        $data['Paginacion'] = $producto->paginacion($tamanio, "");
        $data['RutaImagen'] = $this->rutaImagenesProducto();
        $data['Pagina'] = 1;
        $this->view->show("mantenimiento/producto.phtml");
    }

    function producto2() {
        $this->view->show("producto/listado.phtml");
    }

    function linea() {
        $linea = new Linea();
        $data['LineaPadre'] = $linea->listadolineas("idpadre=0");
        $this->view->show("mantenimiento/linea.phtml", $data);
    }

    function zona() {
        $zona = new Zona();
        $data['Categoria'] = $zona->listacategorias($id, $tamanio, "");
        $this->view->show("/mantenimiento/zona.phtml", $data);
    }

    function vendedor() {
        $this->view->show("/mantenimiento/vendedor.phtml");
    }

    function transporte() {
        $this->view->show("/mantenimiento/transporte.phtml");
    }

    function condicionletra() {
        $data['origen'] = $_REQUEST['id'];
        $this->view->show("/mantenimiento/condicionletra.phtml", $data);
    }

    function jefelinea() {
        $vendedor = $this->AutoLoadModel('actor');
        $datos['Jefes'] = $vendedor->listadojefesdelinea();
        $datos['Vendedor'] = $vendedor->listadoVendedoresTodos();
        $this->view->show("/mantenimiento/jefelinea.phtml", $datos);
    }

    function jefelinea_gestor() {
        $idactor = $_REQUEST['idactor'];
        if (!empty($idactor)) {
            $actor = $this->AutoLoadModel('actor');
            $actor->cambiarJefeLinea($idactor);
        }
    }
    
    function ofertas() {
        $oferta = $this->AutoLoadModel('oferta');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['arrayTipocobro'] = $archivoConfig['TipoCobro'];
        $data['ofertas'] = $oferta->listaOfertaPaginado($_REQUEST['id'], $_REQUEST['txtBusqueda']);
        $paginacion = $oferta->paginadoOfertas();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("mantenimiento/ofertas.phtml", $data);
    }
    
    function oferta () {
        if (!empty($_REQUEST['id'])) {
            $idoferta = $_REQUEST['id'];
            $ofertamodel = $this->AutoLoadModel('oferta');
            $oferta = $ofertamodel->listaxId($idoferta);
            if (isset($oferta)) {
                $data['ofertasproducto'] = $ofertamodel->listaxIdproducto($oferta[0]['idproducto']);
                $data['oferta'] = $oferta;
            }
            $archivoConfig = parse_ini_file("config.ini", true);
            $data['arrayTipocobro'] = $archivoConfig['TipoCobro'];
        }
        $this->view->show("mantenimiento/oferta.phtml", $data);
    }
    
    function ofertaeliminar() {
        if (!empty($_REQUEST['id'])) {
            $ofertamodel = $this->AutoLoadModel('oferta');
            $dataOferta['estado'] = 0;
            $ofertamodel->actualizaOferta($dataOferta, $_REQUEST['id']);
        }
        $_REQUEST['id'] = 1;
        $this->ofertas();
    }
    
    function verofertas_producto() {
        $idproducto = $_REQUEST['idproducto'];
        $productomodel = $this->AutoLoadModel('producto');
        $dataProducto = $productomodel->buscaProducto($idproducto);
        $preciolista = 'Error';
        $preciolistadolares = 'Error';
        $tempOfertas = '';
        $tempTipocoro = '<option value="">Tipo Cobro</option>';
        if (count($dataProducto) > 0) {
            $preciolista = ' Precio Lista S/ ' . $dataProducto[0]['preciolista'];
            $preciolistadolares = ' Precio Lista US $ ' . $dataProducto[0]['preciolistadolares'];
            $ofertamodel = $this->AutoLoadModel('oferta');
            $ofertasproducto = $ofertamodel->listaxIdproducto($idproducto);
            $tam = count($ofertasproducto);
            $arrayExiste = array();
            $archivoConfig = parse_ini_file("config.ini", true);
            $arrayTipocobro = $archivoConfig['TipoCobro'];
            for ($i = 0; $i < $tam; $i++) {
                $arrayExiste[$ofertasproducto[$i]['tipocobro']] = 1;
                $tempOfertas .= '<li><a href="/mantenimiento/oferta/' . $ofertasproducto[$i]['idoferta'] . '" class="preciooferta"> .:: Oferta ' . $arrayTipocobro[$ofertasproducto[$i]['tipocobro']] . ' ::.</a></li>';
            }
            for ($j = 1; $j <= count($arrayTipocobro); $j++) {
                if (!isset($arrayExiste[$j])) {
                    $tempTipocoro .= '<option value="' . $j . '">' . $arrayTipocobro[$j] . '</option>';
                }
            }
        }
        $respuesta['ofertas'] = $tempOfertas;
        $respuesta['Tipocbro'] = $tempTipocoro;
        $respuesta['preciolista'] = $preciolista;
        $respuesta['preciolistadolares'] = $preciolistadolares;
        echo json_encode($respuesta);
    }
    
    function grabaoferta() {
        $ofertamodel = $this->AutoLoadModel('oferta');
        $grabaOferta = $_REQUEST['oferta'];
        if ($_REQUEST['txtidOferta'] > 0) {
            $idoferta = $_REQUEST['txtidOferta'];
            $ofertamodel->actualizaOferta($grabaOferta, $idoferta);
        } else if ($_REQUEST['txtidproducto'] > 0) {
            $idproducto = $_REQUEST['txtidproducto'];
            $grabaOferta['idproducto'] = $idproducto;
            $xisteOferta = $ofertamodel->listaxIdproducto($idproducto, $grabaOferta['tipocobro']);
            if (isset($xisteOferta)) {
                $ofertamodel->actualizaOferta($grabaOferta, $xisteOferta[0]['idoferta']);
            } else {
                $ofertamodel->grabaOferta($grabaOferta);
            }
        }
        $this->ofertas();
    }
    
    function submotivosdevolucion(){
        $data['MotivoDevolucion']=$this->configIniTodo('MotivoDevolucion');
        $submotivo=new Submotivodevolucion();
	$data['SubMotivo']=$submotivo->listado();
	$this->view->show("/mantenimiento/submotivosdevolucion.phtml",$data);
    }
    function nuevosubmotivodevolucion(){
        $data['MotivoDevolucion']=$this->configIniTodo('MotivoDevolucion');
        $this->view->show("/submotivosdevoluciones/nuevo.phtml",$data);
    }
    function grabarsubmotivodevolucion(){
        $data=$_REQUEST['SubMotivo'];
        if($data['tipo']>0){
            $submotivo=new Submotivodevolucion();
            $exito=$submotivo->grabar($data);
            if($exito){
                $ruta['ruta']="/mantenimiento/submotivosdevolucion";
                $this->view->show("ruteador.phtml",$ruta);
            }
        } else {
            echo 'Seleccione un motivo';
        }
    }
    function eliminarsubmotivodevolucion(){
        $id=$_REQUEST['id'];
        $submotivo=new Submotivodevolucion();
        $datasubmot['estado']=0;
        $exito=$submotivo->actualiza($datasubmot,"idsubmotivodevolucion='".$id."'");
        if($exito){
            $ruta['ruta']="/mantenimiento/submotivosdevolucion";
            $this->view->show("ruteador.phtml",$ruta);
        }
    }
    function editarsubmotivodevolucion(){
        $data['MotivoDevolucion']=$this->configIniTodo('MotivoDevolucion');
        $id=$_REQUEST['id'];
	$submotivo=new Submotivodevolucion();
	$data['SubMotivo']=$submotivo->buscar($id);
	$this->view->show("/submotivosdevoluciones/editar.phtml",$data);
    }
    function actualizasubmotivodevolucion(){
        $id=$_REQUEST['idSubMot'];
        $data=$_REQUEST['SubMotivo'];
        $submotivo=new Submotivodevolucion();
        $exito=$submotivo->actualiza($data,"idsubmotivodevolucion='".$id."'");
        if($exito){
                $ruta['ruta']="/mantenimiento/submotivosdevolucion";
                $this->view->show("ruteador.phtml",$ruta);
        }
    }

}

?>