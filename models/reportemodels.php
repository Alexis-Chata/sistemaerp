<?php

class Reporte extends Applicationbase {

    private $tablaStockProducto = "wc_producto,wc_linea,wc_almacen";
    private $tablaStockPorLinea = "wc_producto,wc_linea,wc_almacen";
    private $tablaDietario = "wc_dietario";
    private $tablaDetalleCobro = "wc_detalleordencobro";
    //private $tablaKardex="wc_detallemovimiento,wc_producto,wc_movimiento";
    private $tablaKardex = "wc_detallemovimiento as dm,wc_producto as p,wc_movimiento as m,wc_linea as l";
    private $tablaReporteOrdenCompra = "wc_ordencompra,wc_almacen,wc_proveedor";
    private $tablaAgotados = "wc_detallemovimiento,wc_movimiento,wc_producto";
    private $tablaproducto = "wc_producto";

    function reporteStockValorizado($idLinea = '', $idSubLinea = '') {
        $condicion = '';
        if (!empty($idLinea)) {
            $condicion = "and t2.idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "and t1.idlinea=$idSubLinea";
        }
        $producto = $this->leeRegistro3($this->tablaStockPorLinea, "nompro,codigop,nomalm,nomlin,unidadmedida,stockactual,preciolista", "t1.estado=1 and t2.estado=1 $condicion", "", 2);
        return $producto;
    }

    function reportingresosconasignacion($idordenventa = "", $idcliente = "", $idcobrador = "", $nrorecibo = "", $fechaInicio = "", $fechaFinal = "", $monto = "", $idtipocobro = "", $tipo = "") {
        $condicion = "i.`estado`=1 and i.`esvalidado`=1 ";
        $condicion .= !empty($idordenventa) ? " and i.`idordenventa`='$idordenventa' " : " ";
        $condicion .= !empty($idcliente) ? " and i.`idcliente`='$idcliente' " : " ";
        $condicion .= !empty($idcobrador) ? " and i.`idcobrador`='$idcobrador' " : " ";

        if (!empty($nrorecibo)) {
            if ($idtipocobro == 3 || $idtipocobro == 4) {
                $condicion .= " and i.`nrooperacion`='$nrorecibo' ";
            } else if ($idtipocobro == 2) {
                $condicion .= " and i.`nrodoc`='$nrorecibo' ";
            } else {
                $condicion .= " and i.`nrorecibo`='$nrorecibo' ";
            }
        }
        if (!empty($tipo)) {
            $condicion .= " and i.`tipo`='$tipo' ";
        }

        $condicion .= !empty($fechaInicio) ? " and i.`fcobro`>='$fechaInicio' " : " ";
        $condicion .= !empty($fechaFinal) ? " and i.`fcobro`<='$fechaFinal' " : " ";
        $condicion .= !empty($idtipocobro) ? " and i.`tipocobro`='$idtipocobro' " : " ";
        $condicion .= !empty($monto) ? $monto : "";

        $data = $this->leeRegistro(
                "wc_ingresos i
                 left join wc_detalleordencobroingreso doci on doci.idingreso = i.idingresos and doci.estado = 1
                 left join wc_detalleordencobro doc on doc.iddetalleordencobro = doci.iddetalleordencobro and doc.estado = 1
                 inner join wc_cliente c on i.`idcliente`=c.`idcliente`
                 inner join wc_actor a on a.`idactor`=i.`idcobrador`
                 ", "i.*, c.*, doc.numeroletra, a.*", $condicion, ""
        );
        return $data;
    }

