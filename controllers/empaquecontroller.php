<?php

class EmpaqueController extends ApplicationGeneral {

    function listar() {
        $empaque = new Empaque();
        $data['Empaque'] = $empaque->listarEmpaque();
        $this->view->show("/empaque/listar.phtml", $data);
    }

    function nuevo() {
        $this->view->show("/empaque/nuevo.phtml");
    }

    function graba() {
        $data = $_REQUEST['Empaque'];
        $empaque = new Empaque();
        $exito = $empaque->guardaEmpaque($data);
        if ($exito) {
            $ruta['ruta'] = "/empaque/listar";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function editar() {
        $id = $_REQUEST['id'];
        $empaque = new Empaque();
        $data['Empaque'] = $empaque->buscaEmpaque($id);
        $this->view->show("/empaque/editar.phtml", $data);
    }

    function actualiza() {
        $id = $_REQUEST['idEmpaque'];
        $data = $_REQUEST['Empaque'];
        $empaque = new Empaque();
        $exito = $empaque->actualizaEmpaque($data, $id);
        if ($exito) {
            $ruta['ruta'] = "/empaque/listar/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function elimina() {
        $id = $_REQUEST['id'];
        $empaque = new Empaque();
        $exito = $empaque->eliminaEmpaque($id);
        if ($exito) {
            $ruta['ruta'] = "/empaque/listar/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

}

?>