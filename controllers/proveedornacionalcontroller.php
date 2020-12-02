<?php

class ProveedornacionalController extends ApplicationGeneral {
    
    public function eliminarEncuesta() {
        if(unlink('public/encuesta/'.$_REQUEST['parameters'][1].'/'.$_REQUEST['parameters'][2])){
            $proveedornacional = new Proveedornacional();
            $proveedornacional->eliminarEncuesta('idproveedornacionalencuesta = '.$_REQUEST['parameters'][0]);
            $numerodeencuestas = $proveedornacional->listadoproveedornacionalencuesta($_REQUEST['parameters'][1]);
            if(count($numerodeencuestas)==0){
                $data['encuesta'] = '0';
                $proveedornacional->actualizaEncuestaProveedornacional($data, $_REQUEST['parameters'][1]);
                rmdir('public/encuesta/'.$_REQUEST['parameters'][1]);
            }
        }
        header("Location: /proveedornacional/encuesta/".$_REQUEST['parameters'][1]);
    }
    
    public function encuesta(){
        $id = $_REQUEST['id'];
        $productoservicio = new Productoservicio();
        $proveedornacional = new Proveedornacional();
        $distrito = new Distrito();
        $provincia = new Provincia();
        $departamento = new Departamento();
        $dataProveedorNacional = $proveedornacional->buscaProveedorNacional($id);
        $data['Productoservicios'] = $productoservicio->listadoProductoservicio();
        
        $proveedornacionalinfcomercialmodel = new Proveedornacionalinfcomercial();
        $data['informacioncomercial'] = $proveedornacionalinfcomercialmodel->listadoxproveedornacional($id);
        $proveedornacionalinftecnicamodel = new Proveedornacionalinftecnica();
        $data['informicontecnica'] = $proveedornacionalinftecnicamodel->listadoxproveedornacional($id);
        $proveedornacionalevaltecnicamodel = new Proveedornacionalevaltecnica();
        $data['evaluaciontecnica'] = $proveedornacionalevaltecnicamodel->listadoxproveedornacional($id);
        
        $proveedornacionalproductoserviciomodel = new Proveedornacionalproductoservicio();
        $data['productoservicios'] = $proveedornacionalproductoserviciomodel->listadoxproveedornacional($id);
        
        $data['Departamento'] = $departamento->listado();
        if ($dataProveedorNacional[0]['iddistrito'] > 0) {
            $dataDistrito = $distrito->buscarxid($dataProveedorNacional[0]['iddistrito']);
            $data['Provincia'] = $provincia->listado($dataDistrito[0]['codigodepto']);
            $data['Distrito'] = $distrito->listado($dataDistrito[0]['idprovincia']);
        }
        
        $data['ProveedorNacional'] = $dataProveedorNacional;
        $data['TipoProveedor'] = $this->tipoCliente();
        $archivoConfig = parse_ini_file("config.ini", true);
        
        $valuador = new Evaluador();
        $data['Evaluadores'] = $valuador->listadoEvaluadores();
        
		  $data['Condiciones'] = $archivoConfig['Condicion'];
		  
		  $data['listadoencuesta'] = $proveedornacional->listadoproveedornacionalencuesta($id);
        $this->view->show('/proveedornacional/encuesta.phtml', $data);
    }

    public function grabaencuesta() {
        $id = $_REQUEST['idProveedorNacional'];
        $datae['idproveedornacional'] = $id;
		  $data['encuesta'] = '1';
		  var_dump($_FILES);//die();
		  
			if(isset($_FILES['pdfencuesta'])){
				$dirPdf = $_SERVER["DOCUMENT_ROOT"] . "/public/encuesta/";
				chdir($dirPdf);
				if (!file_exists($dirPdf . $id)) {
						mkdir($id);
				}
				chdir("../../");
				$dirGuardar = $_SERVER["DOCUMENT_ROOT"] . "/public/encuesta/" . $id . "/";

				// Recorremos los pdf recibidos
				foreach ($_FILES as $vPdf) {
					$name = date('Y-m-d_H-i-s').'-'.$vPdf["name"];
					// Se establece el pdf con el nombre original
					$sPdf = $dirGuardar . $name;
					// Si el pdf ya existe, no lo guardamos
					if (file_exists($sPdf)) {
						//echo "<br/> el pdf ".$vPdf["name"]." ya existe<br/>";
						unlink($sPdf);
					}
					// Copiamos de la direcci�n temporal al directorio final
					if (filesize($vPdf["tmp_name"])) {
						if (!(move_uploaded_file($vPdf["tmp_name"], $sPdf))) {
							echo "<br/>Error al escribir el pdf " . $vPdf["name"] . "<br/>";
						} else {
							//chmod($sPdf, 0666);
							$datae['encuesta'] = $name;
							$Proveedornacional = new Proveedornacional();
							$Proveedornacional->actualizaEncuestaProveedornacional($data, $id);
							$exito = $Proveedornacional->grabaencuesta($datae);
							header("Location: /proveedornacional/encuesta/".$id);
						}
					}
				}
			}
        
        // if ($exito) {
        //     $ruta['ruta'] = "/producto/lista/";
        //     $this->view->show("ruteador.phtml", $ruta);
        // }
        // $this->view->show('/proveedornacional/nuevo.phtml', $data);
    }
    
