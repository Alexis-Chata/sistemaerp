<?php  

Class OrdenCobroController extends ApplicationGeneral{

	function listado(){

	}

	function buscarxOrdenVenta(){
		$idOrdenVenta=$_REQUEST['id'];
		$cliente=new Cliente();
		$data=$cliente->buscaxOrdenVenta($idOrdenVenta);

			$dataRespuesta['razonsocial']=!empty($data[0]['razonsocial'])?(html_entity_decode($data[0]['razonsocial'],ENT_QUOTES,'UTF-8')):"";
			$dataRespuesta['idcliente']=!empty($data[0]['idcliente'])?$data[0]['idcliente']:"";
			$dataRespuesta['codcliente']=!empty($data[0]['codcliente'])?$data[0]['codcliente']:"";
			$dataRespuesta['codantiguo']=!empty($data[0]['codantiguo'])?$data[0]['codantiguo']:"";
			$dataRespuesta['codigov']=!empty($data[0]['codigov'])?$data[0]['codigov']:"";
			$dataRespuesta['ruc']=!empty($data[0]['ruc'])?$data[0]['ruc']:"";
			echo json_encode($dataRespuesta);
	}

    function buscarDetalleOrdenCobro() {
        $idOrdenVenta = $_REQUEST['id'];
        $ordencobro = New OrdenCobro();
        $detalleOrdenCobro = New DetalleOrdenCobro();
        $actor = new Actor();
        $tipoGasto = $this->AutoLoadModel('tipogasto');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $ordenVenta = $this->AutoLoadModel('ordenventa');        
        $dataOrdenVenta = $ordenVenta->buscarOrdenVentaxId($idOrdenVenta);
        $dataOrdenCobro = $ordencobro->listarxguia($idOrdenVenta);
        
        echo "<table>";
        echo "<thead>";
            echo "<tr>";
                echo "<th colspan='9' style='background:#B4D1F7;'>".$dataOrdenVenta[0]['codigov']."</th>";
            echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        if($dataOrdenVenta[0]['desaprobado'] == 1){
            echo "<tr>";
            echo "<td colspan='9' style='text-align:center;'><h2>DESAPROBADO</h2></td>";
            echo "</tr>";
            echo "</tbody>";
            echo "</table>";
        } else {

            $cantidadOrdenCobro = count($dataOrdenCobro);

            $TipoCambio = $this->AutoLoadModel("TipoCambio");
            $dataTipoCambio = $TipoCambio->consultaDatosTCVigentexTCElegido($dataOrdenVenta[0]['IdTipoCambioVigente']);
            $simboloMoneda = $dataTipoCambio[0]['simbolo'];

            $montoProgramacionPendiente = $ordencobro->deudatotal($idOrdenVenta);
            $montoGuia = $ordenGasto->totalGuia($idOrdenVenta);
            $montoPagado = $dataOrdenVenta[0]['importepagado'];
            $montoDeuda = $montoGuia - $montoPagado;

            $Percepcion = $ordenGasto->importeGasto($idOrdenVenta, 6);
            $fechavencimiento = $dataOrdenVenta[0]['fechavencimiento'];

                    echo "<tr>";
                        echo "<th style='color:black;background:#C6DCF9;'>Total Guia:</th>";
                        echo "<td style='color:blue'>" . $simboloMoneda . " " . number_format($montoGuia, 2) . "</td>";
                        echo "<th style='color:black;background:#C6DCF9;'>Total Pendiente:</th>";
                        echo "<td style='color:blue'>" . $simboloMoneda . " " . number_format($montoDeuda, 2) . "</td>";
                        echo "<th style='color:black;background:#C6DCF9;'>Total Pagado:</th>";
                        echo "<td style='color:blue'>" . $simboloMoneda . " " . number_format($montoPagado, 2) . "</td>";
                        echo "<th style='color:black;background:#C6DCF9;'>Percepcion:</th>";
                        echo "<td colspan='2' style='color:blue'>" . $simboloMoneda . "  " . number_format($Percepcion, 2) . "<input type='hidden' id='fechavencimiento' value='" . $fechavencimiento . "'></td>";
                    echo "</tr>";
                echo "</tbody>";
            echo "</table>";
            
            echo "<hr><hr><br>";
            
            for ($y = 0; $y < $cantidadOrdenCobro; $y++) {
                echo "<table>";
                echo "<thead>";
                    echo "<tr>";
                        echo "<th colspan='10' style='background:#B4D1F7;'>DETALLE DE LA PROGRAMACION DE PAGOS</th>";
                    echo "</tr>";
                echo "</thead>";
                $dataDetalleOrdenCobro = $detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$y]['idordencobro']);
                $tamanio = count($dataDetalleOrdenCobro);
                echo "<tr>";
                    echo "<th style='background:#C6DCF9;'>Nro:</th>";
                    echo "<th style='background:#C6DCF9;'>Monto:</th>";
                    echo "<th style='background:#C6DCF9;'>Saldo:</th>";
                    echo "<th style='background:#C6DCF9;'>Condicion:</th>";
                    echo "<th style='background:#C6DCF9;'>Nro letra:</th>";
                    echo "<th style='background:#C6DCF9;'>Fecha Vencimiento:</th>";
                    echo "<th style='background:#C6DCF9;'>Situación:</th>";
                    echo "<th style='background:#C6DCF9;'>Ref</th>";
                    echo "<th style='background:#C6DCF9;'>Tipo Gasto:</th>";
                    echo "<th style='background:#C6DCF9;'>Usuario Mod:</th>";
                echo "</tr>";
                for ($i = 0; $i < $tamanio; $i++) {
                    $dataActor = $actor->buscarxid($dataDetalleOrdenCobro[$i]['usuariomodificacion']);

                    echo "<tr>";
                    echo "<td style='text-align:center;'>" . ($i + 1) . "</td>";
                    echo "<td>" . $simboloMoneda . " " . number_format($dataDetalleOrdenCobro[$i]['importedoc'], 2) . "</td>";
                    echo "<td>" . $simboloMoneda . " " . number_format($dataDetalleOrdenCobro[$i]['saldodoc'], 2) . "</td>";
                    
                    switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
                        case '1': $formacobro = "Contado";
                            break;
                        case '2': $formacobro = "Crédito";
                            break;
                        case '3': $formacobro = "Letras";
                            if ($dataDetalleOrdenCobro[$i]['recepcionLetras'] == 'PA') {
                                //if ($dataDetalleOrdenCobro[$i]['numerounico'] == 'CPA' || $dataDetalleOrdenCobro[$i]['numerounico'] == 'R-BBVA' || $dataDetalleOrdenCobro[$i]['numerounico'] == 'R-BCP' || $dataDetalleOrdenCobro[$i]['numerounico'] == 'R-CL') {
                                    $formacobro .= ' ' . $dataDetalleOrdenCobro[$i]['numerounico'];
                                //}
                            }
                    }

                    echo "<td>" . $formacobro . "</td>";
                    echo "<td>" . $dataDetalleOrdenCobro[$i]['numeroletra'] . "</td>";
                    echo "<td>" . $dataDetalleOrdenCobro[$i]['fvencimiento'] . "</td>";
                    echo "<td ><label>" . (($dataDetalleOrdenCobro[$i]['situacion'] == '') ? 'pendiente ref (' . $dataDetalleOrdenCobro[$i]['referencia'] . ')' : $dataDetalleOrdenCobro[$i]['situacion'] . ' ref (' . $dataDetalleOrdenCobro[$i]['referencia'] . ')') . "</label></td>";
                    echo "<td>" . $dataDetalleOrdenCobro[$i]['recepcionLetras'] . "</td>";
                    echo "<td>" . $tipoGasto->nombreGasto($dataDetalleOrdenCobro[$i]['tipogasto']) . "</td>";
                    echo "<td>" . ($dataActor[0]['nombres'] . ' ' . $dataActor[0]['apellidopaterno'] . ' ' . $dataActor[0]['apellidomaterno']) . "</td>";
                    echo "</tr>";
                }
                echo "<tr>";
                    echo "<th style='background:#B4D1F7;'>Monto Total:</th>";
                    echo "<td style='color:blue;'><b>" . $simboloMoneda . " " . number_format($dataOrdenCobro[$y]['importeordencobro'], 2) . "</b></td>";
                    echo "<th style='background:#B4D1F7;'>Saldo Total:</th>";
                    echo "<td style='color:blue;'><b>" . $simboloMoneda . " " . number_format($dataOrdenCobro[$y]['saldoordencobro'], 2) . "</b></td>";
                    echo "<th style='background:#B4D1F7;'>Fecha emisión</th>";
                    echo "<td style='color:blue;'><b>" . $dataOrdenCobro[$y]['femision'] . "</b></td>";
                    echo "<th style='background:#B4D1F7;'>Situación</th>";
                    echo "<td style='color:blue;' colspan='3'><b>" . $dataOrdenCobro[$y]['situacion'] . "</b></td>";
                echo "</tr>";
                echo "</table>";
            }
            echo "<hr><hr>";
        }
    }

    function detalleOrdenCobroVistaGlobal()
	{
		$idOrdenVenta=$_REQUEST['id'];
		$ordencobro=New OrdenCobro();
		$dataOrdenCobro=$ordencobro->listarxguia($idOrdenVenta);
		$detalleOrdenCobro=New DetalleOrdenCobro();
		$cantidadOrdenCobro=count($dataOrdenCobro);
		for ($n=0; $n <$cantidadOrdenCobro ; $n++) { 
			$dataDetalleOrdenCobro=$detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$n]['idordencobro']);
			$tamanio=count($dataDetalleOrdenCobro);
			echo "	<tr>
						<td colspan=8><h3>Condiciones financieras:</h3></td>
					</tr>";

			echo "<tr>";
			echo "<th>Nro:</th>";
			echo "<th>Monto:</th>";
			echo "<th>Condicion:</th>";
			echo "<th>Nro letra:</th>";
			echo "<th>Fecha Giro:</th>";
			echo "<th>Fecha Vencimiento:</th>";
			echo "<th>Situación:</th>";
			echo "<th>R. de letra :</th>";
			echo "</tr>";		
			for ($i=0; $i < $tamanio; $i++) { 
				echo "<tr>";
				echo "<td>".($i+1)."</td>";
				echo "<td>".'S/.'.number_format($dataDetalleOrdenCobro[$i]['importedoc'],2)."</td>";
				switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
					case '1': $formacobro="Contado"; break;
					case '2': $formacobro="Crédito"; break;
					case '3': $formacobro="Letras"; break;								
				}
				echo "<td>".$formacobro."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['numeroletra']."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['fechagiro']."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['fvencimiento']."</td>";
				$situacion=empty($dataDetalleOrdenCobro[$i]['situacion'])?"Pendiente":$dataDetalleOrdenCobro[$i]['situacion'];
				echo "<td>".$situacion."<input type='hidden' value=".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']." class='iddetalleordencobro'></td>";
				if ($dataDetalleOrdenCobro[$i]['formacobro']==3) {
					echo "<td>".$dataDetalleOrdenCobro[$i]['recepcionLetras']."</td>";
				}else{
					echo "<td>".$dataDetalleOrdenCobro[$i]['recepcionLetras']."</td>";
				}
				echo "</tr>";
			}
			echo "<tr>";
			echo "<th>Monto Total:</th>";
			echo "<td><b>".'S/.'.$dataOrdenCobro[$n]['importeordencobro']."</b></td>";
			echo "<th colspan=6>&nbsp</th>";
			echo "</tr>";
		}
	}

	function cargargasto($iddetalleordencobro){
		$ordencobro=New OrdenCobro();
		$DetalleOrdencobro=New DetalleOrdencobro();

		$idtipogasto=4;
		$importegasto=$_REQUEST['importegasto'];
		$iddetalleordencobro=$_REQUEST['iddetalleordencobro']?$_REQUEST['iddetalleordencobro']:$_REQUEST['id'];

		$DetalleOrdenCobro=$DetalleOrdencobro->buscaDetalleOrdencobro($iddetalleordencobro);

		$dataCargarGastoDetalleOrdenCobro['idordencobro']=$DetalleOrdenCobro[0]['idordencobro'];
		$dataCargarGastoDetalleOrdenCobro['importedoc']=$importegasto;
		$dataCargarGastoDetalleOrdenCobro['saldodoc']=$importegasto;
		$dataCargarGastoDetalleOrdenCobro['formacobro']=2;
		$dataCargarGastoDetalleOrdenCobro['fechagiro']=$DetalleOrdenCobro[0]['fechagiro'];
		$dataCargarGastoDetalleOrdenCobro['fvencimiento']=$DetalleOrdenCobro[0]['fvencimiento'];
		$dataCargarGastoDetalleOrdenCobro['referencia']='GC'.$DetalleOrdenCobro[0]['numeroletra'];
		$dataCargarGastoDetalleOrdenCobro['tipopago']=$idtipogasto;
		//var_dump($dataCargarGastoDetalleOrdenCobro);
		if(!empty($importegasto)){
			$data0=$DetalleOrdencobro->grabaDetalleOrdenVentaCobro($dataCargarGastoDetalleOrdenCobro);
			if($data0){
				$bOrdenCobro=$ordencobro->buscaOrdencobro($DetalleOrdenCobro[0]['idordencobro']);

				$dataUpdateOrdenCobro['importeordencobro']=$bOrdenCobro[0]['importeordencobro']+$importegasto;
				$dataUpdateOrdenCobro['saldoordencobro']=$bOrdenCobro[0]['saldoordencobro']+$importegasto;
				$data1=$ordencobro->actualizaOrdencobro($dataUpdateOrdenCobro,$DetalleOrdenCobro[0]['idordencobro']);
				if($data1){
					$bOrdenGasto=$ordencobro->buscaOrdengastoxidovxtipogasto($bOrdenCobro[0]['idordenventa'],$idtipogasto);
					if($bOrdenGasto==null){
						$data['idordenventa']=$bOrdenCobro[0]['idordenventa'];
						$data['importegasto']=$importegasto;
						$data['idtipogasto']=$idtipogasto;
						$data2=$ordencobro->cargargasto($data);
						echo json_encode($data2);
					}else{
						$dataUpdateOrdenGasto['importegasto']=floatval($bOrdenGasto[0]['importegasto'])+$importegasto;
						$data2=$ordencobro->actualizaOrdengasto($dataUpdateOrdenGasto,$bOrdenGasto[0]['idordenventa'],$idtipogasto);
						echo json_encode($data2);
					}
				}
			}
			//echo json_encode($data1);
		}else{echo ('cargargasto es un metodo _POST');}
		
		//echo json_encode($data0);
	}

	function anulargasto($iddetalleordencobro){
		$ordencobro=New OrdenCobro();
		$DetalleOrdencobro=New DetalleOrdencobro();

		$idtipogasto=4;
		$iddetalleordencobro=$_REQUEST['iddetalleordencobro']?$_REQUEST['iddetalleordencobro']:$_REQUEST['id'];
		
		$DetalleOrdenCobro=$DetalleOrdencobro->buscaDetalleOrdencobro($iddetalleordencobro);
		$importedoc=$DetalleOrdenCobro[0]['importedoc'];
		$idordencobro=$DetalleOrdenCobro[0]['idordencobro'];

		$dataAnularGastoDetalleOrdenCobro['saldodoc']=0;
		$dataAnularGastoDetalleOrdenCobro['situacion']='anulado';

		$bOrdenCobro=$ordencobro->buscaOrdencobro($idordencobro);
		$idordenventa=$bOrdenCobro[0]['idordenventa'];
		$dataUpdateOrdenCobro['importeordencobro']=$bOrdenCobro[0]['importeordencobro']-$importedoc;
		$dataUpdateOrdenCobro['saldoordencobro']=$bOrdenCobro[0]['saldoordencobro']-$importedoc;

		$bOrdenGasto=$ordencobro->buscaOrdengastoxidovxtipogasto($bOrdenCobro[0]['idordenventa'],$idtipogasto);

		if($_POST['iddetalleordencobro']){
			$data0=$DetalleOrdencobro->actualizar_cargado($dataAnularGastoDetalleOrdenCobro,$iddetalleordencobro);
			$data1=$ordencobro->actualizaOrdencobro($dataUpdateOrdenCobro,$idordencobro);
	
			if($bOrdenGasto[0]['importegasto']=$importedoc){
				$data2=$ordencobro->eliminarOrdengasto($bOrdenGasto[0]['idordengasto']);
				echo json_encode($data2);
			}else{
				$dataUpdateOrdenGasto['importeordencobro']=$bOrdenGasto[0]['importegasto']-$importedoc;
				$data2=$ordencobro->actualizaOrdengasto($dataUpdateOrdenGasto,$idordenventa,$idtipogasto);
				echo json_encode($data2);
			}
		}else{echo ('cargargasto es un metodo _POST');}

		//echo json_encode($bOrdenGasto);
	}

    function buscarDetalleOrdenCobro2()
	{
		$idOrdenVenta=$_REQUEST['id'];
		$ordencobro=New OrdenCobro();
		$tipoGasto=$this->AutoLoadModel('tipogasto');
		$ordenGasto=$this->AutoLoadModel('ordengasto');
		$ordenVenta=$this->AutoLoadModel('ordenventa');

		$dataOrdenCobro=$ordencobro->listarxguia($idOrdenVenta);
		$dataOrdenVenta=$ordenVenta->buscarOrdenVentaxId($idOrdenVenta);
		$cantidadOrdenCobro=count($dataOrdenCobro);
		$simbolo=$dataOrdenVenta[0]['Simbolo'];
		$montoProgramacionPendiente=$ordencobro->deudatotal($idOrdenVenta);
		$montoGuia=$ordenGasto->totalGuia($idOrdenVenta);
		$montoPagado=$dataOrdenVenta[0]['importepagado'];
		$montoDeuda=$montoGuia-$montoPagado;

		$Percepcion=$ordenGasto->importeGasto($idOrdenVenta,6);
		$fechavencimiento=$dataOrdenVenta[0]['fechavencimiento'];
			echo "<tr>";
			echo "<th style='color:black;background:#4096EE;'>Total <br>Guia:</th>";
			echo "<td style='color:blue'> ".$simbolo." ".number_format($montoGuia,2)."</td>";
			echo "<th style='color:black;background:#4096EE;'>Total <br>Pendiente:</th>";
			echo "<td style='color:blue'> ".$simbolo." ".number_format($montoDeuda,2)."</td>";
			echo "<th style='color:black;background:#4096EE;'>Total <br>Pagado:</th>";
			echo "<td style='color:blue'> ".$simbolo." ".number_format($montoPagado,2)."</td>";
			echo "<th style='color:black;background:#4096EE;'>Percepcion:</th>";
			echo "<td style='color:blue'> ".$simbolo." ".number_format($Percepcion,2)."<input type='hidden' id='fechavencimiento' value='".$fechavencimiento."'></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th style='background:white;'>&nbsp</th>";
			echo "</tr>";
		for ($y=0; $y <$cantidadOrdenCobro ; $y++) {

			$detalleOrdenCobro=New DetalleOrdenCobro();
			$dataDetalleOrdenCobro=$detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$y]['idordencobro']);
			$dataBusqueda=$detalleOrdenCobro->listadoxidOrdenCobroxrenovado($dataOrdenCobro[$y]['idordencobro']);
			$cantidad=count($dataBusqueda);
			//echo '<tr><td>la cantidad es '.$cantidad."</td></tr>";
			$tamanio=count($dataDetalleOrdenCobro);
			echo "<tr>";
			echo "<th style='color:black;background:#4096EE;'>Monto <br>Total:</th>";
			echo "<th style='color:black;background:#4096EE;'>Saldo:</th>";
			echo "<th style='color:black;background:#4096EE;'>Fecha <br>emisión</th>";
			echo "<th style='color:black;background:#4096EE;'>Situación</th>";
			echo "<th colspan=9 >&nbsp</th>";
			echo "</tr>";
			echo "<tr>";
			echo "<td style='color:blue;'><b > ".$simbolo." ".number_format($dataOrdenCobro[$y]['importeordencobro'],2)."</b></td>";
			echo "<td style='color:blue;'><b > ".$simbolo." ".number_format($dataOrdenCobro[$y]['saldoordencobro'],2)."</b ></td>";
			echo "<td style='color:blue;'><b >".$dataOrdenCobro[$y]['femision']."</b ></td>";
			echo "<td style='color:blue;'><b >".$dataOrdenCobro[$y]['situacion']."</b ></td>";
			echo "<td colspan=9 >&nbsp</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th>Nro:</th>";
			echo "<th>Padre:</th>";
			echo "<th>Monto:  ".$simbolo." </th>";
			echo "<th>Condicion:</th>";
			echo "<th>Nro letra:</th>";
			echo "<th>Nro Unico:</th>";
			echo "<th>Recepcion:</th>";
			echo "<th>Fecha Vencimiento:</th>";
			echo "<th>Situación:</th>";
			echo "<th>Tipo Gasto:</th>";

			echo "<th colspan=4>Acciones:</th>";
			echo "</tr>";
			for ($i=0; $i < $tamanio; $i++) {
				$fechavencimiento=$dataDetalleOrdenCobro[$i]['fvencimiento'];
				if (!empty($fechavencimiento)) {
					$diasPago=120;
					$fechaPago=strtotime("$fechavencimiento + " . $diasPago . " day");
					$diasProtesto=7;
					$fechaProtesto=strtotime("$fechavencimiento + " . $diasProtesto . " day");
					$hoy=strtotime(date('Y-m-d'));
				}
				echo "<tr>";
				echo "<td>".($dataDetalleOrdenCobro[$i]['iddetalleordencobro'])."</td>";
				echo "<td>".(!empty($dataDetalleOrdenCobro[$i]['idpadre'])?$dataDetalleOrdenCobro[$i]['idpadre']:'')."</td>";
				echo "<td><label class='importe'>".number_format($dataDetalleOrdenCobro[$i]['importedoc'],2)."</label></td>";
				switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
					case '1': $formacobro="Contado"; break;
					case '2': $formacobro="Crédito"; break;
					case '3': $formacobro="Letras"; break;
				}
				echo "<td>".$formacobro."</td>";
				echo "<td><label class='lblletra'>".$dataDetalleOrdenCobro[$i]['numeroletra']."</td>";
				echo "<td><label class='lblunico'>".$dataDetalleOrdenCobro[$i]['numerounico']."</td>";
				echo "<td><label class='lblrecepcion'>".$dataDetalleOrdenCobro[$i]['recepcionLetras']."</td>";
				echo "<td><label class='fechavencimiento'>".$dataDetalleOrdenCobro[$i]['fvencimiento']."</label></td>";



				echo "<td class='situacion'><label>".(($dataDetalleOrdenCobro[$i]['situacion']=='')?'pendiente ref ('.$dataDetalleOrdenCobro[$i]['referencia'].')':$dataDetalleOrdenCobro[$i]['situacion'].' ref ('.$dataDetalleOrdenCobro[$i]['referencia'].')')."</label></td>";
				echo "<td >".$tipoGasto->nombreGasto($dataDetalleOrdenCobro[$i]['tipogasto'])."</td>";

				if (strcmp($dataDetalleOrdenCobro[$i]['situacion'],"refinanciado")!=0 && strcmp($dataDetalleOrdenCobro[$i]['situacion'],"cancelado")!=0 && strcmp($dataDetalleOrdenCobro[$i]['situacion'],"reprogramado")!=0 && strcmp($dataDetalleOrdenCobro[$i]['situacion'],"renovado")!=0 && strcmp($dataDetalleOrdenCobro[$i]['situacion'],"protestado")!=0 && strcmp($dataDetalleOrdenCobro[$i]['situacion'],"anulado")!=0 && strcmp($dataDetalleOrdenCobro[$i]['situacion'],"extornado")!=0 ) {


					if ($dataDetalleOrdenCobro[$i]['formacobro']!='2' && $dataDetalleOrdenCobro[$i]['formacobro']!='1' && $dataOrdenCobro[$y]['tipoletra']!=2) {

						/*if ($dataDetalleOrdenCobro[$i]['importedoc']<200) {
							echo "<td colspan='2'></td>";
						}else{
							echo "<td colspan='2'><button class=renovar>Renovar</button></td>";
						}*/

						if ($cantidad==2 ) {
							if ($hoy<=$fechaPago) {
								echo "<td><span class='c1_datashet'><button class='pagar'>Pagar</button></span></td>";
								echo "<td ><span class='c3_datashet'><button class='renovar'>Renovar</button></span></td>";

							}else{
								echo "<td></td>";
								echo "<td></td>";
							}
							//if ($hoy>=$fechaProtesto) {
								echo "<td><span class='c4_datashet'><button class='protestar'>Protestar</button></span></td>";
							//}else{
							//	echo "<td></td>";
							//}


							echo "<td><span class='c5_datashet'><button class='extornar'>Extornar</button></span></td>";

						}else{
                                                    $fechapandemia = '2020-03-07';
							if ($hoy<=$fechaPago || $fechapandemia <= $dataDetalleOrdenCobro[$i]['fvencimiento']) {
								echo "<td><span class='c1_datashet'><button class='pagar'>Pagar</button></span></td>";
								echo "<td ><span class='c3_datashet'><button class='renovar'>Renovar</button></span></td>";
								//echo "<td><button class=protestar>Protestar</button></td>";
							}else{
								echo "<td></td>";
								echo "<td ></td>";
							}

							//if ($hoy>=$fechaProtesto) {
								echo "<td><span class='c4_datashet'><button class='protestar'>Protestar</button></span></td>";
							//}else{
							//	echo "<td></td>";
							//}


							echo "<td></td>";

						}

					}else{
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";

					}


					echo "<input class='iddetalle' type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."'>";
					echo "<input class='formacobro' type='hidden' value='".$dataDetalleOrdenCobro[$i]['formacobro']."'>";
					echo "<input class='iddetalleordencobro' type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."'>";
					echo "<input class='valorLetra' type='hidden' value='".$dataDetalleOrdenCobro[$i]['importedoc']."'>";

					echo "</tr>";
				}else{
					if (strcmp($dataDetalleOrdenCobro[$i]['situacion'],"cancelado")==0 && $dataDetalleOrdenCobro[$i]['formacobro']==3) {
						echo "<td><span class='c2_datashet'><button class='deshacerPago'>Des.Pago</button></span></td>";

					}else{
						echo "<td></td>";

					}


					echo "<td></td>";
					echo "<td></td>";
					echo "<td>";
					echo "<input class='iddetalle' type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."'>";
					echo "<input class='formacobro' type='hidden' value='".$dataDetalleOrdenCobro[$i]['formacobro']."'>";
					echo "<input class='iddetalleordencobro' type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."'>";
					echo "<input class='valorLetra' type='hidden' value='".$dataDetalleOrdenCobro[$i]['importedoc']."'>";

					echo "</td>";
					echo "</tr>";
				}

			}
			echo "<tr ><td style='background:silver;' colspan='13'>&nbsp</td></tr>";
		}
	}
	
	function buscaDetalleOrdencobroReprogramar()
	{
		$idOrdenVenta=$_REQUEST['id'];
		$ordencobro=New OrdenCobro();
		$tipoGasto=$this->AutoLoadModel('tipogasto');
		$ordenGasto=$this->AutoLoadModel('ordengasto');
		$ordenVenta=$this->AutoLoadModel('ordenventa');
		
		$dataOrdenCobro=$ordencobro->listarxguia($idOrdenVenta);
		$dataOrdenVenta=$ordenVenta->buscarOrdenVentaxId($idOrdenVenta);
		$cantidadOrdenCobro=count($dataOrdenCobro);
		
		$montoProgramacionPendiente=$ordencobro->deudatotal($idOrdenVenta);
		$montoGuia=$ordenGasto->totalGuia($idOrdenVenta);
		$montoPagado=$dataOrdenVenta[0]['importepagado'];
		$montoDeuda=$montoGuia-$montoPagado;
		$simboloMoneda=$dataOrdenVenta[0]['Simbolo'];
		$Percepcion=$ordenGasto->importeGasto($idOrdenVenta,6);
		$fechavencimiento=$dataOrdenVenta[0]['fechavencimiento'];
		
			echo "<tr>";
			echo "<th style='color:black;background:#4096EE;'>Total <br>Guia:</th>";
			echo "<td style='color:blue'>".$simboloMoneda." ".number_format($montoGuia,2)."</td>";
			echo "<th style='color:black;background:#4096EE;'>Total <br>Pendiente:</th>";
			echo "<td style='color:blue'>".$simboloMoneda." ".number_format($montoDeuda,2)."</td>";
			echo "<th style='color:black;background:#4096EE;'>Total <br>Pagado:</th>";
			echo "<td style='color:blue'>".$simboloMoneda." ".number_format($montoPagado,2)."</td>";
			echo "<th style='color:black;background:#4096EE;'>Percepcion:</th>";
			echo "<td style='color:blue'>".$simboloMoneda." ".number_format($Percepcion,2)."<input type='hidden' id='fechavencimiento' value='".$fechavencimiento."'></td>";
			echo "<th style='color:black;background:#4096EE;'>Reprogramar <br>Toda la Deuda</th>";
			echo "<td style='color:blue' colspan=2><button class='reprogramacionTotal'>Reprogramacion Total</button><input type='hidden' value='".round($montoDeuda,2)."' id='totalImporteDeuda'></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th style='background:white;'>&nbsp</th>";
			echo "</tr>";
		for ($y=0; $y <$cantidadOrdenCobro ; $y++) { 
			
			$detalleOrdenCobro=New DetalleOrdenCobro();
			$dataDetalleOrdenCobro=$detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$y]['idordencobro']);
			foreach ($dataDetalleOrdenCobro as $key) {
				if($key['situacion']!='anulado'){
					$referencias[]=$key['referencia'];
				}
			}
			$dataBusqueda=$detalleOrdenCobro->listadoxidOrdenCobroxrenovado($dataOrdenCobro[$y]['idordencobro']);
			$cantidad=count($dataBusqueda);
			//echo '<tr><td>la cantidad es '.$cantidad."</td></tr>";
			$tamanio=count($dataDetalleOrdenCobro);
			echo "<tr>";
			echo "<th style='color:black;background:#4096EE;'>Monto <br>Total:</th>";
			echo "<th style='color:black;background:#4096EE;'>Saldo:</th>";
			echo "<th style='color:black;background:#4096EE;'>Fecha <br>emisión</th>";
			echo "<th style='color:black;background:#4096EE;'>Situación</th>";
                        
                        echo "<th colspan=10>&nbsp</th>";
			echo "</tr>";
			echo "<tr>";
			echo "<td style='color:blue;'><b >".$simboloMoneda." ".number_format($dataOrdenCobro[$y]['importeordencobro'],2)."</b></td>";
			echo "<td style='color:blue;'><b >".$simboloMoneda." ".number_format($dataOrdenCobro[$y]['saldoordencobro'],2)."</b ></td>";
			echo "<td style='color:blue;'><b >".$dataOrdenCobro[$y]['femision']."</b ></td>";
			echo "<td style='color:blue;'><b >".$dataOrdenCobro[$y]['situacion']."</b ></td>";
			echo "<td colspan=10 >&nbsp</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th>Nro:</th>";
			echo "<th>Padre:</th>";
			echo "<th>Monto:".$simboloMoneda." </th>";
			echo "<th>Saldo: ".$simboloMoneda." </th>";
			echo "<th>Condicion:</th>";
			echo "<th>Nro letra:</th>";
			echo "<th>Nro Unico:</th>";
			echo "<th>Recoge Letra:</th>";
			echo "<th>Fecha Vencimiento:</th>";
			echo "<th>Situación:</th>";
			echo "<th>Tipo Gasto:</th>";
			
			echo "<th colspan=3>Acciones:</th>";
			echo "</tr>";		
			for ($i=0; $i < $tamanio; $i++) { 
				$fechavencimiento=$dataDetalleOrdenCobro[$i]['fvencimiento'];
				if (!empty($fechavencimiento)) {
					$diasPago=9;
					$fechaPago=strtotime("$fechavencimiento + " . $diasPago . " day");
					$diasProtesto=10;
					$fechaProtesto=strtotime("$fechavencimiento + " . $diasProtesto . " day");
					$hoy=strtotime(date('Y-m-d'));
				}
				echo "<tr>";
				echo "<td>".($dataDetalleOrdenCobro[$i]['iddetalleordencobro'])."</td>";
				echo "<td>".(!empty($dataDetalleOrdenCobro[$i]['idpadre'])?$dataDetalleOrdenCobro[$i]['idpadre']:'')."</td>";
				echo "<td><label class='importe'>".number_format($dataDetalleOrdenCobro[$i]['importedoc'],2)."</label></td>";
				echo "<td><label >".number_format($dataDetalleOrdenCobro[$i]['saldodoc'],2)."</label></td>";
				switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
					case '1': $formacobro="Contado"; break;
					case '2': $formacobro="Crédito"; break;
					case '3': $formacobro="Letras"; break;								
				}
				echo "<td>".$formacobro."</td>";
				echo "<td><label class='lblletra'>".$dataDetalleOrdenCobro[$i]['numeroletra']."</td>";
				echo "<td><label class='lblletra'>".$dataDetalleOrdenCobro[$i]['numerounico']."</td>";
				echo "<td><label class='lblletra'>".$dataDetalleOrdenCobro[$i]['recepcionLetras']."</td>";
				echo "<td><label class='fechavencimiento'>".$dataDetalleOrdenCobro[$i]['fvencimiento']."</label></td>";
				
				echo "<td class='situacion'><label>".(($dataDetalleOrdenCobro[$i]['situacion']=='')?'pendiente ref ('.$dataDetalleOrdenCobro[$i]['referencia'].')':$dataDetalleOrdenCobro[$i]['situacion'].' ref ('.$dataDetalleOrdenCobro[$i]['referencia'].')')."</label></td>";
				echo "<td >".$tipoGasto->nombreGasto($dataDetalleOrdenCobro[$i]['tipogasto']). "</td>";

				if (strcmp($dataDetalleOrdenCobro[$i]['situacion'],"")==0 && $dataDetalleOrdenCobro[$i]['renovado']==0 ) {
					if($formacobro=='Crédito' && strpos($dataDetalleOrdenCobro[$i]['referencia'], 'GC')!==false) {
						echo "<td>&nbsp;</td>";
					}else{
						if($dataDetalleOrdenCobro[$i]['saldodoc']==$dataDetalleOrdenCobro[$i]['importedoc']){
							echo "<td><button class=anular Pago>Anular</button></td>";
						}else{
							echo "<td>&nbsp;</td>";
						}
					}
					
					echo "<td><button class=modificar Pago>Reprogramar</button></td>";		
						
					
					if ($formacobro=='Letras' && $dataDetalleOrdenCobro[$i]['situacion']=='') {
						if(in_array('GC'.$dataDetalleOrdenCobro[$i]['numeroletra'], $referencias)){
							echo "<td class='bold' style='vertical-align: middle;'>GASTO CARGADO</td>";
						}else{
							echo "<td><span class='c1_datashet'><button class='cargargasto' style='width: 100px;'>Cargar Gasto</button></span></td>";
						}
					}else if($formacobro=='Crédito' && strpos($dataDetalleOrdenCobro[$i]['referencia'], 'GC')!==false) {
						echo "<td><span class='c1_datashet'><button class='anulargasto' style='width: 100px;'>Anular Gasto</button></span></td>";
					}else{
						echo "<td></td>";
					}
						
					
					echo "<input type='hidden' value='".$simboloMoneda."' id='SMoneda'>";
					echo "<input class='iddetalle' type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."'>";
					echo "<input class='formacobro' type='hidden' value='".$dataDetalleOrdenCobro[$i]['formacobro']."'>";
					echo "<input class='iddetalleordencobro' type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."'>";
					echo "<input class='valorLetra' type='hidden' value='".round($dataDetalleOrdenCobro[$i]['importedoc'],2)."'>";
					echo "<input class='valorSaldo' type='hidden' value='".round($dataDetalleOrdenCobro[$i]['saldodoc'],2)."'>";
					echo "</tr>";
				}else{
					
					
					
					echo "<td></td>";
					echo "<td></td>";
					echo "<td>";
					echo "<input type='hidden' value='".$simboloMoneda."' id='SMoneda'>";
					echo "<input class='iddetalle' type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."'>";
					echo "<input class='formacobro' type='hidden' value='".$dataDetalleOrdenCobro[$i]['formacobro']."'>";
					echo "<input class='iddetalleordencobro' type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."'>";
					echo "<input class='valorLetra' type='hidden' value='".round($dataDetalleOrdenCobro[$i]['importedoc'],2)."'>";
					echo "<input class='valorSaldo' type='hidden' value='".round($dataDetalleOrdenCobro[$i]['saldodoc'],2)."'>";
					echo "</td>";
					echo "</tr>";
				}
				
			}
			echo "<tr ><td style='background:silver;' colspan='13'>&nbsp</td></tr>";
			//var_dump($referencias);var_dump($dataDetalleOrdenCobro);
		}
	}
        
        function buscarDetalleOrdenCobroIntereses() {
            $idOrdenVenta = $_REQUEST['id'];
            $ordencobro = New OrdenCobro();
            $tipoGasto = $this->AutoLoadModel('tipogasto');
            $ordenGasto = $this->AutoLoadModel('ordengasto');
            $ordenVenta = $this->AutoLoadModel('ordenventa');


            $dataOrdenCobro = $ordencobro->listarxguia($idOrdenVenta);
            $dataOrdenVenta = $ordenVenta->buscarOrdenVentaxId($idOrdenVenta);
            $simbolo = $dataOrdenVenta[0]['Simbolo'];
            $cantidadOrdenCobro = count($dataOrdenCobro);

            $montoProgramacion = $ordencobro->deudatotal($idOrdenVenta);
            $montoGuia = $ordenGasto->totalGuia($idOrdenVenta);
            $montoPagado = $dataOrdenVenta[0]['importepagado'];
            $montoDeuda = $montoGuia - $montoPagado;

            $Percepcion = $ordenGasto->importeGasto($idOrdenVenta, 6);
            $fechavencimiento = $dataOrdenVenta[0]['fechavencimiento'];
            echo "<tr>";
            echo "<th style='color:black;background:#4096EE;'>Total <br>Guia:</th>";
            echo "<td style='color:blue'>" . $simbolo . " " . number_format($montoGuia, 2) . "</td>";

            echo "<th style='color:black;background:#4096EE;'>Total <br>Pendiente:</th>";
            echo "<td style='color:blue'>" . $simbolo . " " . number_format($montoDeuda, 2) . "</td>";

            echo "<th style='color:black;background:#4096EE;'>Total <br>Pagado:</th>";
            echo "<td style='color:blue'>" . $simbolo . " " . number_format($montoPagado, 2) . "</td>";
            echo "<th style='color:black;background:#4096EE;'>Percepcion:</th>";
            echo "<td style='color:blue'>" . $simbolo . " " . number_format($Percepcion, 2);
            echo "<th style='color:black;background:#4096EE;'>Zona:</th>";
            echo "<td style='color:blue'>" . $simbolo . " " . number_format($Percepcion, 2);
            echo "<input type='hidden' id='montoProgramacion' value='" . round($montoProgramacion, 2) . "'>";
            echo "<input type='hidden' id='montoReal' value='" . $montoGuia . "'>";
            echo "<input type='hidden' id='errorAjuste' value='" . $this->configIni($this->configIni('Globals', 'Modo'), 'errorAjuste') . "'>";
            echo "<input type='hidden' id='fechavencimiento' value='" . $fechavencimiento . "'> </td>";
            echo "</tr>";
            echo "<tr>";
            echo "<th style='background:white;'>&nbsp</th>";
            echo "</tr>";
            for ($y = 0; $y < $cantidadOrdenCobro; $y++) {

                $detalleOrdenCobro = New DetalleOrdenCobro();
                $dataDetalleOrdenCobro = $detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$y]['idordencobro']);
                $tamanio = count($dataDetalleOrdenCobro);
                echo "<tr>";
                echo "<th>Monto Total:</th>";
                echo "<th>Saldo:</th>";
                echo "<th>Fecha emisión</th>";
                echo "<th colspan=9>&nbsp</th>";
                echo "</tr>";
                echo "<tr>";
                echo "<td><b>" . $simbolo . " " . number_format($dataOrdenCobro[$y]['importeordencobro'], 2) . "</b></td>";
                echo "<td>" . $simbolo . " " . number_format($dataOrdenCobro[$y]['saldoordencobro'], 2) . "</td>";
                echo "<td>" . $dataOrdenCobro[$y]['femision'] . "</td>";
                echo "<th colspan=9>&nbsp</th>";
                echo "</tr>";
                echo "<tr>";
                echo "<th>Nro:</th>";
                echo "<th>Padre:</th>";
                echo "<th>Monto:</th>";
                echo "<th>Saldo:</th>";
                echo "<th>Condicion:</th>";
                echo "<th>Nro letra:</th>";
                echo "<th>Fecha<br> Giro:</th>";
                echo "<th>Fecha<br> Vencimiento:</th>";
                echo "<th>Dias</th>";
                echo "<th>Situación:</th>";
                echo "<th>Tipo<br>Gasto:</th>";

                echo "<th>Acciones:</th>";
                echo "</tr>";
                for ($i = 0; $i < $tamanio; $i++) {
                    echo "<tr>";
                    echo "<td>" . ($dataDetalleOrdenCobro[$i]['iddetalleordencobro']) . "</td>";
                    echo "<td>" . (!empty($dataDetalleOrdenCobro[$i]['idpadre']) ? $dataDetalleOrdenCobro[$i]['idpadre'] : '') . "</td>";
                    echo "<td><label >" . $simbolo . " " . number_format($dataDetalleOrdenCobro[$i]['importedoc'], 2) . "</label><input type='hidden' class='importedoc' value='" . $dataDetalleOrdenCobro[$i]['importedoc'] . "' ></td>";
                    echo "<td><label >" . $simbolo . " " . number_format($dataDetalleOrdenCobro[$i]['saldodoc'], 2) . "</label><input type='hidden' class='saldodoc' value='" . $dataDetalleOrdenCobro[$i]['saldodoc'] . "' ></td>";
                    switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
                        case '1': $formacobro = "Contado";
                            break;
                        case '2': $formacobro = "Crédito";
                            break;
                        case '3': $formacobro = "Letras";
                            break;
                    }
                    echo "<td>" . $formacobro . "</td>";
                    echo "<td><label class='lblletra'>" . $dataDetalleOrdenCobro[$i]['numeroletra'] . "</td>";
                    echo "<td><label class='fechavencimiento'>" . $dataDetalleOrdenCobro[$i]['fechagiro'] . "</label></td>";
                    echo "<td><label class='fechavencimiento'>" . $dataDetalleOrdenCobro[$i]['fvencimiento'] . "</label></td>";
                    $dias = $detalleOrdenCobro->cantidad_dias_entre_dos_fechas($dataDetalleOrdenCobro[$i]['fechagiro'], $dataDetalleOrdenCobro[$i]['fvencimiento']);
                    echo "<td style='text-align: center'>$dias</td>";
                    echo "<td class='situacion'><label>" . (($dataDetalleOrdenCobro[$i]['situacion'] == '') ? 'pendiente ref (' . $dataDetalleOrdenCobro[$i]['referencia'] . ')' : $dataDetalleOrdenCobro[$i]['situacion'] . ' ref (' . $dataDetalleOrdenCobro[$i]['referencia'] . ')') . "</label></td>";
                    echo "<td >" . $tipoGasto->nombreGasto($dataDetalleOrdenCobro[$i]['tipogasto']) . "</td>";
                    if (strcmp($dataDetalleOrdenCobro[$i]['situacion'], "") == 0) {
                        if ($dataDetalleOrdenCobro[$i]['formacobro'] == '3' && $dataDetalleOrdenCobro[$i]['interes'] == 0) {
                            if ($dias > 75) {
                                echo "<td style='text-align: center'><button class='btnIntereses' data-id='" . $dataDetalleOrdenCobro[$i]['iddetalleordencobro'] . "'>Sumar Interes</td>";
                            } else {
                                echo "<td></td>";
                            }
                        } else {
                            echo "<td></td>";
                        }
                    } else {
                        echo "<td></td>";
                    }
                }
                echo "<tr ><td style='background:silver;' colspan='12'>&nbsp</td></tr>";
            }
    }

    function buscarDetalleOrdenCobroPagos()
	{
		$idOrdenVenta=$_REQUEST['id'];
		$ordencobro=New OrdenCobro();
		$tipoGasto=$this->AutoLoadModel('tipogasto');
		$ordenGasto=$this->AutoLoadModel('ordengasto');
		$ordenVenta=$this->AutoLoadModel('ordenventa');


		$dataOrdenCobro=$ordencobro->listarxguia($idOrdenVenta);
		$dataOrdenVenta=$ordenVenta->buscarOrdenVentaxId($idOrdenVenta);
		$simbolo=$dataOrdenVenta[0]['Simbolo'];
		$cantidadOrdenCobro=count($dataOrdenCobro);

		$montoProgramacion=$ordencobro->deudatotal($idOrdenVenta);
		$montoGuia=$ordenGasto->totalGuia($idOrdenVenta);
		$montoPagado=$dataOrdenVenta[0]['importepagado'];
		$montoDeuda=$montoGuia-$montoPagado;

		$Percepcion=$ordenGasto->importeGasto($idOrdenVenta,6);
		$fechavencimiento=$dataOrdenVenta[0]['fechavencimiento'];
			echo "<tr>";
			echo "<th style='color:black;background:#4096EE;'>Total <br>Guia:</th>";
			echo "<td style='color:blue'>".$simbolo." ".number_format($montoGuia,2)."</td>";

			echo "<th style='color:black;background:#4096EE;'>Total <br>Pendiente:</th>";
			echo "<td style='color:blue'>".$simbolo." ".number_format($montoDeuda,2)."</td>";

			echo "<th style='color:black;background:#4096EE;'>Total <br>Pagado:</th>";
			echo "<td style='color:blue'>".$simbolo." ".number_format($montoPagado,2)."</td>";
			echo "<th style='color:black;background:#4096EE;'>Percepcion:</th>";
			echo "<td style='color:blue'>".$simbolo." ".number_format($Percepcion,2);
			echo "<input type='hidden' id='montoProgramacion' value='".round($montoProgramacion,2)."'>";
			echo "<input type='hidden' id='montoReal' value='".$montoGuia."'>";
			echo "<input type='hidden' id='errorAjuste' value='".$this->configIni($this->configIni('Globals', 'Modo'), 'errorAjuste')."'>";
			echo "<input type='hidden' id='fechavencimiento' value='".$fechavencimiento."'> </td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th style='background:white;'>&nbsp</th>";
			echo "</tr>";
		for ($y=0; $y <$cantidadOrdenCobro ; $y++) {

			$detalleOrdenCobro=New DetalleOrdenCobro();
			$dataDetalleOrdenCobro=$detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$y]['idordencobro']);
			$tamanio=count($dataDetalleOrdenCobro);
			echo "<tr>";
			echo "<th>Monto Total:</th>";
			echo "<th>Saldo:</th>";
			echo "<th>Fecha emisión</th>";
			echo "<th colspan=9>&nbsp</th>";
			echo "</tr>";
			echo "<tr>";
			echo "<td><b>".$simbolo." ".number_format($dataOrdenCobro[$y]['importeordencobro'],2)."</b></td>";
			echo "<td>".$simbolo." ".number_format($dataOrdenCobro[$y]['saldoordencobro'],2)."</td>";
			echo "<td>".$dataOrdenCobro[$y]['femision']."</td>";
			echo "<th colspan=9>&nbsp</th>";
			echo "</tr>";
			echo "<tr>";
			echo "<th>Nro:</th>";
			echo "<th>Padre:</th>";
			echo "<th>Monto:</th>";
			echo "<th>Saldo:</th>";
			echo "<th>Condicion:</th>";
			echo "<th>Nro letra:</th>";
			echo "<th>Fecha<br> Vencimiento:</th>";
			echo "<th>Situación:</th>";
			echo "<th>Tipo<br>Gasto:</th>";

			echo "<th colspan=3>Acciones:</th>";
			echo "</tr>";
			for ($i=0; $i < $tamanio; $i++) {
				echo "<tr>";
				echo "<td>".($dataDetalleOrdenCobro[$i]['iddetalleordencobro'])."</td>";
				echo "<td>".(!empty($dataDetalleOrdenCobro[$i]['idpadre'])?$dataDetalleOrdenCobro[$i]['idpadre']:'')."</td>";
				echo "<td><label >".$simbolo." ".number_format($dataDetalleOrdenCobro[$i]['importedoc'],2)."</label><input type='hidden' class='importedoc' value='".$dataDetalleOrdenCobro[$i]['importedoc']."' ></td>";
				echo "<td><label >".$simbolo." ".number_format($dataDetalleOrdenCobro[$i]['saldodoc'],2)."</label><input type='hidden' class='saldodoc' value='".$dataDetalleOrdenCobro[$i]['saldodoc']."' ></td>";
				switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
					case '1': $formacobro="Contado"; break;
					case '2': $formacobro="Crédito"; break;
                                        case '3': $formacobro="Letras";
                                            if ($dataDetalleOrdenCobro[$i]['recepcionLetras'] == 'PA') {
                                                //if ($dataDetalleOrdenCobro[$i]['numerounico'] == 'CPA' || $dataDetalleOrdenCobro[$i]['numerounico'] == 'R-BBVA' || $dataDetalleOrdenCobro[$i]['numerounico'] == 'R-BCP' || $dataDetalleOrdenCobro[$i]['numerounico'] == 'R-CL') {
                                                    $formacobro .= ' ' . $dataDetalleOrdenCobro[$i]['numerounico'];
                                                //}
                                            }
				}
				echo "<td>".$formacobro."</td>";
				echo "<td><label class='lblletra'>".$dataDetalleOrdenCobro[$i]['numeroletra']."</td>";
				echo "<td><label class='fechavencimiento'>".$dataDetalleOrdenCobro[$i]['fvencimiento']."</label></td>";



				echo "<td class='situacion'><label>".(($dataDetalleOrdenCobro[$i]['situacion']=='')?'pendiente ref ('.$dataDetalleOrdenCobro[$i]['referencia'].')':$dataDetalleOrdenCobro[$i]['situacion'].' ref ('.$dataDetalleOrdenCobro[$i]['referencia'].')')."</label></td>";
				echo "<td >".$tipoGasto->nombreGasto($dataDetalleOrdenCobro[$i]['tipogasto'])."</td>";
				if (strcmp($dataDetalleOrdenCobro[$i]['situacion'],"")==0) {


					if ($dataDetalleOrdenCobro[$i]['formacobro']=='1') {

						echo "<td><span class='c1_datashet'><button class=cancelar>Pagar Todo</button><input type='hidden' class='iddetalleordencobro' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."' ></span></td>";
						echo "<td><span class='c2_datashet'><button class=pagarparte>Pagar una parte</button></span><input class='valorLetra' type='hidden' value='".$dataDetalleOrdenCobro[$i]['importedoc']."'></td>";
						if ($dataDetalleOrdenCobro[$i]['importedoc']>$dataDetalleOrdenCobro[$i]['saldodoc']) {
							echo "<td><span class='c3_datashet'><button class=deshacerPago>Des. Pago</button></span>";
						}else{
							echo "<td></td>";
						}


					}elseif($dataDetalleOrdenCobro[$i]['formacobro']=='2' || $dataDetalleOrdenCobro[$i]['formacobro']=='3' && $dataOrdenCobro[$y]['tipoletra']==2){
						echo "<td><span class='c4_datashet'><button class=cancelar>Pagar Todo</button></span><input type='hidden' class='iddetalleordencobro' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."' >
							<input class='valorLetra' type='hidden' value='".$dataDetalleOrdenCobro[$i]['importedoc']."'></td>";
						echo "<td><span class='c5_datashet'><button class=pagarparte>Pagar una parte</button></span></td>";
						if ($dataDetalleOrdenCobro[$i]['importedoc']>$dataDetalleOrdenCobro[$i]['saldodoc']) {
							echo "<td><span class='c6_datashet'><button class=deshacerPago>Des. Pago</button></span>";
						}else{
							echo "<td></td>";
						}



                                            } else if ($dataDetalleOrdenCobro[$i]['formacobro']=='3'&&$dataDetalleOrdenCobro[$i]['recepcionLetras']=='PA'&&($dataDetalleOrdenCobro[$i]['numerounico']=='R-CL'||$dataDetalleOrdenCobro[$i]['numerounico']=='CPA')){

						echo "<td><span class='c1_datashet'><button class=cancelar>Pagar Todo</button><input type='hidden' class='iddetalleordencobro' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."' ></span></td>";
						echo "<td><span class='c2_datashet'><button class=pagarparte>Pagar una parte</button></span><input class='valorLetra' type='hidden' value='".$dataDetalleOrdenCobro[$i]['importedoc']."'></td>";
						if ($dataDetalleOrdenCobro[$i]['importedoc']>$dataDetalleOrdenCobro[$i]['saldodoc']) {
							echo "<td><span class='c3_datashet'><button class=deshacerPago>Des. Pago</button></span>";
						}else{
							echo "<td></td>";
						}


					} else{
						echo "<td></td>";
						echo "<td></td>";
						echo "<td></td>";

					}
				}else{
					if (strcmp($dataDetalleOrdenCobro[$i]['situacion'],"cancelado")==0) {
						echo "<td><span class='c7_datashet'><button class=deshacerPago>Des. Pago</button></span>
							<input type='hidden' class='iddetalleordencobro' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."' >
							<input class='valorLetra' type='hidden' value='".$dataDetalleOrdenCobro[$i]['importedoc']."'></td>";
					}else{
						echo "<td></td>";
					}


					echo "<td></td>";
					echo "<td></td>";
				}



			}
			echo "<tr ><td style='background:silver;' colspan='12'>&nbsp</td></tr>";
		}
	}

    
        
	function buscarDetalleOrdenCobroGuia(){
		$idOrdenVenta=$_REQUEST['id'];
                $tipoGasto=$this->AutoLoadModel('tipogasto');
		$dataGuia=$this->AutoLoadModel("OrdenVenta");
		$idTipoCambio=$dataGuia->BuscarCampoOVxId($idOrdenVenta,"IdTipoCambioVigente");//PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA

		$TipoCambio=$this->AutoLoadModel("TipoCambio");
		$dataTipoCambio=$TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
		$simboloMoneda=$dataTipoCambio[0]['simbolo'];
		$TC_PrecioVenta=$dataTipoCambio[0]['venta'];


		$ordencobro=New OrdenCobro();
		$dataOrdenCobro=$ordencobro->listarxguia($idOrdenVenta);
		$detalleOrdenCobro=New DetalleOrdenCobro();
		$cantidadOrdenCobro=count($dataOrdenCobro);
		for ($n=0; $n <$cantidadOrdenCobro ; $n++) { 
			
				$dataDetalleOrdenCobro=$detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$n]['idordencobro']);
				$tamanio=count($dataDetalleOrdenCobro);
			
				
				echo "	<tr class='contenedorOcultador'>
							<td colspan=8 style='color:black;background:#4096EE;'><h3>Condiciones financieras:<input type='checkbox' value='".$n."' class='ocultador'></h3></td>
						</tr>";

				echo "<tr class='".$n."'>";
				echo "<th width='5%'>Nro:</th>";
				echo "<th>Monto:</th>";
				echo "<th>Condicion:</th>";
				echo "<th>Nro letra:</th>";
				echo "<th>Fecha Giro:</th>";
				echo "<th>Fecha Vencimiento:</th>";
				echo "<th>Situación:</th>";
                                echo "<th>Tipo Gasto:</th>";
				echo "</tr>";		
				for ($i=0; $i < $tamanio; $i++) { 
					echo "<tr class='".$n."'>";
					echo "<td>".($dataDetalleOrdenCobro[$i]['iddetalleordencobro'])."</td>";
					
					echo "<td>".' '.$simboloMoneda.' '.number_format($dataDetalleOrdenCobro[$i]['importedoc'],2)."</td>";
					switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
						case '1': $formacobro="Contado"; break;
						case '2': $formacobro="Crédito"; break;
						case '3': $formacobro="Letras"; break;								
					}
					echo "<td>".$formacobro."</td>";
					echo "<td>".$dataDetalleOrdenCobro[$i]['numeroletra']."</td>";
					echo "<td>".$dataDetalleOrdenCobro[$i]['fechagiro']."</td>";
					echo "<td>".$dataDetalleOrdenCobro[$i]['fvencimiento']."</td>";
					$situacion=empty($dataDetalleOrdenCobro[$i]['situacion'])?"Pendiente":$dataDetalleOrdenCobro[$i]['situacion'];
					echo "<td>".$situacion."</td>";
                                        echo "<td>".$tipoGasto->nombreGasto($dataDetalleOrdenCobro[$i]['tipogasto'])."</td>";
					echo "</tr>";
				}
				echo "<tr class='".$n."'>";
				echo "<th colspan='2'>Monto Total:</th>";
				echo "<td><b>".' '.$simboloMoneda.' '.number_format($dataOrdenCobro[$n]['importeordencobro'],2)."</b></td>";
				echo "<th colspan=5>&nbsp</th>";
				echo "</tr>";
				echo "<tr class='".$n."'><td colspan='8' style='background:white;'>&nbsp</td></tr>";
			
		}
	}
	function buscarDetalleOrdenCobroEstadoGuia()
	{
		$idOrdenVenta=$_REQUEST['id'];

		$dataGuia=$this->AutoLoadModel("OrdenVenta");
		$idTipoCambio=$dataGuia->BuscarCampoOVxId($idOrdenVenta,"IdTipoCambioVigente");//PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA

		$TipoCambio=$this->AutoLoadModel("TipoCambio");
		$dataTipoCambio=$TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
		$simboloMoneda=$dataTipoCambio[0]['simbolo'];
		$TC_PrecioVenta=$dataTipoCambio[0]['venta']; ///desde 
		$ordencobro=New OrdenCobro();
		$dataOrdenCobro=$ordencobro->listarxguia($idOrdenVenta);
		$detalleOrdenCobro=New DetalleOrdenCobro();
		$cantidadOrdenCobro=count($dataOrdenCobro);
		$actor=new Actor();
		$tipoGasto=$this->AutoLoadModel('tipogasto');
                $archivoConfig = parse_ini_file("config.ini", true);

                $Numerounicomodel=$this->AutoLoadModel("Numerounico");
                $numerosunicos = $Numerounicomodel->listarNumerounico();
		for ($n=0; $n <$cantidadOrdenCobro ; $n++) { 
		
			$dataDetalleOrdenCobro=$detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$n]['idordencobro']);
			$tamanio=count($dataDetalleOrdenCobro);
			echo "	<tr>
						<tH colspan=14><h3>DETALLE DE LA PROGRAMACION DE PAGOS:</h3></tH>
					</tr>";

			echo "<tr>";
			echo "<th>Nro:</th>";
			echo "<th>Padre:</th>";
			echo "<th>Monto:</th>";
			echo "<th>Saldo:</th>";
			echo "<th>Condicion:</th>";
			echo "<th>Nro letra:</th>";
			echo "<th>Fecha Giro:</th>";
			echo "<th>Fecha<br> Vencimiento:</th>";
			echo "<th>Fecha<br> Pago:</th>";
			echo "<th>Situación:</th>";
			echo "<th>Tipo Gasto:</th>";
			echo "<th>Usuario Mod.:</th>";
			echo "<th>N° Unico</th>";
			echo "<th>R. de letra :</th>";
			echo "</tr>";		
			for ($i=0; $i < $tamanio; $i++) { 
				$dataActor=$actor->buscarxid($dataDetalleOrdenCobro[$i]['usuariomodificacion']);
				echo "<tr>";
				echo "<td>".($dataDetalleOrdenCobro[$i]['iddetalleordencobro'])."</td>";
				echo "<td>".(empty($dataDetalleOrdenCobro[$i]['idpadre'])?'':$dataDetalleOrdenCobro[$i]['idpadre'])."</td>";
				echo "<td>".' '.$simboloMoneda.' '.number_format($dataDetalleOrdenCobro[$i]['importedoc'],2)."</td>";
				echo "<td>".' '.$simboloMoneda.' '.number_format($dataDetalleOrdenCobro[$i]['saldodoc'],2)."</td>";
				switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
					case '1': $formacobro="Contado"; break;
					case '2': $formacobro="Crédito"; break;
					case '3': $formacobro="Letras"; break;				
				}
				echo "<td>".$formacobro."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['numeroletra']."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['fechagiro']."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['fvencimiento']."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['fechapago']."</td>";
				$situacion=(($dataDetalleOrdenCobro[$i]['situacion']=='')?'pendiente ref ('.$dataDetalleOrdenCobro[$i]['referencia'].')':$dataDetalleOrdenCobro[$i]['situacion'].' ref ('.$dataDetalleOrdenCobro[$i]['referencia'].')');
				echo "<td>".$situacion."<input type='hidden' value=".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']." class='iddetalleordencobro'></td>";
				echo "<td >".$tipoGasto->nombreGasto($dataDetalleOrdenCobro[$i]['tipogasto'])."</td>";
				echo "<td >".($dataActor[0]['nombres'].' '.$dataActor[0]['apellidopaterno'].' '.$dataActor[0]['apellidomaterno'])."<button style='font-size:5;heigth:5;width:15' class='motivo' value='".$dataDetalleOrdenCobro[$i]['motivo']."'>..</button></td>";
				
				if ($dataDetalleOrdenCobro[$i]['formacobro']==3) {
                                    echo "<td>" . 
                                            "<span style='color: blue' class='Textnumerounico'>" . $dataDetalleOrdenCobro[$i]['numerounico'] . "</span>";
                                    echo    "<select class='numerounico' style='display:none;'>
                                             <option value=''> </option>";
                                    $bandera = 0;
                                    for ($inu = 0; $inu < count($numerosunicos); $inu++) {
                                        if ($dataDetalleOrdenCobro[$i]['numerounico'] == $numerosunicos[$inu]['codigo']) {
                                            echo "<option value='" . $numerosunicos[$inu]['codigo'] . "' selected>" . $numerosunicos[$inu]['nombre'] . "</option>";
                                            $bandera = 1;
                                        } else {
                                            echo "<option value='" . $numerosunicos[$inu]['codigo'] . "'>" . $numerosunicos[$inu]['nombre'] . "</option>";
                                        }
                                    }
                                    if ($bandera == 0 && $dataDetalleOrdenCobro[$i]['numerounico'] != '') {
                                        echo "<option value='" . $dataDetalleOrdenCobro[$i]['numerounico'] . "' selected>" . $dataDetalleOrdenCobro[$i]['numerounico'] . "</option>";
                                    }
                                    echo    "</select>";
                                            //"<input class='numerounico' size='8' maxlength='11' type='text' value='".$dataDetalleOrdenCobro[$i]['numerounico']."' readonly>" . 
                                    echo "</td>";
                                    echo "<td></a><input type='text' value='".$dataDetalleOrdenCobro[$i]['recepcionLetras']."' class='recepcionLetra uppercase ' size='1' maxlength='2' readonly > <br><a href='' class='editar'><img src='/imagenes/editar.gif' >  <a class='grabar' href=''><img width='21' heigth='21' src='/imagenes/grabar.gif' ></a> </td>";
//					echo "<td><input class='numerounico' size='8' maxlength='11' type='text' value='".$dataDetalleOrdenCobro[$i]['numerounico']."' readonly></td>";
//					echo "<td></a><input type='text' value='".$dataDetalleOrdenCobro[$i]['recepcionLetras']."' class='recepcionLetra uppercase ' size='1' maxlength='2' readonly > <br><a href='' class='editar'><img src='/imagenes/editar.gif' >  <a class='grabar' href=''><img width='21' heigth='21' src='/imagenes/grabar.gif' ></a> </td>";
				} else {
					echo "<td></td>";
				}
				echo "</tr>";
			}
			echo "<tr>";
			echo "<th>Monto :</th>";
			echo "<td><b>".' '.$simboloMoneda.' '.number_format($dataOrdenCobro[$n]['importeordencobro'],2)."</b></td>";
			echo "<th>Saldo :</th>";
			echo "<td><b>".' '.$simboloMoneda.' '.number_format($dataOrdenCobro[$n]['saldoordencobro'],2)."</b></td>";
			echo "<th>Situación :</th>";
			echo "<td><b>".$dataOrdenCobro[$n]['situacion']."</b></td>";
			echo "<th colspan=9>&nbsp</th>";
			echo "</tr>";
			echo "<tr><td>&nbsp</td></tr>";
		}
	}
        
        function cargardetalleordencobro() {
            $iddetalle = $_POST['iddetalle'];
            $data['estacargada'] = $_POST['estado'];
            $detalleordencobro=$this->AutoLoadModel('detalleordencobro');
            $estado = $detalleordencobro->actualizar_cargado($data, $iddetalle);
            $datos['rspta'] = $estado;
            header('Content-type: application/json; charset=utf-8');
            echo json_encode($datos);
        }

