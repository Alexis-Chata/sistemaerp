<?php

class documentocontroller extends ApplicationGeneral {

    function listaDocumentos() {
        $model = $this->AutoLoadModel('documento');
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        session_start();
        $_SESSION['P_Documento'] = "";
        $Factura = $model->listaDocumentosPaginado("", "", $pagina);
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoDocumentos("", "");
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show('/documento/listaDocumentos.phtml', $data);
    }

    function buscaDocumentos() {
        $model = $this->AutoLoadModel('documento');
        $pagina = $_REQUEST['id'];
        if (empty($_REQUEST['id'])) {
            $pagina = 1;
        }
        session_start();
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_Documento'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_Documento'];
        $Factura = $model->listaDocumentosPaginado("", "", $pagina, $parametro);
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoDocumentos("", "", $parametro);
        $data['retorno'] = $parametro;
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = $model->cuentaDocumentos("", "", $parametro);
        $this->view->show('/documento/buscaDocumentos.phtml', $data);
    }

    function editarDocumento() {
        $id = $_REQUEST['id'];
        $cont = 0;
        $documento = $this->AutoLoadModel('documento');
        $datadoc = $documento->buscaDocumento($id, "");
        $filtro = "iddocumento !='" . $datadoc[0]['iddocumento'] . "' and idordenventa='" . $datadoc[0]['idordenventa'] . "'";
        $databusqueda = $documento->buscaDocumento("", $filtro);
        $datatipo = $this->tipoDocumento();
        $dataNuevo = array();
        foreach ($datatipo as $key => $datos) {
            $cont++;
            if (count($databusqueda) == 0) {
                $dataNuevo = $datatipo;
            } else {
                for ($x = 0; $x < count($databusqueda); $x++) {
                    if ($key != $databusqueda[$x]['nombredoc']) {
                        $dataNuevo[$cont] = $datos;
                    } else {
                        $dataNuevo[$cont] = "";
                    }
                }
            }
        }
        $data['ModoFacturacion'] = $this->modoFacturacion();
        $data['documento'] = $datadoc;
        $data['tipodocumento'] = $dataNuevo;
        $this->view->show('/documento/editarDocumento.phtml', $data);
    }

    function actualizaDocumento() {
        $documento = $this->AutoLoadModel('documento');
        $orden = $this->AutoLoadModel('ordenventa');
        $data = $_REQUEST['documento'];
        $databusqueda = $documento->buscaDocumento($data['iddocumento'], "");
        $idordenventa = $databusqueda[0]['idordenventa'];
        if ($data['nombredoc'] == 1) {
            $dataorden['esfacturado'] = 1;
            $exitoN = $orden->actualizaOrdenVenta($dataorden, $idordenventa);
        } elseif ($databusqueda[0]['nombredoc'] == 1) {
            $dataorden['esfacturado'] = 0;
            $exitoN = $orden->actualizaOrdenVenta($dataorden, $idordenventa);
        }
        $data['esimpreso'] = 0;
        $filtro = "iddocumento='" . $data['iddocumento'] . "'";
        $exito = $documento->actualizarDocumento($data, $filtro);
        if ($exito) {
            $ruta['ruta'] = '/documento/listaDocumentos/';
            $this->view->show('ruteador.phtml', $ruta);
        } else {
            $ruta['ruta'] = '/documento/actualizaDocumento/' . $data['iddocumento'];
            $this->view->show('ruteador.phtml', $ruta);
        }
    }

    function imprimirDocumento() {
        $documento = $this->AutoLoadModel('documento');
        $id = $_REQUEST['id'];
        $datos = $documento->buscaDocumento($id, "");
        $tipodocumento = $datos[0]['nombredoc'];
        $data['tipodocumento'] = $this->tipoDocumento();
        if ($tipodocumento == 1) {
            $datos[0]['title'] = 'Numero Guia de Remision';
            $datos[0]['action'] = '/reporte/generaFactura/' . $id;
            $datos[0]['tipo'] = $tipodocumento;
        } elseif ($tipodocumento == 2) {
            $datos[0]['title'] = 'Numero Guia de Remision';
            $datos[0]['action'] = '/pdf/generaBoleta/';
            $datos[0]['tipo'] = $tipodocumento;
        } elseif ($tipodocumento == 3) {
            $datos[0]['title'] = '';
            $datos[0]['action'] = '';
            $datos[0]['tipo'] = $tipodocumento;
        } elseif ($tipodocumento == 4) {
            $datos[0]['title'] = 'Numero Documento';
            $datos[0]['action'] = '/reporte/generaGuia/' . $id;
            $datos[0]['tipo'] = $tipodocumento;
        } elseif ($tipodocumento == 5) {
            $datos[0]['title'] = '';
            $datos[0]['action'] = '';
            $datos[0]['tipo'] = $tipodocumento;
        } elseif ($tipodocumento == 6) {
            $datos[0]['title'] = '';
            $datos[0]['action'] = '';
            $datos[0]['tipo'] = $tipodocumento;
        } elseif ($tipodocumento == 7) {
            $datos[0]['title'] = '';
            $datos[0]['action'] = '';
            $datos[0]['tipo'] = $tipodocumento;
        }
        $data['documento'] = $datos;
        $this->view->show('/documento/imprimir.phtml', $data);
    }

    function documentosxordenventa() {
        $documento = $this->AutoLoadModel('documento');
        $idordenventa = $_REQUEST['id'];

        $filtro = " nombredoc=1 ";
        $datadocumento = $documento->buscadocumentoxordenventa($idordenventa, $filtro);
        $cantidaddata = count($datadocumento);

        $filtro2 = " nombredoc=2 ";
        $datadocumento2 = $documento->buscadocumentoxordenventa($idordenventa, $filtro2);
        $cantidaddata2 = count($datadocumento2);

        $filtro3 = " nombredoc=4 ";
        $datadocumento3 = $documento->buscadocumentoxordenventa($idordenventa, $filtro3);
        $cantidaddata3 = count($datadocumento3);

        $filtro5 = " nombredoc=5 ";
        $datadocumento5 = $documento->buscadocumentoxordenventa($idordenventa, $filtro5);
        $cantidaddata5 = count($datadocumento5);

        $filtro6 = " nombredoc=6 ";
        $datadocumento6 = $documento->buscadocumentoxordenventa($idordenventa, $filtro6);
        $cantidaddata6 = count($datadocumento6);

        echo "<tr><th colspan=5><h3>DETALLE DE DOCUMENTOS ASOCIADOS AL PEDIDO: </h3></th></tr>";
        for ($i = 0; $i < $cantidaddata; $i++) {
            echo "<tr>";
            echo "<th>N° Factura </th>";
            $serie = str_pad($datadocumento[$i]['serie'], 3, '0', STR_PAD_LEFT);
            $ultimo = $datadocumento[$i]['numdoc'] + $datadocumento[$i]['CantidadDocumentos'] - 1;
            echo "<td>" . $serie . '-' . $datadocumento[$i]['numdoc'] . ($datadocumento[$i]['CantidadDocumentos'] > 0 ? (" al " . $serie . '-' . $ultimo) : "") . "</td>";
            echo "<th>Estado </th>";
            echo "<td>" . ($datadocumento[$i]['esAnulado'] == 1 ? 'Anulado' : 'Activo') . "</td>";
            echo "<td>" . ($datadocumento[$i]['esImpreso'] == 1 ? 'Impreso' : 'No Impreso') . "</td>";
            echo "</tr>";
        }

        for ($x = 0; $x < $cantidaddata2; $x++) {
            echo "<tr>";
            echo "<th>N° Boleta </th>";
            $serie = str_pad($datadocumento2[$x]['serie'], 3, '0', STR_PAD_LEFT);
            $ultimo = $datadocumento2[$x]['numdoc'] + $datadocumento2[$x]['CantidadDocumentos'] - 1;
            echo "<td>" . $serie . '-' . $datadocumento2[$x]['numdoc'] . ($datadocumento2[$x]['CantidadDocumentos'] > 0 ? (" al " . $serie . '-' . $ultimo) : "") . "</td>";
            echo "<th>Estado </th>";
            echo "<td>" . ($datadocumento2[$x]['esAnulado'] == 1 ? 'Anulado' : 'Activo') . "</td>";
            echo "<td>" . ($datadocumento2[$x]['esImpreso'] == 1 ? 'Impreso' : 'No Impreso') . "</td>";
            echo "</tr>";
        }

        for ($y = 0; $y < $cantidaddata3; $y++) {
            echo "<tr>";
            echo "<th>N° Guia Remision </th>";
            $serie = str_pad($datadocumento3[$y]['serie'], 3, '0', STR_PAD_LEFT);
            $ultimo = $datadocumento3[$y]['numdoc'] + $datadocumento3[$y]['CantidadDocumentos'] - 1;
            echo "<td>" . $serie . '-' . $datadocumento3[$y]['numdoc'] . ($datadocumento3[$y]['CantidadDocumentos'] > 0 ? (" al " . $serie . '-' . $ultimo) : "") . "</td>";
            echo "<th>Estado </th>";
            echo "<td>" . ($datadocumento3[$y]['esAnulado'] == 1 ? 'Anulado' : 'Activo') . "</td>";
            echo "<td>" . ($datadocumento3[$y]['esImpreso'] == 1 ? 'Impreso' : 'No Impreso') . "</td>";
            echo "</tr>";
        }

        for ($x = 0; $x < $cantidaddata5; $x++) {
            echo "<tr>";
            echo "<th>N° Nota Credito </th>";
            $serie = str_pad($datadocumento5[$x]['serie'], 3, '0', STR_PAD_LEFT);
            $ultimo = $datadocumento5[$x]['numdoc'] + $datadocumento5[$x]['CantidadDocumentos'] - 1;
            echo "<td>" . $serie . '-' . $datadocumento5[$x]['numdoc'] . ($datadocumento5[$x]['CantidadDocumentos'] > 0 ? (" al " . $serie . '-' . $ultimo) : "") . "</td>";
            echo "<th>Estado </th>";
            echo "<td>" . ($datadocumento5[$x]['esAnulado'] == 1 ? 'Anulado' : 'Activo') . "</td>";
            echo "<td>" . ($datadocumento5[$x]['esImpreso'] == 1 ? 'Impreso' : 'No Impreso') . "</td>";
            echo "</tr>";
        }

        for ($y = 0; $y < $cantidaddata6; $y++) {
            echo "<tr>";
            echo "<th>N° Nota Devito </th>";
            $serie = str_pad($datadocumento6[$y]['serie'], 3, '0', STR_PAD_LEFT);
            $ultimo = $datadocumento6[$y]['numdoc'] + $datadocumento6[$y]['CantidadDocumentos'] - 1;
            echo "<td>" . $serie . '-' . $datadocumento6[$y]['numdoc'] . ($datadocumento6[$y]['CantidadDocumentos'] > 0 ? (" al " . $serie . '-' . $ultimo) : "") . "</td>";
            echo "<th>Estado </th>";
            echo "<td>" . ($datadocumento6[$y]['esAnulado'] == 1 ? 'Anulado' : 'Activo') . "</td>";
            echo "<td>" . ($datadocumento6[$y]['esImpreso'] == 1 ? 'Impreso' : 'No Impreso') . "</td>";
            echo "</tr>";
        }
    }

    /*
      function transferir2() {
      $nombre = "BJ";
      echo "Nombre: ".$nombre;

      $nombre_archivo = 'public/documentos/facturas/prueba2.txt';
      $contenido = $nombre;
      fopen($nombre_archivo, 'a+');

      // Asegurarse primero de que el archivo existe y puede escribirse sobre el.
      if (is_writable($nombre_archivo)) {

      // En nuestro ejemplo estamos abriendo $nombre_archivo en modo de adicion.
      // El apuntador de archivo se encuentra al final del archivo, asi que
      // alli es donde ira $contenido cuando llamemos fwrite().
      if (!$gestor = fopen($nombre_archivo, 'a')) {
      echo "No se puede abrir el archivo ($nombre_archivo)";
      exit;
      }

      // Escribir $contenido a nuestro arcivo abierto.
      if (fwrite($gestor, $contenido) === FALSE) {
      echo "No se puede escribir al archivo ($nombre_archivo)";
      exit;
      }

      echo "&Eacute;xito, se escribi&oacute; ($contenido) al archivo ($nombre_archivo)";

      fclose($gestor);

      } else {
      echo "No se puede escribir sobre el archivo $nombre_archivo";
      }
      }

      function transferir() {
      $ar = fopen("archivo.txt", "w") or die("Problemas en la creacion");
      for ($i = 0; $i <= 4; $i++) {

      for ($a = 0; $a <= 4; $a++) {

      fputs($ar, "(");
      fputs($ar, $i);
      fputs($ar, ",");
      fputs($ar, $a);
      fputs($ar, ")");
      }

      fputs($ar, chr(13) . chr(10));
      }
      fclose($ar);
      echo "El archivo TXT se genero correctamente.";
      echo "Bajar el archivo: <a href='archivo.txt' target='_blank'>Bajar txt</a>";
      }
     */

    function verDetalleFactura() {
        $idDoc = $_REQUEST['id'];
        $pdf = $this->AutoLoadModel('pdf');
        $ordenventa = $this->AutoLoadModel('documento');
        $buscaFactura = $ordenventa->buscaDocumento($idDoc, "");
        $data = $pdf->buscarDetalleOrdenVenta($buscaFactura[0]['idordenventa']);
        $nroLinea = 1;
        $importeTotal = 0;
        echo '<tr>';
        echo '<th colspan="6"><h2>' . ($buscaFactura[0]['electronico'] == 1 ? 'F' : '') . str_pad($buscaFactura[0]['serie'], 3, '0', STR_PAD_LEFT) . "-" . str_pad($buscaFactura[0]['numdoc'], 8, '0', STR_PAD_LEFT) . '</h2></th>';
        echo '</tr>';
        echo '<tr>' .
                    '<th>Nro</th>' .
                    '<th>Codigo</th>' .
                    '<th>Nombre</th>' .
                    '<th>Precio</th>' .
                    '<th>Cantidad</th>' .
                    '<th>Importe</th>' .
            '</tr>';
        for ($i = $buscaFactura[0]['desde'] - 1; $i < $buscaFactura[0]['hasta']; $i++) {
            if ($porcentaje != "") {
                if ($modo == 1) {
                    $precio = $data[$i]['preciofinal'];
                    $data[$i]['preciofinal'] = (($precio * $porcentaje) / 100);
                    $cantidadP = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                    $data[$i]['cantdespacho'] = $cantidadP;
                } elseif ($modo == 2) {
                    $cantidadP = $data[$i]['cantdespacho'] - $data['cantdevuelta'];
                    $data[$i]['cantdespacho'] = (($cantidadP * $porcentaje) / 100);
                } else {
                    $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                }
            } else {
                $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
            }
            if ($data[$i]['cantdespacho'] > 0) {
                $montoneto += ($data[$i]['preciofinal'] - $data[$i]['preciofinal'] * 0.18) * $data[$i]['cantdespacho'];
                $montoigv += $data[$i]['preciofinal'] * $data[$i]['cantdespacho'] * 0.18;
            }
            if ($data[$i]['cantdespacho'] > 0) {
                echo '<tr>';
                echo '<td>' . $nroLinea . '</td>';
                echo '<td>' . $data[$i]['codigopa'] . '</td>';
                echo '<td>' . $data[$i]['nompro'] . '</td>';
                echo '<td>' . number_format($data[$i]['preciofinal'], 2) . '</td>';
                echo '<td>' . $data[$i]['cantdespacho'] . '</td>';
                echo '<td>' . number_format($data[$i]['preciofinal'] * $data[$i]['cantdespacho'], 2) . '</td>';
                echo '</tr>';
                $importeTotal += ($data[$i]['preciofinal'] * $data[$i]['cantdespacho']);
                $nroLinea++;
            }
        }
        echo '<tr>';
        echo '<td colspan="3"></td>';
        echo '<th colspan="2">Importe Neto</th>';
        echo '<td><b>' . number_format($importeTotal - ($importeTotal * 0.18), 2) . '</b></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="3"></td>';
        echo '<th colspan="2">Importe IGV(18%)</th>';
        echo '<td><b>' . number_format($importeTotal * 0.18, 2) . '</b></td>';
        echo '</tr>';
        echo '<tr>';
        echo '<td colspan="3"></td>';
        echo '<th colspan="2">Importe Total</th>';
        echo '<td><b>' . number_format($importeTotal, 2) . '</b></td>';
        echo '</tr>';
    }

    function generaFacturaTXT() {
        $idDoc = $_REQUEST['iddocumentogeneral'];
        $percepcionporcentaje = (!empty($_REQUEST['percepcion']) ? $_REQUEST['percepcion'] : 0);
        $pdf = $this->AutoLoadModel('pdf');
        $ordenventa = $this->AutoLoadModel('documento');
        $buscaFactura = $ordenventa->buscaDocumento($idDoc, "");
        if (!empty($idDoc) && !empty($buscaFactura) && $idDoc > 0 && $buscaFactura[0]['nombredoc'] == 1 && $buscaFactura[0]['esAnulado'] != 1 && $buscaFactura[0]['esCargado'] != 1) {
            $archivoConfig = parse_ini_file("config.ini", true);
            $rutasFE = $archivoConfig['RutasFE'];
            $archivo = "F-" . $idDoc . ".txt";
            $ruta = $rutasFE[1] . $archivo;
            $crea = fopen($ruta, "w") or die("Problemas en la creacion");
            $porcentaje = $buscaFactura[0]['porcentajefactura'];
            $modo = $buscaFactura[0]['modofactura'];
            //$numeroFactura = $buscaFactura[0]['numdoc'];
            $numeroFactura = $ordenventa->ultimoCorrelativoElectronico($buscaFactura[0]['serie'], 1);
            $serieFactura = str_pad($buscaFactura[0]['serie'], 3, '0', STR_PAD_LEFT);
            $filtro = "nombredoc=4";
            $dataGuia = $ordenventa->buscadocumentoxordenventaPrimero($buscaFactura[0]['idordenventa'], $filtro);
            $numeroRelacionado = $dataGuia[0]['numdoc'];
            $dataPer['percepcion'] = $percepcionporcentaje;
            $ordenventa->actualizarPercepcion($dataPer, "idordenventa=" . $buscaFactura[0]['idordenventa']);
            fputs($crea, "A;Serie;;F" . $serieFactura);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;Correlativo;;" . str_pad($numeroFactura, 8, "0", STR_PAD_LEFT));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RznSocEmis;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;CODI_EMPR;;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RUTEmis;;20509811858");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirEmis;;JR. ALFEREZ F.RICARDO HERRERA NRO. 665 (ALT. CDRA 13 DE LA AV.ARGENTINA) LIMA - LIMA - LIMA");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;ComuEmis;;140101");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;NomComer;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoDTE;;01");
            fputs($crea, chr(13) . chr(10));//39121700
            fputs($crea, "A;TipoRutReceptor;;6");
            $tipodocumentorelacionado = $dataGuia[0]['nombredoc'];
            $dataFactura = $pdf->buscarxOrdenVenta($buscaFactura[0]['idordenventa']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RUTRecep;;" . $dataFactura[0]['ruc']);
            fputs($crea, chr(13) . chr(10));
            $dataFactura[0]['razonsocial'] = html_entity_decode(trim($dataFactura[0]['razonsocial']), ENT_QUOTES, 'UTF-8');
            $dataFactura[0]['razonsocial'] = iconv(mb_detect_encoding($dataFactura[0]['razonsocial']), "cp1252", $dataFactura[0]['razonsocial']);
            fputs($crea, "A;RznSocRecep;;" . $dataFactura[0]['razonsocial']);
            fputs($crea, chr(13) . chr(10));
            $dataFactura[0]['direccion_envio'] = html_entity_decode(trim($dataFactura[0]['direccion_envio']), ENT_QUOTES, 'UTF-8');
            $dataFactura[0]['direccion_envio'] = iconv(mb_detect_encoding($dataFactura[0]['direccion_envio']), "cp1252", $dataFactura[0]['direccion_envio']);
            fputs($crea, "A;DirRecep;;" . $dataFactura[0]['direccion_envio']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoMoneda;;" . $dataFactura[0]['tipomoneda']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;CodigoAutorizacion;;000");
            fputs($crea, chr(13) . chr(10));
            $dataFactura[0]['numeroRelacionado'] = !empty($numeroRelacionado) ? str_pad($dataGuia[0]['serie'], 4, '0', STR_PAD_LEFT) . "-" . str_pad($numeroRelacionado, 6, '0', STR_PAD_LEFT) : '';
            $dataFactura[0]['numeroFactura'] = $numeroFactura;
            $dataFactura[0]['serieFactura'] = $serieFactura;
            $dataFactura[0]['fecha'] = date('d/m/Y');
            $data = $pdf->buscarDetalleOrdenVenta($buscaFactura[0]['idordenventa']);
            $descuento = New Descuento();
            $dataDescuento = $descuento->listadoTotal();
            for ($i = 0; $i < count($dataDescuento); $i++) {
                $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
            }
            $dataN = array();
            $total = 0;
            $cont = 0;
            $nroLinea = 1;
            $montoneto = 0;
            $montoigv = 0;
            $totalGratuito = 0;
            
            for ($i = $buscaFactura[0]['desde'] - 1; $i < $buscaFactura[0]['hasta']; $i++) {
                if ($data[$i]['preciofinal']*($data[$i]['cantdespacho'] - $data[$i]['cantdevuelta']) <= 0.05) {
                    $data[$i]['preciofinal'] = 0;
                    $data[$i]['regalo'] = 1;
                } else {
                    $data[$i]['regalo'] = 0;
                }
                if ($porcentaje != "") {
                    if ($modo == 1) {
                        $precio = $data[$i]['preciofinal'];
                        $data[$i]['preciofinal'] = (($precio * $porcentaje) / 100);
                        $cantidadP = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];

                        $data[$i]['cantdespacho'] = $cantidadP;
                    } elseif ($modo == 2) {
                        $cantidadP = $data[$i]['cantdespacho'] - $data['cantdevuelta'];
                        $data[$i]['cantdespacho'] = (($cantidadP * $porcentaje) / 100);
                    } else {
                        $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                    }
                } else {
                    $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                }
                $data[$i]['cantdespacho'] = round($data[$i]['cantdespacho']);
                if ($data[$i]['cantdespacho'] > 0) {
                    $TemprecioTotal = $data[$i]['cantdespacho'] * $data[$i]['preciofinal'];
                    $montoneto += $TemprecioTotal / 1.18;
                    $montoigv += $TemprecioTotal - ($TemprecioTotal / 1.18);
//                    if ($data[$i]['regalo'] == 1) {
//                        $data[$i]['preciofinal'] = 0.1;
//                        $totalGratuito += 0.1*$data[$i]['cantdespacho'];
//                    }
                }
            }
            fputs($crea, "A;MntNeto;;" . round($montoneto, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntExe;;0.00");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntExo;;0.00");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotGrat;;" . round($totalGratuito, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotalIgv;;" . round($montoigv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotal;;" . round($montoneto + $montoigv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;FchEmis;;" . date('Y-m-d'));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;HoraEmision;;" . date('H:i:s'));// este es nuevo
            fputs($crea, chr(13) . chr(10)); // este es nuevo
            fputs($crea, "A;CodigoLocalAnexo;;0000"); //esto es nuevo
            fputs($crea, chr(13) . chr(10)); // esto es nuevo
            if ($percepcionporcentaje > 0) {
                fputs($crea, "A;TipoOperacion;;2001"); //esto es nuevo
            } else {
                fputs($crea, "A;TipoOperacion;;0101"); //esto es nuevo
                /*fputs($crea, chr(13) . chr(10));
                fputs($crea, "A;IndPercepcion;;1");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "A;MntImpPercepcion;;" . round($percepcionporcentaje * 100, 2));
                fputs($crea, chr(13) . chr(10));
                $valorPercepcion = ($montoneto + $montoigv) * $percepcionporcentaje;
                fputs($crea, "A;MntPercepcion;;" . round($valorPercepcion, 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "A;MntTotalMasPercepcion;;" . round($montoneto + $montoigv + $valorPercepcion, 2));*/
            }
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;CodigoImpuesto;1;1000");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;MontoImpuesto;1;" . round($montoigv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;TasaImpuesto;1;18");
            fputs($crea, chr(13) . chr(10));
            for ($i = $buscaFactura[0]['desde'] - 1; $i < $buscaFactura[0]['hasta']; $i++) {
                if ($data[$i]['cantdespacho'] > 0 && $data[$i]['regalo'] != 1) {
                    fputs($crea, "B;NroLinDet;" . $nroLinea . ";" . $nroLinea);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;QtyItem;" . $nroLinea . ";" . $data[$i]['cantdespacho']);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;UnmdItem;" . $nroLinea . ";" . $pdf->enlazarUnidadesSunat($data[$i]['idunidadmedida']));
                    fputs($crea, chr(13) . chr(10));
                    $data[$i]['codigopa'] = html_entity_decode(trim($data[$i]['codigopa']), ENT_QUOTES, 'UTF-8');
                    $data[$i]['codigopa'] = iconv(mb_detect_encoding($data[$i]['codigopa']), "cp1252", $data[$i]['codigopa']);
                    fputs($crea, "B;VlrCodigo;" . $nroLinea . ";" . $data[$i]['codigopa']);
                    fputs($crea, chr(13) . chr(10));
                    $data[$i]['nompro'] = html_entity_decode(trim($data[$i]['nompro']), ENT_QUOTES, 'UTF-8');
                    $data[$i]['nompro'] = iconv(mb_detect_encoding($data[$i]['nompro']), "cp1252", $data[$i]['nompro']);
                    fputs($crea, "B;NmbItem;" . $nroLinea . ";" . $data[$i]['nompro']);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;PrcItem;" . $nroLinea . ";" . round($data[$i]['preciofinal'], 2));
                    fputs($crea, chr(13) . chr(10));
                    $tempCodigosunat = $data[$i]['codigosunat'];
                    if ($data[$i]['regalo'] == 1) {
                        fputs($crea, "B;PrcItemSinIgv;" . $nroLinea . ";0.00");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;MontoItem;" . $nroLinea . ";" . round($data[$i]['cantdespacho']*0.01, 2));
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;IndExe;" . $nroLinea . ";21");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;CodigoTipoIgv;" . $nroLinea . ";9996");
                        
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;TasaIgv;" . $nroLinea . ";18");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;ImpuestoIgv;" . $nroLinea . ";0.00");
                        fputs($crea, chr(13) . chr(10));
                        if (strlen($tempCodigosunat) != 8) {
                            $tempCodigosunat = '80141600';
                        }
                    } else {
                        $precioigv = $data[$i]['preciofinal'] - ($data[$i]['preciofinal'] / 1.18);
                        fputs($crea, "B;PrcItemSinIgv;" . $nroLinea . ";" . round($data[$i]['preciofinal'] - $precioigv, 2));
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;MontoItem;" . $nroLinea . ";" . round(($data[$i]['preciofinal'] - $precioigv) * $data[$i]['cantdespacho'], 2));
                        fputs($crea, chr(13) . chr(10));
                    
                        fputs($crea, "B;IndExe;" . $nroLinea . ";10");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;CodigoTipoIgv;" . $nroLinea . ";1000");
                        
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;TasaIgv;" . $nroLinea . ";18");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;ImpuestoIgv;" . $nroLinea . ";" . round($precioigv*$data[$i]['cantdespacho'], 2));
                        fputs($crea, chr(13) . chr(10));                        
                        if (strlen($tempCodigosunat) != 8) {
                            $tempCodigosunat = $pdf->codigoSunatxlinea($data[$i]['idlinea']);
                        }
                    }
                    fputs($crea, "B;CodigoProductoSunat;" . $nroLinea . ";" . $tempCodigosunat);
                    fputs($crea, chr(13) . chr(10));
                    $nroLinea++;
                    $cont++;
                }
            }
            /*AQUI SE MOVIO LA PERCEPCION*/
            $importepercepcionfinal = 0;
            if ($percepcionporcentaje > 0) {
                fputs($crea, "C;NroLinDR;1;1");
                fputs($crea, chr(13) . chr(10));
                $tipodeCambio = $this->AutoLoadModel('TipoCambio');
                $tcVentas = 1;
                if ($dataFactura[0]['tipomoneda'] != 'PEN') {                    
                    $tcVentas = round($tipodeCambio->consultatipocambioXfecha(date('Y-m-d')), 2);
                    /*
                    fputs($crea, "C;TipoMoneda;1;PEN");
                    fputs($crea, chr(13) . chr(10));
                    */
                }
                $importebaseimponible = $montoneto + $montoigv;
                $valorPercepcion = $importebaseimponible * $percepcionporcentaje;
                
                /*fputs($crea, "C;TpoMov;1;G");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "C;ValorDR;1;" . round($valorPercepcion, 2));
                fputs($crea, chr(13) . chr(10));*/
                fputs($crea, "C;IndCargoDescuento;1;1");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "C;CodigoCargoDescuento;1;51");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "C;FactorCargoDescuento;1;" . $percepcionporcentaje);
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "C;MontoCargoDescuento;1;" . round($valorPercepcion*$tcVentas, 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "C;MBaseCargoDescuento;1;" . round($importebaseimponible*$tcVentas, 2));
                fputs($crea, chr(13) . chr(10));
                
                $importepercepcionfinal = round($importebaseimponible*$tcVentas, 2) + round($valorPercepcion*$tcVentas, 2);
            }
            /***********************************/
            fputs($crea, "E;TipoAdicSunat;01;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;01;01");
            fputs($crea, chr(13) . chr(10));
            $dataCobro = $pdf->buscarOrdenCompraxId($buscaFactura[0]['idordenventa']);
            if ($dataCobro[0]['escontado'] == 1 && $dataCobro[0]['escredito'] == 0 && $dataCobro[0]['esletras'] == 0) {
                fputs($crea, "E;DescripcionAdicsunat;01;CONTADO");
                fputs($crea, chr(13) . chr(10));
            } elseif ($dataCobro[0]['escredito'] == 1 && $dataCobro[0]['esletras'] == 0) {
                fputs($crea, "E;DescripcionAdicSunat;01;CREDITO");
                fputs($crea, chr(13) . chr(10));
            } elseif ($dataCobro[0]['esletras'] == 1) {
                fputs($crea, "E;DescripcionAdicSunat;01;LETRAS");
                fputs($crea, chr(13) . chr(10));
            }
            fputs($crea, "E;TipoAdicSunat;02;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;02;02");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;02;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;03;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;03;03");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;03;" . (!empty($dataFactura[0]['numeroRelacionado']) ? $dataFactura[0]['numeroRelacionado'] : '-'));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;04;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;04;04");
            fputs($crea, chr(13) . chr(10));
            $DescripcionAdicSunat04 = html_entity_decode(trim($dataFactura[0]['nombredepartamento'] . ' - ' . $dataFactura[0]['nombreprovincia'] . ' - ' . $dataFactura[0]['nombredistrito']), ENT_QUOTES, 'UTF-8');
            $DescripcionAdicSunat04 = iconv(mb_detect_encoding($DescripcionAdicSunat04), "cp1252", $DescripcionAdicSunat04);
            fputs($crea, "E;DescripcionAdicSunat;04;" . $DescripcionAdicSunat04);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;05;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;05;05");
            fputs($crea, chr(13) . chr(10));
            $dataFactura[0]['referencia'] = '#' . $dataFactura[0]['idvendedor'] . ' [' . $dataFactura[0]['codigov'] . ']';
            fputs($crea, "E;DescripcionAdicSunat;05;" . (!empty($dataFactura[0]['referencia']) ? $dataFactura[0]['referencia'] : '-'));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;06;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;06;06");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;06;" . $dataFactura[0]['telefono'] . '/' . $dataFactura[0]['celular']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;07;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;07;07");
            fputs($crea, chr(13) . chr(10));
            
            $EnLetras = New EnLetras();
            fputs($crea, "E;DescripcionAdicSunat;07;" . $EnLetras->ValorEnLetras(round($montoneto + $montoigv, 2), $dataFactura[0]['nombremoneda']));
            
            //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
            $cantidadDocumentos = 0;
            $maximoItem = $this->configIni("MaximoItem", "Factura");
            if ($cont % $maximoItem == 0) {
                $cantidadDocumentos = $cont / $maximoItem;
            } elseif ($cont % $maximoItem != 0) {
                $cantidadDocumentos = floor($cont / $maximoItem) + 1;
            }
            
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;08;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;08;08");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;08;" . number_format(round($importepercepcionfinal, 2), 2));
            
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;NroLinMail;1;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;MailEnvi;1;documentoselectronicos@cpoweracoustik.com");
            if (!empty($dataFactura[0]['email'])) {
                $arrayEmail = explode(' - ', $dataFactura[0]['email']);
                $itememail = 2;
                for ($email = 0; $email < count($arrayEmail) && $email < 3; $email++) {
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;NroLinMail;" . $itememail . ";" . $itememail);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;MailEnvi;" . $itememail . ";" . $arrayEmail[$email]);
                    $itememail++;
                }
            }
            $dataV['esCargado'] = 1;
            $dataV['CantidadDocumentos'] = $cantidadDocumentos;
            $dataV['numerorelacionado'] = $numeroRelacionado;
            $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $dataV['numdoc'] = $numeroFactura;
            $dataV['montofacturado'] = $montoneto + $montoigv;
            $dataV['montoigv'] = $montoigv;
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $ordenventa->actualizarDocumento($dataV, $filtro);
            $movimiento=new Movimiento();
            $dataMovimiento['ndocumento'] = $numeroFactura;
            $exitoMovimiento=$movimiento->actualizaMovimiento($dataMovimiento, "idordenventa='" . $buscaFactura[0]['idordenventa'] . "' and iddevolucion = 0");
            fclose($crea);
            $resp['rspta'] = 1;
            $resp['correlativo'] = $numeroFactura;
        } else {
            if ($buscaFactura[0]['esCargado'] == 1) {
                $resp['rspta'] = 2;
            } else {
                $resp['rspta'] = 0;
            }
        }
        header('Content-type: application/json; charset=cp1252');
        echo json_encode($resp);
    }

    function documentosxOrden() {
        $documento = $this->AutoLoadModel('documento');
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $idordenventa = $_REQUEST['idordenventa'];
        $tipodoc = $_REQUEST['tipodoc'];

        $filtro = "nombredoc='$tipodoc'";
        $tipoDocumento = $this->tipoDocumento();
        if ($tipodoc != 7) {
            $datadocumento = $documento->buscadocumentoxordenventa($idordenventa, $filtro);
        } else {
            $datadocumento = $documento->buscaletrasxordenventa($idordenventa, $filtro);
        }
        $dataorden = $ordenventa->buscarOrdenVentaxId($idordenventa);
        echo "<h2>" . $dataorden[0]['codigov'] . "</h2>";
        echo '<table>
                <thead>
                        <tr>
                                <th>Tipo Documento</th>
                                <td>' . ($tipodoc == 10 ? "Comprobante de Percepcion" : $tipoDocumento[$tipodoc]) . '</td>
                        </tr>
                        <tr>
                                <th>Serie</th>
                                <th>Numero Doc</th>
                                <th>Monto</th>
                                <th>igv</th>
                                <th>Fecha de Creacion</th>
                                <th>Fecha de Vencimiento</th>
                                <th>Numero Unico</th>
                                <th>Banco</th>
                                <th>Situacion</th>
                                <th>% de Doc</th>
                                <th>Modo de Doc</th>
                                <th>Fue Impreso</th>
                                <th colspan="3">Acciones</th>
                        </tr>
                </thead>
                <tbody>';
        if ($tipodoc == 1 || $tipodoc == 2 || $tipodoc == 4 || $tipodoc == 9) { //factura,boleta,guia remision percepcion
            $cantDocumento = count($datadocumento);
            for ($i = 0; $i < $cantDocumento; $i++) {
                $letra = "";
                if ($datadocumento[$i]['electronico'] == 1) {
                    $letra = "F";
                    if ($tipodoc == 2)
                        $letra = "B";
                }
                switch ($datadocumento[$i]['modofactura']) {
                    case '1':
                        $datadocumento[$i]['modofactura'] = 'precio';
                        break;
                    case '2':
                        $datadocumento[$i]['modofactura'] = 'Cantidad';
                        break;
                    default:
                        $datadocumento[$i]['modofactura'] = "";
                        break;
                }
                if (($tipodoc == 1 || $tipodoc == 2) && empty($datadocumento[$i]['numdoc'])) {
                    echo '<tr>
                            <td>' . $letra . (str_pad($datadocumento[$i]['serie'], 3, '0', STR_PAD_LEFT)) . '</td>
                            <td id="idBlockSinCargar' . $datadocumento[$i]['iddocumento'] . '"><small>Sin cargar</small></td>';
                } else {
                    echo '<tr>
                            <td>' . $letra . (str_pad($datadocumento[$i]['serie'], 3, '0', STR_PAD_LEFT)) . '</td>
                            <td>' . ($datadocumento[$i]['numdoc']) . '</td>';
                }
                   echo '<td>' . number_format($datadocumento[$i]['montofacturado'], 2) . '</td>
                        <td>' . number_format($datadocumento[$i]['montoigv'], 2) . '</td>
                        <td>' . $datadocumento[$i]['fechadoc'] . '</td>
                        <td>' . $datadocumento[$i]['fvencimiento'] . '</td>
                        <td>' . $datadocumento[$i]['numerounico'] . '</td>
                        <td>' . $datadocumento[$i]['recepcionLetras'] . '</td>
                        <td>' . $datadocumento[$i]['situacion'] . '</td>
                        <td>' . ($datadocumento[$i]['porcentajefactura'] == 0 ? '100' : $datadocumento[$i]['porcentajefactura']) . '</td>
                        <td>' . $datadocumento[$i]['modofactura'] . '<input type="hidden" class="iddocumento" value="' . $datadocumento[$i]['iddocumento'] . '" ></td>
                        <td>' . ($datadocumento[$i]['esImpreso'] == 1 || $datadocumento[$i]['esCargado'] == 1 ? "<img style='margin:auto;display:block' src='/imagenes/correcto.png'>" : "") . '</td>';
                if ($datadocumento[$i]['electronico'] == 0) {
                    if ($datadocumento[$i]['esAnulado'] == 1 && $datadocumento[$i]['esImpreso'] == 1) {
                        echo '<td></td>';
                        echo '<td>Anulado' . $datadocumento[$i]['motivo'] . '</td>';
                    } elseif ($datadocumento[$i]['esImpreso'] != 1 && $datadocumento[$i]['esAnulado'] != 1) {
                        echo '<td></td>';
                        echo '<td> <a href="#" id="' . $datadocumento[$i]['iddocumento'] . '" class="imprimir c7_datashet"><img style="margin:auto;display:block" src="/imagenes/imprimir.gif"></a> <input type="number" step="1" min="0" placeholder="0" id="noimprimir" autocomplete="off"><br> <input type="text" id="dist_prov_depa" placeholder="Distrito"></td>';
                    } elseif ($datadocumento[$i]['esImpreso'] == 1 && $datadocumento[$i]['esAnulado'] != 1) {
                        echo '<td></td>';
                        echo '<td> <button class="anular c9_datashet"> Anular</button> </td>';
                    }
                } else {
                    echo '<td><a href="#" id="' . $datadocumento[$i]['iddocumento'] . '" class="lupaVer c1_datashet"><img src="/imagenes/ver.gif"></a></td>';
                    if (($tipodoc == 1 || $tipodoc == 2) && $datadocumento[$i]['esImpreso'] != 1 && $datadocumento[$i]['esAnulado'] != 1 && $datadocumento[$i]['esCargado'] == 0 && $datadocumento[$i]['electronico'] == 1) {
                        echo '<td><a href="#" data-id="' . $datadocumento[$i]['iddocumento'] . '" class="cargar c2_datashet"><img style="margin:auto;display:block" id="iconofe' . $datadocumento[$i]['iddocumento'] . '" src="/imagenes/impfe.png"></a></td>';
                    } else {
                        if ($datadocumento[$i]['esAnulado'] == 1) {
                            echo '<td colspan="2"> <b>Anulado</b> </td>';
                        } else {
                            echo '<td> <img src="/imagenes/impfebien.png"> </td>';
                            echo '<td> <button class="anular c3_datashet"> <b>Anular Doc</b></button> </td>';
                        }
                    }
                }
                echo '</tr>';
            }
        } else if ($tipodoc == 10) {
            $cantDocumento = count($datadocumento);
            for ($i = 0; $i < $cantDocumento; $i++) {
                echo '<tr>
                        <td>P' . (str_pad($datadocumento[$i]['serie'], 3, '0', STR_PAD_LEFT)) . '</td>';
                echo '<td>' . ($datadocumento[$i]['numdoc'] == 0 ? str_pad($documento->ultimoCorrelativoElectronico($datadocumento[$i]['serie'], 10), 8, '0', STR_PAD_LEFT) : str_pad($datadocumento[$i]['numdoc'], 8, '0', STR_PAD_LEFT)) . '</td>';
                echo '<td>' . number_format($datadocumento[$i]['montofacturado'], 2) . '</td>
                        <td>' . number_format($datadocumento[$i]['montoigv'], 2) . '</td>
                        <td>' . $datadocumento[$i]['fechadoc'] . '</td>
                        <td>' . $datadocumento[$i]['fvencimiento'] . '</td>
                        <td>' . $datadocumento[$i]['numerounico'] . '</td>
                        <td>' . $datadocumento[$i]['recepcionLetras'] . '</td>
                        <td>' . $datadocumento[$i]['situacion'] . '</td>
                        <td>' . $datadocumento[$i]['porcentajefactura'] . '%</td>
                        <td>' . $datadocumento[$i]['modofactura'] . '<input type="hidden" class="iddocumento" value="' . $datadocumento[$i]['iddocumento'] . '" ></td>';
                if ($datadocumento[$i]['esAnulado'] == 1) {
                    echo '<td></td><td colspan="2"> <b>Anulado</b> </td>';
                } else if ($datadocumento[$i]['esCargado'] == 1) {
                    echo '<td> <img src="/imagenes/impfebien.png"> </td>';
                    echo '<td> <button class="anular c3_datashet"> <b>Anular Doc</b></button> </td>';
                } else {
                    echo '<td></td>';
                    echo '<td><a href="#" data-id="' . $datadocumento[$i]['iddocumento'] . '" class="cargar"><img style="margin:auto;display:block" id="iconofe' . $datadocumento[$i]['iddocumento'] . '" src="/imagenes/impfe.png"></a></td>';
                }
                echo '</tr>';
            }
        } else if ($tipodoc == 5 || $tipodoc == 6 || $tipodoc == 7) { //nota cred/debito
            $cantDocumento = count($datadocumento);
            for ($i = 0; $i < $cantDocumento; $i++) {
                switch ($datadocumento[$i]['modofactura']) {
                    case '1':
                        $datadocumento[$i]['modofactura'] = 'precio';
                        break;
                    case '2':
                        $datadocumento[$i]['modofactura'] = 'Cantidad';
                        break;

                    default:
                        $datadocumento[$i]['modofactura'] = "";
                        break;
                }
                echo '<tr>';
                if ($tipodoc == 7) {
                    echo '<td>' . (str_pad($datadocumento[$i]['serie'], 3, '0', STR_PAD_LEFT)) . '</td>';
                } else {
                    echo '<td>' . ($datadocumento[$i]['serie'] == 0 ? '<input maxlength="3" type="text" class="serie numeric"> ' : ($datadocumento[$i]['electronico'] == 1 ? 'F' : '') . str_pad($datadocumento[$i]['serie'], 3, '0', STR_PAD_LEFT)) . '</td>';
                }
                if ($datadocumento[$i]['electronico'] == 1) {
                    echo '<td id="idBlockSinCargar' . $datadocumento[$i]['iddocumento'] . '">' . ($datadocumento[$i]['numdoc'] == 0 ? '<small>Sin cargar</small>' : str_pad($datadocumento[$i]['numdoc'], 8, '0', STR_PAD_LEFT)) . '</td>';
                } else {
                    echo '<td>' . ($datadocumento[$i]['numdoc'] == 0 ? '<input maxlength="8" type="text" class="numdoc numeric"> ' : $datadocumento[$i]['numdoc']) . '</td>';
                }
                echo '<td>' . number_format($datadocumento[$i]['montofacturado'], 2) . '</td>
			<td>' . number_format($datadocumento[$i]['montigv'], 2) . '</td>
			<td>' . $datadocumento[$i]['fechadoc'] . '</td>
			<td>' . $datadocumento[$i]['fvencimiento'] . '</td>
			<td>' . $datadocumento[$i]['numerounico'] . '</td>
			<td>' . $datadocumento[$i]['recepcionLetras'] . '</td>
			<td>' . $datadocumento[$i]['situacion'] . '</td>
			<td>' . ($datadocumento[$i]['porcentajefactura'] == 0 ? '100' : $datadocumento[$i]['porcentajefactura']) . '</td>
			<td>' . $datadocumento[$i]['modofactura'] . '<input type="hidden" class="iddocumento" value="' . $datadocumento[$i]['iddocumento'] . '" ></td>
			<td>' . ($datadocumento[$i]['esImpreso'] == 1 ? "<img style='margin:auto;display:block' src='/imagenes/correcto.png'>" : "") . '</td>';
                if ($datadocumento[$i]['electronico'] == 0) {
                    if ($datadocumento[$i]['esAnulado'] == 1 && $datadocumento[$i]['esImpreso'] == 1) {
                        echo '<td colspan="2">Anulado&nbsp</td>
                                                                                            <td>' . $datadocumento[$i]['motivo'] . '</td>';
                    } elseif ($datadocumento[$i]['esImpreso'] != 1 && $datadocumento[$i]['esAnulado'] != 1) {
                        echo '<td colspan="2"> <a href="#" id="' . $datadocumento[$i]['iddocumento'] . '"  class="imprimir c8_datashet"><img style="margin:auto;display:block" src="/imagenes/imprimir.gif"></a> <input type="number" step="1" min="0" placeholder="0" id="noimprimir" autocomplete="off"><br> <input type="text" id="dist_prov_depa" ></td>';
                    } elseif ($datadocumento[$i]['esImpreso'] == 1 && $datadocumento[$i]['esAnulado'] != 1) {
                        echo '<td colspan="2"></td>
                                                                                            <td > <button class="anular c4_datashet"> Anular</button> </td>';
                    }
                } else {
                    echo '<td><a href="#" id="' . $datadocumento[$i]['iddocumento'] . '" class="lupaVer c5_datashet"><img src="/imagenes/ver.gif"></a></td>';
                    if (($tipodoc == 5 || $tipodoc == 6) && $datadocumento[$i]['esImpreso'] != 1 && $datadocumento[$i]['esAnulado'] != 1 && $datadocumento[$i]['esCargado'] == 0 && $datadocumento[$i]['electronico'] == 1) {
                        echo '<td><a href="#" data-id="' . $datadocumento[$i]['iddocumento'] . '" class="cargar c6_datashet"><img style="margin:auto;display:block" id="iconofe' . $datadocumento[$i]['iddocumento'] . '" src="/imagenes/impfe.png"></a></td>';
                    } else {
                        if ($datadocumento[$i]['esAnulado'] == 1) {
                            echo '<td colspan="2"> <b>Anulado</b> </td>';
                        } else {
                            echo '<td> <img src="/imagenes/impfebien.png"> </td>';
                            echo '<td> <button class="anular"> <b>Anular Doc</b></button> </td>';
                        }
                    }
                }
                echo '</tr>';
            }
        }
        echo '</tbody>
		</table>';
    }

    function actualizaDocumentoJson() {
        $documento = $this->AutoLoadModel('documento');
        $iddocumento = $_REQUEST['iddocumento'];
        $dataDocumento = $documento->buscaNotaCredito($iddocumento);
        if (!empty($dataDocumento)) {
            $movimiento = $this->AutoLoadModel('movimiento');
            $iddevolucion = $dataDocumento[0]['iddevolucion'];
            $idordenventa = $dataDocumento[0]['idordenventa'];
            $dataMovimiento['serie'] = $_REQUEST['serie'];
            $dataMovimiento['ndocumento'] = $_REQUEST['numdoc'];
            $condicion = "iddevolucion='$iddevolucion' and idordenventa='$idordenventa' ";
            $exito = $movimiento->actualizaMovimiento($dataMovimiento, $condicion);
        }
        $data['iddocumento'] = $iddocumento;
        $data['numdoc'] = $_REQUEST['numdoc'];
        $data['serie'] = $_REQUEST['serie'];
        $data['esimpreso'] = 1;
        $filtro = "iddocumento='$iddocumento'";
        $exito = $documento->actualizarDocumento($data, $filtro);
        echo $exito;
    }

    function listafacturas() {
        $idOV = $_REQUEST['id'];
        $documento = $this->AutoLoadModel('documento');
        $filtro = "doc.nombredoc=1 and doc.esAnulado!=1 and doc.esCargado=1";
        $datadocumento = $documento->buscadocumentoxordenventa($idOV, $filtro);
        $cantDocumento = count($datadocumento);
        $nro = 1;
        for ($i = 0; $i < $cantDocumento; $i++) {
            echo '<tr>
                        <td>' . $nro . '</td>
                        <td>' . ($datadocumento[$i]['electronico'] == 1 ? 'Electronica' : '') . '</td>
                        <td>' . ($datadocumento[$i]['electronico'] == 1 ? 'F' : '') . (str_pad($datadocumento[$i]['serie'], 3, '0', STR_PAD_LEFT)) . '</td>
                        <td>' . (str_pad($datadocumento[$i]['numdoc'], 8, '0', STR_PAD_LEFT)) . '</td>
                        <td>' . $datadocumento[$i]['simbolo'] . " " . number_format($datadocumento[$i]['montofacturado'], 2) . '</td>
                        <td>' . $datadocumento[$i]['fechadoc'] . '</td>
                        <td><img src="/public/imagenes/ver.gif" style="margin:auto;display:block" class="VerDetalle" data-id="' . $datadocumento[$i]['iddocumento'] . '"></td>';
            echo '</tr>';
            $nro++;
        }
    }

    function impresionDocumentos() {
        $data['documentos'] = $this->tipoDocumento();
        $this->view->show('/documento/impresionDocumentos.phtml', $data);
    }

    function verFactura() {
        $pdf = $this->AutoLoadModel('pdf');
        $ordenventa = $this->AutoLoadModel('documento');
        $EnLetras = New EnLetras();
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $cobro = $this->AutoLoadModel('ordencobro');
        $idDoc = $_REQUEST['id'];
        $buscaFactura = $ordenventa->buscaDocumento($idDoc, "");
        if (!empty($_REQUEST['id']) && !empty($buscaFactura) && $_REQUEST['id'] > 0 && $buscaFactura[0]['nombredoc'] == 1) {
            //obtemos la condicion y tiempo de credito
            //obtemos los porcenajes y modo que fue facturado
            $porcentaje = $buscaFactura[0]['porcentajefactura'];
            $modo = $buscaFactura[0]['modofactura'];
            $numeroFactura = $buscaFactura[0]['numdoc'];
            $serieFactura = str_pad($buscaFactura[0]['serie'], 3, '0', STR_PAD_LEFT);
            //buscamos la guia de remision que le pertenece en caso que lo hubiera
            $filtro = "nombredoc=4";
            $dataGuia = $ordenventa->buscadocumentoxordenventaPrimero($buscaFactura[0]['idordenventa'], $filtro);
            $numeroRelacionado = $dataGuia[0]['numdoc'];
            $tipodocumentorelacionado = $dataGuia[0]['nombredoc'];
            //Grabamos en
            //*********************//
            $dataFactura = $pdf->buscarxOrdenVenta($buscaFactura[0]['idordenventa']);
            $dataFactura[0]['numeroRelacionado'] = $numeroRelacionado;
            $dataFactura[0]['numeroFactura'] = $numeroFactura;
            $dataFactura[0]['serieFactura'] = $serieFactura;
            $dataFactura[0]['fecha'] = date('d/m/Y');
            $dataFactura[0]['referencia'] = 'VEN: ' . $dataFactura[0]['idvendedor'] . ' DC: ' . $dataFactura[0]['codigov'];
            $data = $pdf->buscarDetalleOrdenVenta($buscaFactura[0]['idordenventa']);
            $dataCobro = $pdf->buscarOrdenCompraxId($buscaFactura[0]['idordenventa']);
            if ($dataCobro[0]['escontado'] == 1 && $dataCobro[0]['escredito'] == 0 && $dataCobro[0]['esletras'] == 0) {
                $dataFactura[0]['condicion'] = 'CONTADO';
            } elseif ($dataCobro[0]['escredito'] == 1 && $dataCobro[0]['esletras'] == 0) {
                $dataFactura[0]['condicion'] = 'CREDITO';
            } elseif ($dataCobro[0]['esletras'] == 1) {
                $dataFactura[0]['condicion'] = 'LETRAS';
                //$dataFactura[0]['fechavencimiento']=$pdf->listaDetalleOrdenCompraxId($dataCobro[0]['idordencobro'],3);
            }
            $descuento = New Descuento();
            $dataDescuento = $descuento->listadoTotal();
            for ($i = 0; $i < count($dataDescuento); $i++) {
                $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
            }
            $cantidad = count($data);
            $dataN = array();
            $total = 0;
            $cont = 0;
            for ($i = 0; $i < $cantidad; $i++) {
                if ($porcentaje != "") {
                    if ($modo == 1) {
                        $precio = $data[$i]['preciofinal'];
                        $data[$i]['preciofinal'] = (($precio * $porcentaje) / 100);
                        $cantidadP = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                        $data[$i]['cantdespacho'] = $cantidadP;
                    } elseif ($modo == 2) {
                        $cantidadP = $data[$i]['cantdespacho'] - $data['cantdevuelta'];
                        $data[$i]['cantdespacho'] = (($cantidadP * $porcentaje) / 100);
                    } else {
                        $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                    }
                } else {
                    $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                }
                if ($data[$i]['cantdespacho'] > 0) {
                    $dataN[$cont]['cantdespacho'] = $data[$i]['cantdespacho'];
                    $dataN[$cont]['preciofinal'] = $data[$i]['preciofinal'];
                    $dataN[$cont]['nompro'] = $data[$i]['nompro'];
                    $dataN[$cont]['codigopa'] = $data[$i]['codigopa'];
                    $cont++;
                }
            }
            echo '';
            $cantidadDocumentos = 0;
            $maximoItem = $this->configIni("MaximoItem", "Factura");
            if ($cont % $maximoItem == 0) {
                $cantidadDocumentos = $cont / $maximoItem;
            } elseif ($cont % $maximoItem != 0) {
                $cantidadDocumentos = floor($cont / $maximoItem) + 1;
            }
            //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
            $dataV['esimpreso'] = 1;
            $dataV['CantidadDocumentos'] = $cantidadDocumentos;
            $dataV['numerorelacionado'] = $numeroRelacionado;
            $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $filtro = "iddocumento='" . $idDoc . "'";
            $datos['hojas'] = $cantidadDocumentos;
            $datos['maximoItem'] = $maximoItem;
            $datos['Factura'] = $dataFactura;
            $datos['DetalleFactura'] = $dataN;
            $datos['letras'] = $EnLetras;
            $datos['mes'] = $meses[date('n')];
            $this->view->show('/documento/verFactura.phtml', $datos);
            //$this->view->showImpr('/documento/generaFactura.phtml', $datos);
        }
    }

    function verBoleta() {
        $pdf = $this->AutoLoadModel('pdf');
        $documento = $this->AutoLoadModel('documento');
        $EnLetras = New EnLetras();
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $cobro = $this->AutoLoadModel('ordencobro');
        $idDoc = $_REQUEST['id'];
        $buscaDocumento = $documento->buscaDocumento($idDoc, "");
        //obtemos numero de boleta y serie
        if (!empty($_REQUEST['id']) && !empty($buscaDocumento) && $_REQUEST['id'] > 0 && $buscaDocumento[0]['nombredoc'] == 2 && $buscaDocumento[0]['esAnulado'] != 1) {
            $numeroFactura = $buscaDocumento[0]['numdoc'];
            $serieFactura = str_pad($buscaDocumento[0]['serie'], 3, '0', STR_PAD_LEFT);
            //buscamos la guia de remision que le pertenece en caso que lo hubiera
            $filtro = "nombredoc=4";
            $dataGuia = $documento->buscadocumentoxordenventaPrimero($buscaDocumento[0]['idordenventa'], $filtro);
            $numeroRelacionado = $dataGuia[0]['numdoc'];
            $tipodocumentorelacionado = $dataGuia[0]['nombredoc'];
            //*********************//
            $datadocumento = $pdf->buscarxOrdenVenta($buscaDocumento[0]['idordenventa']);
            if (!empty($buscaDocumento[0]['nombrecliente'])) {
                $datadocumento[0]['razonsocial'] = $buscaDocumento[0]['nombrecliente'];
                $datadocumento[0]['direccion_envio'] = '';
            }
            $datadocumento[0]['numeroRelacionado'] = $numeroRelacionado;
            $datadocumento[0]['simbolo'] = $dataGuia[0]['simbolo'];
            $datadocumento[0]['nombresimbolo'] = $dataGuia[0]['nombre'];
            $datadocumento[0]['numeroFactura'] = $numeroFactura;
            $datadocumento[0]['serieFactura'] = $serieFactura;
            $datadocumento[0]['fecha'] = date('d/m/Y');
            $datadocumento[0]['referencia'] = 'VEN: ' . $datadocumento[0]['idvendedor'] . ' DC: ' . $datadocumento[0]['idordenventa'];
            $data = $pdf->buscarDetalleOrdenVenta($buscaDocumento[0]['idordenventa']);
            $cantidad = count($data);
            $cantidadDocumentos = 0;
            $maximoItem = $this->configIni("MaximoItem", "Boleta");
            if ($cantidad % $maximoItem == 0) {
                $cantidadDocumentos = $cantidad / $maximoItem;
            } elseif ($cantidad % $maximoItem != 0) {
                $cantidadDocumentos = floor($cantidad / $maximoItem) + 1;
            }
            //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
            $dataV['esimpreso'] = 1;
            $dataV['CantidadDocumentos'] = $cantidadDocumentos;
            $dataV['numerorelacionado'] = $numeroRelacionado;
            $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $filtro = "iddocumento='" . $idDoc . "'";
            $datos['hojas'] = $cantidadDocumentos;
            $datos['Boleta'] = $datadocumento;
            $datos['DetalleBoleta'] = $data;
            $datos['letras'] = $EnLetras;
            $datos['mes'] = $meses[date('n')];
            $datos['maximoItem'] = $maximoItem;
            $this->view->show('/documento/verBoleta.phtml', $datos);
        }
    }

    function generaFactura() {
        $pdf = $this->AutoLoadModel('pdf');
        $ordenventa = $this->AutoLoadModel('documento');
        $EnLetras = New EnLetras();
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $cobro = $this->AutoLoadModel('ordencobro');
        $idDoc = $_REQUEST['id'];
        $buscaFactura = $ordenventa->buscaDocumento($idDoc, "");
        if (!empty($_REQUEST['id']) && !empty($buscaFactura) && $_REQUEST['id'] > 0 && $buscaFactura[0]['nombredoc'] == 1 && $buscaFactura[0]['esAnulado'] != 1) {
            //obtemos la condicion y tiempo de credito
            //obtemos los porcenajes y modo que fue facturado
            $porcentaje = $buscaFactura[0]['porcentajefactura'];
            $modo = $buscaFactura[0]['modofactura'];
            $numeroFactura = $buscaFactura[0]['numdoc'];
            $serieFactura = str_pad($buscaFactura[0]['serie'], 3, '0', STR_PAD_LEFT);
            //buscamos la guia de remision que le pertenece en caso que lo hubiera
            $filtro = "nombredoc=4";
            $dataGuia = $ordenventa->buscadocumentoxordenventaPrimero($buscaFactura[0]['idordenventa'], $filtro);
            $numeroRelacionado = $dataGuia[0]['numdoc'];
            $tipodocumentorelacionado = $dataGuia[0]['nombredoc'];
            //Grabamos en
            //*********************//
            $dataFactura = $pdf->buscarxOrdenVenta($buscaFactura[0]['idordenventa']);
            $dataFactura[0]['numeroRelacionado'] = $numeroRelacionado;
            $dataFactura[0]['numeroFactura'] = $numeroFactura;
            $dataFactura[0]['serieFactura'] = $serieFactura;
            $dataFactura[0]['fecha'] = date('d/m/Y');
            $dataFactura[0]['referencia'] = 'VEN: ' . $dataFactura[0]['idvendedor'] . ' DC: ' . $dataFactura[0]['codigov'];
            $data = $pdf->buscarDetalleOrdenVenta($buscaFactura[0]['idordenventa']);
            $dataCobro = $pdf->buscarOrdenCompraxId($buscaFactura[0]['idordenventa']);
            if ($dataCobro[0]['escontado'] == 1 && $dataCobro[0]['escredito'] == 0 && $dataCobro[0]['esletras'] == 0) {
                $dataFactura[0]['condicion'] = 'CONTADO';
            } elseif ($dataCobro[0]['escredito'] == 1 && $dataCobro[0]['esletras'] == 0) {
                $dataFactura[0]['condicion'] = 'CREDITO';
            } elseif ($dataCobro[0]['esletras'] == 1) {
                $dataFactura[0]['condicion'] = 'LETRAS';
                //$dataFactura[0]['fechavencimiento']=$pdf->listaDetalleOrdenCompraxId($dataCobro[0]['idordencobro'],3);
            }
            $descuento = New Descuento();
            $dataDescuento = $descuento->listadoTotal();
            for ($i = 0; $i < count($dataDescuento); $i++) {
                $dscto[$dataDescuento[$i]['id']] = $dataDescuento[$i]['valor'];
            }
            $cantidad = count($data);
            $dataN = array();
            $total = 0;
            $cont = 0;
            for ($i = 0; $i < $cantidad; $i++) {
                if ($porcentaje != "") {
                    if ($modo == 1) {
                        $precio = $data[$i]['preciofinal'];
                        $data[$i]['preciofinal'] = (($precio * $porcentaje) / 100);
                        $cantidadP = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                        $data[$i]['cantdespacho'] = $cantidadP;
                    } elseif ($modo == 2) {
                        $cantidadP = $data[$i]['cantdespacho'] - $data['cantdevuelta'];
                        $data[$i]['cantdespacho'] = (($cantidadP * $porcentaje) / 100);
                    } else {
                        $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                    }
                } else {
                    $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                }
                if ($data[$i]['cantdespacho'] > 0) {
                    $dataN[$cont]['cantdespacho'] = $data[$i]['cantdespacho'];
                    $dataN[$cont]['preciofinal'] = $data[$i]['preciofinal'];
                    $dataN[$cont]['nompro'] = $data[$i]['nompro'];
                    $dataN[$cont]['codigopa'] = $data[$i]['codigopa'];
                    $cont++;
                }
            }
            echo '';
            $cantidadDocumentos = 0;
            $maximoItem = $this->configIni("MaximoItem", "Factura");
            if ($cont % $maximoItem == 0) {
                $cantidadDocumentos = $cont / $maximoItem;
            } elseif ($cont % $maximoItem != 0) {
                $cantidadDocumentos = floor($cont / $maximoItem) + 1;
            }
            //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
            $dataV['esimpreso'] = 1;
            $dataV['CantidadDocumentos'] = $cantidadDocumentos;
            $dataV['numerorelacionado'] = $numeroRelacionado;
            $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $ordenventa->actualizarDocumento($dataV, $filtro);
            $datos['hojas'] = $cantidadDocumentos;
            $datos['maximoItem'] = $maximoItem;
            $datos['Factura'] = $dataFactura;
            $datos['DetalleFactura'] = $dataN;
            $datos['letras'] = $EnLetras;
            $datos['mes'] = $meses[date('n')];
            $this->view->showImpr('/documento/generaFactura.phtml', $datos);
        }
    }

    function generaGuia() {
        $_REQUEST['parameters'][1]=$_REQUEST['parameters'][1]?intval($_REQUEST['parameters'][1]):0;
        $_REQUEST['parameters'][2]=$_REQUEST['parameters'][2]?strtoupper($_REQUEST['parameters'][2]):null;
        $pdf = $this->AutoLoadModel('pdf');
        $ordenventa = $this->AutoLoadModel('documento');
        $idDoc = $_REQUEST['id'];
        $tipo = $this->tipoDocumento();
        $buscaGuia = $ordenventa->buscaDocumento($idDoc, "");
        if (!empty($_REQUEST['id']) && !empty($buscaGuia) && $_REQUEST['id'] > 0 && $buscaGuia[0]['nombredoc'] == 4 && $buscaGuia[0]['esAnulado'] != 1) {
            //buscamos la Factura que le pertenece en caso que lo hubiera
            $filtro = "nombredoc=1";
            $dataFactura = $ordenventa->buscadocumentoxordenventaPrimero($buscaGuia[0]['idordenventa'], $filtro);
            $tipodocumentorelacionado = $dataFactura[0]['nombredoc'];
            session_start();
            $usuario = $_SESSION['nombres'] . ' ' . $_SESSION['apellidopaterno'];
            $dataGuia = $pdf->buscarxOrdenVenta($buscaGuia[0]['idordenventa']);
            $dataGuia[0]['imprimir'] = $imprimir;
            $dataGuia[0]['tipo'] = $tipodocumentorelacionado;
            $numeroRelacionado = $dataFactura[0]['numdoc'];
            if ($dataFactura[0]['electronico'] == 1) {
                $dataGuia[0]['numeroRelacionado'] = "F" . str_pad($dataFactura[0]['serie'], 3, '0', STR_PAD_LEFT) . "-" . str_pad($dataFactura[0]['numdoc'], 8, '0', STR_PAD_LEFT);
            } else {
                $dataGuia[0]['numeroRelacionado'] = $numeroRelacionado;
            }
            for ($if = 1; $if < count($dataFactura) && $dataFactura[$if]['electronico'] == 1; $if++) {
                $numeroRelacionado .= "-" . $dataFactura[$if]['numdoc'];
                $dataGuia[0]['numeroRelacionado'] .= "/" . str_pad($dataFactura[$if]['numdoc'], 8, '0', STR_PAD_LEFT);
            }
            $dataGuia[0]['tipo'] = $tipo[$dataGuia[0]['tipo']];
            $dataGuia[0]['numeroFactura'] = $buscaGuia[0]['numdoc'];
            $dataGuia[0]['serieFactura'] = str_pad($buscaGuia[0]['serie'], 3, '0', STR_PAD_LEFT);
            $dataGuia[0]['fecha'] = date('d/m/Y');
            $dataGuia[0]['referencia'] = ' REF: ' . $dataGuia[0]['codigov'] . '    VEN: ' . $dataGuia[0]['idvendedor'] . '     ' . strtoupper($usuario) . '  --  ' . date('H:i:s');
            $dataGuia[0]['domiPartida'] = 'JR. ALFZ. RICARDO HERRERA 665 - LIMA -  LIMA  -  LIMA';
            $dataGuia[0]['telefonos'] = $dataGuia[0]['telefono'] . '/' . $dataGuia[0]['celular'];
            $dataCobro = $pdf->buscarOrdenCompraxId($buscaGuia[0]['idordenventa']);
            if ($dataCobro[0]['escontado'] == 1 && $dataCobro[0]['escredito'] == 0 && $dataCobro[0]['esletras'] == 0) {
                $dataGuia[0]['condiciones'] = "CONTADO";
            } elseif ($dataCobro[0]['escredito'] == 1 && $dataCobro[0]['esletras'] == 0) {
                $dataGuia[0]['condiciones'] = "CREDITO";
            } elseif ($dataCobro[0]['esletras'] == 1) {
                $dataGuia[0]['condiciones'] = "LETRAS";
            }
            $data = $pdf->buscarDetalleOrdenVenta($buscaGuia[0]['idordenventa']);
            $cantidad = count($data)-$_REQUEST['parameters'][1];
            $datos['PartidaRegistral'] = '';
            for ($iPel = 0; $iPel < $cantidad; $iPel++) {
                if ($data[$iPel]['peligro'] == 1) {
                    $iPel = $cantidad;
                    $datos['PartidaRegistral'] = $this->configIni("PartidaRegistral", "valor");
                }
            }
            $cantidadDocumentos = 0;
            $maximoItem = $this->configIni("MaximoItem", "Guia");
            if ($cantidad % $maximoItem == 0) {
                $cantidadDocumentos = $cantidad / $maximoItem;
            } elseif ($cantidad % $maximoItem != 0) {
                $cantidadDocumentos = floor($cantidad / $maximoItem) + 1;
            }
            //acutalizamos Documento que ya fue impreso
            $data2['esimpreso'] = 1;
            $data2['CantidadDocumentos'] = $cantidadDocumentos;
            $data2['numeroRelacionado'] = $numeroRelacionado;
            $data2['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $filtro2 = "iddocumento='" . $idDoc . "'";
            $exitoE = $ordenventa->actualizarDocumento($data2, $filtro2);
            $datos['hojas'] = $cantidadDocumentos;
            $datos['Factura'] = $dataGuia;
            $datos['DetalleFactura'] = $data;
            $datos['noImprimir'] = $_REQUEST['parameters'][1];
            $datos['direc_desp0'] = $_REQUEST['parameters'][2];
            $datos['maximoItem'] = $maximoItem;
            $this->view->show('/documento/generaGuia.phtml', $datos);
        }
    }

    function generaLetra() {
        $iddocumento = $_REQUEST['id'];
        $detallecobro = $this->AutoLoadModel('detalleordencobro');
        $datadetallecobro = $detallecobro->listaConClientes($iddocumento);
        if (!empty($_REQUEST['id']) && !empty($datadetallecobro) && $_REQUEST['id'] > 0 && $datadetallecobro[0]['nombredoc'] == 7 && $datadetallecobro[0]['esAnulado'] != 1) {
            $numeroletra = $datadetallecobro[0]['numdoc'];
            $montofacturado = $datadetallecobro[0]['montofacturado'];
            $databusqueda = $detallecobro->buscaDetalleOrdencobroxNumeroletra($numeroletra, $montofacturado);
            $EnLetras = New EnLetras();
            $datadetallecobro[0]['importedoc'] = $databusqueda[0]['importedoc'];
            $datadetallecobro[0]['fechagiro'] = $databusqueda[0]['fechagiro'];
            $datadetallecobro[0]['fvencimiento'] = $databusqueda[0]['fvencimiento'];
            $datadetallecobro[0]['numeroletra'] = $databusqueda[0]['numeroletra'];
            if ($datadetallecobro[0]['tipocliente'] == 1) {
                $datadetallecobro[0]['ruc'] = $datadetallecobro[0]['dni'];
            }
            $data['detalle'] = $datadetallecobro;
            $data['letra'] = $EnLetras;
            $this->view->show('/documento/generaLetra.phtml', $data);
        }
    }

    function generaBoletaTXT() {
        $pdf = $this->AutoLoadModel('pdf');
        $documento = $this->AutoLoadModel('documento');
        $EnLetras = New EnLetras();
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $cobro = $this->AutoLoadModel('ordencobro');
        $idDoc = $_REQUEST['iddocumentogeneral'];
        $buscaDocumento = $documento->buscaDocumento($idDoc, "");
        if (!empty($idDoc) && !empty($buscaDocumento) && $idDoc > 0 && $buscaDocumento[0]['nombredoc'] == 2 && $buscaDocumento[0]['esAnulado'] != 1 && $buscaDocumento[0]['esCargado'] != 1) {
            $archivoConfig = parse_ini_file("config.ini", true);
            $rutasFE = $archivoConfig['RutasFE'];
            $archivo = "B-" . $idDoc . ".txt";
            $ruta = $rutasFE[2] . $archivo;
            $crea = fopen($ruta, "w") or die("Problemas en la creacion");
            //$numeroBoleta = $buscaDocumento[0]['numdoc'];
            $numeroBoleta = $documento->ultimoCorrelativoElectronico($buscaDocumento[0]['serie'], 2);
            $serieBoleta = str_pad($buscaDocumento[0]['serie'], 3, '0', STR_PAD_LEFT);
            fputs($crea, "A;Serie;;B" . $serieBoleta);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;Correlativo;;" . str_pad($numeroBoleta, 8, "0", STR_PAD_LEFT));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RznSocEmis;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;CODI_EMPR;;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RUTEmis;;20509811858");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirEmis;;JR. ALFEREZ F.RICARDO HERRERA NRO. 665 (ALT. CDRA 13 DE LA AV.ARGENTINA) LIMA - LIMA - LIMA");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;ComuEmis;;140101");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;NomComer;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoDTE;;03");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoRutReceptor;;1");
            fputs($crea, chr(13) . chr(10));
            $datadocumento = $pdf->buscarxOrdenVenta($buscaDocumento[0]['idordenventa']);
            if (!empty($buscaDocumento[0]['nombrecliente'])) {
                $datadocumento[0]['razonsocial'] = $buscaDocumento[0]['nombrecliente'];
                $datadocumento[0]['direccion_envio'] = '';
            }
            fputs($crea, "A;RUTRecep;;" . $datadocumento[0]['dni']);
            fputs($crea, chr(13) . chr(10));
            
            $datadocumento[0]['razonsocial'] = html_entity_decode(trim($datadocumento[0]['razonsocial']), ENT_QUOTES, 'UTF-8');
            $datadocumento[0]['razonsocial'] = iconv(mb_detect_encoding($datadocumento[0]['razonsocial']), "cp1252", $datadocumento[0]['razonsocial']);
            fputs($crea, "A;RznSocRecep;;" . $datadocumento[0]['razonsocial']);
            fputs($crea, chr(13) . chr(10));
            $datadocumento[0]['direccion_envio'] = html_entity_decode(trim($datadocumento[0]['direccion_envio']) . " - " . $datadocumento[0]['nombredepartamento'] . ' - ' . $datadocumento[0]['nombreprovincia'] . ' - ' . $datadocumento[0]['nombredistrito'], ENT_QUOTES, 'UTF-8');
            $datadocumento[0]['direccion_envio'] = iconv(mb_detect_encoding($datadocumento[0]['direccion_envio']), "cp1252", $datadocumento[0]['direccion_envio']);
            fputs($crea, "A;DirRecep;;" . $datadocumento[0]['direccion_envio']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoMoneda;;" . $datadocumento[0]['tipomoneda']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;CodigoAutorizacion;;000");
            fputs($crea, chr(13) . chr(10));
            $data = $pdf->buscarDetalleOrdenVenta($buscaDocumento[0]['idordenventa']);
            $tam = count($data);
            $totalMonto = 0;
            $totalGratuito = 0;
            for ($i = $buscaDocumento[0]['desde'] - 1; $i < $buscaDocumento[0]['hasta']; $i++) {
                $data[$i]['cantdespacho'] = $data[$i]['cantdespacho'] - $data[$i]['cantdevuelta'];
                if ($data[$i]['preciofinal']*$data[$i]['cantdespacho'] <= 0.05) {
                    $data[$i]['preciofinal'] = 0;
                    $data[$i]['regalo'] = 1;
//                    $totalGratuito += 0.1*$data[$i]['cantdespacho'];
                } else {
                    $data[$i]['regalo'] = 0;
                }
                if ($data[$i]['cantdespacho'] > 0) {
                    $totalMonto += $data[$i]['preciofinal'] * $data[$i]['cantdespacho'];
                }
            }
            fputs($crea, "A;MntNeto;;" . round($totalMonto / 1.18, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntExe;;0.00");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntExo;;0.00");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotGrat;;" . round($totalGratuito, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotalIgv;;" . round($totalMonto - ($totalMonto / 1.18), 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotal;;" . round($totalMonto, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;FchEmis;;" . date('Y-m-d'));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;HoraEmision;;" . date('H:i:s')); //por esto
            fputs($crea, chr(13) . chr(10)); // por esto
            fputs($crea, "A;CodigoLocalAnexo;;0000"); //esto es nuevo
            fputs($crea, chr(13) . chr(10)); // esto es nuevo
            fputs($crea, "A;TipoOperacion;;0101"); //esto es nuevo
            fputs($crea, chr(13) . chr(10)); //esto es nuevo
            
            fputs($crea, "A2;CodigoImpuesto;1;1000");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;MontoImpuesto;1;" . round($totalMonto - ($totalMonto / 1.18), 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;TasaImpuesto;1;18");
            fputs($crea, chr(13) . chr(10));
            $nroLinea = 1;
            for ($i = $buscaDocumento[0]['desde'] - 1; $i < $buscaDocumento[0]['hasta']; $i++) {
                if ($data[$i]['cantdespacho'] > 0 && $data[$i]['regalo'] != 1) {
                    fputs($crea, "B;NroLinDet;" . $nroLinea . ";" . $nroLinea);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;QtyItem;" . $nroLinea . ";" . $data[$i]['cantdespacho']);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;UnmdItem;" . $nroLinea . ";" . $pdf->enlazarUnidadesSunat($data[$i]['idunidadmedida']));
                    fputs($crea, chr(13) . chr(10));
                    $data[$i]['codigopa'] = html_entity_decode(trim($data[$i]['codigopa']), ENT_QUOTES, 'UTF-8');
                    $data[$i]['codigopa'] = iconv(mb_detect_encoding($data[$i]['codigopa']), "cp1252", $data[$i]['codigopa']);
                    fputs($crea, "B;VlrCodigo;" . $nroLinea . ";" . $data[$i]['codigopa']);
                    fputs($crea, chr(13) . chr(10));
                    $data[$i]['nompro'] = html_entity_decode(trim($data[$i]['nompro']), ENT_QUOTES, 'UTF-8');
                    $data[$i]['nompro'] = iconv(mb_detect_encoding($data[$i]['nompro']), "cp1252", $data[$i]['nompro']);
                    fputs($crea, "B;NmbItem;" . $nroLinea . ";" . $data[$i]['nompro']);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;PrcItem;" . $nroLinea . ";" . round($data[$i]['preciofinal'], 2));
                    fputs($crea, chr(13) . chr(10));
                    $tempCodigosunat = $data[$i]['codigosunat'];
                    if ($data[$i]['regalo'] == 1) {
                        fputs($crea, "B;PrcItemSinIgv;" . $nroLinea . ";0.00");
                        fputs($crea, chr(13) . chr(10));
//                        fputs($crea, "B;MontoItem;" . $nroLinea . ";" . round($data[$i]['cantdespacho']*0.06, 2));
//                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;IndExe;" . $nroLinea . ";21");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;CodigoTipoIgv;" . $nroLinea . ";9996");
                        
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;TasaIgv;" . $nroLinea . ";18");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;ImpuestoIgv;" . $nroLinea . ";0.00");
                        fputs($crea, chr(13) . chr(10));
                        if (strlen($tempCodigosunat) != 8) {
                            $tempCodigosunat = '80141600';
                        }
                    } else {
                        $precioigv = $data[$i]['preciofinal'] - ($data[$i]['preciofinal'] / 1.18);
                        fputs($crea, "B;PrcItemSinIgv;" . $nroLinea . ";" . round($data[$i]['preciofinal'] - $precioigv, 2));
                        fputs($crea, chr(13) . chr(10));
                        
                        fputs($crea, "B;MontoItem;" . $nroLinea . ";" . round(($data[$i]['preciofinal'] - $precioigv) * $data[$i]['cantdespacho'], 2));
                        fputs($crea, chr(13) . chr(10));
                    
                        fputs($crea, "B;IndExe;" . $nroLinea . ";10");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;CodigoTipoIgv;" . $nroLinea . ";1000");
                        
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;TasaIgv;" . $nroLinea . ";18");
                        fputs($crea, chr(13) . chr(10));
                        fputs($crea, "B;ImpuestoIgv;" . $nroLinea . ";" . round($precioigv*$data[$i]['cantdespacho'], 2));
                        fputs($crea, chr(13) . chr(10));
                        if (strlen($tempCodigosunat) != 8) {
                            $tempCodigosunat = $pdf->codigoSunatxlinea($data[$i]['idlinea']);
                        }
                    }
                    
                    fputs($crea, "B;CodigoProductoSunat;" . $nroLinea . ";" . $tempCodigosunat);
                    fputs($crea, chr(13) . chr(10));
                    $nroLinea++;
                }
            }
            fputs($crea, "E;TipoAdicSunat;01;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;01;01");
            fputs($crea, chr(13) . chr(10));
            $dataCobro = $pdf->buscarOrdenCompraxId($buscaDocumento[0]['idordenventa']);
            if ($dataCobro[0]['escontado'] == 1 && $dataCobro[0]['escredito'] == 0 && $dataCobro[0]['esletras'] == 0) {
                fputs($crea, "E;DescripcionAdicsunat;01;CONTADO");
            } elseif ($dataCobro[0]['escredito'] == 1 && $dataCobro[0]['esletras'] == 0) {
                fputs($crea, "E;DescripcionAdicSunat;01;CREDITO");
            } elseif ($dataCobro[0]['esletras'] == 1) {
                fputs($crea, "E;DescripcionAdicSunat;01;LETRAS");
            }
            $datadocumento[0]['referencia'] = '#' . $datadocumento[0]['idvendedor'] . ' [' . $datadocumento[0]['idordenventa'] . ']';
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;02;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;02;02");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;02;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;03;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;03;03");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;03;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;04;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;04;04");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;04;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;05;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;05;05");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;05;" . $datadocumento[0]['referencia']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;06;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;06;06");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;06;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;07;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;07;7");
            fputs($crea, chr(13) . chr(10));
            $EnLetras = New EnLetras();
            fputs($crea, "E;DescripcionAdicSunat;07;" . $EnLetras->ValorEnLetras(round($totalMonto, 2), $datadocumento[0]['nombremoneda']));
            
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;NroLinMail;1;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;MailEnvi;1;documentoselectronicos@cpoweracoustik.com");
            if (!empty($datadocumento[0]['email'])) {
                $arrayEmail = explode(' - ', $datadocumento[0]['email']);
                $itememail = 2;
                for ($email = 0; $email < count($arrayEmail) && $email < 3; $email++) {
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;NroLinMail;" . $itememail . ";" . $itememail);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;MailEnvi;" . $itememail . ";" . $arrayEmail[$email]);
                    $itememail++;
                }
            }
            $filtro = "nombredoc=4";
            $dataGuia = $documento->buscadocumentoxordenventaPrimero($buscaDocumento[0]['idordenventa'], $filtro);
            $numeroRelacionado = $dataGuia[0]['numdoc'];
            $tipodocumentorelacionado = $dataGuia[0]['nombredoc'];
            //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
            $dataV['esCargado'] = 1;
            $dataV['numerorelacionado'] = $numeroRelacionado;
            $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $dataV['numdoc'] = $numeroBoleta;
            $dataV['montofacturado'] = $totalMonto;
            $dataV['montoigv'] = $totalMonto - ($totalMonto / 1.18);
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $documento->actualizarDocumento($dataV, $filtro);
            fclose($crea);
            $resp['rspta'] = 1;
            $resp['correlativo'] = $numeroBoleta;
        } else {
            if ($buscaDocumento[0]['esCargado'] == 1) {
                $resp['rspta'] = 2;
            } else {
                $resp['rspta'] = 0;
            }
        }
        header('Content-type: application/json; charset=cp1252');
        echo json_encode($resp);
    }


    function generaBoleta() {
        $this->view->template = 'impresion';
        $pdf = $this->AutoLoadModel('pdf');
        $documento = $this->AutoLoadModel('documento');
        $EnLetras = New EnLetras();
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $cobro = $this->AutoLoadModel('ordencobro');
        $idDoc = $_REQUEST['id'];
        $buscaDocumento = $documento->buscaDocumento($idDoc, "");
        //obtemos numero de boleta y serie
        if (!empty($_REQUEST['id']) && !empty($buscaDocumento) && $_REQUEST['id'] > 0 && $buscaDocumento[0]['nombredoc'] == 2 && $buscaDocumento[0]['esAnulado'] != 1) {
            $numeroFactura = $buscaDocumento[0]['numdoc'];
            $serieFactura = str_pad($buscaDocumento[0]['serie'], 3, '0', STR_PAD_LEFT);
            //buscamos la guia de remision que le pertenece en caso que lo hubiera
            $filtro = "nombredoc=4";
            $dataGuia = $documento->buscadocumentoxordenventaPrimero($buscaDocumento[0]['idordenventa'], $filtro);
            $numeroRelacionado = $dataGuia[0]['numdoc'];
            $tipodocumentorelacionado = $dataGuia[0]['nombredoc'];
            //*********************//
            $datadocumento = $pdf->buscarxOrdenVenta($buscaDocumento[0]['idordenventa']);
            if (!empty($buscaDocumento[0]['nombrecliente'])) {
                $datadocumento[0]['razonsocial'] = $buscaDocumento[0]['nombrecliente'];
                $datadocumento[0]['direccion_envio'] = '';
            }
            $datadocumento[0]['numeroRelacionado'] = $numeroRelacionado;
            $datadocumento[0]['simbolo'] = $dataGuia[0]['simbolo'];
            $datadocumento[0]['nombresimbolo'] = $dataGuia[0]['nombre'];
            $datadocumento[0]['numeroFactura'] = $numeroFactura;
            $datadocumento[0]['serieFactura'] = $serieFactura;
            $datadocumento[0]['fecha'] = date('d/m/Y');
            $datadocumento[0]['referencia'] = 'VEN: ' . $datadocumento[0]['idvendedor'] . ' DC: ' . $datadocumento[0]['idordenventa'];
            $data = $pdf->buscarDetalleOrdenVenta($buscaDocumento[0]['idordenventa']);
            $cantidad = count($data);
            $cantidadDocumentos = 0;
            $maximoItem = $this->configIni("MaximoItem", "Boleta");
            if ($cantidad % $maximoItem == 0) {
                $cantidadDocumentos = $cantidad / $maximoItem;
            } elseif ($cantidad % $maximoItem != 0) {
                $cantidadDocumentos = floor($cantidad / $maximoItem) + 1;
            }
            //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
            $dataV['esimpreso'] = 1;
            $dataV['CantidadDocumentos'] = $cantidadDocumentos;
            $dataV['numerorelacionado'] = $numeroRelacionado;
            $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $documento->actualizarDocumento($dataV, $filtro);
            $datos['hojas'] = $cantidadDocumentos;
            $datos['Boleta'] = $datadocumento;
            $datos['DetalleBoleta'] = $data;
            $datos['letras'] = $EnLetras;
            $datos['mes'] = $meses[date('n')];
            $datos['maximoItem'] = $maximoItem;
            $this->view->show('/documento/generaBoleta.phtml', $datos);
        }
    }

    function generaNotaCreditoTXT() {
        $pdf = $this->AutoLoadModel('pdf');
        $documento = $this->AutoLoadModel('documento');
        $devolucion = $this->AutoLoadModel('devolucion');
        $cantidadDocumentos = 0;
        $maximoItem = $this->configIni("MaximoItem", "NotaCredito");
        $idDoc = $_REQUEST['iddocumentogeneral'];
        $buscaDocumento = $documento->buscaDocumento($idDoc, "");
        if (!empty($idDoc) && !empty($buscaDocumento) && $idDoc > 0 && $buscaDocumento[0]['nombredoc'] == 5 && $buscaDocumento[0]['esAnulado'] != 1 && $buscaDocumento[0]['esCargado'] != 1) {
            $archivoConfig = parse_ini_file("config.ini", true);
            $rutasFE = $archivoConfig['RutasFE'];
            $archivo = "NC-" . $idDoc . ".txt";
            $ruta = $rutasFE[3] . $archivo;
            $crea = fopen($ruta, "w") or die("Problemas en la creacion");
            $dataV['numdoc'] = $documento->ultimoCorrelativoElectronico(1, 5);
            $numeroNC = $dataV['numdoc'];
            $serieNC = str_pad($buscaDocumento[0]['serie'], 3, '0', STR_PAD_LEFT);
            fputs($crea, "A;Serie;;F" . $serieNC);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;Correlativo;;" . str_pad($numeroNC, 8, "0", STR_PAD_LEFT));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RznSocEmis;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;CODI_EMPR;;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RUTEmis;;20509811858");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirEmis;;JR. ALFEREZ F.RICARDO HERRERA NRO. 665 (ALT. CDRA 13 DE LA AV.ARGENTINA) LIMA - LIMA - LIMA");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;ComuEmis;;140101");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;NomComer;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoDTE;;07");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoRutReceptor;;6");
            fputs($crea, chr(13) . chr(10));
            $iddevolucion = $buscaDocumento[0]['iddevolucion'];
            $concepto = $buscaDocumento[0]['concepto'];
            $importe = $buscaDocumento[0]['montofacturado'];
            $filtro = "nombredoc=1";
            if ($buscaDocumento[0]['electronico'] == 0)
                $dataGuia = $documento->buscadocumentoxordenventaPrimero($buscaDocumento[0]['idordenventa'], $filtro);
            else
                $dataGuia = $documento->buscadocumentoxRelacionado($buscaDocumento[0]['idordenventa'], $buscaDocumento[0]['idRelacionado'], $filtro);
            $datadocumento = $pdf->buscarxOrdenVenta($buscaDocumento[0]['idordenventa']);
            $datadocumento[0]['referencia'] = '#' . $datadocumento[0]['idvendedor'] . ' [' . $datadocumento[0]['codigov'] . ']';
            fputs($crea, "A;RUTRecep;;" . $datadocumento[0]['ruc']);
            fputs($crea, chr(13) . chr(10));
            $datadocumento[0]['razonsocial'] = html_entity_decode(trim($datadocumento[0]['razonsocial']), ENT_QUOTES, 'UTF-8');
            $datadocumento[0]['razonsocial'] = iconv(mb_detect_encoding($datadocumento[0]['razonsocial']), "cp1252", $datadocumento[0]['razonsocial']);
            fputs($crea, "A;RznSocRecep;;" . $datadocumento[0]['razonsocial']);
            fputs($crea, chr(13) . chr(10));
            $datadocumento[0]['direccion_envio'] = html_entity_decode(trim($datadocumento[0]['direccion_envio']) . " - " . $datadocumento[0]['nombredepartamento'] . ' - ' . $datadocumento[0]['nombreprovincia'] . ' - ' . $datadocumento[0]['nombredistrito'], ENT_QUOTES, 'UTF-8');
            $datadocumento[0]['direccion_envio'] = iconv(mb_detect_encoding($datadocumento[0]['direccion_envio']), "cp1252", $datadocumento[0]['direccion_envio']);
            fputs($crea, "A;DirRecep;;" . $datadocumento[0]['direccion_envio']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoMoneda;;" . $datadocumento[0]['tipomoneda']);
            fputs($crea, chr(13) . chr(10));
            if (!empty($iddevolucion) && $concepto == 1) {
                $data = $devolucion->listaDevolucionParaImpresion($iddevolucion);
                $cantidad = count($data);
                $sustento = "Por Devolucion";
                $TipoNotaCredito = "05";
            } elseif (empty($iddevolucion) && $concepto == 2) {
                $data[0]['nompro'] = "Por Diferencia de Precio";
                $data[0]['precio'] = $importe;
                $data[0]['cantidad'] = 1;
                $data[0]['unidadmedida'] = 7;
                $data[0]['codigopa'] = "NC-DP";
                $data[0]['codigosunat'] = "84121705";
                $cantidad = 1;
                $sustento = "POR PRECIO";
                $TipoNotaCredito = "04";
            }
            fputs($crea, "A;Sustento;;" . $sustento);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoNotaCredito;;" . $TipoNotaCredito);
            fputs($crea, chr(13) . chr(10));
            $importetotal = 0;
            for ($i = 0; $i < $cantidad; $i++) {
                if ($data[$i]['precio']*$data[$i]['cantidad'] > 0.05) {
                    $importetotal += ($data[$i]['precio']) * ($data[$i]['cantidad']);
                } else {
                    $data[$i]['precio'] = 0;
                }
            }
            $totaligv = $importetotal - ($importetotal / 1.18);
            fputs($crea, "A;MntNeto;;" . round($importetotal - $totaligv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntExe;;0.00");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntExo;;0.00");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotalIgv;;" . round($totaligv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotal;;" . round($importetotal, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;FchEmis;;" . date('Y-m-d'));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;HoraEmision;;" . date('H:i:s')); // este campo es nuevo
            fputs($crea, chr(13) . chr(10)); // este campo es nuevo
            fputs($crea, "A;CodigoLocalAnexo;;0000"); // este campo es nuevo
            fputs($crea, chr(13) . chr(10)); // este campo es nuevo
            fputs($crea, "A2;CodigoImpuesto;1;1000");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;MontoImpuesto;1;" . round($totaligv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;TasaImpuesto;1;18");
            fputs($crea, chr(13) . chr(10));
            $nroLinea = 1;
            for ($i = 0; $i < $cantidad; $i++) {
                if ($data[$i]['precio'] > 0) {
                    fputs($crea, "B;NroLinDet;" . $nroLinea . ";" . $nroLinea);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;QtyItem;" . $nroLinea . ";" . $data[$i]['cantidad']);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;UnmdItem;" . $nroLinea . ";" . $pdf->enlazarUnidadesSunat($data[$i]['unidadmedida']));
                    fputs($crea, chr(13) . chr(10));
                    $data[$i]['codigopa'] = html_entity_decode(trim($data[$i]['codigopa']), ENT_QUOTES, 'UTF-8');
                    $data[$i]['codigopa'] = iconv(mb_detect_encoding($data[$i]['codigopa']), "cp1252", $data[$i]['codigopa']);
                    fputs($crea, "B;VlrCodigo;" . $nroLinea . ";" . $data[$i]['codigopa']);
                    fputs($crea, chr(13) . chr(10));
                    $data[$i]['nompro'] = html_entity_decode(trim($data[$i]['nompro']), ENT_QUOTES, 'UTF-8');
                    $data[$i]['nompro'] = iconv(mb_detect_encoding($data[$i]['nompro']), "cp1252", $data[$i]['nompro']);
                    fputs($crea, "B;NmbItem;" . $nroLinea . ";" . $data[$i]['nompro']);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;PrcItem;" . $nroLinea . ";" . round($data[$i]['precio'], 2));
                    fputs($crea, chr(13) . chr(10));
                    $totaligv = $data[$i]['precio'] - ($data[$i]['precio'] / 1.18);
                    fputs($crea, "B;PrcItemSinIgv;" . $nroLinea . ";" . round($data[$i]['precio'] - $totaligv, 2));
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;MontoItem;" . $nroLinea . ";" . round(($data[$i]['precio'] - $totaligv) * $data[$i]['cantidad'], 2));
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;IndExe;" . $nroLinea . ";10");
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;CodigoTipoIgv;" . $nroLinea . ";1000");                
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;TasaIgv;" . $nroLinea . ";18");
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "B;ImpuestoIgv;" . $nroLinea . ";" . round($totaligv*$data[$i]['cantidad'], 2));
                    fputs($crea, chr(13) . chr(10));
                    $tempCodigosunat = $data[$i]['codigosunat'];
                    if (strlen($tempCodigosunat) != 8) {
                        $tempCodigosunat = $pdf->codigoSunatxlinea($data[$i]['idlinea']);
                    }
                    fputs($crea, "B;CodigoProductoSunat;" . $nroLinea . ";" . $tempCodigosunat);
                    fputs($crea, chr(13) . chr(10));
                    $nroLinea++;
                }
            }
            fputs($crea, "D;NroLinRef;1;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "D;TpoDocRef;1;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "D;SerieRef;1;" . ($dataGuia[0]['electronico'] == 1 ? 'F' : '') . str_pad($dataGuia[0]['serie'], 3, "0", STR_PAD_LEFT));
            fputs($crea, chr(13) . chr(10));
            $correlativoFolios = "";
            if ($dataGuia[0]['electronico'] == 1) {
                $correlativoFolios = str_pad($dataGuia[0]['numdoc'], 8, "0", STR_PAD_LEFT);
            } else {
                $CorrelativosArray = explode('-', $dataGuia[0]['numdoc']);
                if ($buscaDocumento[0]['nroSeleccion'] > 0) {
                    $CorrelativosArray[$buscaDocumento[0]['nroSeleccion']] = substr_replace($CorrelativosArray[0], $CorrelativosArray[$buscaDocumento[0]['nroSeleccion']], strlen($CorrelativosArray[0]) - strlen($CorrelativosArray[$buscaDocumento[0]['nroSeleccion']]), strlen($CorrelativosArray[$buscaDocumento[0]['nroSeleccion']]));
                }
                $correlativoFolios = str_pad($CorrelativosArray[$buscaDocumento[0]['nroSeleccion']], 6, "0", STR_PAD_LEFT);
            }
            fputs($crea, "D;FolioRef;1;" . $correlativoFolios);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;01;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;01;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;01;" . $datadocumento[0]['referencia']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;02;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;02;02");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;02;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;03;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;03;03");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;03;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;04;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;04;04");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;04;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;05;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;05;05");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;05;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;06;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;06;06");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;06;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;07;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;07;07");
            fputs($crea, chr(13) . chr(10));
            $EnLetras = New EnLetras();
            fputs($crea, "E;DescripcionAdicSunat;07;" . $EnLetras->ValorEnLetras(round($importetotal, 2), $datadocumento[0]['nombremoneda']));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;08;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;08;08");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;08;" . $dataGuia[0]['fechadoc']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;NroLinMail;1;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;MailEnvi;1;documentoselectronicos@cpoweracoustik.com");
            if (!empty($datadocumento[0]['email'])) {
                $arrayEmail = explode(' - ', $datadocumento[0]['email']);
                $itememail = 2;
                for ($email = 0; $email < count($arrayEmail) && $email < 3; $email++) {
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;NroLinMail;" . $itememail . ";" . $itememail);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;MailEnvi;" . $itememail . ";" . $arrayEmail[$email]);
                    $itememail++;
                }
            }
            $dataV['esCargado'] = 1;
            $dataV['fechadoc'] = date('Y-m-d');
            $dataV['CantidadDocumentos'] = 1;
            $dataV['numerorelacionado'] = $dataGuia[0]['numdoc'];
            $dataV['tipoDocumentoRelacionado'] = 1;
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $documento->actualizarDocumento($dataV, $filtro);
            fclose($crea);
            $resp['rspta'] = 1;
            $resp['correlativo'] = $numeroNC;
        } else {
            if ($buscaDocumento[0]['esCargado'] == 1) {
                $resp['rspta'] = 2;
            } else {
                $resp['rspta'] = 0;
            }
        }
        header('Content-type: application/json; charset=cp1252');
        echo json_encode($resp);
    }


    function generaNotaCredito() {
        $this->view->template = 'impresion';
        $pdf = $this->AutoLoadModel('pdf');
        $documento = $this->AutoLoadModel('documento');
        $devolucion = $this->AutoLoadModel('devolucion');
        $cantidadDocumentos = 0;
        $maximoItem = $this->configIni("MaximoItem", "NotaCredito");
        $idDoc = $_REQUEST['id'];
        //recuperamos la orden de venta
        $buscaDocumento = $documento->buscaDocumento($idDoc, "");
        if (!empty($_REQUEST['id']) && !empty($buscaDocumento) && $_REQUEST['id'] > 0 && $buscaDocumento[0]['nombredoc'] == 5 && $buscaDocumento[0]['esAnulado'] != 1) {
            $iddevolucion = $buscaDocumento[0]['iddevolucion'];
            $concepto = $buscaDocumento[0]['concepto'];
            $importe = $buscaDocumento[0]['montofacturado'];
            //buscamos la Factura  que le pertenece
            $filtro = "nombredoc=1";
            $dataGuia = $documento->buscadocumentoxordenventaPrimero($buscaDocumento[0]['idordenventa'], $filtro);
            $numeroRelacionado = $dataGuia[0]['numdoc'];
            $tipodocumentorelacionado = $dataGuia[0]['nombredoc'];
            $serieFactura = $dataGuia[0]['serie'];
            $fechaD = date('d/m/Y', strtotime($dataGuia[0]['fechacreacion']));
            //Buscamos la devolucion que tiene
            //*********************//
            $datadocumento = $pdf->buscarxOrdenVenta($buscaDocumento[0]['idordenventa']);
            $datadocumento[0]['numeroRelacionado'] = $numeroRelacionado;
            $datadocumento[0]['simbolo'] = $dataGuia[0]['simbolo'];
            $datadocumento[0]['nombresimbolo'] = $dataGuia[0]['nombre'];
            $datadocumento[0]['fechaFactura'] = $fechaD;
            $datadocumento[0]['serieFactura'] = $serieFactura;
            $datadocumento[0]['fecha'] = date('d/m/Y');
            $datadocumento[0]['referencia'] = 'VEN: ' . $datadocumento[0]['idvendedor'] . ' DC: ' . $datadocumento[0]['idordenventa'];
            //1 es devolucion y 2 para diferencia de precio
            if (!empty($iddevolucion) && $concepto == 1) {
                $data = $devolucion->listaDevolucionParaImpresion($iddevolucion);
                /* echo '<pre>';
                  print_r($data);
                  exit; */
                $cantidad = count($data);
                if ($cantidad % $maximoItem == 0) {
                    $cantidadDocumentos = $cantidad / $maximoItem;
                } elseif ($cantidad % $maximoItem != 0) {
                    $cantidadDocumentos = floor($cantidad / $maximoItem) + 1;
                }
                $motivo = "POR DEVOLUCION";
            } elseif (empty($iddevolucion) && $concepto == 2) {
                $data[0]['nompro'] = "Por Diferencia de Precio";
                $data[0]['precio'] = $importe;
                $data[0]['cantidad'] = 1;
                $cantidadDocumentos = 1;
                $motivo = "POR PRECIO";
            }
            //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
            $dataV['esimpreso'] = 1;
            $dataV['CantidadDocumentos'] = $cantidadDocumentos;
            $dataV['numerorelacionado'] = $numeroRelacionado;
            $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $documento->actualizarDocumento($dataV, $filtro);
            $datos['hojas'] = $cantidadDocumentos;
            $datos['maximoItem'] = $maximoItem;
            $datos['NotaCredito'] = $datadocumento;
            $datos['DetalleNCredito'] = $data;
            $datos['motivo'] = $motivo;
            $this->view->show('/documento/generaNotaCredito.phtml', $datos);
        }
    }

    public function generarPercepcionTxt() {
        $idDoc = $_REQUEST['iddocumentogeneral'];
        $documento = $this->AutoLoadModel('documento');
        $buscaDocumento = $documento->buscaDocumento($idDoc, "nombredoc=10 and numdoc='' and esCargado=0 and esAnulado=0");
        if (!empty($buscaDocumento)) {
            $pdf = $this->AutoLoadModel('pdf');
            $dataordenventa = $pdf->buscarxOrdenVenta($buscaDocumento[0]['idordenventa']);
            $archivoConfig = parse_ini_file("config.ini", true);
            $rutasFE = $archivoConfig['RutasFE'];
            $archivo = "P-" . $idDoc . ".txt";
            $ruta = $rutasFE[5] . $archivo;
            $crea = fopen($ruta, "w") or die("Problemas en la creacion");
            $correlativo = $documento->ultimoCorrelativoElectronico($buscaDocumento[0]['serie'], 10);
            fputs($crea, "A;CODI_EMPR;;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoDTE;;40");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;Serie;;P" . str_pad($buscaDocumento[0]['serie'], 3, "0", STR_PAD_LEFT));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;Correlativo;;" . str_pad($correlativo, 8, "0", STR_PAD_LEFT));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;FchEmis;;" . date('Y-m-d'));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RUTEmis;;20509811858");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoRucEmis;;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;NomComer;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RznSocEmis;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;ComuEmis;;140101");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirEmis;;JR. ALFEREZ F.RICARDO HERRERA NRO. 665 (ALT. CDRA 13 DE LA AV.ARGENTINA)");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;UrbanizaEmis;;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;ProviEmis;;LIMA");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DeparEmis;;LIMA");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DistriEmis;;LIMA");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;PaisEmis;;PE");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoRutReceptor;;6");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RUTRecep;;" . $dataordenventa[0]['ruc']);
            fputs($crea, chr(13) . chr(10));
            $dataordenventa[0]['razonsocial'] = html_entity_decode(trim($dataordenventa[0]['razonsocial']), ENT_QUOTES, 'UTF-8');
            $dataordenventa[0]['razonsocial'] = iconv(mb_detect_encoding($dataordenventa[0]['razonsocial']), "cp1252", $dataordenventa[0]['razonsocial']);
            fputs($crea, "A;RznSocRecep;;" . $dataordenventa[0]['razonsocial']);
            fputs($crea, chr(13) . chr(10));
            $localizacion = $pdf->Ubigeo_localizacion($dataordenventa[0]['iddistrito']);
            fputs($crea, "A;DirRecepUbiGeo;;" . $localizacion[0]['codubigeo']);
            fputs($crea, chr(13) . chr(10));
            $dataordenventa[0]['direccion_envio'] = html_entity_decode(trim($dataordenventa[0]['direccion_envio']), ENT_QUOTES, 'UTF-8');
            $dataordenventa[0]['direccion_envio'] = iconv(mb_detect_encoding($dataordenventa[0]['direccion_envio']), "cp1252", $dataordenventa[0]['direccion_envio']);
            fputs($crea, "A;DirRecep;;" . $dataordenventa[0]['direccion_envio']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirRecepUrbaniza;;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirRecepProvincia;;" . $localizacion[0]['provincia']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirRecepDepartamento;;" . $localizacion[0]['provincia']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirRecepDistrito;;" . $localizacion[0]['distrito']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirRecepCodPais;;PE");
            fputs($crea, chr(13) . chr(10));
            if ($buscaDocumento[0]['porcentajefactura'] == 2) {
                fputs($crea, "A;CodPercepcion;;01");
            } else if ($buscaDocumento[0]['porcentajefactura'] == 1) {
                fputs($crea, "A;CodPercepcion;;02");
            } else {
                fputs($crea, "A;CodPercepcion;;03");
            }            
            fputs($crea, chr(13) . chr(10));
            $lstFacturas = $documento->getDetallePercepcion($buscaDocumento[0]['idordenventa'], " and d.esCargado=1 and d.idRelacionado='" . $idDoc . "' and d.esAnulado=0");
            $tamano = count($lstFacturas);
            $montoPercepcion = 0;
            $montoFacturado = 0;
            $fechaperiodo = "";       
            $tipodeCambio = $this->AutoLoadModel('TipoCambio');     
            for ($i = 0; $i < $tamano; $i++) {
                $tcVentas = 1;
                if ($dataordenventa[0]['tipomoneda'] != 'PEN') {
                    $tcVentas = round($tipodeCambio->consultatipocambioXfecha($lstFacturas[$i]['fechadoc']), 2);
                }  /*              
                if ($lstFacturas[$i]['porcentajefactura'] != "") {
                    $lstFacturas[$i]['montofacturado'] = (($lstFacturas[$i]['montofacturado'] * $lstFacturas[$i]['porcentajefactura']) / 100);
                }*/
                $montoPercepcion += round($lstFacturas[$i]['montofacturado'] * $lstFacturas[$i]['percepcion'] * $tcVentas, 2);
                $montoFacturado += round($lstFacturas[$i]['montofacturado'] * $tcVentas, 2);
                
                $fechaperiodo = $lstFacturas[$i]['fechadoc'];
            }
            fputs($crea, "A;MntImpPercepcion;;" . $buscaDocumento[0]['porcentajefactura']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;ObsPercepcion;;Se emite con facturas del periodo " . DateTime::createFromFormat('Y-m-d', $fechaperiodo)->format('m/Y'));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntPercepcion;;" . round($montoPercepcion, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotalMasPercepcion;;" . round($montoFacturado+$montoPercepcion, 2));
            fputs($crea, chr(13) . chr(10));
            $nrolinea = 1;
            for ($i = 0; $i < $tamano; $i++) {
                fputs($crea, "D;NroLinRef;" . $nrolinea . ";" . str_pad($nrolinea, 2, "0", STR_PAD_LEFT));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;TpoDocRef;" . $nrolinea . ";01");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;SerieRef;" . $nrolinea . ";F" . str_pad($lstFacturas[$i]['serie'], 3, "0", STR_PAD_LEFT));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;FolioRef;" . $nrolinea . ";" . str_pad($lstFacturas[$i]['numdoc'], 8, "0", STR_PAD_LEFT));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;FechEmisDocRef;" . $nrolinea . ";" . $lstFacturas[$i]['fechadoc']);
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;MntTotalDocRef;" . $nrolinea . ";" . round($lstFacturas[$i]['montofacturado'], 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;MonedaDocRef;" . $nrolinea . ";" . $dataordenventa[0]['tipomoneda']);
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;FechOperacion;" . $nrolinea . ";" . $lstFacturas[$i]['fechadoc']);
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;NroOperacion;" . $nrolinea . ";1");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;ImporteOperacion;" . $nrolinea . ";" . round($lstFacturas[$i]['montofacturado'], 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;MonedaOperacion;" . $nrolinea . ";" . $dataordenventa[0]['tipomoneda']);
                fputs($crea, chr(13) . chr(10));
                $tcVentas = 1;
                if ($dataordenventa[0]['tipomoneda'] != 'PEN') {                    
                    $tcVentas = round($tipodeCambio->consultatipocambioXfecha($lstFacturas[$i]['fechadoc']), 2);
                }
                fputs($crea, "D;ImporteMovimiento;" . $nrolinea . ";" . round($lstFacturas[$i]['montofacturado'] * $lstFacturas[$i]['percepcion'] * $tcVentas, 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;MonedaMovimiento;" . $nrolinea . ";PEN");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;FechaMovimiento;" . $nrolinea . ";" . date('Y-m-d'));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;TotalMovimiento;" . $nrolinea . ";" . round(($lstFacturas[$i]['montofacturado'] + $lstFacturas[$i]['montofacturado'] * $lstFacturas[$i]['percepcion']) * $tcVentas, 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "D;Moneda;" . $nrolinea . ";PEN");
                fputs($crea, chr(13) . chr(10));
                if ($dataordenventa[0]['tipomoneda'] == 'USD') {
                    fputs($crea, "D;MonedaReferencia;" . $nrolinea . ";USD");
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "D;MonedaObjetivo;" . $nrolinea . ";PEN");
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "D;TipoCambio;" . $nrolinea . ";" . round($tcVentas, 2));
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "D;FechTipoCambio;" . $nrolinea . ";" . $lstFacturas[$i]['fechadoc']);
                    fputs($crea, chr(13) . chr(10));
                } 
                $nrolinea++;
            }
            
            fputs($crea, "M;NroLinMail;1;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;MailEnvi;1;documentoselectronicos@cpoweracoustik.com");
            if (!empty($dataordenventa[0]['email'])) {
                $arrayEmail = explode(' - ', $dataordenventa[0]['email']);
                $itememail = 2;
                for ($email = 0; $email < count($arrayEmail) && $email < 3; $email++) {
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;NroLinMail;" . $itememail . ";" . $itememail);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;MailEnvi;" . $itememail . ";" . $arrayEmail[$email]);
                    $itememail++;
                }
            }
            $dataV['esCargado'] = 1;
            $dataV['CantidadDocumentos'] = 1;
            $dataV['numdoc'] = $correlativo;
            $dataV['tipoDocumentoRelacionado'] = 1;
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $documento->actualizarDocumento($dataV, $filtro);
            $resp['rspta'] = 1;
            fclose($crea);
        } else {
            if ($buscaDocumento[0]['esCargado'] == 1) {
                $resp['rspta'] = 2;
            } else {
                $resp['rspta'] = 0;
            }
        }
        header('Content-type: application/json; charset=cp1252');
        echo json_encode($resp);
    }
    
    function generaNotaDebitoTXT() {
        $pdf = $this->AutoLoadModel('pdf');
        $documento = $this->AutoLoadModel('documento');
        $devolucion = $this->AutoLoadModel('devolucion');
        $cantidadDocumentos = 0;
        $maximoItem = $this->configIni("MaximoItem", "NotaDebito");
        $idDoc = $_REQUEST['iddocumentogeneral'];
        $buscaDocumento = $documento->buscaDocumento($idDoc, "");
        if (!empty($idDoc) && !empty($buscaDocumento) && $idDoc > 0 && $buscaDocumento[0]['nombredoc'] == 6 && $buscaDocumento[0]['esAnulado'] != 1 && $buscaDocumento[0]['esCargado'] != 1) {
            $archivoConfig = parse_ini_file("config.ini", true);
            $rutasFE = $archivoConfig['RutasFE'];
            $archivo = "ND-" . $idDoc . ".txt";
            $ruta = $rutasFE[4] . $archivo;
            $crea = fopen($ruta, "w") or die("Problemas en la creacion");
            $numeroNC = $buscaDocumento[0]['numdoc'];
            $serieNC = str_pad($buscaDocumento[0]['serie'], 3, '0', STR_PAD_LEFT);
            fputs($crea, "A;Serie;;F" . $serieNC);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;Correlativo;;" . str_pad($numeroNC, 8, "0", STR_PAD_LEFT));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RznSocEmis;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;CODI_EMPR;;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;RUTEmis;;20509811858");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;DirEmis;;JR. ALFEREZ F.RICARDO HERRERA NRO. 665 (ALT. CDRA 13 DE LA AV.ARGENTINA) LIMA - LIMA - LIMA");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;ComuEmis;;140101");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;NomComer;;CORPORACION POWER ACOUSTIK S.A.C.");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoDTE;;08");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoRutReceptor;;6");
            fputs($crea, chr(13) . chr(10));
            $concepto = $buscaDocumento[0]['concepto'];
            $importe = $buscaDocumento[0]['montofacturado'];
            $filtro = "nombredoc=1";
            if ($buscaDocumento[0]['electronico'] == 0)
                $dataGuia = $documento->buscadocumentoxordenventaPrimero($buscaDocumento[0]['idordenventa'], $filtro);
            else
                $dataGuia = $documento->buscadocumentoxRelacionado($buscaDocumento[0]['idordenventa'], $buscaDocumento[0]['idRelacionado'], $filtro);
            $datadocumento = $pdf->buscarxOrdenVenta($buscaDocumento[0]['idordenventa']);
            $datadocumento[0]['referencia'] = '#' . $datadocumento[0]['idvendedor'] . ' [' . $datadocumento[0]['codigov'] . ']';
            fputs($crea, "A;RUTRecep;;" . $datadocumento[0]['ruc']);
            fputs($crea, chr(13) . chr(10));
            $datadocumento[0]['razonsocial'] = html_entity_decode(trim($datadocumento[0]['razonsocial']), ENT_QUOTES, 'UTF-8');
            $datadocumento[0]['razonsocial'] = iconv(mb_detect_encoding($datadocumento[0]['razonsocial']), "cp1252", $datadocumento[0]['razonsocial']);
            fputs($crea, "A;RznSocRecep;;" . $datadocumento[0]['razonsocial']);
            fputs($crea, chr(13) . chr(10));
            
            $datadocumento[0]['direccion_envio'] = html_entity_decode(trim($datadocumento[0]['direccion_envio']) . " - " . $datadocumento[0]['nombredepartamento'] . ' - ' . $datadocumento[0]['nombreprovincia'] . ' - ' . $datadocumento[0]['nombredistrito'], ENT_QUOTES, 'UTF-8');
            $datadocumento[0]['direccion_envio'] = iconv(mb_detect_encoding($datadocumento[0]['direccion_envio']), "cp1252", $datadocumento[0]['direccion_envio']);
            fputs($crea, "A;DirRecep;;" . $datadocumento[0]['direccion_envio']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoMoneda;;" . $datadocumento[0]['tipomoneda']);
            fputs($crea, chr(13) . chr(10));
            $archivoConfig = parse_ini_file("config.ini", true);
            $sustento = $archivoConfig['SustentoDebito'];
            $codigo = $archivoConfig['CodigoSustento'];
            $codigo = $codigo[$buscaDocumento[0]['concepto']];
            $codigoSunat = $archivoConfig['CodigoSustentoSunat'][$buscaDocumento[0]['concepto']];
            fputs($crea, "A;Sustento;;" . $sustento[$buscaDocumento[0]['concepto']]);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;TipoNotaCredito;;02");
            fputs($crea, chr(13) . chr(10));
            $totaligv = $buscaDocumento[0]['montofacturado'] - ($buscaDocumento[0]['montofacturado'] / 1.18);
            fputs($crea, "A;MntNeto;;" . round($buscaDocumento[0]['montofacturado'] - $totaligv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntExe;;0.00");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntExo;;0.00");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotalIgv;;" . round($totaligv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;MntTotal;;" . round($buscaDocumento[0]['montofacturado'], 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;FchEmis;;" . date('Y-m-d'));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A;HoraEmision;;" . date('H:i:s')); //este es nuevo
            fputs($crea, chr(13) . chr(10)); //este es nuevo
            fputs($crea, "A;CodigoLocalAnexo;1;0000"); //este es nuevo
            fputs($crea, chr(13) . chr(10)); //este es nuevo
            fputs($crea, "A2;CodigoImpuesto;1;1000");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;MontoImpuesto;1;" . round($totaligv, 2));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "A2;TasaImpuesto;1;18");
            fputs($crea, chr(13) . chr(10));
            $debitomodel = new Notedebito();
            $detallesDebito = $debitomodel->listarXDocumento($idDoc);
            $nroLinea = 1;
            for ($i = 0; $i < count($detallesDebito); $i++) {
                fputs($crea, "B;NroLinDet;" . $nroLinea . ";" . $nroLinea);
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;QtyItem;" . $nroLinea . ";" . $detallesDebito[$i]['cantidad']);
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;UnmdItem;" . $nroLinea . ";NIU");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;VlrCodigo;" . $nroLinea . ";" . $codigo);
                fputs($crea, chr(13) . chr(10));
                $detallesDebito[$i]['descripcion'] = html_entity_decode(trim($detallesDebito[$i]['descripcion']), ENT_QUOTES, 'UTF-8');
                $detallesDebito[$i]['descripcion'] = iconv(mb_detect_encoding($detallesDebito[$i]['descripcion']), "cp1252", $detallesDebito[$i]['descripcion']);
                fputs($crea, "B;NmbItem;" . $nroLinea . ";" . $detallesDebito[$i]['descripcion']);
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;PrcItem;" . $nroLinea . ";" . round($detallesDebito[$i]['preciouni'], 2));
                fputs($crea, chr(13) . chr(10));
                $totaligv = $detallesDebito[$i]['preciouni'] - ($detallesDebito[$i]['preciouni'] / 1.18);
                fputs($crea, "B;PrcItemSinIgv;" . $nroLinea . ";" . round($detallesDebito[$i]['preciouni'] - $totaligv, 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;MontoItem;" . $nroLinea . ";" . round(($detallesDebito[$i]['preciouni'] - $totaligv) * $detallesDebito[$i]['cantidad'], 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;IndExe;" . $nroLinea . ";10");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;CodigoTipoIgv;" . $nroLinea . ";1000");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;TasaIgv;" . $nroLinea . ";18");
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;ImpuestoIgv;" . $nroLinea . ";" . round($totaligv, 2));
                fputs($crea, chr(13) . chr(10));
                fputs($crea, "B;CodigoProductoSunat;" . $nroLinea . ";" . $codigoSunat); //este es nuevo
                fputs($crea, chr(13) . chr(10));
                $nroLinea++;
            }
            fputs($crea, "D;NroLinRef;1;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "D;TpoDocRef;1;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "D;SerieRef;1;" . ($dataGuia[0]['electronico'] == 1 ? 'F' : '') . str_pad($dataGuia[0]['serie'], 3, "0", STR_PAD_LEFT));
            fputs($crea, chr(13) . chr(10));
            $correlativoFolios = "";
            if ($dataGuia[0]['electronico'] == 1) {
                $correlativoFolios = str_pad($dataGuia[0]['numdoc'], 8, "0", STR_PAD_LEFT);
            } else {
                $CorrelativosArray = explode('-', $dataGuia[0]['numdoc']);
                if ($buscaDocumento[0]['nroSeleccion'] > 0) {
                    $CorrelativosArray[$buscaDocumento[0]['nroSeleccion']] = substr_replace($CorrelativosArray[0], $CorrelativosArray[$buscaDocumento[0]['nroSeleccion']], strlen($CorrelativosArray[0]) - strlen($CorrelativosArray[$buscaDocumento[0]['nroSeleccion']]), strlen($CorrelativosArray[$buscaDocumento[0]['nroSeleccion']]));
                }
                $correlativoFolios = str_pad($CorrelativosArray[$buscaDocumento[0]['nroSeleccion']], 6, "0", STR_PAD_LEFT);
            }
            fputs($crea, "D;FolioRef;1;" . $correlativoFolios);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;01;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;01;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;01;" . $datadocumento[0]['referencia']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;02;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;02;02");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;02;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;03;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;03;03");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;03;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;04;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;04;04");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;04;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;05;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;05;05");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;05;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;06;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;06;06");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;06;-");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;07;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;07;07");
            fputs($crea, chr(13) . chr(10));
            $EnLetras = New EnLetras();
            fputs($crea, "E;DescripcionAdicSunat;07;" . $EnLetras->ValorEnLetras(round($buscaDocumento[0]['montofacturado'], 2), $datadocumento[0]['nombremoneda']));
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;TipoAdicSunat;08;01");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;NmrLineasAdicSunat;08;08");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "E;DescripcionAdicSunat;08;" . $dataGuia[0]['fechadoc']);
            
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;NroLinMail;1;1");
            fputs($crea, chr(13) . chr(10));
            fputs($crea, "M;MailEnvi;1;documentoselectronicos@cpoweracoustik.com");
            if (!empty($datadocumento[0]['email'])) {
                $arrayEmail = explode(' - ', $datadocumento[0]['email']);
                $itememail = 2;
                for ($email = 0; $email < count($arrayEmail) && $email < 3; $email++) {
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;NroLinMail;" . $itememail . ";" . $itememail);
                    fputs($crea, chr(13) . chr(10));
                    fputs($crea, "M;MailEnvi;" . $itememail . ";" . $arrayEmail[$email]);
                    $itememail++;
                }
            }
            $dataV['esCargado'] = 1;
            $dataV['CantidadDocumentos'] = 1;
            $dataV['numerorelacionado'] = $dataGuia[0]['numdoc'];
            $dataV['tipoDocumentoRelacionado'] = 1;
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $documento->actualizarDocumento($dataV, $filtro);
            fclose($crea);
            $resp['rspta'] = 1;
            $resp['correlativo'] = str_pad($numeroNC, 8, "0", STR_PAD_LEFT);
        } else {
            if ($buscaDocumento[0]['esCargado'] == 1) {
                $resp['rspta'] = 2;
            } else {
                $resp['rspta'] = 0;
            }
        }
        header('Content-type: application/json; charset=cp1252');
        echo json_encode($resp);
    }

    
    function generaGuiaMadreTxt() {
        $idordenventa = $_REQUEST['idordenventa'];
        $sucursal = $this->AutoLoadModel('sucursal');
        $dxSucursal = $sucursal->sucursalXOrdenVenta($idordenventa);
        if (count($dxSucursal) > 0) {
            $archivoConfig = parse_ini_file("config.ini", true);
            $ruta= $archivoConfig['RutaSucursal'];
            $ruta = $ruta[1] . $dxSucursal[0]['codigo'] . '/' . $dxSucursal[0]['codigov'] . '.cpa';            
            $crea = fopen($ruta, "w") or die("Problemas en la creacion");            
            $cliente=New Cliente();;
            $actorRol=New actorRol();
            $dataCliente=$cliente->buscaxOrdenVenta($idordenventa);
            $iddespachador=$dataCliente[0]['iddespachador'];
            $idverificador=$dataCliente[0]['idverificador'];
            $idverificador2=$dataCliente[0]['idverificador2'];
            $dataDespachador=$actorRol->buscaActorxRol($iddespachador);
            $dataVerificador=$actorRol->buscaActorxRol($idverificador);
            $dataVerificador2=$actorRol->buscaActorxRol($idverificador2);
            fputs($crea, $dxSucursal[0]['idsucursal']); //wc_ordenventa
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['idmoneda']); //wc_ordenventa
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['codigov']); //wc_ordenventa
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['importeov']); //wc_ordenventa
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['fordenventa']); //wc_ordenventa
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['razonsocialtransp']); //wc_transporte 4 
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['telfonotransp']); //wc_transporte 5
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['porComision']); //wc_ordenventa 6 
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['fechadespacho']); //wc_ordenventa
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['fechavencimiento']); //wc_ordenventa 8 
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['condiciones']); //wc_ordenventa observaciones
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['nrocajas']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['nrobultos']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataDespachador[0]['nombres'] . ";" . $dataDespachador[0]['apellidopaterno'] . ";" . $dataDespachador[0]['apellidomaterno']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataVerificador[0]['nombres'] . ";" . $dataVerificador[0]['apellidopaterno'] . ";" . $dataVerificador[0]['apellidomaterno']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataVerificador2[0]['nombres'] . ";" . $dataVerificador2[0]['apellidopaterno'] . ";" . $dataVerificador2[0]['apellidomaterno']);
            fputs($crea, chr(13) . chr(10));
            fputs($crea, $dataCliente[0]['mventas']);
            $detalleOrdenVenta=new detalleOrdenVenta();
            $data=$detalleOrdenVenta->listaDetalleOrdenVentaGuia($idordenventa);
            $cantidad=count($data);	
            for($i=0;$i<$cantidad;$i++){
                fputs($crea, chr(13) . chr(10));
                $columna =  $data[$i]['idunidaddemedida'] . "[|]" . 
                            $data[$i]['unidadmedida'] . "[|]" . 
                            $data[$i]['idproducto'] . "[|]" . 
                            $data[$i]['codigopa'] . "[|]" . 
                            $data[$i]['nompro'] . "[|]" . 
                            $data[$i]['cantdespacho'] . "[|]" .  
                            $data[$i]['precioaprobado'] . "[|]" . 
                            $data[$i]['preciofinal'];
                fputs($crea, $columna);
            }
            fclose($crea);
            $resp['rspta'] = 1;
        } else {
            $resp['rspta'] = 0;
        }
        header('Content-type: application/json; charset=UTF-8');
        echo json_encode($resp);
    }

    function generaNotaDevito() {
        $this->view->template = 'impresion';
        $pdf = $this->AutoLoadModel('pdf');
        $documento = $this->AutoLoadModel('documento');
        $idDoc = $_REQUEST['id'];
        //recuperamos la orden de venta
        $buscaDocumento = $documento->buscaDocumento($idDoc, "");
        if (!empty($_REQUEST['id']) && !empty($buscaDocumento) && $_REQUEST['id'] > 0 && $buscaDocumento[0]['nombredoc'] == 6 && $buscaDocumento[0]['esAnulado'] != 1) {
            $concepto = $buscaDocumento[0]['concepto'];
            $importe = $buscaDocumento[0]['montofacturado'];
            //buscamos la Factura  que le pertenece
            $filtro = "nombredoc=1";
            $dataGuia = $documento->buscadocumentoxordenventaPrimero($buscaDocumento[0]['idordenventa'], $filtro);
            $numeroRelacionado = $dataGuia[0]['numdoc'];
            $tipodocumentorelacionado = $dataGuia[0]['nombredoc'];
            $serieFactura = $dataGuia[0]['serie'];
            //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
            $dataV['esimpreso'] = 1;
            $dataV['CantidadDocumentos'] = 1;
            $dataV['numerorelacionado'] = $numeroRelacionado;
            $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
            $filtro = "iddocumento='" . $idDoc . "'";
            $exitoE = $documento->actualizarDocumento($dataV, $filtro);
            $datadocumento = $pdf->buscarxOrdenVenta($buscaDocumento[0]['idordenventa']);
            $datadocumento[0]['numeroRelacionado'] = $numeroRelacionado;
            $datadocumento[0]['serieFactura'] = $serieFactura;
            //1 es renovado y 2 para protesto
            if ($concepto == 1) {
                $data[0]['nompro'] = "Por Gastos de Renovacion";
                $data[0]['precio'] = $importe;
                $data[0]['cantidad'] = 1;
            } elseif ($concepto == 2) {
                $data[0]['nompro'] = "Por Gastos de Protesto";
                $data[0]['precio'] = $importe;
                $data[0]['cantidad'] = 1;
            }
            $datos['maximoItem'] = 9;
            $datos['NotaDevito'] = $datadocumento;
            $datos['DetalleNDevito'] = $data;
            $this->view->show('/documento/generaNotaDevito.phtml', $datos);
        }
    }

    function anularDocumentos() {
        $documento = $this->AutoLoadModel('documento');
        $iddocumento = $_REQUEST['iddocumento'];
        $data = $documento->buscaDocumento($iddocumento, "");
        $tipodocumento = $data[0]['nombredoc'];
        $idordenventa = $data[0]['idordenventa'];
        $montofacturado = $data[0]['montofacturado'];
        $iddevolucion = $data[0]['iddevolucion'];
        $concepto = $data[0]['concepto'];
        if ($tipodocumento == 1 || $tipodocumento == 2 || $tipodocumento == 4) {
            $ordenventa = $this->AutoLoadModel('ordenventa');
            $dataEnvio['esAnulado'] = 1;
            $filtro = "iddocumento='$iddocumento'";
            $exito = $documento->actualizarDocumento($dataEnvio, $filtro);
            if ($exito) {
                if ($tipodocumento == 4) {
                    $data2['guiaremision'] = 0;
                } else {
                    $data2['esfacturado'] = 0;
                }
                $exito2 = $ordenventa->actualizaOrdenVenta($data2, $idordenventa);
                if (!$exito2) {
                    echo 'Error 1.1';
                }
            } else {
                echo 'Error 1';
            }
        } elseif ($tipodocumento == 5 || $tipodocumento == 6) {
            echo 'entro';
            $dataEnvio['esAnulado'] = 1;
            $filtro = "iddocumento='$iddocumento'";
            $exito = $documento->actualizarDocumento($dataEnvio, $filtro);
            if ($exito && $data[0]['electronico'] == 0) {
                $dataNuevo['idordenventa'] = $idordenventa;
                $dataNuevo['nombredoc'] = $tipodocumento;
                $dataNuevo['montofacturado'] = $montofacturado;
                $dataNuevo['iddevolucion'] = $iddevolucion;
                $dataNuevo['concepto'] = $concepto;
                $dataNuevo['fechadoc'] = date('Y-m-d');
                $exito2 = $documento->grabaDocumento($dataNuevo);
                if (!$exito2) {
                    echo 'Error 2.1';
                }
            } else {
                echo 'Error 2';
            }
        } elseif ($tipodocumento == 7) {
            $dataEnvio['esImpreso'] = 0;
            $filtro = "iddocumento='$iddocumento'";
            $exito2 = $documento->actualizarDocumento($dataEnvio, $filtro);
        }
        if ($exito2) {
            echo 'Correcto';
        } else {
            echo 'Error';
        }
    }

    function buscardocumentosinsaldo() {
        $documento = $this->AutoLoadModel('documento');
        $iddoc = $_REQUEST['id'];
        $filtro = "doc.esAnulado!=1 and doc.nombredoc=1";
        $data = $documento->buscaDocumentoXId($iddoc, $filtro);
        echo json_encode($data[0]);
    }

    function buscardocumento() {
        $documento = $this->AutoLoadModel('documento');
        $iddoc = $_REQUEST['id'];
        $filtro = "doc.esAnulado!=1 and doc.nombredoc=1";
        $data = $documento->buscaDocumentoXId($iddoc, $filtro);
        $ArrayFacturas = explode('-', $data[0]['numdoc']);
        $OpcSelecciones = "";
        for ($i = 0; $i < count($ArrayFacturas); $i++) {
            $OpcSelecciones .= "<option value='" . ($i + 1) . "'>" . ($i + 1) . "</option>";
        }
        $data[0]['opcSelecciones'] = $OpcSelecciones;
        $dataNotaCredito = $documento->sumaNotasCreditoXFactura($data[0]['electronico'], $data[0]['serie'], $data[0]['numdoc'], $data[0]['idordenventa'], $iddoc);
        $data[0]['saldo'] = $dataNotaCredito;
        echo json_encode($data[0]);
    }

    function buscar() {
        $documento = $this->AutoLoadModel('documento');
        $idordenventa = $_REQUEST['id'];
        $filtro = "nombredoc=1 and doc.esAnulado!=1";
        $data = $documento->buscadocumentoxordenventa($idordenventa, $filtro);
        $dataNotaCredito = $documento->sumaNotasCredito($idordenventa);
        $data[0]['saldo'] = $dataNotaCredito;
        echo json_encode($data[0]);
    }

    function mostrardocumentoelectronico() {
        $idGuia = $_REQUEST['idguia'];
        $documento = $this->AutoLoadModel('documento');
        $facturas = $documento->buscadocumentoxordenventa($idGuia, "doc.electronico=1 and doc.nombredoc=1");
        $resp['rspta'] = 0;
        if (count($facturas)) {
            $resp['serie'] = "F" . str_pad($facturas[0]['serie'], 3, '0', STR_PAD_LEFT);
            $resp['correlativo'] = str_pad($facturas[0]['numdoc'], 8, '0', STR_PAD_LEFT);
            $resp['tipo'] = $facturas[0]['nombredoc'];
            $resp['fecha'] = $facturas[0]['fechadoc'];
            $resp['facturado'] = number_format($facturas[0]['nomtofacturado'], 2);
            $resp['rspta'] = 1;
        }
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

    function guiassindocumento() {
        $model = $this->AutoLoadModel('documento');
        $pagina = (empty($_REQUEST['id']) || $_REQUEST['id'] == 'x') ? 1 : $_REQUEST['id'];
        session_start();
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_Documento'] = $_REQUEST['txtBusqueda'];
        }
        if (!empty($_REQUEST['txtFechaInicio'])) {
            $_SESSION['P_fechainicio'] = $_REQUEST['txtFechaInicio'];
        }
        if (!empty($_REQUEST['txtFechaFin'])) {
            $_SESSION['P_fechafin'] = $_REQUEST['txtFechaFin'];
        }
        if ($_REQUEST['id'] == 'x') {
            $_SESSION['P_Documento'] = "";
            $_SESSION['P_fechainicio'] = "";
            $_SESSION['P_fechafin'] = "";
        }
        $parametro = $_SESSION['P_Documento'];
        $fechaini = $_SESSION['P_fechainicio'];
        $fechafin = $_SESSION['P_fechafin'];
        $Factura = $model->listaGuiasSinDocumentos($pagina, $parametro, $fechaini, $fechafin);
        $data['retorno'] = $parametro;
        $data['fechaini'] = $fechaini;
        $data['fechafin'] = $fechafin;
        $data['Factura'] = $Factura;
        $paginacion = $model->paginadoGuiasSinDocumentos($parametro, $fechaini, $fechafin);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show('/documento/guiassindocumento.phtml', $data);
    }

    function montostotales() {
        $this->view->show('/documento/montostotales.phtml');
    }
    
    function montostotales_consultar() {
        $fechainicio = $_REQUEST['txtFechaInicio'];
        $fechafin = $_REQUEST['txtFechaFin'];
        $tipodocumento = $_REQUEST['lstTipoDocumento'] > 0 ? $_REQUEST['lstTipoDocumento'] : 1;
        $electronico = $_REQUEST['idElectronico'] == 1 ? 1 : 0;
        $fisico = $_REQUEST['idFisico'] == 1 ? 1 : 0;
        $moneda = $_REQUEST['txtMoneda'];
        $documento = $this->AutoLoadModel('documento');
        $montototal = $documento->sumarTotalxDocumento($fechainicio, $fechafin, $tipodocumento, $electronico, $fisico, $moneda);
        $arraytipoDocumento = $this->tipoDocumento();
        $resp['fechainicio'] = $fechainicio;
        $resp['fechafin'] = $fechafin;
        $acumulador = '';
        if ($fisico == 1) {
            $acumulador = 'Físico';
        }
        if ($electronico == 1) {
            $acumulador .= (!empty($acumulador) ? ' - ' : '') . 'Electrónico';
        }        
        $resp['documento'] = $arraytipoDocumento[$tipodocumento] . ' [' . $acumulador . ']';
        $nombremoneda = '';
        $simbolo = '';
        if ($moneda == 1) {
            $nombremoneda = 'Soles';
            $simbolo = 'S/ ';
        }
        if ($moneda == 2) {
            $nombremoneda = 'Dolares Americanos ';
            $simbolo = 'US $ ';
        }
        $resp['moneda'] = $nombremoneda;
        $resp['total'] = $simbolo . number_format($montototal, 2);
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }

