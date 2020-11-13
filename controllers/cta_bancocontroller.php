<?php

Class cta_bancoController extends ApplicationGeneral {

    private $name = 'cta_banco';

    function listaCta_banco() {
        $idbanco = $_REQUEST['idbanco'];
        $cta_banco = $this->AutoLoadModel('cta_banco');
        $dataCuenta = $cta_banco->buscaxIdBanco($idbanco);
        $cantidad = count($dataCuenta);
        $lista = "<option value=''>Cuenta Corriente</option>";
        if ($cantidad > 0) {
            foreach ($dataCuenta as $data) {
                $lista .= "<option value='" . $data['idctabanco'] . "''>" . $data['nomalm'] . ' / ' . $data['descripcion'] . "</option>";
            }
        }
        echo $lista;
    }

    public function lista() {
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        $object = $this->AutoLoadModel($this->name);
        $data['data'] = $object->listaBanco();
        $data['titulo'] = $this->name;
        $this->view->show('/' . $this->name . '/lista.phtml', $data);
    }

    public function nuevo() {
        $data['titulo'] = $this->name;
        $banco = $this->AutoLoadModel('banco');
        $empresa = $this->AutoLoadModel('almacen');
        $data['banco'] = $banco->listado();
        $data['empresa'] = $empresa->listado();
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
        $banco = $this->AutoLoadModel('banco');
        $empresa = $this->AutoLoadModel('almacen');
        $object = $this->AutoLoadModel($this->name);
        $data['banco'] = $banco->listado();
        $data['empresa'] = $empresa->listado();
        $data['data'] = $object->buscarxId($id);
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
        $id = $_REQUEST['idctabanco'];
        $data = $_REQUEST[$this->name];
        $object = $this->AutoLoadModel($this->name);
        $exito = $object->actualiza($data, $id);
        if ($exito) {
            $ruta['ruta'] = "/" . $this->name . "/lista";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    public function buscaCtaxBanco() {
        $idbanco = $_REQUEST['idbanco'];
        $cta_banco = $this->AutoLoadModel('cta_banco');
        $dataCuenta = $cta_banco->buscaxIdBanco($idbanco);
        $resp = "<option value=''>Seleccione Cuenta Corriente</option>";
        foreach ($dataCuenta as $value) {
            $resp .= "<option value='" . $value['idctabanco'] . "'>" . $value['razsocalm'] . ' / ' . $value['descripcion'] . "</option>";
        }
        echo $resp;
    }

}

?>