<?php

class Movimiento extends Applicationbase {

    private $tabla = 'wc_movimiento';
    private $tabla1 = 'wc_repuesto';
    private $_tabla_detallemovimiento = 'wc_detallemovimiento';
    private $_tabla_detallerepuesto = 'wc_detallerepuesto';

    function listadoMovimientos() {
        $movimiento = $this->leeRegistro($this->tabla, "", "", "fechamovimiento desc", "");
        return $movimiento;
    }

    function listadoTotal() {
        $datos = $this->leeRegistro($this->tabla, "", "estado=1", "");
        return $datos;
    }

    function actualizaMovimiento($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function buscaMovimiento($idMovimiento) {
        $movimiento = $this->leeRegistro($this->tabla, "", "id=" . $idMovimiento . " AND estado='1'", "");
        return $movimiento;
    }

    function buscaMovimientoxfiltro($filtro) {
        $movimiento = $this->leeRegistro($this->tabla, "", " estado='1' and " . $filtro, "");
        return $movimiento;
    }

    function cambiaEstadoMovimiento($idMovimiento) {
        $estado = $this->cambiaEstado($this->tabla, "id=" . $idMovimiento);
        return $estado;
    }

    function registraMovimiento($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function grabaMovimiento($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function grabaMovimientoRep($data) {
        $exito = $this->grabaRegistro($this->tabla1, $data);
        return $exito;
    }

    function contarMovimiento() {
        $cantidad = $this->contarRegistro($this->tabla, "");
        return $cantidad;
    }

    function generaCodigo() {
        $data = $this->leeRegistro($this->tabla, "MAX(idmovimiento)+1 as id", "", "");
        $valor = "00000" . $data[0]['id'];
        $codigo = substr($valor, strlen($valor) - 6, 6);
        return $codigo;
    }

    function listaMovPaginado($pagina, $parametro = "") {
        $condicion = "estado=1";
        if (!empty($parametro)) {
            $condicion = "estado=1 and fechamovimiento='$parametro' or ndocumento='$parametro'";
        }
        $data = $this->leeRegistroPaginado(
                $this->tabla, "", $condicion, "fechamovimiento desc,idmovimiento desc", $pagina);
        return $data;
    }

    function listaMovPaginadoRep($pagina, $parametro = "") {
        $condicion = $this->tabla1.".estado=1";
        if (!empty($parametro)) {
            $condicion = $this->tabla1.".estado=1 and fechamovimiento='$parametro' or codigooc='$parametro'";
        }
        $data = $this->leeRegistroPaginado(
                $this->tabla1." INNER JOIN `wc_ordencompra` 
                ON (`wc_repuesto`.`idordencompra` = `wc_ordencompra`.`idordencompra`)", $this->tabla1.".*, `wc_ordencompra`.`codigooc`", $condicion, "fechamovimiento desc,idrepuesto desc", $pagina);
        return $data;
    }

    function paginadoMov($parametro = "") {
        $condicion = "estado=1";
        if (!empty($parametro)) {
            $condicion = "estado=1 and fechamovimiento='$parametro' or ndocumento='$parametro'";
        }
        return $this->paginado($this->tabla, $condicion);
    }

    function paginadoMovRep($parametro = "") {
        $condicion = $this->tabla1.".estado=1";
        if (!empty($parametro)) {
            $condicion = $this->tabla1.".estado=1 and fechamovimiento='$parametro' or codigooc='$parametro'";
        }
        return $this->paginado($this->tabla1." INNER JOIN `wc_ordencompra` 
        ON (`wc_repuesto`.`idordencompra` = `wc_ordencompra`.`idordencompra`)", $condicion);
    }

    function listadoxParametro($parametro = "") {
        $condicion = "estado=1";
        if (!empty($parametro)) {
            $condicion = "estado=1 and fechamovimiento='$parametro' or ndocumento='$parametro'";
        }
        $datos = $this->leeRegistro($this->tabla, "", $condicion, "");
        return $datos;
    }

    function listadoxParametroRep($parametro = "") {
        $condicion = $this->tabla1.".estado=1";
        if (!empty($parametro)) {
            $condicion = $this->tabla1.".estado=1 and fechamovimiento='$parametro' or codigooc='$parametro'";
        }
        $datos = $this->leeRegistro($this->tabla1." INNER JOIN `wc_ordencompra` 
        ON (`wc_repuesto`.`idordencompra` = `wc_ordencompra`.`idordencompra`)", "", $condicion, "");
        return $datos;
    }

    function kardexValorizadoxProducto($idProducto, $anoInicial, $anoFinal, $mesInicial, $mesFinal, $sunat) {
        $filtro = "and m.estado=1 and dm.estado=1";
        if ($sunat == 1) {
            $filtro = "and m.essunat='$sunat' ";
        }
        $diafin = $this->obtenerFinMes($mesFinal, $anoFinal);
        $sql = "	SELECT
                        m.fechamovimiento,m.tipomovimiento,t.codigotipooperacion,dt.codigotipodocumento,dm.cantidad, m.serie, m.ndocumento,
                        CASE m.tipomovimiento WHEN 1 THEN dm.cantidad ELSE '' END AS EntradaCantidad,
                        CASE m.tipomovimiento WHEN 1 THEN dm.preciovalorizado ELSE '' END AS EntradaPrecio,
                        CASE m.tipomovimiento WHEN 1 THEN dm.preciovalorizado*dm.cantidad ELSE '' END AS EntradaCosto,
                        CASE m.tipomovimiento WHEN 2 THEN dm.cantidad ELSE '' END AS SalidaCantidad,
                        CASE m.tipomovimiento WHEN 2 THEN dm.preciovalorizado ELSE '' END AS SalidaPrecio,
                        CASE m.tipomovimiento WHEN 2 THEN dm.preciovalorizado*dm.cantidad ELSE '' END AS SalidaCosto,
                        dm.stockactual as SaldoCantidad, dm.preciovalorizado as SaldoPrecio, (dm.stockactual*dm.preciovalorizado) AS SaldoCosto
                        FROM  `wc_detallemovimiento` dm
                        INNER JOIN wc_movimiento m ON m.idmovimiento = dm.idmovimiento
                        left JOIN wc_tipooperacion t ON m.idtipooperacion=t.idtipooperacion
                        LEFT JOIN wc_documentotipo dt ON dt.iddocumentotipo=m.iddocumentotipo
                        WHERE dm.idproducto = $idProducto " . $filtro . " and
                        m.fechamovimiento between '".$anoInicial."-".$mesInicial."-01' and '".$anoFinal."-".$mesFinal."-".$diafin."'
                        ORDER BY m.fechamovimiento,dm.iddetallemovimiento";
                        //YEAR(m.fechamovimiento)>='$ano' MONTH(m.fechamovimiento)>='$mesInicial' and MONTH(m.fechamovimiento)<='$mesFinal' and YEAR(m.fechamovimiento)<='$ano' " . $filtro . "
        return $this->EjecutaConsulta($sql);
    }

    function resumenKardexXProducto($idProducto, $anoInicial, $anoFinal, $mesInicial, $mesFinal) {
        $filtro = "and m.estado=1 and dm.estado=1";

        $diafin = $this->obtenerFinMes($mesFinal, $anoFinal);
        $sql = "	SELECT
                        m.fechamovimiento,m.tipomovimiento,t.codigotipooperacion,mt.codigo,mt.nombre, dt.codigotipodocumento,m.ndocumento,m.serie,dm.cantidad,dm.pu,
                        CASE m.tipomovimiento WHEN 1 THEN dm.cantidad ELSE '' END AS EntradaCantidad,
                        CASE m.tipomovimiento WHEN 1 THEN dm.preciovalorizado ELSE '' END AS EntradaPrecio,
                        CASE m.tipomovimiento WHEN 1 THEN dm.preciovalorizado*dm.cantidad ELSE '' END AS EntradaCosto,
                        CASE m.tipomovimiento WHEN 2 THEN dm.cantidad ELSE '' END AS SalidaCantidad,
                        CASE m.tipomovimiento WHEN 2 THEN dm.preciovalorizado ELSE '' END AS SalidaPrecio,
                        CASE m.tipomovimiento WHEN 2 THEN dm.preciovalorizado*dm.cantidad ELSE '' END AS SalidaCosto,
                        dm.stockactual as SaldoCantidad, dm.preciovalorizado as SaldoPrecio, round(dm.stockactual*dm.preciovalorizado,2) AS SaldoCosto
                        FROM  `wc_detallemovimiento` dm
                        INNER JOIN wc_movimiento m ON m.idmovimiento = dm.idmovimiento
                        left JOIN wc_tipooperacion t ON m.idtipooperacion=t.idtipooperacion
                        INNER JOIN wc_movimientotipo mt ON m.tipomovimiento = mt.idmovimientotipo
                        INNER JOIN wc_producto p ON dm.idproducto = p.idproducto
                        LEFT JOIN wc_documentotipo dt ON dt.iddocumentotipo=m.iddocumentotipo
                        WHERE dm.estado=1 and dm.idproducto =$idProducto and
                        m.fechamovimiento between '".$anoInicial."-".$mesInicial."-01' and '".$anoFinal."-".$mesFinal."-".$diafin."' " . $filtro . "
                        ORDER BY m.fechamovimiento,dm.iddetallemovimiento";
                        //YEAR(m.fechamovimiento)>='$ano' MONTH(m.fechamovimiento)>='$mesInicial' and MONTH(m.fechamovimiento)<='$mesFinal' and YEAR(m.fechamovimiento)<='$ano' " . $filtro . "
        return $this->EjecutaConsulta($sql);
    }

    function kardexTotalxProducto($ano, $mesInicio, $mesFinal) {
        $filtro = "and m.estado=1 and dm.estado=1 and p.estado=1";

        $sql = "	SELECT
			p.codigopa,p.nompro,p.idproducto,m.fechamovimiento,m.tipomovimiento,t.codigotipooperacion,mt.codigo,mt.nombre, dt.codigotipodocumento,m.ndocumento,m.serie,dm.cantidad,dm.pu,
			CASE m.tipomovimiento WHEN 1 THEN dm.cantidad ELSE '' END AS EntradaCantidad,
			CASE m.tipomovimiento WHEN 1 THEN dm.preciovalorizado ELSE '' END AS EntradaPrecio,
			CASE m.tipomovimiento WHEN 1 THEN dm.preciovalorizado*dm.cantidad ELSE '' END AS EntradaCosto,
			CASE m.tipomovimiento WHEN 2 THEN dm.cantidad ELSE '' END AS SalidaCantidad,
			CASE m.tipomovimiento WHEN 2 THEN dm.preciovalorizado ELSE '' END AS SalidaPrecio,
			CASE m.tipomovimiento WHEN 2 THEN dm.preciovalorizado*dm.cantidad ELSE '' END AS SalidaCosto,
			dm.stockactual as SaldoCantidad, dm.preciovalorizado as SaldoPrecio, round(dm.stockactual*dm.preciovalorizado,2) AS SaldoCosto
			FROM  `wc_detallemovimiento` dm
			INNER JOIN wc_movimiento m ON m.idmovimiento = dm.idmovimiento
			left JOIN wc_tipooperacion t ON m.idtipooperacion=t.idtipooperacion
			INNER JOIN wc_movimientotipo mt ON m.tipomovimiento = mt.idmovimientotipo
			INNER JOIN wc_producto p ON dm.idproducto = p.idproducto

			LEFT JOIN wc_documentotipo dt ON dt.iddocumentotipo=m.iddocumentotipo
			WHERE dm.estado=1  and MONTH(m.fechamovimiento)>='$mesInicio' and MONTH(m.fechamovimiento)<='$mesFinal' and YEAR(m.fechamovimiento)='$ano' " . $filtro . "
			ORDER BY dm.idproducto,m.fechamovimiento";
        return $this->EjecutaConsulta($sql);
    }

    function InactivaMovimientoxIdOrdenVenta($idOrdenVenta) {
        $sql = "Select idmovimiento From " . $this->tabla . " Where idordenventa=" . $idOrdenVenta;
        $dataMovimiento = $this->EjecutaConsulta($sql);
        $idmovimiento = $dataMovimiento[0]['idmovimiento'];

        $sql = "Update  " . $this->_tabla_detallemovimiento . " Set estado=0 Where idmovimiento=" . $idmovimiento;
        $exito_detmov = $this->EjecutaConsulta($sql);

        $sql = "Update  " . $this->tabla . " Set estado=0 Where idmovimiento=" . $idmovimiento;
        $exito_mov = $this->EjecutaConsulta($sql);
        return true;
    }

    function kardexNuevaConsultaContabilidad($idProducto, $anoInicial, $anoFinal, $mesInicial, $mesFinal, $sunat) {
        $filtro = "and m.estado=1 and dm.estado=1";
        if ($sunat == 1) {
            $filtro = "and m.essunat='$sunat' ";
        }
        $diafin = $this->obtenerFinMes($mesFinal, $anoFinal);
        $sql = "    (select * from
                        (
                        select
                        m.fechamovimiento as fechainicio,
                        -- concat(oc.faproxllegada,' 00:00:00') as fechainicio,
                        case oc.idproveedor when 57 then 01 else 50 end as tipodocumento, -- cambiar el 01 pór lo que tiene que ser
                        m.serie as serie,
                        m.ndocumento as documento,
                        '02' as tipooperacion,
                        doc.cantidadrecibidaoc as cantidad,
                        round(tc.venta*doc.totalunitario,2) as costounitario,
                        1 as mov,
                        1 as compra,
                        oc.codigooc as ORDEN,
                        m.idmovimiento as num

                        from wc_ordencompra oc
                        inner join wc_detalleordencompra doc on doc.idordencompra = oc.idordencompra
                        inner join wc_movimiento m on m.idordencompra = oc.idordencompra
                        inner join wc_tipocambio tc on tc.idtipocambio = oc.idtipocambiovigente
                        where oc.estado = 1
                        and doc.estado = 1
                        and doc.idproducto = ".$idProducto."
                        and m.fechamovimiento <= '".$anoFinal."-".$mesFinal."-".$diafin."'
                        ) as t1
                        )
                        union all
                        (
                        select * from
                        -- FIN COMPRAS

                        -- INICIO DEVOLUCIONES
                        (
                        select
                        -- d.fechaaprobada as fechainicio,
                        m.fechamovimiento as fechainicio,
                        if (m.iddocumentotipo = 0 ,'',dt.codigotipodocumento) as tipodocumento,
                        -- case ov.esfacturado when 1 then '07' else '' end as tipodocumento,
                        '' as serie,
                        '' as documento,
                        '05' as tipooperacion,
                        dd.cantidad as cantidad,
                        '' as costounitario,
                        1 as mov,
                        0 as compra,
                        ov.codigov as ORDEN,
                        m.idmovimiento as num

                        from wc_devolucion d
                        inner join wc_detalledevolucion dd on dd.iddevolucion = d.iddevolucion
                        inner join wc_ordenventa ov on ov.idordenventa = d.idordenventa
                        inner join wc_movimiento m on m.iddevolucion = d.iddevolucion
                        left join wc_documentotipo dt on dt.iddocumentotipo = m.iddocumentotipo
                        where d.estado = 1
                        and dd.estado = 1
                        and ov.estado = 1
                        and dd.idproducto = ".$idProducto."
                        and d.aprobado=1
                        and dd.cantidad <> 0
                        and m.fechamovimiento <= '".$anoFinal."-".$mesFinal."-".$diafin."'
                        ) as t2
                        )
                         -- order by fechainicio;
                        union all
                        -- FIN DEVOLUCIONES
                        -- SALIDAS
                        (select * from
                        (
                        select
                        -- concat(m.fechamovimiento,' 00:00:00') as fechainicio,
                        m.fechamovimiento as fechainicio,
                        if (m.iddocumentotipo = 0 ,'',dt.codigotipodocumento) as tipodocumento,
                        -- case ov.esfacturado when 1 then '01' else 'No se sabe' end as tipodocumento,
                        m.serie as serie,
                        m.ndocumento as documento,
                        '01' as tipooperacion,
                        dov.cantdespacho as cantidad,
                        '' as costounitario,
                        2 as mov,
                        0 as compra,
                        ov.codigov as ORDEN,
                        m.idmovimiento as num

                        from wc_ordenventa ov
                        inner join wc_detalleordenventa dov on dov.idordenventa = ov.idordenventa
                        inner join wc_movimiento m on m.idordenventa = ov.idordenventa
                        left join wc_documentotipo dt on dt.iddocumentotipo = m.iddocumentotipo
                        where ov.estado = 1
                        and dov.estado = 1
                        and dov.idproducto = ".$idProducto."
                        and m.estado = 1
                        and m.iddevolucion = 0
                        and ov.fechadespacho <> '0000-00-00'
                        and m.fechamovimiento <= '".$anoFinal."-".$mesFinal."-".$diafin."'
                        ) as t3
                        )
                        union all
                        (select * from

                                (select
                                m.fechamovimiento as fechainicio,
                                if (m.iddocumentotipo = 0 ,'',dt.codigotipodocumento) as tipodocumento,
                                m.serie as serie,
                                m.ndocumento as documento,
                                tpo.codigotipooperacion as tipooperacion,
                                dm.cantidad as cantidad,
                                '' as costounitario,
                                mt.idmovimientotipo as mov,
                                -- 1 as mov,
                                0 as compra,
                                '' as ORDEN,
                                m.idmovimiento as num

                                from wc_movimiento m
                                inner join wc_detallemovimiento dm on dm.idmovimiento=m.idmovimiento
                                inner join wc_movimientotipo mt on mt.idmovimientotipo=m.tipomovimiento
                                left join wc_tipooperacion tpo on tpo.idtipooperacion=m.idtipooperacion
                                left join wc_documentotipo dt on dt.iddocumentotipo = m.iddocumentotipo
                                where m.estado =1
                                and dm.estado = 1
                                and dm.idproducto = ".$idProducto."
                                and mt.idmovimientotipo = 1
                                and (m.idtipooperacion = 0 or m.idtipooperacion = 3 or m.idtipooperacion = 6 or m.idtipooperacion = 10 or m.idtipooperacion = 12 or m.idtipooperacion = 14)
                                and m.fechamovimiento <= '".$anoFinal."-".$mesFinal."-".$diafin."') as t4
                                )
                                union all
                                -- Movimiento de Stock Salida
                                (select * from
                                (select
                                m.fechamovimiento as fechainicio,
                                if (m.iddocumentotipo = 0 ,'',dt.codigotipodocumento) as tipodocumento,
                                m.serie as serie,
                                m.ndocumento as documento,
                                tpo.codigotipooperacion as tipooperacion,
                                dm.cantidad as cantidad,
                                '' as costounitario,
                                mt.idmovimientotipo as mov,
                                -- 2 as mov,
                                0 as compra,
                                '' as ORDEN,
                                m.idmovimiento as num

                                from wc_movimiento m
                                inner join wc_detallemovimiento dm on dm.idmovimiento=m.idmovimiento
                inner join wc_movimientotipo mt on mt.idmovimientotipo=m.tipomovimiento
                                left join wc_tipooperacion tpo on tpo.idtipooperacion=m.idtipooperacion
                                left join wc_documentotipo dt on dt.iddocumentotipo = m.iddocumentotipo
                                where m.estado =1
                                and dm.estado = 1
                                and dm.idproducto = ".$idProducto."
                                and mt.idmovimientotipo = 2
                                and (m.idtipooperacion = 0 or m.idtipooperacion = 4 or m.idtipooperacion = 8 or m.idtipooperacion = 9 or m.idtipooperacion = 11 or m.idtipooperacion = 13)
                                and m.fechamovimiento <= '".$anoFinal."-".$mesFinal."-".$diafin."') as t5
                                )order by num;"
                        ;
                        //YEAR(m.fechamovimiento)>='$ano' MONTH(m.fechamovimiento)>='$mesInicial' and MONTH(m.fechamovimiento)<='$mesFinal' and YEAR(m.fechamovimiento)<='$ano' " . $filtro . "
        return $this->EjecutaConsulta($sql);
    }

    public function buscaNumeroFactura($numero){
        $cantidad = $this->contarRegistro($this->tabla,"ndocumento=" . $numero . " and idtipooperacion=1 AND estado='1'");
        return $cantidad;
    }

    public function buscaNumeroFacturaj($numero){
        $cantidad = $this->contarRegistro("wc_movimientoj","documento=" . $numero . " and document=1 AND estado='1'");
        return $cantidad;
    }

    public function buscaNumeroDevolucion($numero){
        $cantidad = $this->contarRegistro($this->tabla,"ndocumento=" . $numero . " and idtipooperacion=5 AND estado='1'");
        return $cantidad;
    }

    public function buscaNumeroDevolucionj($numero){
        $cantidad = $this->contarRegistro("wc_movimientoj","documento=" . $numero . " and document=2 AND estado='1'");
        return $cantidad;
    }

    public function buscaNumeroBoleta($numero){
        $cantidad = $this->contarRegistro($this->tabla,"ndocumento=" . $numero . " and idtipooperacion=4 AND estado='1'");
        return $cantidad;
    }

    public function buscaNumeroBoletaj($numero){
        $cantidad = $this->contarRegistro("wc_movimientoj","documento=" . $numero . " and document=3 AND estado='1'");
        return $cantidad;
    }

    public function grabaFacturaj($data){
        $exito = $this->grabaRegistro("wc_movimientoj", $data);
        return $exito;
    }

    public function buscarSaldoInicialj($idProducto){
        $cantidad = $this->contarRegistro("wc_movimientoj","idproducto=".$idProducto." and tipooperacion = 16");
        return $cantidad;
    }

    public function grabaSaldosIniciales($data){
        $exito = $this->grabaRegistro("wc_movimientoj", $data);
        return $exito;
    }


    function registrarInventarioPorBloque_graba($tabla,$data)
    {

    $exito = $this->grabaRegistro($tabla, $data);
        return $exito;
    }


}
?>