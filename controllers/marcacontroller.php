<?php

class MarcaController extends ApplicationGeneral {

    function nuevo() {
        $this->view->show("/marca/nuevo.phtml", $data);
    }

    function graba() {
        $marca = $this->AutoLoadModel('marca');
        $data = $_REQUEST['marca'];
        $exito = $marca->grabaMarca($data);
        $ruta['ruta'] = "/marca/lista/";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function editar() {
        $marca = $this->AutoLoadModel('marca');
        $id = $_REQUEST['id'];
        $data['marca'] = $marca->listaxId($id);
        $this->view->show("/marca/editar.phtml", $data);
    }

    function lista() {
        $marca = $this->AutoLoadModel('marca');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_Marca'] = "";

        $data['marca'] = $marca->listaMarcaPaginado($_REQUEST['id']);
        $paginacion = $marca->paginadoMarca();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("marca/lista.phtml", $data);
    }

    function actualiza() {
        $marca = $this->AutoLoadModel('marca');
        $data = $_REQUEST['marca'];
        $id = $_REQUEST['codigo'];
        $exito = $marca->actualizaMarca($data, $id);
        $ruta['ruta'] = "/marca/lista/";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function elimina() {
        $marca = $this->AutoLoadModel('marca');
        $id = $_REQUEST['id'];
        $exito = $marca->eliminaMarca($id);
        $ruta['ruta'] = "/marca/lista/";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function buscar() {
        $marca = $this->AutoLoadModel('marca');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_Marca'];
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_Marca'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_Marca'];
        $paginacion = $marca->paginadoMarcaxNombre($parametro);
        $data['retorno'] = $parametro;
        $data['marca'] = $marca->listaMarcaPaginadoxNombre($_REQUEST['id'], $parametro);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = count($marca->listaxNombre($parametro));

        $this->view->show("/marca/buscar.phtml", $data);
    }

    function grabaJason() {
        $linea = $this->AutoLoadModel('marca');
        $data['nombre'] = $_REQUEST['nombre'];
        $data['estado'] = 1;
        $exito = $linea->grabaMarca($data);
        if ($exito) {
            $dataResp['valid'] = true;
            $dataResp['resp'] = 'Dato Agregado';
            $dataResp['idMarca'] = $exito;
            echo json_encode($dataResp);
        } else {
            $dataResp['valid'] = false;
            $dataResp['resp'] = 'No se pudo Agregar';
            echo json_encode($dataResp);
        }
    }

    function listaMarca() {
        $marca = $this->AutoLoadModel('marca');
        $dataBusqueda = $marca->listado();
        $cantidad = count($dataBusqueda);
        $option = "<option value=''>Seleccione Marca</option>";
        for ($i = 0; $i < $cantidad; $i++) {
            $option .= "<option value=" . $dataBusqueda[$i]['idmarca'] . ">" . $dataBusqueda[$i]['nombre'] . "</option>";
        }
        echo $option;
    }

}

?>