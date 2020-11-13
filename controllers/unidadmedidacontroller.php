<?php

class UnidadmedidaController extends ApplicationGeneral {

    function lista() {
        $unidadmedida = new Unidadmedida();
        $data['Unidadmedida'] = $unidadmedida->listadoUnidadmedidas();
        $this->view->show("/unidadmedida/listar.phtml", $data);
    }

    function nuevo() {
        $this->view->show("/unidadmedida/nuevo.phtml");
    }

    function graba() {
        $data = $_REQUEST['Unidadmedida'];
        $unidadmedida = new Unidadmedida();
        $exito = $unidadmedida->grabaUnidadmedida($data);
        if ($exito) {
            $ruta['ruta'] = "/unidadmedida/lista";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function editar() {
        $id = $_REQUEST['id'];
        $unidadMedida = new Unidadmedida();
        $data['Unidadmedida'] = $unidadMedida->buscaUnidadMedida($id);
        $this->view->show("/unidadmedida/editar.phtml", $data);
    }

    function actualiza() {
        $id = $_REQUEST['idUnidadMedida'];
        $data = $_REQUEST['Unidadmedida'];
        $unidadMedidad = new Unidadmedida();
        $exito = $unidadMedidad->actualizaUnidadMedida($data, $id);
        if ($exito) {
            $ruta['ruta'] = "/unidadmedida/lista";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function elimina() {
        $id = $_REQUEST['id'];
        $unidadMedida = new Unidadmedida();
        $exito = $unidadMedida->eliminaUnidadMedida($id);
        echo $exito;
        if ($exito) {
            $ruta['ruta'] = "/unidadmedida/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function grabaJason() {
        $linea = $this->AutoLoadModel('unidadmedida');
        $data['nombre'] = $_REQUEST['nombre'];
        $data['codigo'] = $_REQUEST['codigo'];
        $data['estado'] = 1;
        $exito = $linea->grabaUnidadmedida($data);
        if ($exito) {
            $dataResp['valid'] = true;
            $dataResp['resp'] = 'Dato Agregado';
            $dataResp['idUnidadMedida'] = $exito;
            echo json_encode($dataResp);
        } else {
            $dataResp['valid'] = false;
            $dataResp['resp'] = 'No se pudo Agregar';
            echo json_encode($dataResp);
        }
    }

}

?>