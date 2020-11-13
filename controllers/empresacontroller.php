<?php

class empresacontroller extends ApplicationGeneral {

    private $name = 'empresa';

    public function lista() {
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        $object = $this->AutoLoadModel($this->name);
        $data['data'] = $object->listado();
        $data['tipoEmpresa'] = $object->listadoTipoEmpresa();
        $data['titulo'] = $this->name;
        $this->view->show('/' . $this->name . '/lista.phtml', $data);
    }

    public function nuevo() {
        $object = $this->AutoLoadModel($this->name);
        $data['tipoEmpresa'] = $object->listadoTipoEmpresa();
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
        $data['tipoEmpresa'] = $object->listadoTipoEmpresa();
        $data['data'] = $object->buscaxId($id);
        $data['titulo'] = $this->name;
        $this->view->show('/' . $this->name . '/editar.phtml', $data);
    }

    public function actualizar() {
        $id = $_REQUEST['idobjeto'];
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