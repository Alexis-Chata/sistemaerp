<?php

Class DescuentoController extends ApplicationGeneral {

    private $name = 'descuento';

    function listado() {
        $descuento = New Descuento();
        $data = $descuento->listado();
        $total = count($data);
        for ($i = 0; $i < $total; $i++) {
            echo '<option value="' . $data[$i]['id'] . '">' . $data[$i]['valor'] . ' ===> (' . number_format($data[$i]['dunico'] * 100, 2) . '%)';
        }
    }

    function valorunico() {
        $descuento = New Descuento();
        $id = $_REQUEST['id'];
        $data = $descuento->buscarxid($id);
        echo json_encode($data);
    }

    public function lista() {
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        $object = $this->AutoLoadModel($this->name);
        $data['data'] = $object->lista();
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
        $desc = 1;
        $cont = 0;
        $valor = '';
        $d = 1;
        for ($i = 1; $i < 11; $i++) {
            $descuento = $data['d' . $i];
            if (!empty($descuento)) {
                $d = $d * (1 - $data['d' . $i] / 100);

                $cont++;
                if ($i == 1) {
                    $valor .= $descuento;
                } else {
                    $valor .= '+' . $descuento;
                }
            }
            $datafinal['d' . $i] = $data['d' . $i];
        }

        if (!empty($datafinal)) {
            $datafinal['valor'] = $valor;
            //$dunico=(1-$desc);
            $dunico = (1 - $d);
            $datafinal['dunico'] = $dunico;

            $graba = $object->graba($datafinal);
            $ruta['ruta'] = "/" . $this->name . "/lista";
            $this->view->show("ruteador.phtml", $ruta);
        } else {
            $ruta['ruta'] = "/" . $this->name . "/nuevo";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    public function gestionar() {
        $txtdescuento = $_REQUEST['descuento'];
        $descuento = New Descuento();
        $data = $descuento->buscarDescuento($txtdescuento, $txtdescuento/100);
        if (count($data) > 0) {
            echo json_encode($data[0]);
        } else {
            $dataNuevo['d1'] = $txtdescuento;
            $dataNuevo['valor'] = $txtdescuento;
            $dataNuevo['dunico'] = $txtdescuento/100;
            $dataNuevo['id'] = $descuento->graba($dataNuevo);
            echo json_encode($dataNuevo);
        }
    }

}

?>