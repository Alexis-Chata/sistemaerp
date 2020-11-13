<?php

class ConceptomovimientoController extends ApplicationGeneral {

    function listar() {
        $conceptoMovimiento = new Conceptomovimiento();
        $data['Conceptomovimiento'] = $conceptoMovimiento->listadoConceptosmovimiento();
        $this->view->show("/conceptomovimiento/listar.phtml", $data);
    }

    function listarOptions() {
        $id = $_REQUEST['id'];
        $conceptoMovimiento = new Conceptomovimiento();
        $data = $conceptoMovimiento->listadoOptionsConceptosMovimiento($id);
        for ($i = 0; $i < count($data); $i++) {
            echo '<option value="' . $data[$i]['idconceptomovimiento'] . '">' . $data[$i]['nomconmov'] . '</option>';
        }
    }

    function nuevo() {
        $tipoMovimiento = new Tipomovimiento();
        $conceptoMovimiento = new Conceptomovimiento();
        $data['Tipomovimiento'] = $tipoMovimiento->listadoTiposmovimiento();
        $this->view->show("/conceptomovimiento/nuevo.phtml", $data);
    }

    function graba() {
        $data = $_REQUEST['Conceptomovimiento'];
        $conceptoMovimiento = new Conceptomovimiento();
        $exito = $conceptoMovimiento->grabaConceptomovimiento($data);
        if ($exito) {
            $ruta['ruta'] = "/conceptomovimiento/listar";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function editar() {
        $id = $_REQUEST['id'];
        $idTipoMovimiento = $_REQUEST["parameters"][1];
        $tipoMovimiento = new Tipomovimiento();
        $conceptoMovimiento = new Conceptomovimiento();
        $data['idTipoMovimiento'] = $idTipoMovimiento;
        $data['Tipomovimiento'] = $tipoMovimiento->listadoTiposmovimiento();
        $data['Conceptomovimiento'] = $conceptoMovimiento->editaConceptoMovimiento($id);
        $this->view->show("/conceptomovimiento/editar.phtml", $data);
    }

    function actualiza() {
        $id = $_REQUEST['idConceptoMovimiento'];
        $data = $_REQUEST['Conceptomovimiento'];
        $conceptoMovimiento = new Conceptomovimiento();
        $exito = $conceptoMovimiento->actualizaConceptoMovimiento($data, $id);
        if ($exito) {
            $ruta['ruta'] = "/conceptomovimiento/listar";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function elimina() {
        $id = $_REQUEST['id'];
        $conceptoMovimiento = new Conceptomovimiento();
        $exito = $conceptoMovimiento->eliminaConceptoMoviento($id);
        if ($exito) {
            $ruta['ruta'] = "/conceptomovimiento/listar";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

}

?>