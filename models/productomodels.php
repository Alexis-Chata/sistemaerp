<?php

class Producto extends Applicationbase
{

    private $tabla = "wc_producto";
	private $tabla2 = "wc_producto,wc_linea";
	private $tabla3 = "wc_tipoproducto";
	private $tabla4 = "wc_productopadre";

	function listatipoproductoxId($id)
	{
		$data = $this->leeRegistro($this->tabla3, "idtipoproducto,nombretipoproducto", "idtipoproducto='$id' and estado=1", "");
		return $data;
	}

    function listadoProductos()
    {
        $producto = $this->leeRegistro($this->tabla, "", "estado=1", "", "");
        return $producto;
    }

    function listadoProductosTotal()
    {
        $producto = $this->leeRegistro($this->tabla, "", "", "", "");
        return $producto;
    }

    function listaProductosPaginado($pagina)
    {
        $data = $this->leeRegistroPaginado(
            $this->tabla,
            "",
            "estado=1",
            "idproducto DESC",
            $pagina
        );
        return $data;
    }

    function listaProductosPaginadoxnombre($pagina, $condicion = "")
    {
        $condicion = ($condicion != "") ? htmlentities($condicion, ENT_QUOTES, 'UTF-8') : "";
        $data = $this->leeRegistroPaginado(
            $this->tabla,
            "",
            "(nompro like '%$condicion%') or (codigopa like '$condicion%')  and estado=1  ",
            "",
            $pagina
        );
        return $data;
    }

    function paginadoProductosxnombre($condicion = "")
    {
        $condicion = ($condicion != "") ? htmlentities($condicion, ENT_QUOTES, 'UTF-8') : "";
        return $this->paginado($this->tabla, "nompro like '$condicion%' or codigopa like '$condicion%'  and estado=1");
    }

    public function BuscarRegistrosxnombre($data)
    {
        $data = htmlentities($data, ENT_QUOTES, 'UTF-8');
        $condicion = "(nompro like '%$data%') or
			(codigopa like '%$data%')  and
			estado=1";
        $data = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        return $data;
    }

    function paginadoProducto()
    {
        return $this->paginado($this->tabla, "estado=1");
    }

    function listadoProductos2($nombre)
    {
        $nombre = htmlentities($nombre, ENT_QUOTES, 'UTF-8');
        $condicion = "";
        if (!empty($nombre)) {
            $condicion = " and nompro like '%$nombre%'";
        }
        $producto = $this->leeRegistro($this->tabla, "nompro,stockactual,stockdisponible", "estado=1 $condicion", "", "");
        return $producto;
    }

    function listaPrecio($idLinea, $idSubLinea)
    {
        $condicion = "";
        if (!empty($idLinea)) {
            $condicion = "idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "t2.idlinea=$idSubLinea";
        }
        $producto = $this->leeRegistro2($this->tabla2, "", "$condicion", "");
        return $producto;
    }

    function inventario($idAlmacen, $idLinea, $idSubLinea, $idProducto)
    {
        $condicion = "";
        if (!empty($idAlmacen)) {
            $condicion = "idalmacen=$idAlmacen";
        }
        if (!empty($idLinea)) {
            $condicion = "idpadre=$idLinea";
        }
        if (!empty($idSubLinea)) {
            $condicion = "t1.idlinea=$idSubLinea";
        }
        if (!empty($idProducto)) {
            $condicion = "idproducto=$idProducto";
        }
        $producto = $this->leeRegistro2(
			$this->tabla2, 
			"idproducto,imagen,codigopa,nompro,preciolista,stockactual,'0' as stockporllegar,'0' as stockpordespachar,unidadmedida,empaque", 
			"$condicion", 
			""
		);
        return $producto;
    }

    function grabaProducto($data)
    {
        $exito = $this->grabaRegistro($this->tabla, $data);

        return $exito;
	}
	function grabaProductoPadre($data)
	{
		$exito = $this->grabaRegistro($this->tabla4, $data);

		return  $exito;
	}
	function  buscaProductoPadre($idproductorepuesto)
	{
		$producto = $this->leeRegistro($this->tabla4, "*", "idproductorepuesto='$idproductorepuesto'", "");
		return $producto;
	}
	function  obteneridnombre($idProducto)
	{
		$producto = $this->leeRegistro($this->tabla, "idproducto, nompro", "idproducto='$idProducto'", "");
		return $producto;
	}
    function buscaProducto($idProducto)
    {
        $producto = $this->leeRegistro($this->tabla, "*", "idproducto='$idProducto'", "");
        return $producto;
    }

