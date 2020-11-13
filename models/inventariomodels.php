<?php

class inventario extends Applicationbase {

    private $tabla = 'wc_inventario';

    function listadoInventarioxId($idinventario) {
        $movimiento = $this->leeRegistro($this->tabla, "", "idinventario='$idinventario'", "", "");
        return $movimiento;
    }

    function listado() {
        $data = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $data;
    }

    function listadoConFecha() {
        $sql = "select * from wc_inventario where estado=1 order by idinventario desc";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function buscaxId($idinventario) {
        $data = $this->leeRegistro($this->tabla, "", "idinventario='$idinventario' and estado=1", "");
        return $data;
    }

    function actualiza($data, $idinventario) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idinventario=$idinventario");
        return $exito;
    }

    function cambiaEstado($idinventario) {
        $exito = $this->inactivaRegistro($this->tabla, "idinventario=$idinventario");
        return $exito;
    }

    function graba($data) {
        $estado = $this->grabaRegistro($this->tabla, $data);

        return $estado;
    }

    function actualizarStock() {
        $sql = "select * from wc_inventario where estado=1 order by idinventario desc";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
    
    function reporte_cuadregeneral($Inventario, $Bloques, $Almacen, $Linea, $Sublinea, $Idproducto, $estado) {
        $filtroProducto = "";
        if (!empty($Almacen)) $filtroProducto .= " and wdi.idalmacen='$Almacen'";
        else if (!empty($Sublinea)) $filtroProducto .= " and wdi.idlinea='$Sublinea'";
        else if (!empty($Idproducto)) $filtroProducto .= " and wdi.idproducto='$Idproducto'";
        
        $data = $this->leeRegistro("wc_detalleinventario wdi "
                                    . "inner join wc_producto wp on wp.idproducto = wdi.idproducto" . $filtroProducto
                                    . " inner join wc_bloques wb on wb.idbloque = wdi.idbloque" . (!empty($Bloques) ? " and wb.idbloque='$Bloques'" : "")
                                    . (!empty($Linea) ? 
                                    " inner join wc_linea wsubli on wsubli.idlinea = wp.idlinea" .
                                    " inner join wc_linea wlin on wlin.idlinea = wsubli.idpadre": ""), 
                                   "wb.codigo as codigobloque, wp.idproducto, wp.codigopa, wp.nompro, wp.fob, wp.cifventasdolares, wdi.buenos, wdi.buenos2, wdi.buenos3, wdi.malos, wdi.servicio, wdi.showroom, wdi.stockanterior, wdi.fechacreacion", 
                                   "wdi.estado = '$estado' and wdi.idinventario='$Inventario'" . (!empty($Bloques) ? " and wdi.idbloque='$Bloques'" : ""), 
                                   "wb.idbloque asc", "");  
        return $data;
    }
    
    function listaKardexValorizado($diproducto, $fechainicio, $fechafinal) {
        $data = $this->EjecutaConsulta("select  dedc.iddetalleestructuradecostos as 'idregistro',
                                                dedc.idproducto as idproducto,
                                                '02' as 'tipomovimiento',
                                                dedc.cantidadrecibidaoc as cantidad,
                                                edc.serieDua as 'serie', edc.nroDua as 'numdoc', 0 as 'electronico', 6 as 'operacion',
                                                '0' as 'nombredoc',
                                                dedc.totalunitario as 'costounitario',
                                                2 as 'idmoneda',
                                                edc.fechadua as fecha,
                                                -1 as 'idordenventa'
                                                from wc_detalleestructuradecostos dedc
                                                inner join wc_estructuradecostos edc on edc.idestructuradecostos = dedc.idestructuradecostos and
                                                                                       edc.estado=1
                                                where dedc.idproducto='$diproducto' and edc.fechadua>='$fechainicio' and edc.fechadua<='$fechafinal' and dedc.estado=1
                                        union all
                                        select  dov.iddetalleordenventa as 'idregistro',
                                                dov.idproducto as idproducto,
                                                '01' as 'tipomovimiento',
                                                dov.cantdespacho as cantidad,
                                                doc.serie as 'serie', doc.numdoc as 'numdoc', doc.electronico as 'electronico', (doc.nombredoc*(doc.nombredoc-1) + 1) as 'operacion',
                                                doc.nombredoc as 'nombredoc',
                                                dov.preciofinal as 'costounitario',
                                                ov.IdMoneda as 'idmoneda',
                                                doc.fechadoc as fecha,
                                                ov.idordenventa as 'idordenventa'
                                                from wc_detalleordenventa dov
                                                inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa
                                                inner join wc_documento doc on doc.idordenventa = dov.idordenventa and doc.estado = 1 and
                                                                               (doc.nombredoc = 1 or doc.nombredoc = 2) and doc.esAnulado=0 and (doc.esImpreso=1 or doc.esCargado=1)
                                                where dov.idproducto='$diproducto' and doc.fechadoc>='$fechainicio' and doc.fechadoc<='$fechafinal' and dov.estado=1
                                                    group by doc.idordenventa
                                        union all
                                        select  dd.iddetalledevolucion as 'idregistro',
                                                dd.idproducto as idproducto,
                                                '04' as 'tipomovimiento', 
                                                dd.cantidad as cantidad,
                                                doc.serie as 'serie', doc.numdoc as 'numdoc', doc.electronico as 'electronico', 5 as 'operacion',
                                                doc.nombredoc as 'nombredoc',
                                                dd.precio as 'costounitario',
                                                ov.IdMoneda as 'idmoneda',
                                                doc.fechadoc as fecha,
                                                ov.idordenventa as 'idordenventa'
                                                from wc_detalledevolucion dd
                                                inner join wc_devolucion d on d.iddevolucion = dd.iddevolucion and d.estado=1 and d.aprobado=1
                                                inner join wc_ordenventa ov on ov.idordenventa = d.idordenventa
                                                inner join wc_documento doc on doc.idordenventa = d.idordenventa and 
                                                                               doc.estado=1 and doc.iddevolucion = d.iddevolucion and 
                                                                               doc.nombredoc = 5 and doc.esAnulado=0 and (doc.esImpreso=1 or doc.esCargado=1)
                                                where dd.idproducto='$diproducto' and doc.fechadoc>='$fechainicio' and doc.fechadoc<='$fechafinal' and dd.estado=1 and dd.cantidad>0
                                        order by fecha asc");
        return $data;
    }
    
    function listaDetalleOrdenVenta($idOrdenVenta){
        $sql = "select idordenventa,idproducto from wc_detalleordenventa where estado=1 and idordenventa='".$idOrdenVenta."'  order by iddetalleordenventa asc";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
    function listaComprobantesElectronicas($idOrdenVenta){
        $sql = "select iddocumento,serie,numdoc,desde,hasta from wc_documento where  electronico=1 and nombredoc in(1,2,5) and esAnulado=0  and (esCargado=1 or esImpreso=1) 
        and idordenventa='".$idOrdenVenta."' order by iddocumento asc;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
    function stockActualProductoBloques($idinventario,$idproducto){
        $sql = "select
wc_producto.idproducto
,wc_producto.codigopa
,wc_producto.stockactual
,wc_producto.stockdisponible
,wc_detalleinventario.idbloque
,wc_bloques.codigo as 'bloque'
,wc_detalleinventario.iddetalleinventario
,wc_detalleinventario.idinventario
from  wc_producto
left join wc_detalleinventario on wc_producto.idproducto=wc_detalleinventario.idproducto and wc_detalleinventario.idinventario='".$idinventario."' and wc_detalleinventario.estado=1
left join wc_bloques on wc_detalleinventario.idbloque=wc_bloques.idbloque and wc_bloques.estado=1
where wc_producto.estado=1
and wc_producto.idproducto='".$idproducto."';";
        $data = $this->scriptArrayCompleto($sql);
        return $data;
    }


}

?>