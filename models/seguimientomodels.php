<?php

class seguimiento extends Applicationbase {

    private $tabla = "wc_seguimiento";
    private $tabla2 = "wc_ordenventa";

    function listaOrdenVenta($filtro) {

        $condicion = "wc_actorrol.`idrol`=25 and wc_ordenventa.`estado`=1";
        if (!empty($filtro)) {
            $condicion.=" and " . $filtro;
        }
        $data = $this->leeRegistro(
                "`wc_actor` wc_actor INNER JOIN `wc_ordenventa` wc_ordenventa ON wc_actor.`idactor` = wc_ordenventa.`idvendedor`
			     INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
			     INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
			     INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.`IdMoneda`=wc_moneda.`IdMoneda`
			     INNER JOIN `wc_actorrol` wc_actorrol ON wc_actor.`idactor` = wc_actorrol.`idactor`", "wc_ordenventa.`idordenventa`,
                        wc_cliente.`razonsocial`,
     			wc_actor.`nombres`,
     			wc_actor.`apellidopaterno`,
     			wc_actor.`apellidomaterno`,
    			wc_ordenventa.`vbcobranzas`,
     			wc_ordenventa.`vbalmacen`,
     			wc_ordenventa.`codigov`,
     			wc_ordenventa.`vbcreditos`,
     			wc_ordenventa.`vbventas`,
     			wc_ordenventa.`importeov`,
     			wc_ordenventa.`esfacturado`,
     			wc_ordenventa.`esguiado`,
     			wc_ordenventa.`esdespachado`,
     			wc_ordenventa.`importepagado`,
     			wc_ordenventa.`importedevolucion`,
     			wc_ordenventa.`tiempoduracion`,
     			wc_ordenventa.`idordenventa`,
     			wc_moneda.`simbolo` as SimboloMoneda,
     			wc_moneda.`nombre` as NombreMoneda,
                        wc_ordenventa.`observaciones`,
                        wc_ordenventa.`confirmacion_prod`,
                        wc_ordenventa.`idclientetransporte`,
                        wc_ordenventa.`observacion_entregaProd`", $condicion, "", "");
        return $data;
    }

    function grabaOrdenventa($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function nombretransporte($id) {
        $condicion = "ct.idclientetransporte=" . $id;
        $data = $this->leeRegistro(
                "wc_clientetransporte ct
                                inner join wc_transporte t on ct.idtransporte=t.idtransporte", "t.trazonsocial", $condicion, "", ""
        );
        return $data[0]['trazonsocial'];
    }

    function listadoSeguimiento($id) {
        $filtro = "idordenventa=$id and estado=1";
        $data = $this->leeRegistro($this->tabla, "", $filtro, "", "");
        return $data;
    }

    function actualizaSeguimiento($idordenventa, $campo, $valor) {
        $filtro = "idordenventa=$idordenventa and estado=1";
        if (substr($campo, 0, 1) == 's') {
            $estado = $this->leeRegistro("$this->tabla", $campo, $filtro, "");
            $estado1 = $estado[0][$campo];
            $valor = ($estado1 - 1) * (-1);
        } else {
            $valor = "'" . $valor . "'";
        }
        $fecha = " ,fecha$campo=NOW()";
        $sql = "Update  " . $this->tabla . " Set $campo=" . $valor . "$fecha Where " . $filtro;
        $exito = $this->EjecutaConsultaBoolean($sql);
        return $exito;
    }
    function ListaDespachos($fecha, $filtro) {
        $data = $this->leeRegistro("wc_ordenventa ov 
        inner join wc_seguimiento s on ov.idordenventa=s.idordenventa ", "ov.codigov,s.*", $filtro, "", "");
        return $data;
    }

      function updateConfirmacion($idordenventa,$campo,$valor){
        $filtro= "idordenventa=".$idordenventa;
        $fecha=",fechaobservacion=now()";
        $valor=(int)$valor;
        $estado1=($valor-1)*(-1);
        $sql="Update  ".$this->tabla." Set $campo=".$estado1."$fecha Where idordenventa=".$idordenventa;
        echo $sql;
        exit;
	$exito=$this->EjecutaConsultaBoolean($sql);
        return $exito;
    }

    function verlistavalesxOrdenventa($codigo){
        $sql = "select
                ov.codigov as codigo,
                case concat(c.nombrecli,' ',c.apellido1,' ',c.apellido2) when ' ' then c.razonsocial else concat(c.nombrecli,' ',c.apellido1,' ',c.apellido2) end as cliente,
                concat(a.nombrecompleto,' ',a.apellidopaterno,' ',a.apellidomaterno) as vendedor
                from wc_ordenventa ov
                inner join wc_actor a on a.idactor = ov.idvendedor
                inner join wc_cliente c on c.idcliente = ov.idcliente
                where ov.codigov like '%".$codigo."'";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
    
    function indicadoresventa_productos ($fechainicio, $fechafin, $idmoneda, $order) {
        $sql = "SELECT producto.idproducto, 
		producto.codigopa,
		producto.nompro,
                linea.nomlin,
                sum(dov.cantdespacho-dov.cantdevuelta) as undvendida,
                sum((dov.cantdespacho-dov.cantdevuelta)*dov.preciofinal) as totalfinal
                        FROM wc_ordenventa ordenventa
                INNER JOIN wc_detalleordenventa dov ON dov.idordenventa = ordenventa.idordenventa and
                                                        dov.estado = 1 and
                                                        dov.cantdespacho > 0 and
                                                        dov.cantdespacho > dov.cantdevuelta
                INNER JOIN wc_producto producto ON producto.idproducto = dov.idproducto and
                                                   producto.estado = 1
                INNER JOIN wc_linea sublinea ON sublinea.idlinea = producto.idlinea
                INNER JOIN wc_linea linea ON linea.idlinea = sublinea.idpadre
                WHERE ordenventa.fordenventa >= '$fechainicio' and
                                  ordenventa.fordenventa <= '$fechafin' and
                                ordenventa.estado = 1 and
                    ordenventa.esguiado = 1 and
                    ordenventa.vbcreditos = 1 and
                    ordenventa.faprobado != '' and
                    ordenventa.IdMoneda = $idmoneda and
                    ordenventa.idvendedor not in (136, 241, 152, 184, 264, 59, 391, 445)
                    GROUP BY producto.idproducto
                    ORDER BY $order DESC
                    LIMIT 1;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
            
    function indicadoresdespacho_productos_VENTA($fecha) {
        $sql = "select  count(*) as totalpedidos
                        from wc_ordenventa
                        where fordenventa = '$fecha' and 
                              vbcreditos = 1 and
                              estado = 1;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
    
    function indicadoresdespacho_productos_DESPACHO($fecha) {
        $sql = "select  count(*) as totaldespacho
                        from wc_ordenventa
                        where fechadespacho = '$fecha' and 
                              vbcreditos = 1 and
                              estado = 1;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function pedidosentregados_seguiminetoxdia($fecha) {
        $sql = "select  count(ov.idordenventa) as totalpedidos, sum(og.importegasto) as valor, count(ov.nrocajas) as bultos, ov.IdMoneda as moneda, seguimiento.confirmacion
                        from wc_ordenventa ov
                        inner join (select idordenventa, sum(importegasto) as importegasto
                                                            from wc_ordengasto 
                                                            where idtipogasto in (7, 9) and 
                                                                  estado = 1
                                                            group by idordenventa) as og on og.idordenventa = ov.idordenventa
                        inner join wc_seguimiento seguimiento on seguimiento.idordenventa = ov.idordenventa and
                                                                                                             seguimiento.fechacreacion = (select max(fechacreacion) from wc_seguimiento 
                                                                                                             where estado = 1 and confirmacion != '' and idordenventa = ov.idordenventa) and
                                                                                                                   seguimiento.estado = 1
                        where ov.fordenventa = '$fecha' and 
                              ov.vbcreditos = 1 and
                              ov.estado = 1
                        group by ov.IdMoneda, seguimiento.confirmacion;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function devolucionesxmeses($fechainicio, $fechafin) {
        $sql = "select producto.idproducto, 
    producto.codigopa, 
                producto.nompro,
                ordenventa.IdMoneda as moneda,
                sum(ddevolucion.cantidad) as cantidad,
                sum(ddevolucion.cantidad*ddevolucion.precio) as importe,
                (case when movimientosalida.cantidadsalida is null then 0 else movimientosalida.cantidadsalida end) as cantidadsalida, 
                (case when movimientoingreso.cantidadingreso is null then 0 else movimientoingreso.cantidadingreso end) as cantidadingreso
    from wc_devolucion devolucion
                inner join wc_ordenventa ordenventa ON ordenventa.idordenventa = devolucion.idordenventa
                inner join wc_detalledevolucion ddevolucion ON ddevolucion.iddevolucion = devolucion.iddevolucion and
                                                                ddevolucion.cantidad > 0 and
                ddevolucion.estado = 1
                inner join wc_producto producto ON producto.idproducto = ddevolucion.idproducto
                left join (select ddetallemovimiento.idproducto as idproductoingreso, sum(ddetallemovimiento.cantidad) as cantidadingreso
          from wc_movimiento movimiento
          inner join wc_detallemovimiento ddetallemovimiento ON ddetallemovimiento.idmovimiento = movimiento.idmovimiento and
                                      ddetallemovimiento.estado = 1
          where movimiento.idordenventa is null and
            movimiento.idordencompra is null and
            movimiento.iddevolucion = 0 and
            movimiento.estado = 1 and
            movimiento.tipomovimiento = 1 and
            movimiento.fechamovimiento >= '$fechainicio' and
            movimiento.fechamovimiento <= '$fechafin'
          group by ddetallemovimiento.idproducto) as movimientoingreso ON movimientoingreso.idproductoingreso = ddevolucion.idproducto
                left join (select ddetallemovimiento.idproducto as idproductosalida, sum(ddetallemovimiento.cantidad) as cantidadsalida
          from wc_movimiento movimiento
          inner join wc_detallemovimiento ddetallemovimiento ON ddetallemovimiento.idmovimiento = movimiento.idmovimiento and
                                      ddetallemovimiento.estado = 1
          where movimiento.idordenventa is null and
            movimiento.idordencompra is null and
            movimiento.iddevolucion = 0 and
            movimiento.estado = 1 and
            movimiento.tipomovimiento = 2 and
            movimiento.fechamovimiento >= '$fechainicio' and
            movimiento.fechamovimiento <= '$fechafin'
          group by ddetallemovimiento.idproducto) as movimientosalida ON movimientosalida.idproductosalida = ddevolucion.idproducto
    where devolucion.aprobado = 1 and
        devolucion.registrado = 1 and
                        devolucion.estado = 1 and
                        devolucion.fechaaprobada >= '$fechainicio 00:00:01' and
                        devolucion.fechaaprobada <= '$fechafin 23:59:59'
                group by ordenventa.IdMoneda, ddevolucion.idproducto
                order by producto.codigopa, producto.idproducto asc;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
        
    }

    function pedidosentregados_productosmeses($fechainicio, $fechafin) {
        $sql = "select ov.IdMoneda as moneda,
    dov.idproducto,
                producto.codigopa, 
                producto.nompro,
                ov.idordenventa,
                seguimiento.confirmacion,
                dov.cantdespacho as cantidad,
                (dov.cantdespacho*dov.preciofinal) as importe
                                from wc_ordenventa ov
                    inner join wc_seguimiento seguimiento on seguimiento.idordenventa = ov.idordenventa and
                                                             seguimiento.fechacreacion = (select max(fechacreacion) from wc_seguimiento 
                                                                                                                                                                                where estado = 1 and 
                                                                                            confirmacion != '' and 
                                                                                            idordenventa = ov.idordenventa) and
                                                                                            seguimiento.estado = 1
                    inner join wc_detalleordenventa dov on dov.idordenventa = ov.idordenventa and
                                                                                                                dov.estado = 1
                    inner join wc_producto producto on producto.idproducto = dov.idproducto and
                                                                                                        producto.estado = 1
                    where ov.vbcreditos = 1 and
                          ov.estado = 1 and
                          ov.fordenventa >= '$fechainicio' and ov.fordenventa <= '$fechafin'
                    group by ov.IdMoneda, producto.idproducto, ov.idordenventa, seguimiento.confirmacion
                                order by producto.codigopa, producto.idproducto asc;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
    
    function productosdevueltos_devuletos ($idproducto, $idordenventa) {
        $sql = "select sum(ddevolucion.cantidad) as devuelto
    from wc_devolucion devolucion 
        inner join wc_detalledevolucion ddevolucion on ddevolucion.iddevolucion = devolucion.iddevolucion and
                        ddevolucion.estado = 1 and
                                                ddevolucion.cantidad > 0 and
                                                ddevolucion.idproducto = '$idproducto'
        where devolucion.aprobado = 1 and
        devolucion.registrado = 1 and
        devolucion.estado = 1 and
        devolucion.idmotivodevolucion = 1 and
                devolucion.idordenventa = '$idordenventa';";
        $data = $this->EjecutaConsulta($sql);
        return empty($data[0]['devuelto']) ? 0 : $data[0]['devuelto'];
    }

}
