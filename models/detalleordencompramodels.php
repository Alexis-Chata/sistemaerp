<?php
Class Detalleordencompra extends Applicationbase{

		private $tabla="wc_detalleordencompra";
		private $tablas="wc_detalleordencompra,wc_producto";

		function grabaDetalleOrdenCompra($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
                function listaDetalleOrdenCompraxproducto($idOrdenCompra, $idproducto){
			$sql="Select doc.*,p.*,m.nombre as marca,um.nombre as unidadmedida,oc.vbimportaciones,oc.tipocambiovigente as tipocambiovigenteoc
					From wc_detalleordencompra doc
					Inner join wc_ordencompra oc On doc.idordencompra=oc.idordencompra
					Inner Join wc_producto p On doc.idproducto=p.idProducto
					Left Join wc_marca m On p.idmarca=m.idmarca
					Left Join wc_unidadmedida um On um.idunidadmedida=p.unidadmedida
				 Where doc.estado=1 and doc.idordencompra='".$idOrdenCompra. "' and doc.idproducto='$idproducto'";
                        
			$data=$this->EjecutaConsulta($sql);
			//$data=$this->leeRegistro2($this->tablas,"","idordencompra=$idOrdenCompra","");
			return $data;
		}
		function listaDetalleOrdenCompra($idOrdenCompra){
			$sql="Select doc.*,p.*,m.nombre as marca,um.nombre as unidadmedida,oc.vbimportaciones 
					From wc_detalleordencompra doc
					Inner join wc_ordencompra oc On doc.idordencompra=oc.idordencompra
					Inner Join wc_producto p On doc.idproducto=p.idProducto
					Left Join wc_marca m On p.idmarca=m.idmarca
					Left Join wc_unidadmedida um On um.idunidadmedida=p.unidadmedida
				 Where doc.estado=1 and doc.idordencompra=".$idOrdenCompra;
                        
			$data=$this->EjecutaConsulta($sql);
			//$data=$this->leeRegistro2($this->tablas,"","idordencompra=$idOrdenCompra","");
			return $data;
		}
		function listaDetalleOrdenCompraxFecha($year){
			$sql="Select doc.*,p.*,m.nombre as marca,um.nombre as unidadmedida,oc.vbimportaciones 
					From wc_detalleordencompra doc
					Inner join wc_ordencompra oc On doc.idordencompra=oc.idordencompra
					Inner Join wc_producto p On doc.idproducto=p.idProducto
					Left Join wc_marca m On p.idmarca=m.idmarca
					Left Join wc_unidadmedida um On um.idunidadmedida=p.unidadmedida
				 Where doc.estado=1 and year(oc.fechacreacion)=".$year;
                        
			$data=$this->EjecutaConsulta($sql);
			//$data=$this->leeRegistro2($this->tablas,"","idordencompra=$idOrdenCompra","");
			return $data;
		}
		function exiteProductoDetalleOrdenCompra($idProducto,$idOrdenCompra){
			$existe=$this->exiteRegistro($this->tabla,"idproducto=$idProducto AND idordencompra=$idOrdenCompra");
			return $existe;
		}
		function actualizaDetalleOrdenCompra($data,$idDetalleOrdenCompra){
			$exito=$this->actualizaRegistro($this->tabla,$data,"iddetalleordencompra=".$idDetalleOrdenCompra);
			return $exito;
		}
		//Busqueda por idordencompra y iddetalleordencompra
		function buscarDetalleOrdenCompra($idOrdenCompra,$idDetalleOrdenCompra){
			$data=$this->leeRegistro($this->tabla,"","idordencompra=$idOrdenCompra AND iddetalleordencompra=$idDetalleOrdenCompra","");
			return $data;
		}
		function buscaDetalleOrdenCompra($idOrdenCompra){
			$data=$this->leeRegistro($this->tabla,"","idordencompra=$idOrdenCompra and estado=1","");
			return $data;
		}
		function sumaCantidadProducto($filtro){
			$data=$this->leeRegistro("wc_ordencompra as oc INNER JOIN wc_detalleordencompra as doc ON oc.idordencompra=doc.idordencompra","sum(doc.cantidadsolicitadaoc)",$filtro,"");
			$respuesta=empty($data[0]['sum(doc.cantidadsolicitadaoc)'])?0:($data[0]['sum(doc.cantidadsolicitadaoc)']);
			return $respuesta;
		}
                function listaPagoOrdenCompra($idOrdenCompra){
			$sql="select * from wc_pagoCompra where idordencompra = $idOrdenCompra and estado = 1";                        
                        $data=$this->EjecutaConsulta($sql);
			return $data;
		}

		function productosxagotar($idAlmacen, $idLinea, $idSubLinea, $idProducto){
                    $condicion = 'producto.estado = 1';
                    if (!empty($idAlmacen)) {
                        $condicion .= " and almacen.idalmacen=$idAlmacen";
                    }
                    if (!empty($idLinea)) {
                        $condicion .= " and linea.idlinea=$idLinea";
                    }
                    if (!empty($idSubLinea)) {
                        $condicion .= " and sublinea.idlinea=$idSubLinea";
                    }
                    if (!empty($idProducto)) {
                        $condicion .= " and producto.idproducto=$idProducto";
                    }
                    $sql="select  
                                linea.idlinea,
                                linea.nomlin as nombrelinea,
                                sublinea.nomlin,
                                producto.idproducto, 
                                producto.codigopa, 
                                producto.nompro, 
                                producto.stockactual, 
                                producto.stockdisponible,
                                oc.fordencompra,
                                doc.fobdoc,
                                producto.cifventasdolares,
                                producto.preciolistadolares,
                                doc.cantidadrecibidaoc,
                                doc.cantidadrecibidaoc*0.1
                            from wc_producto producto
                            inner join wc_detalleordencompra as doc on
                                                            doc.idproducto = producto.idproducto and
                                                            doc.estado = 1
                            inner join wc_ordencompra oc on
                                                      oc.idordencompra = doc.idordencompra
                            inner join wc_linea sublinea on sublinea.idlinea = producto.idlinea
                            inner join wc_linea linea on linea.idlinea = sublinea.idpadre
                            inner join wc_almacen almacen on almacen.idalmacen = producto.idalmacen
                            where " . $condicion . "
                            order by linea.idlinea asc, sublinea.nomlin asc, producto.idproducto asc, oc.fordencompra desc";
                        
			$data=$this->EjecutaConsulta($sql);
			return $data;
		}
	}
?>