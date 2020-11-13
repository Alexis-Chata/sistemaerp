<?php

class Ordengasto extends Applicationbase {

    private $tabla = "wc_ordengasto";

    function graba($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualiza($data, $idOrdenGasto) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idordengasto=$idOrdenGasto");
        return $exito;
    }

    function buscaxid($idTipoGasto) {
        $data = $this->leeRegistro($this->tabla, "", "idordengasto=$idOrdenGasto and estado=1", "");
        return $data;
    }

    function buscaxFiltro($filtro) {
        $data = $this->leeRegistro($this->tabla, "", $filtro, "");
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

    function totalGuia($idOrdenVenta) {
        $data = $this->leeRegistro($this->tabla, "sum(importegasto) as total", "idordenventa='$idOrdenVenta' and Estado=1", "", "");
        return $data[0]['total'];
    }

    function importeGasto($idOrdenVenta, $idTipoGasto) {
        $data = $this->leeRegistro($this->tabla, "importegasto", "idordenventa='$idOrdenVenta' and idtipogasto='$idTipoGasto' and Estado=1", "", "");
        return $data[0]['importegasto'];
    }

    function InactivaxIdOrdenventa($idOrdenVenta) {
        return $this->inactivaRegistro($this->tabla, "idordenventa=" . $idOrdenVenta);
    }

    public function FunctionName($value = '') {
        $sql = "SELECT doc.iddetalleordencobro,ov.codigov,doc.fechagiro,doc.fvencimiento,doc.fechapago,doc.situacion,
                        CASE doc.formacobro when 1 then 'Contado' when 2 then 'Credito' when 3 then 'Letras' END as condicioncobro,tg.nombre,og.importegasto 
                        FROM celestium_produccion.wc_detalleordencobro doc
                        Inner Join wc_ordencobro oc On doc.idordencobro=oc.idordencobro
                        Inner Join wc_ordenventa ov On oc.idordenventa=ov.idordenventa
                        Inner Join wc_ordengasto og On oc.idordenventa=og.idordenventa
                        Inner Join wc_tipogasto tg On og.idtipogasto=tg.idtipogasto
                        where tg.idtipogasto=6 and doc.tipogasto=6 and (doc.situacion='' or doc.situacion='cancelado')
                        and fechagiro between '2014-08-01' and '2014-08-30'
                        order by doc.fechagiro desc";
    }

    public function ImporteGastoxIdDetalleOrdenCobro($iddetalleordencobro) {
        $sql = "SELECT og.importegasto 
			FROM wc_detalleordencobro doc
			Inner Join wc_ordencobro oc On doc.idordencobro=oc.idordencobro
			Inner Join wc_ordenventa ov On oc.idordenventa=ov.idordenventa
			Inner Join wc_ordengasto og On oc.idordenventa=og.idordenventa
			Inner Join wc_tipogasto tg On og.idtipogasto=tg.idtipogasto
			where tg.idtipogasto=6 and doc.tipogasto=6 and (doc.situacion='' or doc.situacion='cancelado')
			and doc.iddetalleordencobro=" . $iddetalleordencobro;
        $data = $this->EjecutaConsulta($sql);
        return $data[0]['importegasto'];
    }

}

?>