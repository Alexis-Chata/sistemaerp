<?php

Class Estructuradecostos extends Applicationbase{
        
    function graba($data) {
        $exito = $this->grabaRegistro('wc_estructuradecostos', $data);
        return $exito;
    }
    
    function grabaDetalle($data) {
        $exito = $this->grabaRegistro('wc_detalleestructuradecostos', $data);
        return $exito;
    }
    
    function verEstructuraCostos($idedc) {
        $data = $this->leeRegistro("wc_estructuradecostos", "*", "idestructuradecostos='$idedc'", "");
        return $data;
    }
    
    function listadetallexestructuradecostos($idedc) {
        $data = $this->leeRegistro("wc_detalleestructuradecostos edc " .
                                   "inner join wc_producto producto on producto.idproducto = edc.idproducto " . 
                                   "Left Join wc_marca m On producto.idmarca = m.idmarca " .
                                   "Left Join wc_unidadmedida um On um.idunidadmedida = producto.unidadmedida", 
                                   "edc.*, producto.nompro, producto.codigopa, m.nombre as marca, um.nombre as unidadmedida", "edc.idestructuradecostos='$idedc'", "");
        return $data;
    }
    
}

?>