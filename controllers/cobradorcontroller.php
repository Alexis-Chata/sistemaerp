<?php

class CobradorController extends ApplicationGeneral {

    private $name = 'cobrador';

    public function lista() {
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        $object = $this->AutoLoadModel($this->name);
        $categoria = $this->AutoLoadModel('zona');
        $dataCobrador = $object->lista();
        $cantidadCobrador = count($dataCobrador);
        for ($i = 0; $i < $cantidadCobrador; $i++) {
            $idactor = $dataCobrador[$i]['idactor'];
            $dataBusqueda = $object->buscaxIdActorZonas($idactor);
            $cantidad = count($dataBusqueda);
            $dataCobrador[$i]['zonas'] = "<select>";
            $dataCobrador[$i]['zonas'] .= "<option>Zonas de Cobranza</option>";
            for ($y = 0; $y < $cantidad; $y++) {
                $dataCobrador[$i]['zonas'] .= "<option value='" . $dataBusqueda[$y]['idcategoria'] . "'>" . $dataBusqueda[$y]['nombrec'] . "</option>";
            }
            $dataCobrador[$i]['zonas'] .= "</select>";
        }
        $data['data'] = $dataCobrador;
        $data['titulo'] = $this->name;
        $this->view->show('/' . $this->name . '/lista.phtml', $data);
    }

    public function editar() {
        $idactor = $_REQUEST['id'];
        $categoria = $this->AutoLoadModel('zona');
        $cobrador = $this->AutoLoadModel('cobrador');
        $data['cobrador'] = $cobrador->buscaxIdActorZonas($idactor);
        $data['datacobrador'] = $cobrador->buscaxid($idactor);
        $data['categoria'] = $categoria->listacategoriaHijo();
        $this->view->show('/' . $this->name . '/editar.phtml', $data);
    }

    public function asignarZonas() {
        $idzona = $_REQUEST['idcategoria'];
        $idactor = $_REQUEST['idactor'];
        $estado = $_REQUEST['estado'];
        $cobrador = $this->AutoLoadModel('cobrador');
        $dataCobrador = $cobrador->buscaxActorxZona($idactor, $idzona);
        if ($estado == 1) {
            if (empty($dataCobrador)) {
                $data['idcobrador'] = $idactor;
                $data['idzona'] = $idzona;
                $exito = $cobrador->graba($data);
            }
        } elseif ($estado == 0) {
            if (!empty($dataCobrador)) {
                $filtro = "idcobrador='$idactor' and idzona='$idzona'";
                $exito = $cobrador->elimina($filtro);
            }
        }
        echo $exito;
    }

    function autocompletecobrador() {
        $vendedor = new Actor();
        $text = $_REQUEST['term'];
        $datos = $vendedor->buscaautocompletevcobrador($text);
        echo json_encode($datos);
    }

}

?>