    function buscaProductoxId($idproducto)
    {
        $sql = "Select p.precioreferencia01,p.precioreferencia02,p.precioreferencia03,p.idproducto,p.codigopa,p.nompro,p.codigop,p.preciocosto,p.preciolista,p.stockactual,p.stockdisponible,p.idmarca
				From wc_producto p
				Where  idproducto=" . $idproducto;
        return $this->EjecutaConsulta($sql);
    }

    function durezaProducto($idproducto, $tipo)
    {
        if ($tipo == 1) {
            $sql = "select p.codigopa, p.nompro, p.stockactual, um.nombre as nommedida,lin.nomlin as linea, sublin.nomlin as sublinea, (case when faproxllegada is null then oc.fordencompra else oc.faproxllegada end) as fecha, doc.cantidadrecibidaoc as stockinicial
                                from wc_ordencompra oc
                                inner join wc_detalleordencompra doc on doc.idordencompra = oc.idordencompra
                                inner join wc_producto p on p.idproducto = doc.idproducto
                                inner join wc_unidadmedida um on um.idunidadmedida = p.unidadmedida
                                inner join wc_linea sublin on sublin.idlinea = p.idlinea
                                INNER JOIN wc_linea lin on lin.idlinea = sublin.idpadre
                                where doc.idproducto = '" . $idproducto . "'
                                order by doc.idordencompra DESC
                                limit 1";
        } else {
            $sql = "select p.codigopa, p.nompro, p.stockactual, p.cif, um.nombre as nommedida,lin.nomlin as linea, sublin.nomlin as sublinea
                                from wc_producto p
                                inner join wc_unidadmedida um on um.idunidadmedida = p.unidadmedida
                                inner join wc_linea sublin on sublin.idlinea = p.idlinea
                                INNER JOIN wc_linea lin on lin.idlinea = sublin.idpadre
                                where p.idproducto = '" . $idproducto . "'
                                limit 1";
        }
        return $this->EjecutaConsulta($sql);
    }

    function buscaxcodigo($codProducto)
    {
        $codProducto = htmlentities($codProducto, ENT_QUOTES, 'UTF-8');
        $producto = $this->leeRegistro($this->tabla, "", 'codigopa="' . $codProducto . '"', "");
        return $producto;
    }

    function buscaProductoOrdenCompra($idProducto)
    {
        $producto = $this->leeRegistro($this->tabla, "", "idproducto=$idProducto", "", "");
        return $producto;
    }

