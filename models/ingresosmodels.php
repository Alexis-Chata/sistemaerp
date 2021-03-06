<?php

class Ingresos extends Applicationbase {

    private $tabla = "wc_ingresos";
    private $tablas = 'wc_actor,wc_ingresos,wc_orden';

    function listado() {
        $cuenta = $this->leeRegistro($this->tabla, "", "estado=1 and esvalidado=1", "");
        return $cuenta;
    }

    function graba($data) {
        $id = $this->grabaRegistro($this->tabla, $data);
        return $id;
    }

    function actualiza($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function actualizaxid($data, $idingresos) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idingresos=$idingresos");
        return $exito;
    }

    function buscar($id) {
        $data = $this->leeRegistro($this->tabla, "", "idingresos=$id", "");
        return $data;
    }

    function eliminar($id) {
        $exito = $this->inactivaRegistro($this->tabla, "idingresos='$id'");
        return $exito;
    }

    function listarxvendedor($idvendedor) {
        $data = $this->leeRegistro($this->tabla, "", " esvalidado=1 and estado=1 situacionpago=0 and idvendedor=" . $idvendedor, "");
        return $data;
    }
    
    function buscaxidyOV($idingresos, $idordenventa) {
        $data = $this->leeRegistro($this->tabla, "", "estado=1 and idingresos='$idingresos' and idordenventa='$idordenventa'", "");
        return $data;
    }

    function buscaxid($idingresos) {
        $data = $this->leeRegistro($this->tabla, "", "estado=1 and idingresos='$idingresos'", "");
        return $data;
    }
    
    function listarIngresosConCobrador_consaldo($idOrdenVenta, $filtro = "") {
        $data = $this->leeRegistro("`wc_actor` wc_actor inner join `wc_ingresos` wc_ingresos on wc_actor.`idactor`=wc_ingresos.`idcobrador`", "", "wc_ingresos.`idOrdenVenta`='$idOrdenVenta' and wc_ingresos.`estado`=1 and wc_ingresos.`saldo` > 0 and esvalidado=1" . $filtro, "");
        return $data;
    }

    function listarxzona($idzona) {
        $data = $this->leeRegistro($this->tabla, "", " estado=1 and idzona=" . $idzona, "");
        return $data;
    }

    function contarIngresos() {
        $cantidadMovimiento = $this->contarRegistro($this->tabla, "estado=1");
        return $cantidadMovimiento;
    }

    function listarxcliente($idcliente) {
        $data = $this->leeRegistro($this->tabla, "", "idcliente=" . $idcliente, "");
        return $data;
    }

    function listarxordenpago($id) {
        $data = $this->leeRegistro($this->tabla, "", "idordenpago=" . $id, "");
        return $data;
    }

    //Ingresos del día
    function listarxhoy() {
        $data = $this->leeRegistro($this->tabla, "", "date(`fechacreacion`)=curdate() and estado=1 and esvalidado=1", "");
        return $data;
    }

