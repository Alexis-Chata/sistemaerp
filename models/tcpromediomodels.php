<?php
Class Tcpromedio extends Applicationbase{
    
    private $tabla;
    function __construct(){
            $this->tabla="wc_tcpromedio";
    }
    
    function getTipocambio ($mes, $anio) {
        $data = $this->leeRegistro($this->tabla, "valor", "mes='$mes' and anio='$anio'", "");
        return $data[0]['valor'];
    }
    
}
?>