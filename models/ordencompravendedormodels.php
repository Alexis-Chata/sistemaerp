<?php

class Ordencompravendedor extends Applicationbase {

    private $tabla = "wc_ordencompravendedor";

    function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualiza($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }
    
    function buscarXfiltro($filtro) {
        $data = $this->leeRegistro($this->tabla,"",$filtro,"");
	return $data;
    }

    function buscarColaboradorXOC($idOrdenCompra) {
        $data = $this->leeRegistro("wc_ordencompravendedor ocv inner join wc_actor actor on actor.idactor = ocv.idvendedor", "actor.idactor, actor.nombres, actor.apellidopaterno, actor.apellidomaterno", "ocv.estado=1 and ocv.idordencompra=$idOrdenCompra", "");
        return $data;
    }

}

?>