<?php

class Ordencompra extends Applicationbase {

    private $tabla = "wc_ordencompra";
    private $tabla2 = "wc_ordencompra as t1,wc_detalleordencompra as t2,wc_producto as t3,wc_linea as t4";
    private $tabla3 = "wc_ordencompra,wc_proveedor,wc_almacen";
    private $tabla4 = "wc_detalleordencompra,wc_ordencompra";
    private $tabla5 = "wc_pagoCompra";

    function listadoOrdenescompra() {
        $ordenCompra = $this->leeRegistro3($this->tabla3, "", "t1.estado=1", "t1.fechacreacion desc", 2);
        return $ordenCompra;
    }

    function lista2UltimasCompras($idProducto) {
        $ordenCompra = $this->leeRegistro($this->tabla4, "fordencompra,cantidadsolicitadaoc", "", "fordencompra desc", "limit 2");
        return $ordenCompra;
    }
   
    function listadoOrdenecompraNoRegistrado() {
        $ordenCompra = $this->leeRegistro3($this->tabla3, "", "registrado='0' and valorizado=1 and t1.estado=1", "", 2);
        return $ordenCompra;
    }
    
    function listarOrdenCompraXDua($idedc) {
        $data = $this->leeRegistro("wc_ordencompra oc " . 
                                   "inner join wc_proveedor proveedor on proveedor.idproveedor = oc.idproveedor", 
                                   "oc.codigooc, oc.idordencompra, oc.vbimportaciones, proveedor.razonsocialp", "oc.idestructuradecostos='" . $idedc . "' and oc.actualizado='1'", "oc.codigooc asc", "");
        return $data;
    }
    
    function buscarVendedorXOrdenCompra($idOrdenCompra) {
        $data=$this->leeRegistro("wc_ordencompra oc inner join wc_actor actor on actor.idactor = oc.jefelinea","actor.nombres, actor.apellidopaterno, actor.apellidomaterno","idordencompra=$idOrdenCompra","");
	return $data;
    }
    
    function inventario($idAlmacen, $idLinea, $idSubLinea, $idProducto) {
        $condicion = "";
        if (!empty($idAlmacen)) {
            $condicion = "t1.idalmacen=$idAlmacen";
        }
        if (!empty($idLinea)) {
            $condicion = "idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "t3.idlinea=$idSubLinea";
        }
        if (!empty($idProducto)) {
            $condicion = "t2.idproducto=$idProducto";
        }
        if (!empty($condicion)) {
            $condicion .= " and";
        }
        $producto = $this->leeRegistro($this->tabla2, "t2.idproducto,sum(cantidadsolicitadaoc) as cantidadsolicitadaoc", "$condicion registrado=0 and t1.idordencompra=t2.idordencompra and t2.idproducto=t3.idproducto and t3.idlinea=t4.idlinea", "", "group by t2.idproducto");
        return $producto;
    }

    function grabaOrdenCompra($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function contarOrdenCompra() {
        $cantidadOrdenCompra = $this->contarRegistro($this->tabla);
        return $cantidadOrdenCompra;
    }

    function contarOrdenCompraNoRegistrado() {
        $cantOrdCom = $this->contarRegistro($this->tabla, "registrado=0");
        return $cantOrdCom;
    }
    
    function solicitarCifventascpa($codigoc) {
        $data = $this->leeRegistro($this->tabla, "cifcpa", "codigooc='$codigoc'", "", "limit 1");
        return $data[0]['cifcpa'];
    }

    function editaOrdenCompra($idOrdenCompra) {
        $data = $this->leeRegistro($this->tabla, "", "idordencompra=$idOrdenCompra", "");
        return $data;
    }

    function eliminaOrdenCompra($idOrdenCompra) {
        $exito = $this->cambiaEstado($this->tabla, "idordencompra=$idOrdenCompra");
        return $exito;
    }

    function actualizaOrdenCompra($data, $idOrdenCompra) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idordencompra=$idOrdenCompra");
        return $exito;
    }