    public function nuevo() {
        $departamento = new Departamento();
        $productoservicio = new Productoservicio();
        $valuador = new Evaluador();
        $cargo = new Cargo();
        $data['Cargos'] = $cargo->listadoCargos();
        $data['Productoservicios'] = $productoservicio->listadoProductoservicio();
        $data['Evaluadores'] = $valuador->listadoEvaluadores();
        $data['Departamento'] = $departamento->listado();
        $data['TipoCliente'] = $this->tipoCliente();
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['Condiciones'] = $archivoConfig['Condicion'];
        $data['ProduccionesIT'] = $archivoConfig['InformacionTecnicaProduccion'];
        $data['DocumentoIdentidad'] = $archivoConfig['DocumentoIdentidad'];
        
        $this->view->show('/proveedornacional/nuevo.phtml', $data);
    }
    
    public function nuevocontacto_guardar() {
        $nombre = $_REQUEST['txtNuevoCargo'];
        $cargo = new Cargo();
        $idNuevoId = 0;
        if (!empty($nombre)) {
            $dataCargo = $cargo->verificarCargo($nombre);
            if (count($dataCargo) > 0) {
                $idNuevoId = $dataCargo[0]['idcargo'];
            } else {
                $dataNuevo['nombre'] = $nombre;
                $idNuevoId = $cargo->grabar($dataNuevo);
            }
        }
        $cargos = $cargo->listadoCargos();
        echo '<option value=""> -- Seleccione -- </option>';
        for ($i = 0; $i < count($cargos); $i++) {
            echo '<option value="' . $cargos[$i]['idcargo'] . '"' . ($cargos[$i]['idcargo'] == $idNuevoId ? ' selected' : '') . '>' . $cargos[$i]['nombre'] . '</option>';
        }
    }
    
    public function nuevoevaluador_guardar() {
        $nombre = $_REQUEST['txtNuevoEvaluador'];
        $evaluador = new Evaluador();
        $idNuevoId = 0;
        if (!empty($nombre)) {
            $dataEvaluador = $evaluador->verificarEvaluador($nombre);
            if (count($dataEvaluador) > 0) {
                $idNuevoId = $dataEvaluador[0]['idevaluador'];
            } else {
                $dataNuevo['nombre'] = $nombre;
                $idNuevoId = $evaluador->grabar($dataNuevo);
            }
        }
        $evaluadores = $evaluador->listadoEvaluadores();
        echo '<option value=""> -- Seleccione -- </option>';
        for ($i = 0; $i < count($evaluadores); $i++) {
            echo '<option value="' . $evaluadores[$i]['idevaluador'] . '"' . ($evaluadores[$i]['idevaluador'] == $idNuevoId ? ' selected' : '') . '>' . $evaluadores[$i]['nombre'] . '</option>';
        }
    }

    public function guardar_productoservicio() {
        $nombre = $_REQUEST['idNombrePS'];
        $productoservicio = new Productoservicio();
        $idNuevoId = 0;
        if (!empty($nombre)) {
            $dataProductoSrvicio = $productoservicio->verificarProductoservicio($nombre);
            if (count($dataProductoSrvicio) > 0) {
                $idNuevoId = $dataProductoSrvicio[0]['idproductoservicio'];
            } else {
                $dataNuevo['nombre'] = $nombre;
                $idNuevoId = $productoservicio->grabar($dataNuevo);
            }
        }
        $Productoservicios = $productoservicio->listadoProductoservicio();
        echo '<option value=""> -- Seleccione -- </option>';
        for ($i = 0; $i < count($Productoservicios); $i++) {
            echo '<option value="' . $Productoservicios[$i]['idproductoservicio'] . '"' . ($Productoservicios[$i]['idproductoservicio'] == $idNuevoId ? ' selected' : '') . '>' . $Productoservicios[$i]['nombre'] . '</option>';
        }
    }
    
