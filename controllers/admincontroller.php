<?php  
class AdminController extends applicationgeneral{
/*
	function actualizaproductos(){
		$rrruta="D:\sistema_erp\bd\prod.txt";
		$archivo=fopen($rrruta,"r");
		while(!feof($archivo)){
			$fila=fgets($archivo);
			$datafila=explode(",", $fila);
			$data['stockactual']=$datafila[1];
			$data['stockdisponible']=$datafila[1];
			$data['preciolista']=$datafila[2];
			$data['preciocosto']=$datafila[2]/1.3;
			$codigo=$datafila[0];
			$producto=New Producto();
			$exito=$producto->actualizaProductoxCodigo($data,$codigo);
			if($exito){
				echo "Actualizo ".$codigo."<br>";
				$buenos++;
			}else{
				$ruta2="D:\sistema_erp\bd\prod2.txt";
				$tt=fopen($ruta2,"ab");
				$cadena=$codigo."\n";
				fwrite($tt,$cadena);
				fclose($tt);
				echo "<b>Error en ".$codigo."</b><br>";
				$malos++;

			}

		}
		fclose($archivo);
		echo "Exitos = ".$buenos."<br>";
		echo "<b>No encontrados = ".$malos."</b><br>";
		exit;
	}

	function llenaSucursales(){
		$cliente=$this->AutoLoadModel('cliente');
		$clientezona=$this->AutoLoadModel('clientezona');
		$dataCliente=$cliente->listadoTotalClientes();
		$cantidadCliente=count($dataCliente);
		$cont=0;
		for ($i=0; $i <$cantidadCliente ; $i++) { 
			$data['direccion_despacho_contacto ']=$dataCliente[$i]['direccion'];
			$data['direccion_fiscal ']=$dataCliente[$i]['direccion'];
			$data['nomcontacto ']=$dataCliente[$i]['nombre_contacto'];
			$filtro="idcliente='".$dataCliente[$i]['idcliente']."'";
			$graba=$clientezona->actualizaCliente($data,$filtro);
			if ($graba) {
				$cont++;
			}
		}
		echo "Se grabaron ".$cont." de ".$cantidadCliente;
	}



	function cargaInventario(){
		$detalleInventario=$this->AutoLoadModel('detalleinventario');
		$producto=$this->AutoLoadModel('producto');
		$pp=new Proveedorproducto();

		$cont=0;
		$contP=0;
		$contI=0;
		$cont3=0;

		$ruta="D:\sistema_erp\bd\inventario.txt";
		//$ruta="C:\wamp\www\bd\inventario.txt";
		$archivo=fopen($ruta,"r");
		while(!feof($archivo)){
			$fila=fgets($archivo);
			$datafila=explode(";", $fila);
			$codigo=$datafila[0];



			$dataProducto=$producto->buscaxcodigo($codigo);
			
			if (empty($dataProducto)) {
				$data["codigopa"]=$datafila[0];
				//$data['nompro']=str_replace(search, replace, subject)$datafila[1];
				$data['nompro']=str_replace("'", "", $datafila[1]);
				$data['idalmacen']=9;
				$data['idlinea']=1000;
				$data['fob']=round($datafila[8],2);
				$data['cif']=round($datafila[9]*2.9,2);
				$data['preciolista']=round($datafila[11],2);
				$data['preciocosto']=round($datafila[12],2);
				$data['estado']=1;
				$exito=$producto->grabaProducto($data);

				
					$dataProveedor['idproveedor']=57;
					$dataProveedor['idproducto']=$exito;
					$graba=$pp->grabaProveedorProducto($dataProveedor);
					$contP++;
				
				$cont++;

				

				$dataInventario['idbloque']=44;
				$dataInventario['malos']=$datafila[2];
				$dataInventario['servicio']=$datafila[3];
				$dataInventario['showroom']=$datafila[4];
				$dataInventario['idinventario']=1;
				$dataInventario['idproducto']=$exito;
				$dataInventario['estado']=1;
				$grabaInventario=$detalleInventario->graba($dataInventario);
			}else{
				$idProducto=$dataProducto[0]['idproducto'];
				$filtro="idproducto='$idProducto'";
				$dataBusqueda=$detalleInventario->buscaxfiltro($filtro);

				if (!empty($dataBusqueda)) {
					
					$dataInven['malos']=$datafila[2];
					$dataInven['servicio']=$datafila[3];
					$dataInven['showroom']=$datafila[4];

					$actualizaI=$detalleInventario->actualizaxFiltro($dataInven,$filtro);
					$contI++;
				}else{
					$dti['idbloque']=44;
					$dti['malos']=$datafila[2];
					$dti['servicio']=$datafila[3];
					$dti['showroom']=$datafila[4];
					$dti['idinventario']=1;
					$dti['idproducto']=$idProducto;
					$dti['estado']=1;

					$grabaInventario=$detalleInventario->graba($dti);
					$cont3++;
				}
				
				
				
			}
			
			
			if($exito || $grabaInventario){
				echo "grabo ".$codigo."<br>";
				
			}elseif($actualizaI){
				echo "actualizo ".$codigo."<br>";
			}
			else{
				$ruta2="D:\sistema_erp\bd\prod2.txt";
				//$ruta2="C:\wamp\www\bd\prod2.txt";
				$tt=fopen($ruta2,"ab");
				$cadena=$codigo."\n";
				fwrite($tt,$cadena);
				fclose($tt);
				echo "<b>Error en ".$codigo."</b><br>";
			}

		}
		fclose($archivo);
		echo "producto creados = ".$cont."<br>";
		echo "<b>proveedores enlazados = ".$contP."</b><br>";
		echo "<b>Inventario actualizados = ".$contI."</b><br>";
		echo "<b>Inventario creados = ".($cont+$cont3)."</b><br>";
		exit;

		
			//INSERT INTO `celestium_produccion`.`wc_almacen` (`idalmacen`, `nomalm`, `razsocalm`, `codigoalmacen`, `diralm`, `rucalm`, `estado`, `usuariocreacion`, `usuariomodificacion`, `fechacreacion`, `fechamodificacion`) VALUES ('9', 'sin almacen', 'sin almacen', '', NULL, NULL, '1', '0', '0', '2013-06-28 17:09:56', '2013-06-28 17:09:58');

			//INSERT INTO `celestium_produccion`.`wc_proveedor` (`idproveedor`, `razonsocialp`, `rlegal`, `direccionp`, `descripcionp`, `rucp`, `telefonop`, `telefonop2`, `emailp`, `emailp2`, `webp`, `tipoEmpresa`, `contacto`, `faxp`, `idpais`, `estado`, `usuariocreacion`, `fechacreacion`, `usuariomodificacion`, `fechamodificacion`) VALUES (NULL, 'SIN PROVEEDOR', 'SIN PROVEEDOR', '', '', '', '', '', '', '', '', '1', '', '', '167', '1', '0', '2013-06-28 19:07:11', '0', '2013-06-28 19:07:15');
		
	}
	
	function ponerPrecioInventario(){
		$producto=$this->AutoLoadModel('producto');
		$detalleInventario=$this->AutoLoadModel('detalleinventario');
		$dataInventario=$detalleInventario->listado();
		$cantidad=count($dataInventario);
		$cont=0;
		for ($i=0; $i <$cantidad ; $i++) {
			$idProducto=$dataInventario[$i]['idproducto'];
			$idDetalleInventario=$dataInventario[$i]['iddetalleinventario'];
			$dataProducto=$producto->buscaProducto($idProducto);
			$data['precio']=$dataProducto[0]['preciocosto'];
			$exito=$detalleInventario->actualiza($data,$idDetalleInventario);
			if ($exito) {
				$cont++;
			}
		}
		echo "Se actualizo : ".$cont." de : ".$cantidad;
	
		/*
			ALTER TABLE `wc_detalleinventario`  ADD `precio` DOUBLE NOT NULL AFTER `stockactual`;
		*/
	/*
	}
	function  codificaProductos(){
		$producto=$this->AutoLoadModel('producto');
		$dataProducto=$producto->listadoProductosTotal();
		$cantidad=count($dataProducto);
		$cont=0;
		$cont2=0;
		for ($i=0;$i<$cantidad;$i++){
			$data['nompro']=html_entity_decode($dataProducto[$i]['nompro'],ENT_QUOTES,'UTF-8');
			$data['codigopa']=html_entity_decode($dataProducto[$i]['codigopa'],ENT_QUOTES,'UTF-8');
			$idProducto=$dataProducto[$i]['idproducto'];
			$exito=$producto->actualizaProducto($data,$idProducto);
			if ($exito){
				$cont++;
			}
			else{
				$cont2++;
			}
		}
		echo 'Se actualizaron : '.$cont.' de '.$cantidad;
		echo 'Errores : '.$cont2;
	}
	function  codificaClientes(){
		$cliente=$this->AutoLoadModel('cliente');
		$dataCliente=$cliente->listadoTotalClientes();
		$cantidad=count($dataCliente);
		$cont=0;
		$cont2=0;
		for ($i=0;$i<$cantidad;$i++){
			$data['razonsocial']=html_entity_decode($dataCliente[$i]['razonsocial'],ENT_QUOTES,'UTF-8');
			$data['direccion']=html_entity_decode($dataCliente[$i]['direccion'],ENT_QUOTES,'UTF-8');
			$data['direccion_despacho_cliente']=html_entity_decode($dataCliente[$i]['direccion_despacho_cliente'],ENT_QUOTES,'UTF-8');
			$data['nombrecomercial']=html_entity_decode($dataCliente[$i]['nombrecomercial'],ENT_QUOTES,'UTF-8');
			$data['nombre_contacto']=html_entity_decode($dataCliente[$i]['nombre_contacto'],ENT_QUOTES,'UTF-8');
			$data['nombrecli']=html_entity_decode($dataCliente[$i]['nombrecli'],ENT_QUOTES,'UTF-8');
			$data['apellido1']=html_entity_decode($dataCliente[$i]['apellido1'],ENT_QUOTES,'UTF-8');
			$data['apellido2']=html_entity_decode($dataCliente[$i]['apellido2'],ENT_QUOTES,'UTF-8');
			$idCliente=$dataCliente[$i]['idcliente'];
			$filtro="idcliente='$idCliente'";
			$exito=$cliente->actualizaCliente($data,$filtro);
			if ($exito){
				$cont++;
			}
			else{
				$cont2++;
			}
		}
		echo 'Se actualizaron : '.$cont.' de '.$cantidad;
		echo 'Errores : '.$cont2;
	}
	
	function creaOrdenGasto(){
		$ordenGasto=$this->AutoLoadModel('ordengasto');
		$ordenVenta=$this->AutoLoadModel('ordenventa');
		$ordenCobro=$this->AutoLoadModel('ordencobro');

		$dataOrdenVenta=$ordenVenta->listadoAprobados();
		$cantidadOrdenVenta=count($dataOrdenVenta);
		for ($i=0;$i<$cantidadOrdenVenta;$i++){
			$idOrdenVenta=$dataOrdenVenta[$i]['idordenventa'];
			$dataGasto['idordenventa']=$idOrdenVenta;
			$dataGasto['idtipogasto']=1;
			$dataGasto['importegasto']=$ordenCobro->totalRenovados($idOrdenVenta);
			$grabag=$ordenGasto->graba($dataGasto);
			
			$dataGasto['idtipogasto']=2;
			$dataGasto['importegasto']=$ordenCobro->totalGastoProtesto($idOrdenVenta);
			$grabag=$ordenGasto->graba($dataGasto);
			
			$dataGasto['idtipogasto']=3;
			$dataGasto['importegasto']=$ordenCobro->totalFlete($idOrdenVenta);
			$grabag=$ordenGasto->graba($dataGasto);
			
			$dataGasto['idtipogasto']=4;
			$dataGasto['importegasto']=$ordenCobro->totalGastoBancario($idOrdenVenta);
			$grabag=$ordenGasto->graba($dataGasto);
			
			$dataGasto['idtipogasto']=5;
			$dataGasto['importegasto']=$ordenCobro->totalEnvioSobre($idOrdenVenta);
			$grabag=$ordenGasto->graba($dataGasto);
			
			$dataGasto['idtipogasto']=6;
			$dataGasto['importegasto']=$ordenCobro->totalCostoMantenimiento($idOrdenVenta);
			$grabag=$ordenGasto->graba($dataGasto);
			
			$dataGasto['idtipogasto']=7;
			$dataGasto['importegasto']=$dataOrdenVenta[$i]['importeov']-$dataOrdenVenta[$i]['importeov']/1.18;
			$grabag=$ordenGasto->graba($dataGasto);
			
			$dataGasto['idtipogasto']=8;
			$dataGasto['importegasto']=0.00;
			$grabag=$ordenGasto->graba($dataGasto);
			
			$dataGasto['idtipogasto']=9;
			$dataGasto['importegasto']=$dataOrdenVenta[$i]['importeov']/1.18;
			$grabag=$ordenGasto->graba($dataGasto);
		}
		
	}
	
	function llenaCondicionPago(){
		
		$ordenVenta=$this->AutoLoadModel('ordenventa');
		$ordenCobro=$this->AutoLoadModel('ordencobro');
		
		$dataOrdenVenta=$ordenVenta->listadoAprobados();
		$cantidadOrdenVenta=count($dataOrdenVenta);
		$filtro="";
		$cont=0;
		for ($i=0;$i<$cantidadOrdenVenta;$i++){
			$idOrdenVenta=$dataOrdenVenta[$i]['idordenventa'];
			$filtro="idordenventa='$idOrdenVenta'order by idordencobro limit 0,1 ";
			$dataOrdenCobro=$ordenCobro->buscaxFiltro($filtro);
			$dataov['es_contado']=$dataOrdenCobro[0]['escontado'];
			$dataov['es_credito']=$dataOrdenCobro[0]['escredito'];
			$dataov['es_letras']=$dataOrdenCobro[0]['esletras'];
			$dataov['tipo_letra']=$dataOrdenCobro[0]['tipoletra'];
			
			$exito=$ordenVenta->actualizaOrdenVenta($dataov,$idOrdenVenta);
			if ($exito) {
				$cont++;
			}
		}
		echo $cont.' de '.$cantidadOrdenVenta;
	}
	
	function llenarCarteraClientes(){
		$cliente=$this->AutoLoadModel('cliente');
		$ordenVenta=$this->AutoLoadModel('ordenventa');
		$clienteVendedor=$this->AutoLoadModel('clientevendedor');
		
		$dataCliente=$cliente->listadoxFiltro("estado=1");
		$cantidadClientes=count($dataCliente);
		$cont1=0;
		$cont2=0;
		$cont3=0;
		
		for ($i=0;$i<$cantidadClientes;$i++){
			$idCliente=$dataCliente[$i]['idcliente'];
			
			
			$dataOrden=$ordenVenta->buscarUltimaOrden($idCliente);
			

			
			if (!empty($dataOrden)) {
				$dataClienteVendedor['idvendedor']=$dataOrden['0']['idvendedor'];
				$data['idultimaorden']=$dataOrden[0]['idordenventa'];
			}else{
				$dataClienteVendedor['idvendedor']=172;
				$data['idultimaorden']=0;
			}
			//echo $data['idultimaorden'];
			
			$dataBusqueda=$clienteVendedor->buscarxid($idCliente);
			if (count($dataBusqueda)>0) {
				$exito1=$clienteVendedor->actualizaClienteVendedor($idCliente,$dataClienteVendedor);
				$cont1++;
			}else{
				$dataClienteVendedor['idcliente']=$idCliente;
				$dataClienteVendedor['estado']=1;
				$exito1=$clienteVendedor->grabaClienteVendedor($dataClienteVendedor);
				$cont2++;
			}
			$exitoc=$cliente->actualizaCliente($data,"idcliente='$idCliente'");
			if ($exitoc) {
				$cont3++;
			}	
			
			
		}
		
		echo 'vendedores actualizados : '.$cont1;
		echo 'vendedores grabados : '.$cont2;
		echo 'clientes Actualizados : '.$cont3." de : ".$cantidadClientes;
		
	}
	*/
          function corrigeImportePagado(){
            $ordenVenta=$this->AutoLoadModel('ordenventa');
            $ingresos=$this->AutoLoadModel('ingresos');
            $filtro="vbcreditos=1";
            $dataOrden=$ordenVenta->buscarOrdenxParametro($filtro);
            $cantidadData=count($dataOrden);
            for($i=0;$i<$cantidadData;$i++){
                $idOrdenVenta=$dataOrden[$i]['idordenventa'];
                $dataIngreso=$ingresos->sumaIngresos($idOrdenVenta);
                $importePagado=$dataIngreso[0]['sum(montoasignado)'];
                $data['importepagado']=empty($importePagado)?0:round($importePagado,2);
                $exito=$ordenVenta->actualizaOrdenVenta($data,$idOrdenVenta);
            }
        }
	function prueba(){
		$dato="ñ  '";
		echo htmlentities($dato,ENT_QUOTES,'UTF-8');
		echo '<br>';
		echo htmlspecialchars($dato,ENT_QUOTES);
		exit;
	}

	function corrigeMovimiento(){
		$detalleMovimiento=$this->AutoLoadModel('detallemovimiento');
		$producto=$this->AutoLoadModel('producto');
		$filtro="dm.pu=0 or dm.preciovalorizado=0";
		$dataDetalle=$detalleMovimiento->buscaDetalleMovimientoxFiltro($filtro);
		$cantidad=count($dataDetalle);
		for ($i=0; $i <$cantidad ; $i++) { 
			$idProducto=$dataDetalle[$i]['idproducto'];
			$idDetalleMovimiento=$dataDetalle[$i]['iddetallemovimiento'];
			$dataProducto=$producto->buscaProducto($idProducto);
			$data['pu']=$dataProducto[0]['preciocosto'];
			$data['preciovalorizado']=$dataProducto[0]['preciocosto'];
			$exito=$detalleMovimiento->actualizaDetalleMovimientoxid($idDetalleMovimiento,$data);
		}

		//update  wc_detallemovimiento set preciovalorizado=pu where preciovalorizado=0;
	}	
}
?>
