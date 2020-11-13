<?php

Class bancoController extends ApplicationGeneral {

    private $name = 'banco';

    function lista_banco() {
        $banco = $this->AutoLoadModel('banco');
        $dataBanco = $banco->listado();
        json_encode($dataBanco);
    }

    public function lista() {
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        $object = $this->AutoLoadModel($this->name);
        $data['data'] = $object->listado();
        $data['titulo'] = $this->name;
        $this->view->show('/' . $this->name . '/lista.phtml', $data);
    }

    public function nuevo() {
        $data['titulo'] = $this->name;
        $this->view->show('/' . $this->name . '/nuevo.phtml', $data);
    }

    public function grabar() {
        $object = $this->AutoLoadModel($this->name);
        $data = $_REQUEST[$this->name];
        $graba = $object->graba($data);
        $ruta['ruta'] = "/" . $this->name . "/lista";
        $this->view->show("ruteador.phtml", $ruta);
    }

    public function editar() {
        $id = $_REQUEST['id'];
        $object = $this->AutoLoadModel($this->name);
        $data['data'] = $object->buscaxId($id);
        $data['titulo'] = $this->name;
        $this->view->show('/' . $this->name . '/editar.phtml', $data);
    }

    public function eliminar() {
        $id = $_REQUEST['id'];
        $object = $this->AutoLoadModel($this->name);
        $exito = $object->cambiaEstado($id);
        if ($exito) {
            $ruta['ruta'] = "/" . $this->name . "/lista";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    public function actualizar() {
        $id = $_REQUEST['idbanco'];
        $data = $_REQUEST[$this->name];
        $object = $this->AutoLoadModel($this->name);
        $exito = $object->actualiza($data, $id);
        if ($exito) {
            $ruta['ruta'] = "/" . $this->name . "/lista";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

}

?>