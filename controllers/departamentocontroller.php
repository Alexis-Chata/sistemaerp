<?php

class DepartamentoController extends ApplicationGeneral {

    function listaroptions() {
        $codpais = $_REQUEST['id'];
        $departamento = new Departamento();
        $data = $departamento->listadoOptionsdepartamento($codpais);
        $tot = count($data);
        for ($i = 0; $i < $tot; $i++) {
            echo '<option value="' . $data[$i]['iddepartamento'] . '">' . $data[$i]['nombredepartamento'];
        }
    }

}

?>