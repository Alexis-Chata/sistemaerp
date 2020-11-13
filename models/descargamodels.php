<?php
Class Descarga extends Applicationbase{
    
    private $tabla="wc_descarga";
    
    public function graba($data) {
        $exito=$this->grabaRegistro($this->tabla,$data);
	return $exito;
    }
    
    public function ultimaDescarga() {
        $data=$this->leeRegistro($this->tabla,"*","estado=1","", "order by iddescarga desc limit 1");
	return $data;
    }

    public function lista($fecha) {
        $data=$this->leeRegistro($this->tabla . " wd 
                inner join wc_cliente wc on wc.fechacreacion > '" . $fecha . "' and wc.estado=1 and (case when wc.tipocliente=2 then length(wc.ruc)=11 else length(wc.dni)=8 end)
                inner join ubigeo ubi on ubi.iddistrito = wc.iddistrito",
                "(case when wc.tipocliente=2 then '6' else '1' end) as tipocliente, 
                (case when wc.tipocliente=2 then wc.ruc else wc.dni end) as documento,
                wc.razonsocial, wc.direccion, ubi.*,
                concat(wc.telefono, ' ', wc.celular) as telefonos, 
                wc.email",
                "wd.estado=1 and (case when wc.tipocliente=2 then length(wc.ruc)=11 else length(wc.dni)=8 end)","", "group by documento");
	return $data;
    }
        
}

?>