    public function graba() {
        $dataProveedorNacional = $_REQUEST['ProveedorNacional'];
        $chkContingencia = $_REQUEST['chkContingencia'];
        if (!$chkContingencia) {
            $dataProveedorNacional['contingencias'] = '';
        }
        if (empty($dataProveedorNacional['dni'])) {
            $dataProveedorNacional['tipodocumento'] = '';
        } else {
            if (empty($dataProveedorNacional['tipodocumento'])) {
                $dataProveedorNacional['tipodocumento'] = 1;
            }
        }
        
        $chkDiaTP15 = $_REQUEST['chkDiaTP15'];
        $chkDiaTP30 = $_REQUEST['chkDiaTP30'];
        $chkDiaTP45 = $_REQUEST['chkDiaTP45'];
        $chkDiaTP60 = $_REQUEST['chkDiaTP60'];
        $txtDiaTPotro = $_REQUEST['txtDiaTPotro'];
        
        if ($chkDiaTP15) {
            $dataProveedorNacional['terminopago'] = '15 dias';
        } else if ($chkDiaTP30) {
            $dataProveedorNacional['terminopago'] = '30 dias';
        } else if ($chkDiaTP45) {
            $dataProveedorNacional['terminopago'] = '45 dias';
        } else if ($chkDiaTP60) {
            $dataProveedorNacional['terminopago'] = '60 dias';
        } else {
            $dataProveedorNacional['terminopago'] = $txtDiaTPotro;
        }
        
        $chkDia15TE = $_REQUEST['chkDia15TE'];
        $chkDiaTE = $_REQUEST['chkDiaTE'];
        $chkOtroTE = $_REQUEST['chkOtroTE'];
        
        if ($chkDia15TE) {
            $dataProveedorNacional['tiempoentrega'] = 'MENOR O IGUAL A 15 DIAS';
        } else if ($chkDiaTE) {
            $dataProveedorNacional['tiempoentrega'] = $chkDiaTE . ' dias';
        } else {
            $dataProveedorNacional['tiempoentrega'] = $chkOtroTE;
        }
        
        $proveedornacionalmodel = new Proveedornacional();
        $dataVerificarExistencia = $proveedornacionalmodel->verificarProveedornacional($dataProveedorNacional['razonsocial'], $dataProveedorNacional['rucdni']);
        if (count($dataVerificarExistencia) == 0) {
            if(isset($_FILES['txtficharucpdf']) && $_FILES['txtficharucpdf']['type']=='application/pdf'){
                $dataProveedorNacional['ficharuc'] = date('Ymd His') . '_' . $_FILES['txtficharucpdf']['name'];
                if (!move_uploaded_file ($_FILES['txtficharucpdf']['tmp_name'] , './public/ficharuc/' . $dataProveedorNacional['ficharuc'])) {
                    $dataProveedorNacional['ficharuc'] = '';
                }
            }
            $idnuevoproveedornacional = $proveedornacionalmodel->grabar($dataProveedorNacional);
            
            $arrayNombreDPS = $_REQUEST['txtNombreDPS'];
            $tamNPDS = count($arrayNombreDPS);
            if ($tamNPDS > 0) {
                $proveedornacionalproductoserviciomodel = new Proveedornacionalproductoservicio();
                $dataNuevoPNPS['idproveedornacional'] = $idnuevoproveedornacional;
                for ($i = 0; $i < $tamNPDS; $i++) {
                    if (!empty($arrayNombreDPS[$i])) {
                        $dataVerificarPNPS = $proveedornacionalproductoserviciomodel->verificar($idnuevoproveedornacional, $arrayNombreDPS[$i]);
                        if (count($dataVerificarPNPS == 0)) {
                            $dataNuevoPNPS['nombre'] = $arrayNombreDPS[$i];
                            $proveedornacionalproductoserviciomodel->grabar($dataNuevoPNPS);
                        }
                    }                    
                }
            }
            
            
            $arrayPrincipalesIC = $_REQUEST['txtPrincipalesIC'];
            $arrayParticipacionIC = $_REQUEST['txtParticipacionIC'];
            $arrayAntiguedadIC = $_REQUEST['txtAntiguedadIC'];
            $tamIC = count($arrayPrincipalesIC);
            if ($tamIC > 0) {
                $proveedornacionalinfcomercialmodel = new Proveedornacionalinfcomercial();
                $dataNuevoPNIC['idproveedornacional'] = $idnuevoproveedornacional;
                for ($i = 0; $i < $tamIC; $i++) {
                    if (!empty($arrayPrincipalesIC[$i])) {
                        $dataVerificarPNIC = $proveedornacionalinfcomercialmodel->verificar($idnuevoproveedornacional, $arrayPrincipalesIC[$i], $arrayParticipacionIC[$i], $arrayAntiguedadIC[$i]);
                        if (count($dataVerificarPNIC == 0)) {
                            $dataNuevoPNIC['cliente'] = $arrayPrincipalesIC[$i];
                            $dataNuevoPNIC['participacion'] = $arrayParticipacionIC[$i];
                            $dataNuevoPNIC['antiguedad'] = $arrayAntiguedadIC[$i];
                            $proveedornacionalinfcomercialmodel->grabar($dataNuevoPNIC);
                        }
                    }
                }
            }
        
            
            $arrayCertificadoIT = $_REQUEST['txtCertificadoIT'];
            $arrayAprobacionIT = $_REQUEST['txtAprobacionIT'];
            $arrayFechaIT = $_REQUEST['txtFechaIT'];
            $arrayFechaUltimaIT = $_REQUEST['txtFechaUltimaIT'];
            $tamIT = count($arrayCertificadoIT);
            if ($tamIT > 0) {
                $proveedornacionalinftecnicamodel = new Proveedornacionalinftecnica();
                $dataNuevoPNIT['idproveedornacional'] = $idnuevoproveedornacional;
                for ($i = 0; $i < $tamIT; $i++) {
                    if (!empty($arrayCertificadoIT[$i])) {
                        $dataVerificarPNIT = $proveedornacionalinftecnicamodel->verificar($idnuevoproveedornacional, $arrayCertificadoIT[$i], $arrayAprobacionIT[$i]);
                        if (count($dataVerificarPNIT == 0)) {
                            $dataNuevoPNIT['certificado'] = $arrayCertificadoIT[$i];
                            $dataNuevoPNIT['aprobacionnro'] = $arrayAprobacionIT[$i];
                            $dataNuevoPNIT['fecha'] = $arrayFechaIT[$i];
                            $dataNuevoPNIT['fultimaauditoria'] = $arrayFechaUltimaIT[$i];
                            $proveedornacionalinftecnicamodel->grabar($dataNuevoPNIT);
                        }
                    }
                }
            }
            
            $arrayProductoET = $_REQUEST['txtProductoET'];        
            $arrayEvaluadorET = $_REQUEST['txtEvaluadorET'];
            $arrayCondicionET = $_REQUEST['txtCondicionET'];
            $arrayFechaET = $_REQUEST['txtFechaET'];
            $arrayComentariosET = $_REQUEST['txtComentariosET'];
            $tamET = count($arrayProductoET);
            if ($tamET > 0) {
                $proveedornacionalevaltecnicamodel = new Proveedornacionalevaltecnica();
                $dataNuevoPNET['idproveedornacional'] = $idnuevoproveedornacional;
                for ($i = 0; $i < $tamET; $i++) {
                    if (!empty($arrayProductoET[$i])) {
                        $arrayFechaET[$i] = (!empty($arrayFechaET[$i]) ? $arrayFechaET[$i] : '0000-00-00');
                        $dataVerificarPNET = $proveedornacionalevaltecnicamodel->verificar($idnuevoproveedornacional, $arrayProductoET[$i], $arrayEvaluadorET[$i], $arrayCondicionET[$i], $arrayFechaET[$i]);
                        if (count($dataVerificarPNET == 0)) {
                            $dataNuevoPNET['productoservicio'] = $arrayProductoET[$i];
                            $dataNuevoPNET['idevaluador'] = $arrayEvaluadorET[$i];
                            $dataNuevoPNET['condicion'] = $arrayCondicionET[$i];
                            $dataNuevoPNET['fecha'] = $arrayFechaET[$i];
                            $dataNuevoPNET['comentarios'] = $arrayComentariosET[$i];
                            $proveedornacionalevaltecnicamodel->grabar($dataNuevoPNET);
                        }
                    }
                }
            }
            
            $arrayNombresC = $_REQUEST['txtNombresC'];
            $arrayCargosC = $_REQUEST['txtCargosC'];
            $arrayTelefonosC = $_REQUEST['txtTelefonosC'];
            $arrayCorreosC = $_REQUEST['txtCorreosC'];
            $tamC = count($arrayNombresC);
            if ($tamC > 0) {
                $proveedornacionalcontactomodel = new Proveedornacionalcontacto();
                $dataNuevoPNC['idproveedornacional'] = $idnuevoproveedornacional;
                for ($i = 0; $i < $tamC; $i++) {
                    if (!empty($arrayNombresC[$i])) {
                        $dataVerificarPNC = $proveedornacionalcontactomodel->verificar($idnuevoproveedornacional, $arrayNombresC[$i], $arrayCargosC[$i]);
                        if (count($dataVerificarPNC == 0)) {
                            $dataNuevoPNC['nombre'] = $arrayNombresC[$i];
                            $dataNuevoPNC['idcargo'] = $arrayCargosC[$i];
                            $dataNuevoPNC['telefono'] = $arrayTelefonosC[$i];
                            $dataNuevoPNC['correo'] = $arrayCorreosC[$i];
                            $proveedornacionalcontactomodel->grabar($dataNuevoPNC);
                        }
                    }
                }
            }
        }
        $ruta['ruta'] = "/proveedornacional/listado";
        $this->view->show("ruteador.phtml", $ruta);
    }
    
