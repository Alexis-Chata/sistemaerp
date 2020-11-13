<?php

class DistritoController extends ApplicationGeneral {

    function listaroptions() {
        $relacion = $_REQUEST['id'];
        $distrito = new Distrito();
        $data = $distrito->listadoOptionsdistrito($relacion);
        $tot = count($data);
        for ($i = 0; $i < $tot; $i++) {
            echo '<option value="' . $data[$i]['iddistrito'] . '">' . $data[$i]['nombredistrito'];
        }
    }

}

?>