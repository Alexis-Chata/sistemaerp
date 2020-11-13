<?php

class tipocobranza extends Applicationbase {

    private $tabla = "wc_tipocobranza";

    function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualiza($data, $idtipocobranza) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idtipocobranza=$idtipocobranza");
        return $exito;
    }

    function buscaxid($idtipocobranza) {
        $data = $this->leeRegistro($this->tabla, "", "idtipocobranza=$idtipocobranza", "");
        return $data;
    }

    function lista() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $data;
    }

    function listaPaginado($pagina) {
        $data = $this->leeRegistroPaginado($this->tabla, "", "", "", $pagina);
        return $data;
    }

    function GeneraCodigo() {
        $maxcodigo = $this->leeRegistro($this->tabla, "max(idtipocobranza)", "", "", "");
        $data['Codigo'] = 'TI' . str_pad($maxcodigo[0]['max(idtipocobranza)'] + 1, 5, '0', STR_PAD_LEFT);
        return $data['Codigo'];
    }

    public function listaNueva() {
        $data = $this->leeRegistro($this->tabla, "", "estado=0 and ntc='A'", "");
        return $data;
    }

    public function NombreTipoCobranzaxDiasVencidos($diasvencidos) {
        $sql = "Select nombre From " . $this->tabla . " Where " . $diasvencidos . " BETWEEN diaini and diafin";
        $data = $this->EjecutaConsulta($sql);
        return $data[0]['nombre'];
    }

}

?>