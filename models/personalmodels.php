<?php

class Personal extends Applicationbase {

    private $tabla = "wc_actor";
    private $tablas = "wc_actor,wc_distrito,wc_provincia,wc_departamento,wc_pais";

    function listadoPersonal() {
        $data = $this->leeRegistro($this->tabla, "", "", "");
        return $data;
    }

    function grabapersonal($data) {
        $exito = $this->grabaRegistro1($this->tabla, $data);
        return $exito;
    }

    function actualizapersonal($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function buscapersonalautocomplete($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $personal = $this->leeRegistroto($this->tabla, "", "nombres LIKE '%$tex%' or razonsocial LIKE '%$tex%'", "");
        return $personal;
    }

    function buscarcliordenventa($ruc) {
        $ruc = htmlentities($ruc, ENT_QUOTES, 'UTF-8');
        $data = $this->leeRegistro5($this->tablas, "", "ruc=$ruc", "");
        return $data;
    }

    function buscarcliordenventadni($dni) {
        $dni = htmlentities($dni, ENT_QUOTES, 'UTF-8');
        $data = $this->leeRegistro5($this->tablas, "", "dni=$dni", "");
        return $data;
    }

    function buscavendedorautocomplete($tex) {
        $tex = htmlentities($tex, ENT_QUOTES, 'UTF-8');
        $agencia = $this->leeRegistrovendedor($this->tabla, "", "nombres LIKE '%$tex%' and tipoactor='2'", "");
        return $agencia;
    }

}

?>