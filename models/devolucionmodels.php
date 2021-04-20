<?php

class devolucion extends Applicationbase {

    private $tabla = "wc_devolucion";
    private $tabla2 = "wc_detalledevolucion";
    private $tabla3 = "`wc_devolucion` wc_devolucion inner join `wc_ordenventa` wc_ordenventa on wc_devolucion.`idordenventa`=wc_ordenventa.`idordenventa`";
    private $tabla4 = "wc_documento";

    function grabaDevolucion($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function actualizarDevolucion($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function actualizarDetalleDevolucion($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla2, $data, $filtro);
        return $exito;
    }
    
    function verificarDetalleDevolucion($iddevolucion, $idproducto) {
        $data = $this->leeRegistro("wc_detalledevolucion ", "*", "iddevolucion='$iddevolucion' and idproducto='$idproducto'", "", "");
        return $data;
    }
    
    function listaDetalleDevolucionXDetalleOV ($idordenventa, $iddevolucion) {
        $data = $this->leeRegistro("wc_detalleordenventa dov
                                    inner join wc_producto producto on producto.idproducto = dov.idproducto
                                    left join wc_devolucion devolucion on 
                                                        devolucion.idordenventa = dov.idordenventa and 
                                                        devolucion.registrado = 0 and 
                                                        devolucion.aprobado = 0 and
                                                        devolucion.iddevolucion = '$iddevolucion' and
                                                        devolucion.estado = 1
                                    left join wc_detalledevolucion ddevolucion on 
                                                        ddevolucion.iddevolucion = devolucion.iddevolucion and 
                                                        ddevolucion.idproducto = dov.idproducto and
                                                        ddevolucion.estado = 1 and
                                                        ddevolucion.cantidad > 0", 
                                    "producto.idproducto,
                                    producto.codigopa, 
                                    producto.nompro,
                                    dov.iddetalleordenventa,
                                    dov.cantdespacho,
                                    dov.cantdevuelta,
                                    dov.preciofinal,
                                    ddevolucion.precio,
                                    ddevolucion.cantidad", 
                                    "dov.idordenventa='$idordenventa' and                                    
                                    dov.estado = 1", 
                                    "", 
                                    "group by dov.iddetalleordenventa");
        return $data;
    }

    function listaDetalleDevolucionOV($idordenventa) {
        $sql = "select dov.idproducto, dov.cantdespacho
                        from wc_detalleordenventa dov
                        inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa
                        where ov.codigov = '" . $idordenventa . "'
                        and ov.estado=1
                        order by dov.idproducto";
        $tabla = "wc_detalleordenventa dov
                                inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa";
        $columnas = "select dov.idproducto, dov.cantdespacho";
        $filtro = "ov.codigov = '" . $idordenventa . "'
                                and ov.estado=1";
        $orden = "dov.idproducto";
        $opciones = "";

        $data = $this->leeRegistro($tabla, $columnas, $filtro, $orden, $opciones);

        return $data;
    }

    function listaDevolucion($idordenventa) {
        $condicion = "estado=1";
        if (!empty($idordenventa)) {
            $condicion = "estado=1 and idordenventa='" . $idordenventa . "'";
        }

        $data = $this->leeRegistro($this->tabla, "", $condicion, "");
        return $data;
    }

    function listaDevolucionFiltro($condicion) {
        $data = $this->leeRegistro($this->tabla, "", $condicion, "");
        return $data;
    }
            
    public function totalDevolucionsAprobadas($idordenventa) {
        $data = $this->leeRegistro($this->tabla, 
                                    "sum(importetotal) as total", 
                                    "registrado=1 and "
                                    . "estado=1 and "
                                    . "aprobado=1 and "
                                    . "idordenventa='$idordenventa'", "");
        return $data[0]['total'];
    }

    function nuevoId() {
        $data = $this->leeRegistro($this->tabla, "max(iddevolucion)", "", "");
        return $data[0]['max(iddevolucion)'];
    }

    function ultimaId($idOV) {
        $condicion = "idordenventa='$idOV' and registrado=0 and estado=1 and aprobado=0";
        $data = $this->leeRegistro($this->tabla, "*", $condicion, "");
        return $data;
    }

    function verificar($iddevolucion) {
        $condicion = " estado=1 and aprobado=1 and iddevolucion='$iddevolucion'";
        $data = $this->leeRegistro($this->tabla, "", $condicion, "");
        return $data;
    }

    function listaDevolucion2($idordenventa, $iddevolucion) {
        $condicion = "estado=1 and idordenventa='$idordenventa' and iddevolucion='$iddevolucion'";

        $data = $this->leeRegistro($this->tabla, "", $condicion, "");
        return $data;
    }

    function actualizaDetalleDevolucion($data, $iddevolucion, $iddetalledevolucion) {
        $condicion = "estado=1";
        if (!empty($iddetalledevolucion) && empty($iddevolucion)) {
            $condicion = "estado=1 and iddetalledevolucion='$iddetalledevolucion'";
        } elseif (!empty($iddevolucion) && empty($iddetalledevolucion)) {
            $condicion = "estado=1 and iddevolucion='$iddevolucion'";
        } elseif (!empty($iddetalledevolucion) && !empty($iddevolucion)) {
            $condicion = "estado=1 and iddevolucion='$iddevolucion' and iddetalledevolucion='$iddetalledevolucion'";
        }
        $exito = $this->actualizaRegistro($this->tabla2, $data, $condicion);
        return $exito;
    }

    function grabaDetalleDevolucion($data) {
        $exito = $this->grabaRegistro($this->tabla2, $data);
        return $exito;
    }

    function listaDetalleDevolucion($iddevolucion, $iddetalledevolucion) {
        $condicion = "cantidad>0 and estado=1";
        if (!empty($iddetalledevolucion) && empty($iddevolucion)) {
            $condicion .= " and iddetalledevolucion='$iddetalledevolucion'";
        } elseif (!empty($iddevolucion) && empty($iddetalledevolucion)) {
            $condicion .= " and iddevolucion='$iddevolucion'";
        } elseif (!empty($iddetalledevolucion) && !empty($iddevolucion)) {
            $condicion .= " and iddevolucion='$iddevolucion' and iddetalledevolucion='$iddetalledevolucion'";
        }
        $data = $this->leeRegistro($this->tabla2, "", $condicion, "");
        return $data;
    }

    function buscaDetalleDevolucion($iddetalledevolucion) {
        $data = $this->leeRegistro($this->tabla2, "", "iddetalledevolucion='$iddetalledevolucion' ", "");
        return $data;
    }

    function listaDevolucionxid($iddevolucion) {
        $condicion = " estado=1 and iddevolucion='" . $iddevolucion . "'";
        $data = $this->leeRegistro($this->tabla, "", $condicion, "");
        return $data;
    }

    function eliminarDevolucion($iddevolucion) {
        $exito = $this->cambiaEstado($this->tabla, "iddevolucion=$iddevolucion");
        return $exito;
    }

    function eliminarDetalleDevolucion($iddevolucion) {
        $exito = $this->cambiaEstado($this->tabla2, "iddevolucion=$iddevolucion");
        return $exito;
    }

    function confirmar($iddevolucion) {
        $data['aprobado'] = 1;
        $data['fechaaprobada'] = date('Y/m/d H:i:s');
        $filtro = "iddevolucion='$iddevolucion' and registrado=1 and estado=1";
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function tieneNotaCredito($iddevolucion) {
        $condicion = " estado=1 and iddevolucion='" . $iddevolucion . "'";
        $data = $this->leeRegistro($this->tabla, "esnotacredito", $condicion, "");
        return $data[0]['esnotacredito'];
    }

    function paginadoDevoluciones($iddevolucion, $paraBusqueda = "") {
        $condicion2 = "";
        $condicion = "wc_devolucion.`estado`=1 and wc_devolucion.`registrado`=1 ";
        if (!empty($paraBusqueda)) {
            $condicion2 = " and wc_devolucion.`iddevolucion`='$paraBusqueda' or wc_ordenventa.`codigov`='$paraBusqueda' ";
        }
        if (!empty($iddevolucion)) {
            $condicion = " wc_devolucion.`estado`=1 and wc_devolucion.`registrado`=1 and wc_devolucion.`iddevolucion`='$iddevolucion' or wc_ordenventa.`codigov`='$paraBusqueda' ";
        }

        return $this->paginado(
                        $this->tabla3, $condicion . $condicion2);
    }

    function listaDevolucionesPaginado($iddevolucion, $pagina, $paraBusqueda = "") {
        $condicion2 = "";
        $condicion = "wc_devolucion.`estado`=1 and wc_devolucion.`registrado`=1 ";
        if (!empty($paraBusqueda)) {
            $condicion2 = " and wc_devolucion.`aprobado`=1 and (wc_devolucion.`iddevolucion`='$paraBusqueda' or wc_ordenventa.`codigov` like '%$paraBusqueda%') ";
        }
        if (!empty($iddevolucion)) {
            $condicion = " wc_devolucion.`estado`=1 and wc_devolucion.`registrado`=1 and wc_devolucion.`iddevolucion`='$iddevolucion' or wc_ordenventa.`codigov` like '%$paraBusqueda%' ";
        }

        $data = $this->leeRegistroPaginado(
                $this->tabla3, "", $condicion . $condicion2, "wc_devolucion.`iddevolucion` desc", $pagina);
        return $data;
    }

    function listaDevolucionesProductoPaginado($pagina, $paraBusqueda = "") {
        $condicion = "d.`estado`=1 and d.`registrado`=1 and dd.cantidad > 0 ";
        if (!empty($paraBusqueda)) {
            $condicion .= " and d.`aprobado`=1 and (p.`codigopa` like '%" . $paraBusqueda . "%' or p.`nompro` like '%" . $paraBusqueda . "%') ";
        }

        $data = $this->leeRegistroPaginado(
                "wc_producto p inner join wc_detalledevolucion dd on dd.idproducto = p.idproducto inner join wc_devolucion d on d.iddevolucion = dd.iddevolucion", "distinct d.*", $condicion, "d.`iddevolucion` desc", $pagina);
        return $data;
    }

    function cuentaDevoluciones($iddevolucion, $paraBusqueda = "") {
        $condicion2 = "";
        $condicion = "wc_devolucion.`estado`=1 and wc_devolucion.`registrado`=1 ";
        if (!empty($paraBusqueda)) {
            $condicion2 = " and wc_devolucion.`aprobado`=1 and (wc_devolucion.`iddevolucion`='$paraBusqueda' or wc_ordenventa.`codigov`='$paraBusqueda') ";
        }
        if (!empty($iddevolucion)) {
            $condicion = " wc_devolucion.`estado`=1 and wc_devolucion.`registrado`=1 and wc_devolucion.`iddevolucion`='$iddevolucion' or wc_ordenventa.`codigov`='$paraBusqueda' ";
        }
        $data = $this->leeRegistro($this->tabla3, "count(*)", $condicion . $condicion2, "");
        return $data[0]['count(*)'];
    }

    function listaDetalleconProductos($idordenventa) {
        $condicion = "wc_detalledevolucion.´estado´=1";
        if (!empty($idordenventa)) {
            $condicion = "wc_devolucion.`estado`=1 and  wc_ordenventa.`idordenventa`='$idordenventa'";
        }

        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa 
						inner join `wc_devolucion` wc_devolucion on wc_ordenventa.`idordenventa`=wc_devolucion.`idordenventa`
						inner join `wc_detalledevolucion` wc_detalledevolucion on wc_detalledevolucion.`iddevolucion`=wc_devolucion.`iddevolucion`
						inner join `wc_producto` wc_producto on wc_producto.`idproducto`=wc_detalledevolucion.`idproducto`
						", "wc_ordenventa.`codigov`,
						wc_ordenventa.`idordenventa` as idordenventa,
						wc_producto.`codigopa`,
						wc_producto.`nompro`,
						wc_detalledevolucion.`precio` as preciodevuelto,
						wc_detalledevolucion.`cantidad` as cantidaddevuelta,
						wc_detalledevolucion.`importe` as importedevuelto,
						wc_devolucion.`iddevolucion` as iddevolucion,
						wc_devolucion.`importetotal` as importetotal
						", $condicion, "");
        return $data;
    }

    function listaDevolucionconOrden($idordenventa) {
        $condicion = "wc_detalledevolucion.´estado´=1";
        if (!empty($idordenventa)) {
            $condicion = "wc_devolucion.`estado`=1 and  wc_ordenventa.`idordenventa`='$idordenventa'";
        }

        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa 
						inner join `wc_devolucion` wc_devolucion on wc_ordenventa.`idordenventa`=wc_devolucion.`idordenventa`
						inner join `wc_detalledevolucion` wc_detalledevolucion on wc_detalledevolucion.`iddevolucion`=wc_devolucion.`iddevolucion`
						inner join `wc_producto` wc_producto on wc_producto.`idproducto`=wc_detalledevolucion.`idproducto`
						", "wc_ordenventa.`codigov`,
						wc_ordenventa.`idordenventa` as idordenventa,
						wc_producto.`codigopa`,
						wc_producto.`nompro`,
						wc_detalledevolucion.`precio` as preciodevuelto,
						wc_detalledevolucion.`cantidad` as cantidaddevuelta,
						wc_detalledevolucion.`importe` as importedevuelto,
						wc_devolucion.`iddevolucion` as iddevolucion,
						wc_devolucion.`importetotal` as importetotal
						", $condicion, "");
        return $data;
    }

    function listaOrdenconCliente($idordenventa) {
        $condicion = "";
        if (!empty($idordenventa)) {
            $condicion = "wc_ordenventa.`idordenventa`='$idordenventa'";
        }

        $data = $this->leeRegistro(
                "`wc_clientezona` wc_clientezona INNER JOIN `wc_ordenventa` wc_ordenventa ON wc_clientezona.`idclientezona` = wc_ordenventa.`idclientezona`
     					INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
						", "wc_ordenventa.`codigov`,
						wc_ordenventa.`idordenventa`,
						wc_cliente.`razonsocial`,
						wc_cliente.`ruc`,
						wc_ordenventa.`situacion`,
						wc_ordenventa.`codigov`
						", $condicion, "");
        return $data;
    }

    function listaDevolucionParaImpresion($iddevolucion) {

        $condicion = "wc_devolucion.`iddevolucion`='$iddevolucion' and wc_devolucion.`registrado`=1 and wc_devolucion.`estado`=1 and wc_detalledevolucion.`estado`=1 and wc_detalledevolucion.`cantidad`>0";


        $data = $this->leeRegistro(
                " `wc_devolucion` wc_devolucion 
						inner join `wc_detalledevolucion` wc_detalledevolucion on wc_detalledevolucion.`iddevolucion`=wc_devolucion.`iddevolucion`
						
						inner join `wc_producto` wc_producto on wc_producto.`idproducto`=wc_detalledevolucion.`idproducto`
						", "wc_producto.`codigopa`,
                                                    wc_producto.`unidadmedida`,
						wc_producto.`nompro`,
						wc_detalledevolucion.`cantidad`,
						wc_detalledevolucion.`precio`
						
						", $condicion, "");
        return $data;
    }

    public function ReporteDevoluciones($idcliente, $idordenventa, $esregistrado, $fecregini, $fecregfin, $esaprobado, $fecaprini, $fecaprfin, $devtotal) {

        $sql = "	SELECT cli.idcliente,cli.razonsocial,ov.codigov,ov.idordenventa,ov.importeaprobado,dev.iddevolucion,
					CONCAT(REPEAT('0', 6-LENGTH(dev.iddevolucion)), dev.iddevolucion) as devolucion,CASE (dev.registrado) WHEN 1 THEN 'REG.' ELSE ' ' END as registrado,
					dev.fecharegistrada,CASE (dev.aprobado) WHEN 1 THEN 'APROB.' ELSE ' ' END as aprobado
					,dev.fechaaprobada,dev.importetotal,mn.simbolo,dev.observaciones FROM wc_devolucion dev
					Inner Join wc_ordenventa ov On dev.idordenventa=ov.idordenventa
					Inner Join wc_moneda mn On ov.IdMoneda=mn.idmoneda
					Inner Join wc_cliente cli On ov.idcliente=cli.idcliente
					Where dev.estado=1 ";
        if (!empty($idcliente)) {
            $sql .= "and cli.idcliente=" . $idcliente . " ";
        }
        if (!empty($idordenventa)) {
            $sql .= "and ov.idordenventa=" . $idordenventa . " ";
        }
        if (!empty($esregistrado) and $esregistrado == 1) {
            $sql .= "and dev.registrado=1 ";
        }
        if (!empty($fecregini) and ! empty($fecregfin)) {
            $sql .= "and dev.fecharegistrada between '" . $fecregini . "' and '" . $fecregfin . "' ";
        }
        if (!empty($fecregini) and empty($fecregfin)) {
            $sql .= "and dev.fecharegistrada >= '" . $fecregini . "' ";
        }
        if (empty($fecregini) and ! empty($fecregfin)) {
            $sql .= "and dev.fecharegistrada <= '" . $fecregfin . "' ";
        }
        if (!empty($esaprobado) and $esaprobado == 1) {
            $sql .= "and dev.aprobado=1 ";
        }
        if (!empty($fecaprini) and ! empty($fecaprfin)) {
            $sql .= "and dev.fechaaprobada between '" . $fecaprini . "' and '" . $fecaprfin . "' ";
        }
        if (!empty($fecaprini) and empty($fecaprfin)) {
            $sql .= "and dev.fechaaprobada >= '" . $fecaprini . "' ";
        }
        if (empty($fecaprini) and ! empty($fecaprfin)) {
            $sql .= "and dev.fechaaprobada <= '" . $fecaprfin . "' ";
        }
        if (!empty($devtotal) and $devtotal == 1) {
            $sql .= "and ov.importeaprobado=dev.importetotal ";
        }

        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    public function anularDevolucion2($id, $motivo) {
        //return $id."-".$motivo;
        $devolucion = $this->leeRegistro("wc_devolucion", "idordenventa, importetotal", "estado=1 and registrado=1 and iddevolucion='$id'", "");
        //return var_dump($devolucion);
        if (!empty($devolucion)) {
            //return "entra";
            $bandera = $this->leeRegistro('wc_ingresos', "estado", "idordenventa='" . $devolucion[0]['idordenventa'] . "'  ", "");
            //return var_dump($bandera);
            if (count($bandera) > 0) {
                //return "hola";
                $this->EjecutaConsultaBoolean("update wc_ordenventa set importedevolucion = importedevolucion - " . $devolucion[0]['importetotal'] . " where idordenventa='" . $devolucion[0]['idordenventa'] . "'");
//                            $detalle = $this->leeRegistro('wc_detalledevolucion', "idproducto, cantidad", "iddevolucion='$id' and estado=1" , "");
//                            foreach ($detalle as $producto) {
//                                $this->EjecutaConsultaBoolean("update wc_producto set stockactual = stockactual - ".$producto['cantidad'].", stockdisponible = stockdisponible - ".$producto['cantidad']." where idproducto = '".$producto['idproducto']."'");
//                            }
//                            foreach ($detalle as $producto) {
//                                $this->EjecutaConsultaBoolean("update wc_detalleordenventa set cantdevuelta = cantdevuelta - ".$producto['cantidad']." where idproducto = '".$producto['idproducto']."' and idordenventa='".$devolucion[0]['idordenventa']."'");
//                            }
                $this->EjecutaConsultaBoolean("update wc_detalledevolucion set estado = 0 where iddevolucion='$id' ");
                $this->EjecutaConsultaBoolean("update wc_ingresos set estado = 0 where idordenventa='" . $devolucion[0]['idordenventa'] . "' and tipocobro=7 and montoingresado='" . $devolucion[0]['importetotal'] . "' and montoasignado=0.00");
                $this->EjecutaConsultaBoolean("update wc_movimiento set estado = 0 where iddevolucion='$id' ");
                $this->EjecutaConsultaBoolean("update wc_documento set estado = 0 where iddevolucion='$id' and nombredoc=5 and idordenventa='" . $devolucion[0]['idordenventa'] . "'");
                $this->EjecutaConsultaBoolean("update wc_devolucion set estado = 0, motivoanulacion='$motivo' where iddevolucion='$id'");
            } else {
                return "No se puede anular porque tiene ";
            }
        }
        //return $bandera;
    }

    public function anularDevolucion($id, $motivo = "") {
        $devolucion = $this->leeRegistro("wc_devolucion", "idordenventa, importetotal", "estado=1 and registrado=1 and iddevolucion='$id'", "");
        if (!empty($devolucion)) {
            $bandera = $this->leeRegistro('wc_ingresos', "estado", "idordenventa='" . $devolucion[0]['idordenventa'] . "'  ", "");
            if (count($bandera) > 0) {
                $this->EjecutaConsultaBoolean("update wc_ordenventa set importedevolucion = importedevolucion - " . $devolucion[0]['importetotal'] . " where idordenventa='" . $devolucion[0]['idordenventa'] . "'");
                $detalles = $this->leeRegistro('wc_detalledevolucion', "idproducto, cantidad", "iddevolucion='$id' and estado=1", "");
                foreach ($detalles as $pdto) {
                    $this->EjecutaConsultaBoolean("update wc_producto set stockactual = stockactual - " . $pdto['cantidad'] . ", stockdisponible = stockdisponible - " . $pdto['cantidad'] . " where idproducto = '" . $pdto['idproducto'] . "'");
                }
                foreach ($detalles as $pdto) {
                    $this->EjecutaConsultaBoolean("update wc_detalleordenventa set cantdevuelta = cantdevuelta - " . $pdto['cantidad'] . " where idproducto = '" . $pdto['idproducto'] . "' and idordenventa='" . $devolucion[0]['idordenventa'] . "'");
                }
                $this->EjecutaConsultaBoolean("update wc_detalledevolucion set estado = 0 where iddevolucion='$id' ");
                $this->EjecutaConsultaBoolean("update wc_ingresos set estado = 0 where idordenventa='" . $devolucion[0]['idordenventa'] . "' and tipocobro=7 and montoingresado='" . $devolucion[0]['importetotal'] . "' and montoasignado=0.00");
                $this->EjecutaConsultaBoolean("update wc_movimiento set estado = 0 where iddevolucion='$id' ");
                $this->EjecutaConsultaBoolean("update wc_documento set estado = 0 where iddevolucion='$id' and nombredoc=5 and idordenventa='" . $devolucion[0]['idordenventa'] . "'");
                $this->EjecutaConsultaBoolean("update wc_devolucion set estado = 0, motivoanulacion='$motivo' where iddevolucion='$id'");
            } else {
                return "No se puede anular porque tiene ";
            }
        }
        //return $bandera;
    }

    public function totalDevolucionXVendedor($fechaInicio, $fechaFin, $idvendedor) {
        // fordenventa>='$fechainicio' and fordenventa<='$fechafinal'
        $data = $this->leeRegistro("wc_devolucion devolucion
                                inner join wc_ordenventa ordenventa on ordenventa.idordenventa = devolucion.idordenventa and ordenventa.idvendedor='$idvendedor' and fordenventa>='$fechaInicio' and fordenventa<='$fechaFin'", 
                                "sum(devolucion.importetotal) as importetotal, ordenventa.idMoneda", 
                                "devolucion.registrado=1 and devolucion.aprobado=1 and devolucion.fechaaprobada>='$fechaInicio 00:00:00' and devolucion.fechaaprobada<='$fechaFin 23:59:59' and devolucion.estado=1", "", "group by ordenventa.idMoneda");
        return $data;
    }

    public function consultaCorrelativos($idordenventa) {
        $condicion = " idordenventa='".$idordenventa."' and nombredoc in ('1','2','4') and estado=1";
        $data = $this->leeRegistro($this->tabla4, "iddocumento,electronico,nombredoc,serie,numdoc", $condicion, "nombredoc desc");
        return $data;
    }
    
    public function lstadoDevoluciones($fechainicio, $fechafin, $idcliente, $idordenventa, $idvendedor, $idmotivo, $idsubmotivo) {
        $condicion = '';
        !empty($fechainicio) ? $condicion .= " and devolucion.fechaaprobada>='$fechainicio 00:00:00'" : "";
        !empty($fechafin) ? $condicion .= " and devolucion.fechaaprobada<='$fechafin 23:59:59'" : "";
        !empty($idcliente) ? $condicion .= " and ordenventa.idcliente='$idcliente'" : "";
        !empty($idordenventa) ? $condicion .= " and devolucion.idordenventa='$idordenventa'" : "";
        !empty($idvendedor) ? $condicion .= " and ordenventa.idvendedor='$idvendedor'" : "";
        !empty($idmotivo) ? $condicion .= " and devolucion.idmotivodevolucion='$idmotivo'" : "";
        !empty($idsubmotivo) ? $condicion .= " and devolucion.idsubmotivodevolucion='$idsubmotivo'" : "";
        $data = $this->leeRegistro("wc_devolucion devolucion
                                    left join wc_submotivodevolucion submotivodevolucion ON submotivodevolucion.idsubmotivodevolucion = devolucion.idsubmotivodevolucion
                                    inner join wc_ordenventa ordenventa ON ordenventa.idordenventa = devolucion.idordenventa AND
                                                                           ordenventa.estado = 1
                                    inner join wc_actor actor ON actor.idactor = ordenventa.idvendedor
                                    inner join wc_cliente cliente ON cliente.idcliente = ordenventa.idcliente", 
                                    "devolucion.iddevolucion,
                                     devolucion.idordenventa,   
                                     devolucion.importetotal,
                                     devolucion.fechaaprobada,
                                     devolucion.observaciones,
                                     devolucion.idmotivodevolucion,
                                     submotivodevolucion.descripcion as submotivo,
                                     ordenventa.codigov,
                                     ordenventa.idmoneda,
                                     ordenventa.idvendedor,
                                     concat(actor.nombres, ' ', actor.apellidopaterno, ' ', actor.apellidomaterno) as vendedor,
                                     cliente.idcliente,
                                     cliente.ruc,
                                     cliente.razonsocial
                                    ", 
                                    "devolucion.estado = 1 and
                                     devolucion.registrado = 1 and
                                     devolucion.aprobado = 1" . $condicion,
                                    "ordenventa.idvendedor asc, 
                                     devolucion.idmotivodevolucion asc,                     
                                     devolucion.idsubmotivodevolucion asc,
                                     devolucion.iddevolucion asc");
        return $data;
    }
    
    public function resumenDevoluciones($fechainicio, $fechafin, $idcliente, $idordenventa, $idvendedor, $idmotivo, $idsubmotivo) {
        $condicion = '';
        !empty($fechainicio) ? $condicion .= " and devolucion.fechaaprobada>='$fechainicio 00:00:00'" : "";
        !empty($fechafin) ? $condicion .= " and devolucion.fechaaprobada<='$fechafin 23:59:59'" : "";
        !empty($idcliente) ? $condicion .= " and ordenventa.idcliente='$idcliente'" : "";
        !empty($idordenventa) ? $condicion .= " and devolucion.idordenventa='$idordenventa'" : "";
        !empty($idvendedor) ? $condicion .= " and ordenventa.idvendedor='$idvendedor'" : "";
        !empty($idmotivo) ? $condicion .= " and devolucion.idmotivodevolucion='$idmotivo'" : "";
        !empty($idsubmotivo) ? $condicion .= " and devolucion.idsubmotivodevolucion='$idsubmotivo'" : "";
        
        $data = $this->leeRegistro("wc_devolucion devolucion
                                    left join wc_submotivodevolucion submotivodevolucion ON submotivodevolucion.idsubmotivodevolucion = devolucion.idsubmotivodevolucion
                                    inner join wc_ordenventa ordenventa ON ordenventa.idordenventa = devolucion.idordenventa AND
                                                                           ordenventa.estado = 1
                                    inner join wc_actor actor ON actor.idactor = ordenventa.idvendedor", 
                                    "devolucion.iddevolucion,
                                     devolucion.idordenventa,   
                                     devolucion.importetotal,
                                     devolucion.fechaaprobada,
                                     devolucion.observaciones,
                                     devolucion.idmotivodevolucion,
                                     submotivodevolucion.idsubmotivodevolucion,
                                     submotivodevolucion.descripcion as submotivo,
                                     ordenventa.idmoneda,
                                     ordenventa.idvendedor,
                                     concat(actor.nombres, ' ', actor.apellidopaterno, ' ', actor.apellidomaterno) as vendedor
                                    ", 
                                    "devolucion.estado = 1 and
                                     devolucion.registrado = 1 and
                                     devolucion.aprobado = 1" . $condicion,
                                    "devolucion.idmotivodevolucion asc,                     
                                     devolucion.idsubmotivodevolucion asc,
                                     ordenventa.idvendedor asc");
        return $data;
    }
    
    public function resumenDevolucionesVendedor($fechainicio, $fechafin, $idcliente, $idordenventa, $idvendedor, $idmotivo, $idsubmotivo) {
        $condicion = '';
        !empty($fechainicio) ? $condicion .= " and devolucion.fechaaprobada>='$fechainicio 00:00:00'" : "";
        !empty($fechafin) ? $condicion .= " and devolucion.fechaaprobada<='$fechafin 23:59:59'" : "";
        !empty($idcliente) ? $condicion .= " and ordenventa.idcliente='$idcliente'" : "";
        !empty($idordenventa) ? $condicion .= " and devolucion.idordenventa='$idordenventa'" : "";
        !empty($idvendedor) ? $condicion .= " and ordenventa.idvendedor='$idvendedor'" : "";
        !empty($idmotivo) ? $condicion .= " and devolucion.idmotivodevolucion='$idmotivo'" : "";
        !empty($idsubmotivo) ? $condicion .= " and devolucion.idsubmotivodevolucion='$idsubmotivo'" : "";
        
        $data = $this->leeRegistro("wc_devolucion devolucion
                                    left join wc_submotivodevolucion submotivodevolucion ON submotivodevolucion.idsubmotivodevolucion = devolucion.idsubmotivodevolucion
                                    inner join wc_ordenventa ordenventa ON ordenventa.idordenventa = devolucion.idordenventa AND
                                                                           ordenventa.estado = 1
                                    inner join wc_actor actor ON actor.idactor = ordenventa.idvendedor", 
                                    "devolucion.iddevolucion,
                                     devolucion.idordenventa,   
                                     devolucion.importetotal,
                                     devolucion.fechaaprobada,
                                     devolucion.observaciones,
                                     devolucion.idmotivodevolucion,
                                     submotivodevolucion.idsubmotivodevolucion,
                                     submotivodevolucion.descripcion as submotivo,
                                     ordenventa.idmoneda,
                                     ordenventa.idvendedor,
                                     concat(actor.nombres, ' ', actor.apellidopaterno, ' ', actor.apellidomaterno) as vendedor
                                    ", 
                                    "devolucion.estado = 1 and
                                     devolucion.registrado = 1 and
                                     devolucion.aprobado = 1" . $condicion,
                                    "ordenventa.idvendedor asc,
                                     devolucion.idmotivodevolucion asc,                     
                                     devolucion.idsubmotivodevolucion asc");
        return $data;
    }
    
}
?>

