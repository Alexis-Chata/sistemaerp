<?php

class TransporteController extends ApplicationGeneral {

    function nuevo() {
        $this->view->show("transporte/nuevo.phtml", $data);
    }

    function graba() {
        $dataTransporte = $_REQUEST['Transporte'];
        $transporte = new Transporte();
        $exito = $transporte->grabar($dataTransporte);
        if ($exito) {
            $ruta['ruta'] = "/transporte/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function editar() {
        $id = $_REQUEST['id'];
        $transporte = new Transporte();
        $data['Transporte'] = $transporte->buscarxId($id);
        $this->view->show("transporte/editar.phtml", $data);
    }

    function actualiza() {
        $dataTransporte = $_REQUEST['Transporte'];
        $id = $_REQUEST['idTransporte'];
        $transporte = new Transporte();
        $exito = $transporte->actualizar($id, $dataTransporte);
        if ($exito) {
            $ruta['ruta'] = "/transporte/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function eliminar() {
        $id = $_REQUEST['id'];
        $transporte = new Transporte();
        $exito = $transporte->cambiarEstado($id);
        if ($exito) {
            $ruta['ruta'] = "/transporte/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function buscar() {
        $transporte = New Transporte();
        $data = $transporte->listaTodo();
        $objeto = $this->formatearparakui($data);
        header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        echo json_encode($objeto);
    }

    function verificarNombre() {
        $nombre = $_REQUEST['id'];
        $transporte = new Transporte();
        $existe = $transporte->verificaExiste("trazonsocial='$nombre'");
        echo '{"existe":"' . $existe . '"}';
    }

    function verificarRuc() {
        $ruc = $_REQUEST['id'];
        $transporte = new Transporte();
        $existe = $transporte->verificaExiste("truc='$ruc'");
        echo '{"existe":"' . $existe . '"}';
    }

    function autocomplete() {
        $transporte = new Transporte();
        $text = $_REQUEST['term'];
        $datos = $transporte->buscaautocomplete($text);
        echo json_encode($datos);
    }

    function lista() {
        $transporte = $this->AutoLoadModel('transporte');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_transporte'] = "";

        $data['transporte'] = $transporte->listaTransportePaginado($_REQUEST['id']);
        $paginacion = $transporte->paginadoTransporte();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);

        $this->view->show("/transporte/lista.phtml", $data);
    }

    function busca() {
        $transporte = $this->AutoLoadModel('transporte');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_transporte'] = $_REQUEST['txtBusqueda'];/*
        $_SESSION['P_transporte'];
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_transporte'] = $_REQUEST['txtBusqueda'];
        }*/
        $parametro = $_REQUEST['txtBusqueda'];
        $filtroTransporte['txtIdentificador'] = $_REQUEST['txtIdentificador'];
        $filtroTransporte['txtRazonSocial'] = $_REQUEST['txtRazonSocial'];
        $filtroTransporte['txtRuc'] = $_REQUEST['txtRuc'];
        $filtroTransporte['txtDireccion'] = $_REQUEST['txtDireccion'];
        $data['filtroTransporte'] = $filtroTransporte;
        
        $paginacion = $transporte->paginadoTransportexnombreymasfiltros($parametro, $filtroTransporte);
        $data['retorno'] = $parametro;
        $data['transporte'] = $transporte->listaTransportePaginadoxnombreymasfiltros($_REQUEST['id'], $parametro, $filtroTransporte);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = count($transporte->buscarxParametro($parametro));
        $this->view->show("/transporte/busca.phtml", $data);
    }

}

?>