function documentoRegistrado() {
    $data['TipoDocumentoOjalaDe']=array(0=>"Todos los Documentos",
                                        1=>"Factura",
                                        2=>"Boleta",
                                        3=>"Nota de Crédito",
                                        4=>"Nota de Débito",
                                        5=>"Perceción");
    $this->view->show('/documento/documentosRegistrados.phtml',$data);
}

function consultaDocumentoRegistrado() {
        $txtFecha = !empty($_REQUEST['txtFecha']) ? date('Y-m-d', strtotime($_REQUEST['txtFecha'])) : date('Y-m-d');        
        $idDocumemto= $_REQUEST['idTipoDocumento'];
        
        $model = $this->AutoLoadModel('documento');
        $data=$model->documentosRegistrados($txtFecha,$idDocumemto);
        
        echo "<table>";
            echo "<thead>";
                echo "<tr>";
                    echo "<th>Serie</th>";
                    echo "<th>Documento</th>";
                    echo "<th>Numero Doc</th>";
                    echo "<th>Orden Venta</th>";
                    echo "<th>Razón Social</th>";
                    echo "<th>DNI / RUC";
                    echo "<th>Monto Facturado</th>";
                    echo "<th>IGV</th>";
                    echo "<th>Fecha</th>";
                    
                echo "</tr>";
            echo "</thead>";
            
            for ($index = 0; $index < count($data); $index++) {
                echo "<tbody>";
                    echo "<tr>";
                            $letra = "";
                            if ($data[$index]['electronico'] == 1) {
                                $letra = "F";
                                if ($data[$index]['nombredoc'] == 2)
                                    $letra = "B";
                                if ($data[$index]['nombredoc'] == 10)
                                    $letra = "P";
                            }
                            echo '<td>' . $letra . (str_pad($data[$index]['serie'], 3, '0', STR_PAD_LEFT)) . '</td>';
                            echo "<td rowspan='2'>".$data[$index]['nombre']."</td>";
                            echo "<td rowspan='2'>"; echo (empty($data[$index]['numdoc']))?"<small>Sin cargar</small>":$data[$index]['numdoc']; echo "</td>";
                            echo "<td rowspan='2'>".$data[$index]['codigov']."</td>";
                            echo "<td rowspan='2'>".$data[$index]['razonsocial']."</td>";
                            if($data[$index]['nombredoc']==2){
                                echo "<td rowspan='2'>".$data[$index]['dni']."</td>";
                            } else {
                                echo "<td rowspan='2'>".$data[$index]['ruc']."</td>";
                            }
                            echo "<td rowspan='2'>".(round($data[$index]['montofacturado'] * 100) / 100)."</td>";
                            echo "<td rowspan='2'>".(round($data[$index]['montoigv'] * 100) / 100)."</td>";
                            echo "<td rowspan='2'>".$data[$index]['fechadoc']."</td>";

                    echo "</tr>";
                echo "</tbody>";
            }
        echo "</table>";
    }

    
}

?>