    public function editar() {
        $id = $_REQUEST['id'];
        if ($id > 0) {
            $productoservicio = new Productoservicio();
            $proveedornacional = new Proveedornacional();
            $distrito = new Distrito();
            $provincia = new Provincia();
            $departamento = new Departamento();
            $dataProveedorNacional = $proveedornacional->buscaProveedorNacional($id);
            if (count($dataProveedorNacional) > 0) {
                $data['Productoservicios'] = $productoservicio->listadoProductoservicio();

                $proveedornacionalinfcomercialmodel = new Proveedornacionalinfcomercial();
                $data['informacioncomercial'] = $proveedornacionalinfcomercialmodel->listadoxproveedornacional($id);
                $proveedornacionalinftecnicamodel = new Proveedornacionalinftecnica();
                $data['informicontecnica'] = $proveedornacionalinftecnicamodel->listadoxproveedornacional($id);
                $proveedornacionalevaltecnicamodel = new Proveedornacionalevaltecnica();
                $data['evaluaciontecnica'] = $proveedornacionalevaltecnicamodel->listadoxproveedornacional($id);
                $proveedornacionalcontactomodel = new Proveedornacionalcontacto();
                $data['contactos'] = $proveedornacionalcontactomodel->listadoxproveedornacional($id);

                $proveedornacionalproductoserviciomodel = new Proveedornacionalproductoservicio();
                $data['productoservicios'] = $proveedornacionalproductoserviciomodel->listadoxproveedornacional($id);

                $data['Departamento'] = $departamento->listado();
                if ($dataProveedorNacional[0]['iddistrito'] > 0) {
                    $dataDistrito = $distrito->buscarxid($dataProveedorNacional[0]['iddistrito']);
                    $data['Provincia'] = $provincia->listado($dataDistrito[0]['codigodepto']);
                    $data['Distrito'] = $distrito->listado($dataDistrito[0]['idprovincia']);
                }

                $data['ProveedorNacional'] = $dataProveedorNacional;
                $data['TipoProveedor'] = $this->tipoCliente();
                $archivoConfig = parse_ini_file("config.ini", true);

                $valuador = new Evaluador();
                $data['Evaluadores'] = $valuador->listadoEvaluadores();

                $cargo = new Cargo();
                $data['Cargos'] = $cargo->listadoCargos();

                $data['Condiciones'] = $archivoConfig['Condicion'];
                $data['ProduccionesIT'] = $archivoConfig['InformacionTecnicaProduccion'];
                $data['DocumentoIdentidad'] = $archivoConfig['DocumentoIdentidad'];
                $this->view->show("proveedornacional/editar.phtml", $data);
            } else {
                $ruta['ruta'] = "/proveedornacional/listado";
                $this->view->show("ruteador.phtml", $ruta);
            }
        } else {
            $ruta['ruta'] = "/proveedornacional/listado";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function contacto_guardar() {
        $idNombreC = $_REQUEST['idNombreC'];
        $idCargoC = $_REQUEST['idCargoC'];
        $idTelefonoC = $_REQUEST['idTelefonoC'];
        $idCorreoC = $_REQUEST['idCorreoC'];
        $idProveedorNacional = $_REQUEST['idProveedorNacional'];    
        if ($idProveedorNacional > 0 && !empty($idNombreC)) {
            $idTextIdC = ($_REQUEST['idTextIdC'] > 0 ? $_REQUEST['idTextIdC'] : '');            
            $proveedornacionalcontactomodel = new Proveedornacionalcontacto();
            $datacontacto = $proveedornacionalcontactomodel->verificar($idProveedorNacional, $idNombreC, $idCargoC, $idTextIdC);
            if (count($datacontacto) == 0) {
                $dataNuevo['nombre'] = $idNombreC;
                $dataNuevo['idcargo'] = $idCargoC;
                $dataNuevo['telefono'] = $idTelefonoC;
                $dataNuevo['correo'] = $idCorreoC;
                if (!empty($idTextIdC)) {
                    $proveedornacionalcontactomodel->actualiza($dataNuevo, $idTextIdC);
                } else {
                    $dataNuevo['idproveedornacional'] = $idProveedorNacional;
                    $proveedornacionalcontactomodel->grabar($dataNuevo);
                }
            }
            $contactos = $proveedornacionalcontactomodel->listadoxproveedornacional($idProveedorNacional);
            for ($i = 0; $i < count($contactos); $i++) {
                echo '<tr>' .
                        '<td>' . $contactos[$i]['nombre'] . '</td>' .
                        '<td>' . $contactos[$i]['nombrecargo'] . '</td>' .
                        '<td>' . $contactos[$i]['telefono'] . '</td>' .
                        '<td>' . $contactos[$i]['correo'] . '</td>' .
                        '<td>' . 
                            '<a href="#" class="btnEditarC" data-correo="' . $contactos[$i]['correo'] . '" data-telefono="' . $contactos[$i]['telefono'] . '" data-idcargo="' . $contactos[$i]['idcargo'] . '" data-nombre="' . $contactos[$i]['nombre'] . '" data-id="' . $contactos[$i]['idproveedornacionalcontacto'] . '"><img src="/imagenes/editar.gif"></a> ' . 
                            '<a href="#" class="btnEliminarC" data-id="' . $contactos[$i]['idproveedornacionalcontacto'] . '"><img src="/imagenes/eliminar.gif"></a>' . 
                        '</td>' .
                     '</tr>';
            }
        }
    }
    
    function evaluaciontecnica_guardar() {
        $idProductoET = $_REQUEST['idProductoET'];
        $idEvaluadorET = $_REQUEST['idEvaluadorET'];
        $idCondicionET = $_REQUEST['idCondicionET'];
        $idFechaET = (!empty($_REQUEST['idFechaET']) ? $_REQUEST['idFechaET'] : '0000-00-00');
        $idComentariosET = $_REQUEST['idComentariosET'];
        $idProveedorNacional = $_REQUEST['idProveedorNacional'];    
        if ($idProveedorNacional > 0 && !empty($idProductoET)) {
            $idTextIdET = ($_REQUEST['idTextIdET'] > 0 ? $_REQUEST['idTextIdET'] : '');            
            $proveedornacionalevaltecnicamodel = new Proveedornacionalevaltecnica();
            $dataEvaluacionTecnica = $proveedornacionalevaltecnicamodel->verificar($idProveedorNacional, $idProductoET, $idEvaluadorET, $idCondicionET, $idFechaET, $idTextIdET);
            if (count($dataEvaluacionTecnica) == 0) {
                $dataNuevo['productoservicio'] = $idProductoET;
                $dataNuevo['idevaluador'] = $idEvaluadorET;
                $dataNuevo['condicion'] = $idCondicionET;
                $dataNuevo['fecha'] = $idFechaET;
                $dataNuevo['comentarios'] = $idComentariosET;
                if (!empty($idTextIdET)) {
                    $proveedornacionalevaltecnicamodel->actualiza($dataNuevo, $idTextIdET);
                } else {
                    $dataNuevo['idproveedornacional'] = $idProveedorNacional;
                    $proveedornacionalevaltecnicamodel->grabar($dataNuevo);
                }
            }
            $archivoConfig = parse_ini_file("config.ini", true);
            $Condiciones = $archivoConfig['Condicion'];
            $evaluaciontecnica = $proveedornacionalevaltecnicamodel->listadoxproveedornacional($idProveedorNacional);
            for ($i = 0; $i < count($evaluaciontecnica); $i++) {
                $evaluaciontecnica[$i]['fecha'] = ($evaluaciontecnica[$i]['fecha'] == '0000-00-00' ? '' : $evaluaciontecnica[$i]['fecha']);
                echo '<tr>' .
                        '<td class="classProductoET">' . $evaluaciontecnica[$i]['productoservicio'] . '</td>' .
                        '<td class="classEvaluadorET">' . $evaluaciontecnica[$i]['nombreevaluador'] . '</td>' .
                        '<td class="classCondicionET">' . $Condiciones[$evaluaciontecnica[$i]['condicion']] . '</td>' .
                        '<td class="classFechaET">' . $evaluaciontecnica[$i]['fecha'] . '</td>' .
                        '<td class="classComentariosET">' . $evaluaciontecnica[$i]['comentarios'] . '</td>' .
                        '<td>' .
                            '<a href="#" class="btnEditarET" data-comentarios="' . $evaluaciontecnica[$i]['comentarios'] . '" data-productoservicio="' . $evaluaciontecnica[$i]['productoservicio'] . '" data-idevaluador="' . $evaluaciontecnica[$i]['idevaluador'] . '" data-condicion="' . $evaluaciontecnica[$i]['condicion'] . '" data-fecha="' . $evaluaciontecnica[$i]['fecha'] . '" data-id="' . $evaluaciontecnica[$i]['idproveedornacionaevaltecnica'] . '"><img src="/imagenes/editar.gif"></a> ' . 
                            '<a href="#" class="btnEliminarET" data-id="' . $evaluaciontecnica[$i]['idproveedornacionaevaltecnica'] . '"><img src="/imagenes/eliminar.gif"></a>' . 
                        '</td>' .
                   '</tr>';
            }
        }
    }
    
    function informaciontecnica_guardar() {
        $idCertificadoIT = $_REQUEST['idCertificadoIT'];
        $idAprobacionIT = $_REQUEST['idAprobacionIT'];
        $idFechaIT = $_REQUEST['idFechaIT'];
        $idFechaUltimaIT = $_REQUEST['idFechaUltimaIT'];
        $idProveedorNacional = $_REQUEST['idProveedorNacional'];    
        if ($idProveedorNacional > 0 && !empty($idCertificadoIT)) {
            $idTextIdIC = ($_REQUEST['idTextIdIT'] > 0 ? $_REQUEST['idTextIdIT'] : '');            
            $proveedornacionalinftecnicamodel = new Proveedornacionalinftecnica();
            $dataInformaciontecnica = $proveedornacionalinftecnicamodel->verificar($idProveedorNacional, $idCertificadoIT, $idAprobacionIT, $idTextIdIC);
            if (count($dataInformaciontecnica) == 0) {
                $dataNuevo['certificado'] = $idCertificadoIT;
                $dataNuevo['aprobacionnro'] = $idAprobacionIT;
                $dataNuevo['fecha'] = $idFechaIT;
                $dataNuevo['fultimaauditoria'] = $idFechaUltimaIT;
                if (!empty($idTextIdIC)) {
                    $proveedornacionalinftecnicamodel->actualiza($dataNuevo, $idTextIdIC);
                } else {
                    $dataNuevo['idproveedornacional'] = $idProveedorNacional;
                    $proveedornacionalinftecnicamodel->grabar($dataNuevo);
                }
            }
            $informicontecnica = $proveedornacionalinftecnicamodel->listadoxproveedornacional($idProveedorNacional);
            $tamanioIT = count($informicontecnica);
            for ($i = 0; $i < $tamanioIT; $i++) {
                if ($informicontecnica[$i]['fecha'] == '0000-00-00') {
                    $informicontecnica[$i]['fecha'] = '';
                } else {
                    $informicontecnica[$i]['fecha'] = str_replace("-", "/", $informicontecnica[$i]['fecha']);
                }
                if ($informicontecnica[$i]['fultimaauditoria'] == '0000-00-00') {
                    $informicontecnica[$i]['fultimaauditoria'] = '';
                } else {
                    $informicontecnica[$i]['fultimaauditoria'] = str_replace("-", "/", $informicontecnica[$i]['fultimaauditoria']);
                }
                echo '<tr>' .
                        '<td>' . $informicontecnica[$i]['certificado'] . '</td>' .
                        '<td>' . $informicontecnica[$i]['aprobacionnro'] . '</td>' .
                        '<td>' . $informicontecnica[$i]['fecha'] . '</td>' .
                        '<td>' . $informicontecnica[$i]['fultimaauditoria'] . '</td>' .
                        '<td>' .
                            '<a href="#" class="btnEeditarIT" data-certificado="' . $informicontecnica[$i]['certificado'] . '" data-aprobacionnro="' . $informicontecnica[$i]['aprobacionnro'] . '" data-fecha="' . $informicontecnica[$i]['fecha'] . '" data-fultimaauditoria="' . $informicontecnica[$i]['fultimaauditoria'] . '" data-id="' . $informicontecnica[$i]['idproveedornacionalinftecnica'] . '"><img src="/imagenes/editar.gif"></a> ' . 
                            '<a href="#" class="btnEliminarIT" data-id="' . $informicontecnica[$i]['idproveedornacionalinftecnica'] . '"><img src="/imagenes/eliminar.gif"></a>' . 
                   '</tr>';
            }
            if ($tamanioIT == 0) {
                echo '<tr>
                        <td colspan="5">NO PRESENTA</td>
                    </tr>';
            }
        }
    }
    
    function informacioncomercial_guardar() {
        $idPrincipalIC = $_REQUEST['idPrincipalIC'];
        $idParticipacionIC = $_REQUEST['idParticipacionIC'];
        $idAntiguedadIC = $_REQUEST['idAntiguedadIC'];
        $idProveedorNacional = $_REQUEST['idProveedorNacional'];    
        if ($idProveedorNacional > 0 && !empty($idPrincipalIC)) {
            $idTextIdIC = ($_REQUEST['idTextIdIC'] > 0 ? $_REQUEST['idTextIdIC'] : '');            
            $proveedornacionalinfcomercialmodel = new Proveedornacionalinfcomercial();
            $dataInformacioncomercial = $proveedornacionalinfcomercialmodel->verificar($idProveedorNacional, $idPrincipalIC, $idParticipacionIC, $idAntiguedadIC, $idTextIdIC);
            if (count($dataInformacioncomercial) == 0) {
                $dataNuevo['cliente'] = $idPrincipalIC;
                $dataNuevo['participacion'] = $idParticipacionIC;
                $dataNuevo['antiguedad'] = $idAntiguedadIC;
                if (!empty($idTextIdIC)) {
                    $proveedornacionalinfcomercialmodel->actualiza($dataNuevo, $idTextIdIC);
                } else {
                    $dataNuevo['idproveedornacional'] = $idProveedorNacional;
                    $proveedornacionalinfcomercialmodel->grabar($dataNuevo);
                }
            }
            $informacioncomercial = $proveedornacionalinfcomercialmodel->listadoxproveedornacional($idProveedorNacional);
            for ($i = 0; $i < count($informacioncomercial); $i++) {
                $participacion = (empty($informacioncomercial[$i]['participacion']) ? '' : $informacioncomercial[$i]['participacion']);
                echo '<tr>' .
                        '<td>' . $informacioncomercial[$i]['cliente'] . '</td>' .
                        '<td>' . $participacion . '</td>' .
                        '<td>' . $informacioncomercial[$i]['antiguedad'] . '</td>' .
                        '<td>' .
                             '<a href="#" class="btnEeditarIC" data-cliente="' . $informacioncomercial[$i]['cliente'] . '" data-participacion="' . $participacion . '" data-antiguedad="' . $informacioncomercial[$i]['antiguedad'] . '" data-id="' . $informacioncomercial[$i]['idproveedornacionalinfcomercial'] . '"><img src="/imagenes/editar.gif"></a> ' . 
                            '<a href="#" class="btnEliminarIC" data-id="' . $informacioncomercial[$i]['idproveedornacionalinfcomercial'] . '"><img src="/imagenes/eliminar.gif"></a>' . 
                   '</tr>';
            }
        }
    }
            
    function productoservicio_guardar() {
        $idProveedorNacional = $_REQUEST['idProveedorNacional'];
        $idNombreDetallePS = $_REQUEST['idNombreDetallePS'];
        if ($idProveedorNacional > 0 && !empty($idNombreDetallePS)) {
            $idTextIdPS = ($_REQUEST['idTextIdPS'] > 0 ? $_REQUEST['idTextIdPS'] : '');            
            $proveedornacionalproductoserviciomodel = new Proveedornacionalproductoservicio();
            $dataProductoservicios = $proveedornacionalproductoserviciomodel->verificar($idProveedorNacional, $idNombreDetallePS, $idTextIdPS);
            if (count($dataProductoservicios) == 0) {
                $dataNuevo['nombre'] = $idNombreDetallePS;
                if (!empty($idTextIdPS)) {
                    $proveedornacionalproductoserviciomodel->actualiza($dataNuevo, $idTextIdPS);
                } else {
                    $dataNuevo['idproveedornacional'] = $idProveedorNacional;
                    $proveedornacionalproductoserviciomodel->grabar($dataNuevo);
                }
            }
            $dataProductoservicios = $proveedornacionalproductoserviciomodel->listadoxproveedornacional($idProveedorNacional);
            for ($i = 0; $i < count($dataProductoservicios); $i++) {
                echo '<tr>' .
                        '<td>' . $dataProductoservicios[$i]['nombre'] . '</td>' .
                        '<td>' . 
                            '<a href="#" class="btnEditarDPS" data-nombre="' . $dataProductoservicios[$i]['nombre'] . '" data-id="' . $dataProductoservicios[$i]['idproveedornacionalproductoservicio'] . '"><img src="/imagenes/editar.gif"></a> ' . 
                            '<a href="#" class="btnEliminarDPS" data-id="' . $dataProductoservicios[$i]['idproveedornacionalproductoservicio'] . '"><img src="/imagenes/eliminar.gif"></a>' . 
                        '</td>' .
                     '</tr>';
            }
        }
    }
    
    function informaciontecnica_eliminar() {
        $id = $_REQUEST['ideliminar'];
        $proveedornacionalinftecnicamodel = new Proveedornacionalinftecnica();
        $dataAct['estado'] = 0;
        $proveedornacionalinftecnicamodel->actualiza($dataAct, $id);
    }
    
    function contacto_eliminar() {
        $id = $_REQUEST['ideliminar'];
        $proveedornacionalcontactomodel = new Proveedornacionalcontacto();
        $dataAct['estado'] = 0;
        $proveedornacionalcontactomodel->actualiza($dataAct, $id);
    }
    
    function evaluaciontecnica_eliminar() {
        $id = $_REQUEST['ideliminar'];
        $proveedornacionalevaltecnicamodel = new Proveedornacionalevaltecnica();
        $dataAct['estado'] = 0;
        $proveedornacionalevaltecnicamodel->actualiza($dataAct, $id);
    }
    
    function informacioncomercial_eliminar() {
        $id = $_REQUEST['ideliminar'];
        $proveedornacionalinfcomercialmodel = new Proveedornacionalinfcomercial();
        $dataAct['estado'] = 0;
        $proveedornacionalinfcomercialmodel->actualiza($dataAct, $id);
    }
    
    function productoservicio_eliminar() {
        $id = $_REQUEST['ideliminar'];
        $proveedornacionalproductoserviciomodel = new Proveedornacionalproductoservicio();
        $dataAct['estado'] = 0;
        $proveedornacionalproductoserviciomodel->actualiza($dataAct, $id);
    }
            
    function eliminar() {
        $id = $_REQUEST['id'];
        $proveedornacional = new Proveedornacional();
        $estado = $proveedornacional->cambiaEstadoProveedorNacional($id);
        if ($estado) {
            $ruta['ruta'] = "/proveedornacional/listado";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    public function listado() {
        $proveedornacional = new Proveedornacional();        
        $archivoConfig = parse_ini_file("config.ini", true);
        $data['ProduccionesIT'] = $archivoConfig['InformacionTecnicaProduccion'];
        $data['EstadoSPN'] = $archivoConfig['EstadoPN'];
        $data['proveedornacional'] = $proveedornacional->listaProveedoresNacionalPaginado($_REQUEST['id']);
        $data['paginacion'] = $proveedornacional->paginadoProveedoresNacional();
        $this->view->show('/proveedornacional/listado.phtml', $data);
    }
    
    function busqueda() {
        $texto = $_REQUEST['txtBusqueda'];
        $proveedornacional = new Proveedornacional();
        $data['proveedornacional'] = $proveedornacional->buscarxnombre(0, 10, $texto);
        $data['texto'] = $texto;
        $this->view->show("proveedornacional/listado.phtml", $data);
    }
    
    function actualiza() {
        $idProveedorNacional = $_REQUEST['idProveedorNacional'];
        if ($idProveedorNacional > 0) {
            $dataProveedorNacional = $_REQUEST['ProveedorNacional'];
            $chkContingencia = $_REQUEST['chkContingencia'];
            if (!$chkContingencia) {
                $dataProveedorNacional['contingencias'] = '';
            }
            if (empty($dataProveedorNacional['dni'])) {
                $dataProveedorNacional['tipodocumento'] = '';
            } else {
                if (empty($dataProveedorNacional['tipodocumento'])) {
                    $dataProveedorNacional['tipodocumento'] = 1;
                }
            }

            $chkDiaTP15 = $_REQUEST['chkDiaTP15'];
            $chkDiaTP30 = $_REQUEST['chkDiaTP30'];
            $chkDiaTP45 = $_REQUEST['chkDiaTP45'];
            $chkDiaTP60 = $_REQUEST['chkDiaTP60'];
            $txtDiaTPotro = $_REQUEST['txtDiaTPotro'];

            if ($chkDiaTP15) {
                $dataProveedorNacional['terminopago'] = '15 dias';
            } else if ($chkDiaTP30) {
                $dataProveedorNacional['terminopago'] = '30 dias';
            } else if ($chkDiaTP45) {
                $dataProveedorNacional['terminopago'] = '45 dias';
            } else if ($chkDiaTP60) {
                $dataProveedorNacional['terminopago'] = '60 dias';
            } else {
                $dataProveedorNacional['terminopago'] = $txtDiaTPotro;
            }

            $chkDia15TE = $_REQUEST['chkDia15TE'];
            $chkDiaTE = $_REQUEST['chkDiaTE'];
            $chkOtroTE = $_REQUEST['chkOtroTE'];

            if ($chkDia15TE) {
                $dataProveedorNacional['tiempoentrega'] = 'MENOR O IGUAL A 15 DIAS';
            } else if ($chkDiaTE) {
                $dataProveedorNacional['tiempoentrega'] = $chkDiaTE . ' dias';
            } else {
                $dataProveedorNacional['tiempoentrega'] = $chkOtroTE;
            }

            $proveedornacionalmodel = new Proveedornacional();
            $dataVerificarExistencia = $proveedornacionalmodel->verificarProveedornacional($dataProveedorNacional['razonsocial'], $dataProveedorNacional['rucdni'], $idProveedorNacional);
            if (count($dataVerificarExistencia) == 0) {
                if(isset($_FILES['txtficharucpdf']) && $_FILES['txtficharucpdf']['type']=='application/pdf'){
                    $dataProveedorNacional['ficharuc'] = date('Ymd His') . '_' . $_FILES['txtficharucpdf']['name'];
                    if (!move_uploaded_file ($_FILES['txtficharucpdf']['tmp_name'] , './public/ficharuc/' . $dataProveedorNacional['ficharuc'])) {
                        $dataProveedorNacional['ficharuc'] = '';
                    }
                }
                $proveedornacionalmodel->actualiza($dataProveedorNacional, $idProveedorNacional);
            }
        }
        $ruta['ruta'] = "/proveedornacional/listado";
        $this->view->show("ruteador.phtml", $ruta);
    }
        
    function ficharuc_eliminar() {
        $id = $_REQUEST['idProveedorNacional'];
        $proveedornacional = new Proveedornacional();
        $dataAct['ficharuc'] = '';
        $proveedornacional->actualiza($dataAct, $id);
    }
    
    function ficharuc() {
        $fichatecnica = "public/ficharuc/" . $_REQUEST['id'];
        if (file_exists($fichatecnica)) {
            header('Content-type: application/pdf');
            header('Content-Disposition: inline; filename="' . $fichatecnica . '"');
            readfile($fichatecnica);
        } else {
            echo "<script languaje='javascript' type='text/javascript'>window.close();</script>";
        }
    }
    
    function proveedornacional_situacion() {
        $situacion = $_REQUEST['situacion'];
        $idproveedornacional = $_REQUEST['idproveedornacional'];
        $proveedornacional = new Proveedornacional();
        $dataAct['situacion'] = $situacion;
        $proveedornacional->actualiza($dataAct, $idproveedornacional);
    }
    
}

?>