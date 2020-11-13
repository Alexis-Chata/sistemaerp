<?php

class Ipv extends Applicationbase {

    private $tabla = "wc_ipv";

    function listadoxProducto($idProducto) {
        $data = $this->leeRegistro($this->tabla, "", "idproducto='$idProducto' and estado=1", "", "");
        return $data;
    }
    
    function grabar($data) {
        $this->grabaRegistro($this->tabla, $data);
    }
    
    function buscarDuplicidad($idProducto, $fechas, $TipoDoc, $Serie, $Numero, $tipoMovimiento, $Cantidad, $CostoUnitario) {
        $data = $this->leeRegistro($this->tabla, "", 
                "idproducto='" . $idProducto . "' and " .
                "fecha='" . $fechas . "' and " .
                "tipodoc='" . $TipoDoc . "' and " . 
                "serie='" . $Serie . "' and " .
                "numero='" . $Numero . "' and " .
                "tipomov='" . $tipoMovimiento . "' and " .
                "cant='" . $Cantidad . "' and " .
                "costouni='" . $CostoUnitario . "'"
                , "", "");
        return $data;
    }
    
    function eliminar($idipv) {
        $data['estado'] = 0;
        $exito=$this->actualizaRegistro($this->tabla,$data,"idipv=$idipv");
	return $exito;
    }

}

?>