    function buscaOrdenCompra($idOrdenCompra) {
        $data = $this->leeRegistro($this->tabla, "", "idordencompra=$idOrdenCompra", "");
        return $data;
    }

    function buscaxvendedor($idProveedor, $fecha, $fechaInicio, $fechaFinal) {
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
        $data = $this->leeRegistro($this->tabla, "", "idordencompra=$idOrdenCompra", "");
        return $data;
    }

    function generaCodigo() {
        $data = $this->leeRegistro($this->tabla, "CONCAT( 'OC-',DATE_FORMAT( NOW( ) ,  '%y' ) , LPAD(  (MAX(SUBSTRING(`codigooc`,6,6))+1) , 6,  '0' ) )  as codigo", " year(fechacreacion)=year(now()) ", "");
        $codigo = "";
        if ($data[0]['codigo'] != "") {
            return $data[0]['codigo'];
        } else {
            return "OC-" . date('y') . str_pad(1, 6, '0', STR_PAD_LEFT);
        }
    }

    function OrdenCuadroUtilidad($idOrdenCompra) {
        $data = $this->leeRegistro(
                "wc_ordencompra oc inner join wc_almacen a on oc.`idalmacen`=a.`idalmacen`
					inner join wc_proveedor p on p.`idproveedor`=oc.`idproveedor`
					", "", "idordencompra='$idOrdenCompra'", "");
        return $data;
    }

    function ListaCuadroUtilidad($year) {
        $data = $this->leeRegistro(
                "wc_ordencompra oc inner join wc_almacen a on oc.`idalmacen`=a.`idalmacen`
					inner join wc_proveedor p on p.`idproveedor`=oc.`idproveedor`
					", "", "YEAR(oc.fechacreacion)='$year' and oc.estado=1", "");
        return $data;
    }

    function OrdenesValorizados($filtro = "") {
        $data = $this->leeRegistro($this->tabla, "", " estado='1' and valorizado='1'" . $filtro, "");
        return $data;
    }

    function fechaxOrdenes() {
        $data = $this->leeRegistro($this->tabla, "", " estado='1' and valorizado='1' group by year(fechacreacion)", "");
        return $data;
    }

    function TipoCambioxIdOrdenCompra($idOrdenCompra) {
        return $this->leeRegistro($this->tabla, "tipocambiovigente", "idordencompra=$idOrdenCompra", "");
    }

    function listaOrdenCompraPaginado($pagina) {
        $data = $this->leeRegistroPaginado(
                "wc_ordencompra oc inner join wc_almacen a on oc.idalmacen=a.idalmacen 
				inner join wc_proveedor p on  oc.idproveedor=p.idproveedor
				", "", "oc.estado=1", "oc.fechacreacion desc", $pagina);
        return $data;
    }

    function paginadoOrdenCompra() {
        return $this->paginado(
                        "wc_ordencompra oc inner join wc_almacen a on oc.idalmacen=a.idalmacen 
				inner join wc_proveedor p on oc.idproveedor=p.idproveedor
				", "oc.estado=1");
    }

    function autoCompleteAprobados($codigoOrdenCompra) {
        $data = $this->leeRegistro("wc_ordencompra", "", "codigooc LIKE '%$codigoOrdenCompra%' ", "", "");

        foreach ($data as $valor) {
            $dato[] = array("value" => $valor['codigooc'],
                "label" => $valor['codigooc'],
                "id" => $valor['idordencompra'],
            );
        }
        return $dato;
    }

    function listarordenes($codigoOrdenCompra) {
        $cliente = $this->leeRegistro("`wc_ordencompra`", "distinct idordencompra, codigooc", "codigooc LIKE '%$codigoOrdenCompra%' and estado = 1", "codigooc", "limit 0,10");
        $modoFacturacion = $this->modoFacturacion();
        foreach ($cliente as $valor) {
            $dato[] = array("value" => $valor['codigooc'],
                "label" => $valor['codigooc'],
                "id" => $valor['idordencompra'],
            );
        }
        return $dato;
    }

    function reporteOrdenCompraProducto($idproducto) {
        $sql = "select oc.codigooc, p.codigopa, p.nompro, um.nombre as nommedida,lin.nomlin as linea, sublin.nomlin as sublinea, (case when faproxllegada is null then oc.fordencompra else oc.faproxllegada end) as fecha
                            from wc_ordencompra oc
                            inner join wc_detalleordencompra doc on doc.idordencompra = oc.idordencompra
                            inner join wc_producto p on p.idproducto = doc.idproducto
                            inner join wc_unidadmedida um on um.idunidadmedida = p.unidadmedida 
                            inner join wc_linea sublin on sublin.idlinea = p.idlinea
                            INNER JOIN wc_linea lin on lin.idlinea = sublin.idpadre
                            where doc.estado = 1 and oc.estado = 1 and p.estado = 1 and doc.idproducto = '" . $idproducto . "'
                            order by fecha DESC 
                            limit 1";
        return $this->EjecutaConsulta($sql);
    }

    function reporteDetalleOrdenCompra($idorden) {
        $sql = "select p.idproducto, p.codigopa, p.stockactual, doc.cantidadrecibidaoc, doc.fobdoc, um.nombre as medida, (doc.cantidadrecibidaoc*doc.fobdoc) as fobtotal, (case when oc.faproxllegada is null then oc.fordencompra else oc.faproxllegada end) as fecha 
                                from wc_ordencompra oc
                        inner join wc_detalleordencompra doc on doc.idordencompra = oc.idordencompra
                        inner join wc_producto p on p.idproducto = doc.idproducto
                        inner join wc_unidadmedida um on um.idunidadmedida = p.unidadmedida
                        where oc.idordencompra = '" . $idorden . "'";
        return $this->EjecutaConsulta($sql);
    }

    function reporteDetalleOrdenCompra2($idproducto, $fecha) {
        $sql = "select SUM(dov.preciofinal)/count(*) as preciototal, (SUM(dov.cantdespacho) - SUM(dov.cantdevuelta)) vendida, SUM(ov.es_credito) as credito, SUM(ov.es_contado) as contado
                            from wc_detalleordenventa dov
                            inner join wc_producto p on p.idproducto = dov.idproducto	 
                            inner join wc_ordenventa ov on ov.idordenventa = dov.idordenventa
                            where  dov.idproducto='" . $idproducto . "' and ov.fordenventa>='" . $fecha . "' and dov.cantdespacho != cantdevuelta";
        return $this->EjecutaConsulta($sql);
    }

    function reporteCostodeProducto() {
        $sql = "select p.idproducto, p.codigopa, p.nompro, doc.totalunitario
                                from wc_producto p 
                        inner join wc_detalleordencompra doc on doc.idproducto = p.idproducto
                        where p.stockactual >= 1 and p.estado = 1
                        order by p.idproducto, doc.iddetalleordencompra desc";
        return $this->EjecutaConsulta($sql);
    }

    function grabaPagoOrdenCompra($data) {
        $exito = $this->grabaRegistro('wc_pagocompra', $data);
        return $exito;
    }

    function autoCompleteAprobadosSinDua($codigoOrdenCompra, $formato = 0) {        
        $data = $this->leeRegistro("wc_ordencompra oc " .
                "inner join wc_proveedor proveedor on proveedor.idproveedor = oc.idproveedor", "oc.codigooc, oc.idordencompra, oc.vbimportaciones, proveedor.razonsocialp", "oc.codigooc LIKE '%$codigoOrdenCompra%' and oc.nuevoformato='$formato' and oc.actualizado=''", "oc.codigooc asc", "");
        foreach ($data as $valor) {
            $dato[] = array("value" => $valor['codigooc'],
                "label" => $valor['codigooc'],
                "id" => $valor['idordencompra'],
                "vbimportaciones" => $valor['vbimportaciones'],
                "proveedor" => $valor['razonsocialp'],
            );
        }
        return $dato;
    }

}

?>