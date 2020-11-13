<?php 

	class letrascontroller extends ApplicationGeneral{
		private $name='letras';
		public function lista()
		{
			$pagina=$_REQUEST['id'];
			if (empty($_REQUEST['id'])) {
				$pagina=1;
			}
			$object=$this->AutoLoadModel($this->name);
			$data['data']=$object->listado();
			$data['titulo']=$this->name;
			$this->view->show('/'.$this->name.'/lista.phtml',$data);
			
		}
		public function nuevo(){
			$data['titulo']=$this->name;
			$this->view->show('/'.$this->name.'/nuevo.phtml',$data);
		}
                
		public function grabar(){
			$object=$this->AutoLoadModel($this->name);
			$condicion=$_REQUEST['condicion'];
			$cant=$_REQUEST['cantidad'];
			
			$diasLetra = split('/', $condicion);
			$cantidad=count($diasLetra);
			$verificacion=1;
			$dato['titulo']=$this->name;

			for ($i=0; $i <$cantidad; $i++) { 
				if ($diasLetra[$i]==0 || $diasLetra[$i]=="") {
					$verificacion=0;
					
					
				}
			}

			if ($cant!=$cantidad) {
				$dato['respuesta']="No coincide la cantidad de letra con el formato";
				$this->view->show('/'.$this->name.'/nuevo.phtml',$dato);
			}
			elseif ($verificacion==0) {
				$dato['respuesta']="No debe Ingresar Cero o Vacio";
				$this->view->show('/'.$this->name.'/nuevo.phtml',$dato);
			}
			else{

				$data['cantidadletra']=$cant;
				$data['nombreletra']=$condicion;
				
				$exito=$object->graba($data);
				
				if ($exito) {
					$ruta['ruta']="/".$this->name."/lista";
					$this->view->show("ruteador.phtml",$ruta);
				}
			}
		}
		public function eliminar(){
			$idletra=$_REQUEST['id'];
			$object=$this->AutoLoadModel($this->name);
			$exito=$object->cambiaEstado($idletra);
			if ($exito) {
				$ruta['ruta']="/".$this->name."/lista";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
                
                public function verLetraPendiente() {
                    $iddetalle = $_POST['iddetalle'];
                    session_start();
                    $dataGuia = $this->AutoLoadModel("OrdenVenta");
                    $ov = $dataGuia->verLetraPendiente($iddetalle); 
                    echo "<tr>";
                        echo "<td colspan='6' style='background:#B4D1F7;'><b>".$ov['codigov']."</b></td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td style='background:#C6DCF9;'><b>Cliente: </b></td>";
                        echo "<td colspan='5'><b>[".$ov['codantiguo']."] ".$ov['razonsocial']."</b></td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td colspan='6'><br></td>";
                    echo "</tr>";
                    echo "<tr>
                            <th>Nro Letra</th>
                            <th>Monto</th>
                            <th>Saldo</th>
                            <th>Fecha Giro</th>
                            <th>Fecha Vencimiento</th>
                        </tr>";
                  
                    echo "<tr>";
                        echo "<td>" . $ov['numeroletra'] . "</td>";
                        echo "<td>" . ' ' . $ov['simbolo'] . ' ' . number_format($ov['importedoc'], 2) . "</td>";
                        echo "<td>" . ' ' . $ov['simbolo'] . ' ' . number_format($ov['saldodoc'], 2) . "</td>";
                        echo "<td>" . $ov['fechagiro'] . "</td>";
                        echo "<td>" . $ov['fvencimiento'] . "</td>";
                    echo "</tr>";
                }

                public function listarletraspendientesAC()  {
                    $nroletra=$_REQUEST['term'];
                    $doc=new DetalleOrdenCobro();
                    $data=$doc->buscaletrasPendientes($nroletra);
                    echo json_encode($data);
                }

                public function letraspendientes($msg = "") {
                    set_time_limit(500);
                    $id = $_REQUEST['id'];
                    if (empty($id)) {
                        $id = 1;
                    }
                    session_start();
                    if(!empty($msg)) $data['msg'] = $msg;
                    
                    $dataGuia = $this->AutoLoadModel("OrdenVenta");
                    $ov = $dataGuia->listarLetrasPendientes($id);
                    $total = count($ov);
                    $resp = "";
                    $temp = "";
                    for ($i=0; $i < $total; $i++) {
                        if(strcmp($ov[$i]['codigov'], $temp) != 0) {
                            $resp .= "<tr>";
                                $resp .= "<td colspan='6'><br></td>";
                            $resp .= "</tr>";                            
                            $resp .= "<tr>"; 
                                $resp .= "<td style='background:#C6DCF9;'><b>Cliente: </b></td>";
                                $resp .= "<td style='background:rgba(198, 220, 249, 0.53);' colspan='2'><b>[".$ov[$i]['codantiguo']."] ".$ov[$i]['razonsocial']."</b></td>";
                                $resp .= "<td style='background:#C6DCF9;'><b>Orden Venta: </b></td>";
                                $resp .= "<td style='background:rgba(198, 220, 249, 0.53);' colspan='2'><b>".$ov[$i]['codigov']."</b></td>";
                            $resp .= "</tr>"; 
                            $temp = $ov[$i]['codigov'];
                        }
                        $resp .= "<tr>";
                            $resp .= "<td>" . $ov[$i]['numeroletra'] . "</td>";
                            $resp .= "<td>" . ' ' . $ov[$i]['simbolo'] . ' ' . number_format($ov[$i]['importedoc'], 2) . "</td>";
                            $resp .= "<td>" . ' ' . $ov[$i]['simbolo'] . ' ' . number_format($ov[$i]['saldodoc'], 2) . "</td>";
                            $resp .= "<td>" . $ov[$i]['fechagiro'] . "</td>";
                            $resp .= "<td>" . $ov[$i]['fvencimiento'] . "</td>";
                        $resp .= "</tr>";
                    }
                    
                    $data['resp'] = $resp;
                    $paginacion = $dataGuia->paginarListarLetrasPendientes();
                    $data['paginacion'] = $paginacion;
                    $data['blockpaginas'] = round($paginacion / 30);
                    
                    $this->view->show('/letras/letraspendientes.phtml', $data);
                }

                public function letrasenelbanco($msg = "") {
                    set_time_limit(500);
                    $id = $_REQUEST['id'];
                    if (empty($id)) {
                        $id = 1;
                    }
                    session_start();
                    if(!empty($msg)) $data['msg'] = $msg;
                    $dataGuia = $this->AutoLoadModel("OrdenVenta");

                    $ov = $dataGuia->listarOrdenesconLetrasPA($id);
                    $total = count($ov);
                    $resp = "";
                    $temp = "";
                    for ($i=0; $i < $total; $i++) {
                        if(strcmp($ov[$i]['codigov'], $temp) != 0) {
                            $resp .= "<tr>";
                                $resp .= "<td colspan='7'><br></td>";
                            $resp .= "</tr>";                            
                            $resp .= "<tr>";
                                $resp .= "<td colspan='7' style='background:#B4D1F7;'><b>".$ov[$i]['codigov']."</b></td>";
                            $resp .= "</tr>";
                            $resp .= "<tr>";
                                $resp .= "<td style='background:#C6DCF9;'><b>Cliente: </b></td>";
                                $resp .= "<td colspan='6'><b>[".$ov[$i]['codantiguo']."] ".$ov[$i]['razonsocial']."</b></td>";
                            $resp .= "</tr>"; 
                            $temp = $ov[$i]['codigov'];
                        }
                        $resp .= "<tr>";
                            $resp .= "<td>" . $ov[$i]['numeroletra'] . "</td>";
                            $resp .= "<td>" . ' ' . $ov[$i]['simbolo'] . ' ' . number_format($ov[$i]['importedoc'], 2) . "</td>";
                            $resp .= "<td>" . ' ' . $ov[$i]['simbolo'] . ' ' . number_format($ov[$i]['saldodoc'], 2) . "</td>";
                            $resp .= "<td>" . $ov[$i]['fechagiro'] . "</td>";
                            $resp .= "<td>" . $ov[$i]['fvencimiento'] . "</td>";
                            $resp .= "<td>" . $ov[$i]['fechapago'] . "</td>";
                            $resp .= "<td><input type='checkbox' class='checkcobro' data-id='".$ov[$i]['iddetalleordencobro']."'></td>";
                        $resp .= "</tr>";
                    }
                    
                    $data['resp'] = $resp;
                    $paginacion = $dataGuia->paginarOrdenesconLetrasPA();
                    $data['paginacion'] = $paginacion;
                    $data['blockpaginas'] = round($paginacion / 30);
                    $this->view->show('/letras/letrasbanco.phtml', $data);
                }
                
                public function CARGARLISTALETRAS() {
                    if ($_REQUEST['talista'] != '') {
                        $lista = explode("\n", $_REQUEST['talista']);
                        $detalleordencobro=$this->AutoLoadModel('detalleordencobro');
                        $c = 0;
                        for ($i = 0; $i < count($lista); $i++) {
                            if(!empty($lista[$i])) {
                                $c++;
                                $data['estacargada'] = 1;
                                $detalleordencobro->actualizar_cargado2($data, $lista[$i]);
                            } 
                        }
                        $this->letrasenelbanco("<b>".$c." Letras cargadas con exito!</b>"); //aqui me quede ok!
                    } else {
                        $this->letrasenelbanco();
                    }
                }

                public function listadeletrassincargar() {
                    $nroletra=$_REQUEST['term'];
                    $doc=new DetalleOrdenCobro();
                    $data=$doc->buscaLetrasinCargar($nroletra);
                    echo json_encode($data);
                }
                
                function verLetrasinCargar() {
                    $iddetalle = $_POST['iddetalle'];
                    session_start();
                    $dataGuia = $this->AutoLoadModel("OrdenVenta");
                    $ov = $dataGuia->verLetrasinCargar($iddetalle); 
                    echo "<tr>";
                        echo "<td colspan='7' style='background:#B4D1F7;'><b>".$ov['codigov']."</b></td>";
                        echo "</tr>";
                        echo "<tr>";
                        echo "<td style='background:#C6DCF9;'><b>Cliente: </b></td>";
                        echo "<td colspan='6'><b>[".$ov['codantiguo']."] ".$ov['razonsocial']."</b></td>";
                    echo "</tr>";
                    echo "<tr>";
                        echo "<td colspan='7'><br></td>";
                    echo "</tr>";
                    echo "<tr>
                            <th>Nro Letra</th>
                            <th>Monto</th>
                            <th>Saldo</th>
                            <th>Fecha Giro</th>
                            <th>Fecha Vencimiento</th>
                            <th>Fecha Pago</th>
                            <th>Asignar</th>
                        </tr>";
                  
                    echo "<tr>";
                        echo "<td>" . $ov['numeroletra'] . "</td>";
                        echo "<td>" . ' ' . $ov['simbolo'] . ' ' . number_format($ov['importedoc'], 2) . "</td>";
                        echo "<td>" . ' ' . $ov['simbolo'] . ' ' . number_format($ov['saldodoc'], 2) . "</td>";
                        echo "<td>" . $ov['fechagiro'] . "</td>";
                        echo "<td>" . $ov['fvencimiento'] . "</td>";
                        echo "<td>" . $ov['fechapago'] . "</td>";
                        echo "<td><input type='checkbox' class='checkcobro' data-id='".$ov['iddetalleordencobro']."'></td>";
                    echo "</tr>";
                }

}

 ?>