function actualizadetalleordencobro()
	{
		$detalleordencobro=$this->AutoLoadModel('detalleordencobro');
		$iddetalleordencobro=$_REQUEST['iddetalleordencobro'];
		$data['recepcionLetras']=$_REQUEST['recepcionLetras'];
		$data['numerounico']=$_REQUEST['numerounico'];
        if(isset($_REQUEST['evaluacion']) and $_REQUEST['recepcionLetras']=="PA"){
                    $data['evaluacion']='2';
        }
        if(isset($_REQUEST['evaluacion']) and $_REQUEST['recepcionLetras']==""){
                    $data['evaluacion']='1';
        }
		$exito=$detalleordencobro->actualizaDetalleOrdencobro($data,$iddetalleordencobro);
		echo $exito;
	}



	function verificarCobro(){
		$idOrdenVenta=$_REQUEST['idOrdenVenta'];
		//$idOrdenVenta=$_REQUEST['id'];
		$ordencobro=New OrdenCobro();
		$ordenventa=New OrdenVenta();
		$dataOrdenCobro=$ordencobro->buscaxFiltro("idordenventa='$idOrdenVenta' and situacion='Pendiente'");

		
		if (empty($dataOrdenCobro)) {
			$data['situacion']="cancelado";
			$data['fechaCancelado']=date('Y-m-d');
			$exito=$ordenventa->actualizaOrdenVenta($data,$idOrdenVenta);
			echo $exito;
		}else{
			$data['situacion']="Pendiente";
			$data['fechaCancelado']=null;
			$exito=$ordenventa->actualizaOrdenVenta($data,$idOrdenVenta);
			echo $exito;
			echo $cantidadOrdenCobro;
		}

	}

	function buscarImporteExtornado(){
		$detalleordencobro=$this->AutoLoadModel('detalleordencobro');
		$iddetalleordencobro=$_REQUEST['iddetalleordencobro'];

		$datadetordencobro=$detalleordencobro->buscaDetalleOrdencobro2($iddetalleordencobro);
		$idordencobro=$datadetordencobro[0]['idordencobro'];

		$databusqueda=$detalleordencobro->listadoxidOrdenCobroxrenovado($idordencobro);
		$cantidad=count($databusqueda);
		$importe=0;
		for ($i=0; $i <$cantidad ; $i++) { 
			$importe+=$databusqueda[$i]['importedoc'];
		}
		$dataRespuesta['importe']=$importe;
		echo json_encode($dataRespuesta);
	}
	
	function buscarDetallePercepcion(){
		$idOrdenVenta=$_REQUEST['orden'];

		$dataGuia=$this->AutoLoadModel("OrdenVenta");
		$idTipoCambio=$dataGuia->BuscarCampoOVxId($idOrdenVenta,"IdTipoCambioVigente");//PREGUNTAR SI ACTUAL O AL ELEGIDO EN LA COMPRA

		$TipoCambio=$this->AutoLoadModel("TipoCambio");
		$dataTipoCambio=$TipoCambio->consultaDatosTCVigentexTCElegido($idTipoCambio);
		$simboloMoneda=$dataTipoCambio[0]['simbolo'];
		$TC_PrecioVenta=$dataTipoCambio[0]['venta'];

		$tipoAccion=$_REQUEST['tipoAccion'];
		$montoPercepcion=$_REQUEST['montoPercepcion'];
		$idOrdenGasto=$_REQUEST['idOrdenGasto'];
		$ordencobro=parent::AutoLoadModel('ordencobro');
		$dataOrdenCobro=$ordencobro->listarxguia($idOrdenVenta);
		
		$detalleOrdenCobro=parent::AutoLoadModel('detalleordencobro');
		$cantidadOrdenCobro=count($dataOrdenCobro);
		$redondeo=parent::configIni('Globals','Redondeo');
		for ($n=0; $n <$cantidadOrdenCobro ; $n++) { 
			$dataDetalleOrdenCobro=$detalleOrdenCobro->listadoxidOrdenCobro($dataOrdenCobro[$n]['idordencobro']);
			$tamanio=count($dataDetalleOrdenCobro);
			echo "<input type='hidden' value='".$montoPercepcion."' id='percepcion'>";
			echo "<input type='hidden' value='".(empty($idOrdenGasto)?'0':$idOrdenGasto)."' id='idOrdenGasto'>";
			echo "<table>";
			echo "<tr>";
			echo "<td colspan=10><h3>Condiciones financieras:</h3></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<th>Nro:</th>";
			echo "<th>Padre</th>";
			echo "<th>Monto:</th>";
			echo "<th>Saldo:</th>";
			echo "<th>Condicion:</th>";
			echo "<th>Nro letra:</th>";
			echo "<th>Fecha Giro:</th>";
			echo "<th>Fecha Vencimiento:</th>";
			echo "<th>Situación:</th>";
			echo "<th>R. de letra :</th>";
			echo "<th></th>";
			echo "</tr>";		
			for ($i=0; $i < $tamanio; $i++) { 
				echo "<tr>";
				echo "<td>";
				echo $dataDetalleOrdenCobro[$i]['iddetalleordencobro'];
				echo "<input type='hidden' value='".$dataDetalleOrdenCobro[$i]['iddetalleordencobro']."' class='idDetalleOrdenCobro'>";
				echo "<input type='hidden' value='".round($dataDetalleOrdenCobro[$i]['importedoc'],$redondeo)."' class='importe'>";
				echo "<input type='hidden' value='".round($dataDetalleOrdenCobro[$i]['saldodoc'],$redondeo)."' class='saldo'>";
				echo "<input type='hidden' value='".$dataDetalleOrdenCobro[$i]['numeroletra']."' class='numDoc'>";
				echo "</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['idpadre']."</td>";
				echo "<td>".' '.$simboloMoneda.' '.number_format($dataDetalleOrdenCobro[$i]['importedoc'],$redondeo)."</td>";
				echo "<td>".' '.$simboloMoneda.' '.number_format($dataDetalleOrdenCobro[$i]['saldodoc'],$redondeo)."</td>";
				switch ($dataDetalleOrdenCobro[$i]['formacobro']) {
					case '1': $formacobro="Contado"; break;
					case '2': $formacobro="Crédito"; break;
					case '3': $formacobro="Letras"; break;								
				}
				echo "<td>".$formacobro."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['numeroletra']."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['fechagiro']."</td>";
				echo "<td>".$dataDetalleOrdenCobro[$i]['fvencimiento']."</td>";
				$situacion=empty($dataDetalleOrdenCobro[$i]['situacion'])?"Pendiente":$dataDetalleOrdenCobro[$i]['situacion'];
				echo "<td>".$situacion." ref(".$dataDetalleOrdenCobro[$i]['referencia'].")";
				echo "<td>".$dataDetalleOrdenCobro[$i]['recepcionLetras']."</td>";
				if($tipoAccion==1 && strtolower($dataDetalleOrdenCobro[$i]['situacion'])=="" && $dataDetalleOrdenCobro[$i]['renovado']==0){
					echo "<td> <a class='btnAumentarPercepcion' title='Agregar' href='#' ><img width='20' height='20' src='/imagenes/grabar.gif'></a> </td>";
				}else if(strtolower($dataDetalleOrdenCobro[$i]['situacion'])=="" && $dataDetalleOrdenCobro[$i]['renovado']==0 && $dataDetalleOrdenCobro[$i]['importedoc']==$dataDetalleOrdenCobro[$i]['saldodoc'] && round($dataDetalleOrdenCobro[$i]['saldodoc'],$redondeo)==round($montoPercepcion,$redondeo)){
				//}else if(strtolower($dataDetalleOrdenCobro[$i]['situacion'])=="" && $dataDetalleOrdenCobro[$i]['renovado']==0 && $dataDetalleOrdenCobro[$i]['importedoc']==$dataDetalleOrdenCobro[$i]['saldodoc']){
					echo "<td> <a class='btnDisminuirPercepcion' title='Disminuir' href='#'><img width='20' height='20' src='/imagenes/eliminar.gif'></a> </td>";
				}
				
				echo "</tr>";
			}
			echo "<tr>";
			echo "<th colspan='2'>Monto Total:</th>";
			echo "<td><b>".' '.$simboloMoneda.' '.number_format($dataOrdenCobro[$n]['importeordencobro'],$redondeo)."</b></td>";
			echo "<th colspan=8>&nbsp</th>";
			echo "</tr>";
			echo "<table>";
			
		}
		if($tipoAccion==1){
			echo "<button id='btnNP'>Crear programacion x Percepcion</button>";
		}
	}
	public function traerProgramacion(){
		$idDetalleOrdenCobro=$_REQUEST['idDetalleOrdenCobro'];
		
		$detalleOrdenCobro=parent::AutoLoadModel('detalleordencobro');
		
		$dataBusqueda=$detalleOrdenCobro->buscaDetalleOrdencobro($idDetalleOrdenCobro);
		echo json_encode($dataBusqueda[0]);
	}
	public function creaProgramacionPercepcion(){
		$idOrdenVenta=$_REQUEST['idOrdenVenta'];
		$montoPercepcion=$_REQUEST['montoPercepcion'];
		$idOrdenGasto=$_REQUEST['idOrdenGasto'];
		
		$ordenCobro=$this->AutoLoadModel('ordencobro');
		$detalleOrdenCobro=$this->AutoLoadModel('detalleordencobro');
		$ordenGasto=$this->AutoLoadModel('ordengasto');
		$dataOG['importegasto']=$montoPercepcion;
		$dataRespuesta=array();
		
		
		
		if(!empty($idOrdenGasto) || $idOrdenGasto!=0){
			$exitoOG=$ordenGasto->actualiza($dataOG,$idOrdenGasto);
		}else{
			$dataOG['idordenventa']=$idOrdenVenta;
			$dataOG['idtipogasto']=6;
			$exitoOG=$ordenGasto->graba($dataOG);
		}
		
		if($exitoOG){
			$dataOC['importeordencobro']=$montoPercepcion;
			$dataOC['saldoordencobro']=$montoPercepcion;
			$dataOC['escontado']=1;
			$dataOC['idOrdenVenta']=$idOrdenVenta;
			$dataOC['femision']=date('Y-m-d');
			$exitoOC=$ordenCobro->grabaOrdencobro($dataOC);
			
			if($exitoOC){
				$dataDOC['importedoc']=$montoPercepcion;
				$dataDOC['saldodoc']=$montoPercepcion;
				$dataDOC['formacobro']=1;
				$dataDOC['tipogasto']=6;
				$dataDOC['idordencobro']=$exitoOC;
				$dataDOC['fechagiro']=date('Y-m-d');
				$dataDOC['fvencimiento']=date('Y-m-d');
				$exitoDOC=$detalleOrdenCobro->grabaDetalleOrdenVentaCobro($dataDOC);
				if($exitoDOC){
					$dataRespuesta['verificacion']=true;
				}else{
					$dataRespuesta['verificacion']=false;
				}
			}else{
				$dataRespuesta['verificacion']=false;
			}
			
		}else{
			$dataRespuesta['verificacion']=false;
		}
		echo json_encode($dataRespuesta);
	}

	
}

?>