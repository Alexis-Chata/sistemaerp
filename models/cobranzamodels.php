<?php

class Cobranza extends Applicationbase {
    
    function listarGuias($fecini, $fecfin) {
        $condicion = "ov.estado=1";
        if (!empty($fecini)) {
            $condicion .= " and ov.fordenventa>='".$fecini."'";
        }
        if (!empty($fecfin)) {
            $condicion .= " and ov.fordenventa<='".$fecfin."'";
        }
        
        $ordenCompra = $this->leeRegistro('wc_ordenventa ov
                inner join wc_cliente c on c.idcliente = ov.idcliente
                left join wc_moneda m on m.idmoneda = ov.idmoneda',
                "ov.idordenventa, ov.codigov, ov.fordenventa, ov.importeov, ov.observaciones, c.razonsocial, c.ruc, m.simbolo", $condicion, "");
        return $ordenCompra;
    }

    function creditoscreadosxprotestos($txtFechaInicio, $txtFechaFinal, $txtOrdenVenta, $txtCliente, $txtVendedor, $txtPrincipal, $txtCategoria, $txtZona, $txtMoneda) {
        $condicion = "wc_ordenventa.`esguiado`=1 and " .
                        "wc_ordenventa.`estado`=1 and " .
                        "wc_detalleordencobro.`idpadre`!='' and " .
                        "wc_detalleordencobro.`estado`='1' and " .
                        "wc_detalleordencobro.`formacobro`='2' and " .
                        "(substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
        if (!empty($txtFechaFinal)) $condicion .= " and wc_detalleordencobro.fechacreacion >= '$txtFechaInicio 00:00:01'";
        if (!empty($txtFechaFinal)) $condicion .= " and wc_detalleordencobro.fechacreacion <= '$txtFechaFinal 23:59:59'";
        //if (!empty($txtFecha)) $condicion .= " and wc_detalleordencobro.fechagiro='$txtFecha'";
        if (!empty($txtPrincipal)) {
            $condicion .= ' and wc_categoria.idpadrec=' . $txtPrincipal;
        } else {
            $condicion .= ' and (wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2)';
        }
        if (!empty($txtMoneda)) $condicion .= ' and wc_ordenventa.IdMoneda=' . $txtMoneda;
        if (!empty($txtZona)) $condicion .= ' and wc_zona.idzona=' . $txtZona;
        if (!empty($txtCategoria)) $condicion .= ' and wc_categoria.idcategoria=' . $txtCategoria;
        if (!empty($txtCliente)) $condicion .= ' and wc_ordenventa.idcliente=' . $txtCliente;
        if (!empty($txtVendedor)) $condicion .= ' and wc_ordenventa.idvendedor=' . $txtVendedor;
        if (!empty($txtOrdenVenta)) $condicion .= ' and wc_ordenventa.idordenventa=' . $txtOrdenVenta;
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente` and wc_ordenventa.estado= 1
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado`=1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro` and wc_detalleordencobro.`estado`=1",
                                    "wc_ordenventa.idmoneda,
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
                                    wc_detalleordencobro.*",
                                    $condicion,
                                    "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        
        return $data;
    }
    
    function resumencobranzapagado($txtFechaInicio, $txtFechaFin, $txtPrincipal, $txtCategoria, $txtZona, $txtMoneda, $txtCliente, $txtOrdenVenta, $cmbFormaPago) {
        $condicion = "wc_ordenventa.`esguiado`=1 and " .
                        "wc_ordencobro.`estado`=1 and " .
                        "wc_ordenventa.`estado`=1 and " .
                        "wc_detalleordencobro.`situacion`='cancelado' and " .
                        "wc_detalleordencobro.`estado`='1'";
                        
        if (!empty($txtPrincipal)) {
            $condicion .= ' and wc_categoria.idpadrec=' . $txtPrincipal;
        } else {
            $condicion .= ' and (wc_categoria.`idpadrec`= 1 or wc_categoria.`idpadrec`= 2)';
        }
        $condicion .= !empty($txtFechaInicio) ? " and wc_detalleordencobro.`fechagiro`>='$txtFechaInicio'" : "";
        $condicion .= !empty($txtFechaFin) ? " and wc_detalleordencobro.`fechagiro`<='$txtFechaFin'" : "";
        if (!empty($txtMoneda)) $condicion .= ' and wc_ordenventa.IdMoneda=' . $txtMoneda;
        if (!empty($txtZona)) $condicion .= ' and wc_zona.idzona=' . $txtZona;
        if (!empty($txtCategoria)) $condicion .= ' and wc_categoria.idcategoria=' . $txtCategoria;
        if (!empty($txtCliente)) $condicion .= ' and wc_ordenventa.idcliente=' . $txtCliente;
        if (!empty($txtOrdenVenta)) $condicion .= ' and wc_ordenventa.idordenventa=' . $txtOrdenVenta;        
        $condicion .= !empty($cmbFormaPago) ? " and wc_detalleordencobro.`formacobro`>='$cmbFormaPago'" : " and wc_detalleordencobro.`formacobro` in (1, 2, 3)";
        $data = $this->leeRegistro("`wc_ordenventa` wc_ordenventa
                                    INNER JOIN `wc_actor` wc_actor ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
                                    INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                    INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente` and wc_ordenventa.estado= 1
                                    INNER JOIN `wc_zona` wc_zona ON wc_clientezona.`idzona` = wc_zona.`idzona`
                                    INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
                                    INNER JOIN `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa` and wc_ordencobro.`estado`=1
                                    INNER JOIN `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`",
                                    "wc_ordenventa.idmoneda,
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
                                    wc_ordencobro.saldoordencobro,wc_detalleordencobro.referencia,wc_detalleordencobro.fechamodificacion,
                                    wc_detalleordencobro.*",
                                    $condicion,
                                    "wc_actor.idactor, wc_ordenventa.idordenventa, wc_ordencobro.idordencobro, wc_detalleordencobro.iddetalleordencobro asc");
        
        return $data;
    }
    
}