    function listarIngresosDifLetras() {
        $data = $this->leeRegistro(
                "`wc_ingresos` wc_ingresos INNER JOIN `wc_detalleordencobroingreso` wc_detalleordencobroingreso ON wc_ingresos.`idingresos` = wc_detalleordencobroingreso.`idingreso`
     			INNER JOIN `wc_detalleordencobro` wc_detalleordencobro ON wc_detalleordencobroingreso.`iddetalleordencobro` = wc_detalleordencobro.`iddetalleordencobro`", "wc_ingresos.`idingresos`,
			     wc_ingresos.`idOrdenVenta`,
			     wc_ingresos.`idcliente`,
			     wc_ingresos.`idtipocambio`,
			     wc_ingresos.`idcobrador`,
			     wc_ingresos.`nrorecibo`,
			     wc_ingresos.`montoingresado`,
			     wc_ingresos.`tipocobro`,
			     wc_ingresos.`nrodoc`,
			     wc_ingresos.`monedai`,
			     wc_ingresos.`fcobro`,
			     wc_detalleordencobroingreso.`iddetalleordencobroingreso`,

			     wc_detalleordencobroingreso.`montop`,
			     wc_detalleordencobroingreso.`monedap`,
			     wc_detalleordencobro.`iddetalleordencobro`,
			     wc_detalleordencobro.`idordencobro`,
			     wc_detalleordencobro.`importedoc`,
			     wc_detalleordencobro.`formacobro`,
			     wc_detalleordencobro.`numeroletra`,
			     wc_detalleordencobro.`fvencimiento`,
			     wc_detalleordencobro.`protesto`,
			     wc_detalleordencobro.`situacion`,
			     wc_ingresos.`estadocomicobra`", "wc_ingresos.`saldo`>0 and wc_ingresos.`tipocobro`!=3 and wc_detalleordencobro.`situacion`='' and wc_ingresos.`estado`=1", "");
        return $data;
    }

    //Registro de ingresos generales del día.
    function resumeningresoshoy() {
        $sql = "Select
			concat(wa.apellidopaterno,' ',wa.apellidomaterno,' ,',wa.nombres) as cobrador,
			wc.razonsocial as cliente,ing.nrorecibo,ing.idingresos,ing.montoingresado,ing.tipocobro,ing.nrodoc,ing.idOrdenVenta,ov.codigov,ing.usuariocreacion
			From wc_ingresos ing
			Inner Join wc_cliente wc On ing.idcliente=wc.idcliente
			Inner Join wc_actor wa On ing.idcobrador=wa.idactor
			Inner Join wc_ordenventa ov ON ing.idOrdenVenta=ov.idordenventa
			Where date(ing.`fechacreacion`)=curdate() and ing.estado=1 and esvalidado=1";
        return $this->EjecutaConsulta($sql);
    }

    function equivalencias($mes, $anio) {
        $data = $this->leeRegistro("wc_equivalente", "*", "mes='$mes' and anio='$anio'", "");
        return $data;
    }

    function listarIngresos($idOrdenVenta) {
        $data = $this->leeRegistro($this->tabla, "", "idOrdenVenta='$idOrdenVenta' and estado=1 and esvalidado=1 ", "");
        return $data;
    }

    function getMontoAsignado($letra, $idOrdenVenta) {
        $data = $this->leeRegistro($this->tabla, "montoasignado", "idingresos='$letra' and idOrdenVenta='$idOrdenVenta' and estado=1 and montoasignado=0", "");
        if (count($data) > 0)
            return $data[0]['montoasignado'];
        return -1;
    }

    function IngresosxIdordenVenta($idOrdenVenta) {
        $data = $this->leeRegistro($this->tabla, "", "idOrdenVenta='$idOrdenVenta' and estado=1", "");
        return $data;
    }

    function listarIngresosConCobrador($idOrdenVenta, $filtro = "") {
        $data = $this->leeRegistro("`wc_actor` wc_actor inner join `wc_ingresos` wc_ingresos on wc_actor.`idactor`=wc_ingresos.`idcobrador`", "", "wc_ingresos.`idOrdenVenta`='$idOrdenVenta' and wc_ingresos.`estado`=1 and esvalidado=1" . $filtro, "");
        return $data;
    }

    function listarIngresosConSaldo($idOrdenVenta) {
        $data = $this->leeRegistro($this->tabla, "", "idOrdenVenta='$idOrdenVenta' and saldo>0 and estado=1 and esvalidado=1", "");
        return $data;
    }

    function sumaIngresos($idOrdenVenta) {
        $data = $this->leeRegistro($this->tabla, "sum(saldo),sum(montoingresado),sum(montoasignado)", "idOrdenVenta='$idOrdenVenta' and estado=1 and esvalidado=1", "tipocobro desc");
        return $data;
    }

    function sumaIngresosAsignadosPorFecha($FechaInicio, $Principal, $Categoria, $lstZona) {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and " .
                "wc_ordenventa.`esguiado`=1 and " .
                "wc_ordenventa.`estado`=1";
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($FechaInicio))
            $condicion .= " and wc_ingresos.fcobro<='" . $FechaInicio . "'";
        $condicion .= (!empty($Principal) ? ' and wc_categoria.idpadrec=' . $Principal : ' and wc_categoria.idpadrec in (1, 2)');
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                INNER JOIN `wc_ingresos` wc_ingresos on wc_ingresos.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ingresos.`estado`=1  and wc_ingresos.`esvalidado`=1 ", "  wc_ordenventa.idordenventa,
                                   wc_ordenventa.idmoneda,
                                   wc_categoria.idpadrec,
                                   sum(wc_ingresos.montoasignado) as totalasignado", $condicion, "", "group by wc_ordenventa.idordenventa order by wc_ordenventa.idordenventa asc");
        $data[0]['devuelveSQL'] = $this->devuelveSQL("`wc_ordenventa` wc_ordenventa
                                INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                INNER JOIN `wc_ingresos` wc_ingresos on wc_ingresos.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ingresos.`estado`=1  and wc_ingresos.`esvalidado`=1 ", "  wc_ordenventa.idordenventa,
                                   wc_ordenventa.idmoneda,
                                   wc_categoria.idpadrec,
                                   sum(wc_ingresos.montoasignado) as totalasignado", $condicion, "", "group by wc_ordenventa.idordenventa order by wc_ordenventa.idordenventa asc");
        return $data;
    }

    function verificarrecibo($nrorecibo) {
        $nrorecibo = htmlentities($nrorecibo, ENT_QUOTES, 'UTF-8');
        $data = $this->leeRegistro($this->tabla, "", "nrorecibo='$nrorecibo' and estado=1", "");
        return count($data);
    }

    function validaRegistroIngresos($tipocobro, $nrorecibo, $nrdoc, $lstbanco, $lstbancocheque, $nrooperacion, $condBusqueda) {
        if ($condBusqueda == 1) {
            if ($tipocobro == 12) {
                $data = $this->leeRegistro("wc_ingresos ing, wc_ordenventa ov", "'0' as 'nrodoc','0' as 'banco','0' as 'bancocheque',ov.codigov,ing.nrorecibo,'0' as 'nrooperacion'", "ing.idordenventa=ov.idordenventa and ing.estado=1 and ing.nrorecibo='" . $nrorecibo . "' and ing.tipocobro in (1, '" . $tipocobro . "')", "");
            } else {
                $data = $this->leeRegistro("wc_ingresos ing, wc_ordenventa ov", "'0' as 'nrodoc','0' as 'banco','0' as 'bancocheque',ov.codigov,ing.nrorecibo,'0' as 'nrooperacion'", "ing.idordenventa=ov.idordenventa and ing.estado=1 and ing.nrorecibo='" . $nrorecibo . "' and ing.tipocobro='" . $tipocobro . "'", "");
            }
        }
        
        if ($condBusqueda == 2) {
            $data = $this->leeRegistro("wc_ingresos ing, wc_ordenventa ov,wc_banco ba", "'0' as 'nrodoc',ba.codigo as 'banco','0' as 'bancocheque',ov.codigov,'0' as 'nrorecibo',ing.nrooperacion", "ing.idordenventa=ov.idordenventa and ing.idbanco=ba.idbanco and ing.estado=1 and ing.nrooperacion='" . $nrooperacion . "' and ing.idbanco='" . $lstbanco . "' and ing.tipocobro='" . $tipocobro . "'", "");
        }
        if ($condBusqueda == 3) {
            if ($tipocobro == 13) {
                $data = $this->leeRegistro("wc_ingresos ing, wc_ordenventa ov,wc_banco ba", "ing.nrodoc,'0' as 'banco',ba.codigo as 'bancocheque',ov.codigov,'0' as 'nrorecibo',ing.nrooperacion", "ing.idordenventa=ov.idordenventa and ing.idbancocheque=ba.idbanco and ing.estado=1 and ing.nrodoc='" . $nrdoc . "' and ing.idbancocheque='" . $lstbancocheque . "' and ing.tipocobro in (3, '" . $tipocobro . "')", "");
            } else {
                $data = $this->leeRegistro("wc_ingresos ing, wc_ordenventa ov,wc_banco ba", "ing.nrodoc,'0' as 'banco',ba.codigo as 'bancocheque',ov.codigov,'0' as 'nrorecibo',ing.nrooperacion", "ing.idordenventa=ov.idordenventa and ing.idbancocheque=ba.idbanco and ing.estado=1 and ing.nrodoc='" . $nrdoc . "' and ing.idbancocheque='" . $lstbancocheque . "' and ing.tipocobro='" . $tipocobro . "'", "");
            }            
        }

        return $data;
    }

    function listarIngresosNoValidados() {
        $data = $this->leeRegistro(
                "wc_ingresos i inner join wc_ordenventa ov on i.`idOrdenVenta`=ov.`idordenventa`
							inner join wc_clientezona cz on  ov.`idclientezona`=cz.`idclientezona`
							inner join wc_cliente  c  on c.`idcliente`=cz.`idcliente`
							inner join wc_moneda mn on ov.IdMoneda=mn.idmoneda
							 ", "", "i.`esvalidado`=0 and i.`estado`=1 ", "");
        return $data;
    }

    function liberaAsignacionxIdOrdenVenta($idOrdenVenta) {
        $sql = "Update " . $this->tabla . " Set saldo=montoingresado,montoasignado=0 where idOrdenVenta=" . $idOrdenVenta;
        return $this->EjecutaConsulta($sql);
    }

    function getMontoTotal($fecha, $moneda, $tipocobro) {
        $data = $this->leeRegistro($this->tabla . " i
				Inner Join wc_ordenventa ov ON ov.idordenventa=i.idordenVenta", "SUM(i.montoingresado) as montototal", "i.estado=1 and ov.estado=1 and i.fcobro='$fecha' and i.esvalidado=1 and ov.idMoneda='$moneda' and i.tipocobro IN ($tipocobro)", "", "");

        return $data[0]['montototal'];
    }
    
    function rankingGeneralXVendedor($fechaInicio, $fechaFinal, $idOrdenVenta, $idCliente, $idCobrador, $idTipoCobro, $cmbtipo, $nroRecibo, $simbolo, $monto, $moneda) {
        $condicion = "ingresos.montoasignado>0 and ingresos.esvalidado=1 and ingresos.estado=1";
        $condicion .= (!empty($fechaInicio) ? " and ingresos.fcobro>='$fechaInicio'" : "");
        $condicion .= (!empty($fechaFinal) ? " and ingresos.fcobro<='$fechaFinal'" : "");
        $condicion .= (!empty($idOrdenVenta) ? " and ingresos.idOrdenVenta='$idOrdenVenta'" : "");
        $condicion .= (!empty($idCliente) ? " and ingresos.idcliente='$idCliente'" : "");
        $condicion .= (!empty($idCobrador) ? " and ingresos.idcobrador='$idCobrador'" : "");    
        $condicion .= (!empty($idTipoCobro) ? " and ingresos.tipocobro='$idTipoCobro'" : ""); 
        $condicion .= (!empty($nroRecibo) ? " and ingresos.nrorecibo='$nroRecibo'" : "");
        $condicion .= (!empty($cmbtipo) ? " and ingresos.tipo='$cmbtipo'" : "");
        if (!empty($simbolo)&&!empty($monto)) {
            $condicion .= " and ingresos.montoasignado>='$monto'";
        }
        $data = $this->leeRegistro($this->tabla . " ingresos " .
                                    "inner join wc_ordenventa ordenventa on ordenventa.idordenventa = ingresos.idOrdenVenta and ordenventa.IdMoneda='$moneda' " .
                                    "inner join wc_actor cobrador on cobrador.idactor = ingresos.idcobrador", 
                                    "ingresos.idcobrador, concat(cobrador.nombres, ' ', cobrador.apellidopaterno, ' ', cobrador.apellidomaterno) as nombrecobrador, sum(ingresos.montoasignado) as totalcobrado", $condicion, "", "group by ingresos.idcobrador order by totalcobrado desc");
        return $data;
    }
    
    function rankingDetalladoXVendedor($fechaInicio, $fechaFinal, $idOrdenVenta, $idCliente, $idCobrador, $idTipoCobro, $cmbtipo, $nroRecibo, $simbolo, $monto, $moneda) {
        $condicion = "ingresos.montoasignado>0 and ingresos.esvalidado=1 and ingresos.estado=1";
        $condicion .= (!empty($fechaInicio) ? " and ingresos.fcobro>='$fechaInicio'" : "");
        $condicion .= (!empty($fechaFinal) ? " and ingresos.fcobro<='$fechaFinal'" : "");
        $condicion .= (!empty($idOrdenVenta) ? " and ingresos.idOrdenVenta='$idOrdenVenta'" : "");
        $condicion .= (!empty($idCliente) ? " and ingresos.idcliente='$idCliente'" : "");
        $condicion .= (!empty($idCobrador) ? " and ingresos.idcobrador='$idCobrador'" : "");    
        $condicion .= (!empty($idTipoCobro) ? " and ingresos.tipocobro='$idTipoCobro'" : ""); 
        $condicion .= (!empty($nroRecibo) ? " and ingresos.nrorecibo='$nroRecibo'" : "");
        $condicion .= (!empty($cmbtipo) ? " and ingresos.tipo='$cmbtipo'" : "");
        if (!empty($simbolo)&&!empty($monto)) {
            $condicion .= " and ingresos.montoasignado>='$monto'";
        }
        $data = $this->leeRegistro($this->tabla . " ingresos
                                inner join wc_ordenventa ordenventa on ordenventa.idordenventa = ingresos.idOrdenVenta and ordenventa.IdMoneda='$moneda'
                                inner join wc_actor cobrador on cobrador.idactor = ingresos.idcobrador", 
                                "ingresos.tipo, ingresos.idingresos, ingresos.tipocobro, ingresos.idbanco, ingresos.idbancocheque, sum(ingresos.montoasignado) as totalasignado", $condicion, "", "group by ingresos.tipocobro, ingresos.idbanco, ingresos.tipo");
        return $data;
    }
    
    function rankingDetalladoXVendedor_resumen($fechaInicio, $fechaFinal, $idOrdenVenta, $idCliente, $idCobrador, $idTipoCobro, $cmbtipo, $nroRecibo, $simbolo, $monto, $moneda) {
        $condicion = "ingresos.montoasignado>0 and ingresos.esvalidado=1 and ingresos.estado=1";
        $condicion .= (!empty($fechaInicio) ? " and ingresos.fcobro>='$fechaInicio'" : "");
        $condicion .= (!empty($fechaFinal) ? " and ingresos.fcobro<='$fechaFinal'" : "");
        $condicion .= (!empty($idOrdenVenta) ? " and ingresos.idOrdenVenta='$idOrdenVenta'" : "");
        $condicion .= (!empty($idCliente) ? " and ingresos.idcliente='$idCliente'" : "");
        $condicion .= (!empty($idCobrador) ? " and ingresos.idcobrador='$idCobrador'" : "");    
        $condicion .= (!empty($idTipoCobro) ? " and ingresos.tipocobro='$idTipoCobro'" : ""); 
        $condicion .= (!empty($nroRecibo) ? " and ingresos.nrorecibo='$nroRecibo'" : "");
        $condicion .= (!empty($cmbtipo) ? " and ingresos.tipo='$cmbtipo'" : "");
        if (!empty($simbolo)&&!empty($monto)) {
            $condicion .= " and ingresos.montoasignado>='$monto'";
        }
        $data = $this->leeRegistro($this->tabla . " ingresos
                                inner join wc_ordenventa ordenventa on ordenventa.idordenventa = ingresos.idOrdenVenta and ordenventa.IdMoneda='$moneda'
                                inner join wc_detalleordencobroingreso doci on doci.idingreso = ingresos.idingresos and doci.estado = 1 and doci.montop > 0.01
                                inner join wc_detalleordencobro doc on doc.iddetalleordencobro = doci.iddetalleordencobro and doc.estado = 1
                                inner join wc_actor cobrador on cobrador.idactor = ingresos.idcobrador", 
                                "doc.formacobro, sum(doci.montop) as totalasignado", $condicion, "", "group by doc.formacobro");
        return $data;
    }

}

?>