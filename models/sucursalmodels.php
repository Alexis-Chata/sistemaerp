<?php

class Sucursal extends Applicationbase {

    private $tabla = "wc_sucursal";
    
    function sucursalXOrdenVenta($idordenventa) {
        $data = $this->leeRegistro($this->tabla . " suc "
                                    . "inner join wc_ordenventa ov on ov.idcliente = suc.idcliente and ov.estado =1", 
                                    "ov.codigov, suc.*", 
                                    "suc.estado = 1 and ov.idordenventa=$idordenventa", "", "limit 1");
        return $data;
    }

    function verificar($idordenventa) {
        $data = $this->leeRegistro($this->tabla . " suc "
                                    . "inner join wc_ordenventa ov on ov.idcliente = suc.idcliente and ov.estado =1", 
                                    "suc.idsucursal", 
                                    "suc.estado = 1 and ov.idordenventa=$idordenventa", "", "group by suc.idsucursal");
        if (!empty($data[0]['idsucursal'])) {
            return $data[0]['idsucursal'];
        }            
        return 0;
    }

}

?>