    function buscaProductoAutocomplete($texIni, $idLinea = "")
    {
        $texIni = htmlentities($texIni, ENT_QUOTES, 'UTF-8');
        //(Stock: ".$valor['stockdisponible']." ) esto se quito al titulo de control por mientras
        $condicion = "estado=1 and (codigopa LIKE '$texIni%') and preciolista!=0 and preciocosto!=0 ";
        if (!empty($idLinea)) {
            $condicion .= "AND idlineapadre=$idLinea";
        }
        $producto = $this->leeRegistro($this->tabla, "codigopa,nompro,idproducto,stockactual,stockdisponible,actualizado", "$condicion", "codigopa", "limit 0,15");
        foreach ($producto as $valor) {
            //$mensaje_SxV=" =======>>> STOCK POR VERIFICAR <========";
            $mensaje_SV = "Disponible:" . $valor['stockdisponible'] . " - Real:" . $valor['stockactual'];
            $titulocontrol = (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8'));
            $titulolista = (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8')) . " ";
            //if($valor['actualizado']==1){
            $titulolista .= $mensaje_SV;
            //}else{
            //$titulolista.=$mensaje_SxV;
            //
            //$dato[]=array("value"=>$valor['codigopa'],"label"=>$valor['codigopa']." ".$valor['nompro'],"id"=>$valor['idproducto']);
            $dato[] = array("value" => (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')), "label" => $titulolista, "id" => $valor['idproducto'], "tituloProducto" => $titulocontrol);
        }
        return $dato;
    }

	function buscaProductoAutocompleteRepuesto($texIni, $idLinea = "")
	{
		$texIni = htmlentities($texIni, ENT_QUOTES, 'UTF-8');
		$condicion = "idtipoproducto=1 and `wc_producto`.estado=1 and (codigopa LIKE '$texIni%')";
		if (!empty($idLinea)) {
			$condicion .= "AND idlineapadre=$idLinea";
		}
		$producto = $this->leeRegistro($this->tabla, "codigopa,nompro,idproducto,stockactual,stockdisponible,actualizado,imagen", "$condicion", "codigopa", "limit 0,15");
		foreach ($producto as $valor) {
			$condicionpadre = "idproductorepuesto=" . $valor['idproducto'];
			$productoPadre = $this->leeRegistro("`wc_productopadre`
			INNER JOIN `dbceletium`.`wc_producto` 
				ON (`wc_productopadre`.`idproducto` = `wc_producto`.`idproducto`)", "`wc_productopadre`.`idproductorepuesto`,codigopa,nompro,`wc_producto`.idproducto", "$condicionpadre", "codigopa", "");
			//var_dump($productoPadre);die();
			$mensaje_SV = "Disponible:" . $valor['stockdisponible'] . " - Real:" . $valor['stockactual'];
			$titulocontrol = (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8'));
			$titulolista = (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8')) . " ";
			$titulolista .= $mensaje_SV;
			$dato[] = array("value" => (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')), "label" => $titulolista, "id" => $valor['idproducto'], "tituloProducto" => $titulocontrol, "imagen" => $valor['imagen'], "productospadre" => $productoPadre);
		} //var_dump($dato);die();
		return $dato;
	}

    function buscaProductoAutocompletej($texIni, $idLinea = "")
    {
        $texIni = htmlentities($texIni, ENT_QUOTES, 'UTF-8');
        //(Stock: ".$valor['stockdisponible']." ) esto se quito al titulo de control por mientras
        $condicion = "estado=1 and (codigopa LIKE '$texIni%' or nompro LIKE '$texIni%') and preciolista!=0 and preciocosto!=0 ";
        //                        $condicion="estado=1 and (codigopa LIKE '$texIni%') and preciolista!=0 and preciocosto!=0 ";
        if (!empty($idLinea)) {
            $condicion .= "AND idlineapadre=$idLinea";
        }
        $producto = $this->leeRegistro($this->tabla, "codigopa,nompro,idproducto,stockactual,stockdisponible,actualizado", "$condicion", "codigopa", "limit 0,15");
        foreach ($producto as $valor) {
            $mensaje_SxV = " =======>>> STOCK POR VERIFICAR <========";
            $mensaje_SV = "Disponible:" . $valor['stockdisponible'] . " - Real:" . $valor['stockactual'];
            $titulocontrol = (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8'));
            $titulolista = (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')) . "::" . (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8')) . " ";
            $titulolista;
            //				if($valor['actualizado']==1){
            //					$titulolista.=$mensaje_SV;
            //				}else{
            //					$titulolista.=$mensaje_SxV;
            //				}
            //$dato[]=array("value"=>$valor['codigopa'],"label"=>$valor['codigopa']." ".$valor['nompro'],"id"=>$valor['idproducto']);
            $dato[] = array("value" => (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')), "label" => $titulolista, "id" => $valor['idproducto'], "tituloProducto" => $titulocontrol);
        }
        return $dato;
    }

    function buscaProductoAutocompleteLimpio($texIni, $idLinea = "")
    {
        $texIni = htmlentities($texIni, ENT_QUOTES, 'UTF-8');
        $condicion = "p.estado=1 and (p.codigopa LIKE '$texIni%') and p.preciolista!=0 and p.preciocosto!=0 ";
        if (!empty($idLinea)) {
            $condicion .= "AND idlineapadre=$idLinea";
        }
        $producto = $this->leeRegistro("wc_producto as p left join wc_unidadmedida as u on p.unidadmedida=u.idunidadmedida", "p.unidadmedida,p.codigopa,p.nompro,p.idproducto,p.stockactual,p.stockdisponible,p.imagen,p.idalmacen,u.cod_sunat,u.nombre", "$condicion", "", "limit 0,15");
        foreach ($producto as $valor) {
            $titulocontrol = (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8'));
            $titulolista = (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8')) . "";
            $imagen = $valor['imagen'];

            $dato[] = array("value" => (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')), "unidad" => $valor['unidadmedida'], "almacen" => $valor['idalmacen'], "label" => $titulolista, "id" => $valor['idproducto'], "tituloProducto" => $titulocontrol, "imagen" => $imagen, "cod_sunat" => $valor['cod_sunat'] . ' (' . $valor['nombre'] . ')');
        }
        return $dato;
    }

    function buscaProductoAutocompleteLimpioRep($texIni, $idLinea = "")
    {
        $texIni = htmlentities($texIni, ENT_QUOTES, 'UTF-8');
        $condicion = "p.estado=1 and (p.codigopa LIKE '$texIni%') and p.idtipoproducto=1 ";
        if (!empty($idLinea)) {
            $condicion .= "AND idlineapadre=$idLinea";
        }
        $producto = $this->leeRegistro("wc_producto as p left join wc_unidadmedida as u on p.unidadmedida=u.idunidadmedida", "p.unidadmedida,p.codigopa,p.nompro,p.idproducto,p.stockactual,p.stockdisponible,p.imagen,p.idalmacen,u.cod_sunat,u.nombre", "$condicion", "", "limit 0,15");
        foreach ($producto as $valor) {
            $titulocontrol = (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8'));
            $titulolista = (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8')) . "";
            $imagen = $valor['imagen'];

            $dato[] = array("value" => (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')), "unidad" => $valor['unidadmedida'], "almacen" => $valor['idalmacen'], "label" => $titulolista, "id" => $valor['idproducto'], "tituloProducto" => $titulocontrol, "imagen" => $imagen, "cod_sunat" => $valor['cod_sunat'] . ' (' . $valor['nombre'] . ')');
        }
        return $dato;
    }

    function buscaProductoAutocompleteCompras($texIni, $idLinea = "")
    {
        $texIni = htmlentities($texIni, ENT_QUOTES, 'UTF-8');
        $condicion = "estado=1 and (codigopa LIKE '$texIni%')";
        if (!empty($idLinea)) {
            $condicion .= "AND idlineapadre=$idLinea";
        }
        $producto = $this->leeRegistro($this->tabla, "unidadmedida,codigopa,nompro,idproducto,stockactual,stockdisponible,imagen,idalmacen", "$condicion", "", "limit 0,15");
        foreach ($producto as $valor) {
            $titulocontrol = (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8'));
            $titulolista = (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')) . " " . (html_entity_decode($valor['nompro'], ENT_QUOTES, 'UTF-8')) . "";
            $imagen = $valor['imagen'];

            $dato[] = array("value" => (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')), "unidad" => $valor['unidadmedida'], "almacen" => $valor['idalmacen'], "label" => $titulolista, "id" => $valor['idproducto'], "tituloProducto" => $titulocontrol, "imagen" => $imagen);
        }
        return $dato;
    }

    function contarProducto($codProducto = "")
    {
        $codProducto = htmlentities($codProducto, ENT_QUOTES, 'UTF-8');
        $condicion = "estado=1";
        if (!empty($codProducto)) {
            $condicion = " AND codigopa='$codProducto";
        }
        $cantidad = $this->contarRegistro($this->tabla, "$condicion");
        return $cantidad;
    }

    function actualizaProducto($data, $idProducto)
    {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idproducto=$idProducto");
        return $exito;
    }

    function actualizaProductoxCodigo($data, $codigo)
    {
        $exito = $this->actualizaRegistro($this->tabla, $data, "codigopa='" . htmlentities($codigo, ENT_QUOTES, 'UTF-8') . "'");

        return $exito;
    }

    function eliminaProducto($idProducto)
    {
        $exito = $this->cambiaEstado($this->tabla, "idproducto=$idProducto");
        return $exito;
    }
	function eliminaProductoPadre($idProducto)
	{
		$exito = $this->eliminaRegistro($this->tabla4, "idproductorepuesto=$idProducto");
		return $exito;
	}
    function existeProducto($codigoProducto)
    {
        $data = $this->leeRegistro($this->tabla, "idproducto", 'idproducto="' . htmlentities($codigoProducto, ENT_QUOTES, 'UTF-8') . '"', "");
        if (count($data) >= 1) {
            return 1;
        } else {
            return 0;
        }
    }

    function paginacion($tamanio, $condicion = "")
    {
        $condicion = ($condicion != "") ? $condicion .= " and" : "";
        $data = $this->leeRegistro($this->tabla, "idalmacen", "$condicion estado=1", "", "");
        $paginas = ceil(count($data) / $tamanio);
        return $paginas;
    }

    function buscarxnombre($inicio, $tamanio, $nombre)
    {
        $nombre = htmlentities($nombre, ENT_QUOTES, 'UTF-8');
        $inicio = ($inicio - 1) * $tamanio;
        if ($inicio < 0) {
            $inicio = 0;
        }
        $data = $this->leeRegistro($this->tabla, "", "concat(codigopa,' ',nompro) like '%$nombre%' and estado=1", "", "limit $inicio,$tamanio");
        return $data;
    }

    function autocomplete($tex)
    {
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro($this->tabla, "codigopa,idproducto", "concat(codigopa,' ',nompro) LIKE '%$tex%'", "", "limit 0,15");
        foreach ($datos as $valor) {
            $dato[] = array("value" => (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')), "label" => (html_entity_decode($valor['codigopa'], ENT_QUOTES, 'UTF-8')), "id" => $valor['idproducto']);
        }
        return $dato;
    }

    public function GeneraCodigo($id)
    {
        $maxcodigo = $this->leeRegistro($this->tabla, "max(idproducto)", "", "", "");
        $data['codigop'] = 'PDR' . str_pad($maxcodigo[0]['max(idproducto)'], 5, '0', STR_PAD_LEFT);
        $this->actualizaRegistro($this->tabla, $data, "idproducto=" . $maxcodigo[0]['max(idproducto)']);
        return $data;
    }

    public function ValorizadoxLinea($idFecha)
    {
        $sql = "select lin.nomlin,sum(prd.cifventasdolares*(select detll.stockActual from wc_detallemovimiento as detll where detll.idproducto=prd.idproducto and detll.fechacreacion<='" . $idFecha . " 23:59:59' order by detll.fechacreacion desc limit 1)) as valorizado
                        from wc_producto prd
                        inner join wc_linea slin On prd.idlinea=slin.idlinea
                        Inner Join wc_linea lin On slin.idpadre=lin.idlinea
                        group by slin.idpadre;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    public function getTotalProductos()
    {
        $data = $this->leeRegistro($this->tabla, 'idproducto,codigopa, nompro, stockdisponible, preciolistadolares', 'estado = 1', 'nompro'); //-- and (idproducto = 1819 or idproducto = 1512)
        return $data;
    }

    public function sumaTotalProductosCompra($fecha, $idProducto)
    {
        $sql = "select
                    sum(doc.cantidadrecibidaoc) as compras
                    from wc_ordencompra oc
                    inner join wc_detalleordencompra doc on doc.idordencompra = oc.idordencompra
                    inner join wc_producto p on p.idProducto = doc.idProducto
                    where oc.estado = 1
                    and doc.estado = 1
                    and oc.fordencompra > '" . $fecha . "'
                    and p.idproducto = " . $idProducto;
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    public function sumaTotalProductosVenta($fecha, $idProducto)
    {
        $sql = "select distinct
                        ov.codigov,
                        dov.cantdespacho,
                        dm.cantidad as cantid
                        from wc_ordenventa ov
                        inner join wc_detalleordenventa dov on dov.idordenventa = ov.idordenventa
                        inner join wc_producto p on p.idProducto = dov.idProducto
                        inner join wc_movimiento m on m.idordenventa = ov.idordenventa
                        inner join wc_detallemovimiento dm on dm.idmovimiento = m.idmovimiento
                        and dm.idproducto = p.idproducto
                        where ov.estado = 1
                        and dov.estado = 1
                        and p.idproducto = " . $idProducto . "
                        and m.fechamovimiento > '" . $fecha . "'";
        $datos = $this->EjecutaConsulta($sql);
        $cantidad = count($datos);
        $suma = 0;
        for ($i = 0; $i < $cantidad; $i++) {
            $suma += $datos[$i]['cantid'];
        }
        return $suma;
    }

    public function sumaTotalDevolucion($fecha, $idProducto)
    {
        $sql = "select
                    ov.codigov,
                    dov.cantdespacho,
                    dm.cantidad,
                    dev.iddevolucion
                    from wc_ordenventa ov
                    inner join wc_detalleordenventa dov on dov.idordenventa = ov.idordenventa
                    inner join wc_producto p on p.idProducto = dov.idProducto
                    inner join wc_movimiento m on m.idordenventa = ov.idordenventa
                    inner join wc_detallemovimiento dm on dm.idmovimiento = m.idmovimiento
                    and dm.idproducto = p.idproducto
                    inner join wc_devolucion dev on dev.idordenventa = ov.idordenventa
                    and dev.iddevolucion = m.iddevolucion
                    where ov.estado = 1
                    and dov.estado = 1
                    and m.fechamovimiento > '" . $fecha . "'
                    and p.idproducto = " . $idProducto;
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
}