    function resumenPesados_detalle($txtFechaInicio = "", $txtFechaFinal = "", $lstCategoria = "", $lstZona = "", $txtIdCliente = "", $txtidOrdenVenta = "", $lstMoneda = "", $cmbCondicion = "") {
        $condicion = "wc_detalleordencobro.situacion = '' and "
                . "wc_detalleordencobro.estado = 1 and "
                . "wc_ordencobro.estado = 1 and "
                . "wc_categoria.`idpadrec` in (40, 48) and "
                . "wc_detalleordencobro.saldodoc > 0 and "
                . "wc_ordenventa.estado = 1 and "
                . "wc_ordenventa.esanulado = 0 ";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_ordenventa.fechacreacion >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_ordenventa.fechacreacion <= '" . $txtFechaFinal . "'";
        }
        if (!empty($lstCategoria)) {
            $condicion .= ' and wc_categoria.idpadrec=' . $lstCategoria;
        }
        if (!empty($lstZona)) {
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        }
        if (!empty($txtIdCliente)) {
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        }
        if (!empty($txtidOrdenVenta)) {
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtidOrdenVenta;
        }
        if (!empty($lstMoneda)) {
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        }
        if (!empty($cmbCondicion) && $cmbCondicion != 4 && $cmbCondicion != 2) {
            $condicion .= ' and wc_detalleordencobro.formacobro=' . $cmbCondicion;
        } else if ($cmbCondicion == 2) {
            $condicion .= " and wc_detalleordencobro.`situacion`='' and
                        wc_detalleordencobro.`formacobro`='2' and
                        wc_detalleordencobro.`montoprotesto`=0";
        } else if ($cmbCondicion == 4) {
            $condicion .= " and wc_detalleordencobro.`situacion`!='reprogramado' and
                        wc_detalleordencobro.`situacion`!='anulado' and
                        wc_detalleordencobro.`situacion`!='extornado' and
                        wc_detalleordencobro.`situacion`!='refinanciado' and
                        wc_detalleordencobro.`situacion`!='protestado' and
                        wc_detalleordencobro.`situacion`!='renovado' and
                        wc_detalleordencobro.`situacion`='' and
                        wc_detalleordencobro.`formacobro`='2' and
                        (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                 INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                 INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                 INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                 INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                 INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                 INNER JOIN `wc_ordencobro` wc_ordencobro ON wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                 INNER JOIN `wc_detalleordencobro` wc_detalleordencobro ON wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", 
                "wc_ordenventa.idmoneda,
                 wc_ordenventa.idordenventa,
                 wc_ordenventa.codigov,
                 wc_ordenventa.fordenventa,
                 wc_ordenventa.situacion as situacionov,
                 wc_ordenventa.idvendedor,
                 concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                 wc_cliente.razonsocial,
                 (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                 wc_ordencobro.situacion as situacionoc,
                 wc_ordencobro.femision,
                 wc_ordencobro.importeordencobro,
                 wc_ordencobro.saldoordencobro,
                 wc_categoria.`idcategoria`, 
                 wc_categoria.`idpadrec`, 
                 wc_detalleordencobro.recepcionLetras as recepLetra,
                 wc_detalleordencobro.*,
                 wc_ordenventa.importepagado, substring( wc_detalleordencobro.referencia,9,1) as referencia1, substring( wc_detalleordencobro.referencia,11,1) as referencia2", $condicion, 
                "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        
        return $data;
    }

    function totalesStockValorizado() {
        $condicion = '';
        if (!empty($idLinea)) {
            $condicion = "and t2.idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "and t1.idlinea=$idSubLinea";
        }
        $suma = $this->leeRegistro3($this->tablaStockPorLinea, "sum(preciolista*stockactual) as totalpreciolista", "t1.estado=1 and t2.estado=1 $condicion", "", 2);
        return $suma;
    }

    function reportletraszona($filtro = "", $idzona = "", $idcategoriaprincipal = "", $idcategorias = "", $idvendedor = "", $idtipocobranza = "", $fechainicio = "", $fechafinal = "", $octavaNovena = "", $situacion = "", $fechaPagoInicio = "", $fechaPagoFinal = "", $IdCliente = "", $IdOrdenVenta = "", $orderDireccion = "", $tipoBanco = "", $recepLetras = "") {
        $condicion = "wc_detalleordencobro.`estado`=1 and wc_ordenventa.`esguiado`=1 and wc_ordenventa.`estado`=1 and wc_ordencobro.`estado`=1 and wc_detalleordencobro.`situacion`!='reprogramado'  and wc_detalleordencobro.`situacion`!='anulado'  and wc_detalleordencobro.`situacion`!='extornado' and wc_detalleordencobro.`situacion`!='refinanciado' and wc_detalleordencobro.`situacion`!='protestado' and wc_detalleordencobro.`situacion`!='renovado'  ";
        $condicion .= !empty($idzona) ? " and wc_zona.`idzona`='$idzona' " : "";
        $condicion .= !empty($idcategoriaprincipal) ? " and wc_categoria.`idpadrec`='$idcategoriaprincipal' " : "";
        $condicion .= !empty($idcategorias) ? $idcategorias : "";
        $condicion .= !empty($idvendedor) ? " and wc_actor.`idactor`='$idvendedor' " : "";
        if($recepLetras==1){
            $condicion .= "and wc_detalleordencobro.`recepcionLetras`='PA'";
        } else if($recepLetras==2){
            $condicion .= "and wc_detalleordencobro.`recepcionLetras`=''";
        }
        if (!empty($idtipocobranza)) {
            $sql = "Select idtipocobranza,nombre,diaini,diafin
                                From wc_tipocobranza Where estado=0 and ntc='A' and idtipocobranza=" . $idtipocobranza;
            $data1 = $this->EjecutaConsulta($sql);

            $nomtipocobranza = $data[0]['nombre'];
            $diaini = (int) $data1[0]['diaini'];
            $diafin = (int) $data1[0]['diafin'];
            $condicion .= "AND DATEDIFF(NOW(),wc_detalleordencobro.`fvencimiento`) BETWEEN " . $diaini . " and " . $diafin . " ";
            $situacion .= " and wc_detalleordencobro.`situacion`='' ";
        }
        $condicion .= !empty($tipoBanco) ? " and wc_detalleordencobro.`numerounico`='$tipoBanco' " : "";
        $condicion .= !empty($IdCliente) ? " and wc_cliente.`idcliente`='$IdCliente' " : "";
        $condicion .= !empty($IdOrdenVenta) ? " and wc_ordenventa.`idordenventa`='$IdOrdenVenta' " : "";
        $condicion .= !empty($fechainicio) ? " and wc_detalleordencobro.`fechagiro`>='$fechainicio' " : "";
        $condicion .= !empty($fechafinal) ? " and wc_detalleordencobro.`fechagiro`<='$fechafinal' " : "";
        $condicion .= !empty($fechaPagoInicio) ? " and wc_detalleordencobro.`fechapago`>='$fechaPagoInicio' " : "";
        $condicion .= !empty($fechaPagoFinal) ? " and wc_detalleordencobro.`fechapago`<='$fechaPagoFinal' " : "";
        $condicion .= !empty($octavaNovena) ? $octavaNovena : "";
        $condicion .= !empty($situacion) ? $situacion : "";
        $condicion .= !empty($filtro) ? " and  " . $filtro . " " : "";
        if ($orderDireccion == "" or $orderDireccion == "0") {
            $data = $this->leeRegistro(
                    "`wc_ordenventa` wc_ordenventa
                                INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.IdMoneda=wc_moneda.idmoneda
                                INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                                INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                                INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                                inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_actor.`apellidomaterno`,
                                 wc_ordenventa.`codigov`,
                                (wc_ordenventa.`importepagado`-wc_ordenventa.`importedevolucion`) as `importepagado`,
                                 wc_ordenventa.`idordenventa`,
                                 wc_ordenventa.`idtipocobranza`,
                                 wc_ordenventa.`fechadespacho`,
                                 wc_ordenventa.`fechavencimiento`,
                                 wc_ordenventa.`importedevolucion`,
                                 wc_moneda.`simbolo`,
                                 wc_cliente.`idcliente`,
                                 wc_cliente.`iddistrito`,
                                 wc_cliente.`apellido1`,
                                 wc_cliente.`apellido2`,
                                 replace(wc_cliente.direccion,'.',' ') as direccion,
                                 wc_categoria.`idcategoria`,
                                 wc_categoria.`idpadrec`,
                                 wc_categoria.`codigoc`,
                                 wc_categoria.`nombrec`,
                                 wc_actor.`idactor`,
                                 wc_actor.`codigoa`,
                                 wc_cliente.`nombrecli`,
                                 wc_cliente.`razonsocial`,
                                 wc_zona.`idzona`,
                                 wc_zona.`nombrezona`,
                                 wc_actor.`nombres`,
                                 wc_actor.`apellidopaterno`,
                                 wc_actor.`apellidomaterno`,
                                 wc_ordencobro.`saldoordencobro`,
                                 wc_detalleordencobro.`iddetalleordencobro`,
                                 wc_detalleordencobro.`situacion`,
                                 wc_detalleordencobro.`saldodoc`,
                                 wc_detalleordencobro.`importedoc`,
                                 wc_detalleordencobro.`numeroletra`,
                                 wc_detalleordencobro.`referencia`,
                                 wc_detalleordencobro.`numerounico`,
                                 wc_detalleordencobro.`recepcionLetras`,
                                 wc_detalleordencobro.`fechagiro`,
                                 wc_detalleordencobro.`fvencimiento`,
                                 wc_detalleordencobro.`gastosrenovacion`,
                                 wc_detalleordencobro.`recepcionLetras`,
                                 wc_detalleordencobro.`fechapago`,
                                 wc_detalleordencobro.`formacobro`,
                                 wc_detalleordencobro.`montoprotesto`,
                                 CASE  WHEN (SUBSTRING( wc_detalleordencobro.`numeroletra`,9,1)='R' OR wc_detalleordencobro.`renovado`>0) and wc_detalleordencobro.`protesto`='' THEN CONCAT('RENOV.',SUBSTRING( wc_detalleordencobro.`referencia`,10,1)) WHEN SUBSTRING( wc_detalleordencobro.`referencia`,9,1)='P' THEN 'PROTE.' WHEN SUBSTRING(wc_detalleordencobro.`numeroletra`,9,1)='' AND length(wc_detalleordencobro.`numeroletra`)>1 THEN 'REPRO.' WHEN wc_detalleordencobro.`tipogasto`=1 THEN 'GAST. RENOV.'  WHEN wc_detalleordencobro.`tipogasto`=2 THEN 'GAST. PROTE.' ELSE ' ' END as Proviene,
                                 CASE wc_detalleordencobro.`situacion` when '' then DATEDIFF(NOW(),wc_detalleordencobro.`fvencimiento`) else 0 end  as DifFechas", $condicion, "wc_zona.nombrezona asc, trim(wc_cliente.`razonsocial`) asc ,wc_cliente.`idCliente`,wc_ordenventa.`idordenventa`,wc_detalleordencobro.`fvencimiento`"
            );
        }
        if ($orderDireccion == "1") {
            $data = $this->leeRegistro(
                    "`wc_ordenventa` wc_ordenventa
                                INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.IdMoneda=wc_moneda.idmoneda
                                INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                                INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                                INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                                inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_actor.`apellidomaterno`,
                                 wc_ordenventa.`codigov`,
                                (wc_ordenventa.`importepagado`-wc_ordenventa.`importedevolucion`) as `importepagado`,
                                 wc_ordenventa.`idordenventa`,
                                 wc_ordenventa.`idtipocobranza`,
                                 wc_ordenventa.`fechadespacho`,
                                 wc_ordenventa.`fechavencimiento`,
                                 wc_ordenventa.`importedevolucion`,
                                 wc_moneda.`simbolo`,
                                 wc_cliente.`idcliente`,
                                 wc_cliente.`iddistrito`,
                                 wc_cliente.`apellido1`,
                                 wc_cliente.`apellido2`,
                                 replace(wc_cliente.direccion,'.',' ') as direccion,
                                 replace(replace(wc_cliente.direccion,'.',' '),'-',' ') as  direccionOrder1,
                                 wc_categoria.`idcategoria`,
                                 wc_categoria.`idpadrec`,
                                 wc_categoria.`codigoc`,
                                 wc_categoria.`nombrec`,
                                 wc_actor.`idactor`,
                                 wc_actor.`codigoa`,
                                 wc_cliente.`nombrecli`,
                                 wc_cliente.`razonsocial`,
                                 wc_zona.`idzona`,
                                 wc_zona.`nombrezona`,
                                 wc_actor.`nombres`,
                                 wc_actor.`apellidopaterno`,
                                 wc_actor.`apellidomaterno`,
                                 wc_ordencobro.`saldoordencobro`,
                                 wc_detalleordencobro.`iddetalleordencobro`,
                                 wc_detalleordencobro.`situacion`,
                                 wc_detalleordencobro.`saldodoc`,
                                 wc_detalleordencobro.`importedoc`,
                                 wc_detalleordencobro.`numeroletra`,
                                 wc_detalleordencobro.`referencia`,
                                 wc_detalleordencobro.`numerounico`,
                                 wc_detalleordencobro.`recepcionLetras`,
                                 wc_detalleordencobro.`fechagiro`,
                                 wc_detalleordencobro.`fvencimiento`,
                                 wc_detalleordencobro.`gastosrenovacion`,
                                 wc_detalleordencobro.`recepcionLetras`,
                                 wc_detalleordencobro.`fechapago`,
                                 wc_detalleordencobro.`formacobro`,
                                 wc_detalleordencobro.`montoprotesto`,
                                 CASE  WHEN (SUBSTRING( wc_detalleordencobro.`numeroletra`,9,1)='R' OR wc_detalleordencobro.`renovado`>0) and wc_detalleordencobro.`protesto`='' THEN CONCAT('RENOV.',SUBSTRING( wc_detalleordencobro.`referencia`,10,1)) WHEN SUBSTRING( wc_detalleordencobro.`referencia`,9,1)='P' THEN 'PROTE.' WHEN SUBSTRING(wc_detalleordencobro.`numeroletra`,9,1)='' AND length(wc_detalleordencobro.`numeroletra`)>1 THEN 'REPRO.' WHEN wc_detalleordencobro.`tipogasto`=1 THEN 'GAST. RENOV.'  WHEN wc_detalleordencobro.`tipogasto`=2 THEN 'GAST. PROTE.' ELSE ' ' END as Proviene,
                                 CASE wc_detalleordencobro.`situacion` when '' then DATEDIFF(NOW(),wc_detalleordencobro.`fvencimiento`) else 0 end  as DifFechas", $condicion, "wc_zona.nombrezona asc, trim(direccionOrder1) asc ,wc_cliente.`idCliente`,wc_ordenventa.`idordenventa`,wc_detalleordencobro.`fvencimiento`"
            );
        }
        return $data;
    }


    function enviarDietario($_DATOS_EXCEL) {
//
        for ($i = 0; $i < count($_DATOS_EXCEL); $i++) {
            $data = array('numero_letra' => (string) $_DATOS_EXCEL[$i]['numero_letra'],
                'numero_unico' => $_DATOS_EXCEL[$i]['numero_unico'],
                'aceptante_nombre' => $_DATOS_EXCEL[$i]['aceptante_nombre'],
                'aceptante_documento' => $_DATOS_EXCEL[$i]['aceptante_documento'],
                'fechavencimiento' => $_DATOS_EXCEL[$i]['fechavencimiento'],
                'monto' => $_DATOS_EXCEL[$i]['monto'],
                'estado' => $_DATOS_EXCEL[$i]['estado'],
                'causal' => $_DATOS_EXCEL[$i]['causal'],
                'fechaingreso' => $_DATOS_EXCEL[$i]['fechaingreso'],
                'fechadescargo' => $_DATOS_EXCEL[$i]['fechadescargo'],
                'interes' => $_DATOS_EXCEL[$i]['interes'],
                'comision' => $_DATOS_EXCEL[$i]['comision'],
                'portes' => $_DATOS_EXCEL[$i]['portes'],
                'protesto' => $_DATOS_EXCEL[$i]['protesto'],
                'aceptante_lugar' => $_DATOS_EXCEL[$i]['aceptante_lugar']
            );
            $numeroUnico = $_DATOS_EXCEL[$i]['numero_unico'];
            $idDietario = $this->leeRegistro($this->tablaDietario, 'iddietario', "numero_unico=$numeroUnico", "");

            if (count($idDietario)) {
                $id = $idDietario[0]['iddietario'];
                $exito = $this->actualizaRegistro($this->tablaDietario, $data, "iddietario=$id");
//                             return $exito;
            } else {
                $exito = $this->grabaDietario($this->tablaDietario, $data);
//                            return $exito;
            }
        }
        return $exito;
    }

    function listarDietario($fechainicio, $fechafinal) {
        $condicion = "fechadescargo between $fechainicio and $fechafinal";
        $data = $this->leeRegistro($this->tablaDietario, "", "", "$condicion");
        return $data;
    }

    function reporteDetalleCompraXMes($idordenventa) {
        $sql = "Select doc.*,p.*,m.nombre as marca,um.nombre as unidadmedida,oc.vbimportaciones
					From wc_detalleordencompra doc
					Inner join wc_ordencompra oc On doc.idordencompra=oc.idordencompra
					Inner Join wc_producto p On doc.idproducto=p.idProducto
					Left Join wc_marca m On p.idmarca=m.idmarca
					Left Join wc_unidadmedida um On um.idunidadmedida=p.unidadmedida
				 Where doc.estado=1 and doc.idordencompra=" . $idordenventa;

        $data = $this->EjecutaConsulta($sql);
        //$data=$this->leeRegistro2($this->tablas,"","idordencompra=$idOrdenCompra","");
        return $data;
    }

    function reporteestructuracostos($anho, $mes) {
        $dia = $this->obtenerFinMes($mes, $anho);
        $fecha = $anho . "-" . $mes . "-" . $dia;
        $condicion = "oc.estado='1' and oc.fechacreacion>='" . $anho . "-" . $mes . "-01 00:00:00' and oc.fechacreacion<='" . $fecha . " 11:59:59'";
        $data = $this->leeRegistro(
                "wc_ordencompra oc inner join wc_almacen a on oc.idalmacen=a.idalmacen
				inner join wc_proveedor p on oc.idproveedor=p.idproveedor
				", "", $condicion, "oc.fechacreacion asc");
        return $data;
    }

    function reporteestructuradecostos($anho, $mes) {
        $dia = $this->obtenerFinMes($mes, $anho);
        $fecha = $anho . "-" . $mes . "-" . $dia;
        $condicion = "oc.estado='1' and oc.fechacreacion>='" . $anho . "-" . $mes . "-01 00:00:00' and oc.fechacreacion<='" . $fecha . " 11:59:59'";
        $data = $this->leeRegistro(
                "wc_ordencompra oc inner join wc_almacen a on oc.idalmacen=a.idalmacen
				inner join wc_proveedor p on oc.idproveedor=p.idproveedor
				", "", $condicion, "oc.fechacreacion desc");
        return $data;
    }

    function reporteProductoDureza($idLinea, $idSubLinea, $idProducto, $stock = 1) {
        $condicion = "t1.estado=1";
        if (!empty($idLinea)) {
            $condicion = "idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "t2.idlinea=$idSubLinea";
        }
        if (!empty($idProducto)) {
            $condicion = "t1.idproducto=$idProducto";
        }
        if ($stock == 2)
            $condicion .= " and t1.stockactual > 0";
        else if ($stock == 3)
            $condicion .= " and t1.stockactual = 0";
        $stockProducto = $this->leeRegistro("wc_producto t1
                        inner join  wc_linea t2 on t1.idlinea=t2.idlinea", "t1.idproducto", "$condicion", "", "order by t1.fechaingreso desc");
        return $stockProducto;
    }

    function reporteIdproductos($idLinea, $idSubLinea, $idProducto) {
        $condicion = "t1.estado=1";
        if (!empty($idLinea)) {
            $condicion = "idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "t2.idlinea=$idSubLinea";
        }
        if (!empty($idProducto)) {
            $condicion = "t1.idproducto=$idProducto";
        }
        $producto = $this->leeRegistro("wc_producto t1
                    inner join wc_linea t2 on t1.idlinea=t2.idlinea", "t1.idproducto", "$condicion", "", "order by t1.fechaingreso desc");
        return $producto;
    }

    function reporteStockProducto($idAlmacen, $idLinea, $idSubLinea, $idProducto) {
        $productoOrepuesto=" and idtipoproducto=0";
        $condicion = "t1.estado=1".$productoOrepuesto;
        if (!empty($idAlmacen)) {
            $condicion = "t1.idalmacen=$idAlmacen".$productoOrepuesto;
        }
        if (!empty($idLinea)) {
            $condicion = "idpadre=$idLinea".$productoOrepuesto;
        }
        if (!empty($idSubLinea)) {
            $condicion = "t2.idlinea=$idSubLinea".$productoOrepuesto;
        }
        if (!empty($idProducto)) {
            $condicion = "t1.idproducto=$idProducto".$productoOrepuesto;
        }
        $stockProducto = $this->leeRegistro("wc_producto t1
            inner join  wc_linea t2 on t1.idlinea=t2.idlinea
            inner join wc_almacen t3 on t1.idalmacen=t3.idalmacen
            left join wc_unidadmedida t4 on t1.unidadmedida=t4.idunidadmedida
            ", "*,t4.codigo as unidadmedida", "$condicion", "", "order by idpadre,trim(t1.codigopa) asc");
        return $stockProducto;
    }

    function reporteStockProductoRep($idAlmacen, $idLinea, $idSubLinea, $idProducto) {
        $productoOrepuesto=" and idtipoproducto=1";
        $condicion = "t1.estado=1".$productoOrepuesto;
        if (!empty($idProducto)) {
            $condicion = "t1.idproducto=$idProducto".$productoOrepuesto;
        }
        $stockProducto = $this->leeRegistro("wc_producto t1
            inner join  wc_linea t2 on t1.idlinea=t2.idlinea
            inner join wc_almacen t3 on t1.idalmacen=t3.idalmacen
            left join wc_unidadmedida t4 on t1.unidadmedida=t4.idunidadmedida
            ", "*,t4.codigo as unidadmedida", "$condicion", "", "order by idpadre,trim(t1.codigopa) asc");
        return $stockProducto;
    }
    
    function reporteListaPrecio_consinstock($idAlmacen, $idLinea, $idSubLinea, $idProducto, $lstStock = "") {
        $condicion = "t1.estado=1";        
        if (!empty($idAlmacen)) {
            $condicion = "t1.idalmacen=$idAlmacen";
        }
        if (!empty($idLinea)) {
            $condicion = "idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "t2.idlinea=$idSubLinea";
        }
        if (!empty($idProducto)) {
            $condicion = "t1.idproducto=$idProducto";
        }
        if ($lstStock == 1) {
            $condicion .= " and t1.stockactual>0";
        } else if ($lstStock == 2) {
            $condicion .= " and t1.stockactual=0";
        }
        
        $stockProducto = $this->leeRegistro("wc_producto t1
                                                inner join  wc_linea t2 on t1.idlinea=t2.idlinea
                                                inner join wc_almacen t3 on t1.idalmacen=t3.idalmacen
                                                left join wc_unidadmedida t4 on t1.unidadmedida=t4.idunidadmedida
                                                left join wc_empaque em on t1.empaque=em.idempaque
                                                ", "*,t4.nombre as nombremedida", "$condicion and t1.estado=1", "", "order by t2.nomlin,t2.idpadre,t2.idlinea,t1.idproducto asc");
        return $stockProducto;
    }

    function reporteListaPrecio($idAlmacen, $idLinea, $idSubLinea, $idProducto) {
        $condicion = "t1.estado=1";
        if (!empty($idAlmacen)) {
            $condicion = "t1.idalmacen=$idAlmacen";
        }
        if (!empty($idLinea)) {
            $condicion = "idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "t2.idlinea=$idSubLinea";
        }
        if (!empty($idProducto)) {
            $condicion = "t1.idproducto=$idProducto";
        }
        $stockProducto = $this->leeRegistro("wc_producto t1
                                                inner join  wc_linea t2 on t1.idlinea=t2.idlinea
                                                inner join wc_almacen t3 on t1.idalmacen=t3.idalmacen
                                                left join wc_unidadmedida t4 on t1.unidadmedida=t4.idunidadmedida
                                                left join wc_empaque em on t1.empaque=em.idempaque
                                                ", "*,t4.nombre as nombremedida", "$condicion and t1.stockactual>0 and t1.estado=1", "", "order by t2.nomlin,t2.idpadre,t2.idlinea,t1.idproducto asc");
        return $stockProducto;
    }

    function reporteKardex($idAlmacen, $idLinea, $idSubLinea, $idProducto) {
        $condicion = "";
        if (!empty($idAlmacen)) {
            $condicion = "and idalmacen=$idAlmacen";
        }
        if (!empty($idLinea)) {
            $condicion = "and idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "and p.idlinea=$idSubLinea";
        }
        if (!empty($idProducto)) {
            $condicion = "and p.idproducto=$idProducto";
        }
        //$kardex=$this->leeRegistro3($this->tablaKardex,"","$condicion","",2);
        $kardex = $this->leeRegistro($this->tablaKardex, "", "dm.idproducto=p.idproducto and dm.idmovimiento=m.idmovimiento and p.idlinea=l.idlinea $condicion", "");
        return $kardex;
    }

    function reporteAgotados($fecha, $fechaInicio, $fechaFinal, $idProducto) {
        $condicion = "";
        if (!empty($fecha)) {
            $condicion = "and fechamovimiento='$fecha'";
        }
        if (!empty($fechaInicio)) {
            $condicion = "and fechamovimiento between '$fechaInicio' and '$fechaFinal'";
        }
        if (!empty($idProducto)) {
            $condicion = "and t3.idproducto=$idProducto";
        }
        $agotados = $this->leeRegistro3($this->tablaAgotados, "", "t3.stockactual=0 $condicion", "", 2, "");
        return $agotados;
    }

    function reporteOrdenCompra($idProveedor, $fecha, $fechaInicio, $fechaFinal) {
        $condicion = "";
        if (!empty($idProveedor)) {
            $condicion = "and t3.idproveedor=$idProveedor";
        }
        if (!empty($fecha)) {
            $condicion = "and fordencompra='$fecha'";
        }
        if (!empty($fechaInicio)) {
            $condicion = "and fordencompra between '$fechaInicio' and '$fechaFinal'";
        }
        $data = $this->leeRegistro3($this->tablaReporteOrdenCompra, "", "t1.estado=1 $condicion", "", 2);
        return $data;
    }
    
    function reportletras_verprotestopordetalleoc($filtro = "", $situacion = "", $iddetalleordencobro = "") {
        $condicion = "wc_detalleordencobro.`estado`=1 and wc_ordenventa.`esguiado`=1 and wc_ordenventa.`estado`=1 and wc_ordencobro.`estado`=1 and wc_detalleordencobro.`situacion`!='reprogramado'  and wc_detalleordencobro.`situacion`!='anulado'  and wc_detalleordencobro.`situacion`!='extornado' and wc_detalleordencobro.`situacion`!='refinanciado' and wc_detalleordencobro.`situacion`!='protestado' and wc_detalleordencobro.`situacion`!='renovado'  ";
        
        $condicion .= !empty($situacion) ? $situacion : "";
        $condicion .= !empty($filtro) ? " and  " . $filtro . " " : "";
        $condicion .= !empty($iddetalleordencobro) ? " and iddetalleordencobro='" . $iddetalleordencobro . "' " : "";

        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                            INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.IdMoneda=wc_moneda.idmoneda
                            INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                            INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                            INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                            INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                            INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                            inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                            inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_actor.`apellidomaterno`,
                             wc_ordenventa.`codigov`,
                            (wc_ordenventa.`importepagado`-wc_ordenventa.`importedevolucion`) as `importepagado`,
                             wc_ordenventa.`idordenventa`,
                             wc_ordenventa.`idtipocobranza`,
                             wc_ordenventa.`fechadespacho`,
                             wc_ordenventa.`fechavencimiento`,
                             wc_ordenventa.`importedevolucion`,
                             wc_moneda.`simbolo`,
                             wc_cliente.`idcliente`,
                             wc_cliente.`iddistrito`,
                             wc_cliente.`apellido1`,
                             wc_cliente.`apellido2`,
                             replace(wc_cliente.direccion,'.',' ') as direccion,
                             wc_categoria.`idcategoria`,
                             wc_categoria.`idpadrec`,
                             wc_categoria.`codigoc`,
                             wc_categoria.`nombrec`,
                             wc_actor.`idactor`,
                             wc_actor.`codigoa`,
                             wc_cliente.`nombrecli`,
                             wc_cliente.`razonsocial`,
                             wc_zona.`idzona`,
                             wc_zona.`nombrezona`,
                             wc_actor.`nombres`,
                             wc_actor.`apellidopaterno`,
                             wc_actor.`apellidomaterno`,
                             wc_ordencobro.`saldoordencobro`,
                             wc_detalleordencobro.`iddetalleordencobro`,
                             wc_detalleordencobro.`situacion`,
                             wc_detalleordencobro.`saldodoc`,
                             wc_detalleordencobro.`importedoc`,
                             wc_detalleordencobro.`numeroletra`,
                             wc_detalleordencobro.`referencia`,
                             wc_detalleordencobro.`numerounico`,
                             wc_detalleordencobro.`recepcionLetras`,
                             wc_detalleordencobro.`fechagiro`,
                             wc_detalleordencobro.`fvencimiento`,
                             wc_detalleordencobro.`gastosrenovacion`,
                             wc_detalleordencobro.`recepcionLetras`,
                             wc_detalleordencobro.`fechapago`,
                             wc_detalleordencobro.`formacobro`,
                             wc_detalleordencobro.`montoprotesto`,
                             CASE  WHEN (SUBSTRING( wc_detalleordencobro.`numeroletra`,9,1)='R' OR wc_detalleordencobro.`renovado`>0) and wc_detalleordencobro.`protesto`='' THEN CONCAT('RENOV.',SUBSTRING( wc_detalleordencobro.`referencia`,10,1)) WHEN SUBSTRING( wc_detalleordencobro.`referencia`,9,1)='P' THEN 'PROTE.' WHEN SUBSTRING(wc_detalleordencobro.`numeroletra`,9,1)='' AND length(wc_detalleordencobro.`numeroletra`)>1 THEN 'REPRO.' WHEN wc_detalleordencobro.`tipogasto`=1 THEN 'GAST. RENOV.'  WHEN wc_detalleordencobro.`tipogasto`=2 THEN 'GAST. PROTE.' ELSE ' ' END as Proviene,
                             CASE wc_detalleordencobro.`situacion` when '' then DATEDIFF(NOW(),wc_detalleordencobro.`fvencimiento`) else 0 end  as DifFechas", $condicion, "trim(wc_cliente.`razonsocial`) asc ,wc_cliente.`idCliente`,wc_ordenventa.`idordenventa`,wc_detalleordencobro.`fvencimiento`"
        );
                
        return $data;
    }

    function reportletras($filtro = "", $idzona = "", $idcategoriaprincipal = "", $idcategorias = "", $idvendedor = "", $idtipocobranza = "", $fechainicio = "", $fechafinal = "", $octavaNovena = "", $situacion = "", $fechaPagoInicio = "", $fechaPagoFinal = "", $IdCliente = "", $IdOrdenVenta = "", $orderDireccion = "", $tipoBanco = "", $recepLetras = "") {
        $condicion = "wc_detalleordencobro.`estado`=1 and wc_ordenventa.`esguiado`=1 and wc_ordenventa.`estado`=1 and wc_ordencobro.`estado`=1 and wc_detalleordencobro.`situacion`!='reprogramado'  and wc_detalleordencobro.`situacion`!='anulado'  and wc_detalleordencobro.`situacion`!='extornado' and wc_detalleordencobro.`situacion`!='refinanciado' and wc_detalleordencobro.`situacion`!='protestado' and wc_detalleordencobro.`situacion`!='renovado'  ";
        $condicion .= !empty($idzona) ? " and wc_zona.`idzona`='$idzona' " : "";
        $condicion .= !empty($idcategoriaprincipal) ? " and wc_categoria.`idpadrec`='$idcategoriaprincipal' " : "";
        $condicion .= !empty($idcategorias) ? $idcategorias : "";
        $condicion .= !empty($idvendedor) ? " and wc_actor.`idactor`='$idvendedor' " : "";
        if($recepLetras==1){
            $condicion .= "and wc_detalleordencobro.`recepcionLetras`='PA'";
        } else if($recepLetras==2){
            $condicion .= "and wc_detalleordencobro.`recepcionLetras`=''";
        }
        if (!empty($idtipocobranza)) {
            $sql = "Select idtipocobranza,nombre,diaini,diafin
                                From wc_tipocobranza Where estado=0 and ntc='A' and idtipocobranza=" . $idtipocobranza;
            $data1 = $this->EjecutaConsulta($sql);

            $nomtipocobranza = $data[0]['nombre'];
            $diaini = (int) $data1[0]['diaini'];
            $diafin = (int) $data1[0]['diafin'];
            $condicion .= "AND DATEDIFF(NOW(),wc_detalleordencobro.`fvencimiento`) BETWEEN " . $diaini . " and " . $diafin . " ";
            $situacion .= " and wc_detalleordencobro.`situacion`='' ";
        }
        $condicion .= !empty($tipoBanco) ? " and wc_detalleordencobro.`numerounico`='$tipoBanco' " : "";
        $condicion .= !empty($IdCliente) ? " and wc_cliente.`idcliente`='$IdCliente' " : "";
        $condicion .= !empty($IdOrdenVenta) ? " and wc_ordenventa.`idordenventa`='$IdOrdenVenta' " : "";
        $condicion .= !empty($fechainicio) ? " and wc_detalleordencobro.`fechagiro`>='$fechainicio' " : "";
        $condicion .= !empty($fechafinal) ? " and wc_detalleordencobro.`fechagiro`<='$fechafinal' " : "";
        $condicion .= !empty($fechaPagoInicio) ? " and wc_detalleordencobro.`fechapago`>='$fechaPagoInicio' " : "";
        $condicion .= !empty($fechaPagoFinal) ? " and wc_detalleordencobro.`fechapago`<='$fechaPagoFinal' " : "";
        $condicion .= !empty($octavaNovena) ? $octavaNovena : "";
        $condicion .= !empty($situacion) ? $situacion : "";
        $condicion .= !empty($filtro) ? " and  " . $filtro . " " : "";
        if ($orderDireccion == "" or $orderDireccion == "0") {
            $data = $this->leeRegistro(
                    "`wc_ordenventa` wc_ordenventa
                                INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.IdMoneda=wc_moneda.idmoneda
                                INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                                INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                                INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                                inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_actor.`apellidomaterno`,
                                 wc_ordenventa.`codigov`,
                                (wc_ordenventa.`importepagado`-wc_ordenventa.`importedevolucion`) as `importepagado`,
                                 wc_ordenventa.`idordenventa`,
                                 wc_ordenventa.`idtipocobranza`,
                                 wc_ordenventa.`fechadespacho`,
                                 wc_ordenventa.`fechavencimiento`,
                                 wc_ordenventa.`importedevolucion`,
                                 wc_moneda.`simbolo`,
                                 wc_cliente.`idcliente`,
                                 wc_cliente.`iddistrito`,
                                 wc_cliente.`apellido1`,
                                 wc_cliente.`apellido2`,
                                 replace(wc_cliente.direccion,'.',' ') as direccion,
                                 wc_categoria.`idcategoria`,
                                 wc_categoria.`idpadrec`,
                                 wc_categoria.`codigoc`,
                                 wc_categoria.`nombrec`,
                                 wc_actor.`idactor`,
                                 wc_actor.`codigoa`,
                                 wc_cliente.`nombrecli`,
                                 wc_cliente.`razonsocial`,
                                 wc_zona.`idzona`,
                                 wc_zona.`nombrezona`,
                                 wc_actor.`nombres`,
                                 wc_actor.`apellidopaterno`,
                                 wc_actor.`apellidomaterno`,
                                 wc_ordencobro.`saldoordencobro`,
                                 wc_detalleordencobro.`iddetalleordencobro`,
                                 wc_detalleordencobro.`situacion`,
                                 wc_detalleordencobro.`saldodoc`,
                                 wc_detalleordencobro.`importedoc`,
                                 wc_detalleordencobro.`numeroletra`,
                                 wc_detalleordencobro.`referencia`,
                                 wc_detalleordencobro.`numerounico`,
                                 wc_detalleordencobro.`recepcionLetras`,
                                 wc_detalleordencobro.`fechagiro`,
                                 wc_detalleordencobro.`fvencimiento`,
                                 wc_detalleordencobro.`gastosrenovacion`,
                                 wc_detalleordencobro.`recepcionLetras`,
                                 wc_detalleordencobro.`fechapago`,
                                 wc_detalleordencobro.`formacobro`,
                                 wc_detalleordencobro.`montoprotesto`,
                                 CASE  WHEN (SUBSTRING( wc_detalleordencobro.`numeroletra`,9,1)='R' OR wc_detalleordencobro.`renovado`>0) and wc_detalleordencobro.`protesto`='' THEN CONCAT('RENOV.',SUBSTRING( wc_detalleordencobro.`referencia`,10,1)) WHEN SUBSTRING( wc_detalleordencobro.`referencia`,9,1)='P' THEN 'PROTE.' WHEN SUBSTRING(wc_detalleordencobro.`numeroletra`,9,1)='' AND length(wc_detalleordencobro.`numeroletra`)>1 THEN 'REPRO.' WHEN wc_detalleordencobro.`tipogasto`=1 THEN 'GAST. RENOV.'  WHEN wc_detalleordencobro.`tipogasto`=2 THEN 'GAST. PROTE.' ELSE ' ' END as Proviene,
                                 CASE wc_detalleordencobro.`situacion` when '' then DATEDIFF(NOW(),wc_detalleordencobro.`fvencimiento`) else 0 end  as DifFechas", $condicion, "trim(wc_cliente.`razonsocial`) asc ,wc_cliente.`idCliente`,wc_ordenventa.`idordenventa`,wc_detalleordencobro.`fvencimiento`"
            );
        }
        if ($orderDireccion == "1") {
            $data = $this->leeRegistro(
                    "`wc_ordenventa` wc_ordenventa
                                INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.IdMoneda=wc_moneda.idmoneda
                                INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                                INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                                INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                                inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_actor.`apellidomaterno`,
                                 wc_ordenventa.`codigov`,
                                (wc_ordenventa.`importepagado`-wc_ordenventa.`importedevolucion`) as `importepagado`,
                                 wc_ordenventa.`idordenventa`,
                                 wc_ordenventa.`idtipocobranza`,
                                 wc_ordenventa.`fechadespacho`,
                                 wc_ordenventa.`fechavencimiento`,
                                 wc_ordenventa.`importedevolucion`,
                                 wc_moneda.`simbolo`,
                                 wc_cliente.`idcliente`,
                                 wc_cliente.`iddistrito`,
                                 wc_cliente.`apellido1`,
                                 wc_cliente.`apellido2`,
                                 replace(wc_cliente.direccion,'.',' ') as direccion,
                                 replace(replace(wc_cliente.direccion,'.',' '),'-',' ') as  direccionOrder1,
                                 wc_categoria.`idcategoria`,
                                 wc_categoria.`idpadrec`,
                                 wc_categoria.`codigoc`,
                                 wc_categoria.`nombrec`,
                                 wc_actor.`idactor`,
                                 wc_actor.`codigoa`,
                                 wc_cliente.`nombrecli`,
                                 wc_cliente.`razonsocial`,
                                 wc_zona.`idzona`,
                                 wc_zona.`nombrezona`,
                                 wc_actor.`nombres`,
                                 wc_actor.`apellidopaterno`,
                                 wc_actor.`apellidomaterno`,
                                 wc_ordencobro.`saldoordencobro`,
                                 wc_detalleordencobro.`iddetalleordencobro`,
                                 wc_detalleordencobro.`situacion`,
                                 wc_detalleordencobro.`saldodoc`,
                                 wc_detalleordencobro.`importedoc`,
                                 wc_detalleordencobro.`numeroletra`,
                                 wc_detalleordencobro.`referencia`,
                                 wc_detalleordencobro.`numerounico`,
                                 wc_detalleordencobro.`recepcionLetras`,
                                 wc_detalleordencobro.`fechagiro`,
                                 wc_detalleordencobro.`fvencimiento`,
                                 wc_detalleordencobro.`gastosrenovacion`,
                                 wc_detalleordencobro.`recepcionLetras`,
                                 wc_detalleordencobro.`fechapago`,
                                 wc_detalleordencobro.`formacobro`,
                                 wc_detalleordencobro.`montoprotesto`,
                                 CASE  WHEN (SUBSTRING( wc_detalleordencobro.`numeroletra`,9,1)='R' OR wc_detalleordencobro.`renovado`>0) and wc_detalleordencobro.`protesto`='' THEN CONCAT('RENOV.',SUBSTRING( wc_detalleordencobro.`referencia`,10,1)) WHEN SUBSTRING( wc_detalleordencobro.`referencia`,9,1)='P' THEN 'PROTE.' WHEN SUBSTRING(wc_detalleordencobro.`numeroletra`,9,1)='' AND length(wc_detalleordencobro.`numeroletra`)>1 THEN 'REPRO.' WHEN wc_detalleordencobro.`tipogasto`=1 THEN 'GAST. RENOV.'  WHEN wc_detalleordencobro.`tipogasto`=2 THEN 'GAST. PROTE.' ELSE ' ' END as Proviene,
                                 CASE wc_detalleordencobro.`situacion` when '' then DATEDIFF(NOW(),wc_detalleordencobro.`fvencimiento`) else 0 end  as DifFechas", $condicion, "trim(direccionOrder1) asc ,wc_cliente.`idCliente`,wc_ordenventa.`idordenventa`,wc_detalleordencobro.`fvencimiento`"
            );
        }
        return $data;
    }

    function reportclienteCobro($filtro = "", $idzona = "", $idcategoriaprincipal = "", $idcategoria = "", $idvendedor = "", $idtipocobranza = "", $fechainicio = "", $fechafinal = "", $situacion = "") {
        $condicion = "wc_ordenventa.`estado`=1 and wc_detalleordencobro.estado = 1";
        $condicion .= !empty($idzona) ? " and wc_zona.`idzona`='$idzona' " : "";
        $condicion .= !empty($idcategoriaprincipal) ? " and wc_categoria.`idpadrec`='$idcategoriaprincipal' " : "";
        $condicion .= !empty($idcategoria) ? " and wc_categoria.`idcategoria`='$idcategoria' " : "";
        $condicion .= !empty($idvendedor) ? " and wc_actor.`idactor`='$idvendedor' " : "";
        $condicion .= !empty($idtipocobranza) ? " and wc_ordenventa.`idtipocobranza`='$idtipocobranza' " : "";
        $condicion .= !empty($fechainicio) ? " and wc_detalleordencobro.`fvencimiento`>='$fechainicio' " : "";
        $condicion .= !empty($fechafinal) ? " and wc_detalleordencobro.`fvencimiento`<='$fechafinal' " : "";
        $condicion .= !empty($situacion) ? $situacion : "";
        $condicion .= !empty($filtro) ? " and  " . $filtro . " " : "";

        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                             INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                             INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.`idmoneda`=wc_moneda.`idmoneda`
                             INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                             INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                             INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                             inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado`=1
                             inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_actor.`apellidomaterno`,
                             wc_ordenventa.`codigov`,
                             wc_ordenventa.`idordenventa`,
                             wc_ordenventa.`idtipocobranza`,
                             wc_ordenventa.`fechadespacho`,
                             wc_ordenventa.`fechavencimiento`,
                             wc_ordenventa.`importepagado`,
                             wc_ordenventa.`importedevolucion`,
                             wc_ordenventa.`observaciones`,
                             wc_moneda.`simbolo` as simbolomoneda,
                             wc_cliente.`idcliente`,
                             wc_cliente.`iddistrito`,
                             wc_cliente.`apellido1`,
                             wc_cliente.`apellido2`,
                             wc_cliente.`direccion`,
                             wc_categoria.`idcategoria`,
                             wc_categoria.`idpadrec`,
                             wc_categoria.`codigoc`,
                             wc_categoria.`nombrec`,
                             wc_actor.`idactor`,
                             wc_actor.`codigoa`,
                             wc_cliente.`nombrecli`,
                             wc_cliente.`razonsocial`,
                             wc_zona.`idzona`,
                             wc_zona.`nombrezona`,
                             wc_actor.`nombres`,
                             wc_actor.`apellidopaterno`,
                             wc_ordencobro.`saldoordencobro`,
                             wc_detalleordencobro.`iddetalleordencobro`,
                             wc_detalleordencobro.`situacion`,
                             wc_detalleordencobro.`montoprotesto`,
                             wc_detalleordencobro.`numerounico`,
                             wc_detalleordencobro.`formacobro`,
                             wc_detalleordencobro.`saldodoc`,
                             wc_detalleordencobro.`importedoc`,
                             wc_detalleordencobro.`numeroletra`,
                             wc_detalleordencobro.`fechagiro`,
                             wc_detalleordencobro.`fvencimiento`,
                             wc_detalleordencobro.`referencia`,
                             wc_detalleordencobro.`gastosrenovacion`,
                             wc_detalleordencobro.`recepcionLetras`", $condicion, "wc_ordenventa.`codigov`, wc_detalleordencobro.`fechagiro` desc"
        );
        return $data;
    }
    
    function reportingresos($idordenventa = "", $idcliente = "", $idcobrador = "", $nrorecibo = "", $fechaInicio = "", $fechaFinal = "", $monto = "", $idtipocobro = "", $tipo = "") {
        $condicion = "i.`estado`=1 and i.`esvalidado`=1 ";
        $condicion .= !empty($idordenventa) ? " and i.`idordenventa`='$idordenventa' " : " ";
        $condicion .= !empty($idcliente) ? " and i.`idcliente`='$idcliente' " : " ";
        $condicion .= !empty($idcobrador) ? " and i.`idcobrador`='$idcobrador' " : " ";

        if (!empty($nrorecibo)) {
            if ($idtipocobro == 3 || $idtipocobro == 4) {
                $condicion .= " and i.`nrooperacion`='$nrorecibo' ";
            } else if ($idtipocobro == 2) {
                $condicion .= " and i.`nrodoc`='$nrorecibo' ";
            } else {
                $condicion .= " and i.`nrorecibo`='$nrorecibo' ";
            }
        }
        if (!empty($tipo)) {
            $condicion .= " and i.`tipo`='$tipo' ";
        }
        
        $condicion .= !empty($fechaInicio) ? " and i.`fcobro`>='$fechaInicio' " : " ";
        $condicion .= !empty($fechaFinal) ? " and i.`fcobro`<='$fechaFinal' " : " ";
        $condicion .= !empty($idtipocobro) ? " and i.`tipocobro`='$idtipocobro' " : " ";
        $condicion .= !empty($monto) ? $monto : "";

        $data = $this->leeRegistro(
                "wc_ingresos i
                                inner join wc_cliente c on i.`idcliente`=c.`idcliente`
                                inner join wc_actor a on a.`idactor`=i.`idcobrador`
                                ", "", $condicion, ""
        );
        return $data;
    }

    function totalVentasXdia($fecha, $moneda, $condicion) {
        $filtro = " ov.estado=1 and ov.vbcreditos=1 and ov.desaprobado='' and ov.fordenventa='$fecha' and ov.IdMoneda='$moneda'";

        if ($condicion == 1) {
            $filtro .= " and ov.es_contado='1' and ov.es_credito!='1' and ov.es_letras!='1' ";
        } elseif ($condicion == 2) {
            $filtro .= " and ov.es_credito='1' and ov.es_letras!='1' ";
        } elseif ($condicion == 3) {
            $filtro .= "  and ov.es_letras='1' and ov.tipo_letra=1 ";
        } elseif ($condicion == 4) {
            $filtro .= "  and ov.es_letras='1' and ov.tipo_letra=2 ";
        }

        $data = $this->leeRegistro(
                "wc_ordenventa ov
                            ", "SUM(ov.importeaprobado) as aprobado", $filtro, "", "group by ov.fordenventa order by ov.fordenventa asc"
        );
        if (count($data) == 0)
            return 0;

        return $data[0]['aprobado'];
    }

    function montoDespachado($fecha, $moneda) {
        $filtro .= !empty($fecha) ? "ov.fordenventa='$fecha' " : "";
        $filtro .= " and ov.IdMoneda='$moneda'";
        $data = $this->leeRegistro(
                "wc_ordenventa ov
                            ", "SUM(ov.importeov) as despachado", $filtro, "", "group by ov.fordenventa order by ov.fordenventa asc", "order by ov.fordenventa"
        );
        return $data[0]['despachado'];
    }

    function montoAprobado($fecha, $moneda) {
        $filtro .= !empty($fecha) ? "ov.fordenventa='$fecha' " : "";
        $filtro .= " and ov.IdMoneda='$moneda'";
        $data = $this->leeRegistro(
                "wc_ordenventa ov
                            ", "SUM(ov.importeaprobado) as aprobado", $filtro, "", "group by ov.fordenventa order by ov.fordenventa asc", "order by ov.fordenventa"
        );
        return $data[0]['aprobado'];
    }

    function reporteVentasXdia($desde, $hasta, $moneda) {
        $filtro = " ov.estado=1 and ov.vbcreditos=1 and ov.desaprobado='' ";

        $filtro .= !empty($desde) ? " and ov.fordenventa>='$desde' " : "";
        $filtro .= !empty($hasta) ? " and ov.fordenventa<='$hasta' " : "";
        $filtro .= " and ov.IdMoneda='$moneda'";
        $data = $this->leeRegistro(
                "wc_ordenventa ov
                            ", "SUM(ov.importeov) as despachado, SUM(ov.importeaprobado) as aprobado, ov.fordenventa, ov.faprobado", $filtro, "", "group by ov.fordenventa order by ov.fordenventa asc", "order by ov.fordenventa"
        );
        return $data;
    }

    function reporteVentas($txtFechaAprobadoInicio, $txtFechaAprobadoFinal, $txtFechaGuiadoInicio, $txtFechaGuiadoFin, $txtFechaDespachoInicio, $txtFechaDespachoFin, $txtFechaCanceladoInicio, $txtFechaCanceladoFin, $idOrdenVenta, $idCliente, $idVendedor, $idpadre, $idcategoria, $idzona, $condicion, $aprobados, $desaprobados, $pendiente, $idmoneda, $condVenta, $filtrocliente) {
        $filtro = " ov.estado=1 ";

        $filtro .= !empty($txtFechaGuiadoInicio) ? " and ov.fordenventa>='$txtFechaGuiadoInicio' " : "";
        $filtro .= !empty($txtFechaGuiadoFin) ? " and ov.fordenventa<='$txtFechaGuiadoFin' " : "";
        $filtro .= !empty($txtFechaDespachoInicio) ? " and ov.fechadespacho>='$txtFechaDespachoInicio' " : "";
        $filtro .= !empty($txtFechaDespachoFin) ? " and ov.fechadespacho<='$txtFechaDespachoFin' " : "";
        $filtro .= !empty($txtFechaCanceladoInicio) ? " and ov.fechaCancelado>='$txtFechaCanceladoInicio' " : "";
        $filtro .= !empty($txtFechaCanceladoFin) ? " and ov.fechaCancelado<='$txtFechaCanceladoFin' " : "";
        $filtro .= !empty($idOrdenVenta) ? " and ov.idordenventa='$idOrdenVenta' " : "";
        $filtro .= !empty($idCliente) ? " and c.idcliente='$idCliente' " : "";
        $filtro .= !empty($idVendedor) ? " and ov.idvendedor='$idVendedor' " : "";
        $filtro .= !empty($idpadre) ? " and ct.idpadrec='$idpadre' " : "";
        $filtro .= !empty($idcategoria) ? " and ct.idcategoria='$idcategoria' " : "";
        $filtro .= !empty($idzona) ? " and z.idzona='$idzona' " : "";
        $filtro .= !empty($condicion) ? $condicion : "";
        $filtro .= !empty($aprobados) ? " and ov.vbcreditos=1 " : "";
        $filtro .= !empty($desaprobados) ? " and ov.desaprobado='1' " : "";
        $filtro .= !empty($pendiente) ? " and ov.situacion='Pendiente' " : "";
        if ($condVenta != 0) {
            if ($condVenta == 1)
                $filtro .= !empty($condVenta) ? " and ov.es_contado=1 and ov.es_credito!=1 and ov.es_letras!=1 " : "";
            else if ($condVenta == 2)
                $filtro .= !empty($condVenta) ? " and ov.es_credito==1 and ov.es_letras!=1 " : "";
            else if ($condVenta == 3)
                $filtro .= !empty($condVenta) ? " and ov.es_letras=1 " : "";
        }
        $groupby = "group by ov.idordenventa";
        if ($filtrocliente == 1)
            $groupby = "group by c.idcliente, ov.idordenventa having count(ov.idordenventa) <= 1";
        $groupby .= " order by a.apellidopaterno asc,a.apellidomaterno asc, a.nombres asc";
        $data = $this->leeRegistro(
                "wc_ordenventa ov
                                inner join wc_clientezona cz ON cz.idclientezona=ov.idclientezona
                                inner join wc_cliente c ON c.idcliente=cz.idcliente
                                inner join wc_zona z ON z.idzona=cz.idzona
                                inner join wc_categoria ct ON ct.idcategoria=z.idcategoria
                                inner join wc_actor a ON a.idactor=ov.idvendedor
                                inner join wc_tipocambio tc On ov.fordenventa=tc.fechatc
                                inner join wc_moneda mn ON ov.idmoneda=mn.idmoneda
                                ", "*,ov.idordenventa, ov.situacion as estadoov,mn.simbolo,tc.venta,tc.compra,ov.MontoTipoCambioVigente,
                                case " . $idmoneda . " when 1 then ov.importeov*MontoTipoCambioVigente when 2 then ov.importeov*tc.compra end as importeov1,
                                z.nombrezona as nombrezonax, c.direccion as dir, c.direccion_despacho_cliente as dirdc", $filtro, "", $groupby
        ); //
        return $data;
    }

    function reporteProductoAgotados($idLinea, $idSubLinea, $idMarca, $idAlmacen, $idProducto, $fechaInicio, $fechaFinal) {
        $filtro = " p.estado=1 and p.stockactual<=0 ";
        $filtro .= !empty($idLinea) ? " and li.idpadre='$idLinea' " : "";
        $filtro .= !empty($idSubLinea) ? " and p.idlinea='$idSubLinea' " : "";
        $filtro .= !empty($idMarca) ? " and p.idmarca='$idMarca' " : "";
        $filtro .= !empty($idAlmacen) ? " and p.idalmacen='$idAlmacen' " : "";
        $filtro .= !empty($idProducto) ? " and p.idproducto='$idProducto' " : "";
        $filtro .= !empty($fechaInicio) ? " and DATE(p.fechaagotado)>='$fechaInicio' " : "";
        $filtro .= !empty($fechaFinal) ? " and DATE(p.fechaagotado)<='$fechaFinal' " : "";

        $data = $this->leeRegistro(
                "wc_producto p
                                left join wc_almacen a on p.idalmacen=a.idalmacen
                                left join wc_linea li on li.idlinea=p.idlinea
                                left join wc_marca m on m.idmarca=p.idmarca
                                ", "", $filtro, "", ""
        );
        return $data;
    }

    function reporteVendedores($idVendedor, $fechaInicio, $fechaFinal) {

        $filtro .= !empty($fechaInicio) ? " ov.fordenventa>='$fechaInicio' " : "";
        $filtro .= !empty($fechaFinal) ? " and ov.fordenventa<='$fechaFinal' " : "";
        $filtro .= !empty($idVendedor) ? " and ov.idvendedor='$idVendedor' " : "";
        $data = $this->leeRegistro(
                "wc_ordenventa ov inner join wc_actor a
                     on a.idactor=ov.idvendedor", "a.idactor,a.nombres,a.apellidopaterno,apellidomaterno", $filtro, "", "group by a.idactor order by a.nombres asc, a.apellidopaterno asc,a.apellidomaterno asc");
        return $data;
    }

    function reporteProductoVendidos($idLinea, $idSubLinea, $idMarca, $idAlmacen, $idProducto, $fechaInicio, $fechaFinal) {
        $filtro = " p.estado=1 and ov.esguiado=1 ";
        $filtro .= !empty($idLinea) ? " and li.idpadre='$idLinea' " : "";
        $filtro .= !empty($idSubLinea) ? " and p.idlinea='$idSubLinea' " : "";
        $filtro .= !empty($idMarca) ? " and p.idmarca='$idMarca' " : "";
        $filtro .= !empty($idAlmacen) ? " and p.idalmacen='$idAlmacen' " : "";
        $filtro .= !empty($idProducto) ? " and p.idproducto='$idProducto' " : "";
        $filtro .= !empty($fechaInicio) ? " and ov.fordenventa>='$fechaInicio' " : "";
        $filtro .= !empty($fechaFinal) ? " and ov.fordenventa<='$fechaFinal' " : "";

        $data = $this->leeRegistro(
                "wc_producto p
                                left join wc_almacen a on p.idalmacen=a.idalmacen
                                left join wc_linea li on li.idlinea=p.idlinea
                                left join wc_marca m on m.idmarca=p.idmarca
                                inner join wc_detalleordenventa dov on dov.idproducto=p.idproducto
                                inner join wc_ordenventa ov on ov.idordenventa=dov.idordenventa
                                ", "*,sum(dov.cantdespacho) as cantidadvendida", $filtro, "", "group by dov.idproducto order by cantidadvendida desc"
        );
        return $data;
    }

    function UpdateStockProducto($conteo1, $idproducto) {
        $data = array('conteo1' => $conteo1);
        $exito = $this->actualizaRegistro($this->tablaproducto, $data, "idproducto=$idproducto");
        return $exito;
    }

    function reporteInventario($idInventario = "", $idBloque = "", $idProducto = "", $lstStock = "") {
        $filtro = " di.estado=1";
        if (!empty($idInventario)) {
            $filtro .= !empty($idInventario) ? " and di.idinventario='$idInventario' " : "";
            $filtro .= !empty($idBloque) ? " and di.idbloque='$idBloque' " : "";
            $filtro .= !empty($idProducto) ? " and di.idproducto='$idProducto' " : "";
            if ($lstStock == 1) {
                $filtro .= " and p.stockactual>0 ";
            } else if ($lstStock == 2) {
                $filtro .= " and p.stockactual<=0 ";
            }
            $data = $this->leeRegistro(
                    "wc_detalleinventario di
                                inner join wc_producto p on di.idproducto=p.idproducto
                                left join wc_unidadmedida um on um.idunidadmedida = p.unidadmedida and um.estado = 1
                                inner join wc_inventario i on i.idinventario=di.idinventario
                                inner join wc_bloques b on b.idbloque=di.idbloque
                                ", "*,um.codigo as codigoum, b.codigo as 'descripcionbloque_info',p.stockactual as stockactual_wc_producto,p.stockdisponible as stockdisponible_wc_producto,di.fechacreacion as 'reg_det_inv',di.descripcion,di.horainicio,di.horatermino,di.stockactual as stockinventario,p.cifventasdolares as precioinventario", $filtro, "", "order by di.idbloque,di.iddetalleinventario"
            );
        }
        return $data;
    }

    function dataRolesPorBloque($idInventario, $idBloque) {

        $sql = "select * from wc_inventarioroles where idinventario='" . $idInventario . "' and idbloque='" . $idBloque . "';";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function reporteOrdenCompraRevision($idOrdenCompra, $idProducto = '') {
        $condicion = "oc.estado=1 and doc.estado=1 and oc.registrado=1  ";
        if (!empty($idProducto) && empty($idOrdenCompra)) {
            $condicion .= " and  oc.idordencompra=(select max(oc.idordencompra) from wc_ordencompra oc inner join wc_detalleordencompra doc on oc.idordencompra=doc.idordencompra where doc.idproducto='$idProducto' and oc.estado=1) ";
        } else if (!empty($idProducto) && !empty($idOrdenCompra)) {
            $condicion .= " and doc.idproducto='$idProducto' and oc.idordencompra='$idOrdenCompra' ";
        } else if (empty($idProducto) && !empty($idOrdenCompra)) {

            $condicion .= !empty($idOrdenCompra) ? " and oc.idordencompra='$idOrdenCompra' " : "";
        }
        $data = $this->leeRegistro("wc_ordencompra as oc
                                                                 inner join wc_detalleordencompra as doc on oc.idordencompra=doc.idordencompra
                                                                 inner join wc_producto as p on p.idproducto=doc.idproducto
                                                                 left join wc_unidadmedida as u on u.idunidadmedida=p.unidadmedida ", "", $condicion, "", "order by oc.idordencompra,doc.iddetalleordencompra");
        return $data;
    }

    function historialProducto($idProducto) {
        $condicion = "oc.estado=1 and doc.estado=1 and oc.registrado=1  ";
        $condicion .= !empty($idProducto) ? " and doc.idproducto='$idProducto' " : "";
        $data = $this->leeRegistro("wc_ordencompra as oc
                                                                 inner join wc_detalleordencompra as doc on oc.idordencompra=doc.idordencompra
                                                                 inner join wc_producto as p on p.idproducto=doc.idproducto
                                                                 left join wc_unidadmedida as u on u.idunidadmedida=p.unidadmedida ", "", $condicion, "", "order by oc.idordencompra");
        return $data;
    }

    function carteraClientes($idLinea, $idZona, $idPadre, $idCategoria, $idVendedor, $idDepartamento, $idProvincia, $idDistrito, $fechaInicio, $fechaFin) {
        $sql = "Select razonsocial from wc_cliente c
                inner join wc_clientelinea cl On c.idcliente=cl.idcliente
                inner join wc_linea lin On cl.idlinea=lin.idlinea
                Where cl.idlinea=191
                ";
        $condicion = "c.estado=1  ";
        $condicion .= !empty($idLinea) ? " and cl.idlinea='$idLinea' " : "";
        $condicion .= !empty($idZona) ? " and c.zona='$idZona' " : "";
        $condicion .= !empty($idPadre) ? " and ct.idcategoria='$idPadre' " : "";
        $condicion .= !empty($idCategoria) ? " and ct.idpadrec='$idCategoria' " : "";
        $condicion .= !empty($idVendedor) ? " and cv.idvendedor='$idVendedor' " : "";
        $condicion .= !empty($idDepartamento) ? " and dp.iddepartamento='$idDepartamento' " : "";
        $condicion .= !empty($idProvincia) ? " and pv.idprovincia='$idProvincia' " : "";
        $condicion .= !empty($idDistrito) ? " and d.iddistrito='$idDistrito' " : "";
        $condicion .= (!empty($fechaInicio) && !empty($fechaFin)) ? " and ov.fordenventa between '$fechaInicio' and  '$fechaFin'" : "";


        $data = $this->leeRegistro("wc_cliente as c
                                inner join wc_clientevendedor as cv on cv.idcliente=c.idcliente
                                inner join wc_actor as a  on a.idactor=cv.idvendedor
                                inner join wc_clientelinea cl On c.idcliente=cl.idcliente
                                inner join wc_linea lin On cl.idlinea=lin.idlinea
                                inner join wc_distrito as d on d.iddistrito=c.iddistrito
                                inner join wc_provincia as pv on pv.idprovincia=d.idprovincia
                                inner join wc_departamento as dp on dp.iddepartamento=pv.iddepartamento
                                left join wc_zona as z on z.idzona=c.zona
                                left join wc_ordenventa as ov on ov.idordenventa=c.idultimaorden
                                inner join wc_categoria as ct on ct.idcategoria=z.idcategoria", "lin.nomlin,z.nombrezona,z.idzona,c.idcliente,concat(c.telefono,' ',c.celular) as telefono,c.email,c.ruc,c.razonsocial,c.direccion,d.nombredistrito,
                                dp.nombredepartamento,a.nombres,a.apellidomaterno,a.apellidopaterno,ov.codigov,ov.fordenventa,ov.importeov ", $condicion, "", "order by c.direccion,lin.nomlin,ct.idpadrec,ct.idcategoria,z.idzona,TRIM(c.razonsocial) asc");
        return $data;
    }

    function historialVentasxProducto($idProducto, $idVendedor, $idCliente) {

        $condicion = "ov.estado=1 and dov.estado=1 and vbcreditos=1 ";
        $condicion .= !empty($idProducto) ? " and dov.idproducto='$idProducto' " : "";
        $condicion .= !empty($idVendedor) ? " and ov.idvendedor='$idVendedor' " : "";
        $condicion .= !empty($idCliente) ? " and ov.idcliente='$idCliente' " : "";

        $data = $this->leeRegistro("wc_ordenventa ov
                                INNER JOIN wc_detalleordenventa dov ON ov.idordenventa=dov.idordenventa
                                INNER JOIN wc_producto p  ON p.idproducto=dov.idproducto
                                INNER JOIN wc_cliente c ON ov.idcliente=c.idcliente
                                INNER JOIN wc_actor a ON ov.idvendedor=a.idactor
                                LEFT JOIN wc_unidadmedida um ON p.unidadmedida=um.idunidadmedida
                                LEFT JOIN wc_almacen al ON ov.idalmacen=al.idalmacen", "p.nompro,p.codigopa,a.nombres,a.apellidopaterno,a.apellidomaterno,codigoa,c.razonsocial,ov.fordenventa,ov.idMoneda,dov.cantdespacho,um.nombre as nombremedida ,ov.codigov,dov.idproducto,al.codigoalmacen,ov.importeov,dov.preciolista,dov.descuentoaprobadovalor,dov.descuentoaprobadotexto,dov.preciofinal", $condicion, "", "order by ov.fordenventa,ov.idordenventa ");
        return $data;
    }

    function cobranzaxEmpresa($filtro = "", $idzona = "", $idcategoriaprincipal = "", $idcategoria = "", $idvendedor = "", $idtipocobranza = "", $fechainicio = "", $fechafinal = "", $situacion = "", $idAlmacen = "") {
        $condicion = "wc_ordenventa.`estado`=1 and wc_detalleordencobro.`estado`=1 and wc_documento.`esAnulado`=0 and (wc_documento.`nombredoc`=2 or wc_documento.`nombredoc`=1) ";

        $condicion .= !empty($idzona) ? " and wc_zona.`idzona`='$idzona' " : "";
        $condicion .= !empty($idcategoriaprincipal) ? " and wc_categoria.`idpadrec`='$idcategoriaprincipal' " : "";
        $condicion .= !empty($idcategoria) ? " and wc_categoria.`idcategoria`='$idcategoria' " : "";
        $condicion .= !empty($idvendedor) ? " and wc_actor.`idactor`='$idvendedor' " : "";
        $condicion .= !empty($idtipocobranza) ? " and wc_ordenventa.`idtipocobranza`='$idtipocobranza' " : "";
        $condicion .= !empty($fechainicio) ? " and wc_detalleordencobro.`fvencimiento`>='$fechainicio' " : "";
        $condicion .= !empty($fechafinal) ? " and wc_detalleordencobro.`fvencimiento`<='$fechafinal' " : "";
        $condicion .= !empty($situacion) ? $situacion : "";
        $condicion .= !empty($idAlmacen) ? " and wc_ordenventa.`idalmacen`='$idAlmacen' " : "";
        $condicion .= !empty($filtro) ? " and  " . $filtro . " " : "";

        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                             INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                             LEFT JOIN  `wc_documento` wc_documento ON wc_ordenventa.`idordenventa`=wc_documento.`idordenventa`
                             INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                             INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                             LEFT JOIN `wc_almacen` wc_almacen ON wc_ordenventa.`idalmacen` = wc_almacen.`idalmacen`
                             INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                             inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                             inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_actor.`apellidomaterno`,
                             wc_ordenventa.`codigov`,
                             wc_ordenventa.`idordenventa`,
                             wc_ordenventa.`idtipocobranza`,
                             wc_ordenventa.`fechadespacho`,
                             wc_ordenventa.`fechavencimiento`,
                             wc_ordenventa.`importepagado`,
                             wc_ordenventa.`importedevolucion`,
                             wc_ordenventa.`direccion_envio`,
                             wc_ordenventa.`es_contado`,
                             wc_ordenventa.`es_credito`,
                             wc_ordenventa.`es_letras`,
                             wc_cliente.`idcliente`,
                             wc_cliente.`iddistrito`,
                             wc_cliente.`apellido1`,
                             wc_cliente.`apellido2`,
                             wc_cliente.`direccion`,
                             wc_categoria.`idcategoria`,
                             wc_categoria.`idpadrec`,
                             wc_categoria.`codigoc`,
                             wc_categoria.`nombrec`,
                             wc_actor.`idactor`,
                             wc_documento.`porcentajefactura`,
                             wc_documento.`montofacturado`,
                             wc_documento.`nombredoc`,
                             wc_almacen.`codigoalmacen`,
                             wc_almacen.`nomalm`,
                             wc_cliente.`nombrecli`,
                             wc_cliente.`razonsocial`,
                             wc_zona.`idzona`,
                             wc_zona.`nombrezona`,
                             wc_actor.`codigoa`,
                             wc_actor.`nombres`,
                             wc_actor.`apellidopaterno`,
                             wc_ordencobro.`saldoordencobro`,
                             wc_detalleordencobro.`situacion`,
                             wc_detalleordencobro.`formacobro`,
                             wc_detalleordencobro.`saldodoc`,
                             wc_detalleordencobro.`importedoc`,
                             wc_detalleordencobro.`numeroletra`,
                             wc_detalleordencobro.`fechagiro`,
                             wc_detalleordencobro.`fvencimiento`,
                             wc_detalleordencobro.`gastosrenovacion`,
                             wc_detalleordencobro.`recepcionLetras`", $condicion, "wc_ordenventa.`fechavencimiento`,wc_ordenventa.`idordenventa` desc"
        );
        return $data;
    }

    function rankingVendedor($txtFechaAprobadoInicio, $txtFechaAprobadoFinal, $txtFechaGuiadoInicio, $txtFechaGuiadoFin, $txtFechaDespachoInicio, $txtFechaDespachoFin, $txtFechaCanceladoInicio, $txtFechaCanceladoFin, $idOrdenVenta, $idCliente, $idVendedor, $idpadre, $idcategoria, $idzona, $condicion, $aprobados, $desaprobados, $pendiente, $idmoneda) {
        //$filtro=" ov.estado=1 and ov.importedevolucion < ov.importeov ";
        $filtro = " ov.estado=1 and og.estado = 1 ";
        $filtro .= !empty($txtFechaAprobadoInicio) ? " and ov.faprobado>='$txtFechaAprobadoInicio' " : "";
        $filtro .= !empty($txtFechaAprobadoFinal) ? " and ov.faprobado<='$txtFechaAprobadoFinal' " : "";
        $filtro .= !empty($txtFechaGuiadoInicio) ? " and ov.fordenventa>='$txtFechaGuiadoInicio' " : "";
        $filtro .= !empty($txtFechaGuiadoFin) ? " and ov.fordenventa<='$txtFechaGuiadoFin' " : "";
        $filtro .= !empty($txtFechaDespachoInicio) ? " and ov.fechadespacho>='$txtFechaDespachoInicio' " : "";
        $filtro .= !empty($txtFechaDespachoFin) ? " and ov.fechadespacho<='$txtFechaDespachoFin' " : "";
        $filtro .= !empty($txtFechaCanceladoInicio) ? " and ov.fechaCancelado>='$txtFechaCanceladoInicio' " : "";
        $filtro .= !empty($txtFechaCanceladoFin) ? " and ov.fechaCancelado<='$txtFechaCanceladoFin' " : "";
        $filtro .= !empty($idOrdenVenta) ? " and ov.idordenventa='$idOrdenVenta' " : "";
        $filtro .= !empty($idCliente) ? " and c.idcliente='$idCliente' " : "";
        $filtro .= !empty($idVendedor) ? " and ov.idvendedor='$idVendedor' " : "";
        $filtro .= !empty($idpadre) ? " and ct.idpadrec='$idpadre' " : "";
        $filtro .= !empty($idcategoria) ? " and ct.idcategoria='$idcategoria' " : "";
        $filtro .= !empty($idzona) ? " and z.idzona='$idzona' " : "";
        $filtro .= !empty($condicion) ? $condicion : "";
        $filtro .= !empty($aprobados) ? " and ov.vbcreditos=1 " : "";
        $filtro .= !empty($desaprobados) ? " and ov.desaprobado='1' " : "";
        $filtro .= !empty($pendiente) ? " and ov.situacion='Pendiente' " : "";
        $data = $this->leeRegistro(
                "wc_ordenventa ov
                                inner join wc_clientezona cz ON cz.idclientezona=ov.idclientezona
                                inner join wc_cliente c ON c.idcliente=cz.idcliente
                                inner join wc_zona z ON z.idzona=cz.idzona
                                inner join wc_categoria ct ON ct.idcategoria=z.idcategoria
                                inner join wc_ordengasto og on og.idordenventa = ov.idordenventa
                                inner join wc_actor a ON a.idactor=ov.idvendedor
                                inner join wc_tipocambio tc ON ov.fordenventa=tc.fechatc
                                inner join wc_moneda mn ON ov.IdMoneda=mn.idmoneda
                                ", "ov.idordenventa, ov.codigov,ov.idvendedor,a.nombres,a.apellidopaterno,a.apellidomaterno,ov.situacion as estadoov,ov.es_contado,ov.es_credito,ov.es_letras,
                                ov.tipo_letra, tc.venta,mn.simbolo,sum(og.importegasto) as total, ov.fordenventa, ov.IdMoneda,
                                case concat(" . $idmoneda . ",ov.IdMoneda) when 11 then ov.importepagado when 12 then ov.importepagado*tc.venta when 21 then ov.importepagado/tc.venta when 22 then ov.importepagado end as importepagado,
                                case concat(" . $idmoneda . ",ov.IdMoneda) when 11 then ov.importedevolucion when 12 then ov.importedevolucion*tc.venta when 21 then ov.importedevolucion/tc.venta when 22 then ov.importedevolucion end as importedevolucion,
                                case concat(" . $idmoneda . ",ov.IdMoneda) when 11 then ov.importeov when 12 then ov.importeov*tc.venta when 21 then ov.importeov/tc.venta when 22 then ov.importeov end as importeov
                                ", $filtro, "", "group by ov.idordenventa order by ov.idvendedor"
        );
        return $data;
    }

    function cuadroavance($txtFechaAprobadoInicio, $txtFechaAprobadoFinal, $txtFechaGuiadoInicio, $txtFechaGuiadoFin, $txtFechaDespachoInicio, $txtFechaDespachoFin, $txtFechaCanceladoInicio, $txtFechaCanceladoFin, $idOrdenVenta, $idCliente, $idVendedor, $idpadre, $idcategoria, $idzona, $condicion, $aprobados, $desaprobados, $pendiente, $idmoneda) {
        //$filtro=" ov.estado=1 and ov.importedevolucion < ov.importeov ";
        $filtro = " ov.estado=1 and og.estado = 1 ";
        $filtro .= !empty($txtFechaGuiadoInicio) ? " and ov.fordenventa>='$txtFechaGuiadoInicio' " : "";
        $filtro .= !empty($txtFechaGuiadoFin) ? " and ov.fordenventa<='$txtFechaGuiadoFin' " : "";
        $filtro .= !empty($idVendedor) ? " and ov.idvendedor='$idVendedor' " : "";
        $filtro .= !empty($aprobados) ? " and ov.vbcreditos=1 " : "and ov.vbcreditos=1";
        $filtro .= !empty($txtFechaAprobadoInicio) ? " and ov.faprobado>='$txtFechaAprobadoInicio' " : "";
        $filtro .= !empty($txtFechaAprobadoFinal) ? " and ov.faprobado<='$txtFechaAprobadoFinal' " : "";
        $filtro .= !empty($txtFechaDespachoInicio) ? " and ov.fechadespacho>='$txtFechaDespachoInicio' " : "";
        $filtro .= !empty($txtFechaDespachoFin) ? " and ov.fechadespacho<='$txtFechaDespachoFin' " : "";
        $filtro .= !empty($txtFechaCanceladoInicio) ? " and ov.fechaCancelado>='$txtFechaCanceladoInicio' " : "";
        $filtro .= !empty($txtFechaCanceladoFin) ? " and ov.fechaCancelado<='$txtFechaCanceladoFin' " : "";
        $filtro .= !empty($idOrdenVenta) ? " and ov.idordenventa='$idOrdenVenta' " : "";
        $filtro .= !empty($idCliente) ? " and c.idcliente='$idCliente' " : "";

        $filtro .= !empty($idpadre) ? " and ct.idpadrec='$idpadre' " : "";
        $filtro .= !empty($idcategoria) ? " and ct.idcategoria='$idcategoria' " : "";
        $filtro .= !empty($idzona) ? " and z.idzona='$idzona' " : "";
        $filtro .= !empty($condicion) ? $condicion : "";
//                $filtro.=!empty($aprobados)?" and ov.vbcreditos=1 ":"";
        $filtro .= !empty($desaprobados) ? " and ov.desaprobado='1' " : "";
        $filtro .= !empty($pendiente) ? " and ov.situacion='Pendiente' " : "";
        $data = $this->leeRegistro(
                "wc_ordenventa ov
                                inner join wc_clientezona cz ON cz.idclientezona=ov.idclientezona
                                inner join wc_cliente c ON c.idcliente=cz.idcliente
                                inner join wc_zona z ON z.idzona=cz.idzona
                                inner join wc_categoria ct ON ct.idcategoria=z.idcategoria
                                inner join wc_ordengasto og on og.idordenventa = ov.idordenventa
                                inner join wc_actor a ON a.idactor=ov.idvendedor
                                inner join wc_tipocambio tc ON ov.fordenventa=tc.fechatc
                                inner join wc_moneda mn ON ov.IdMoneda=mn.idmoneda
                                ", "ov.idordenventa, ov.codigov,ov.idvendedor,a.nombres,a.apellidopaterno,a.apellidomaterno,ov.situacion as estadoov,ov.es_contado,ov.es_credito,ov.es_letras,
                                ov.tipo_letra, tc.venta,mn.simbolo,sum(og.importegasto) as total,
                                case concat(" . $idmoneda . ",ov.IdMoneda) when 11 then ov.importepagado when 12 then ov.importepagado*tc.venta when 21 then ov.importepagado/tc.venta when 22 then ov.importepagado end as importepagado,
                                case concat(" . $idmoneda . ",ov.IdMoneda) when 11 then ov.importedevolucion when 12 then ov.importedevolucion*tc.venta when 21 then ov.importedevolucion/tc.venta when 22 then ov.importedevolucion end as importedevolucion,
                                case concat(" . $idmoneda . ",ov.IdMoneda) when 11 then ov.importeov when 12 then ov.importeov*tc.venta when 21 then ov.importeov/tc.venta when 22 then ov.importeov end as importeov
                                ", $filtro, "", "group by ov.idordenventa order by ov.idvendedor"
        );
        return $data;
    }

    function reporteFacturacion($txtFechaInicio, $txtFechaFinal, $idVendedor, $idOrdenVenta, $idCliente, $idTipodoc, $lstSituacion, $orden) {
        $filtro = " ov.estado=1 and d.estado=1 and d.esanulado!=1 and (nombredoc=1 or nombredoc=2) ";
        $filtro .= !empty($txtFechaInicio) ? " and d.fechadoc>='$txtFechaInicio' " : "";
        $filtro .= !empty($txtFechaFinal) ? " and d.fechadoc<='$txtFechaFinal' " : "";
        $filtro .= !empty($idVendedor) ? " and ov.idvendedor='$idVendedor' " : "";
        $filtro .= !empty($idOrdenVenta) ? " and ov.idordenventa='$idOrdenVenta' " : "";
        $filtro .= !empty($idCliente) ? " and ov.idcliente='$idCliente' " : "";
        $filtro .= !empty($idTipodoc) ? " and d.nombredoc='$idTipodoc' " : "";
        $filtro .= !empty($lstSituacion) ? " and ov.situacion='$lstSituacion' " : "";
        $order = !empty($orden) ? (" order by " . $orden . "") : "";

        $data = $this->leeRegistro(
                "wc_ordenventa ov
                                inner join wc_cliente c ON c.idcliente=ov.idcliente
                                inner join wc_actor a ON a.idactor=ov.idvendedor
                                inner join wc_almacen al ON al.idalmacen=ov.idalmacen
                                left join wc_documento d ON ov.idordenventa=d.idordenventa
                                ", "ov.codigov,d.serie,d.numdoc,d.fechadoc,al.codigoalmacen,d.nombredoc,d.montofacturado,d.porcentajefactura,d.modofactura,c.razonsocial,a.nombres,a.apellidopaterno,a.apellidomaterno,ov.situacion", $filtro, "", $order
        );
        return $data;
    }

    function reporteKardexProduccion($txtFechaInicio, $txtFechaFinal, $idProducto, $idTipoMovimiento, $idTipoOperacion) {
        $filtro = "  dm.estado=1 and m.estado=1 ";
        $filtro .= !empty($txtFechaInicio) ? " and m.fechamovimiento>='$txtFechaInicio' " : "";
        $filtro .= !empty($txtFechaFinal) ? " and m.fechamovimiento<='$txtFechaFinal' " : "";
        $filtro .= !empty($idProducto) ? " and dm.idproducto='$idProducto' " : "";
        $filtro .= !empty($idTipoOperacion) ? " and m.conceptomovimiento='$idTipoOperacion' " : "";
        $filtro .= !empty($idTipoMovimiento) ? " and m.tipomovimiento='$idTipoMovimiento' " : "";
        $order = "Order By m.fechamovimiento,m.idmovimiento asc";

        $data = $this->leeRegistro(
                "wc_detallemovimiento dm
                                Inner Join wc_movimiento m On dm.idmovimiento=m.idmovimiento
                                Left Join wc_ordenventa ov On ov.idordenventa=m.idordenventa
                                Left Join wc_ordencompra oc On oc.idordencompra=m.idordencompra
                                Inner Join wc_movimientotipo mt On m.tipomovimiento=mt.idmovimientotipo
                                left Join wc_cliente c On ov.idcliente=c.idcliente
                                left Join wc_proveedor p On oc.idproveedor=p.idproveedor
                                left Join wc_devolucion d On m.iddevolucion=d.iddevolucion
                                left join wc_tipooperacion tio On tio.idtipooperacion=m.conceptomovimiento
                                ", "ov.idordenventa, ov.codigov, ov.importeov, oc.codigooc,oc.idordencompra,m.fechamovimiento as Fecha,mt.nombre as 'Tipo Movimiento',tio.nombre as 'Concepto Movimiento',
                                CASE WHEN ov.codigov<>'Null' Then c.razonsocial WHEN oc.codigooc<>'Null' Then p.razonsocialp Else 'Mov. Interno' END as 'Razon Social',
                                CASE WHEN m.iddevolucion<>0 THEN 'Devolucion' ELSE ' ' END as Devolucion,
                                dm.pu as 'Precio',dm.cantidad,ROUND(dm.stockactual,0) as Saldo,dm.importe as 'Monto'", $filtro, "", $order
        );
        return $data;
    }

    function reporteKardexProduccionRepuesto($txtFechaInicio, $txtFechaFinal, $idProducto, $idTipoMovimiento, $idTipoOperacion) {
        $filtro = "  dm.estado=1 and m.estado=1 ";
        $filtro .= !empty($txtFechaInicio) ? " and m.fechamovimiento>='$txtFechaInicio' " : "";
        $filtro .= !empty($txtFechaFinal) ? " and m.fechamovimiento<='$txtFechaFinal' " : "";
        $filtro .= !empty($idProducto) ? " and dm.idproducto='$idProducto' " : "";
        $filtro .= !empty($idTipoOperacion) ? " and m.conceptomovimiento='$idTipoOperacion' " : "";
        $filtro .= !empty($idTipoMovimiento) ? " and m.tipomovimiento='$idTipoMovimiento' " : "";
        $order = "Order By m.fechamovimiento,m.idrepuesto asc";

        $data = $this->leeRegistro(
                "wc_detallerepuesto dm 
                INNER JOIN wc_repuesto m ON dm.idrepuesto=m.idrepuesto 
                LEFT JOIN wc_ordencompra oc ON oc.idordencompra=m.idordencompra 
                INNER JOIN wc_movimientotipo mt ON m.tipomovimiento=mt.idmovimientotipo 
                LEFT JOIN wc_proveedor p ON oc.idproveedor=p.idproveedor 
                                ", "oc.codigooc ,p.razonsocialp, oc.idordencompra,m.fechamovimiento AS Fecha,mt.nombre AS 'Tipo Movimiento', dm.observacion,
                                dm.pu AS 'Precio',dm.cantidad,ROUND(dm.stockactual,0) AS Saldo,dm.importe AS 'Monto' ", $filtro, "", $order
        );
        return $data;
    }

    function letrasXordenventa($numeroLetra) {
        $filtro = "doc.estado=1 and oc.estado=1 and doc.numeroletra='$numeroLetra'";

        $data = $this->leeRegistro("wc_detalleordencobro doc
                inner join wc_ordencobro  oc on doc.idordencobro=oc.idordencobro
                inner join wc_ordenventa ov on oc.idordenventa=ov.idordenventa
                inner join wc_cliente cl on ov.idcliente=cl.idcliente
                inner join wc_moneda mo on  ov.idmoneda=mo.idmoneda", "ov.codigov, doc.numeroletra", $filtro, "", "");
        return $data;
    }

    function letrasxfirmar($numeroLetra, $idordenventa = "") {

        $filtro = " doc.situacion='' and doc.formacobro=3 and doc.estado=1 and oc.estado=1";
        $filtro .= !empty($numeroLetra) ? " and doc.numeroletra='$numeroLetra' " : "";
        if (!empty($idordenventa)) {
            $filtro .= " and ov.idordenventa='$idordenventa'";
        }
        $data = $this->leeRegistro("wc_detalleordencobro doc
                inner join wc_ordencobro  oc on doc.idordencobro=oc.idordencobro
                inner join wc_ordenventa ov on oc.idordenventa=ov.idordenventa
                inner join wc_cliente cl on ov.idcliente=cl.idcliente
                inner join wc_moneda mo on  ov.idmoneda=mo.idmoneda
                inner join wc_distrito dist on dist.iddistrito = cl.iddistrito
                inner join wc_provincia pro on pro.idprovincia = dist.idprovincia
                inner join wc_departamento dep on dep.iddepartamento = pro.iddepartamento", "cl.razonsocial, ov.tipodoccli,cl.ruc,cl.nombrecli,cl.apellido1,cl.apellido2,cl.dni,doc.numeroletra, doc.fvencimiento,doc.iddetalleordencobro,doc.importedoc,doc.recepcionLetras,mo.simbolo,mo.idmoneda,dep.iddepartamento", $filtro, "", "");
        return $data;
    }

    function updateCampo($numeroLetra, $val) {
        $filtro = " numeroletra=" . $numeroLetra;
        $estado = $this->leeRegistro($this->tablaDetalleCobro, "esplanilla,iddetalleordencobro", $filtro, "", "");
        $valor = $estado[0]['esplanilla'];
        $iddetallecobro = $estado[0]['iddetalleordencobro'];
        $sql = "Update  " . $this->tablaDetalleCobro . " Set esplanilla=" . $val . "  Where iddetalleordencobro=" . $iddetallecobro;
        $exito = $this->EjecutaConsultaBoolean($sql);
        return $exito;
    }

    function reporteVentasxMes($txtFechaInicio, $txtFechaFinal, $idtipodocumento) {
        $filtro = " ov.estado=1 and d.estado=1 and d.esanulado!=1";
        $filtro .= !empty($txtFechaInicio) ? " and d.fechadoc>='$txtFechaInicio' " : date('Y-m-01');
        $filtro .= !empty($txtFechaFinal) ? " and d.fechadoc<='$txtFechaFinal' " : date('Y-m-') . '' . $this->obtenerFinMes(date('n'), date('Y'));
        $filtro .= !empty($idtipodocumento) ? " and d.nombredoc='$idtipodocumento' " : "";
        $order = "d.fechadoc, d.numdoc";

        $data = $this->leeRegistro(
                "wc_documento d
                                left join wc_ordenventa ov ON ov.idordenventa=d.idordenventa
                                inner join wc_detalleordenventa dov on dov.idordenventa = ov.idordenventa
                                inner join wc_producto p on dov.idproducto = p.idproducto
                                ", "d.fechadoc as fecha,d.serie,d.numdoc as numero,p.codigopa as codigo, p.nompro as producto", $filtro, $order
        );
        return $data;
    }

    function reporteguias($txtFechaInicio, $txtFechaFinal) {
        $filtro = " ov.estado=1";
        $filtro .= " and ov.fordenventa >= '" . $txtFechaInicio . "'";
        $filtro .= " and ov.fordenventa <= '" . $txtFechaFinal . "'";
        $order = "ov.fordenventa, ov.codigov";

        $data = $this->leeRegistro(
                "wc_ordenventa ov", "ov.idordenventa, ov.fordenventa, ov.codigov, ov.desaprobado, ov.importeov", $filtro, $order
        );
        return $data;
    }

    function reporteguias2($txtFechaInicio, $txtFechaFinal) {
        //$filtro=" ov.estado=1";
        $filtro1 = " and ov.fordenventa >= '" . $txtFechaInicio . "'";
        $filtro1 .= " and ov.fordenventa <= '" . $txtFechaFinal . "'";

        $filtro2 = " and d.fechadoc >= '" . $txtFechaInicio . "'";
        $filtro2 .= " and d.fechadoc <= '" . $txtFechaFinal . "'";

        //$order="ov.fordenventa, ov.codigov";

        $data = $this->EjecutaConsulta(
                "select distinct
                            t3.*
                            from
                            (
                            (
                            select
                            *
                            from
                            (
                            select
                            ov.idordenventa,
                            ov.fordenventa,
                            ov.codigov,
                            ov.desaprobado,
                            ov.importeov
                            from wc_ordenventa ov
                            where
                            ov.estado = 1
                            and ov.esfacturado = 0
                            " . $filtro1 . "
                            ) as t1
                            )
                            union all
                            (
                            select
                            *
                            from
                            (
                            select
                            ov.idordenventa,
                            ov.fordenventa,
                            ov.codigov,
                            ov.desaprobado,
                            ov.importeov
                            from wc_ordenventa ov
                            inner join wc_documento d on d.idordenventa = ov.idordenventa
                            where
                            ov.estado = 1
                            and d.estado = 1
                            and d.esanulado = 0
                            and (nombredoc = 1 or nombredoc = 2)
                            " . $filtro2 . "
                            ) as t2
                            )
                            ) as t3 order by t3.fordenventa, t3.codigov"
        );
        return $data;
    }

    function reportedocumentos($idordenventa) {
        $data = $this->leeRegistro(
                "wc_documento d", "d.fechadoc, d.serie, (case d.nombredoc when 1 then d.numdoc else '' end) as numdocfac, (case d.nombredoc when 2 then d.numdoc else '' end) as numdocbol, d.montofacturado, d.montoigv, d.esanulado", 'd.estado=1 and (d.nombredoc = 1 or d.nombredoc = 2) and d.idordenventa = ' . $idordenventa, 'd.fechacreacion desc', 'limit 1'
        );
        if (count($data) == 0) {
            return null;
        }
        return $data[0];
    }

    function reportedocumentos2($idordenventa) {
        $data = $this->leeRegistro(
                "wc_documento d inner join wc_ordengasto og on og.idordenventa = d.idordenventa", "d.fechadoc, d.serie, (case d.nombredoc when 1 then d.numdoc else '' end) as numdocfac, (case d.nombredoc when 2 then d.numdoc else '' end) as numdocbol, d.montofacturado, d.esanulado,d.montoigv as montoigv", 'd.estado=1 and (d.nombredoc = 1 or d.nombredoc = 2) and idtipogasto=7 and d.idordenventa = ' . $idordenventa, 'd.fechacreacion desc', 'limit 1'
        );
        if (count($data) == 0) {
            return null;
        }
        return $data[0];
    }

    function reportedocumentos3($idordenventa) {
        $data = $this->leeRegistro(
                'wc_documento d', 'd.numdoc as ndoc', 'd.nombredoc = 4 and d.idordenventa = ' . $idordenventa, 'd.iddocumento desc', 'limit 1'
        );
        if (count($data) == 0) {
            return null;
        }
        return $data[0];
    }

    function reporteEstadoProductos($idordencompra, $idproducto) {
        $data = $this->leeRegistro("wc_ordencompra oc
                        inner join wc_detalleordencompra doc on doc.idordencompra = oc.idordencompra
                        inner join wc_producto p on p.idproducto = doc.idproducto
                        inner join wc_detalleordenventa dov on dov.idproducto = p.idproducto
                        inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa", "p.codigopa, p.nompro, ov.codigov, ov.fordenventa, ov.observaciones, (dov.cantdespacho - dov.cantdevuelta) as cantidad", 'oc.estado=1 and (dov.cantdespacho - dov.cantdevuelta) > 0 and oc.fordencompra < ov.fordenventa and oc.idordencompra = ' . $idordencompra . (!empty($idproducto) ? ' and idproducto = ' . $idproducto : ''), 'p.codigopa, ov.fordenventa'
        );
        return $data;
    }

    function DetalladoLetrasXProducto($FechaInicio, $FechaFin, $Principal, $Categoria, $lstZona, $txtIdCliente, $txtIdOrdenVenta, $lstMoneda, $numerounico) {
        $condicion = "wc_detalleordencobro.numeroletra != '' and "
                . "wc_detalleordencobro.situacion = '' and "
                . "wc_detalleordencobro.estado = 1 and "
                . "wc_ordencobro.estado = 1 and "
                . "(wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2) and "
                . "wc_detalleordencobro.saldodoc > 0 and "
                . "wc_detalleordencobro.formacobro=3 and "
                . "wc_detalleordencobro.renovado=0 and "
                . "wc_detalleordencobro.recepcionLetras='PA'";

        if (!empty($FechaInicio))
            $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
        if (!empty($FechaFin))
            $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
        if (!empty($Principal))
            $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $lstMoneda;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        // $condicion .= " and wc_detalleordencobro.numerounico = '" . $numerounico."'";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $txtFechaFinal . "'";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_ordenventa.`idcliente` = wc_cliente.`idcliente`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro ON wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_ordenventa.idmoneda,
                    wc_ordenventa.idordenventa,
                    wc_ordenventa.codigov,
                    wc_ordenventa.fordenventa,
                    wc_ordenventa.situacion as situacionov,
                    wc_ordenventa.idvendedor,
                    concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                    wc_cliente.razonsocial,
                    (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                    wc_ordencobro.situacion as situacionoc,
                    wc_ordencobro.femision,
                    wc_ordencobro.importeordencobro,
                    wc_ordencobro.saldoordencobro,
                    wc_detalleordencobro.recepcionLetras as recepLetra,
                    wc_detalleordencobro.*", $condicion, "");
        return $data;
    }
    
    function resumenIncobrables_detalle($txtFechaInicio = "", $txtFechaFinal = "", $lstCategoria = "", $lstZona = "", $txtIdCliente = "", $txtidOrdenVenta = "", $lstMoneda = "", $cmbCondicion = "") {
        $condicion = "wc_detalleordencobro.situacion = '' and "
                . "wc_detalleordencobro.estado = 1 and "
                . "wc_ordencobro.estado = 1 and "
                . "wc_categoria.`idpadrec`= 39 and "
                . "wc_detalleordencobro.saldodoc > 0 and "
                . "wc_ordenventa.estado = 1 and "
                . "wc_ordenventa.esanulado = 0 ";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_ordenventa.fechacreacion >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_ordenventa.fechacreacion <= '" . $txtFechaFinal . "'";
        }
        if (!empty($lstCategoria)) {
            $condicion .= ' and wc_categoria.idpadrec=' . $lstCategoria;
        }
        if (!empty($lstZona)) {
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        }
        if (!empty($txtIdCliente)) {
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        }
        if (!empty($txtidOrdenVenta)) {
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtidOrdenVenta;
        }
        if (!empty($lstMoneda)) {
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        }
        if (!empty($cmbCondicion) && $cmbCondicion != 4 && $cmbCondicion != 2) {
            $condicion .= ' and wc_detalleordencobro.formacobro=' . $cmbCondicion;
        } else if ($cmbCondicion == 2) {
            $condicion .= " and wc_detalleordencobro.`situacion`='' and
                        wc_detalleordencobro.`formacobro`='2' and
                        wc_detalleordencobro.`montoprotesto`=0";
        } else if ($cmbCondicion == 4) {
            $condicion .= " and wc_detalleordencobro.`situacion`!='reprogramado' and
                        wc_detalleordencobro.`situacion`!='anulado' and
                        wc_detalleordencobro.`situacion`!='extornado' and
                        wc_detalleordencobro.`situacion`!='refinanciado' and
                        wc_detalleordencobro.`situacion`!='protestado' and
                        wc_detalleordencobro.`situacion`!='renovado' and
                        wc_detalleordencobro.`situacion`='' and
                        wc_detalleordencobro.`formacobro`='2' and
                        (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
        }
        
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                 INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                 INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                 INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                 INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                 INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                 INNER JOIN `wc_ordencobro` wc_ordencobro ON wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                 INNER JOIN `wc_detalleordencobro` wc_detalleordencobro ON wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", 
                "wc_ordenventa.idmoneda,
                 wc_ordenventa.idordenventa,
                 wc_ordenventa.codigov,
                 wc_ordenventa.fordenventa,
                 wc_ordenventa.situacion as situacionov,
                 wc_ordenventa.idvendedor,
                 concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                 wc_cliente.razonsocial,
                 (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                 wc_ordencobro.situacion as situacionoc,
                 wc_ordencobro.femision,
                 wc_ordencobro.importeordencobro,
                 wc_ordencobro.saldoordencobro,
                 wc_categoria.`idcategoria`, 
                 wc_categoria.`idpadrec`, 
                 wc_detalleordencobro.recepcionLetras as recepLetra,
                 wc_detalleordencobro.*,
                 wc_ordenventa.importepagado, substring( wc_detalleordencobro.referencia,9,1) as referencia1, substring( wc_detalleordencobro.referencia,11,1) as referencia2", $condicion, 
                "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        
        return $data;
    }
    
    function resumenIncobrables_mirabymejorado($cmtEtapa = "", $txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "detalleordencobro.situacion = '' 
                       AND detalleordencobro.estado = 1 
                       AND ordencobro.estado = 1 
                       AND categoria.idpadrec = 39
                       AND detalleordencobro.saldodoc > 0 
                       AND ordenventa.estado = 1
                       AND ordenventa.esanulado = 0";
        if (!empty($txtFechaInicio)) {
            $condicion .= " AND ordenventa.fechacreacion >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " AND ordenventa.fechacreacion <= '" . $txtFechaFinal . "'";
        }
        if ($cmtEtapa == 1) {
            $condicion .= " AND ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " AND ordenventa.fordenventa >= '2020-03-17'";
        }
        
        $data = $this->leeRegistro("wc_ordenventa ordenventa 
                                   INNER JOIN wc_clientezona clientezona ON ordenventa.idclientezona = clientezona.idclientezona
                                   INNER JOIN wc_zona zona ON clientezona.idzona = zona.idzona
                                   INNER JOIN wc_categoria categoria ON zona.idcategoria = categoria.idcategoria
                                   INNER JOIN wc_ordencobro ordencobro ON ordencobro.idordenventa = ordenventa.idordenventa AND ordencobro.estado = 1 
                                   INNER JOIN wc_detalleordencobro detalleordencobro ON detalleordencobro.idordencobro = ordencobro.idordencobro AND detalleordencobro.estado = 1",
                                   "ordenventa.idmoneda, 
                                   categoria.idcategoria, 
                                   categoria.idpadrec, 
                                   sum(detalleordencobro.saldodoc) as saldodoc, 
                                   detalleordencobro.formacobro,
                                   concat(Substring(detalleordencobro.referencia, 9, 1), Substring(detalleordencobro.referencia, 11, 1)) as referencias", 
                                    $condicion, "", 
                                    "group by categoria.idcategoria, 
                                     ordenventa.IdMoneda, 
                                     detalleordencobro.formacobro, 
                                     referencias
                                     order by categoria.idpadrec asc");
        return $data;
    }
    
    function resumenIncobrables($txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "wc_detalleordencobro.situacion = '' and "
                . "wc_detalleordencobro.estado = 1 and "
                . "wc_ordencobro.estado = 1 and "
                . "wc_categoria.`idpadrec`= 39 and "
                . "wc_detalleordencobro.saldodoc > 0 and "
                . "wc_ordenventa.estado = 1 and "
                . "wc_ordenventa.esanulado = 0 ";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_ordenventa.fechacreacion >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_ordenventa.fechacreacion <= '" . $txtFechaFinal . "'";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_ordenventa.idmoneda, wc_categoria.`idcategoria`, wc_categoria.`idpadrec`, wc_detalleordencobro.recepcionletras, wc_detalleordencobro.saldodoc, wc_detalleordencobro.formacobro, substring( wc_detalleordencobro.referencia,9,1) as referencia1, substring( wc_detalleordencobro.referencia,11,1) as referencia2", $condicion, "");
        return $data;
    }
    
    function resumenPesados_mirabymejorado($cmtEtapa = "", $txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "detalleordencobro.situacion = '' 
                       AND detalleordencobro.estado = 1 
                       AND ordencobro.estado = 1 
                       AND categoria.idpadrec IN (40, 48) 
                       AND detalleordencobro.saldodoc > 0 
                       AND ordenventa.estado = 1
                       AND ordenventa.esanulado = 0";
        if (!empty($txtFechaInicio)) {
            $condicion .= " AND ordenventa.fechacreacion >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " AND ordenventa.fechacreacion <= '" . $txtFechaFinal . "'";
        }
        if ($cmtEtapa == 1) {
            $condicion .= " AND ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " AND ordenventa.fordenventa >= '2020-03-17'";
        }
        
        $data = $this->leeRegistro("wc_ordenventa ordenventa 
                                   INNER JOIN wc_clientezona clientezona ON ordenventa.idclientezona = clientezona.idclientezona
                                   INNER JOIN wc_zona zona ON clientezona.idzona = zona.idzona
                                   INNER JOIN wc_categoria categoria ON zona.idcategoria = categoria.idcategoria
                                   INNER JOIN wc_ordencobro ordencobro ON ordencobro.idordenventa = ordenventa.idordenventa AND ordencobro.estado = 1 
                                   INNER JOIN wc_detalleordencobro detalleordencobro ON detalleordencobro.idordencobro = ordencobro.idordencobro AND detalleordencobro.estado = 1",
                                   "ordenventa.idmoneda, 
                                   categoria.idcategoria, 
                                   categoria.idpadrec, 
                                   sum(detalleordencobro.saldodoc) as saldodoc, 
                                   detalleordencobro.formacobro,
                                   concat(Substring(detalleordencobro.referencia, 9, 1), Substring(detalleordencobro.referencia, 11, 1)) as referencias", 
                                    $condicion, "", 
                                    "group by categoria.idpadrec, 
                                     ordenventa.IdMoneda, 
                                     detalleordencobro.formacobro, 
                                     referencias
                                     order by categoria.idpadrec asc");
        return $data;
    }

    function resumenPesados($txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "wc_detalleordencobro.situacion = '' and "
                . "wc_detalleordencobro.estado = 1 and "
                . "wc_ordencobro.estado = 1 and "
                . "wc_categoria.`idpadrec` in (40, 48) and "
                . "wc_detalleordencobro.saldodoc > 0 and "
                . "wc_ordenventa.estado = 1 and "
                . "wc_ordenventa.esanulado = 0 ";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_ordenventa.fechacreacion >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_ordenventa.fechacreacion <= '" . $txtFechaFinal . "'";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_ordenventa.idmoneda, wc_categoria.`idcategoria`, wc_categoria.`idpadrec`, wc_detalleordencobro.recepcionletras, wc_detalleordencobro.saldodoc, wc_detalleordencobro.formacobro, substring( wc_detalleordencobro.referencia,9,1) as referencia1, substring( wc_detalleordencobro.referencia,11,1) as referencia2", $condicion, "");
        return $data;
    }
    
    function resumenLetras_mirabymejorado($cmtEtapa = "", $txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "detalleordencobro.numeroletra != '' 
                       AND detalleordencobro.situacion = '' 
                       AND detalleordencobro.estado = 1 
                       AND ordencobro.estado = 1 
                       AND (categoria.idpadrec = 1 OR categoria.idpadrec = 2) 
                       AND detalleordencobro.saldodoc > 0 
                       AND detalleordencobro.formacobro = 3 
                       AND detalleordencobro.renovado = 0 
                       AND ordenventa.estado = 1 
                       AND ordenventa.esanulado = 0";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and ordenventa.fechacreacion >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and ordenventa.fechacreacion <= '" . $txtFechaFinal . "'";
        }
        if ($cmtEtapa == 1) {
            $condicion .= " and ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and ordenventa.fordenventa >= '2020-03-17'";
        }
        
        $data = $this->leeRegistro("wc_ordenventa ordenventa 
                                   INNER JOIN wc_clientezona clientezona ON ordenventa.idclientezona = clientezona.idclientezona
                                   INNER JOIN wc_zona zona ON clientezona.idzona = zona.idzona 
                                   INNER JOIN wc_categoria categoria ON zona.idcategoria = categoria.idcategoria
                                   INNER JOIN wc_ordencobro ordencobro ON ordencobro.idordenventa = ordenventa.idordenventa AND ordencobro.estado = 1 
                                   INNER JOIN wc_detalleordencobro detalleordencobro ON detalleordencobro.idordencobro = ordencobro.idordencobro AND detalleordencobro.estado = 1",
                                   "ordenventa.idmoneda, 
                                   categoria.idpadrec, 
                                   detalleordencobro.recepcionletras, 
                                   sum(detalleordencobro.saldodoc) as saldodoc, 
                                   detalleordencobro.numerounico, 
                                   detalleordencobro.evaluacion", $condicion, "", "group by categoria.idpadrec, 
                                                                                    ordenventa.IdMoneda, 
                                                                                    detalleordencobro.recepcionletras,
                                                                                    detalleordencobro.numerounico
                                                                                    order by categoria.idpadrec asc");
        return $data;
    }


    function resumenLetras($txtFechaInicio = "", $txtFechaFinal = "") {/*
      $condicion = "" .
      "wc_detalleordencobro.numeroletra != '' and " . //ok
      "wc_ordenventa.estado = 1 and ". // agregado
      "wc_ordenventa.esanulado = 0 and ". // agregado
      "wc_detalleordencobro.saldodoc > 0.00 ". //ok
      "and wc_detalleordencobro.formacobro = 3 ". //ok
      "and wc_detalleordencobro.renovado=0 and ". // ok
      "wc_detalleordencobro.situacion = ''"; //ok
     */
        $condicion = "wc_detalleordencobro.numeroletra != '' and " //ok
                . "wc_detalleordencobro.situacion = '' and "
                . "wc_detalleordencobro.estado = 1 and "
                . "wc_ordencobro.estado = 1 and "
                . "(wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2) and "
                . "wc_detalleordencobro.saldodoc > 0 and "
                . "wc_detalleordencobro.formacobro=3 and "
                . "wc_detalleordencobro.renovado=0"
                . " and wc_ordenventa.estado = 1"
                . " and wc_ordenventa.esanulado = 0 ";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_ordenventa.fechacreacion >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_ordenventa.fechacreacion <= '" . $txtFechaFinal . "'";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_ordenventa.idmoneda, wc_categoria.`idpadrec`, wc_detalleordencobro.recepcionletras, wc_detalleordencobro.saldodoc, wc_detalleordencobro.numerounico,wc_detalleordencobro.evaluacion", $condicion, "");
        return $data;
    }

    function resumenLetrasSQL($txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "wc_detalleordencobro.numeroletra != '' and "
                . "wc_detalleordencobro.situacion = '' and "
                . "wc_detalleordencobro.estado = 1 and "
                . "wc_ordencobro.estado = 1 and "
                . "(wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2) and "
                . "wc_detalleordencobro.saldodoc > 0 and "
                . "wc_detalleordencobro.formacobro=3 and "
                . "wc_detalleordencobro.renovado=0";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $txtFechaFinal . "'";
        }
        $data = $this->devuelveSQL(
                "`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_ordenventa.idmoneda, wc_categoria.`idpadrec`, wc_detalleordencobro.recepcionletras, wc_detalleordencobro.saldodoc, wc_detalleordencobro.numerounico", $condicion, "");
        return $data;
    }

    function resumenDetalladoLetrasProtestadas($cmtEtapa = "", $FechaInicio, $FechaFin, $Principal = "", $Categoria = "", $lstZona = "", $txtIdCliente = "", $txtIdOrdenVenta = "", $lstMoneda = "") {
        $condicion = "wc_ordenventa.`esguiado`=1 and
                        wc_ordenventa.`estado`=1 and
                        wc_detalleordencobro.`situacion`!='reprogramado' and
                        wc_detalleordencobro.`situacion`!='anulado' and
                        wc_detalleordencobro.`situacion`!='extornado' and
                        wc_detalleordencobro.`situacion`!='refinanciado' and
                        wc_detalleordencobro.`situacion`!='protestado' and
                        wc_detalleordencobro.`situacion`!='renovado' and
                        wc_detalleordencobro.`situacion`='' and
                        wc_detalleordencobro.`formacobro`='2' and
                        (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";

        if (!empty($FechaInicio)) {
            $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
        }
        if (!empty($FechaFin)) {
            $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
            $condicion .= " and wc_detalleordencobro.fechacreacion <= '$FechaFin 23:59:59'";
        }
        if ($cmtEtapa == 1) {
            $condicion .= " and wc_ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and wc_ordenventa.fordenventa >= '2020-03-17'";
        }
        if (!empty($Principal)) {
            $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
        } else {
            $condicion .= ' and (wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2)';
        }
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_ordenventa.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente` and wc_ordenventa.estado= 1
                                    INNER JOIN `wc_distrito` wc_distrito ON wc_cliente.`iddistrito` = wc_distrito.`iddistrito`
                                    INNER JOIN `wc_provincia` wc_provincia ON wc_distrito.`idprovincia` = wc_provincia.`idprovincia`
                                    INNER JOIN `wc_departamento` wc_departamento ON wc_provincia.`iddepartamento` = wc_departamento.`iddepartamento`
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado`=1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_detalleordencobro.iddetalleordencobro,
                                     wc_detalleordencobro.importedoc,
                                     wc_detalleordencobro.saldodoc,
                                     wc_provincia.`nombreprovincia`,
                                     wc_departamento.`nombredepartamento`,
                                     wc_distrito.`nombredistrito`,
                                     wc_ordenventa.idmoneda,
                                     wc_ordenventa.idordenventa,
                                     wc_ordenventa.codigov,
                                     wc_ordenventa.fordenventa,
                                     wc_ordenventa.situacion as situacionov,
                                     wc_cliente.direccion,
                                     (case when wc_cliente.razonsocial is null then concat(wc_cliente.nombrecli, ' ', wc_cliente.apellido1, ' ', wc_cliente.apellido2) else wc_cliente.razonsocial end) as razonsocial,
                                     (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc", $condicion, "wc_cliente.idcliente, wc_ordenventa.idordenventa asc");
        return $data;
    }
    
    function detalladoLetrasProtestadas_mirabymejorado($cmtEtapa, $FechaInicio, $FechaFin) {
        $condicion = "ordenventa.esguiado = 1 
                       AND ordenventa.estado = 1 
                       AND detalleordencobro.situacion != 'reprogramado' 
                       AND detalleordencobro.situacion != 'anulado' 
                       AND detalleordencobro.situacion != 'extornado' 
                       AND detalleordencobro.situacion != 'refinanciado' 
                       AND detalleordencobro.situacion != 'protestado' 
                       AND detalleordencobro.situacion != 'renovado' 
                       AND detalleordencobro.situacion = '' 
                       AND detalleordencobro.formacobro = '2' 
                       AND (Substring(detalleordencobro.referencia, 9, 1) = 'p' OR Substring(detalleordencobro.referencia, 11, 1) = 'p' ) 
                       AND (categoria.idpadrec = 1 OR categoria.idpadrec = 2)";

        if (!empty($FechaInicio)) {
            $condicion .= " and ordenventa.fechacreacion >= '$FechaInicio'";
        }
        if (!empty($FechaFin)) {
            $condicion .= " and ordenventa.fechacreacion <= '$FechaFin'";
            $condicion .= " and detalleordencobro.fechacreacion <= '$FechaFin 23:59:59'";
        }
        if ($cmtEtapa == 1) {
            $condicion .= " and ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and ordenventa.fordenventa >= '2020-03-17'";
        }
        
        $data = $this->leeRegistro("wc_ordenventa ordenventa 
                                   INNER JOIN wc_actor actor ON actor.idactor = ordenventa.idvendedor 
                                   INNER JOIN wc_clientezona clientezona ON ordenventa.idclientezona = clientezona.idclientezona
                                   INNER JOIN wc_cliente cliente ON cliente.idcliente = ordenventa.idcliente AND ordenventa.estado = 1 
                                   INNER JOIN wc_zona zona ON clientezona.idzona = zona.idzona
                                   INNER JOIN wc_categoria categoria ON zona.idcategoria = categoria.idcategoria
                                   INNER JOIN wc_ordencobro ordencobro ON ordencobro.idordenventa = ordenventa.idordenventa AND ordencobro.estado = 1 
                                   INNER JOIN wc_detalleordencobro detalleordencobro ON detalleordencobro.idordencobro = ordencobro.idordencobro AND detalleordencobro.estado = 1", 
                                   "ordenventa.idmoneda, 
                                   categoria.idpadrec,  
                                   sum(detalleordencobro.saldodoc) as saldodoc", $condicion, "", 
                                    "group by categoria.idpadrec, 
                                    ordenventa.IdMoneda
                                    order by categoria.idpadrec asc");
        return $data;
    }

    function detalladoLetrasProtestadas($cmtEtapa = "", $FechaInicio, $FechaFin, $Principal = "", $Categoria = "", $lstZona = "", $txtIdCliente = "", $txtIdOrdenVenta = "", $lstMoneda = "") {
        $condicion = "wc_ordenventa.`esguiado`=1 and
                        wc_ordenventa.`estado`=1 and
                        wc_detalleordencobro.`situacion`!='reprogramado' and
                        wc_detalleordencobro.`situacion`!='anulado' and
                        wc_detalleordencobro.`situacion`!='extornado' and
                        wc_detalleordencobro.`situacion`!='refinanciado' and
                        wc_detalleordencobro.`situacion`!='protestado' and
                        wc_detalleordencobro.`situacion`!='renovado' and
                        wc_detalleordencobro.`situacion`='' and
                        wc_detalleordencobro.`formacobro`='2' and
                        (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";

        if (!empty($FechaInicio)) {
            $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
        }
        if (!empty($FechaFin)) {
            $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
            $condicion .= " and wc_detalleordencobro.fechacreacion <= '$FechaFin 23:59:59'";
        }
        if (!empty($Principal)) {
            $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
        } else {
            $condicion .= ' and (wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2)';
        }
        if ($cmtEtapa == 1) {
            $condicion .= " and wc_ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and wc_ordenventa.fordenventa >= '2020-03-17'";
        }
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_ordenventa.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente` and wc_ordenventa.estado= 1
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado`=1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_ordenventa.idmoneda,
                                    wc_categoria.idpadrec,
                                    wc_ordenventa.idordenventa,
                                    wc_ordenventa.codigov,
                                    wc_ordenventa.fordenventa,
                                    wc_ordenventa.idVendedor,
                                    wc_ordenventa.situacion as situacionov,
                                    concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                                    wc_cliente.razonsocial,
                                    (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                                    wc_ordencobro.situacion as situacionoc,
                                    wc_ordencobro.femision,
                                    wc_ordencobro.importeordencobro,
                                    wc_ordencobro.saldoordencobro,
                                    wc_detalleordencobro.*", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function detalladoLetrasProtestadaspendienteporvendedor($FechaInicio, $FechaFin, $fechavencimientoinicio, $fechavencimientofin, $Principal = "", $Categoria = "", $lstZona = "", $txtIdCliente = "", $txtIdvendedor = "") {
        $condicion = "wc_ordenventa.`esguiado`=1 and
                        wc_ordenventa.`estado`=1 and
                        wc_detalleordencobro.`situacion`!='reprogramado' and
                        wc_detalleordencobro.`situacion`!='anulado' and
                        wc_detalleordencobro.`situacion`!='extornado' and
                        wc_detalleordencobro.`situacion`!='refinanciado' and
                        wc_detalleordencobro.`situacion`!='protestado' and
                        wc_detalleordencobro.`situacion`!='renovado' and
                        wc_detalleordencobro.`situacion`='' and
                        wc_detalleordencobro.`formacobro`='2' and
                        (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
        if (!empty($fechavencimientoinicio))
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . $fechavencimientoinicio . "'";
        if (!empty($fechavencimientofin))
            $condicion .= " and wc_detalleordencobro.fvencimiento <= '" . $fechavencimientofin . "'";
        if (!empty($FechaInicio)) {
            $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
        }
        if (!empty($FechaFin)) {
            $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
            $condicion .= " and wc_detalleordencobro.fechacreacion <= '$FechaFin 23:59:59'";
        }
        if (!empty($Principal)) {
            $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
        } else {
            $condicion .= ' and (wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2)';
        }
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_ordenventa.idcliente=' . $txtIdCliente;
        if (!empty($txtIdvendedor))
            $condicion .= ' and wc_ordenventa.idvendedor=' . $txtIdvendedor;
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente` and wc_ordenventa.estado= 1
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado`=1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_ordenventa.idmoneda,
                                    wc_categoria.idpadrec,
                                    wc_ordenventa.idordenventa,
                                    wc_ordenventa.codigov,
                                    wc_ordenventa.fordenventa,
                                    wc_ordenventa.idVendedor,
                                    wc_ordenventa.situacion as situacionov,
                                    concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                                    wc_cliente.razonsocial,
                                    (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                                    wc_ordencobro.situacion as situacionoc,
                                    wc_ordencobro.femision,
                                    wc_ordencobro.importeordencobro,
                                    wc_ordencobro.saldoordencobro,
                                    wc_detalleordencobro.*", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function obtenerPadreOrdenCobro($idpadre) {
        $condicion = "wc_detalleordencobro.iddetalleordencobro='" . $idpadre . "'";
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
        inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
        inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
	wc_ordenventa.idordenventa,
	wc_ordenventa.codigov,
        wc_ordenventa.fordenventa,
        wc_ordenventa.situacion as situacionov,
        wc_ordencobro.situacion as situacionoc,
        wc_ordencobro.femision,
        wc_ordencobro.importeordencobro,
        wc_ordencobro.saldoordencobro,
        wc_detalleordencobro.*", $condicion, "");
        return $data;
    }

    function resumenLetrasProtestadas($txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "wc_ordenventa.`esguiado`=1 and wc_ordenventa.`estado`=1 and wc_ordencobro.`estado`=1 and wc_detalleordencobro.`situacion`!='reprogramado'  and wc_detalleordencobro.`situacion`!='anulado'  and wc_detalleordencobro.`situacion`!='extornado' and wc_detalleordencobro.`situacion`!='refinanciado' and wc_detalleordencobro.`situacion`!='protestado' and wc_detalleordencobro.`situacion`!='renovado' and wc_detalleordencobro.`situacion`='' and wc_detalleordencobro.`formacobro`='2' and (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p') and (wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2)";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $txtFechaFinal . "'";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado`=1
                    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "wc_ordenventa.idmoneda, wc_detalleordencobro.iddetalleordencobro, wc_detalleordencobro.`importedoc`, wc_categoria.`idpadrec`, wc_detalleordencobro.saldodoc", $condicion, "");
        return $data;
    }
    
    function resumenEmpresas_mirabymejorado($cmtEtapa = "", $txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "ordenventa.estado=1 AND
                     ordenventa.situacion='pendiente' AND       
                     detalleordencobro.situacion='' AND        
                     ordenventa.idvendedor IN (136, 241, 152, 184, 264, 59, 391, 445, 540, 557, 558, 554)";

        if (!empty($txtFechaInicio)) {
            $condicion .= " AND detalleordencobro.fechagiro >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " AND detalleordencobro.fechagiro <= '" . $txtFechaFinal . "'";
        }
        if ($cmtEtapa == 1) {
            $condicion .= " AND ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " AND ordenventa.fordenventa >= '2020-03-17'";
        }

        $data = $this->leeRegistro("wc_ordenventa ordenventa 
                                    INNER JOIN wc_actor actor ON actor.idactor = ordenventa.idvendedor 
                                    INNER JOIN wc_cliente cliente ON cliente.idcliente = ordenventa.idcliente 
                                    INNER JOIN wc_ordencobro ordencobro ON ordencobro.idordenventa=ordenventa.idordenventa AND ordencobro.estado=1 
                                    INNER JOIN wc_detalleordencobro detalleordencobro ON detalleordencobro.idordencobro=ordencobro.idordencobro AND detalleordencobro.estado=1",
                                    "ordenventa.idvendedor,
                                    ordenventa.idmoneda,
                                    sum(detalleordencobro.saldodoc) as saldodoc", 
                                    $condicion, "",
                                    "GROUP BY ordenventa.idvendedor,
                                    ordenventa.IdMoneda
                                    ORDER BY ordenventa.idvendedor asc");
        return $data;
    }

    function resumenEmpresas($cmtEtapa = "", $txtFechaInicio = "", $txtFechaFinal = "", $idmoneda = "", $muestras = "") {
        $condicion = "wc_ordenventa.`estado`=1 and "
                . "wc_ordenventa.`situacion`='pendiente' and "
                . "wc_detalleordencobro.`situacion`='' and ";
        if ($muestras == "si") {
            $condicion .= "wc_ordenventa.`idvendedor` in (59)";
        } else {
            $condicion .= "wc_ordenventa.`idvendedor` in (136, 241, 152, 184, 264, 59, 391, 445, 540, 557, 558, 554)";
        }
        if ($cmtEtapa == 1) {
            $condicion .= " and wc_ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and wc_ordenventa.fordenventa >= '2020-03-17'";
        }
        
        if (!empty($txtFechaInicio))
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $txtFechaInicio . "'";
        if (!empty($txtFechaFinal))
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $txtFechaFinal . "'";
        if (!empty($idmoneda))
            $condicion .= " and wc_ordenventa.idmoneda=" . $idmoneda;
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
        INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado`=1
        INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1", "Concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ',  wc_actor.apellidomaterno) AS vendedor,
                   wc_cliente.razonsocial,
                   wc_ordenventa.idmoneda,
                    wc_ordenventa.idordenventa,
                    wc_ordenventa.codigov,
                    wc_ordenventa.fordenventa,
                    wc_ordenventa.situacion as situacionov,
                    wc_ordenventa.importepagado,
                    wc_ordenventa.importeaprobado,
                    wc_ordenventa.idvendedor,
                    wc_ordenventa.fechadespacho,
                    wc_ordenventa.fechavencimiento,
                    wc_ordencobro.situacion AS situacionoc,
                    wc_ordencobro.femision,
                    wc_ordencobro.importeordencobro,
                    wc_ordencobro.saldoordencobro,
                    wc_detalleordencobro.*", $condicion, "wc_ordenventa.idvendedor,
                  wc_ordenventa.idordenventa,
                  wc_ordencobro.idordencobro,
                  wc_detalleordencobro.iddetalleordencobro ASC");
        return $data;
    }

    function resumenCreditos($txtFechaInicio = "", $txtFechaFinal = "") {
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $txtFechaFinal . "'";
        }

        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       wc_ordencobro.situacion as situacionoc,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function resumenCreditosSQL($txtFechaInicio = "", $txtFechaFinal = "") {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and wc_ordenventa.`esguiado`=1 and wc_ordenventa.`estado`=1 and wc_ordencobro.`estado`=1 and wc_detalleordencobro.`situacion`!='reprogramado'  and wc_detalleordencobro.`situacion`!='anulado'  and wc_detalleordencobro.`situacion`!='extornado' and wc_detalleordencobro.`situacion`!='refinanciado' and wc_detalleordencobro.`situacion`!='protestado' and wc_detalleordencobro.`situacion`!='renovado' and wc_detalleordencobro.`situacion`='' and wc_detalleordencobro.`formacobro`='2' and wc_detalleordencobro.referencia='' and wc_categoria.`idpadrec` in(1, 2)";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $txtFechaInicio . "'";
        }
        if (!empty($txtFechaFinal)) {
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $txtFechaFinal . "'";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idordenventa, wc_ordenventa.idmoneda, (wc_ordenventa.`importepagado`-wc_ordenventa.`importedevolucion`) as `importepagado`, wc_categoria.`idpadrec`", $condicion, "");
        return $data;
    }

    function clientesDeVendedor($idvendedor, $anio = "") {
        if (!empty($anio)) {
            $anio = " and o.fordenventa >= '" . $anio . "-01-01' and o.fordenventa <= '" . $anio . "-12-31'";
        }
        return $this->EjecutaConsulta("select distinct c.*, z.nombrezona, d.nombredistrito, p.nombreprovincia, t.nombredepartamento, sum(importeov) as suma
                    from wc_cliente c
                    inner join wc_ordenventa o on o.idcliente = c.idcliente
                    inner join wc_zona z on z.idzona = c.zona
                    inner join wc_distrito d on d.iddistrito = c.iddistrito
                    inner join wc_provincia p on p.idprovincia = d.idprovincia
                    inner join wc_departamento t on t.iddepartamento = p.iddepartamento
                    where o.desaprobado != 1 and o.estado = 1 and c.estado = 1 and o.importeov > o.importedevolucion and o.idvendedor = " . $idvendedor . $anio . "
                    group by c.idcliente
                    order by suma desc");
    }

    function getUltimaOrdenCompra($idproducto) {
        return $this->EjecutaConsulta("select oc.fordencompra, doc.cantidadrecibidaoc
                    from wc_detalleordencompra doc
                    inner join wc_ordencompra oc on oc.idordencompra = doc.idordencompra
                    where doc.estado = 1 and oc.estado = 1 and doc.idproducto = $idproducto
                    order by oc.fordencompra desc limit 1");
    }

    function reporteclientevendedorresumido($idvendedor, $anio) {
        $condicion = "o.desaprobado != 1 and o.estado = 1 and c.estado = 1 and o.importeov > o.importedevolucion and o.idvendedor = " . $idvendedor;
        if (!empty($anio)) {
            $condicion .= " and o.fordenventa >= '" . $anio . "-01-01' and o.fordenventa <= '" . $anio . "-12-31'";
        }
        $data = $this->leeRegistro(
                "wc_cliente c "
                . "inner join wc_ordenventa o on o.idcliente = c.idcliente "
                . "inner join wc_tipocambio tc on o.fordenventa = tc.fechatc"
                . " inner join wc_zona z on z.idzona = c.zona
                        inner join wc_distrito dist on dist.iddistrito = c.iddistrito
                        inner join wc_provincia prov on prov.idprovincia = dist.idprovincia
                        inner join wc_departamento dep on dep.iddepartamento = prov.iddepartamento", "c.razonsocial, sum(case o.idMoneda when 1 then (o.importeov * tc.venta) else o.importeov end) as importeov, count(o.idordenventa) as cant, "
                . "month(o.fordenventa) as mes, z.nombrezona as zona, case dep.iddepartamento when 14 then 'L' else 'P' end as provincia", $condicion, "", "group by c.idcliente, month(o.fordenventa) order by c.razonsocial, o.fordenventa");
        return $data;
    }

    function reportePedidoVentas1($idVendedor, $fechaInicial, $fechaFinal, $provinciax) {
        $opcional = empty($idVendedor) ? '' : "and a.idactor = " . $idVendedor;
        $provinciaxx = empty($provinciax) ? 0 : $provinciax;
        if ($provinciaxx == 0) {
            $opcional2 = "";
        } elseif ($provinciaxx == 1) {
            $opcional2 = "and d.codigodepto = 14";
        } else {
            $opcional2 = "and d.codigodepto <> 14";
        }
        $data = $this->EjecutaConsulta("select
                    d.iddistrito as IDDISTRITO,
                    d.nombredistrito AS DISTRITO,
                    CONCAT(a.nombres,' ',a.apellidopaterno,' ',a.apellidomaterno) as VENDEDOR,
                    c.idcliente,
                    CONCAT(c.nombrecli,' ',c.apellido1,' ',c.apellido2) as CLIENTE,
                    ov.importeov as VENTAS
                    from wc_distrito d
                    inner join wc_cliente c on c.iddistrito = d.iddistrito
                    inner join wc_ordenventa ov on ov.idcliente = c.idcliente
                    inner join wc_actor a on a.idactor = ov.idvendedor
                    inner join wc_actorrol ar on ar.idactor = a.idactor
                    where a.estado = 1
                    and ar.idrol = 25
                    " . $opcional . "
                    " . $opcional2 . "
                    and ov.estado = 1
                    and ar.estado = 1
                    and c.estado = 1

                    and ov.fordenventa >= '" . $fechaInicial . "'
                    and ov.fordenventa <= '" . $fechaFinal . "'

                    order by d.nombredistrito, c.idcliente");

        return $data;
    }

    function obtenerVentaxZonaProcentaje($idCategoria, $filtro) {

//            if(!empty($idVendedor)){
//                $filtro.= "and a.idactor = ".$idVendedor;
//            }
//            if(!empty($fechaInicio)){
//                $filtro .= "and ov.fordenventa >= ".$fechaInicio;
//            }
//            if(!empty($fechaFin)){
//                $filtro .= "and ov.fordenventa <= ".$fechaFin;
//            }
        //echo date_format($datai, 'Y-m-d');
        $data = $this->EjecutaConsulta("select
                    z.nombrezona zona,
                    ov.importeov monto
                    from wc_cliente c
                    inner join wc_zona z on z.idzona = c.zona
                    inner join wc_ordenventa ov on ov.idcliente = c.idcliente
                    inner join wc_actor a on a.idactor = ov.idvendedor
                    where " . $filtro . " and z.idcategoria = " . $idCategoria
                . " order by z.nombrezona"
        );
//                    and a.idactor = $idVendedor
//                    and z.idcategoria = $idCategoria
//                    and ov.fordenventa between '$fechaInicio' and '$fechaFin' ");
        return $data;
    }

    function listaProductosTop($linea, $marca, $inicio, $final) {

        if (!empty($linea)) {
            $filtro .= " and l.idlinea = " . $linea;
        }
        if (!empty($marca)) {
            $filtro .= " and m.idmarca = " . $marca;
        }
        if (!empty($inicio)) {
            $finicio = date_create($inicio);
            //$filtro .= " and ov.fordenventa >= '2017-01-01'";//.$inicio;
            $filtro .= " and ov.fordenventa >= '" . date_format($finicio, 'Y-m-d') . "' ";
        }
        if (!empty($final)) {
            $ffinal = date_create($final);
            //$filtro .= " and ov.fordenventa <= '2017-01-31'";//.$final;
            $filtro .= " and ov.fordenventa <= '" . date_format($ffinal, 'Y-m-d') . "' ";
        }

        //echo date_format($datai, 'Y-m-d');
        $data = $this->EjecutaConsulta("select
                        p.idproducto as idproducto,
                        p.nompro as producto,
                        p.codigopa as codigo,
                        l.nomlin as linea,
                        m.nombre as marca,
                        dov.cantdespacho as cantidad,
                        dov.cantdespacho as cantidad,
                        (dov.preciofinal*dov.cantdespacho) - (p.cifventasdolares*dov.cantdespacho) as utilidad
                        from wc_ordenventa ov
                        inner join wc_detalleordenventa dov on dov.idordenventa = ov.idordenventa
                        inner join wc_producto p on p.idproducto = dov.idproducto
                        inner join wc_linea l on l.idlinea = p.idlinea
                        inner join wc_marca m on m.idmarca = p.idmarca
                        where ov.estado = 1
                        and p.estado = 1
                        and dov.cantdespacho != 0
                        and dov.estado = 1 " . $filtro . " order by idproducto"
        );
        return $data;
        return $filtro;
    }

    function infoOrdenVentaxCliente($idCliente) {
        $data = $this->EjecutaConsulta("select
                    ov.idordenventa as idOrdenventa,
                    ov.codigov as codigo,
                    ov.fordenventa as fordenventa,
                    case concat(c.nombrecli,' ',c.apellido1,' ',c.apellido2) when ' ' then c.razonsocial else concat(c.nombrecli,' ',c.apellido1,' ',c.apellido2) end as cliente,
                    concat(a.nombrecompleto,' ',a.apellidopaterno,' ',a.apellidomaterno) as vendedor
                    from wc_ordenventa ov
                    inner join wc_cliente c on c.idcliente = ov.idcliente
                    inner join wc_actor a on a.idactor = ov.idvendedor
                    where ov.estado = 1
                    and c.idcliente = $idCliente");
        return $data;
    }

    function infoDocumento($idOrdenVenta, $tdoc) {
        $data = $this->EjecutaConsulta("select
                    d.numdoc
                    from wc_documento d
                    where d.estado = 1
                    and d.nombredoc = $tdoc
                    and d.idordenventa = $idOrdenVenta");
        return $data;
    }

    function infoLetraxOrdenVenta($idOrdenVenta) {
        $data = $this->EjecutaConsulta("select
                    doc.iddetalleordencobro,
                    doc.fvencimiento,
                    doc.numeroletra,
                    doc.situacion,
                    doc.importedoc,
                    doc.saldodoc
                    from wc_detalleordencobro doc
                    inner join wc_ordencobro oc on oc.idordencobro = doc.idordencobro
                    inner join wc_ordenventa ov on ov.idordenventa = oc.idordenventa
                    where doc.estado = 1
                    and oc.estado = 1
                    and doc.numeroletra <> ''
                    and ov.idordenventa = $idOrdenVenta");
        return $data;
    }

    function cobroTotal($idordenventa) {
        $sql = "select sum(importegasto) as cobrototal from wc_ordengasto where idordenventa = " . $idordenventa . " and estado = 1";
        return $this->EjecutaConsulta($sql);
    }

    function get_zonas($idzona, $idCategoria, $idpadrec) {
        $condicion = '';
        if (!empty($idzona)) {
            $condicion = ' and z.idzona = ' . $idzona;
        } else if (!empty($idCategoria)) {
            $condicion = ' and cat.idcategoria = ' . $idCategoria;
        } else {
            $condicion = ' and cat.idpadrec = ' . $idpadrec;
        }
        $sql = "select
                    z.idzona,
                    z.nombrezona
                    from wc_zona z
                    inner join wc_categoria cat on cat.idcategoria = z.idcategoria
                    where z.estado = 1
                    and cat.estado = 1 " . $condicion;
        //return $sql;
        return $this->EjecutaConsulta($sql);
    }

    function get_detalle_orden_cobro($idzona, $formacobro) {
        $sql = "select
                    doc.fvencimiento,
                    doc.importedoc,
                    doc.saldodoc,
                    ov.idmoneda,
                    ov.situacion
                    from wc_ordenventa ov
                    inner join wc_ordencobro oc on oc.idordenventa = ov.idordenventa
                    inner join wc_detalleordencobro doc on doc.idordencobro = oc.idordencobro
                    inner join wc_cliente c on c.idcliente = ov.idcliente
                    inner join wc_zona z on z.idzona = c.zona
                    where oc.estado = 1
                    and doc.estado = 1
                    and ov.estado = 1
                    and c.estado = 1
                    and (doc.situacion = 'cancelado' or doc.situacion = '')
                    and doc.formacobro = " . $formacobro . " and z.idzona = " . $idzona;
        return $this->EjecutaConsulta($sql);
    }

    function get_detalle_letras($idzona) {
        $sql = "select
                    ov.codigov,
                    doc.fvencimiento,
                    doc.importedoc,
                    doc.saldodoc,
                    ov.idmoneda,
                    ov.situacion,
                    doc.recepcionLetras
                    from wc_ordenventa ov
                    inner join wc_ordencobro oc on oc.idordenventa = ov.idordenventa
                    inner join wc_detalleordencobro doc on doc.idordencobro = oc.idordencobro
                    inner join wc_cliente c on c.idcliente = ov.idcliente
                    inner join wc_zona z on z.idzona = c.zona
                    where oc.estado = 1
                    and doc.estado = 1
                    and ov.estado = 1
                    and c.estado = 1
                    and doc.formacobro = 3";
        return $this->EjecutaConsulta($sql);
    }

    // -- and (doc.situacion = 'cancelado' or doc.situacion = '' or doc.situacion = 'protestado')
    function get_detalle_letras2($idzona) {
        $sql = "select
                    wc_ordenventa.idmoneda,
                    wc_categoria.`idpadrec`,
                    wc_detalleordencobro.recepcionletras,
                    wc_detalleordencobro.saldodoc,
                    wc_detalleordencobro.numerounico
                    from
                    `wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`
                    where wc_detalleordencobro.numeroletra != ''
                    and wc_detalleordencobro.situacion = ''
                    and wc_detalleordencobro.estado = 1
                    and wc_ordencobro.estado = 1
                    and (wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2)
                    and wc_detalleordencobro.saldodoc > 0.00
                    and wc_zona.idzona = " . $idzona;
        return $this->EjecutaConsulta($sql);
    }

    function zonaVendedor($idvendedor, $moneda, $finicio, $ffinal) {
        $sql = "select distinct
                    z.idzona,
                    z.nombrezona
                    from wc_ordenventa ov
                    inner join wc_actor a on a.idactor = ov.idvendedor
                    inner join wc_cliente c on c.idcliente = ov.idcliente
                    inner join wc_zona z on z.idzona = c.zona
                    where ov.estado = 1
                    and a.estado = 1
                    and c.estado = 1
                    and z.estado = 1
                    and a.idactor = $idvendedor
                    and ov.fordenventa >= '$finicio'
                    and ov.fordenventa <= '$ffinal'
                    and ov.idmoneda = $moneda
                    order by z.nombrezona";
        return $this->EjecutaConsulta($sql);
    }

    function listadoOrdenVentaCM($idzona, $idvendedor, $moneda, $finicio, $ffinal) {
        $sql = "select
                    vl.idlinea,
                    dov.cantdespacho*dov.preciofinal as total
                    from wc_ordenventa ov
                    inner join wc_detalleordenventa dov on dov.idordenventa = ov.idordenventa
                    inner join wc_producto p on p.idproducto = dov.idproducto
                    inner join vista_sublinea vsl on vsl.idlinea = p.idlinea
                    inner join vista_linea vl on vl.idlinea = vsl.idpadre
                    inner join wc_actor a on a.idactor = ov.idvendedor
                    inner join wc_cliente c on c.idcliente = ov.idcliente
                    inner join wc_zona z on z.idzona = c.zona
                    where ov.estado = 1
                    and dov.estado = 1
                    and a.estado = 1
                    and c.estado = 1
                    and z.estado = 1
                    and a.idactor = $idvendedor
                    and ov.fordenventa >= '$finicio'
                    and ov.fordenventa <= '$ffinal'
                    and ov.idmoneda = $moneda
                    and z.idzona = $idzona
                    order by vl.idlinea";
        return $this->EjecutaConsulta($sql);
    }

    function listadoOrdenventa2($idzona, $idvendedor, $moneda, $finicio, $ffinal) {
        $sql = "select
                    ov.idordenventa
                    from wc_ordenventa ov
                    inner join wc_actor a on a.idactor = ov.idvendedor
                    inner join wc_cliente c on c.idcliente = ov.idcliente
                    inner join wc_zona z on z.idzona = c.zona
                    where ov.estado = 1
                    and a.estado = 1
                    and c.estado = 1
                    and z.estado = 1
                    and a.idactor = $idvendedor
                    and ov.fordenventa >= '$finicio'
                    and ov.fordenventa <= '$ffinal'
                    and ov.idmoneda = $moneda
                    and z.idzona = $idzona";
        return $this->EjecutaConsulta($sql);
    }

    function listadoOrdenCobro2($idordenventa) {
        $sql = "select
                    idordencobro
                    from wc_ordencobro
                    where estado = 1
                    and idordenventa = $idordenventa
                    order by idordencobro asc
                    limit 1";
        return $this->EjecutaConsulta($sql);
    }

    function listadoDetOrdenCobro2($idordencobro) {
        $sql = "select
                    importedoc,
                    formacobro
                    from wc_detalleordencobro
                    where estado = 1
                    and idordencobro = $idordencobro";
        return $this->EjecutaConsulta($sql);
    }

    function getNombreVendedor($idvendedor) {
        $sql = "select
                    concat(a.nombres,' ',a.apellidopaterno,' ',a.apellidomaterno) as vendedor
                    from wc_actor a
                    inner join wc_actorrol ar on ar.idactor = a.idactor
                    inner join wc_rol r on r.idrol = ar.idrol
                    where a.estado = 1
                    and ar.estado = 1
                    and r.estado = 1
                    and r.idrol = 25
                    and a.idactor = $idvendedor";
        return $this->EjecutaConsulta($sql);
    }

    function consultar_producto_de_inventario_segun_bloque($idInventario, $idProducto) {
        $sql = "SELECT detInv.idbloque,bloq.codigo AS 'bloque'
            FROM wc_detalleinventario detInv,wc_bloques bloq
            WHERE detInv.idbloque=bloq.idbloque
            AND detInv.idinventario='" . $idInventario . "'
            AND detInv.idproducto='" . $idProducto . "' AND detInv.estado=1";
        return $this->EjecutaConsulta($sql);
    }

    function listadoDetalladoLetras($cmtEtapa = "", $FechaInicio, $FechaFin, $Principal, $Categoria, $lstZona, $lstRecepcionLetras, $txtIdCliente, $txtIdOrdenVenta, $lstMoneda) {
        $condicion = "wc_detalleordencobro.numeroletra != '' and wc_ordenventa.estado = 1 and wc_ordenventa.esanulado = 0 and wc_detalleordencobro.saldodoc > 0.00 and wc_detalleordencobro.formacobro = 3 and wc_detalleordencobro.renovado=0 and wc_detalleordencobro.situacion = ''";
        if (!empty($FechaInicio))
            $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
        if (!empty($FechaFin))
            $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
        if (!empty($Principal))
            $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;
        if ($lstRecepcionLetras == 1) {
            $condicion .= " and wc_detalleordencobro.recepcionLetras='PA'";
        } else if ($lstRecepcionLetras == 2) {
            $condicion .= " and wc_detalleordencobro.recepcionLetras != 'PA'";
        }
        if ($cmtEtapa == 1) {
            $condicion .= " and wc_ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and wc_ordenventa.fordenventa >= '2020-03-17'";
        }
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro ON wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro ON wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado` = 1", "wc_ordenventa.idmoneda,
                                    wc_ordenventa.idordenventa,
                                    wc_ordenventa.codigov,
                                    wc_ordenventa.fordenventa,
                                    wc_ordenventa.situacion as situacionov,
                                    wc_ordenventa.idvendedor,
                                    concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                                    wc_cliente.razonsocial,
                                    (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                                    wc_ordencobro.situacion as situacionoc,
                                    wc_ordencobro.femision,
                                    wc_ordencobro.importeordencobro,
                                    wc_ordencobro.saldoordencobro,
                                    wc_detalleordencobro.recepcionLetras as recepLetra,
                                    wc_detalleordencobro.*", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");

        return $data;
    }

    function listadoDetalladoLetraspendientexvendedor($FechaInicio, $FechaFin, $fechavencimientoinicio, $fechavencimientofin, $Principal, $Categoria, $lstZona, $txtIdCliente, $txtIdvendedor) {
        $condicion = "wc_detalleordencobro.numeroletra != '' and wc_ordenventa.estado = 1 and wc_ordenventa.esanulado = 0 and wc_detalleordencobro.saldodoc > 0.00 and wc_detalleordencobro.formacobro = 3 and wc_detalleordencobro.renovado=0 and wc_detalleordencobro.situacion = ''";
        if (!empty($FechaInicio))
            $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
        if (!empty($FechaFin))
            $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
        if (!empty($Principal))
            $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($txtIdvendedor))
            $condicion .= ' and wc_ordenventa.idvendedor=' . $txtIdvendedor;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        if (!empty($fechavencimientoinicio))
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . $fechavencimientoinicio . "'";
        if (!empty($fechavencimientofin))
            $condicion .= " and wc_detalleordencobro.fvencimiento <= '" . $fechavencimientofin . "'";
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro ON wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro ON wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado` = 1", "wc_ordenventa.idmoneda,
                                    wc_ordenventa.idordenventa,
                                    wc_ordenventa.codigov,
                                    wc_ordenventa.fordenventa,
                                    wc_ordenventa.situacion as situacionov,
                                    wc_ordenventa.idvendedor,
                                    concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                                    wc_cliente.razonsocial,
                                    (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                                    wc_ordencobro.situacion as situacionoc,
                                    wc_ordencobro.femision,
                                    wc_ordencobro.importeordencobro,
                                    wc_ordencobro.saldoordencobro,
                                    wc_detalleordencobro.recepcionLetras as recepLetra,
                                    wc_detalleordencobro.*", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");

        return $data;
    }

    function resumenContadoGeneral($FechaInicio, $FechaFin) {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and
				wc_ordenventa.`esguiado`=1 and
                            wc_ordenventa.`estado`=1 and
                            wc_ordencobro.`estado`=1 and
                            wc_detalleordencobro.`estado`=1 and
                            wc_detalleordencobro.`situacion`!='reprogramado' and
                            wc_detalleordencobro.`situacion`!='anulado' and
                            wc_detalleordencobro.`situacion`!='extornado' and
                            wc_detalleordencobro.`situacion`!='refinanciado' and
                            wc_detalleordencobro.`situacion`!='protestado' and
                            wc_detalleordencobro.`situacion`!='renovado' and
                            wc_detalleordencobro.`formacobro`='1'";

        if (!empty($FechaInicio))
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $FechaInicio . "'";
        if (!empty($FechaFin))
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $FechaFin . "'";

        $condicion .= ' and wc_categoria.idpadrec in (1, 2)';
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                                INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                                INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                                   wc_ordenventa.idordenventa,
                                   wc_ordenventa.codigov,
                                   wc_ordenventa.fordenventa,
                                   wc_ordenventa.situacion as situacionov,
                                   wc_ordenventa.idvendedor,
                                   concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                                   wc_cliente.razonsocial,
                                   (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                                   wc_ordencobro.situacion as situacionoc,
                                   wc_ordencobro.femision,
                                   wc_ordencobro.importeordencobro,
                                   wc_ordencobro.saldoordencobro,
                                   wc_detalleordencobro.recepcionLetras as recepLetra,
                                   wc_detalleordencobro.*,
                                   wc_ordenventa.importepagado,
                                   wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function resumenCreditosGeneral($FechaInicio, $FechaFin) {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and
				wc_ordenventa.`esguiado`=1 and
                wc_ordenventa.`estado`=1 and
                wc_ordencobro.`estado`=1 and
                wc_detalleordencobro.`estado`=1 and
                wc_detalleordencobro.`situacion`!='reprogramado' and
                wc_detalleordencobro.`situacion`!='anulado' and
                wc_detalleordencobro.`situacion`!='extornado' and
                wc_detalleordencobro.`situacion`!='refinanciado' and
                wc_detalleordencobro.`situacion`!='protestado' and
                wc_detalleordencobro.`situacion`!='renovado' and
                wc_detalleordencobro.`formacobro`='2'";

        if (!empty($FechaInicio))
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $FechaInicio . "'";
        if (!empty($FechaFin))
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $FechaFin . "'";

        $condicion .= ' and wc_categoria.idpadrec in (1, 2)';
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       wc_ordencobro.situacion as situacionoc,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_ordenventa.importepagado,
                       wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function montoPorCobrar($FechaInicio, $Principal, $Categoria, $lstZona, $conDeuda) {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and " .
                "wc_ordenventa.`esguiado`=1 and " .
                "wc_ordenventa.`estado`=1 and " .
                "wc_ordencobro.`estado`=1 and " .
                "wc_detalleordencobro.`estado`=1 and " .
                "wc_detalleordencobro.`situacion`!='reprogramado' and " .
                "wc_detalleordencobro.`situacion`!='anulado' and " .
                "wc_detalleordencobro.`situacion`!='extornado' and " .
                "wc_detalleordencobro.`situacion`!='refinanciado' and " .
                "wc_detalleordencobro.`situacion`!='protestado' and " .
                "wc_detalleordencobro.`situacion`!='renovado'";
        if ($conDeuda == 1) {
            $condicion .= " and wc_detalleordencobro.`situacion`='' and wc_detalleordencobro.`saldodoc`>0";
            //if (!empty($FechaInicio)) $condicion .= " and wc_detalleordencobro.fechagiro<='" . $FechaInicio . "'";
        } else {
            $condicion .= ' and wc_detalleordencobro.`saldodoc`<=0';
            //if (!empty($FechaInicio)) $condicion .= " and wc_detalleordencobro.fechapago <= '" . $FechaInicio . "'";
        }
        if (!empty($FechaInicio))
            $condicion .= " and wc_ordenventa.fordenventa<='" . $FechaInicio . "'";
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;

        $condicion .= (!empty($Principal) ? ' and wc_categoria.idpadrec=' . $Principal : ' and wc_categoria.idpadrec in (1, 2)');

        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "  wc_ordenventa.idordenventa,
                       wc_ordenventa.idmoneda,
                       wc_categoria.idpadrec,
                       sum(wc_detalleordencobro.importedoc) as importedoc,
                       sum(wc_detalleordencobro.saldodoc) as saldodoc,
                       wc_ordenventa.importepagado", $condicion, "", "group by wc_ordenventa.idordenventa order by wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        $data[0]['devuelveSQL'] = $this->devuelveSQL("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "  wc_ordenventa.idordenventa,
                       wc_ordenventa.idmoneda,
                       wc_categoria.idpadrec,
                       sum(wc_detalleordencobro.importedoc) as importedoc,
                       sum(wc_detalleordencobro.saldodoc) as saldodoc,
                       wc_ordenventa.importepagado", $condicion, "", "group by wc_ordenventa.idordenventa order by wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }
    
    function resumenDetalladoCreditos_mirabymejorado($cmtEtapa, $FechaInicio, $FechaFin, $condicioncredito, $vencidas, $diasporvencer = 0) {
        $condicion = "ordenventa.idvendedor NOT IN (136, 241, 152, 184, 264, 59, 391, 445) 
                       AND ordenventa.esguiado = 1 
                       AND ordenventa.estado = 1 
                       AND ordencobro.estado = 1 
                       AND detalleordencobro.estado = 1 
                       AND detalleordencobro.situacion != 'reprogramado' 
                       AND detalleordencobro.situacion != 'anulado' 
                       AND detalleordencobro.situacion != 'extornado' 
                       AND detalleordencobro.situacion != 'refinanciado' 
                       AND detalleordencobro.situacion != 'protestado' 
                       AND detalleordencobro.situacion != 'renovado' 
                       AND detalleordencobro.situacion = '' 
                       AND detalleordencobro.formacobro = '2' 
                       AND detalleordencobro.montoprotesto = 0
                       AND categoria.idpadrec IN (1, 2) ";
        
        if ($cmtEtapa == 1) {
            $condicion .= " and ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and ordenventa.fordenventa >= '2020-03-17'";
        }
        if (!empty($FechaInicio)) {
            $condicion .= " and detalleordencobro.fechagiro >= '" . $FechaInicio . "'";
        }
        if (!empty($FechaFin)) {
            $condicion .= " and detalleordencobro.fechagiro <= '" . $FechaFin . "'";
        }

        if ($condicioncredito == 1) {
            if ($vencidas >= 30 && $vencidas <= 90) { //vencidos en mas 30,60,90 dias
                $fechaVencimiento = strtotime('-' . $vencidas . ' day', strtotime(date('Y-m-d')));
                $fechaVencimiento = date('Y-m-d', $fechaVencimiento);
                $fechahasta = strtotime('+29 day', strtotime($fechaVencimiento));
                $fechahasta = date('Y-m-d', $fechahasta);
                $condicion .= " and (detalleordencobro.fvencimiento>='" . $fechaVencimiento . "' and detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($vencidas > 90) { //vencidos en mas 90 dias
                $fechaVencimiento = strtotime('-91 day', strtotime(date('Y-m-d')));
                $condicion .= " and detalleordencobro.fvencimiento<='" . date('Y-m-d', $fechaVencimiento) . "'";
            } else { //todas vencidas <selecciones>
                $condicion .= " and detalleordencobro.fvencimiento<'" . date('Y-m-d') . "'";
            }
        } else if ($condicioncredito == 2) { //por vencer
            if ($diasporvencer == 30) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-30 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (detalleordencobro.fvencimiento>='" . $fechaDesde . "' and detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer == 60) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-29 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (detalleordencobro.fvencimiento>='" . $fechaDesde . "' and detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer == 90) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-29 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (detalleordencobro.fvencimiento>='" . $fechaDesde . "' and detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer > 90) {
                $fechaDesde = strtotime('+91 day', strtotime(date('Y-m-d')));
                $condicion .= " and detalleordencobro.fvencimiento>='" . date('Y-m-d', $fechaDesde) . "'";
            } else { //todas vencidas <selecciones>
                $condicion .= " and detalleordencobro.fvencimiento>= '" . date('Y-m-d') . "'";
            }
        }

        $data = $this->leeRegistro("wc_ordenventa ordenventa 
                                   INNER JOIN wc_actor actor ON actor.idactor = ordenventa.idvendedor
                                   INNER JOIN wc_cliente cliente ON cliente.idcliente = ordenventa.idcliente
                                   INNER JOIN wc_distrito distrito ON cliente.iddistrito = distrito.iddistrito
                                   INNER JOIN wc_provincia provincia ON distrito.idprovincia = provincia.idprovincia
                                   INNER JOIN wc_departamento departamento ON provincia.iddepartamento = departamento.iddepartamento
                                   INNER JOIN wc_clientezona clientezona ON ordenventa.idclientezona = clientezona.idclientezona
                                   INNER JOIN wc_zona zona ON clientezona.idzona = zona.idzona
                                   INNER JOIN wc_categoria categoria ON zona.idcategoria = categoria.idcategoria
                                   INNER JOIN wc_ordencobro ordencobro ON ordencobro.idordenventa = ordenventa.idordenventa
                                   INNER JOIN wc_detalleordencobro detalleordencobro ON detalleordencobro.idordencobro = ordencobro.idordencobro", 
                                    "ordenventa.idmoneda, 
                                   categoria.idpadrec,  
                                   sum(detalleordencobro.saldodoc) as saldodoc", $condicion, 
                                    "", 
                                    "group by categoria.idpadrec, 
                                  ordenventa.IdMoneda
                                  order by categoria.idpadrec asc");
        return $data;
    }

    function resumenDetalladoCreditos($cmtEtapa = "", $FechaInicio, $FechaFin, $Principal, $Categoria, $lstZona, $condicioncredito, $vencidas, $txtIdCliente, $txtIdOrdenVenta, $lstMoneda, $diasporvencer = 0) {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and
                wc_ordenventa.`esguiado`=1 and
                wc_ordenventa.`estado`=1 and
                wc_ordencobro.`estado`=1 and
                wc_detalleordencobro.`estado`=1 and
                wc_detalleordencobro.`situacion`!='reprogramado' and
                wc_detalleordencobro.`situacion`!='anulado' and
                wc_detalleordencobro.`situacion`!='extornado' and
                wc_detalleordencobro.`situacion`!='refinanciado' and
                wc_detalleordencobro.`situacion`!='protestado' and
                wc_detalleordencobro.`situacion`!='renovado' and
                wc_detalleordencobro.`situacion`='' and
                wc_detalleordencobro.`formacobro`='2' and
                wc_detalleordencobro.`montoprotesto`=0";
//                 and wc_detalleordencobro.referencia=''";

        if ($cmtEtapa == 1) {
            $condicion .= " and wc_ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and wc_ordenventa.fordenventa >= '2020-03-17'";
        }
        if (!empty($FechaInicio))
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $FechaInicio . "'";
        if (!empty($FechaFin))
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $FechaFin . "'";
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;

        $condicion .= (!empty($Principal) ? ' and wc_categoria.idpadrec=' . $Principal : ' and wc_categoria.idpadrec in (1, 2)');

        if ($condicioncredito == 1) {
            if ($vencidas >= 30 && $vencidas <= 90) { //vencidos en mas 30,60,90 dias
                $fechaVencimiento = strtotime('-' . $vencidas . ' day', strtotime(date('Y-m-d')));
                $fechaVencimiento = date('Y-m-d', $fechaVencimiento);
                $fechahasta = strtotime('+29 day', strtotime($fechaVencimiento));
                $fechahasta = date('Y-m-d', $fechahasta);
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaVencimiento . "' and wc_detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($vencidas > 90) { //vencidos en mas 90 dias
                $fechaVencimiento = strtotime('-91 day', strtotime(date('Y-m-d')));
                $condicion .= " and wc_detalleordencobro.fvencimiento<='" . date('Y-m-d', $fechaVencimiento) . "'";
            } else { //todas vencidas <selecciones>
                $condicion .= " and wc_detalleordencobro.fvencimiento<'" . date('Y-m-d') . "'";
            }
        } else if ($condicioncredito == 2) { //por vencer
            if ($diasporvencer == 30) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-30 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaDesde . "' and wc_detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer == 60) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-29 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaDesde . "' and wc_detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer == 90) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-29 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaDesde . "' and wc_detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer > 90) {
                $fechaDesde = strtotime('+91 day', strtotime(date('Y-m-d')));
                $condicion .= " and wc_detalleordencobro.fvencimiento>='" . date('Y-m-d', $fechaDesde) . "'";
            } else { //todas vencidas <selecciones>
                $condicion .= " and wc_detalleordencobro.fvencimiento>= '" . date('Y-m-d') . "'";
            }
        }

        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_distrito` wc_distrito ON wc_cliente.`iddistrito` = wc_distrito.`iddistrito`
                    INNER JOIN `wc_provincia` wc_provincia ON wc_distrito.`idprovincia` = wc_provincia.`idprovincia`
                    INNER JOIN `wc_departamento` wc_departamento ON wc_provincia.`iddepartamento` = wc_departamento.`iddepartamento`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       wc_cliente.direccion,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       wc_ordencobro.situacion as situacionoc,
                       wc_provincia.`nombreprovincia`,
                        wc_departamento.`nombredepartamento`,
                        wc_distrito.`nombredistrito`,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_ordenventa.importepagado,
                       wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function resumenDetalladocontadopendienteporvendedor($FechaInicio, $FechaFin, $fechavencimientoinicio, $fechavencimientofin, $Principal, $Categoria, $lstZona, $txtIdCliente, $txtIdvendedor) {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and
				wc_ordenventa.`esguiado`=1 and
                    wc_ordenventa.`estado`=1 and
                    wc_ordencobro.`estado`=1 and
                    wc_detalleordencobro.`estado`=1 and
                    wc_detalleordencobro.`situacion`!='reprogramado' and
                    wc_detalleordencobro.`situacion`!='anulado' and
                    wc_detalleordencobro.`situacion`!='extornado' and
                    wc_detalleordencobro.`situacion`!='refinanciado' and
                    wc_detalleordencobro.`situacion`!='protestado' and
                    wc_detalleordencobro.`situacion`!='renovado' and
                    wc_detalleordencobro.`situacion`='' and
                    wc_detalleordencobro.`formacobro`='1'";
//                 and wc_detalleordencobro.referencia=''";

        if (!empty($fechavencimientoinicio))
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . $fechavencimientoinicio . "'";
        if (!empty($fechavencimientofin))
            $condicion .= " and wc_detalleordencobro.fvencimiento <= '" . $fechavencimientofin . "'";
        if (!empty($FechaInicio))
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $FechaInicio . "'";
        if (!empty($FechaFin))
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $FechaFin . "'";
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($txtIdvendedor))
            $condicion .= ' and wc_ordenventa.idvendedor=' . $txtIdvendedor;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;

        $condicion .= (!empty($Principal) ? ' and wc_categoria.idpadrec=' . $Principal : ' and wc_categoria.idpadrec in (1, 2)');

        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       wc_ordencobro.situacion as situacionoc,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_ordenventa.importepagado,
                       wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function resumenDetalladoCreditospendienteporvendedor($FechaInicio, $FechaFin, $fechavencimientoinicio, $fechavencimientofin, $Principal, $Categoria, $lstZona, $txtIdCliente, $txtIdvendedor) {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and
				wc_ordenventa.`esguiado`=1 and
                        wc_ordenventa.`estado`=1 and
                        wc_ordencobro.`estado`=1 and
                        wc_detalleordencobro.`estado`=1 and
                        wc_detalleordencobro.`situacion`!='reprogramado' and
                        wc_detalleordencobro.`situacion`!='anulado' and
                        wc_detalleordencobro.`situacion`!='extornado' and
                        wc_detalleordencobro.`situacion`!='refinanciado' and
                        wc_detalleordencobro.`situacion`!='protestado' and
                        wc_detalleordencobro.`situacion`!='renovado' and
                        wc_detalleordencobro.`situacion`='' and
                        wc_detalleordencobro.`formacobro`='2' and
                        wc_detalleordencobro.`montoprotesto`=0";
//                 and wc_detalleordencobro.referencia=''";

        if (!empty($fechavencimientoinicio))
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . $fechavencimientoinicio . "'";
        if (!empty($fechavencimientofin))
            $condicion .= " and wc_detalleordencobro.fvencimiento <= '" . $fechavencimientofin . "'";

        if (!empty($FechaInicio))
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $FechaInicio . "'";
        if (!empty($FechaFin))
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $FechaFin . "'";
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($txtIdvendedor))
            $condicion .= ' and wc_ordenventa.idvendedor=' . $txtIdvendedor;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;

        $condicion .= (!empty($Principal) ? ' and wc_categoria.idpadrec=' . $Principal : ' and wc_categoria.idpadrec in (1, 2)');

        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       wc_ordencobro.situacion as situacionoc,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_ordenventa.importepagado,
                       wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function resumenDetalladoCreditos_sql($FechaInicio, $FechaFin, $Principal, $Categoria, $lstZona, $condicioncredito, $vencidas, $txtIdCliente, $txtIdOrdenVenta, $lstMoneda) {
        $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and
				wc_ordenventa.`esguiado`=1 and
                wc_ordenventa.`estado`=1 and
                wc_ordencobro.`estado`=1 and
                wc_detalleordencobro.`estado`=1 and
                wc_detalleordencobro.`situacion`!='reprogramado' and
                wc_detalleordencobro.`situacion`!='anulado' and
                wc_detalleordencobro.`situacion`!='extornado' and
                wc_detalleordencobro.`situacion`!='refinanciado' and
                wc_detalleordencobro.`situacion`!='protestado' and
                wc_detalleordencobro.`situacion`!='renovado' and
                wc_detalleordencobro.`situacion`='' and
                wc_detalleordencobro.`formacobro`='2' and
                wc_detalleordencobro.referencia=''";
        if (!empty($FechaInicio))
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $FechaInicio . "'";
        if (!empty($FechaFin))
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $FechaFin . "'";
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;

        $condicion .= (!empty($Principal) ? ' and wc_categoria.idpadrec=' . $Principal : ' and wc_categoria.idpadrec in (1, 2)');

        if ($condicioncredito == 1) {
            if ($vencidas >= 30 && $vencidas <= 90) {
                $fechaVencimiento = strtotime('-' . $vencidas . ' day', strtotime(date('Y-m-d')));
                $fechaVencimiento = date('Y-m-d', $fechaVencimiento);
                $fechahasta = strtotime('+29 day', strtotime($fechaVencimiento));
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaVencimiento . "' and wc_detalleordencobro.fvencimiento<='" . date('Y-m-d', $fechahasta) . "')";
            } else if ($vencidas > 90) {
                $fechaVencimiento = strtotime('-91 day', strtotime(date('Y-m-d')));
                $condicion .= " and wc_detalleordencobro.fvencimiento<='" . date('Y-m-d', $fechaVencimiento) . "'";
            } else {
                $condicion .= " and wc_detalleordencobro.fvencimiento<'" . date('Y-m-d') . "'";
            }
        } else if ($condicioncredito == 2) {
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . date('Y-m-d') . "'";
        }

        $data = $this->devuelveSQL("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       wc_ordencobro.situacion as situacionoc,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_ordenventa.importepagado,
                       wc_ordenventa.importedevolucion", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function detalladoCelestium($get_txtFechaInicio, $get_txtFechaFin, $get_lstMoneda) {
        $condicion = "wc_ordenventa.`estado`=1 and wc_ordenventa.`situacion`='pendiente' and wc_detalleordencobro.`situacion`='' and (wc_ordenventa.`idvendedor`=136 or wc_ordenventa.`idvendedor`=241 or wc_ordenventa.`idvendedor`=152 or wc_ordenventa.`idvendedor`=184 or wc_ordenventa.`idvendedor`=264 or wc_ordenventa.`idvendedor`=59)";
        if (!empty($get_txtFechaInicio)) {
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $get_txtFechaInicio . "'";
        }
        if (!empty($get_txtFechaFin)) {
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $get_txtFechaFin . "'";
        }
        if (!empty($get_lstMoneda)) {
            $condicion .= " and wc_ordenventa.idmoneda='" . $get_lstMoneda . "'";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda, wc_ordenventa.idordenventa, wc_ordenventa.importepagado, wc_ordenventa.`idvendedor`", $condicion, "");
        return $data;
    }

    function letrasxfirmar3($numeroletra) {
//        docu.nombredoc in(1,2,4)
//                1=factura
//                2=boleta
//                4=guiaremision
        $sql = "select doc.fechagiro,docu.nombredoc,docu.serie,docu.numdoc,docu.fechadoc,ov.idordenventa,ov.codigov,cl.razonsocial, ov.tipodoccli,cl.ruc,cl.nombrecli,cl.apellido1,cl.apellido2,
                   cl.dni,doc.numeroletra, doc.fvencimiento,doc.iddetalleordencobro,doc.importedoc,
               doc.recepcionLetras,mo.simbolo,mo.idmoneda,dep.iddepartamento,docu.electronico
               from wc_detalleordencobro doc
                        inner join wc_ordencobro  oc on doc.idordencobro=oc.idordencobro
                        inner join wc_ordenventa ov on oc.idordenventa=ov.idordenventa
                        inner join wc_cliente cl on ov.idcliente=cl.idcliente
                        inner join wc_moneda mo on  ov.idmoneda=mo.idmoneda
                        inner join wc_distrito dist on dist.iddistrito = cl.iddistrito
                        inner join wc_provincia pro on pro.idprovincia = dist.idprovincia
                        inner join wc_departamento dep on dep.iddepartamento = pro.iddepartamento
                        inner join wc_documento docu on ov.idordenventa=docu.idordenventa
                        where doc.formacobro=3 and doc.estado=1 and oc.estado=1 and ov.estado=1
                        and docu.nombredoc in(1,2,4)
                        and doc.numeroletra='" . $numeroletra . "'order by docu.nombredoc desc;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    function listaDevolucionesConta($fechaini, $fechafin, $idproducto, $idcliente, $idvendedor, $orden) {
        $sql .= "SELECT detov.serie,dev.fechaaprobada,pro.idproducto,pro.codigopa,pro.nompro,detdev.cantidad,detdev.precio,round((detdev.importe-(detdev.importe*0.18)),2) as importe,round((detdev.importe*0.18),2) as igv,detdev.importe as total,
                     detdev.iddetalledevolucion,dev.iddevolucion,ov.codigov,
                     ov.idordenventa,ov.importeaprobado,CONCAT(REPEAT('0', 6-LENGTH(dev.iddevolucion)), dev.iddevolucion) as devolucion,CASE (dev.registrado) WHEN 1 THEN 'REG.' ELSE ' ' END as registrado,
                     CASE (dev.aprobado) WHEN 1 THEN 'APROB.' ELSE ' ' END as aprobado,
                     dev.importetotal,mn.simbolo,dev.observaciones,dev.idmotivodevolucion,
                     ac.idactor as 'idvendedor',concat(ac.nombres,' ',ac.apellidopaterno,' ',ac.apellidomaterno) as 'nombrevendedor'
                    FROM wc_devolucion dev
                    Inner Join wc_ordenventa ov On dev.idordenventa=ov.idordenventa
                    Inner join wc_detalledevolucion detdev on detdev.iddevolucion= dev.iddevolucion
                    Inner join wc_producto pro on detdev.idproducto=pro.idproducto
                    inner join wc_detalleordenventa detov on detov.idordenventa=ov.idordenventa and detov.idproducto=pro.idproducto and detov.estado=1
                    Inner Join wc_moneda mn On ov.IdMoneda=mn.idmoneda
                    Inner join wc_actor ac on ov.idvendedor=ac.idactor
                    Where dev.estado=1 and dev.registrado=1
                    and detdev.cantidad>0 and detdev.precio>=0.1  and detdev.importe>=0.1";
        if ($fechaini != "" and $fechafin != "") {
            $sql .= " and dev.fechaaprobada between '" . $fechaini . "' and '" . $fechafin . "'";
        }
        if ($idproducto != "") {
            $sql .= " and detdev.idproducto='" . $idproducto . "'";
        }
        if ($idcliente != "") {
            $sql .= " and ov.idcliente='" . $idcliente . "'";
        }
        if ($idvendedor != "") {
            $sql .= " and ov.idvendedor='" . $idvendedor . "'";
        }
        $orderby = "fechaaprobada";
        if ($orden == 1) {
            $orderby = "dev.idordenventa";
        }
        if ($orden == 2) {
            $orderby = "dev.fechaaprobada";
        }
        if ($orden == 3) {
            $orderby = "nombrevendedor";
        }
        $sql .= " order by " . $orderby . " asc;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    
function ventasfacturadonofacturado1($fechaini, $fechafin, $idmoneda, $situacion = "", $importeov, $anuladas) {
        $sql .= "select  ov.*,cliente.dni,cliente.ruc,ov.estado as 'estado_ov',ov.IdMoneda as 'idmoneda',
                    (case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial
                    from wc_ordenventa ov
                    inner join wc_cliente cliente on cliente.idcliente = ov.idcliente and
                    ov.estado=1 and
                    ov.esguiado=1 and
                    ov.vbcreditos=1 and
                    ov.faprobado!='' and
                    ov.idvendedor not  in (136, 241, 152, 184, 264, 59, 391, 445)
        ";
        if ($fechaini != "" and $fechafin != "") {
            $sql .= " and ov.fordenventa>='" . $fechaini . "' and ov.fordenventa<='" . $fechafin . "'";
        }
        if ($idmoneda != "") {
            $sql .= " and ov.idmoneda='" . $idmoneda . "'";
        }
        if ($situacion == 1) {
            $sql .= " and ov.situacion='Pendiente'";
        } else if ($situacion == 2) {
            $sql .= " and ov.situacion='Cancelado'";
        }
        if ($importeov != "") {
            $sql .= " and ov.importeov" . $importeov . "";
        }
        if ($anuladas == 0) {
            $sql .= " and ov.importedevolucion >= ov.importeov";
        }
        
        $sql .= " order by ov.fordenventa asc;";echo $sql."<br>";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    function listar_ovs_de_comprobantesFaltantes($fechaini, $fechafin, $idmoneda, $get_segregado_idordenventas1, $situacion = "", $importeov,$anuladas) {
        $sql .= "select doc.iddocumento,doc.idordenventa,doc.serie,doc.numdoc,doc.nombredoc,doc.fechadoc,ov.codigov,ov.IdMoneda
                    from wc_documento doc,wc_ordenventa ov
                    where doc.nombredoc in('1','2')
                    and doc.idordenventa=ov.idordenventa
                    and doc.estado=1 and (doc.esCargado=1 or doc.esImpreso=1)
                    and CHARACTER_LENGTH(doc.numdoc)<6 and
                    ov.idvendedor not  in (136, 241, 152, 184, 264, 59, 391, 445)";
        if ($fechaini != "" and $fechafin != "") {
            $sql .= " and doc.fechadoc>='" . $fechaini . "' and doc.fechadoc<='" . $fechafin . "'";
        }
        if ($idmoneda != "") {
            $sql .= " and ov.IdMoneda='" . $idmoneda . "'";
        }
        if ($get_segregado_idordenventas1 != "") {
            $sql .= " and ov.idordenventa not in(" . $get_segregado_idordenventas1 . ")";
        }
        if ($situacion == 1) {
            $sql .= " and ov.situacion='Pendiente'";
        } else if ($situacion == 2) {
            $sql .= " and ov.situacion='Cancelado'";
        }
        if ($importeov != "") {
            $sql .= " and ov.importeov" . $importeov . "";
        }
        if ($anuladas == 0) {
            $sql .= " and ov.importedevolucion >= ov.importeov";
        }
        $sql .= " order by doc.idordenventa asc;";echo $sql."<br>";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
    

    function ventasfacturadonofacturado2($idordenventas) {
        if ($idordenventas == "") {
            $sql = "select 'x' as 'finta' from wc_ordenventa limit 0,1;";
            $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        } else {
            $sql = "select  ov.*,cliente.dni,cliente.ruc,ov.estado as 'estado_ov',ov.IdMoneda as 'idmoneda',
                    (case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial
                    from wc_ordenventa ov
                    inner join wc_cliente cliente on cliente.idcliente = ov.idcliente and
                    ov.estado=1 and
                    ov.esguiado=1 and
                    ov.vbcreditos=1 and
                    ov.faprobado!='' and
                    ov.idordenventa in (" . $idordenventas . ")
                    order by ov.fordenventa asc;";
            $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        }

        return $scriptArrayCompleto;
    }

    function reportetotalgeneralcobranzas($filtro = "", $idzona = "", $idcategoriaprincipal = "", $idcategorias = "", $idvendedor = "", $idtipocobranza = "", $fechainicio = "", $fechafinal = "", $octavaNovena = "", $situacion = "", $fechaPagoInicio = "", $fechaPagoFinal = "", $IdCliente = "", $IdOrdenVenta = "") {
        $condicion = "wc_detalleordencobro.`estado`=1 and wc_ordenventa.`esguiado`=1 and wc_ordenventa.`estado`=1 and wc_ordencobro.`estado`=1 and wc_detalleordencobro.`situacion`!='reprogramado'  and wc_detalleordencobro.`situacion`!='anulado'  and wc_detalleordencobro.`situacion`!='extornado' and wc_detalleordencobro.`situacion`!='refinanciado' and wc_detalleordencobro.`situacion`!='protestado' and wc_detalleordencobro.`situacion`!='renovado'  ";
        $condicion .= !empty($idzona) ? " and wc_zona.`idzona`='$idzona' " : "";
        $condicion .= !empty($idcategoriaprincipal) ? " and wc_categoria.`idpadrec`='$idcategoriaprincipal' " : "";
        $condicion .= !empty($idcategorias) ? $idcategorias : "";
        $condicion .= !empty($idvendedor) ? " and wc_actor.`idactor`='$idvendedor' " : "";
        if (!empty($idtipocobranza)) {
            $sql = "Select idtipocobranza,nombre,diaini,diafin
                                From wc_tipocobranza Where estado=0 and ntc='A' and idtipocobranza=" . $idtipocobranza;
            $data1 = $this->EjecutaConsulta($sql);

            $nomtipocobranza = $data[0]['nombre'];
            $diaini = (int) $data1[0]['diaini'];
            $diafin = (int) $data1[0]['diafin'];
            $condicion .= "AND DATEDIFF(NOW(),wc_detalleordencobro.`fvencimiento`) BETWEEN " . $diaini . " and " . $diafin . " ";
            $situacion .= " and wc_detalleordencobro.`situacion`='' ";
        }

        $condicion .= !empty($IdCliente) ? " and wc_cliente.`idcliente`='$IdCliente' " : "";
        $condicion .= !empty($IdOrdenVenta) ? " and wc_ordenventa.`idordenventa`='$IdOrdenVenta' " : "";
        $condicion .= !empty($fechainicio) ? " and wc_detalleordencobro.`fechagiro`>='$fechainicio' " : "";
        $condicion .= !empty($fechafinal) ? " and wc_detalleordencobro.`fechagiro`<='$fechafinal' " : "";
        $condicion .= !empty($fechaPagoInicio) ? " and wc_detalleordencobro.`fechapago`>='$fechaPagoInicio' " : "";
        $condicion .= !empty($fechaPagoFinal) ? " and wc_detalleordencobro.`fechapago`<='$fechaPagoFinal' " : "";
        $condicion .= !empty($octavaNovena) ? $octavaNovena : "";
        $condicion .= !empty($situacion) ? $situacion : "";
        $condicion .= !empty($filtro) ? " and  " . $filtro . " " : "";

        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa
                            INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.IdMoneda=wc_moneda.idmoneda
                            INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                            INNER JOIN `wc_actor` wc_actor ON wc_ordenventa.`idvendedor` = wc_actor.`idactor`
                            INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                            INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                            INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                            INNER JOIN `wc_categoria` categoriazona ON categoriazona.`idcategoria` = wc_categoria.`idpadrec`
                            inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                            inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_actor.`apellidomaterno`,
                             wc_moneda.`idmoneda`,
                             wc_moneda.`nombre` as nommoneda,
                             wc_moneda.`simbolo`,
                             categoriazona.`nombrec`,
                             wc_categoria.`idpadrec`,
                             sum(wc_detalleordencobro.`saldodoc`) as saldodoc,
                             sum(wc_detalleordencobro.`importedoc`) as importedoc,
                             sum(wc_detalleordencobro.`montoprotesto`) as montoprotesto", $condicion, "", "group by wc_categoria.`idpadrec`, wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc"
        );
        return $data;
    }

    function ventasfacturadonofacturado1_ranking($fechaini, $fechafin, $idmoneda, $mostrarclientes) {
        $sql .= "select  sum(importeov) as 'totalcompradoovs',sum(importedevolucion) as 'totaldevuelto',cliente.dni,cliente.ruc,ov.estado as 'estado_ov',ov.IdMoneda as 'idmoneda',ov.idcliente,
        (case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial
        from wc_ordenventa ov
        inner join wc_cliente cliente on cliente.idcliente = ov.idcliente and
        ov.estado=1 and
        ov.esguiado=1 and
        ov.vbcreditos=1 and
        ov.faprobado!=''";
        $sql .= " and ov.idvendedor not  in (136, 241, 152, 184, 264, 59, 391, 445)";
        if ($fechaini != "" and $fechafin != "") {
            $sql .= " and ov.fordenventa>='" . $fechaini . "' and ov.fordenventa<='" . $fechafin . "'";
        }

        if ($idmoneda != "") {
            $sql .= " and ov.idmoneda='" . $idmoneda . "'";
        }
        $sql .= "group by ov.idcliente
        order by totalcompradoovs desc  limit 0," . $mostrarclientes . ";";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    function ventasfacturadonofacturado2_ranking($fechaini, $fechafin, $idcliente) {
        if ($idcliente == "") {
            $sql = "select 'x' as 'finta' from wc_ordenventa limit 0,1;";
            $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        } else {
            $sql = "select  ov.*,cliente.dni,cliente.ruc,ov.estado as 'estado_ov',ov.IdMoneda as 'idmoneda',
                    (case when cliente.razonsocial is null then concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) else cliente.razonsocial end) as razonsocial
                    from wc_ordenventa ov
                    inner join wc_cliente cliente on cliente.idcliente = ov.idcliente and
                    ov.estado=1 and
                    ov.esguiado=1 and
                    ov.vbcreditos=1 and
                    ov.faprobado!='' and
                    ov.idcliente='" . $idcliente . "'
                    and ov.fordenventa>='" . $fechaini . "' and ov.fordenventa<='" . $fechafin . "'
                    order by ov.importeov desc";
            $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        }

        return $scriptArrayCompleto;
    }

    function reporteKardexProduccionDetallado($txtFechaInicio, $txtFechaFinal, $idProducto, $idTipoMovimiento, $idTipoOperacion, $condiciones = "") {
        $filtro = "  dm.estado=1 and m.estado=1 ";
        $filtro .= !empty($txtFechaInicio) ? " and m.fechamovimiento>='$txtFechaInicio' " : "";
        $filtro .= !empty($txtFechaFinal) ? " and m.fechamovimiento<='$txtFechaFinal' " : "";
        $filtro .= !empty($idProducto) ? " and dm.idproducto='$idProducto' " : "";
        $filtro .= !empty($idTipoOperacion) ? " and m.conceptomovimiento='$idTipoOperacion' " : "";
        $filtro .= !empty($idTipoMovimiento) ? " and m.tipomovimiento='$idTipoMovimiento' " : "";
        if (!empty($condiciones)) {
            $filtro .= " and " . $condiciones;
        }
        $order = "Order By m.fechamovimiento,m.idmovimiento asc";
        $data = $this->leeRegistro(
                "wc_detallemovimiento dm
                                Inner Join wc_movimiento m On dm.idmovimiento=m.idmovimiento
                                Left Join wc_ordenventa ov On ov.idordenventa=m.idordenventa
                                Left Join wc_ordencompra oc On oc.idordencompra=m.idordencompra
                                Inner Join wc_movimientotipo mt On m.tipomovimiento=mt.idmovimientotipo
                                left Join wc_cliente c On ov.idcliente=c.idcliente
                                left Join wc_proveedor p On oc.idproveedor=p.idproveedor
                                left Join wc_devolucion d On m.iddevolucion=d.iddevolucion
                                left join wc_tipooperacion tio On tio.idtipooperacion=m.conceptomovimiento
                                ", "ov.idordenventa, ov.codigov, ov.importeov, oc.codigooc,oc.idordencompra,m.fechamovimiento as Fecha,mt.nombre as 'Tipo Movimiento',tio.nombre as 'Concepto Movimiento',
                                CASE WHEN ov.codigov<>'Null' Then c.razonsocial WHEN oc.codigooc<>'Null' Then p.razonsocialp Else 'Mov. Interno' END as 'Razon Social',
                                CASE WHEN m.iddevolucion<>0 THEN 'Devolucion' ELSE ' ' END as Devolucion,
                                dm.pu as 'Precio',dm.cantidad,ROUND(dm.stockactual,0) as Saldo,dm.importe as 'Monto'", $filtro, "", $order
        );
        return $data;
    }

    function reporteKardexProduccionDetallado2($diproducto, $fecha) {
        $data = $this->EjecutaConsulta("select  dmovimiento.idproducto as idproducto,
                                                '02' as 'conceptomovimiento',
                                                movimiento.tipomovimiento as 'tipomovimiento',
                                                concat('02-', movimiento.idmovimiento) as idordenventa,
                                                0 as codigov,
                                                0 as cantidaddevolucion,
                                                dmovimiento.cantidad,
                                                movimiento.fechamovimiento as fecha
                                                from wc_detallemovimiento dmovimiento
                                                inner join wc_movimiento movimiento on movimiento.idmovimiento = dmovimiento.idmovimiento and 
                                                                                       movimiento.estado=1 and 
                                                                                       iddevolucion=0 and
                                                                                       (movimiento.idordenventa is null and movimiento.idordencompra is null) or
                                                                                       (movimiento.idordenventa = '' and movimiento.idordencompra = '') or
                                                                                       (movimiento.idordenventa = '' and movimiento.idordencompra = '')
                                                where dmovimiento.idproducto='$diproducto' and dmovimiento.estado=1 and movimiento.fechamovimiento>='$fecha'
                                        union all
                                        select  dov.idproducto as idproducto,
                                            '01' as 'conceptomovimiento',
                                            '0' as 'tipomovimiento',
                                            ov.idordenventa,
                                            ov.codigov,
                                            sum(ddevolucion.cantidad) as cantidaddevolucion,
                                            dov.cantdespacho as cantidad,
                                            ov.fordenventa as fecha
                                            from wc_detalleordenventa dov
                                            inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa and ov.vbcreditos=1 = 1 and ov.estado=1
                                            inner join wc_movimiento movimiento on movimiento.idordenventa = ov.idordenventa and movimiento.estado=1 and movimiento.idordencompra is null and movimiento.iddevolucion = 0 and movimiento.fechamovimiento>='$fecha'
                                            left join wc_devolucion devolucion on devolucion.idordenventa = ov.idordenventa and devolucion.estado=1 and devolucion.aprobado=1 and devolucion.registrado = 1
                                            left join wc_detalledevolucion ddevolucion on ddevolucion.iddevolucion = devolucion.iddevolucion and ddevolucion.estado=1 and ddevolucion.idproducto='$diproducto'
                                            where dov.idproducto='$diproducto' and dov.estado=1
                                            group by idordenventa order by fecha, idordenventa asc");
        return $data;
    }

    function listadoLetrassinenviaralbanco($FechaInicio, $FechaFin, $Principal, $Categoria, $lstZona, $lstMoneda, $idcliente, $idvendedor) {
        $condicion = "wc_detalleordencobro.numeroletra != '' and wc_ordenventa.estado = 1 and wc_ordenventa.esanulado = 0 and wc_detalleordencobro.saldodoc > 0.00 and wc_detalleordencobro.formacobro = 3 and wc_detalleordencobro.renovado=0 and wc_detalleordencobro.situacion = ''";
        if (!empty($FechaInicio))
            $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
        if (!empty($FechaFin))
            $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
        if (!empty($Principal))
            $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;

        if (!empty($idcliente))
            $condicion .= ' and wc_ordenventa.idcliente=' . $idcliente;
        if (!empty($idvendedor))
            $condicion .= ' and wc_ordenventa.idvendedor=' . $idvendedor;
        $condicion .= " and wc_detalleordencobro.recepcionLetras != 'PA'";
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                                    INNER JOIN `wc_distrito` wc_distrito ON wc_cliente.`iddistrito` = wc_distrito.`iddistrito`
                                    INNER JOIN `wc_provincia` wc_provincia ON wc_distrito.`idprovincia` = wc_provincia.`idprovincia`
                                    INNER JOIN `wc_departamento` wc_departamento ON wc_provincia.`iddepartamento` = wc_departamento.`iddepartamento`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro ON wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro ON wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado` = 1",
                                    "wc_ordenventa.idmoneda,
                                    wc_ordenventa.idordenventa,
                                    wc_ordenventa.codigov,
                                    wc_ordenventa.fordenventa,
                                    wc_ordenventa.fechadespacho,
                                    wc_ordenventa.situacion as situacionov,
                                    wc_ordenventa.idvendedor,
                                    concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                                    wc_cliente.razonsocial,
                                    wc_cliente.direccion,
                                    (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                                    wc_provincia.`nombreprovincia`,
                                    wc_departamento.`nombredepartamento`,
                                    wc_distrito.`nombredistrito`,
                                    wc_ordencobro.situacion as situacionoc,
                                    wc_ordencobro.femision,
                                    wc_ordencobro.importeordencobro,
                                    wc_ordencobro.saldoordencobro,
                                    wc_detalleordencobro.recepcionLetras as recepLetra,
                                    wc_detalleordencobro.*", $condicion, "wc_detalleordencobro.fechagiro, wc_detalleordencobro.fvencimiento asc");

        return $data;
    }

    function listadoLetrassinenviaralbancoevaluacion($FechaInicio, $FechaFin, $Principal, $Categoria, $lstZona, $lstMoneda, $estadoevaluacion) {
        if ($estadoevaluacion == "1") { // en evaluacion
            $condicion = "wc_detalleordencobro.numeroletra != '' and wc_ordenventa.estado = 1 and wc_ordenventa.esanulado = 0 and wc_detalleordencobro.saldodoc > 0.00 and wc_detalleordencobro.formacobro = 3 and wc_detalleordencobro.renovado=0 and wc_detalleordencobro.situacion = ''";
            if (!empty($FechaInicio))
                $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
            if (!empty($FechaFin))
                $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
            if (!empty($Principal))
                $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
            if (!empty($Categoria))
                $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
            if (!empty($lstZona))
                $condicion .= ' and wc_zona.idzona=' . $lstZona;
            if (!empty($lstMoneda))
                $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
            $condicion .= " and wc_detalleordencobro.recepcionLetras != 'PA' and wc_detalleordencobro.evaluacion='" . $estadoevaluacion . "'";
        }
        if ($estadoevaluacion == "2") { // se aprobo la evaluacion
            $condicion = "wc_detalleordencobro.numeroletra != '' and wc_ordenventa.estado = 1 and wc_ordenventa.esanulado = 0 and wc_detalleordencobro.saldodoc > 0.00 and wc_detalleordencobro.formacobro = 3";
            if (!empty($FechaInicio))
                $condicion .= " and wc_ordenventa.fechacreacion >= '$FechaInicio'";
            if (!empty($FechaFin))
                $condicion .= " and wc_ordenventa.fechacreacion <= '$FechaFin'";
            if (!empty($Principal))
                $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
            if (!empty($Categoria))
                $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
            if (!empty($lstZona))
                $condicion .= ' and wc_zona.idzona=' . $lstZona;
            if (!empty($lstMoneda))
                $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
            $condicion .= " and wc_detalleordencobro.recepcionLetras = 'PA' and wc_detalleordencobro.evaluacion='" . $estadoevaluacion . "'";
        }

        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro ON wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro ON wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado` = 1", "wc_ordenventa.idmoneda,
                                    wc_ordenventa.idordenventa,
                                    wc_ordenventa.codigov,
                                    wc_ordenventa.fordenventa,
                                    wc_ordenventa.situacion as situacionov,
                                    wc_ordenventa.idvendedor,
                                    concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                                    wc_cliente.razonsocial,
                                    (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                                    wc_ordencobro.situacion as situacionoc,
                                    wc_ordencobro.femision,
                                    wc_ordencobro.importeordencobro,
                                    wc_ordencobro.saldoordencobro,
                                    wc_detalleordencobro.recepcionLetras as recepLetra,
                                    wc_detalleordencobro.*", $condicion, "wc_detalleordencobro.fechagiro, wc_detalleordencobro.fvencimiento asc");

        return $data;
    }

    function listaSaldosIniciales($anio, $idproducto, $filtro1, $positivos_negativos) {
        $sql .= "select pro.idproducto,pro.codigopa,pro.nompro,sali.fechasaldo,sali.cantidad1,sali.idmoneda,sali.simulacro,
                    sali.estado,sali.costounitario
                    from wc_saldosiniciales sali
                    inner join wc_producto pro on sali.idproducto=pro.idproducto where sali.estado=1 ";
        if ($anio != "todos") {
            $sql .= " and sali.fechasaldo like '%$anio%'";
        }


        if (!empty($idproducto)) {
            $sql .= " and sali.idproducto='" . $idproducto . "'";
        }

        if ($filtro1 == 1) {
            $sql .= " and sali.simulacro=1";
        }
        if ($filtro1 == 2) {
            $sql .= " and sali.simulacro=0";
        }

        if ($positivos_negativos != "todos") {
            if ($positivos_negativos == 1) {
                $sql .= " and sali.cantidad1>0";
            };
            if ($positivos_negativos == 2) {
                $sql .= " and sali.cantidad1<0";
            };
            if ($positivos_negativos == 3) {
                $sql .= " and sali.cantidad1=0";
            };
        }
        $sql .= " order by sali.idproducto,sali.fechasaldo asc;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    function resumenDetalladoCreditos_ventasvencidasvendedor($FechaInicio_vencimiento, $FechaFin_vencimiento, $Principal, $Categoria, $lstZona, $condicioncredito, $vencidas, $txtIdCliente, $txtIdOrdenVenta, $txtIdVendedor, $lstMoneda, $diasporvencer = 0) {
        if (!empty($txtIdVendedor)) {
            $condicion = "(wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and wc_ordenventa.`idvendedor`='" . $txtIdVendedor . "') and ";
        } else {
            $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and ";
        }

        $condicion .= "wc_ordenventa.`esguiado`=1 and
                wc_ordenventa.`estado`=1 and
                wc_ordencobro.`estado`=1 and
                wc_detalleordencobro.`estado`=1 and
                wc_detalleordencobro.`situacion`!='reprogramado' and
                wc_detalleordencobro.`situacion`!='anulado' and
                wc_detalleordencobro.`situacion`!='extornado' and
                wc_detalleordencobro.`situacion`!='refinanciado' and
                wc_detalleordencobro.`situacion`!='protestado' and
                wc_detalleordencobro.`situacion`!='renovado' and
                wc_detalleordencobro.`situacion`='' and
                wc_detalleordencobro.`formacobro`='2' and
                wc_detalleordencobro.`montoprotesto`=0";
//                 and wc_detalleordencobro.referencia=''";


        if (!empty($FechaInicio_vencimiento))
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . $FechaInicio_vencimiento . "'";
        if (!empty($FechaFin_vencimiento))
            $condicion .= " and wc_detalleordencobro.fvencimiento <= '" . $FechaFin_vencimiento . "'";
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;

        $condicion .= (!empty($Principal) ? ' and wc_categoria.idpadrec=' . $Principal : ' and wc_categoria.idpadrec in (1, 2)');

        if ($condicioncredito == 1) {
            if ($vencidas >= 30 && $vencidas <= 90) { //vencidos en mas 30,60,90 dias
                $fechaVencimiento = strtotime('-' . $vencidas . ' day', strtotime(date('Y-m-d')));
                $fechaVencimiento = date('Y-m-d', $fechaVencimiento);
                $fechahasta = strtotime('+29 day', strtotime($fechaVencimiento));
                $fechahasta = date('Y-m-d', $fechahasta);
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaVencimiento . "' and wc_detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($vencidas > 90) { //vencidos en mas 90 dias
                $fechaVencimiento = strtotime('-91 day', strtotime(date('Y-m-d')));
                $condicion .= " and wc_detalleordencobro.fvencimiento<='" . date('Y-m-d', $fechaVencimiento) . "'";
            } else { //todas vencidas <selecciones>
                $condicion .= " and wc_detalleordencobro.fvencimiento<'" . date('Y-m-d') . "'";
            }
        } else if ($condicioncredito == 2) { //por vencer
            if ($diasporvencer == 30) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-30 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaDesde . "' and wc_detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer == 60) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-29 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaDesde . "' and wc_detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer == 90) {
                $fechahasta = strtotime('+' . $diasporvencer . ' day', strtotime(date('Y-m-d')));
                $fechahasta = date('Y-m-d', $fechahasta);
                $fechaDesde = strtotime('-29 day', strtotime($fechahasta));
                $fechaDesde = date('Y-m-d', $fechaDesde);
                $condicion .= " and (wc_detalleordencobro.fvencimiento>='" . $fechaDesde . "' and wc_detalleordencobro.fvencimiento<='" . $fechahasta . "')";
            } else if ($diasporvencer > 90) {
                $fechaDesde = strtotime('+91 day', strtotime(date('Y-m-d')));
                $condicion .= " and wc_detalleordencobro.fvencimiento>='" . date('Y-m-d', $fechaDesde) . "'";
            } else { //todas vencidas <selecciones>
                $condicion .= " and wc_detalleordencobro.fvencimiento>= '" . date('Y-m-d') . "'";
            }
        }

        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       (case when wc_clientezona.direccion_despacho_contacto='' then  wc_clientezona.direccion_fiscal else wc_clientezona.direccion_despacho_contacto end) as 'direccion',
                       wc_ordencobro.situacion as situacionoc,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_ordenventa.importepagado,
                       wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function resumenDetalladoContado_ventasvencidasvendedor($FechaInicio_vencimiento, $FechaFin_vencimiento, $Principal, $Categoria, $lstZona, $condicioncredito, $vencidas, $txtIdCliente, $txtIdOrdenVenta, $txtIdVendedor, $lstMoneda, $diasporvencer = 0, $get_lstEstado) {
        if (!empty($txtIdVendedor)) {
            $condicion = "(wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and wc_ordenventa.`idvendedor`='" . $txtIdVendedor . "') and ";
        } else {
            $condicion = "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445) and ";
        }

        $condicion .= "wc_ordenventa.`esguiado`=1 and
                wc_ordenventa.`estado`=1 and
                wc_ordencobro.`estado`=1 and
                wc_detalleordencobro.`estado`=1 and
                wc_detalleordencobro.`situacion`!='reprogramado' and
                wc_detalleordencobro.`situacion`!='anulado' and
                wc_detalleordencobro.`situacion`!='extornado' and
                wc_detalleordencobro.`situacion`!='refinanciado' and
                wc_detalleordencobro.`situacion`!='protestado' and
                wc_detalleordencobro.`situacion`!='renovado' and
                wc_detalleordencobro.`situacion`='' and
                wc_detalleordencobro.`formacobro`='1' and
                wc_detalleordencobro.`montoprotesto`=0";
//                 and wc_detalleordencobro.referencia=''";


        if ($get_lstEstado == '' and ! empty($FechaInicio_vencimiento) and ! empty($FechaFin_vencimiento)) {
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . $FechaInicio_vencimiento . "'";
            $condicion .= " and wc_detalleordencobro.fvencimiento < '" . $FechaFin_vencimiento . "'";
        }
        if ($get_lstEstado == 1 and empty($FechaInicio_vencimiento) and empty($FechaFin_vencimiento)) {
            $condicion .= " and wc_detalleordencobro.fvencimiento < '" . date('Y-m-d') . "'";
        }
        if ($get_lstEstado == 2 and empty($FechaInicio_vencimiento) and empty($FechaFin_vencimiento)) {
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . date('Y-m-d') . "'";
        }
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;



        $condicion .= (!empty($Principal) ? ' and wc_categoria.idpadrec=' . $Principal : ' and wc_categoria.idpadrec in (1, 2)');
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       (case when wc_clientezona.direccion_despacho_contacto='' then  wc_clientezona.direccion_fiscal else wc_clientezona.direccion_despacho_contacto end) as 'direccion',
                       wc_ordencobro.situacion as situacionoc,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_ordenventa.importepagado,
                       wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

    function listadoDetalladoLetras_ventasvencidasvendedor($FechaInicio_vencimiento, $FechaFin_vencimiento, $Principal, $Categoria, $lstZona, $lstRecepcionLetras, $txtIdCliente, $get_txtIdVendedor, $txtIdOrdenVenta, $lstMoneda, $get_lstEstado) {
        $condicion = "wc_detalleordencobro.numeroletra != '' and wc_ordenventa.estado = 1 and wc_ordenventa.esanulado = 0 and wc_detalleordencobro.saldodoc > 0.00 and wc_detalleordencobro.formacobro = 3 and wc_detalleordencobro.situacion = ''";
        if ($get_lstEstado == '' and ! empty($FechaInicio_vencimiento) and ! empty($FechaFin_vencimiento)) {
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . $FechaInicio_vencimiento . "'";
            $condicion .= " and wc_detalleordencobro.fvencimiento < '" . $FechaFin_vencimiento . "'";
        }
        if ($get_lstEstado == 1 and empty($FechaInicio_vencimiento) and empty($FechaFin_vencimiento)) {
            $condicion .= " and wc_detalleordencobro.fvencimiento < '" . date('Y-m-d') . "'";
        }
        if ($get_lstEstado == 2 and empty($FechaInicio_vencimiento) and empty($FechaFin_vencimiento)) {
            $condicion .= " and wc_detalleordencobro.fvencimiento >= '" . date('Y-m-d') . "'";
        }

        if (!empty($Principal))
            $condicion .= ' and wc_categoria.idpadrec=' . $Principal;
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;
        if (!empty($get_txtIdVendedor))
            $condicion .= ' and wc_ordenventa.idvendedor=' . $get_txtIdVendedor;
        if (empty($lstRecepcionLetras)) {
            $condicion .= " and wc_detalleordencobro.recepcionLetras != ''";
        }
        if ($lstRecepcionLetras == 1) {
            $condicion .= " and wc_detalleordencobro.recepcionLetras='PA'";
        }
        if ($lstRecepcionLetras == 2) {
            $condicion .= " and wc_detalleordencobro.recepcionLetras != 'PA'";
        }

        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro ON wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado` = 1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro ON wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado` = 1", "wc_ordenventa.idmoneda,
                                    wc_ordenventa.idordenventa,
                                    wc_ordenventa.codigov,
                                    wc_ordenventa.fordenventa,
                                    wc_ordenventa.situacion as situacionov,
                                    wc_ordenventa.idvendedor,
                                    concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                                    wc_cliente.razonsocial,
                                    (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                                    wc_ordencobro.situacion as situacionoc,
                                    wc_ordencobro.femision,
                                    wc_ordencobro.importeordencobro,
                                    wc_ordencobro.saldoordencobro,
                                    wc_detalleordencobro.recepcionLetras as recepLetra,
                                    (case when wc_clientezona.direccion_despacho_contacto='' then  wc_clientezona.direccion_fiscal else wc_clientezona.direccion_despacho_contacto end) as 'direccion',
                                    wc_detalleordencobro.*", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");

        return $data;
    }

    function ventaspendientesvendedor($url_idcliente, $url_idactor, $url_txtFechaInicioGuiado, $url_txtFechaFinalGuiado, $url_txtFechaInicioDespacho, $url_txtFechaFinalDespacho, $url_idordenventa, $url_idmoneda) {
        $sql = "select
                    vendedor.idactor as 'idvendedor'
                    ,concat(vendedor.nombres, ' ', vendedor.apellidopaterno, ' ', vendedor.apellidomaterno) as nombrevendedor1
                    ,vendedor.nombrecompleto as nombrevendedor2
                    ,cliente.idcliente
                    ,cliente.ruc
                    ,cliente.dni
                    ,concat(cliente.nombrecli, ' ', cliente.apellido1, ' ', cliente.apellido2) as 'nombrecliente'
                    ,cliente.razonsocial
                    ,wc_provincia.`nombreprovincia`
                    ,wc_departamento.`nombredepartamento`
                    ,wc_distrito.`nombredistrito`
                    ,ovcab.codigov
                    ,ovcab.fordenventa
                    ,ovcab.fechadespacho
                    ,ovcab.fechavencimiento
                    ,ovcab.situacion
                    ,round(gasto1.importegasto,2) as 'importeinicial'
                    ,round(ingreso.montoasignado,2) as 'montoasignado'
                    ,round(devolucion.montodevuelto,2) as 'montodevuelto'
                    ,round(gasto2.importegasto,2) as 'gastosadicionales'
                    ,ovcab.idmoneda
                    from wc_ordenventa ovcab
                    inner join wc_ordencobro occab on occab.idordenventa=ovcab.idordenventa and occab.estado=1
                    inner join wc_detalleordencobro ocdet on occab.idordencobro=ocdet.idordencobro and ocdet.situacion=''  and ocdet.estado=1
                    inner join wc_cliente cliente on cliente.idcliente=ovcab.idcliente";
        $sql .= !empty($url_idcliente) ? " and cliente.idcliente='" . $url_idcliente . "'" : "";
        $sql .= " INNER JOIN `wc_distrito` wc_distrito ON cliente.`iddistrito` = wc_distrito.`iddistrito`
                 INNER JOIN `wc_provincia` wc_provincia ON wc_distrito.`idprovincia` = wc_provincia.`idprovincia`
                 INNER JOIN `wc_departamento` wc_departamento ON wc_provincia.`iddepartamento` = wc_departamento.`iddepartamento`";

        $sql .= " inner join wc_actor vendedor on vendedor.idactor=ovCab.idvendedor";
        $sql .= !empty($url_idactor) ? " and vendedor.idactor='" . $url_idactor . "'" : "";
        $sql .= " inner join (select sum(importegasto) as 'importegasto',idordenventa,estado from wc_ordengasto where idtipogasto in(7,9) and estado=1 group by idordenventa) as gasto1 on gasto1.idordenventa=ovcab.idordenventa
                    left join (select sum(importegasto) as 'importegasto',idordenventa from wc_ordengasto where idtipogasto not in(7,9) and estado=1 group by idordenventa) as gasto2 on gasto2.idordenventa=ovcab.idordenventa
                    left join (select sum(montoasignado) as 'montoasignado',idordenventa from wc_ingresos where estado=1 and estado=1 group by idordenventa) as ingreso on ingreso.idordenventa=ovcab.idordenventa
                    left join (select sum(importetotal) as 'montodevuelto',idordenventa from wc_devolucion where aprobado=1 and estado=1 group by idordenventa) as devolucion on devolucion.idordenventa=ovcab.idordenventa
                    where ovcab.estado=1
                     and ovcab.vbalmacen=1
                     and ovcab.vbcobranzas=1
                     and ovcab.vbcreditos=1
                     and ovcab.vbventas=1";
        $sql .= !empty($url_txtFechaInicioGuiado) ? " and ovcab.fordenventa>='" . $url_txtFechaInicioGuiado . "'" : "";
        $sql .= !empty($url_txtFechaFinalGuiado) ? " and ovcab.fordenventa<='" . $url_txtFechaFinalGuiado . "'" : "";
        $sql .= !empty($url_txtFechaInicioDespacho) ? " and ovcab.fechadespacho>='" . $url_txtFechaInicioDespacho . "'" : "";
        $sql .= !empty($url_txtFechaFinalDespacho) ? " and ovcab.fechadespacho<='" . $url_txtFechaFinalDespacho . "'" : "";
        $sql .= !empty($url_idordenventa) ? " and ovcab.idordenventa='" . $url_idordenventa . "'" : "";
        $sql .= !empty($url_idmoneda) ? " and ovcab.idmoneda='" . $url_idmoneda . "'" : "";
        $sql .= " group by gasto1.idordenventa
                 order by ovcab.idvendedor,ovcab.idcliente,ovcab.fordenventa asc;";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    function DetalladoContadoFormato2($cmtEtapa = "", $FechaInicio, $FechaFin, $Principal, $Categoria, $lstZona, $condicioncredito, $vencidas, $txtIdCliente, $txtIdOrdenVenta, $lstMoneda, $diasporvencer = 0, $categoriaPrincipal) {
        $condicion = "wc_detalleordencobro.situacion = '' and "
                . "wc_detalleordencobro.estado = 1 and "
                . "wc_ordencobro.estado = 1 and "
                . "wc_detalleordencobro.saldodoc > 0 and "
                . "wc_ordenventa.estado = 1 and "
                . "wc_ordenventa.esanulado = 0 and "
                . "wc_detalleordencobro.formacobro='1' and "
                . "wc_ordenventa.`idvendedor` not in (136, 241, 152, 184, 264, 59, 391, 445, 540, 557, 558, 554)";

        if ($cmtEtapa == 1) {
            $condicion .= " and wc_ordenventa.fordenventa <= '2020-03-16'";
        } else if ($cmtEtapa == 2) {
            $condicion .= " and wc_ordenventa.fordenventa >= '2020-03-17'";
        }
        if (!empty($categoriaPrincipal))
            $condicion .= " and wc_categoria.`idpadrec`='" . $categoriaPrincipal . "'";
        if (!empty($FechaInicio))
            $condicion .= " and wc_detalleordencobro.fechagiro >= '" . $FechaInicio . "'";
        if (!empty($FechaFin))
            $condicion .= " and wc_detalleordencobro.fechagiro <= '" . $FechaFin . "'";
        if (!empty($Categoria))
            $condicion .= ' and wc_categoria.idcategoria=' . $Categoria;
        if (!empty($lstZona))
            $condicion .= ' and wc_zona.idzona=' . $lstZona;
        if (!empty($lstMoneda))
            $condicion .= ' and wc_ordenventa.IdMoneda=' . $lstMoneda;
        if (!empty($txtIdCliente))
            $condicion .= ' and wc_cliente.idcliente=' . $txtIdCliente;
        if (!empty($txtIdOrdenVenta))
            $condicion .= ' and wc_ordenventa.idordenventa=' . $txtIdOrdenVenta;



        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                    INNER JOIN `wc_distrito` wc_distrito ON wc_cliente.`iddistrito` = wc_distrito.`iddistrito`
                    INNER JOIN `wc_provincia` wc_provincia ON wc_distrito.`idprovincia` = wc_provincia.`idprovincia`
                    INNER JOIN `wc_departamento` wc_departamento ON wc_provincia.`iddepartamento` = wc_departamento.`iddepartamento`
                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`", "wc_ordenventa.idmoneda,
                       wc_ordenventa.idordenventa,
                       wc_ordenventa.codigov,
                       wc_ordenventa.fordenventa,
                       wc_ordenventa.situacion as situacionov,
                       wc_ordenventa.idvendedor,
                       concat(wc_actor.nombres, ' ', wc_actor.apellidopaterno, ' ', wc_actor.apellidomaterno) as vendedor,
                       wc_cliente.razonsocial,
                       wc_cliente.direccion,
                       (case when wc_cliente.ruc is null then wc_cliente.dni else wc_cliente.ruc end) as ruc,
                       wc_ordencobro.situacion as situacionoc,
                       wc_provincia.`nombreprovincia`,
                        wc_departamento.`nombredepartamento`,
                        wc_distrito.`nombredistrito`,
                       wc_ordencobro.femision,
                       wc_ordencobro.importeordencobro,
                       wc_ordencobro.saldoordencobro,
                       wc_detalleordencobro.recepcionLetras as recepLetra,
                       wc_detalleordencobro.*,
                       wc_ordenventa.importepagado,
                       wc_categoria.idpadrec", $condicion, "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        return $data;
    }

}
?>

