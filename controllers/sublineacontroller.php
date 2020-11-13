<?php

class SublineaController extends ApplicationGeneral {

    function listar() {
        $sublinea = new Sublinea();
        session_start();
        $_SESSION['P_sublinea'] = "";
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        $data['Sublinea'] = $sublinea->listaSubLineaPaginado($_REQUEST['id']);
        $paginacion = $sublinea->paginadoSubLinea();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("sublinea/listar.phtml", $data);
    }

    function listaroptions() {
        $id = $_REQUEST['id'];
        $sublinea = new Sublinea();
        $data = $sublinea->listadoSublinea($id);
        for ($i = 0; $i < count($data); $i++) {
            echo '<option value="' . $data[$i]['idlinea'] . '">' . $data[$i]['nomlin'] . '</option>';
        }
    }

    function nuevo() {
        $linea = $this->AutoLoadModel('linea');
        $sublinea = $this->AutoLoadModel('sublinea');
        $data['linea'] = $linea->listaLineas();
        $data['sublinea'] = $sublinea->listadoSublinea('idpadre !=0');
        $this->view->show("/sublinea/nuevo.phtml", $data);
    }

    function graba() {
        $data = $_REQUEST['Sublinea'];
        $sublinea = new Sublinea();
        $exito = $sublinea->grabaSublinea($data);
        if ($exito) {
            $ruta['ruta'] = "/sublinea/listar/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function editar() {
        $idSublinea = $_REQUEST["id"];
        $linea = $this->AutoLoadModel('linea');
        $sublinea = $this->AutoLoadModel('sublinea');
        $data['Sublinea'] = $sublinea->buscaSublinea($idSublinea);
        $data['Linea'] = $linea->listaLineas();
        $this->view->show("/sublinea/editar.phtml", $data);
    }

    function actualiza() {
        $id = $_REQUEST['idsublinea'];
        $data = $_REQUEST['Sublinea'];
        $sublinea = new Sublinea();
        $exito = $sublinea->actualizaSublinea($data, $id);
        if ($exito) {
            $ruta['ruta'] = "/sublinea/listar/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function eliminar() {
        $id = $_REQUEST['id'];
        $sublinea = new Sublinea();
        $exito = $sublinea->eliminaSublinea($id);
        if ($exito) {
            $ruta['ruta'] = "/sublinea/listar";
            $this->view->show('ruteador.phtml', $ruta);
        }
    }

    function buscar() {
        $sublinea = $this->AutoLoadModel('sublinea');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_sublinea'];
        if (!empty($_REQUEST['txtBusqueda'])) {

            $_SESSION['P_sublinea'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_sublinea'];
        $filtro = "idpadre='$parametro' or nomlin like '%$parametro%' and idpadre!=0";
        $data['retorno'] = $parametro;
        $data['totregistros'] = count($sublinea->listaSublinea($filtro));
        $data['sublinea'] = $sublinea->listaSubLineaPaginadoxnombre($_REQUEST['id'], $parametro);
        $paginacion = $sublinea->paginadoSubLineaxnombre($parametro);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/sublinea/buscar.phtml", $data);
    }

    function grabaJason() {
        $linea = $this->AutoLoadModel('sublinea');
        $data['nomlin'] = $_REQUEST['nomlin'];
        $data['idpadre'] = $_REQUEST['idpadre'];
        $exito = $linea->grabaSublinea($data);
        if ($exito) {
            $dataResp['valid'] = true;
            $dataResp['resp'] = 'Dato Agregado';
            $dataResp['idlinea'] = $exito;
            echo json_encode($dataResp);
        } else {
            $dataResp['valid'] = false;
            $dataResp['resp'] = 'No se pudo Agregar';
            echo json_encode($dataResp);
        }
    }

}

?>