<?php 
Class OrdenGastoController extends ApplicationGeneral{
    
    public function verificaPercepcion() {
        $idOrdenVenta = $_REQUEST['orden'];
        $dataRespuesta = array();
        $documento = parent::AutoLoadModel('documento');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $ordenVenta = $this->AutoLoadModel('ordenventa');
        $dataDocumento = $documento->buscadocumentoxordenventaPrimero($idOrdenVenta, 'nombredoc=1');
        $redondeo = $this->configIni('Globals', 'Redondeo');
        if (!empty($dataDocumento)) {
            $montoImporte = $ordenGasto->importeGasto($idOrdenVenta, 9);
            $montoIGV = $ordenGasto->importeGasto($idOrdenVenta, 7);
            $dataBusquedaPercepcion = $ordenGasto->buscaxFiltro("idordenventa='$idOrdenVenta' and idtipogasto=6");
            $porcentajeFactura = $dataDocumento[0]['porcentajefactura'] / 100;
            $dataOrdenVenta = $ordenVenta->buscaOrdenVenta($idOrdenVenta);
            $devolucion = round($dataOrdenVenta[0]['importedevolucion'], $redondeo);
            $montoParcial = round($montoIGV + $montoImporte - $devolucion, $redondeo);
            $montoTotal = round($montoParcial * $porcentajeFactura, $redondeo);
            $pararPrceso = 0;
            if ($dataDocumento[0]['electronico'] == 1) {
                $documentosElectronicos = $documento->listaFacturasElectronicas($idOrdenVenta);
                $cargados = 1;
                for ($fe = 0; $fe < count($documentosElectronicos) && $cargados == 1; $fe++) {
                    if ($documentosElectronicos[$fe]['esCargado'] == 0) {
                        $cargados = 0;
                    }
                }
                if ($cargados == 0) {
                    $dataRespuesta['validacion'] = false;
                    $dataRespuesta['existe'] = 2;
                    $pararPrceso = 1;
                } else {
                    $valorPercepcion = $dataOrdenVenta[0]['percepcion'];
                }
            } else {
                $valorPercepcion = $this->configIni($this->configIni('Globals', 'Modo'), 'percepcion');
            }
            if ($pararPrceso == 0) {
                $montoPercepcion = round($montoTotal * $valorPercepcion, $redondeo);
                $importeGasto = !empty($dataBusquedaPercepcion[0]['importegasto']) ? (round($dataBusquedaPercepcion[0]['importegasto'], $redondeo)) : 0;
                if ($importeGasto > 0) {
                    $dataRespuesta['validacion'] = true;
                    $dataRespuesta['montoPercepcion'] = $importeGasto;
                    $dataRespuesta['idOrdenGasto'] = empty($dataBusquedaPercepcion[0]['idordengasto']) ? 0 : $dataBusquedaPercepcion[0]['idordengasto'];
                    $dataRespuesta['existe'] = 1;
                } else {
                    $dataRespuesta['validacion'] = true;
                    $dataRespuesta['montoPercepcion'] = round($montoPercepcion, $redondeo);
                    $dataRespuesta['idOrdenGasto'] = empty($dataBusquedaPercepcion[0]['idordengasto']) ? 0 : $dataBusquedaPercepcion[0]['idordengasto'];
                    $dataRespuesta['existe'] = 0;
                }
            }
        } else {
            $dataBusquedaPercepcion = $ordenGasto->buscaxFiltro("idordenventa='$idOrdenVenta' and idtipogasto=6");
            $importeGasto = !empty($dataBusquedaPercepcion[0]['importegasto']) ? (round($dataBusquedaPercepcion[0]['importegasto'], $redondeo)) : 0;
            if ($importeGasto > 0) {
                $dataRespuesta['validacion'] = false;
                $dataRespuesta['montoPercepcion'] = $dataBusquedaPercepcion[0]['importegasto'];
                $dataRespuesta['idOrdenGasto'] = empty($dataBusquedaPercepcion[0]['idordengasto']) ? 0 : $dataBusquedaPercepcion[0]['idordengasto'];
                $dataRespuesta['existe'] = 1;
            } else {
                $dataRespuesta['validacion'] = false;
                $dataRespuesta['montoPercepcion'] = $dataBusquedaPercepcion[0]['importegasto'];
                $dataRespuesta['idOrdenGasto'] = empty($dataBusquedaPercepcion[0]['idordengasto']) ? 0 : $dataBusquedaPercepcion[0]['idordengasto'];
                $dataRespuesta['existe'] = 0;
            }
        }
        echo json_encode($dataRespuesta);
    }

    public function aumentarPercepcion() {
        $idDetalleOrdenCobro = $_REQUEST['idDetalleOrdenCobro'];
        $montoPercepcion = $_REQUEST['montoPercepcion'];
        $idOrdenGasto = $_REQUEST['idOrdenGasto'];
        $numDoc = $_REQUEST['numDoc'];
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $ordenCobro = $this->AutoLoadModel('ordencobro');
        $detalleOrdenCobro = $this->AutoLoadModel('detalleordencobro');
        $documento = $this->AutoLoadModel('documento');
        $dataRespuesta = array();
        $redondeo = $this->configIni('Globals', 'Redondeo');
        $dataBusquedaDOC = $detalleOrdenCobro->buscaDetalleOrdencobro($idDetalleOrdenCobro);
        if (!empty($dataBusquedaDOC)) {
            $idOrdenCobro = $dataBusquedaDOC[0]['idordencobro'];
            $importeDoc = $dataBusquedaDOC[0]['importedoc'];
            $saldoDoc = $dataBusquedaDOC[0]['saldodoc'];
            $dataDOC['importedoc'] = round($importeDoc, $redondeo) + $montoPercepcion;
            $dataDOC['saldoDoc'] = round($saldoDoc, $redondeo) + $montoPercepcion;
            $dataDOC['tipogasto'] = 6;
            $exitoDOC = $detalleOrdenCobro->actualizaDetalleOrdencobro($dataDOC, $idDetalleOrdenCobro);
            if ($exitoDOC) {
                if ($numDoc != "") {
                    $dataDoc['montofacturado'] = round($importeDoc, $redondeo) + $montoPercepcion;
                    $exitoD = $documento->actualizarDocumento($dataDoc, "nombredoc=7 and numdoc='$numDoc'");
                }
                $dataBusquedaOC = $ordenCobro->buscaOrdencobro($idOrdenCobro);
                if (!empty($dataBusquedaOC)) {
                    $idOrdenVenta = $dataBusquedaOC[0]['idordenventa'];
                    $importeOrdenCobro = $dataBusquedaOC[0]['importeordencobro'];
                    $saldoOrdenCobro = $dataBusquedaOC[0]['saldoordencobro'];
                    $dataOC['importeordencobro'] = round($importeOrdenCobro, $redondeo) + $montoPercepcion;
                    $dataOC['saldoordencobro'] = round($saldoOrdenCobro, $redondeo) + $montoPercepcion;
                    $exitoOC = $ordenCobro->actualizaOrdencobro($dataOC, $idOrdenCobro);
                    if ($exitoOC) {
                        $dataOG['importegasto'] = $montoPercepcion;
                        if (!empty($idOrdenGasto) || $idOrdenGasto != 0) {
                            $exitoOG = $ordenGasto->actualiza($dataOG, $idOrdenGasto);
                        } else {
                            $dataOG['idordenventa'] = $idOrdenVenta;
                            $dataOG['idtipogasto'] = 6;
                            $exitoOG = $ordenGasto->graba($dataOG);
                        }
                        if ($exitoOG) {
                            $dataRespuesta['verificacion'] = true;
                        } else {
                            $dataRespuesta['verificacion'] = false;
                        }
                    } else {
                        $dataRespuesta['verificacion'] = false;
                    }
                } else {
                    $dataRespuesta['verificacion'] = false;
                }
            } else {
                $dataRespuesta['verificacion'] = false;
            }
        } else {
            $dataRespuesta['verificacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    public function disminuirPercepcion() {
        $idDetalleOrdenCobro = $_REQUEST['idDetalleOrdenCobro'];
        $montoPercepcion = $_REQUEST['montoPercepcion'];
        $idOrdenGasto = $_REQUEST['idOrdenGasto'];
        $numDoc = $_REQUEST['numDoc'];
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $ordenCobro = $this->AutoLoadModel('ordencobro');
        $documento = $this->AutoLoadModel('documento');
        $detalleOrdenCobro = $this->AutoLoadModel('detalleordencobro');
        $dataRespuesta = array();
        $redondeo = $this->configIni('Globals', 'Redondeo');
        $dataBusquedaDOC = $detalleOrdenCobro->buscaDetalleOrdencobro($idDetalleOrdenCobro);
        if (!empty($dataBusquedaDOC)) {
            $idOrdenCobro = $dataBusquedaDOC[0]['idordencobro'];
            $importeDoc = $dataBusquedaDOC[0]['importedoc'];
            $saldoDoc = $dataBusquedaDOC[0]['saldodoc'];
            //$dataDOC['importedoc']=round($importeDoc,$redondeo)-$montoPercepcion;
            $dataDOC['saldoDoc'] = round($saldoDoc, $redondeo) - $montoPercepcion;
            if ($dataDOC['saldoDoc'] <= 0) {
                $dataDOC['situacion'] = 'anulado';
                $dataDOC['saldoDoc'] = 0;
            }
            if ($dataBusquedaDOC[0]['tipogasto'] == 6) {
                $dataDOC['tipogasto'] = 0;
            }
            $exitoDOC = $detalleOrdenCobro->actualizaDetalleOrdencobro($dataDOC, $idDetalleOrdenCobro);
            if ($exitoDOC) {
                if ($numDoc != "") {
                    $dataDoc['montofacturado'] = round($importeDoc, $redondeo) - $montoPercepcion;
                    $exitoD = $documento->actualizarDocumento($dataDoc, "nombredoc=7 and numdoc='$numDoc'");
                }
                $dataBusquedaOC = $ordenCobro->buscaOrdencobro($idOrdenCobro);
                if (!empty($dataBusquedaOC)) {
                    $idOrdenVenta = $dataBusquedaOC[0]['idordenventa'];
                    $importeOrdenCobro = $dataBusquedaOC[0]['importeordencobro'];
                    $saldoOrdenCobro = $dataBusquedaOC[0]['saldoordencobro'];
                    //$dataOC['importeordencobro']=round($importeOrdenCobro,$redondeo)-$montoPercepcion;
                    $dataOC['saldoordencobro'] = round($saldoOrdenCobro, $redondeo) - $montoPercepcion;
                    if ($dataOC['saldoordencobro'] <= 0) {
                        $dataOC['situacion'] = 'CANCELADO';
                        $dataOC['saldoordencobro'] = 0;
                    }
                    $exitoOC = $ordenCobro->actualizaOrdencobro($dataOC, $idOrdenCobro);
                    if ($exitoOC) {
                        $dataOG['importegasto'] = 0;
                        if (!empty($idOrdenGasto) || $idOrdenGasto != 0) {
                            $exitoOG = $ordenGasto->actualiza($dataOG, $idOrdenGasto);
                            if ($exitoOG) {
                                $dataRespuesta['verificacion'] = true;
                            } else {
                                $dataRespuesta['verificacion'] = false;
                            }
                        } else {
                            $dataRespuesta['verificacion'] = false;
                        }
                    } else {
                        $dataRespuesta['verificacion'] = false;
                    }
                } else {
                    $dataRespuesta['verificacion'] = false;
                }
            } else {
                $dataRespuesta['verificacion'] = false;
            }
        } else {
            $dataRespuesta['verificacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    public function buscarValorGasto() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $idTipoGasto = $_REQUEST['idTipoGasto'];
        $dataRespuesta = array();
        $redondeo = $this->configIni('Globals', 'Redondeo');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $dataBusqueda = $ordenGasto->buscaxFiltro("idordenventa='$idOrdenVenta' and idtipogasto='$idTipoGasto' and Estado=1");
        if (empty($dataBusqueda)) {
            $dataRespuesta['importe'] = 0;
            $dataRespuesta['idordengasto'] = 0;
            $dataRespuesta['validacion'] = false;
        } else {
            $dataRespuesta['importe'] = round($dataBusqueda[0]['importegasto'], $redondeo);
            $dataRespuesta['idordengasto'] = $dataBusqueda[0]['idordengasto'];
            $dataRespuesta['validacion'] = true;
        }
        echo json_encode($dataRespuesta);
    }

    public function descontarGasto() {
        $idOrdenGasto = $_REQUEST['idOrdenGasto'];
        $importeDescontar = $_REQUEST[''];
        $ordenGasto = $this->AutoLoadModel('ordengasto');
    }

}

?>