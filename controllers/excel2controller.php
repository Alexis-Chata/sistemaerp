<?php

Class Excel2Controller extends ApplicationGeneral {

    function __construct() {
        parent::__construct();
        ob_end_clean();
    }
    
    public function pendientesporvendedor() {
        set_time_limit(1500);
        $get_txtFechaInicio = '';
        $get_txtFechaFin = date('Y-m-d');
        
        $get_txtFechaVencimientoInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $get_txtFechaVencimientoFin = !empty($_REQUEST['txtFechaFin']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFin'])) : null;
        $get_lstPrincipal = $_REQUEST['lstCategoriaPrincipal'];
        $get_lstCategoria = $_REQUEST['lstCategoria'];
        $get_lstZona = $_REQUEST['lstZona'];
        $get_txtIdCliente = $_REQUEST['txtIdCliente'];
        $get_txtIdVendedor = $_REQUEST['txtIdVendedor'];
        $get_condiciones = $_REQUEST['txtCondiciones'];
        $reporte = $this->AutoLoadModel('reporte');
        
        $dataCreditos = $reporte->resumenDetalladoCreditospendienteporvendedor($get_txtFechaInicio, $get_txtFechaFin, $get_txtFechaVencimientoInicio, $get_txtFechaVencimientoFin, $get_lstPrincipal, $get_lstCategoria, $get_lstZona, $get_txtIdCliente, $get_txtIdVendedor);
        $cant = count($dataCreditos);
        $diaActual = date('Y-m-d');
        
        $arrayInformacionIds = array();
        $arrayInformacion = array();
        $idvendedor = -1;
        for ($i = 0; $i < $cant; $i++) {
            if ($dataCreditos[$i]['idvendedor'] != $idvendedor) {
                $idvendedor = $dataCreditos[$i]['idvendedor'];
                $arrayInformacionIds[$dataCreditos[$i]['idvendedor']] = $dataCreditos[$i]['idvendedor'];
                $arrayInformacion[$dataCreditos[$i]['idvendedor']]['vendedor'] = $dataCreditos[$i]['vendedor'];
                $arrayInformacion[$dataCreditos[$i]['idvendedor']]['creditoxvencer']['soles'] = 0;
                $arrayInformacion[$dataCreditos[$i]['idvendedor']]['creditoxvencer']['dolares'] = 0;
                $arrayInformacion[$dataCreditos[$i]['idvendedor']]['creditovencidos']['soles'] = 0;
                $arrayInformacion[$dataCreditos[$i]['idvendedor']]['creditovencidos']['dolares'] = 0;
            }
            
            $tempRespuesta = ($reporte->cantidad_dias_entre_dos_fechas($dataCreditos[$i]['fvencimiento'],$diaActual))*-1;
            if ($tempRespuesta>=0) {
                if ($dataCreditos[$i]['idmoneda'] == 1) {
                    $arrayInformacion[$dataCreditos[$i]['idvendedor']]['creditoxvencer']['soles'] += $dataCreditos[$i]['saldodoc'];
                } else {
                    $arrayInformacion[$dataCreditos[$i]['idvendedor']]['creditoxvencer']['dolares'] += $dataCreditos[$i]['saldodoc'];
                }
            } else {
                if ($dataCreditos[$i]['idmoneda'] == 1) {
                    $arrayInformacion[$dataCreditos[$i]['idvendedor']]['creditovencidos']['soles'] += $dataCreditos[$i]['saldodoc'];
                } else {
                    $arrayInformacion[$dataCreditos[$i]['idvendedor']]['creditovencidos']['dolares'] += $dataCreditos[$i]['saldodoc'];
                }
            }
        }
        $dataLetrasPA_ysinPA = $reporte->listadoDetalladoLetraspendientexvendedor($get_txtFechaInicio, $get_txtFechaFin, $get_txtFechaVencimientoInicio, $get_txtFechaVencimientoFin, $get_lstPrincipal, $get_lstCategoria, $get_lstZona, $get_txtIdCliente, $get_txtIdVendedor);
        $cant = count($dataLetrasPA_ysinPA);
        $idvendedor = -1;
        for ($i = 0; $i < $cant; $i++) {
            if ($dataLetrasPA_ysinPA[$i]['idvendedor'] != $idvendedor) {
                $idvendedor = $dataLetrasPA_ysinPA[$i]['idvendedor'];
                $arrayInformacionIds[$dataLetrasPA_ysinPA[$i]['idvendedor']] = $dataLetrasPA_ysinPA[$i]['idvendedor'];
                $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['vendedor'] = $dataLetrasPA_ysinPA[$i]['vendedor'];
                $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['letrapa']['soles'] = 0;
                $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['letrapa']['dolares'] = 0;
                $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['letrasinpa']['soles'] = 0;
                $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['letrasinpa']['dolares'] = 0;
            }
            //echo 'Moneda: ' . $dataLetrasPA_ysinPA[$i]['idmoneda'] . ' = [' . $dataLetrasPA_ysinPA[$i]['recepcionLetras'] . ' ] ';
            if ($dataLetrasPA_ysinPA[$i]['idmoneda'] == 1) {
                if ($dataLetrasPA_ysinPA[$i]['recepcionLetras']!='PA') {
                    //echo 'PA1<br>';
                    $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['letrasinpa']['soles'] += $dataLetrasPA_ysinPA[$i]['saldodoc'];
                } else {
                    //echo 'sinPA1<br>';
                    $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['letrapa']['soles'] += $dataLetrasPA_ysinPA[$i]['saldodoc'];
                }
            } else {
                if ($dataLetrasPA_ysinPA[$i]['recepcionLetras']!='PA') {
                    //echo 'PA2<br>';
                    $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['letrasinpa']['dolares'] += $dataLetrasPA_ysinPA[$i]['saldodoc'];
                } else {
                    //echo 'sinPA2<br>';
                    $arrayInformacion[$dataLetrasPA_ysinPA[$i]['idvendedor']]['letrapa']['dolares'] += $dataLetrasPA_ysinPA[$i]['saldodoc'];
                }
            }
        }
        
        $dataLetrasProtestadas = $reporte->detalladoLetrasProtestadaspendienteporvendedor($get_txtFechaInicio, $get_txtFechaFin, $get_txtFechaVencimientoInicio, $get_txtFechaVencimientoFin, $get_lstPrincipal, $get_lstCategoria, $get_lstZona, $get_txtIdCliente, $get_txtIdVendedor);
        $cant = count($dataLetrasProtestadas);
        $idvendedor = -1;
        for ($i = 0; $i < $cant; $i++) {
            if ($dataLetrasProtestadas[$i]['idvendedor'] != $idvendedor) {
                $idvendedor = $dataLetrasProtestadas[$i]['idvendedor'];
                $arrayInformacionIds[$dataLetrasProtestadas[$i]['idvendedor']] = $dataLetrasProtestadas[$i]['idvendedor'];
                $arrayInformacion[$dataLetrasProtestadas[$i]['idvendedor']]['vendedor'] = $dataLetrasProtestadas[$i]['vendedor'];
                $arrayInformacion[$dataLetrasProtestadas[$i]['idvendedor']]['protesto']['soles'] = 0;
                $arrayInformacion[$dataLetrasProtestadas[$i]['idvendedor']]['protesto']['dolares'] = 0;
            }
            if ($dataLetrasProtestadas[$i]['idmoneda'] == 1) {
                $arrayInformacion[$dataLetrasProtestadas[$i]['idvendedor']]['protesto']['soles'] += $dataLetrasProtestadas[$i]['saldodoc'];
                
            } else {
                $arrayInformacion[$dataLetrasProtestadas[$i]['idvendedor']]['protesto']['dolares'] += $dataLetrasProtestadas[$i]['saldodoc'];
            }
        }
        
        $dataContado = $reporte->resumenDetalladocontadopendienteporvendedor($get_txtFechaInicio, $get_txtFechaFin, $get_txtFechaVencimientoInicio, $get_txtFechaVencimientoFin, $get_lstPrincipal, $get_lstCategoria, $get_lstZona, $get_txtIdCliente, $get_txtIdVendedor);
        $cant = count($dataContado);
        $idvendedor = -1;
        for ($i = 0; $i < $cant; $i++) {
            if ($dataContado[$i]['idvendedor'] != $idvendedor) {
                $idvendedor = $dataContado[$i]['idvendedor'];
                $arrayInformacionIds[$dataContado[$i]['idvendedor']] = $dataContado[$i]['idvendedor'];
                $arrayInformacion[$dataContado[$i]['idvendedor']]['vendedor'] = $dataContado[$i]['vendedor'];
                $arrayInformacion[$dataContado[$i]['idvendedor']]['contado']['soles'] = 0;
                $arrayInformacion[$dataContado[$i]['idvendedor']]['contado']['dolares'] = 0;
            }
            if ($dataContado[$i]['idmoneda'] == 1) {
                $arrayInformacion[$dataContado[$i]['idvendedor']]['contado']['soles'] += $dataContado[$i]['saldodoc'];
                
            } else {
                $arrayInformacion[$dataContado[$i]['idvendedor']]['contado']['dolares'] += $dataContado[$i]['saldodoc'];
            }
        }
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'pendientes_por_vendedor.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        
        $sharedStyle6 = new PHPExcel_Style();
        $sharedStyle6->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFF8F32B')
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
                )
        );
        
        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ),
            'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':O' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":O" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "REPORTE - PENDIENTES POR VENDEDOR");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);

        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':O' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":O" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $get_condiciones);
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":D" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "E" . ($contador) . ":E" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "F" . ($contador) . ":H" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "I" . ($contador) . ":I" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "J" . ($contador) . ":L" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "M" . ($contador) . ":M" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "N" . ($contador) . ":O" . ($contador));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':D' . ($contador));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':H' . ($contador));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J' . $contador . ':L' . ($contador));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N' . $contador . ':O' . ($contador));
        
        $nombrecliente = "-";
        if (!empty($get_txtIdCliente)) {
            $clientemodel = $this->AutoLoadModel('cliente');
            $dataCliente = $clientemodel->listadoxFiltro("idcliente='$get_txtIdCliente'");
            $nombrecliente = $dataCliente[0]['razonsocial'];
        }
        $nombredevendedor = '-';
        if (!empty($get_txtIdVendedor)) {
            $vendedor = new Actor();
            $reg = $vendedor->buscarxid($get_txtIdVendedor);
            $nombredevendedor = $reg[0]['nombres'] . " " . $reg[0]['apellidopaterno'] . " " . $reg[0]['apellidomaterno'];
        }
        
        $textReporte = '';
        if (!empty($get_txtFechaVencimientoInicio)) {
            $textReporte = $get_txtFechaVencimientoInicio;
        } else {
            $textReporte = 'Inicio';
        }
        $textReporte .= ' - ';
        if (!empty($get_txtFechaVencimientoFin)) {
            $textReporte .= $get_txtFechaVencimientoFin;
        } else {
            $textReporte .= 'Actualidad';
        }
       
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "VENDEDOR: ")
                ->setCellValue('B' . ($contador), $nombrecliente)
                ->setCellValue('E' . ($contador), "CLIENTE: ")
                ->setCellValue('F' . ($contador), $nombredevendedor)
                ->setCellValue('I' . ($contador), "F/ VENCIMIENTO: ")
                ->setCellValue('J' . ($contador), $textReporte)
                ->setCellValue('M' . ($contador), "F/ REPORTE: ")
                ->setCellValue('N' . ($contador), date('Y-m-d'));
        $contador++;        
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':A' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':C' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':E' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':G' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $contador . ':I' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J' . $contador . ':K' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L' . $contador . ':M' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N' . $contador . ':O' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":O" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "VENDEDOR")
                    ->setCellValue('B' . ($contador), "CONTADO")
                    ->setCellValue('D' . ($contador), "CREDITOS VENCIDOS")
                    ->setCellValue('F' . ($contador), "CREDITOS POR VENCER")
                    ->setCellValue('H' . ($contador), "LETRAS AL BANCO")
                    ->setCellValue('J' . ($contador), "LETRAS POR FIRMAR")
                    ->setCellValue('L' . ($contador), "LETRAS PROTESTADAS")
                    ->setCellValue('N' . ($contador), "TOTAL PENDIENTE");
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":O" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('B' . ($contador), "S/")
                    ->setCellValue('C' . ($contador), "US $")
                    ->setCellValue('D' . ($contador), "S/")
                    ->setCellValue('E' . ($contador), "US $")
                    ->setCellValue('F' . ($contador), "S/")
                    ->setCellValue('G' . ($contador), "US $")
                    ->setCellValue('H' . ($contador), "S/")
                    ->setCellValue('I' . ($contador), "US $")
                    ->setCellValue('J' . ($contador), "S/")
                    ->setCellValue('K' . ($contador), "US $")
                    ->setCellValue('L' . ($contador), "S/")
                    ->setCellValue('M' . ($contador), "US $")
                    ->setCellValue('N' . ($contador), "S/")
                    ->setCellValue('O' . ($contador), "US $");
        
        $letraContadoSoles = 0;
        $letraContadoDolares = 0;
        $crediVencidoSoles = 0;
        $crediVencidoDolares = 0;
        $crediXVencerSoles = 0;
        $crediXVencerDolares = 0;
        $letraPASoles = 0;
        $letraPADolares = 0;
        $letrasinPASoles = 0;
        $letrasinPADolares = 0;
        $letraProtestoSoles = 0;
        $letraProtestoDolares = 0;
        $totalGeneralSoles = 0;
        $totalGeneralDolares = 0;
        foreach ($arrayInformacionIds as $aInfo) {
            $totalVendedorSoles = 0;
            $totalVendedorDolares = 0;
            if (isset($arrayInformacion[$aInfo]['contado']['soles'])) {
                $letraContadoSoles += $arrayInformacion[$aInfo]['contado']['soles'];
                $letraContadoDolares += $arrayInformacion[$aInfo]['contado']['dolares'];
                $totalVendedorSoles += $arrayInformacion[$aInfo]['contado']['soles'];
                $totalVendedorDolares += $arrayInformacion[$aInfo]['contado']['dolares'];
            }
            if (isset($arrayInformacion[$aInfo]['creditoxvencer']['soles'])) {
                $crediVencidoSoles += $arrayInformacion[$aInfo]['creditovencidos']['soles'];
                $crediVencidoDolares += $arrayInformacion[$aInfo]['creditovencidos']['dolares'];
                $crediXVencerSoles += $arrayInformacion[$aInfo]['creditoxvencer']['soles'];
                $crediXVencerDolares += $arrayInformacion[$aInfo]['creditoxvencer']['dolares'];
                $totalVendedorSoles += $arrayInformacion[$aInfo]['creditovencidos']['soles'];
                $totalVendedorDolares += $arrayInformacion[$aInfo]['creditovencidos']['dolares'];
                $totalVendedorSoles += $arrayInformacion[$aInfo]['creditoxvencer']['soles'];
                $totalVendedorDolares += $arrayInformacion[$aInfo]['creditoxvencer']['dolares'];
            }
            if (isset($arrayInformacion[$aInfo]['letrasinpa']['soles'])) {
                $letraPASoles += $arrayInformacion[$aInfo]['letrapa']['soles'];
                $letraPADolares += $arrayInformacion[$aInfo]['letrapa']['dolares'];
                $letrasinPASoles += $arrayInformacion[$aInfo]['letrasinpa']['soles'];
                $letrasinPADolares += $arrayInformacion[$aInfo]['letrasinpa']['dolares'];
                $totalVendedorSoles += $arrayInformacion[$aInfo]['letrapa']['soles'];
                $totalVendedorDolares += $arrayInformacion[$aInfo]['letrapa']['dolares'];
                $totalVendedorSoles += $arrayInformacion[$aInfo]['letrasinpa']['soles'];
                $totalVendedorDolares += $arrayInformacion[$aInfo]['letrasinpa']['dolares'];
            }
            if (isset($arrayInformacion[$aInfo]['protesto']['soles'])) {
                $letraProtestoSoles += $arrayInformacion[$aInfo]['protesto']['soles'];
                $letraProtestoDolares += $arrayInformacion[$aInfo]['protesto']['dolares'];
                $totalVendedorSoles += $arrayInformacion[$aInfo]['protesto']['soles'];
                $totalVendedorDolares += $arrayInformacion[$aInfo]['protesto']['dolares'];
            }
            $contador++;
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "A" . ($contador) . ":A" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "B" . ($contador) . ":O" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $arrayInformacion[$aInfo]['vendedor'])
                    ->setCellValue('B' . ($contador), (isset($arrayInformacion[$aInfo]['contado']['soles']) ? $arrayInformacion[$aInfo]['contado']['soles'] : 0.00))
                    ->setCellValue('C' . ($contador), (isset($arrayInformacion[$aInfo]['contado']['dolares']) ? $arrayInformacion[$aInfo]['contado']['dolares'] : 0.00))
                    ->setCellValue('D' . ($contador), (isset($arrayInformacion[$aInfo]['creditovencidos']['soles']) ? $arrayInformacion[$aInfo]['creditovencidos']['soles'] : 0.00))
                    ->setCellValue('E' . ($contador), (isset($arrayInformacion[$aInfo]['creditovencidos']['dolares']) ? $arrayInformacion[$aInfo]['creditovencidos']['dolares'] : 0.00))
                    ->setCellValue('F' . ($contador), (isset($arrayInformacion[$aInfo]['creditoxvencer']['soles']) ? $arrayInformacion[$aInfo]['creditoxvencer']['soles'] : 0.00))
                    ->setCellValue('G' . ($contador), (isset($arrayInformacion[$aInfo]['creditoxvencer']['dolares']) ? $arrayInformacion[$aInfo]['creditoxvencer']['dolares'] : 0.00))
                    ->setCellValue('H' . ($contador), (isset($arrayInformacion[$aInfo]['letrapa']['soles']) ? $arrayInformacion[$aInfo]['letrapa']['soles'] : 0.00))
                    ->setCellValue('I' . ($contador), (isset($arrayInformacion[$aInfo]['letrapa']['dolares']) ? $arrayInformacion[$aInfo]['letrapa']['dolares'] : 0.00))
                    ->setCellValue('J' . ($contador), (isset($arrayInformacion[$aInfo]['letrasinpa']['soles']) ? $arrayInformacion[$aInfo]['letrasinpa']['soles'] : 0.00))
                    ->setCellValue('K' . ($contador), (isset($arrayInformacion[$aInfo]['letrasinpa']['dolares']) ? $arrayInformacion[$aInfo]['letrasinpa']['dolares'] : 0.00))
                    ->setCellValue('L' . ($contador), (isset($arrayInformacion[$aInfo]['protesto']['soles']) ? $arrayInformacion[$aInfo]['protesto']['soles'] : 0.00))
                    ->setCellValue('M' . ($contador), (isset($arrayInformacion[$aInfo]['protesto']['dolares']) ? $arrayInformacion[$aInfo]['protesto']['dolares'] : 0.00))
                    ->setCellValue('N' . ($contador), $totalVendedorSoles)
                    ->setCellValue('O' . ($contador), $totalVendedorDolares);
            $totalGeneralSoles += $totalVendedorSoles;
            $totalGeneralDolares += $totalVendedorDolares;
 	}
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle6, "B" . ($contador) . ":O" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTOS TOTALES:")
                    ->setCellValue('B' . ($contador), "S/ " . number_format($letraContadoSoles, 2))
                    ->setCellValue('C' . ($contador), "US $ " . number_format($letraContadoDolares, 2))
                    ->setCellValue('D' . ($contador), "S/ " . number_format($crediVencidoSoles, 2))
                    ->setCellValue('E' . ($contador), "US $ " . number_format($crediVencidoDolares, 2))
                    ->setCellValue('F' . ($contador), "S/ " . number_format($crediXVencerSoles, 2))
                    ->setCellValue('G' . ($contador), "US $ " . number_format($crediXVencerDolares, 2))
                    ->setCellValue('H' . ($contador), "S/ " . number_format($letraPASoles, 2))
                    ->setCellValue('I' . ($contador), "US $ " . number_format($letraPADolares, 2))
                    ->setCellValue('J' . ($contador), "S/ " . number_format($letrasinPASoles, 2))
                    ->setCellValue('K' . ($contador), "US $ " . number_format($letrasinPADolares, 2))
                    ->setCellValue('L' . ($contador), "S/ " . number_format($letraProtestoSoles, 2))
                    ->setCellValue('M' . ($contador), "US $ " . number_format($letraProtestoDolares, 2))
                    ->setCellValue('N' . ($contador), "S/ " . number_format($totalGeneralSoles, 2))
                    ->setCellValue('O' . ($contador), "US $ " . number_format($totalGeneralDolares, 2));
        
        $objPHPExcel->getActiveSheet()->setTitle('Pendientes por vendedor');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    
    public function devoluciones() {
        set_time_limit(1500);
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporte_devoluciones.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $fechainicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $fechafin = !empty($_REQUEST['txtFechaFin']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFin'])) : null;
        $idcliente = $_REQUEST['txtIdCliente'];
        $idordenventa = $_REQUEST['txtIdOrdenVenta'];
        $idvendedor = $_REQUEST['lstvendedor'];
        $idmotivo = $_REQUEST['idmotivo'];
        $idsubmotivo = $_REQUEST['idsubmotivo'];
        $devolucionmodel = $this->AutoLoadModel('devolucion');
        $dataDevoluciones = $devolucionmodel->lstadoDevoluciones($fechainicio, $fechafin, $idcliente, $idordenventa, $idvendedor, $idmotivo, $idsubmotivo);
        $tamanio = count($dataDevoluciones);
        
        $tempCodigoOV = "-";
        if (!empty($idordenventa)) {
            $ordenventaModel = $this->AutoLoadModel('ordenventa');
            $tempCodigoOV = $ordenventaModel->sacarCodigo($idordenventa);
        }
        $nombrecliente = "-";
        if (!empty($idcliente)) {
            $clientemodel = $this->AutoLoadModel('cliente');
            $dataCliente = $clientemodel->listadoxFiltro("idcliente='$idcliente'");
            $nombrecliente = $dataCliente[0]['razonsocial'];
        }
        
        $arrayMotivoDevolucion = $this->configIniTodo('MotivoDevolucion');
        $textMotivo = '-';
        if (!empty($idmotivo)) {
            $textMotivo = $arrayMotivoDevolucion[$idmotivo];
            if (!empty($idsubmotivo)) {
                $submotivo=new Submotivodevolucion();
                $dataSubMotivo=$submotivo->buscar($idsubmotivo);
                $textMotivo .= ' - ' . $dataSubMotivo[0]['descripcion'];
            }
        } else {
            $idsubmotivo = '';
        }
        $textVendedor = '-';
        if (!empty($idvendedor)) {
            $vendedor = new Actor();
            $reg = $vendedor->buscarxid($idvendedor);
            $textVendedor = $reg[0]['nombres'] . " " . $reg[0]['apellidopaterno'];
        }
        $Textfecha = "";
        if (!empty($fechainicio)) {
            $Textfecha .= "DESDE " . $fechainicio;
        } else {
            $Textfecha .= "TODAS LAS GUIAS";
        }
        $Textfecha .= " HASTA ";
        if (!empty($fechafin)) {
            $Textfecha .= $fechafin;
        } else {
            $Textfecha .= date('Y/m/d');
        }
        $simboloMoneda = array(1 => 'S/', 2 => 'US $');
        $MotivoDevolucion = $this->configIniTodo('MotivoDevolucion');
        
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('Nro', 'Padre', 'Monto', 'Saldo', 'Condicion', 'Nro Letra', 'Fecha Giro', 'F. Vencimiento', 'F. Pago', 'Situacion', 'Recepcion Letra');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => '00000000')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ), 'font'  => array(
                'color' => array('argb' => 'FFFFFFFF')
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFEFEFEF')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ), 'font'  => array(
                'size'  => 12
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':I' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":I" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "REPORTE DE DEVOLUCIONES");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);

        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':E' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':G' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "D" . ($contador) . ":E" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "H" . ($contador) . ":H" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Fecha:")
                ->setCellValue('C' . ($contador), $Textfecha)
                ->setCellValue('D' . ($contador), "Vendedor:")
                ->setCellValue('F' . ($contador), $textVendedor)
                ->setCellValue('H' . ($contador), "Motivo:")
                ->setCellValue('I' . ($contador), $textMotivo);
        $contador++;
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':E' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':G' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "D" . ($contador) . ":E" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Cliente:")
                ->setCellValue('C' . ($contador), $nombrecliente)
                ->setCellValue('D' . ($contador), "Orden Venta:")
                ->setCellValue('F' . ($contador), $tempCodigoOV);
        $contador++;
        $idvendedor = -1;
        $numeroDevoluciones = 0;
        $MontosDevueltos[1] = 0;
        $MontosDevueltos[2] = 0;
        $MontosTotalesDevueltos[1] = 0;
        $MontosTotalesDevueltos[2] = 0;
        for ($i = 0; $i < $tamanio; $i++) {
            if ($idvendedor != $dataDevoluciones[$i]['idvendedor']) {
                $numeroDevoluciones = 0;
                $MontosDevueltos[1] = 0;
                $MontosDevueltos[2] = 0;
                $idvendedor = $dataDevoluciones[$i]['idvendedor'];
                $contador++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':I' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "C" . ($contador) . ":I" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Vendedor: ")
                        ->setCellValue('C' . ($contador), html_entity_decode($dataDevoluciones[$i]['vendedor'], ENT_QUOTES, 'UTF-8'));
                $contador++;
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":I" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Devolucion")
                        ->setCellValue('B' . ($contador), "RUC")
                        ->setCellValue('C' . ($contador), "Razon Social")
                        ->setCellValue('D' . ($contador), "Orden Venta")
                        ->setCellValue('E' . ($contador), "Fecha Aprobacion Dev.")
                        ->setCellValue('F' . ($contador), "Monto Devuelto")
                        ->setCellValue('G' . ($contador), "Motivo")
                        ->setCellValue('H' . ($contador), "Sub motivo")
                        ->setCellValue('I' . ($contador), "Observaciones");
                $contador++;
            }
            $dataDevoluciones[$i]['razonsocial'] = html_entity_decode($dataDevoluciones[$i]['razonsocial'], ENT_QUOTES, 'UTF-8');
            $dataDevoluciones[$i]['observaciones'] = html_entity_decode($dataDevoluciones[$i]['observaciones'], ENT_QUOTES, 'UTF-8');
            /*
            $dataDevoluciones[$i]['observaciones'] = trim($dataDevoluciones[$i]['observaciones']);
            $dataDevoluciones[$i]['observaciones'] = iconv(mb_detect_encoding(trim($dataDevoluciones[$i]['observaciones'])), "UTF-8", $dataDevoluciones[$i]['observaciones']);
            */
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":I" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $dataDevoluciones[$i]['iddevolucion'])
                    ->setCellValue('B' . ($contador), $dataDevoluciones[$i]['ruc'])
                    ->setCellValue('C' . ($contador), $dataDevoluciones[$i]['razonsocial'])
                    ->setCellValue('D' . ($contador), $dataDevoluciones[$i]['codigov'])
                    ->setCellValue('E' . ($contador), $dataDevoluciones[$i]['fechaaprobada'])
                    ->setCellValue('F' . ($contador), $simboloMoneda[$dataDevoluciones[$i]['idmoneda']] . ' ' . $dataDevoluciones[$i]['importetotal'])
                    ->setCellValue('G' . ($contador), $MotivoDevolucion[$dataDevoluciones[$i]['idmotivodevolucion']])
                    ->setCellValue('H' . ($contador), (!empty($dataDevoluciones[$i]['submotivo']) ? $dataDevoluciones[$i]['submotivo'] : '-'))
                    ->setCellValue('I' . ($contador), $dataDevoluciones[$i]['observaciones']);
            $contador++;
            $numeroDevoluciones++;
            $MontosDevueltos[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            $MontosTotalesDevueltos[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            if ($idvendedor != $dataDevoluciones[$i+1]['idvendedor']) {
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":C" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "D" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "E" . ($contador) . ":E" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "F" . ($contador) . ":F" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "G" . ($contador) . ":G" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "H" . ($contador) . ":H" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":H" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Numero de Devoluciones:")
                        ->setCellValue('D' . ($contador), $numeroDevoluciones)
                        ->setCellValue('E' . ($contador), "Monto Soles:")
                        ->setCellValue('F' . ($contador), $simboloMoneda[1] . ' ' . $MontosDevueltos[1])
                        ->setCellValue('G' . ($contador), "Monto Dolares:")
                        ->setCellValue('H' . ($contador), $simboloMoneda[2] . ' ' . $MontosDevueltos[2]);
                $contador++;
            }
        }
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTOS TOTALES");
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "TOTAL DEVOLUCIONES: ")
                    ->setCellValue('C' . ($contador), $tamanio);
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL SOLES: ")
                    ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesDevueltos[1]); 

        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL DOLARES: ")
                    ->setCellValue('C' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesDevueltos[2]); 
        $objPHPExcel->getActiveSheet()->setTitle('Reporte Devoluciones');
        
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    
    public function resumendevoluciones() {
        set_time_limit(1500);
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporte_resumendevoluciones.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;

        $fechainicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $fechafin = !empty($_REQUEST['txtFechaFin']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFin'])) : null;
        $idcliente = $_REQUEST['txtIdCliente'];
        $idordenventa = $_REQUEST['txtIdOrdenVenta'];
        $idvendedor = $_REQUEST['lstvendedor'];
        $idmotivo = $_REQUEST['idmotivo'];
        $idsubmotivo = $_REQUEST['idsubmotivo'];
        $devolucionmodel = $this->AutoLoadModel('devolucion');
        $dataDevoluciones = $devolucionmodel->resumenDevoluciones($fechainicio, $fechafin, $idcliente, $idordenventa, $idvendedor, $idmotivo, $idsubmotivo);
        $tamanio = count($dataDevoluciones);

        $tempCodigoOV = "-";
        if (!empty($idordenventa)) {
            $ordenventaModel = $this->AutoLoadModel('ordenventa');
            $tempCodigoOV = $ordenventaModel->sacarCodigo($idordenventa);
        }
        $nombrecliente = "-";
        if (!empty($idcliente)) {
            $clientemodel = $this->AutoLoadModel('cliente');
            $dataCliente = $clientemodel->listadoxFiltro("idcliente='$idcliente'");
            $nombrecliente = $dataCliente[0]['razonsocial'];
        }

        $arrayMotivoDevolucion = $this->configIniTodo('MotivoDevolucion');
        $textMotivo = '-';
        if (!empty($idmotivo)) {
            $textMotivo = $arrayMotivoDevolucion[$idmotivo];
            if (!empty($idsubmotivo)) {
                $submotivo=new Submotivodevolucion();
                $dataSubMotivo=$submotivo->buscar($idsubmotivo);
                $textMotivo .= ' - ' . $dataSubMotivo[0]['descripcion'];
            }
        } else {
            $idsubmotivo = '';
        }
        $textVendedor = '-';
        if (!empty($idvendedor)) {
            $vendedor = new Actor();
            $reg = $vendedor->buscarxid($idvendedor);
            $textVendedor = $reg[0]['nombres'] . " " . $reg[0]['apellidopaterno'];
        }
        $Textfecha = "";
        if (!empty($fechainicio)) {
            $Textfecha .= "DESDE " . $fechainicio;
        } else {
            $Textfecha .= "TODAS LAS GUIAS";
        }
        $Textfecha .= " HASTA ";
        if (!empty($fechafin)) {
            $Textfecha .= $fechafin;
        } else {
            $Textfecha .= date('Y/m/d');
        }
        $simboloMoneda = array(1 => 'S/', 2 => 'US $');
        $MotivoDevolucion = $this->configIniTodo('MotivoDevolucion');

        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('Nro', 'Padre', 'Monto', 'Saldo', 'Condicion', 'Nro Letra', 'Fecha Giro', 'F. Vencimiento', 'F. Pago', 'Situacion', 'Recepcion Letra');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => '00000000')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ), 'font'  => array(
                'color' => array('argb' => 'FFFFFFFF')
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFEFEFEF')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ), 'font'  => array(
                'size'  => 12
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':D' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":D" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "RESUMEN DE DEVOLUCIONES POR MOTIVO");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);

        $contador++;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':D' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Fecha:")
                ->setCellValue('B' . ($contador), $Textfecha);

        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Motivo:")
                ->setCellValue('B' . ($contador), $textMotivo)
                ->setCellValue('C' . ($contador), "Vendedor:")
                ->setCellValue('D' . ($contador), $textVendedor);
        $contador++;

        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Cliente:")
                ->setCellValue('B' . ($contador), $nombrecliente)
                ->setCellValue('C' . ($contador), "Orden Venta:")
                ->setCellValue('D' . ($contador), $tempCodigoOV);
        
        $contador++;

        $idvendedor = -1;
        $idmotivodevolucion = -1;
        $idsubmotivodevolucion = -1;

        $cantidadDevolucionVendedor = 0;
        $cantidadDevolucionMotivo = 0;
        $cantidadDevolucionSubmotivo = 0;

        $MontosTotalesVendedor[1] = 0;
        $MontosTotalesVendedor[2] = 0;

        $MontosTotalesSubmotivo[1] = 0;
        $MontosTotalesSubmotivo[2] = 0;

        $MontosTotalesMotivo[1] = 0;
        $MontosTotalesMotivo[2] = 0;

        $MontosTotalesDevueltos[1] = 0;
        $MontosTotalesDevueltos[2] = 0;
        $totalCantidadDevoluciones = 0;

        for ($i = 0; $i < $tamanio; $i++) {
            if ($idmotivodevolucion != $dataDevoluciones[$i]['idmotivodevolucion']) {
                $idmotivodevolucion = $dataDevoluciones[$i]['idmotivodevolucion'];
                $idsubmotivodevolucion = -1;
                $contador++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':D' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), $MotivoDevolucion[$dataDevoluciones[$i]['idmotivodevolucion']]);
                $contador++;
                $MontosTotalesMotivo[1] = 0;
                $MontosTotalesMotivo[2] = 0;
                $cantidadDevolucionMotivo = 0;
            }
            if ($idsubmotivodevolucion != $dataDevoluciones[$i]['idsubmotivodevolucion']) {
                $idsubmotivodevolucion = $dataDevoluciones[$i]['idsubmotivodevolucion'];

                $idvendedor = -1;
                $contador++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':D' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "B" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Submotivo: ")
                        ->setCellValue('B' . ($contador), (!empty($dataDevoluciones[$i]['submotivo']) ? $dataDevoluciones[$i]['submotivo'] : '-'));
                $contador++;
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Vendedor")
                        ->setCellValue('B' . ($contador), "Devoluciones")
                        ->setCellValue('C' . ($contador), "Importe S/")
                        ->setCellValue('D' . ($contador), "Importe US$.");
                $contador++;
                $cantidadDevolucionSubmotivo = 0;
                $MontosTotalesSubmotivo[1] = 0;
                $MontosTotalesSubmotivo[2] = 0;
            }

            $totalCantidadDevoluciones++;
            $cantidadDevolucionVendedor++;
            $cantidadDevolucionMotivo++;
            $cantidadDevolucionSubmotivo++;
            $MontosTotalesDevueltos[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            $MontosTotalesVendedor[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            $MontosTotalesSubmotivo[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            $MontosTotalesMotivo[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            if (!isset($dataDevoluciones[$i+1]['idvendedor']) || $idvendedor != $dataDevoluciones[$i+1]['idvendedor']) {
                if ($dataDevoluciones[$i]['idmotivodevolucion'] != $dataDevoluciones[$i+1]['idmotivodevolucion'] || $dataDevoluciones[$i]['idsubmotivodevolucion'] != $dataDevoluciones[$i+1]['idsubmotivodevolucion'] || $dataDevoluciones[$i]['idvendedor'] != $dataDevoluciones[$i+1]['idvendedor']) {
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":D" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), html_entity_decode($dataDevoluciones[$i]['vendedor'], ENT_QUOTES, 'UTF-8'))
                            ->setCellValue('B' . ($contador), $cantidadDevolucionVendedor)
                            ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesVendedor[1])
                            ->setCellValue('D' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesVendedor[2]);
                    $contador++;

                    $idvendedor = $dataDevoluciones[$i+1]['idvendedor'];
                    $cantidadDevolucionVendedor = 0;
                    $MontosTotalesVendedor[1] = 0;
                    $MontosTotalesVendedor[2] = 0;
                }
            }
            if ($idmotivodevolucion != $dataDevoluciones[$i+1]['idmotivodevolucion'] || $idsubmotivodevolucion != $dataDevoluciones[$i+1]['idsubmotivodevolucion']) {
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "B" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "TOTALES::")
                        ->setCellValue('B' . ($contador), $cantidadDevolucionSubmotivo)
                        ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesSubmotivo[1])
                        ->setCellValue('D' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesSubmotivo[2]);
                $contador++;
                if ($idmotivodevolucion != $dataDevoluciones[$i+1]['idmotivodevolucion']) {
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "RESUMEN DE MOTIVO");
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "MOTIVO: ")
                                ->setCellValue('C' . ($contador), $MotivoDevolucion[$dataDevoluciones[$i]['idmotivodevolucion']]);
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "DEVOLUCIONES: ")
                                ->setCellValue('C' . ($contador), $cantidadDevolucionMotivo);
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "TOTAL SOLES: ")
                                ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesMotivo[1]); 
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "TOTAL DOLARES: ")
                                ->setCellValue('C' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesMotivo[2]); 
                    $contador++;
                }
            }
        }
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTOS TOTALES");
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "TOTAL DEVOLUCIONES: ")
                    ->setCellValue('C' . ($contador), $totalCantidadDevoluciones);
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL SOLES: ")
                    ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesDevueltos[1]); 
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL DOLARES: ")
                    ->setCellValue('C' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesDevueltos[2]); 

        $objPHPExcel->getActiveSheet()->setTitle('Resumen Devoluciones');

        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    
    public function resumendevolucionesvendedor() {
        set_time_limit(1500);
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporte_resumendevolucionesvendedor.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;

        $fechainicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $fechafin = !empty($_REQUEST['txtFechaFin']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFin'])) : null;
        $idcliente = $_REQUEST['txtIdCliente'];
        $idordenventa = $_REQUEST['txtIdOrdenVenta'];
        $idvendedor = $_REQUEST['lstvendedor'];
        $idmotivo = $_REQUEST['idmotivo'];
        $idsubmotivo = $_REQUEST['idsubmotivo'];
        $devolucionmodel = $this->AutoLoadModel('devolucion');
        $dataDevoluciones = $devolucionmodel->resumenDevolucionesVendedor($fechainicio, $fechafin, $idcliente, $idordenventa, $idvendedor, $idmotivo, $idsubmotivo);
        $tamanio = count($dataDevoluciones);

        $tempCodigoOV = "-";
        if (!empty($idordenventa)) {
            $ordenventaModel = $this->AutoLoadModel('ordenventa');
            $tempCodigoOV = $ordenventaModel->sacarCodigo($idordenventa);
        }
        $nombrecliente = "-";
        if (!empty($idcliente)) {
            $clientemodel = $this->AutoLoadModel('cliente');
            $dataCliente = $clientemodel->listadoxFiltro("idcliente='$idcliente'");
            $nombrecliente = $dataCliente[0]['razonsocial'];
        }

        $arrayMotivoDevolucion = $this->configIniTodo('MotivoDevolucion');
        $textMotivo = '-';
        if (!empty($idmotivo)) {
            $textMotivo = $arrayMotivoDevolucion[$idmotivo];
            if (!empty($idsubmotivo)) {
                $submotivo=new Submotivodevolucion();
                $dataSubMotivo=$submotivo->buscar($idsubmotivo);
                $textMotivo .= ' - ' . $dataSubMotivo[0]['descripcion'];
            }
        } else {
            $idsubmotivo = '';
        }
        $textVendedor = '-';
        if (!empty($idvendedor)) {
            $vendedor = new Actor();
            $reg = $vendedor->buscarxid($idvendedor);
            $textVendedor = $reg[0]['nombres'] . " " . $reg[0]['apellidopaterno'];
        }
        $Textfecha = "";
        if (!empty($fechainicio)) {
            $Textfecha .= "DESDE " . $fechainicio;
        } else {
            $Textfecha .= "TODAS LAS GUIAS";
        }
        $Textfecha .= " HASTA ";
        if (!empty($fechafin)) {
            $Textfecha .= $fechafin;
        } else {
            $Textfecha .= date('Y/m/d');
        }
        $simboloMoneda = array(1 => 'S/', 2 => 'US $');
        $MotivoDevolucion = $this->configIniTodo('MotivoDevolucion');

        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('Nro', 'Padre', 'Monto', 'Saldo', 'Condicion', 'Nro Letra', 'Fecha Giro', 'F. Vencimiento', 'F. Pago', 'Situacion', 'Recepcion Letra');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => '00000000')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ), 'font'  => array(
                'color' => array('argb' => 'FFFFFFFF')
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFEFEFEF')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ), 'font'  => array(
                'size'  => 12
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':D' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":D" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "RESUMEN DE DEVOLUCIONES POR VENDEDOR");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);

        $contador++;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':D' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Fecha:")
                ->setCellValue('B' . ($contador), $Textfecha);

        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Motivo:")
                ->setCellValue('B' . ($contador), $textMotivo)
                ->setCellValue('C' . ($contador), "Vendedor:")
                ->setCellValue('D' . ($contador), $textVendedor);
        $contador++;

        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Cliente:")
                ->setCellValue('B' . ($contador), $nombrecliente)
                ->setCellValue('C' . ($contador), "Orden Venta:")
                ->setCellValue('D' . ($contador), $tempCodigoOV);
        
        $contador++;

        $idvendedor = -1;
        $idmotivodevolucion = -1;
        $idsubmotivodevolucion = -1;

        $cantidadDevolucionVendedor = 0;
        $cantidadDevolucionMotivo = 0;
        $cantidadDevolucionSubmotivo = 0;

        $MontosTotalesVendedor[1] = 0;
        $MontosTotalesVendedor[2] = 0;

        $MontosTotalesSubmotivo[1] = 0;
        $MontosTotalesSubmotivo[2] = 0;

        $MontosTotalesMotivo[1] = 0;
        $MontosTotalesMotivo[2] = 0;

        $MontosTotalesDevueltos[1] = 0;
        $MontosTotalesDevueltos[2] = 0;
        $totalCantidadDevoluciones = 0;

        for ($i = 0; $i < $tamanio; $i++) {
            if ($idvendedor != $dataDevoluciones[$i]['idvendedor']) {
                $idvendedor = $dataDevoluciones[$i]['idvendedor'];
                $idmotivodevolucion = -1;
                $contador++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':D' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), html_entity_decode($dataDevoluciones[$i]['vendedor'], ENT_QUOTES, 'UTF-8'));
                $contador++;
                $MontosTotalesVendedor[1] = 0;
                $MontosTotalesVendedor[2] = 0;
                $cantidadDevolucionVendedor = 0;
            }
            
            if ($idmotivodevolucion != $dataDevoluciones[$i]['idmotivodevolucion']) {
                $idmotivodevolucion = $dataDevoluciones[$i]['idmotivodevolucion'];
                $idsubmotivodevolucion = -1;
                $contador++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':D' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "B" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Motivo: ")
                        ->setCellValue('B' . ($contador), $MotivoDevolucion[$dataDevoluciones[$i]['idmotivodevolucion']]);
                $contador++;
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Submotivo")
                        ->setCellValue('B' . ($contador), "Devoluciones")
                        ->setCellValue('C' . ($contador), "Importe S/")
                        ->setCellValue('D' . ($contador), "Importe US$.");
                $contador++;
                $cantidadDevolucionMotivo = 0;
                $MontosTotalesMotivo[1] = 0;
                $MontosTotalesMotivo[2] = 0;
            }
            $totalCantidadDevoluciones++;
            $cantidadDevolucionVendedor++;
            $cantidadDevolucionMotivo++;
            $cantidadDevolucionSubmotivo++;
            $MontosTotalesDevueltos[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            $MontosTotalesVendedor[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            $MontosTotalesSubmotivo[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            $MontosTotalesMotivo[$dataDevoluciones[$i]['idmoneda']] += $dataDevoluciones[$i]['importetotal'];
            if (!isset($dataDevoluciones[$i+1]['idsubmotivodevolucion']) || $idvendedor != $dataDevoluciones[$i+1]['idsubmotivodevolucion']) {
                if ($dataDevoluciones[$i]['idmotivodevolucion'] != $dataDevoluciones[$i+1]['idmotivodevolucion'] || $dataDevoluciones[$i]['idsubmotivodevolucion'] != $dataDevoluciones[$i+1]['idsubmotivodevolucion'] || $dataDevoluciones[$i]['idvendedor'] != $dataDevoluciones[$i+1]['idvendedor']) {
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":D" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), (!empty($dataDevoluciones[$i]['submotivo']) ? $dataDevoluciones[$i]['submotivo'] : '-'))
                            ->setCellValue('B' . ($contador), $cantidadDevolucionSubmotivo)
                            ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesSubmotivo[1])
                            ->setCellValue('D' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesSubmotivo[2]);
                    $contador++;

                    $idsubmotivodevolucion = $dataDevoluciones[$i+1]['idsubmotivodevolucion'];
                    $cantidadDevolucionSubmotivo = 0;
                    $MontosTotalesSubmotivo[1] = 0;
                    $MontosTotalesSubmotivo[2] = 0;
                }
            }
            
            if ($idvendedor != $dataDevoluciones[$i+1]['idvendedor'] || $idmotivodevolucion != $dataDevoluciones[$i+1]['idmotivodevolucion']) {
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "B" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":D" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "TOTALES::")
                        ->setCellValue('B' . ($contador), $cantidadDevolucionMotivo)
                        ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesMotivo[1])
                        ->setCellValue('D' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesMotivo[2]);
                $contador++;
                if ($idvendedor != $dataDevoluciones[$i+1]['idvendedor']) {
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "RESUMEN DE VENDEDOR");
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "VENDEDOR: ")
                                ->setCellValue('C' . ($contador), html_entity_decode($dataDevoluciones[$i]['vendedor'], ENT_QUOTES, 'UTF-8'));
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "DEVOLUCIONES: ")
                                ->setCellValue('C' . ($contador), $cantidadDevolucionVendedor);
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "TOTAL SOLES: ")
                                ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesVendedor[1]); 
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A' . ($contador), "TOTAL DOLARES: ")
                                ->setCellValue('C' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesVendedor[2]); 
                    $contador++;
                }
            }
        }
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTOS TOTALES");
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "TOTAL DEVOLUCIONES: ")
                    ->setCellValue('C' . ($contador), $totalCantidadDevoluciones);
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL SOLES: ")
                    ->setCellValue('C' . ($contador), $simboloMoneda[1] . ' ' . $MontosTotalesDevueltos[1]); 
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL DOLARES: ")
                    ->setCellValue('C' . ($contador), $simboloMoneda[2] . ' ' . $MontosTotalesDevueltos[2]); 

        $objPHPExcel->getActiveSheet()->setTitle('Devoluciones Vendedor');

        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }

    function reporteHistorialVentasxProducto() {
        set_time_limit(1500);
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporteHistorialVentasxProducto.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $idVendedor = $_REQUEST['idVendedor'];
        $idProducto = $_REQUEST['idProducto'];
        $idCliente = $_REQUEST['idCliente'];
        $reporte = $this->AutoLoadModel('reporte');

        if ($idProducto == 0) {
            $idProducto = "";
        }
        $datos = $reporte->historialVentasxProducto($idProducto, $idVendedor, $idCliente);
        $cantidadData = count($datos);
        
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('Orden Venta', 'FECHA', 'CLIENTE', 'VENDEDOR', 'ORIG.', 'U.M.', 'PRECIO', 'CANT.', 'IMPORTE');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => '00000000')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ), 'font'  => array(
                'color' => array('argb' => 'FFFFFFFF')
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFEFEFEF')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ), 'font'  => array(
                'size'  => 12
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );
        
        $sharedStyle2R = new PHPExcel_Style();
        $sharedStyle2R->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
                )
        );

        $sharedStyle2C = new PHPExcel_Style();
        $sharedStyle2C->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );
        
        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':I' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":I" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "HISTORIAL DE VENTAS POR PRODUCTO");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':F' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $contador . ':I' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "G" . ($contador) . ":G" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "H" . ($contador) . ":I" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Producto:")
                ->setCellValue('B' . ($contador), $datos[0]['codigopa'] . ' // ' . $datos[0]['nompro'])
                ->setCellValue('G' . ($contador), "Impresion:")
                ->setCellValue('H' . ($contador), date('Y-m-d'));
        $contador++;
        
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":I" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), $titulos[1])
                ->setCellValue('C' . ($contador), $titulos[2])
                ->setCellValue('D' . ($contador), $titulos[3])
                ->setCellValue('E' . ($contador), $titulos[4])
                ->setCellValue('F' . ($contador), $titulos[5])
                ->setCellValue('G' . ($contador), $titulos[6])
                ->setCellValue('H' . ($contador), $titulos[7])
                ->setCellValue('I' . ($contador), $titulos[8]);
        $contador++; 
        $importeSOLES = 0;
        $importeDOLARES = 0;
        $cantidadP = 0;
        for ($i = 0; $i < $cantidadData; $i++) {
            if ($datos[$i]['idmoneda'] == 1) {
                $simbolo = 'S/ ';
                $importeSOLES+=round($datos[$i]['preciofinal'], 2) * $datos[$i]['cantdespacho'];
            } else if ($datos[$i]['idmoneda'] == 2) {
                $simbolo = 'US $ ';
                $importeDOLARES+=round($datos[$i]['preciofinal'], 2) * $datos[$i]['cantdespacho'];
            }
            $cantidadP+=$datos[$i]['cantdespacho'];
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2C, "A" . ($contador) . ":B" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "C" . ($contador) . ":D" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2C, "E" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2R, "G" . ($contador) . ":I" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $datos[$i]['codigov'])
                    ->setCellValue('B' . ($contador), $datos[$i]['fordenventa'])
                    ->setCellValue('C' . ($contador), utf8_decode(html_entity_decode($datos[$i]['razonsocial'], ENT_QUOTES, 'UTF-8')))
                    ->setCellValue('D' . ($contador), utf8_decode(html_entity_decode($datos[$i]['nombres'] . ' ' . $datos[$i]['apellidopaterno'] . ' ' . $datos[$i]['apellidomaterno'], ENT_QUOTES, 'UTF-8')))
                    ->setCellValue('E' . ($contador), $datos[$i]['codigoalmacen'])
                    ->setCellValue('F' . ($contador), $datos[$i]['nombremedida'])
                    ->setCellValue('G' . ($contador), $simbolo . number_format($datos[$i]['preciofinal'], 2))
                    ->setCellValue('H' . ($contador), $datos[$i]['cantdespacho'])
                    ->setCellValue('I' . ($contador), $simbolo . number_format($datos[$i]['preciofinal']*$datos[$i]['cantdespacho'], 2));
            $contador++;
            $simbolo = '';
        }
        
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTOS TOTALES");
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "CANTIDAD TOTAL: ")
                    ->setCellValue('C' . ($contador), $cantidadP);
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL SOLES: ")
                    ->setCellValue('C' . ($contador), 'S/ ' . number_format($importeSOLES, 2)); 
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL DOLARES: ")
                    ->setCellValue('C' . ($contador), 'US $ ' . number_format($importeDOLARES, 2));
        $objPHPExcel->getActiveSheet()->setTitle('Historial Ventas');

        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    
    public function seguridad_consultar() {
        set_time_limit(1500);
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'formaseguridad_recojomercaderia.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $fechainicio = $_REQUEST['txtFechaAprobadoInicio'];
        $fechafin = $_REQUEST['txtFechaAprobadoFin'];
        $idcliente = $_REQUEST['idCliente'];
        $estado = $_REQUEST['cmbEstado'];
        
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('#Descarga', 'Recojo', 'Fecha', 'Venta', 'Cliente', 'RUC/DNI', 'Codigo', 'Producto', 'Cantidad', 'Motivo');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => '00000000')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ), 'font'  => array(
                'color' => array('argb' => 'FFFFFFFF')
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFEFEFEF')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            ), 'font'  => array(
                'size'  => 12
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );
        
        $sharedStyle2R = new PHPExcel_Style();
        $sharedStyle2R->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
            )
                )
        );

        $sharedStyle2C = new PHPExcel_Style();
        $sharedStyle2C->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );
        
        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':J' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":J" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "RECOJO DE MERCADERIA");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;
        $contador++;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':F' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $contador . ':I' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Impresion:")
                ->setCellValue('B' . ($contador), date('Y-m-d'));
        $contador++;
        
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":J" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), $titulos[1])
                ->setCellValue('C' . ($contador), $titulos[2])
                ->setCellValue('D' . ($contador), $titulos[3])
                ->setCellValue('E' . ($contador), $titulos[4])
                ->setCellValue('F' . ($contador), $titulos[5])
                ->setCellValue('G' . ($contador), $titulos[6])
                ->setCellValue('H' . ($contador), $titulos[7])
                ->setCellValue('I' . ($contador), $titulos[8])
                ->setCellValue('J' . ($contador), $titulos[9]);
        $contador++; 
        
        $atcliente = new Atencioncliente();
        $dataRecepciones = $atcliente->listadoRecepciones($fechainicio, $fechafin, $idcliente, $estado);
        $tamanio = count($dataRecepciones);
        $numeroDescarga = 0; 
        for ($i = 0; $i < $tamanio; $i++) {
            if ($dataRecepciones[$i]['descargado'] == 0) {
                if ($numeroDescarga == 0) {
                    $numeroDescarga = $atcliente->ultimaDescarga();
                    $dataDescargado['descargado'] = $numeroDescarga;
                }
                $dataRecepciones[$i]['descargado'] = $numeroDescarga;
                $atcliente->actualizaDetalleRecepcion($dataDescargado, $dataRecepciones[$i]['iddetallerecepcion']);
            }
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2C, "A" . ($contador) . ":D" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "E" . ($contador) . ":E" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2C, "F" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "G" . ($contador) . ":H" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2R, "I" . ($contador) . ":I" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2C, "J" . ($contador) . ":J" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), '#' . $dataRecepciones[$i]['descargado'])
                    ->setCellValue('B' . ($contador), $dataRecepciones[$i]['codigost'])
                    ->setCellValue('C' . ($contador), $dataRecepciones[$i]['fremision'])
                    ->setCellValue('D' . ($contador), $dataRecepciones[$i]['codigov'])
                    ->setCellValue('E' . ($contador), $dataRecepciones[$i]['razonsocial'])
                    ->setCellValue('F' . ($contador), (!empty($dataRecepciones[$i]['ruc']) ? $dataRecepciones[$i]['ruc'] : $dataRecepciones[$i]['dni']))
                    ->setCellValue('G' . ($contador), $dataRecepciones[$i]['codigopa'])
                    ->setCellValue('H' . ($contador), $dataRecepciones[$i]['nompro'])
                    ->setCellValue('I' . ($contador), $dataRecepciones[$i]['cantidad'])
                    ->setCellValue('J' . ($contador), $dataRecepciones[$i]['nombremotivo']);
            $contador++;
            
            
                    //'<td style="text-align:center">' . ($dataRecepciones[$i]['descargado'] == 0 ? ' -' : '#' . $dataRecepciones[$i]['descargado']) . '</td>' .
  
        }
        
        $objPHPExcel->getActiveSheet()->setTitle('Recojo Mercaderia');

        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    
    public function detalleincobrable() {
        set_time_limit(1500);
        
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporte_incobrables.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-d');
        $lstCategoria = $_REQUEST['lstCategoriaPrincipal'];
        $lstZona = $_REQUEST['lstZona'];
        $txtIdCliente = $_REQUEST['txtIdCliente'];
        $txtidOrdenVenta = $_REQUEST['txtIdOrdenVenta'];
        $lstMoneda = $_REQUEST['lstMoneda'];
        $cmbCondicion = $_REQUEST['cmbCondicionIncobrable'];
        
        $textCondicion = 'Todos';
        if ($cmbCondicion == 1) {
            $textCondicion = 'Contado';
        } else if ($cmbCondicion == 2) {
            $textCondicion = 'Credito';
        } else if ($cmbCondicion == 3) {
            $textCondicion = 'Letras';
        } else if ($cmbCondicion == 4) {
            $textCondicion = 'Letras Protestadas';
        }
        
        $get_txt2FechaInicio = ($get_txtFechaInicio == "") ? "Desde el prinicipio" : $get_txtFechaInicio;
        $get_txt2FechaFin = ($get_txtFechaFin == "") ? "Hasta el fin" : $get_txtFechaFin;

        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('Nro', 'Padre', 'Monto', 'Saldo', 'Condicion', 'Nro Letra', 'Fecha Giro', 'F. Vencimiento', 'F. Pago', 'Situacion', 'Recepcion Letra');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':J' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":J" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "REPORTE - DETALLADO INCOBRABLE");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);

        $contador++;
                
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "E" . ($contador) . ":E" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "G" . ($contador) . ":G" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Condicion:")
                ->setCellValue('B' . ($contador), $textCondicion)
                ->setCellValue('C' . ($contador), "Fecha Reporte:")
                ->setCellValue('D' . ($contador), date('Y-m-d'))
                ->setCellValue('E' . ($contador), "Fecha Desde:")
                ->setCellValue('F' . ($contador), $get_txt2FechaInicio)
                ->setCellValue('G' . ($contador), "Fecha Hasta:")
                ->setCellValue('H' . ($contador), $get_txt2FechaFin);
                
        $contador++;
        
        /*
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':E' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G' . $contador . ':H' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('I' . $contador . ':J' . $contador);
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "D" . ($contador) . ":E" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "G" . ($contador) . ":H" . ($contador));
        */
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $reporte = $this->AutoLoadModel('reporte');
        $listado = $reporte->resumenIncobrables_detalle($txtFechaInicio, $txtFechaFinal, $lstCategoria, $lstZona, $txtIdCliente, $txtidOrdenVenta, $lstMoneda, $cmbCondicion);
//        $dataIncobrables = $reporte->resumenIncobrables($txtFechaInicio, $txtFechaFinal);
        $cantI = count($listado);

        $sIMontCont = 0;
        $dIMontCont = 0;

        $sIMontCredi = 0;
        $dIMontCredi = 0;

        $sIMontLet = 0;
        $dIMontLet = 0;
        
        $sIMontLetPro = 0;
        $dIMontLetPro = 0;
        //formacobro
        for ($i = 0; $i < $cantI; $i++) {
            $moneda = 'S/. ';
            if ($listado[$i]['idmoneda'] == 2)
                $moneda = 'US $ ';
            if ($listado[$i]['idvendedor'] != $tempVendedor) {
                if ($i > 1) {
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), "MONTOS TOTALES");
                    $contador++;

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), " ")
                            ->setCellValue('B' . ($contador), "SOLES")
                            ->setCellValue('C' . ($contador), "DOLARES");
                    $contador++;

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), "EMPRESA:")
                            ->setCellValue('B' . ($contador), 'S/. ' . number_format($TotalEmpresaSoles, 2))
                            ->setCellValue('C' . ($contador), 'US $ ' . number_format($TotalEmpresaDolares, 2));
                    $contador++;
                    $TotalEmpresaSoles = 0;
                    $TotalEmpresaDolares = 0;
                }

                $contador = $contador + 2;
                $tempVendedor = $listado[$i]['idvendedor'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':J' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "C" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Vendedor: ")
                        ->setCellValue('C' . ($contador), $listado[$i]['vendedor']);
                $contador++;
            }
            
            if ($listado[$i]['idordenventa'] != $tempOrdenventa) {
                $contador++;
                $importe = $ordenGasto->totalGuia($listado[$i]['idordenventa']);
                if ($listado[$i]['idmoneda'] == 1) {
                    $TotalEmpresaSoles += $importe - $listado[$i]['importepagado'];
                } else {
                    $TotalEmpresaDolares += $importe - $listado[$i]['importepagado'];
                }
                $tempOrdenventa = $listado[$i]['idordenventa'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':D' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":A" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":A" . ($contador))->getFill()->setRotation(1);


                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "E" . ($contador) . ":E" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("E" . ($contador) . ":E" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("E" . ($contador) . ":E" . ($contador))->getFill()->setRotation(1);


                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "G" . ($contador) . ":G" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("G" . ($contador) . ":G" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("G" . ($contador) . ":G" . ($contador))->getFill()->setRotation(1);

                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "I" . ($contador) . ":I" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("I" . ($contador) . ":I" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("I" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);

                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "F" . ($contador) . ":F" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "H" . ($contador) . ":H" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "J" . ($contador) . ":J" . ($contador));
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Cliente:")
                        ->setCellValue('B' . ($contador), $listado[$i]['razonsocial'])
                        ->setCellValue('E' . ($contador), "Orden Venta:")
                        ->setCellValue('F' . ($contador), $listado[$i]['codigov'])
                        ->setCellValue('G' . ($contador), "Fecha:")
                        ->setCellValue('H' . ($contador), $listado[$i]['fordenventa'])
                        ->setCellValue('I' . ($contador), "Situacion: ")
                        ->setCellValue('J' . ($contador), $listado[$i]['situacionov']);
                $contador++;
            }
            if ($listado[$i]['idordencobro'] != $tempOrdenCobro) {
                $contador++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':J' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "DETALLE DE LA PROGRAMACION DE PAGOS");
                $contador++;

                $tempOrdenCobro = $listado[$i]['idordencobro'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':C' . $contador);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $contador . ':F' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "D" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "G" . ($contador) . ":G" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "I" . ($contador) . ":I" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "E" . ($contador) . ":E" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "H" . ($contador) . ":H" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "J" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Monto Total:")
                        ->setCellValue('B' . ($contador), $moneda . $listado[$i]['importeordencobro'])
                        ->setCellValue('D' . ($contador), "Saldo:")
                        ->setCellValue('E' . ($contador), $moneda . $listado[$i]['saldoordencobro'])
                        ->setCellValue('G' . ($contador), "Fecha Emision:")
                        ->setCellValue('H' . ($contador), $listado[$i]['femision'])
                        ->setCellValue('I' . ($contador), "Situacion:")
                        ->setCellValue('J' . ($contador), $listado[$i]['situacionoc']);
                $contador++;

                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Nro")
                        ->setCellValue('B' . ($contador), "Padre")
                        ->setCellValue('C' . ($contador), "Monto")
                        ->setCellValue('D' . ($contador), "Saldo")
                        ->setCellValue('E' . ($contador), "Condicion")
                        ->setCellValue('F' . ($contador), "Nro Letra")
                        ->setCellValue('G' . ($contador), "F. Vencimiento")
                        ->setCellValue('H' . ($contador), "Situacion")
                        ->setCellValue('I' . ($contador), "Nro Unico")
                        ->setCellValue('J' . ($contador), "Recepcion Letra");
                $contador++;
            }
            
            switch ($listado[$i]['formacobro']) {
                case '1': $formacobro = "Contado";
                    break;
                case '2': $formacobro = "Crédito";
                    break;
                case '3': $formacobro = "Letras";
                    break;
            }

            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":J" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $listado[$i]['iddetalleordencobro'])
                    ->setCellValue('B' . ($contador), $listado[$i]['idpadre'])
                    ->setCellValue('C' . ($contador), $moneda . number_format($listado[$i]['importedoc'], 2))
                    ->setCellValue('D' . ($contador), $moneda . number_format($listado[$i]['saldodoc'], 2))
                    ->setCellValue('E' . ($contador), $formacobro)
                    ->setCellValue('F' . ($contador), $listado[$i]['numeroletra'])
                    ->setCellValue('G' . ($contador), $listado[$i]['fvencimiento'])
                    ->setCellValue('H' . ($contador), (($listado[$i]['situacion'] == '') ? 'pendiente ref (' . $listado[$i]['referencia'] . ')' : $listado[$i]['situacion'] . ' ref (' . $listado[$i]['referencia'] . ')'))
                    ->setCellValue('I' . ($contador), $listado[$i]['numerounico'])
                    ->setCellValue('J' . ($contador), $listado[$i]['recepletra']);
            $contador++;
            if ($listado[$i]['idmoneda'] == 1) {
                if ($listado[$i]['formacobro'] == 1) {
                    $sIMontCont += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 2 && $listado[$i]['referencia1'] != 'P' && $listado[$i]['referencia2'] != 'P') {
                    $sIMontCredi += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 2 && ($listado[$i]['referencia1'] == 'P' || $listado[$i]['referencia2'] == 'P')) {
                    $sIMontLetPro += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 3) {
                    $sIMontLet += $listado[$i]['saldodoc'];
                }
            } else {
                if ($listado[$i]['formacobro'] == 1) {
                    $dIMontCont += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 2 && $listado[$i]['referencia1'] != 'P' && $listado[$i]['referencia2'] != 'P') {
                    $dIMontCredi += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 2 && ($listado[$i]['referencia1'] == 'P' || $listado[$i]['referencia2'] == 'P')) {
                    $dIMontLetPro += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 3) {
                    $dIMontLet += $listado[$i]['saldodoc'];
                }
            }
            
        }
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "MONTOS TOTALES:");

        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), " ")
                ->setCellValue('B' . ($contador), "SOLES")
                ->setCellValue('C' . ($contador), "DOLARES");
        $contador++;

        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "CONTADO:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sIMontCont, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dIMontCont, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "CREDITO:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sIMontCredi, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dIMontCredi, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "LETRAS:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sIMontLet, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dIMontLet, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "LETRAS PROTESTADAS:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sIMontLetPro, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dIMontLetPro, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "TOTAL INCOBRABLE:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sIMontCont + $sIMontCredi + $sIMontLet + $sIMontLetPro, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dIMontCont + $dIMontCredi + $dIMontLet + $dIMontLetPro, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setTitle('Reporte Incobrables');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }

    public function letrasprotestadasxdia() {
        set_time_limit(1500);
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporte_letrasprotestadaspordia.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $txtFechaFin = !empty($_REQUEST['txtFechaFin']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFin'])) : date('Y-m-d');
                
        $txtOrdenVenta = $_REQUEST['txtOrdenVenta'];
        $txtCliente = $_REQUEST['txtCliente'];
        $txtVendedor = $_REQUEST['txtVendedor'];
        $txidVendedor = $_REQUEST['idVendedor'];
        $txtPrincipal = $_REQUEST['lstCategoriaPrincipal'];
        $txtCategoria = $_REQUEST['lstCategoria'];
        $txtZona = $_REQUEST['lstZona'];
        $txtMoneda = $_REQUEST['lstMoneda'];

        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('Nro', 'Padre', 'Monto', 'Saldo', 'Condicion', 'Nro Letra', 'Fecha Giro', 'F. Vencimiento', 'F. Pago', 'Situacion', 'Recepcion Letra');

        $sharedStyle6 = new PHPExcel_Style();
        $sharedStyle6->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFBBCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
                )
        );

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':J' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":J" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "REPORTE - DETALLADO DE CREDITOS");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Fecha Reporte:")
                ->setCellValue('C' . ($contador), date('Y-m-d'));
        $contador++;

        $cobranza = new Cobranza();
        $detalleOrdenCobranza = new detalleOrdenCobro();
        $listado = $cobranza->creditoscreadosxprotestos($txtFechaInicio, $txtFechaFin, $txtOrdenVenta, $txtCliente, $txidVendedor, $txtPrincipal, $txtCategoria, $txtZona, $txtMoneda);
        $tam = count($listado);
        $tempVendedor = -1;
        $tempOrdenventa = -1;
        $tempOrdenCobro = -1;

        $CreditoSolesSaldo = 0;
        $CreditoDolaresSaldo = 0;
        $CreditoSolesPagado = 0;
        $CreditoDolaresPagado = 0;

        $LetrasSolesSaldo = 0;
        $LetrasDolaresSaldo = 0;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Fecha Consultada")
                ->setCellValue('C' . ($contador), (!empty($txtFechaInicio) ? $txtFechaInicio . ' - ' : '' ) . $txtFechaFin);
        $contador++;

        $auxPadres = "";

        for ($i = 0; $i < $tam; $i++) {
            $moneda = 'S/. ';
            if ($listado[$i]['idmoneda'] == 2)
                $moneda = 'US $ ';
            if ($listado[$i]['idvendedor'] != $tempVendedor) {
                $contador = $contador + 2;
                $tempVendedor = $listado[$i]['idvendedor'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . ($contador + 1));
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':J' . ($contador + 1));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "C" . ($contador) . ":J" . ($contador + 1));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador + 1));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador + 1))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador + 1))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Vendedor: ")
                        ->setCellValue('C' . ($contador), $listado[$i]['vendedor']);
                $contador = $contador + 2;
            }
            if ($listado[$i]['idmoneda'] == 1) {
                $LetrasSolesSaldo += $listado[$i]['saldodoc'];
            } else {
                $LetrasDolaresSaldo += $listado[$i]['saldodoc'];
            }
            if ($listado[$i]['idordenventa'] != $tempOrdenventa) {
                $contador++;
                $tempOrdenventa = $listado[$i]['idordenventa'];
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":A" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":A" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("C" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("C" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "E" . ($contador) . ":E" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("E" . ($contador) . ":E" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("E" . ($contador) . ":E" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':G' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "D" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "F" . ($contador) . ":G" . ($contador));
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Orden Venta:")
                        ->setCellValue('B' . ($contador), $listado[$i]['codigov'])
                        ->setCellValue('C' . ($contador), "Fecha Venta:")
                        ->setCellValue('D' . ($contador), $listado[$i]['fordenventa'])
                        ->setCellValue('E' . ($contador), "Situacion:")
                        ->setCellValue('F' . ($contador), $listado[$i]['situacionov']);
                $contador++;

                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':D' . $contador);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':G' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":A" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":A" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "E" . ($contador) . ":E" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("E" . ($contador) . ":E" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("E" . ($contador) . ":E" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "F" . ($contador) . ":G" . ($contador));
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Razon Social:")
                        ->setCellValue('B' . ($contador), $listado[$i]['razonsocial'])
                        ->setCellValue('E' . ($contador), "R.U.C.:")
                        ->setCellValue('F' . ($contador), $listado[$i]['ruc']);
                $contador++;
            }
            if ($listado[$i]['idordencobro'] != $tempOrdenCobro) {
                $contador++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':J' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "DETALLE DE LA PROGRAMACION DE PAGOS");
                $contador++;

                $tempOrdenCobro = $listado[$i]['idordencobro'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':C' . $contador);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $contador . ':F' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "D" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "G" . ($contador) . ":G" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "I" . ($contador) . ":I" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "E" . ($contador) . ":E" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "H" . ($contador) . ":H" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "J" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Monto Total:")
                        ->setCellValue('B' . ($contador), $moneda . number_format($listado[$i]['importeordencobro'], 2))
                        ->setCellValue('D' . ($contador), "Saldo:")
                        ->setCellValue('E' . ($contador), $moneda . number_format($listado[$i]['saldoordencobro'], 2))
                        ->setCellValue('G' . ($contador), "Fecha Emision:")
                        ->setCellValue('H' . ($contador), $listado[$i]['femision'])
                        ->setCellValue('I' . ($contador), "Situacion:")
                        ->setCellValue('J' . ($contador), $listado[$i]['situacionoc']);
                $contador++;

                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Nro")
                        ->setCellValue('B' . ($contador), "Padre")
                        ->setCellValue('C' . ($contador), "Monto")
                        ->setCellValue('D' . ($contador), "Saldo")
                        ->setCellValue('E' . ($contador), "Condicion")
                        ->setCellValue('F' . ($contador), "Nro Letra")
                        ->setCellValue('G' . ($contador), "F. Vencimiento")
                        ->setCellValue('H' . ($contador), "Situacion")
                        ->setCellValue('I' . ($contador), "Nro Unico")
                        ->setCellValue('J' . ($contador), "Recepcion Letra");
                $contador++;
            }

            switch ($listado[$i]['formacobro']) {
                case '1': $formacobro = "Contado";
                    break;
                case '2': $formacobro = "Crédito";
                    break;
                case '3': $formacobro = "Letras";
                    break;
            }
            $auxPadres .= $listado[$i]['idpadre'] . ";";
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":J" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $listado[$i]['iddetalleordencobro'])
                    ->setCellValue('B' . ($contador), $listado[$i]['idpadre'])
                    ->setCellValue('C' . ($contador), $moneda . number_format($listado[$i]['importedoc'], 2))
                    ->setCellValue('D' . ($contador), $moneda . number_format($listado[$i]['saldodoc'], 2))
                    ->setCellValue('E' . ($contador), $formacobro)
                    ->setCellValue('F' . ($contador), $listado[$i]['numeroletra'])
                    ->setCellValue('G' . ($contador), $listado[$i]['fvencimiento'])
                    ->setCellValue('H' . ($contador), (($listado[$i]['situacion'] == '') ? 'pendiente ref (' . $listado[$i]['referencia'] . ')' : $listado[$i]['situacion'] . ' ref (' . $listado[$i]['referencia'] . ')'))
                    ->setCellValue('I' . ($contador), $listado[$i]['numerounico'])
                    ->setCellValue('J' . ($contador), $listado[$i]['recepletra']);
            $contador++;
            if ($listado[$i + 1]['idordenventa'] != $tempOrdenventa) {
                $auxPadres .= "0";
                $auxPadres = explode(";", $auxPadres);
                $tempTam = count($auxPadres);
                if ($tempTam >= 2) {
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':J' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle6, "A" . ($contador) . ":J" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), "LETRAS PROTESTADAS DE LA ORDEN DE VENYA");
                    $contador++;
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle6, "A" . ($contador) . ":J" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), "Nro")
                            ->setCellValue('B' . ($contador), "Padre")
                            ->setCellValue('C' . ($contador), "Monto")
                            ->setCellValue('D' . ($contador), "Saldo")
                            ->setCellValue('E' . ($contador), "Condicion")
                            ->setCellValue('F' . ($contador), "Nro Letra")
                            ->setCellValue('G' . ($contador), "F. Vencimiento")
                            ->setCellValue('H' . ($contador), "Situacion")
                            ->setCellValue('I' . ($contador), "Nro Unico")
                            ->setCellValue('J' . ($contador), "Recepcion Letra");
                    $contador++;
                }
                for ($ti = 0; $ti < $tempTam - 1; $ti++) {
                    $tempDOC = $detalleOrdenCobranza->buscaDetalleOrdencobro($auxPadres[$ti]);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":J" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $tempDOC[0]['iddetalleordencobro'])
                            ->setCellValue('B' . ($contador), $tempDOC[0]['idpadre'])
                            ->setCellValue('C' . ($contador), $moneda . number_format($tempDOC[0]['importedoc'], 2))
                            ->setCellValue('D' . ($contador), $moneda . number_format($tempDOC[0]['saldodoc'], 2))
                            ->setCellValue('E' . ($contador), "Letras")
                            ->setCellValue('F' . ($contador), $tempDOC[0]['numeroletra'])
                            ->setCellValue('G' . ($contador), $tempDOC[0]['fvencimiento'])
                            ->setCellValue('H' . ($contador), (($tempDOC[0]['situacion'] == '') ? 'pendiente ref (' . $tempDOC[0]['referencia'] . ')' : $tempDOC[0]['situacion'] . ' ref (' . $tempDOC[0]['referencia'] . ')'))
                            ->setCellValue('I' . ($contador), $tempDOC[0]['numerounico'])
                            ->setCellValue('J' . ($contador), $tempDOC[0]['recepletra']);
                    $contador++;
                    if ($listado[$i]['idmoneda'] == 1) {
                        $CreditoSolesSaldo += $listado[$i]['saldodoc'];
                        $CreditoSolesPagado += ($listado[$i]['importedoc'] - $listado[$i]['saldodoc']);
                    } else {
                        $CreditoDolaresSaldo += $listado[$i]['saldodoc'];
                        $CreditoDolaresPagado += ($listado[$i]['importedoc'] - $listado[$i]['saldodoc']);
                    }
                }
                $auxPadres = "";
            }
        }
        $contador = $contador + 2;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':E' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":E" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":E" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "RESUMEN GENERAL");
        $contador++;

        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":C" . ($contador + 3));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "E" . ($contador) . ":E" . ($contador + 3));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "D" . ($contador) . ":D" . ($contador + 3));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':C' . ($contador + 1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . ($contador + 2) . ':C' . ($contador + 3));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . ($contador + 3));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador + 3))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador + 3))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "CREDITOS")
                ->setCellValue('C' . ($contador), "PAGADOS")
                ->setCellValue('C' . ($contador + 2), "SALDOS")
                ->setCellValue('D' . ($contador), "SOLES")
                ->setCellValue('D' . ($contador + 1), "DOLARES")
                ->setCellValue('E' . ($contador), 'S/. ' . number_format($CreditoSolesPagado, 2))
                ->setCellValue('E' . ($contador + 1), 'US $ ' . number_format($CreditoDolaresPagado, 2))
                ->setCellValue('D' . ($contador + 2), "SOLES")
                ->setCellValue('D' . ($contador + 3), "DOLARES")
                ->setCellValue('E' . ($contador + 2), 'S/. ' . number_format($CreditoSolesSaldo, 2))
                ->setCellValue('E' . ($contador + 3), 'US $ ' . number_format($CreditoDolaresSaldo, 2));
        $contador = $contador + 4;

        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle6, "A" . ($contador) . ":C" . ($contador + 1));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "E" . ($contador) . ":E" . ($contador + 1));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "D" . ($contador) . ":D" . ($contador + 1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . ($contador + 1));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador + 1))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador + 1))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "LETRAS PROTESTADAS")
                ->setCellValue('D' . ($contador), "SOLES")
                ->setCellValue('D' . ($contador + 1), "DOLARES")
                ->setCellValue('E' . ($contador), 'S/. ' . number_format($CreditoSolesPagado+$CreditoSolesSaldo, 2))
                ->setCellValue('E' . ($contador + 1), 'US $ ' . number_format($CreditoDolaresPagado+$CreditoDolaresSaldo, 2));

        $objPHPExcel->getActiveSheet()->setTitle('Letras Protestadas ' . $txtFechaFin);
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    
    public function detalladoCreditos_incobrablesypesados() {
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporte_detalladodecreditos.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        $get_condiciones = $_REQUEST['txtCondiciones'];
        $get_txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $get_txtFechaFin = !empty($_REQUEST['txtFechaFin']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFin'])) : date('Y-m-d');
        $cmtEtapa = $_REQUEST['lstEtapa'];
        $get_lstPrincipal = $_REQUEST['lstCategoriaPrincipal'];
        $get_lstCategoria = $_REQUEST['lstCategoria'];
        $get_lstZona = $_REQUEST['lstZona'];
        $get_lstRecepcionLetras = $_REQUEST['lstRecepcionLetras'];
        $get_txtIdCliente = $_REQUEST['txtIdCliente'];
        $get_txtIdOrdenVenta = $_REQUEST['txtIdOrdenVenta'];
        $get_lstMoneda = $_REQUEST['lstMoneda'];
        $get_lstEstado = $_REQUEST['lstEstado'];
        $get_cmbCondVencimiento2 = $_REQUEST['lstCondVencimiento2'];
        $get_cmbCondVencimiento3 = $_REQUEST['lstCondVencimiento3'];

        $get_txt2FechaInicio = ($get_txtFechaInicio == "") ? "Desde el prinicipio" : $get_txtFechaInicio;
        $get_txt2FechaFin = ($get_txtFechaFin == "") ? "Hasta el fin" : $get_txtFechaFin;

        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('Nro', 'Padre', 'Monto', 'Saldo', 'Condicion', 'Nro Letra', 'Fecha Giro', 'F. Vencimiento', 'F. Pago', 'Situacion', 'Recepcion Letra');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $contador = 1;

        $complementotitulo = "";
        if ($cmtEtapa == 1) {
            $complementotitulo = " ANTES DE LA PANDEMIA";
        } else if ($cmtEtapa == 2) {
            $complementotitulo = " DESPUES DE LA PANDEMIA";
        }

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':M' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":M" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "REPORTE - DETALLADO DE CREDITOS" . $complementotitulo);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);

        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":O" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Fecha Reporte:")
                ->setCellValue('C' . ($contador), date('Y-m-d'));
        $contador++;

        //start cabecera
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':M' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":M" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $get_condiciones);
        $contador++;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':F' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G' . $contador . ':I' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J' . $contador . ':M' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":M" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "FECHA DESDE")
                ->setCellValue('D' . ($contador), "FECHA HASTA")
                ->setCellValue('G' . ($contador), "REPORTE")
                ->setCellValue('J' . ($contador), "ESTADO");
        $contador++;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':F' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G' . $contador . ':I' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J' . $contador . ':M' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":M" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
      
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $get_txt2FechaInicio)
                    ->setCellValue('D' . ($contador), $get_txt2FechaFin)
                    ->setCellValue('G' . ($contador), "CREDITOS")
                    ->setCellValue('J' . ($contador), "TODOS");



        $contador++;
        $reporte = $this->AutoLoadModel('reporte');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
 
        $listado = $reporte->resumenDetalladoCreditos_nuevo($cmtEtapa, $get_txtFechaInicio, $get_txtFechaFin, $get_lstPrincipal, $get_lstCategoria, $get_lstZona, $get_txtIdCliente, $get_txtIdOrdenVenta, $get_lstMoneda);

        $tam = count($listado);
        $tempVendedor = -1;
        $tempOrdenventa = -1;
        $tempOrdenCobro = -1;
        $TotalCreditoSolesLima = 0;
        $TotalCreditoDolaresLima = 0;
        $TotalCreditoSolesProvincia = 0;
        $TotalCreditoDolaresProvincia = 0;

        for ($i = 0; $i < $tam; $i++) {
            $moneda = 'S/. ';
            if ($listado[$i]['idmoneda'] == 2)
                $moneda = 'US $ ';
            if ($listado[$i]['idvendedor'] != $tempVendedor) {
                $contador = $contador + 2;
                $tempVendedor = $listado[$i]['idvendedor'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':M' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "C" . ($contador) . ":M" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Vendedor: ")
                        ->setCellValue('C' . ($contador), $listado[$i]['vendedor']);
                $contador++;

                $tempOrdenCobro = $listado[$i]['idordencobro'];
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":M" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Nº")
                        ->setCellValue('B' . ($contador), "RUC")
                        ->setCellValue('C' . ($contador), "CLIENTE")
                        ->setCellValue('D' . ($contador), "DEPARTAMENTO")
                        ->setCellValue('E' . ($contador), "DISTRITO")
                        ->setCellValue('F' . ($contador), "DIRECCION")
                        ->setCellValue('G' . ($contador), "ORD. VENTA (OV)")
                        ->setCellValue('H' . ($contador), "FECHA DE VENTA")
                        ->setCellValue('I' . ($contador), "FECHA DE EMISION CRED.")
                        ->setCellValue('J' . ($contador), "FECHA DE VENCIMIENTO")
                        ->setCellValue('K' . ($contador), "SITUACION")
                        ->setCellValue('L' . ($contador), "S/.")
                        ->setCellValue('M' . ($contador), "$");

                $contador++;
            }
            if ($listado[$i]['idpadrec'] == 1) {
                if ($listado[$i]['idmoneda'] == 1) {
                    $TotalCreditoSolesLima += $listado[$i]['saldodoc'];
                } else {
                    $TotalCreditoDolaresLima += $listado[$i]['saldodoc'];
                }
            } else {
                if ($listado[$i]['idmoneda'] == 1) {
                    $TotalCreditoSolesProvincia += $listado[$i]['saldodoc'];
                } else {
                    $TotalCreditoDolaresProvincia += $listado[$i]['saldodoc'];
                }
            }

//                if ($listado[$i]['idordencobro'] != $tempOrdenCobro) {
//                    $contador++;
//                    $tempOrdenCobro = $listado[$i]['idordencobro'];
//                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":J" . ($contador));
//                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
//                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
//                    $objPHPExcel->setActiveSheetIndex(0)
//                            ->setCellValue('A' . ($contador), "Nº")
//                            ->setCellValue('B' . ($contador), "RUC")
//                            ->setCellValue('C' . ($contador), "CLIENTE")
//                            ->setCellValue('D' . ($contador), "ORD. VENTA (OV)")
//                            ->setCellValue('E' . ($contador), "FECHA DE VENTA")
//                            ->setCellValue('F' . ($contador), "FECHA DE EMISION CRED.")
//                            ->setCellValue('G' . ($contador), "FECHA DE VENCIMIENTO")
//                            ->setCellValue('H' . ($contador), "SITUACION")
//                            ->setCellValue('I' . ($contador), "S/.")
//                            ->setCellValue('J' . ($contador), "$");
//
//                    $contador++;
//
//                }

            switch ($listado[$i]['formacobro']) {
                case '1': $formacobro = "Contado";
                    break;
                case '2': $formacobro = "Crédito";
                    break;
                case '3': $formacobro = "Letras";
                    break;
            }
            if ($listado[$i]['idmoneda'] == 1) {
                $saldoSolesTemp = $moneda . number_format($listado[$i]['saldodoc'], 2);
            } else {
                $saldoSolesTemp = 0.00;
            }

            if ($listado[$i]['idmoneda'] == 2) {
                $saldoDolaresTemp = $moneda . number_format($listado[$i]['saldodoc'], 2);
            } else {
                $saldoDolaresTemp = 0.00;
            }
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":M" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $listado[$i]['iddetalleordencobro'])
                    ->setCellValue('B' . ($contador), $listado[$i]['ruc'])
                    ->setCellValue('C' . ($contador), $listado[$i]['razonsocial'])
                    ->setCellValue('D' . ($contador), $listado[$i]['nombredepartamento'])
                    ->setCellValue('E' . ($contador), $listado[$i]['nombredistrito'])
                    ->setCellValue('F' . ($contador), $listado[$i]['direccion'])
                    ->setCellValue('G' . ($contador), $listado[$i]['codigov'])
                    ->setCellValue('H' . ($contador), $listado[$i]['fordenventa'])
                    ->setCellValue('I' . ($contador), $listado[$i]['femision'])
                    ->setCellValue('J' . ($contador), $listado[$i]['fvencimiento'])
                    ->setCellValue('K' . ($contador), $listado[$i]['situacionoc'])
                    ->setCellValue('L' . ($contador), $saldoSolesTemp)
                    ->setCellValue('M' . ($contador), $saldoDolaresTemp);
            $contador++;
        }

        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('K' . $contador . ':M' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "K" . ($contador) . ":M" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('K' . ($contador), "MONTOS TOTALES");

        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "K" . ($contador) . ":M" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('K' . ($contador), " ")
                ->setCellValue('L' . ($contador), "SOLES")
                ->setCellValue('M' . ($contador), "DOLARES")
        ;
        $contador++;
        if ($get_lstPrincipal == 1 || $get_lstPrincipal == '') {
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "K" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "L" . ($contador) . ":M" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('K' . ($contador), "LIMA")
                    ->setCellValue('L' . ($contador), 'S/. ' . number_format($TotalCreditoSolesLima, 2))
                    ->setCellValue('M' . ($contador), 'US $ ' . number_format($TotalCreditoDolaresLima, 2));
            $contador++;
        }

        if ($get_lstPrincipal == 2 || $get_lstPrincipal == '') {
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "K" . ($contador));
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "L" . ($contador) . ":M" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('K' . ($contador), "PROVINCIA")
                    ->setCellValue('L' . ($contador), 'S/. ' . number_format($TotalCreditoSolesProvincia, 2))
                    ->setCellValue('M' . ($contador), 'US $ ' . number_format($TotalCreditoDolaresProvincia, 2));
            $contador++;
        }
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "K" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "L" . ($contador) . ":M" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("K" . ($contador) . ":M" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('K' . ($contador), "TOTAL")
                ->setCellValue('L' . ($contador), 'S/. ' . number_format($TotalCreditoSolesLima + $TotalCreditoSolesProvincia, 2))
                ->setCellValue('M' . ($contador), 'US $ ' . number_format($TotalCreditoDolaresLima + $TotalCreditoDolaresProvincia, 2));
        $contador++;

        $objPHPExcel->getActiveSheet()->setTitle('Reporte_Detallado_de_Creditos');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }

    public function detallepesados() {
        set_time_limit(1500);
        $cmtEtapa = $_REQUEST['lstEtapa'];
        
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporte_incobrables.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-d');
        $lstCategoria = $_REQUEST['lstCategoriaPrincipal'];
        $lstZona = $_REQUEST['lstZona'];
        $txtIdCliente = $_REQUEST['txtIdCliente'];
        $txtidOrdenVenta = $_REQUEST['txtIdOrdenVenta'];
        $lstMoneda = $_REQUEST['lstMoneda'];
        $cmbCondicion = $_REQUEST['cmbCondicionIncobrable'];
        
        $textCondicion = 'Todos';
        if ($cmbCondicion == 1) {
            $textCondicion = 'Contado';
        } else if ($cmbCondicion == 2) {
            $textCondicion = 'Credito';
        } else if ($cmbCondicion == 3) {
            $textCondicion = 'Letras';
        } else if ($cmbCondicion == 4) {
            $textCondicion = 'Letras Protestadas';
        }
        
        $get_txt2FechaInicio = ($get_txtFechaInicio == "") ? "Desde el prinicipio" : $get_txtFechaInicio;
        $get_txt2FechaFin = ($get_txtFechaFin == "") ? "Hasta el fin" : $get_txtFechaFin;

        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('Nro', 'Padre', 'Monto', 'Saldo', 'Condicion', 'Nro Letra', 'Fecha Giro', 'F. Vencimiento', 'F. Pago', 'Situacion', 'Recepcion Letra');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':J' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":J" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "REPORTE - DETALLADO PESADO");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);

        $contador++;
                
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "E" . ($contador) . ":E" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "G" . ($contador) . ":G" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "Condicion:")
                ->setCellValue('B' . ($contador), $textCondicion)
                ->setCellValue('C' . ($contador), "Fecha Reporte:")
                ->setCellValue('D' . ($contador), date('Y-m-d'))
                ->setCellValue('E' . ($contador), "Fecha Desde:")
                ->setCellValue('F' . ($contador), $get_txt2FechaInicio)
                ->setCellValue('G' . ($contador), "Fecha Hasta:")
                ->setCellValue('H' . ($contador), $get_txt2FechaFin);
                
        $contador++;
        
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $reporte = $this->AutoLoadModel('reporte');
        $listado = $reporte->resumenPesados_detalle($cmtEtapa, $txtFechaInicio, $txtFechaFinal, $lstCategoria, $lstZona, $txtIdCliente, $txtidOrdenVenta, $lstMoneda, $cmbCondicion);
        $cantP = count($listado);

        $sPMontCont = 0;
        $dPMontCont = 0;

        $sPMontCredi = 0;
        $dPMontCredi = 0;

        $sPMontLet = 0;
        $dPMontLet = 0;
        
        $sPMontLetPro = 0;
        $dPMontLetPro = 0;
        //formacobro
        for ($i = 0; $i < $cantP; $i++) {
            $moneda = 'S/. ';
            if ($listado[$i]['idmoneda'] == 2)
                $moneda = 'US $ ';
            if ($listado[$i]['idvendedor'] != $tempVendedor) {
                if ($i > 1) {
                    $contador++;
                    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), "MONTOS TOTALES");
                    $contador++;

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), " ")
                            ->setCellValue('B' . ($contador), "SOLES")
                            ->setCellValue('C' . ($contador), "DOLARES");
                    $contador++;

                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
                    $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
                    $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
                    $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), "EMPRESA:")
                            ->setCellValue('B' . ($contador), 'S/. ' . number_format($TotalEmpresaSoles, 2))
                            ->setCellValue('C' . ($contador), 'US $ ' . number_format($TotalEmpresaDolares, 2));
                    $contador++;
                    $TotalEmpresaSoles = 0;
                    $TotalEmpresaDolares = 0;
                }

                $contador = $contador + 2;
                $tempVendedor = $listado[$i]['idvendedor'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':J' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "C" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":B" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Vendedor: ")
                        ->setCellValue('C' . ($contador), $listado[$i]['vendedor']);
                $contador++;
            }
            
            if ($listado[$i]['idordenventa'] != $tempOrdenventa) {
                $contador++;
                $importe = $ordenGasto->totalGuia($listado[$i]['idordenventa']);
                if ($listado[$i]['idmoneda'] == 1) {
                    $TotalEmpresaSoles += $importe - $listado[$i]['importepagado'];
                } else {
                    $TotalEmpresaDolares += $importe - $listado[$i]['importepagado'];
                }
                $tempOrdenventa = $listado[$i]['idordenventa'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':D' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":A" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":A" . ($contador))->getFill()->setRotation(1);


                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "E" . ($contador) . ":E" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("E" . ($contador) . ":E" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("E" . ($contador) . ":E" . ($contador))->getFill()->setRotation(1);


                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "G" . ($contador) . ":G" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("G" . ($contador) . ":G" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("G" . ($contador) . ":G" . ($contador))->getFill()->setRotation(1);

                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "I" . ($contador) . ":I" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("I" . ($contador) . ":I" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("I" . ($contador) . ":I" . ($contador))->getFill()->setRotation(1);

                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "F" . ($contador) . ":F" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "H" . ($contador) . ":H" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "J" . ($contador) . ":J" . ($contador));
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Cliente:")
                        ->setCellValue('B' . ($contador), $listado[$i]['razonsocial'])
                        ->setCellValue('E' . ($contador), "Orden Venta:")
                        ->setCellValue('F' . ($contador), $listado[$i]['codigov'])
                        ->setCellValue('G' . ($contador), "Fecha:")
                        ->setCellValue('H' . ($contador), $listado[$i]['fordenventa'])
                        ->setCellValue('I' . ($contador), "Situacion: ")
                        ->setCellValue('J' . ($contador), $listado[$i]['situacionov']);
                $contador++;
            }
            if ($listado[$i]['idordencobro'] != $tempOrdenCobro) {
                $contador++;
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':J' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "DETALLE DE LA PROGRAMACION DE PAGOS");
                $contador++;

                $tempOrdenCobro = $listado[$i]['idordencobro'];
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':C' . $contador);
                $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $contador . ':F' . $contador);
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":A" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "D" . ($contador) . ":D" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "G" . ($contador) . ":G" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "I" . ($contador) . ":I" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "E" . ($contador) . ":E" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "H" . ($contador) . ":H" . ($contador));
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "J" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Monto Total:")
                        ->setCellValue('B' . ($contador), $moneda . $listado[$i]['importeordencobro'])
                        ->setCellValue('D' . ($contador), "Saldo:")
                        ->setCellValue('E' . ($contador), $moneda . $listado[$i]['saldoordencobro'])
                        ->setCellValue('G' . ($contador), "Fecha Emision:")
                        ->setCellValue('H' . ($contador), $listado[$i]['femision'])
                        ->setCellValue('I' . ($contador), "Situacion:")
                        ->setCellValue('J' . ($contador), $listado[$i]['situacionoc']);
                $contador++;

                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":J" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), "Nro")
                        ->setCellValue('B' . ($contador), "Padre")
                        ->setCellValue('C' . ($contador), "Monto")
                        ->setCellValue('D' . ($contador), "Saldo")
                        ->setCellValue('E' . ($contador), "Condicion")
                        ->setCellValue('F' . ($contador), "Nro Letra")
                        ->setCellValue('G' . ($contador), "F. Vencimiento")
                        ->setCellValue('H' . ($contador), "Situacion")
                        ->setCellValue('I' . ($contador), "Nro Unico")
                        ->setCellValue('J' . ($contador), "Recepcion Letra");
                $contador++;
            }
            
            switch ($listado[$i]['formacobro']) {
                case '1': $formacobro = "Contado";
                    break;
                case '2': $formacobro = "Crédito";
                    break;
                case '3': $formacobro = "Letras";
                    break;
            }

            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":J" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":J" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $listado[$i]['iddetalleordencobro'])
                    ->setCellValue('B' . ($contador), $listado[$i]['idpadre'])
                    ->setCellValue('C' . ($contador), $moneda . number_format($listado[$i]['importedoc'], 2))
                    ->setCellValue('D' . ($contador), $moneda . number_format($listado[$i]['saldodoc'], 2))
                    ->setCellValue('E' . ($contador), $formacobro)
                    ->setCellValue('F' . ($contador), $listado[$i]['numeroletra'])
                    ->setCellValue('G' . ($contador), $listado[$i]['fvencimiento'])
                    ->setCellValue('H' . ($contador), (($listado[$i]['situacion'] == '') ? 'pendiente ref (' . $listado[$i]['referencia'] . ')' : $listado[$i]['situacion'] . ' ref (' . $listado[$i]['referencia'] . ')'))
                    ->setCellValue('I' . ($contador), $listado[$i]['numerounico'])
                    ->setCellValue('J' . ($contador), $listado[$i]['recepletra']);
            $contador++;
            if ($listado[$i]['idmoneda'] == 1) {
                if ($listado[$i]['formacobro'] == 1) {
                    $sPMontCont += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 2 && $listado[$i]['referencia1'] != 'P' && $listado[$i]['referencia2'] != 'P') {
                    $sPMontCredi += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 2 && ($listado[$i]['referencia1'] == 'P' || $listado[$i]['referencia2'] == 'P')) {
                    $sPMontLetPro += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 3) {
                    $sPMontLet += $listado[$i]['saldodoc'];
                }
            } else {
                if ($listado[$i]['formacobro'] == 1) {
                    $dPMontCont += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 2 && $listado[$i]['referencia1'] != 'P' && $listado[$i]['referencia2'] != 'P') {
                    $dPMontCredi += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 2 && ($listado[$i]['referencia1'] == 'P' || $listado[$i]['referencia2'] == 'P')) {
                    $dPMontLetPro += $listado[$i]['saldodoc'];
                } else if ($listado[$i]['formacobro'] == 3) {
                    $dPMontLet += $listado[$i]['saldodoc'];
                }
            }
        }
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "MONTOS TOTALES:");

        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), " ")
                ->setCellValue('B' . ($contador), "SOLES")
                ->setCellValue('C' . ($contador), "DOLARES");
        $contador++;

        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "CONTADO:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sPMontCont, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($PIMontCont, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "CREDITO:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sPMontCredi, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dPMontCredi, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "LETRAS:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sPMontLet, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dPMontLet, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "LETRAS PROTESTADAS:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sPMontLetPro, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dPMontLetPro, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "TOTAL INCOBRABLE:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sPMontCont + $sPMontCredi + $sPMontLet + $sPMontLetPro, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dPMontCont + $dPMontCredi + $dPMontLet + $dPMontLetPro, 2));
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setTitle('Reporte Pesados');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    public function RankingClientesxVendedor(){
        /********************DATOS A RECOGER***************************/

        $idvend = $_REQUEST['idVendedor'];
        $condicion = $_REQUEST['lstCondicion'];
        $catprin = $_REQUEST['lstCategoriaPrincipal'];
        $regcobr = $_REQUEST['lstRegionCobranza'];
        $zona = $_REQUEST['lstZona'];
        $fecini = $_REQUEST['txtFechaInicio'];
        $fecfin = $_REQUEST['txtFechaFin'];
        $depa = $_REQUEST['lstDepartamento'];
        $prov = $_REQUEST['lstProvincia'];
        $dist = $_REQUEST['lstDistrito'];
        $ordenar = $_REQUEST['lstOrden'];
        $mostrar = $_REQUEST['lstMostrar'];
        $aprobados = $_REQUEST['lstAprobados'];

        $cartcli = new CarteraCliente($idvend, $condicion, $catprin, $regcobr, $zona, $fecini, $fecfin, $depa, $prov, $dist, $ordenar, $aprobados);
        $datos = $cartcli->listarRankingClientexVendedor($mostrar);
        $cantidadData = count($datos);
        /*******************************FIN DATOS A RECOGER***************************************/
        
        
        /********************************EXCEL************************************/
        set_time_limit(1500);
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'RankingClientexVendedor.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        
        
        /******************************ESTILOS EXCEL***************************************/
        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '86DAF0')
            ), 'borders' => array(
                'right' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOTDOT),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOTDOT),
                'top' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOTDOT),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOTDOT)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ))
        );
        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '5FCCE8')
            ), 'borders' => array(
                'right' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOT),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOT),
                'top' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOT),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_DASHDOT)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ))
        );
        
        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '9BE1F3')
            ), 'borders' => array(
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ))
        );
        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFFFF')
            ), 'borders' => array(
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ))
        );
        
        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'EBEE28')
            ), 'borders' => array(
                'right' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
            ),'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ))
        );

        
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        /********************************FIN ESTILOS EXCEL***************************************/
        
        
        /*******************************ENCABEZADO***************************************/
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', "RANKING CLIENTE POR VENDEDOR")
                ->mergeCells('A1:J2');
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A1:J2");
        $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
        
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A3', "VENDEDOR: ");
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A3");
        $objPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setBold(true);
        
        $vendedor = new Actor();
        $reg = $vendedor->buscarxid($idvend);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('B3', $reg[0]['nombres'] . " " . $reg[0]['apellidopaterno'] . " " . $reg[0]['apellidomaterno'])
                ->mergeCells('B3:E3');;      
        
        if (empty($fecini)) {
            $fecini = "TODAS LAS GUIAS";
        }
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('F3', "FECHA: ");    
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "F3");
        $objPHPExcel->getActiveSheet()->getStyle("F3")->getFont()->setBold(true);

        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('G3', $fecini)
                ->mergeCells('G3:H3');
        
        if (empty($fecfin)) {
            $fecfin = date('Y/m/d');
        } 
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('I3', "AL: ");    
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "I3");
        $objPHPExcel->getActiveSheet()->getStyle("I3")->getFont()->setBold(true);
        
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('J3', $fecfin);
                
        
        
        /*******************************FIN ENCABEZADO***************************************/
        
        /*******************************CUERPO DE EXCEL***************************************/
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A5', "Cliente")
                ->setCellValue('B5', 'Ruc')
                ->setCellValue('C5', 'Telefono')
                ->setCellValue('D5', 'Zona')
                ->setCellValue('E5', 'Direccion')
                //->setCellValue('F5', 'Deuda')
                ->setCellValue('F5', 'Linea')
                ->setCellValue('G5', 'Condición')
                ->setCellValue('H5', 'L/Crédito')
                ->setCellValue('I5', 'Importe Total')
                ->setCellValue('J5', 'Participación %')
                ->setCellValue('K5', 'Estado');
        $objPHPExcel->getActiveSheet()
                ->setSharedStyle($sharedStyle2, "A5:K5");
        $objPHPExcel->getActiveSheet()->getStyle("A5:K5")->getFont()->setBold(true);

        
        $ordenventa = $this->AutoLoadModel('ordenventa');
        
        $TotalFacturación=0;
        for ($i = 0; $i < $cantidadData; $i++) {
            $TotalFacturación+=$datos[$i]['importeTotalResuelto'];
        }
        
        $j=6;
        for ($i = 0; $i < $cantidadData; $i++) {
            $tempContado = ($datos[$i]['es_contado'] == 1 ? 'CONTADO' : '');
            $tempCredito = ($datos[$i]['es_credito'] == 1 ? 'CREDITO' : '');
            $tempLetras = ($datos[$i]['es_letras'] == 1 ? 'LETRAS' : '');
            
            $Auxcondiciones = "";
            if (!empty($tempContado)) {
                $Auxcondiciones = $tempContado;
            }
            if (!empty($tempCredito)) {
                if (empty($Auxcondiciones))
                    $Auxcondiciones = $tempCredito;
                else
                    $Auxcondiciones .= "/" . $tempCredito;
            }
            if (!empty($tempLetras)) {
                if (empty($Auxcondiciones))
                    $Auxcondiciones = $tempLetras;
                else
                    $Auxcondiciones .= "/" . $tempLetras;
            }
            $textLineacredito = (!empty($datos[$i]['lineacredito']) ? $datos[$i]['lineacredito'] : $datos[$i]['lineacreditodisponible']);
            
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A'.$j, $datos[$i]['cliente'])
                ->setCellValue('B'.$j, $datos[$i]['ruc'])
                ->setCellValue('C'.$j, ''.$datos[$i]['telefono'] . (empty($datos[$i]['telefono']) || empty($datos[$i]['celular']) ? "" : " / ") . $datos[$i]['celular'])
                ->setCellValue('D'.$j, $datos[$i]['nombrezona'])
                ->setCellValue('E'.$j, $datos[$i]['direccion'] . ' ' . $datos[$i]['dist'] . ' - ' . $datos[$i]['prov'] . ' - ' . $datos[$i]['depa'])
                //->setCellValue('F'.$j, 'S/ ' . number_format($datos[$i]['deudatotal'], 2))
                ->setCellValue('F'.$j, $ordenventa->lineadeventa($datos[$i]['idcliente']))
                ->setCellValue('G'.$j, $Auxcondiciones)
                ->setCellValue('H'.$j, 'S/ ' . number_format($textLineacredito, 2))
                ->setCellValue('I'.$j, 'S/ '.bcdiv(($datos[$i]['importeTotalResuelto']),'1',2))
                ->setCellValue('J'.$j, bcdiv((($datos[$i]['importeTotalResuelto']*100)/$TotalFacturación),'1',2).'%')
                ->setCellValue('K'.$j,$datos[$i]['deudatotal']>=1?'Atendidos':'No Atendidos');
            $objPHPExcel->getActiveSheet()
                ->setSharedStyle($sharedStyle3, "A".$j.":K".$j);
            $j++;
        }
        
        
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('H'.($j), "TOTAL: ");    
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, 'H'.($j));
        $objPHPExcel->getActiveSheet()->getStyle('H'.($j))->getFont()->setBold(true);
        
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('I'.($j),'S/ '.bcdiv($TotalFacturación,'1',2));  
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, 'I'.($j));
        $objPHPExcel->getActiveSheet()->getStyle('I'.($j))->getFont()->setBold(true);
        
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('J'.($j), "100%");  
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle4, 'J'.($j));
        $objPHPExcel->getActiveSheet()->getStyle('J'.($j))->getFont()->setBold(true);
        /*******************************FIN CUERPO DE EXCEL***************************************/
        
        
        /***************************FINAL EXCEL************************************/
        $objPHPExcel->getActiveSheet()->setTitle('Reporte de producto');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();
        
        readfile($filename);
        unlink($filename);
    }
    
    public function indicadoresventa_productos () {
        set_time_limit(1500);
        
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'indicadoresdespacho.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $anioconsultado = $_REQUEST['cmbAnio'];

        $lblMes[1] = 'ENERO';
        $lblMes[2] = 'FEBRERO';
        $lblMes[3] = 'MARZO';
        $lblMes[4] = 'ABRIL';
        $lblMes[5] = 'MAYO';
        $lblMes[6] = 'JUNIO';
        $lblMes[7] = 'JULIO';
        $lblMes[8] = 'AGOSTO';
        $lblMes[9] = 'SEPTIEMBRE';
        $lblMes[10] = 'OCTUBRE';
        $lblMes[11] = 'NOVIEMBRE';
        $lblMes[12] = 'DICIEMBRE';
        /*$mes = $_REQUEST['opcmes'];
        $anho = $_REQUEST['opcanho'];
        */
        
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('MES', 'CODIGO', 'DESCRIPCION', 'LINEA', 'UND VENDIDAS', 'MONTO VENTAS');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $contador = 1;
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':F' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "INDICADORES DE VENTA");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;
        $seguimiento = new seguimiento();
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":B" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":B" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "AÑO:")
                ->setCellValue('B' . ($contador), $anioconsultado);
                
        $contador++;
        $contador++;
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':F' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "PRODUCTO MAS VENDIDO EN SOLES CON RESPECTO AL IMPORTE");
        
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':C' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':A' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':D' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $contador . ':E' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':F' . ($contador+1));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":F" . ($contador+1));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador+1))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador+1))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), "PRODUCTO")
                ->setCellValue('B' . ($contador+1), $titulos[1])
                ->setCellValue('C' . ($contador+1), $titulos[2])
                ->setCellValue('D' . ($contador), $titulos[3])
                ->setCellValue('E' . ($contador), $titulos[4])
                ->setCellValue('F' . ($contador), $titulos[5]);
        $contador++;
        $contador++;
        FOR ($mesconsultado = 1; $mesconsultado <= 12; $mesconsultado++) {
            $ultimodia = cal_days_in_month(CAL_GREGORIAN, $mesconsultado, $anioconsultado);
            $fechainicio = $anioconsultado . '-' . $mesconsultado . '-01';
            $fechafin = $anioconsultado . '-' . $mesconsultado . '-' . $ultimodia;
            $dataMES_SOLES_TOTALFINAL = $seguimiento->indicadoresventa_productos ($fechainicio, $fechafin, 1, 'totalfinal');
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            if (count($dataMES_SOLES_TOTALFINAL) > 0) {
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $lblMes[$mesconsultado])
                            ->setCellValue('B' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['codigopa'])
                            ->setCellValue('C' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['nompro'])
                            ->setCellValue('D' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['nomlin'])
                            ->setCellValue('E' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['undvendida'])
                            ->setCellValue('F' . ($contador), 's/ ' . number_format($dataMES_SOLES_TOTALFINAL[0]['totalfinal'], 2));

            } else {
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $lblMes[$mesconsultado])
                            ->setCellValue('B' . ($contador), '-')
                            ->setCellValue('C' . ($contador), '-')
                            ->setCellValue('D' . ($contador), '-')
                            ->setCellValue('E' . ($contador), '-')
                            ->setCellValue('F' . ($contador), '-');
            }
            $contador++;
        }
        
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':F' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "PRODUCTO MAS VENDIDO EN SOLES CON RESPECTO A LA CANTIDAD VENDIDA");
        
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':C' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':A' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':D' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $contador . ':E' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':F' . ($contador+1));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":F" . ($contador+1));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador+1))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador+1))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), "PRODUCTO")
                ->setCellValue('B' . ($contador+1), $titulos[1])
                ->setCellValue('C' . ($contador+1), $titulos[2])
                ->setCellValue('D' . ($contador), $titulos[3])
                ->setCellValue('E' . ($contador), $titulos[4])
                ->setCellValue('F' . ($contador), $titulos[5]);
        $contador++;
        $contador++;
        FOR ($mesconsultado = 1; $mesconsultado <= 12; $mesconsultado++) {
            $ultimodia = cal_days_in_month(CAL_GREGORIAN, $mesconsultado, $anioconsultado);
            $fechainicio = $anioconsultado . '-' . $mesconsultado . '-01';
            $fechafin = $anioconsultado . '-' . $mesconsultado . '-' . $ultimodia;
            $dataMES_SOLES_TOTALFINAL = $seguimiento->indicadoresventa_productos ($fechainicio, $fechafin, 1, 'undvendida');
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            if (count($dataMES_SOLES_TOTALFINAL) > 0) {
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $lblMes[$mesconsultado])
                            ->setCellValue('B' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['codigopa'])
                            ->setCellValue('C' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['nompro'])
                            ->setCellValue('D' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['nomlin'])
                            ->setCellValue('E' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['undvendida'])
                            ->setCellValue('F' . ($contador), 's/ ' . number_format($dataMES_SOLES_TOTALFINAL[0]['totalfinal'], 2));

            } else {
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $lblMes[$mesconsultado])
                            ->setCellValue('B' . ($contador), '-')
                            ->setCellValue('C' . ($contador), '-')
                            ->setCellValue('D' . ($contador), '-')
                            ->setCellValue('E' . ($contador), '-')
                            ->setCellValue('F' . ($contador), '-');
            }
            $contador++;
        }/*
        
        $contador++;
        $contador++;
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':F' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "PRODUCTO MAS VENDIDO EN DOLARES CON RESPECTO AL IMPORTE");
        
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':C' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':A' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':D' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $contador . ':E' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':F' . ($contador+1));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":F" . ($contador+1));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador+1))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador+1))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), "PRODUCTO")
                ->setCellValue('B' . ($contador+1), $titulos[1])
                ->setCellValue('C' . ($contador+1), $titulos[2])
                ->setCellValue('D' . ($contador), $titulos[3])
                ->setCellValue('E' . ($contador), $titulos[4])
                ->setCellValue('F' . ($contador), $titulos[5]);
        $contador++;
        $contador++;
        FOR ($mesconsultado = 1; $mesconsultado <= 12; $mesconsultado++) {
            $ultimodia = cal_days_in_month(CAL_GREGORIAN, $mesconsultado, $anioconsultado);
            $fechainicio = $anioconsultado . '-' . $mesconsultado . '-01';
            $fechafin = $anioconsultado . '-' . $mesconsultado . '-' . $ultimodia;
            $dataMES_SOLES_TOTALFINAL = $seguimiento->indicadoresventa_productos ($fechainicio, $fechafin, 2, 'totalfinal');
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            if (count($dataMES_SOLES_TOTALFINAL) > 0) {
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $lblMes[$mesconsultado])
                            ->setCellValue('B' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['codigopa'])
                            ->setCellValue('C' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['nompro'])
                            ->setCellValue('D' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['nomlin'])
                            ->setCellValue('E' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['undvendida'])
                            ->setCellValue('F' . ($contador), 'US $ ' . number_format($dataMES_SOLES_TOTALFINAL[0]['totalfinal'], 2));

            } else {
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $lblMes[$mesconsultado])
                            ->setCellValue('B' . ($contador), '-')
                            ->setCellValue('C' . ($contador), '-')
                            ->setCellValue('D' . ($contador), '-')
                            ->setCellValue('E' . ($contador), '-')
                            ->setCellValue('F' . ($contador), '-');
            }
            $contador++;
        }
        
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':F' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle5, "A" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "PRODUCTO MAS VENDIDO EN DOLARES CON RESPECTO A LA CANTIDAD VENDIDA");
        
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':C' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':A' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':D' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $contador . ':E' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':F' . ($contador+1));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":F" . ($contador+1));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador+1))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador+1))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), "PRODUCTO")
                ->setCellValue('B' . ($contador+1), $titulos[1])
                ->setCellValue('C' . ($contador+1), $titulos[2])
                ->setCellValue('D' . ($contador), $titulos[3])
                ->setCellValue('E' . ($contador), $titulos[4])
                ->setCellValue('F' . ($contador), $titulos[5]);
        $contador++;
        $contador++;
        FOR ($mesconsultado = 1; $mesconsultado <= 12; $mesconsultado++) {
            $ultimodia = cal_days_in_month(CAL_GREGORIAN, $mesconsultado, $anioconsultado);
            $fechainicio = $anioconsultado . '-' . $mesconsultado . '-01';
            $fechafin = $anioconsultado . '-' . $mesconsultado . '-' . $ultimodia;
            $dataMES_SOLES_TOTALFINAL = $seguimiento->indicadoresventa_productos ($fechainicio, $fechafin, 2, 'undvendida');
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            if (count($dataMES_SOLES_TOTALFINAL) > 0) {
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $lblMes[$mesconsultado])
                            ->setCellValue('B' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['codigopa'])
                            ->setCellValue('C' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['nompro'])
                            ->setCellValue('D' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['nomlin'])
                            ->setCellValue('E' . ($contador), $dataMES_SOLES_TOTALFINAL[0]['undvendida'])
                            ->setCellValue('F' . ($contador), 'US $ ' . number_format($dataMES_SOLES_TOTALFINAL[0]['totalfinal'], 2));

            } else {
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A' . ($contador), $lblMes[$mesconsultado])
                            ->setCellValue('B' . ($contador), '-')
                            ->setCellValue('C' . ($contador), '-')
                            ->setCellValue('D' . ($contador), '-')
                            ->setCellValue('E' . ($contador), '-')
                            ->setCellValue('F' . ($contador), '-');
            }
            $contador++;
        }*/

        $objPHPExcel->getActiveSheet()->setTitle('INDICADORES VENTAS');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    
    public function indicadoresdespacho_productos () {
        set_time_limit(1500);
        
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'indicadoresdespacho.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $mesconsultado = $_REQUEST['cmbMes'];
        $anioconsultado = $_REQUEST['cmbAnio'];

        $lblMes['01'] = 'ENERO';
        $lblMes['02'] = 'FEBRERO';
        $lblMes['03'] = 'MARZO';
        $lblMes['04'] = 'ABRIL';
        $lblMes['05'] = 'MAYO';
        $lblMes['06'] = 'JUNIO';
        $lblMes['07'] = 'JULIO';
        $lblMes['08'] = 'AGOSTO';
        $lblMes['09'] = 'SEPTIEMBRE';
        $lblMes['10'] = 'OCTUBRE';
        $lblMes['11'] = 'NOVIEMBRE';
        $lblMes['12'] = 'DICIEMBRE';
        /*$mes = $_REQUEST['opcmes'];
        $anho = $_REQUEST['opcanho'];
        */
        
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('DIA', 'ORDEN DE VENTA', 'ORDEN DE DESPACHO');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $contador = 1;
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':C' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "INDICADORES DE DESPACHO");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;
        $seguimiento = new seguimiento();
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":B" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":B" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "MES:")
                ->setCellValue('B' . ($contador), $lblMes[$mesconsultado]);
        $contador++;   
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":B" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":B" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "AÑO:")
                ->setCellValue('B' . ($contador), $anioconsultado);
                
        $contador++;
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), $titulos[1])
                ->setCellValue('C' . ($contador), $titulos[2]);
        $contador++;
        
        $ultimodia = cal_days_in_month(CAL_GREGORIAN, $mesconsultado, $anioconsultado);
        $importeTotalVenta = 0;
        $importeTotalDespacho = 0;
        for ($dia = 1; $dia <= $ultimodia; $dia++) {            
            $fechaxdia = $anioconsultado . '-' . $mesconsultado . '-' . str_pad($dia, 2, "0", STR_PAD_LEFT);
            $dataxDiaVenta = $seguimiento->indicadoresdespacho_productos_VENTA($fechaxdia);
            $dataxDiaDespacho = $seguimiento->indicadoresdespacho_productos_DESPACHO($fechaxdia);
            $tempVenta = (count($dataxDiaVenta) > 0 ? $dataxDiaVenta[0]['totalpedidos'] : 0);
            $empDespacho = (count($dataxDiaDespacho) > 0 ? $dataxDiaDespacho[0]['totaldespacho'] : 0);
            $importeTotalVenta += $tempVenta;
            $importeTotalDespacho += $empDespacho;
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":C" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $fechaxdia)
                    ->setCellValue('B' . ($contador), $tempVenta)
                    ->setCellValue('C' . ($contador), $empDespacho);
            $contador++;
        }
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "TOTAL:")
                ->setCellValue('B' . ($contador), $importeTotalVenta)
                ->setCellValue('C' . ($contador), $importeTotalDespacho);

        $objPHPExcel->getActiveSheet()->setTitle('INDICADORES DESPACHO');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }

    public function pedidosentregados() {
        set_time_limit(1500);
        
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'pedidosentregados.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $mesconsultado = $_REQUEST['cmbMes'];
        $anioconsultado = $_REQUEST['cmbAnio'];

        $lblMes['01'] = 'ENERO';
        $lblMes['02'] = 'FEBRERO';
        $lblMes['03'] = 'MARZO';
        $lblMes['04'] = 'ABRIL';
        $lblMes['05'] = 'MAYO';
        $lblMes['06'] = 'JUNIO';
        $lblMes['07'] = 'JULIO';
        $lblMes['08'] = 'AGOSTO';
        $lblMes['09'] = 'SEPTIEMBRE';
        $lblMes['10'] = 'OCTUBRE';
        $lblMes['11'] = 'NOVIEMBRE';
        $lblMes['12'] = 'DICIEMBRE';
        /*$mes = $_REQUEST['opcmes'];
        $anho = $_REQUEST['opcanho'];
        */
        
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('FECHA', 'N° PEDIDOS', 'BULTOS', 'VALOR', 'ENTREGA OK*', 'NO ENTREGADO');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $contador = 1;
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':F' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "PEDIDOS ENTREGADOS");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;
        $seguimiento = new seguimiento();
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "D" . ($contador) . ":D" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "MES:")
                ->setCellValue('B' . ($contador), $lblMes[$mesconsultado])
                ->setCellValue('C' . ($contador), "AÑO:")
                ->setCellValue('D' . ($contador), $anioconsultado);
                
        $contador++;
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), $titulos[1])
                ->setCellValue('C' . ($contador), $titulos[2])
                ->setCellValue('D' . ($contador), $titulos[3])
                ->setCellValue('E' . ($contador), $titulos[4])
                ->setCellValue('F' . ($contador), $titulos[5]);
        $contador++;
        
        $ultimodia = cal_days_in_month(CAL_GREGORIAN, $mesconsultado, $anioconsultado);
        
        for ($dia = 1; $dia <= $ultimodia; $dia++) {
            $fechaxdia = $anioconsultado . '-' . $mesconsultado . '-' . str_pad($dia, 2, "0", STR_PAD_LEFT);
            $dataxDia = $seguimiento->pedidosentregados_seguiminetoxdia($fechaxdia);
            $tam = count($dataxDia);
            if ($tam == 0) {
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), $fechaxdia)
                        ->setCellValue('B' . ($contador), '-')
                        ->setCellValue('C' . ($contador), '-')
                        ->setCellValue('D' . ($contador), '-')
                        ->setCellValue('E' . ($contador), '-')
                        ->setCellValue('F' . ($contador), '-');
                $contador++;
            }
            $auxDataDia = array();            
            for ($j = 0; $j < $tam; $j++) {
                if (!isset($auxDataDia[$dataxDia[$j]['moneda']]['totalpedidos'])) {
                    if ($dataxDia[$j]['moneda'] == 1) {
                        $auxDataDia[$dataxDia[$j]['moneda']]['simbolo'] = 'S/ ';
                    } else {
                        $auxDataDia[$dataxDia[$j]['moneda']]['simbolo'] = 'US $ ';
                    }
                    $auxDataDia[$dataxDia[$j]['moneda']]['totalpedidos'] = 0;
                    $auxDataDia[$dataxDia[$j]['moneda']]['bultos'] = 0;
                    $auxDataDia[$dataxDia[$j]['moneda']]['valor'] = 0;
                    $auxDataDia[$dataxDia[$j]['moneda']]['entregado'] = 0;
                    $auxDataDia[$dataxDia[$j]['moneda']]['noentregado'] = 0;
                }
                $auxDataDia[$dataxDia[$j]['moneda']]['totalpedidos'] += $dataxDia[$j]['totalpedidos'];
                $auxDataDia[$dataxDia[$j]['moneda']]['bultos'] += $dataxDia[$j]['bultos'];
                $auxDataDia[$dataxDia[$j]['moneda']]['valor'] += $dataxDia[$j]['valor'];
                if ($dataxDia[$j]['confirmacion'][0] == 'E') {
                    $auxDataDia[$dataxDia[$j]['moneda']]['entregado']+=$dataxDia[$j]['totalpedidos'];
                } else {
                    $auxDataDia[$dataxDia[$j]['moneda']]['noentregado']+=$dataxDia[$j]['totalpedidos'];
                }
            }
            foreach ($auxDataDia as $auxdia) {
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), $fechaxdia)
                        ->setCellValue('B' . ($contador), $auxdia['totalpedidos'])
                        ->setCellValue('C' . ($contador), $auxdia['bultos'])
                        ->setCellValue('D' . ($contador), $auxdia['simbolo'] . ' ' . $auxdia['valor'])
                        ->setCellValue('E' . ($contador), $auxdia['entregado'])
                        ->setCellValue('F' . ($contador), $auxdia['noentregado']);
                $contador++;
            }
            unset($auxDataDia);
        }
        
        
        
        
        

        
        
        /*
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "B" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":C" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "TOTAL INCOBRABLE:")
                ->setCellValue('B' . ($contador), 'S/. ' . number_format($sPMontCont + $sPMontCredi + $sPMontLet + $sPMontLetPro, 2))
                ->setCellValue('C' . ($contador), 'US $ ' . number_format($dPMontCont + $dPMontCredi + $dPMontLet + $dPMontLetPro, 2));
        $contador++;
        */
        $objPHPExcel->getActiveSheet()->setTitle('Pedidos Entregados');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }

    function devolucionesxmeses() {
        set_time_limit(1500);
        
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'pedidosentregados.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $mesconsultado = $_REQUEST['cmbMes'];
        $anioconsultado = $_REQUEST['cmbAnio'];

        $lblMes['01'] = 'ENERO';
        $lblMes['02'] = 'FEBRERO';
        $lblMes['03'] = 'MARZO';
        $lblMes['04'] = 'ABRIL';
        $lblMes['05'] = 'MAYO';
        $lblMes['06'] = 'JUNIO';
        $lblMes['07'] = 'JULIO';
        $lblMes['08'] = 'AGOSTO';
        $lblMes['09'] = 'SEPTIEMBRE';
        $lblMes['10'] = 'OCTUBRE';
        $lblMes['11'] = 'NOVIEMBRE';
        $lblMes['12'] = 'DICIEMBRE';
        $fechainicio = $anioconsultado . '-' . $mesconsultado . '-01';
        $fechafin = $anioconsultado . '-' . $mesconsultado . '-' . cal_days_in_month(CAL_GREGORIAN, $mesconsultado, $anioconsultado);
        
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('PRODUCTO', 'CANTIDAD', 'VALOR', 'SALIDA', 'RE-INGRESO');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $contador = 1;
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':G' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":G" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":G" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":G" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "DEVOLUCIONES");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;
        $seguimiento = new seguimiento();
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "D" . ($contador) . ":D" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":G" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":G" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "MES:")
                ->setCellValue('B' . ($contador), $lblMes[$mesconsultado])
                ->setCellValue('C' . ($contador), "AÑO:")
                ->setCellValue('D' . ($contador), $anioconsultado);
                
        $contador++;
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":G" . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':C' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':E' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':F' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G' . $contador . ':G' . ($contador+1));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":G" . ($contador+1))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":G" . ($contador+1))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('A' . ($contador+1), "Codigo")
                ->setCellValue('B' . ($contador+1), "Descripcion")
                ->setCellValue('C' . ($contador), $titulos[1])
                ->setCellValue('D' . ($contador), $titulos[2])
                ->setCellValue('D' . ($contador+1), "Soles")
                ->setCellValue('E' . ($contador+1), "Dolares")
                ->setCellValue('F' . ($contador), $titulos[3])
                ->setCellValue('G' . ($contador), $titulos[4]);
        $contador++;
        $contador++;
        $seguimiento = new seguimiento();
        $dataMes = $seguimiento->devolucionesxmeses($fechainicio, $fechafin);
        $tam = count($dataMes);
        $auxDataProducto = array();            
        for ($j = 0; $j < $tam; $j++) {
            if (!isset($auxDataProducto[$dataMes[$j]['idproducto']]['idproducto'])) {
                $auxDataProducto[$dataMes[$j]['idproducto']]['idproducto'] = $dataMes[$j]['idproducto'];
                $auxDataProducto[$dataMes[$j]['idproducto']]['codigopa'] = $dataMes[$j]['codigopa'];
                $auxDataProducto[$dataMes[$j]['idproducto']]['nompro'] = $dataMes[$j]['nompro'];
                $auxDataProducto[$dataMes[$j]['idproducto']]['cantidad'] = 0;
                $auxDataProducto[$dataMes[$j]['idproducto']]['cantidadsalida'] = $dataMes[$j]['cantidadsalida'];
                $auxDataProducto[$dataMes[$j]['idproducto']]['cantidadingreso'] = $dataMes[$j]['cantidadingreso'];
                $auxDataProducto[$dataMes[$j]['idproducto']]['importedolares'] = 0.00;
                $auxDataProducto[$dataMes[$j]['idproducto']]['importesoles'] = 0.00;
            }
            if ($dataMes[$j]['moneda'] == 1) {
                $auxDataProducto[$dataMes[$j]['idproducto']]['importesoles'] = $dataMes[$j]['importe'];
            } else {
                $auxDataProducto[$dataMes[$j]['idproducto']]['importedolares'] = $dataMes[$j]['importe'];
            }
            $auxDataProducto[$dataMes[$j]['idproducto']]['cantidad'] += $dataMes[$j]['cantidad'];
        }
        foreach ($auxDataProducto as $auxProducto) {
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":G" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":G" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $auxProducto['codigopa'])
                    ->setCellValue('B' . ($contador), $auxProducto['nompro'])
                    ->setCellValue('C' . ($contador), $auxProducto['cantidad'])
                    ->setCellValue('D' . ($contador), 'S/ ' . round($auxProducto['importesoles'], 2))
                    ->setCellValue('E' . ($contador), 'US $ ' . round($auxProducto['importedolares'], 2))
                    ->setCellValue('F' . ($contador), $auxProducto['cantidadsalida'])
                    ->setCellValue('G' . ($contador), $auxProducto['cantidadingreso']);
            $contador++;
        }
        unset($auxDataProducto);
        /*
        for ($i = 0; $i < $tam; $i++) {
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $fechaxdia)
                    ->setCellValue('B' . ($contador), $auxdia['totalpedidos'])
                    ->setCellValue('C' . ($contador), $auxdia['bultos'])
                    ->setCellValue('D' . ($contador), $auxdia['simbolo'] . ' ' . $auxdia['valor'])
                    ->setCellValue('E' . ($contador), $auxdia['entregado'])
                    ->setCellValue('F' . ($contador), $auxdia['noentregado']);
            $contador++;
            echo $dataMes[$i]['codigopa'] . ' ' . $dataMes[$i]['nompro'] . ' <br>';
        }
        */
        
        $objPHPExcel->getActiveSheet()->setTitle('Devoluciones');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }

    function pedidosentregados_productos() {
        set_time_limit(1500);
        
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'pedidosentregadosxproducto.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;
        
        $mesconsultado = $_REQUEST['cmbMes'];
        $anioconsultado = $_REQUEST['cmbAnio'];

        $lblMes['01'] = 'ENERO';
        $lblMes['02'] = 'FEBRERO';
        $lblMes['03'] = 'MARZO';
        $lblMes['04'] = 'ABRIL';
        $lblMes['05'] = 'MAYO';
        $lblMes['06'] = 'JUNIO';
        $lblMes['07'] = 'JULIO';
        $lblMes['08'] = 'AGOSTO';
        $lblMes['09'] = 'SEPTIEMBRE';
        $lblMes['10'] = 'OCTUBRE';
        $lblMes['11'] = 'NOVIEMBRE';
        $lblMes['12'] = 'DICIEMBRE';
        $fechainicio = $anioconsultado . '-' . $mesconsultado . '-01';
        $fechafin = $anioconsultado . '-' . $mesconsultado . '-' . cal_days_in_month(CAL_GREGORIAN, $mesconsultado, $anioconsultado);
        
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $titulos = array('PRODUCTO', 'CANTIDAD', 'VALOR', 'ENTREGADO OK*', 'NO ENTREGADO', 'DEVOLUCION');

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
                )
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $contador = 1;
        
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':H' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":H" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":H" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":H" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "PEDIDOS ENTREGADOS POR PRODUCTO");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;
        $seguimiento = new seguimiento();
        $contador++;
        
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":A" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "B" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "C" . ($contador) . ":C" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "D" . ($contador) . ":D" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":H" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":H" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "MES:")
                ->setCellValue('B' . ($contador), $lblMes[$mesconsultado])
                ->setCellValue('C' . ($contador), "AÑO:")
                ->setCellValue('D' . ($contador), $anioconsultado);
                
        $contador++;
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":H" . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':C' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':E' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':F' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('G' . $contador . ':G' . ($contador+1));
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('H' . $contador . ':H' . ($contador+1));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":H" . ($contador+1))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":H" . ($contador+1))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('A' . ($contador+1), "Codigo")
                ->setCellValue('B' . ($contador+1), "Descripcion")
                ->setCellValue('C' . ($contador), $titulos[1])
                ->setCellValue('D' . ($contador), $titulos[2])
                ->setCellValue('D' . ($contador+1), "Soles")
                ->setCellValue('E' . ($contador+1), "Dolares")
                ->setCellValue('F' . ($contador), $titulos[3])
                ->setCellValue('G' . ($contador), $titulos[4])
                ->setCellValue('H' . ($contador), $titulos[5]);
        $contador++;
        $contador++;
        $seguimiento = new seguimiento();
        $dataMes = $seguimiento->pedidosentregados_productosmeses($fechainicio, $fechafin);
        $tam = count($dataMes);
        $auxDataProducto = array();            
        for ($j = 0; $j < $tam; $j++) {
            if (!isset($auxDataProducto[$dataMes[$j]['idproducto']]['idproducto'])) {
                $auxDataProducto[$dataMes[$j]['idproducto']]['idproducto'] = $dataMes[$j]['idproducto'];
                $auxDataProducto[$dataMes[$j]['idproducto']]['codigopa'] = $dataMes[$j]['codigopa'];
                $auxDataProducto[$dataMes[$j]['idproducto']]['nompro'] = $dataMes[$j]['nompro'];
                $auxDataProducto[$dataMes[$j]['idproducto']]['cantidad'] = 0;                
                $auxDataProducto[$dataMes[$j]['idproducto']]['importedolares'] = 0.00;
                $auxDataProducto[$dataMes[$j]['idproducto']]['importesoles'] = 0.00;
                $auxDataProducto[$dataMes[$j]['idproducto']]['entregado'] = 0;
                $auxDataProducto[$dataMes[$j]['idproducto']]['noentregado'] = 0;
                $auxDataProducto[$dataMes[$j]['idproducto']]['devolucion'] = 0;
            }
            if ($dataMes[$j]['confirmacion'][0] == 'E') {
                $auxDataProducto[$dataMes[$j]['idproducto']]['entregado']+=$dataMes[$j]['cantidad'];
            } else {
                $temporalDevuelto = $seguimiento->productosdevueltos_devuletos($dataMes[$j]['idproducto'], $dataMes[$j]['idordenventa']);
                if ($temporalDevuelto > 0) {
                    $auxDataProducto[$dataMes[$j]['idproducto']]['noentregado']+=$dataMes[$j]['cantidad'];
                    $auxDataProducto[$dataMes[$j]['idproducto']]['devolucion'] += $temporalDevuelto;
                } else {
                    $auxDataProducto[$dataMes[$j]['idproducto']]['entregado']+=$dataMes[$j]['cantidad'];
                }
            }
            if ($dataMes[$j]['moneda'] == 1) {
                $auxDataProducto[$dataMes[$j]['idproducto']]['importesoles'] += $dataMes[$j]['importe'];
            } else {
                $auxDataProducto[$dataMes[$j]['idproducto']]['importedolares'] += $dataMes[$j]['importe'];
            }
            $auxDataProducto[$dataMes[$j]['idproducto']]['cantidad'] += $dataMes[$j]['cantidad'];
        }
        foreach ($auxDataProducto as $auxProducto) {
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":H" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":H" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $auxProducto['codigopa'])
                    ->setCellValue('B' . ($contador), $auxProducto['nompro'])
                    ->setCellValue('C' . ($contador), $auxProducto['cantidad'])
                    ->setCellValue('D' . ($contador), 'S/ ' . round($auxProducto['importesoles'], 2))
                    ->setCellValue('E' . ($contador), 'US $ ' . round($auxProducto['importedolares'], 2))
                    ->setCellValue('F' . ($contador), $auxProducto['entregado'])
                    ->setCellValue('G' . ($contador), $auxProducto['noentregado'])
                    ->setCellValue('H' . ($contador), $auxProducto['devolucion']);
            $contador++;
        }
        unset($auxDataProducto);
        /*
        for ($i = 0; $i < $tam; $i++) {
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), $fechaxdia)
                    ->setCellValue('B' . ($contador), $auxdia['totalpedidos'])
                    ->setCellValue('C' . ($contador), $auxdia['bultos'])
                    ->setCellValue('D' . ($contador), $auxdia['simbolo'] . ' ' . $auxdia['valor'])
                    ->setCellValue('E' . ($contador), $auxdia['entregado'])
                    ->setCellValue('F' . ($contador), $auxdia['noentregado']);
            $contador++;
            echo $dataMes[$i]['codigopa'] . ' ' . $dataMes[$i]['nompro'] . ' <br>';
        }
        */
        
        $objPHPExcel->getActiveSheet()->setTitle('Productos');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();

        readfile($filename);
        unlink($filename);
    }
    
    function rankingingresos_resumen() {
        set_time_limit(1800);
        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'ranking_ingresos_x_cobrador_resumen.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;

        $fechaInicio = !empty($_REQUEST['fechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['fechaInicio'])) : null;
        $fechaFinal = !empty($_REQUEST['fechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['fechaFinal'])) : null;
        $nroRecibo = $_REQUEST['nroRecibo'];
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $idCliente = $_REQUEST['idCliente'];
        $idCobrador = $_REQUEST['idCobrador'];
        $idTipoCobro = $_REQUEST['idTipoCobro'];
        $simbolo = $_REQUEST['simbolo'];
        $monto = $_REQUEST['monto'];
        $cmbtipo = $_REQUEST['cmbTipo'];
        if ($idtipocobro != 1 && $idtipocobro != 3) {
            $cmbtipo = '';
        }
        $this->configIniTodo('TipoIngreso');
        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();
        

        $sharedStyle6 = new PHPExcel_Style();
        $sharedStyle6->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFBBCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ))
        );

        $sharedStyle5 = new PHPExcel_Style();
        $sharedStyle5->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAECECC')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ))
        );

        $sharedStyle4 = new PHPExcel_Style();
        $sharedStyle4->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFAA8888')
            ), 'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ))
        );

        $sharedStyle3 = new PHPExcel_Style();
        $sharedStyle3->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCDDF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ))
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ))
        );

        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCCCCC')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ))
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ), 'borders' => array(
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ), 'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ))
        );

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        
        $contador = 1;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':F' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":F" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "RESUMEN DE RANKING DE COBRANZAS GENERAL");
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;

        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':D' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle6, "A" . ($contador) . ":B" . ($contador));
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "C" . ($contador) . ":D" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), "FECHA: ")
                ->setCellValue('C' . ($contador), $fechaInicio . " - " . $fechaFinal);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;

        $nombremoneda = "Soles";
        $moneda = "S/. ";

        for ($vuelta = 1; $vuelta <= 2; $vuelta++) {
            $contador++;
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':F' . $contador);
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle6, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "Resumen de Ranking de Cobranza en " . $nombremoneda);
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
            $contador++;
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle3, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':A' . $contador);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B' . $contador . ':B' . $contador);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C' . $contador . ':C' . $contador);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D' . $contador . ':D' . $contador);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('E' . $contador . ':E' . $contador);
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('F' . $contador . ':F' . $contador);
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "Nro.")
                    ->setCellValue('B' . ($contador), "Cobrador")
                    ->setCellValue('C' . ($contador), "Total")
                    ->setCellValue('D' . ($contador), "Contado")
                    ->setCellValue('E' . ($contador), "Crédito")
                    ->setCellValue('F' . ($contador), "Letra");
            $contador++;

            $ingresos = $this->AutoLoadModel('ingresos');
            $dataIngresos = $ingresos->rankingGeneralXVendedor($fechaInicio, $fechaFinal, $idOrdenVenta, $idCliente, $idCobrador, $idTipoCobro, $cmbtipo, $nroRecibo, $simbolo, $monto, $vuelta);
            $tam = count($dataIngresos);
            $total = 0;
            $totales[1] = 0.00;
            $totales[2] = 0.00;
            $totales[3] = 0.00;
            for ($i = 0; $i < $tam; $i++) {
                $montos[1] = 0.00;
                $montos[2] = 0.00;
                $montos[3] = 0.00;              
                $dataCobrador = $ingresos->rankingDetalladoXVendedor_resumen($fechaInicio, $fechaFinal, $idOrdenVenta, $idCliente, $dataIngresos[$i]['idcobrador'], $idTipoCobro, $cmbtipo, $nroRecibo, $simbolo, $monto, $vuelta);
                $tamanio = count($dataCobrador);
                for ($j = 0; $j < $tamanio; $j++) {
                    $montos[$dataCobrador[$j]['formacobro']] += $dataCobrador[$j]['totalasignado'];
                }
                $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":F" . ($contador));
                $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
                $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A' . ($contador), " " . ($i + 1))
                        ->setCellValue('B' . ($contador), $dataIngresos[$i]['nombrecobrador'])
                        ->setCellValue('C' . ($contador), $moneda . number_format($dataIngresos[$i]['totalcobrado'], 2))
                        ->setCellValue('D' . ($contador), $moneda . number_format($montos[1], 2))
                        ->setCellValue('E' . ($contador), $moneda . number_format($montos[2], 2))
                        ->setCellValue('F' . ($contador), $moneda . number_format($montos[3], 2));
                $contador++;
                $total += $dataIngresos[$i]['totalcobrado'];
                $totales[1] += $montos[1];
                $totales[2] += $montos[2];
                $totales[3] += $montos[3];
            }
            $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":F" . ($contador));
            $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':B' . $contador);
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":F" . ($contador))->getFill()->setRotation(1);
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . ($contador), "MONTO TOTAL COBRADO: ")
                    ->setCellValue('C' . ($contador), $moneda . number_format($total, 2))
                    ->setCellValue('D' . ($contador), $moneda . number_format($totales[1], 2))
                    ->setCellValue('E' . ($contador), $moneda . number_format($totales[2], 2))
                    ->setCellValue('F' . ($contador), $moneda . number_format($totales[3], 2));
            $contador++;
            $contador++;
            $nombremoneda = "Dolares";
            $moneda = "US $. ";
        }

        $objPHPExcel->getActiveSheet()->setTitle('Ranking Resumen');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);
        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();
        readfile($filename);
        unlink($filename);
    }
    
    public function ventasfacturadonofacturado1() {
        set_time_limit(1000);
        $url_fechaini = $_REQUEST['txtFechaInicio'];
        $url_fechafin = $_REQUEST['txtFechaFinal'];
        $url_idmoneda = $_REQUEST['cmbMoneda'];
        $url_situacion=$_REQUEST['cmbSituacion'];
        $url_monto=$_REQUEST['cmbMonto'];
        $url_anulados=$_REQUEST['cmbAnulados'];
        $url_opcion = $_REQUEST['cmbFiltro']; // 0 = todo, 1 = facturado, 2 = no facturado
        $esAnulado = $_REQUEST['cmbEstado'];
        $filtro = "";
        if ($url_idmoneda == 1) {
            $filtro = "VENTAS SOLO EN SOLES";
        }
        if ($url_idmoneda == 2) {
            $filtro = "VENTAS SOLO EN DOLARES";
        }
        $reporte = $this->AutoLoadModel('reporte');
        $listar_ventasfacturadonofacturado1 = $reporte->ventasfacturadonofacturado1($url_fechaini, $url_fechafin, $url_idmoneda, $url_situacion,$url_monto,$url_anulados);
        //********************************* Proceso de trasmutacion de ovs generadas de otros dias pero facturadas segun la fecha enviada
        $get_segregado_idordenventas1 = '';
        for ($i = 0; $i < count($listar_ventasfacturadonofacturado1); $i++) {
            $idordenventa = $listar_ventasfacturadonofacturado1[$i]['idordenventa'];
            $get_segregado_idordenventas1 .= $idordenventa . ',';
        }
        $get_segregado_idordenventas1 = substr($get_segregado_idordenventas1, 0, -1);
        $listar_ovs_de_comprobantesFaltantes = array();
        if ($url_opcion != 2) {
            $listar_ovs_de_comprobantesFaltantes = $reporte->listar_ovs_de_comprobantesFaltantes($url_fechaini, $url_fechafin, $url_idmoneda, $get_segregado_idordenventas1, $url_situacion,$url_monto,$url_anulados);
        }
        $idordenventa = -1;
        $get_segregado_idordenventasFaltantes = '';
        for ($i = 0; $i < count($listar_ovs_de_comprobantesFaltantes); $i++) {
            if ($idordenventa != $listar_ovs_de_comprobantesFaltantes[$i]['idordenventa']) {
                $idordenventa = $listar_ovs_de_comprobantesFaltantes[$i]['idordenventa'];
                $get_segregado_idordenventasFaltantes .= $idordenventa . ',';
            }
        }
        $get_segregado_idordenventasFaltantes = substr($get_segregado_idordenventasFaltantes, 0, -1);
        if ($get_segregado_idordenventas1 != "" and $get_segregado_idordenventasFaltantes == "") {
            $get_segregado_total = $get_segregado_idordenventas1;
        }
        if ($get_segregado_idordenventas1 == "" and $get_segregado_idordenventasFaltantes != "") {
            $get_segregado_total = $get_segregado_idordenventasFaltantes;
        }
        if ($get_segregado_idordenventas1 != "" and $get_segregado_idordenventasFaltantes != "") {
            $get_segregado_total = $get_segregado_idordenventas1 . ',' . $get_segregado_idordenventasFaltantes;
        }
        $data = $reporte->ventasfacturadonofacturado2($get_segregado_total);
        //*********************************

        $baseURL = ROOT . 'descargas' . DS;
        $idActor = $_SESSION['idactor'];
        $fechaCreacion = date('Y-m-d_h.m.s');
        $basenombre = 'reporteVentasFacturadoNoFacturado.xls';
        $filename = $baseURL . $idActor . '_' . $fechaCreacion . '_' . $basenombre;

        $this->AutoLoadLib('PHPExcel');
        $objPHPExcel = new PHPExcel();

        $titulos = array('N', 'FECHA.OV', 'FECHA.DES', 'ORD VENTA', 'COND. INICIAL', 'RUC/DNI', 'CLIENTE', 'FACTURA', 'BOLETA', 'GUIA REMI', 'BI FACTURA', 'IGV FACT', 'BI BOLETA', 'IMPORT GUIA', 'TOTAL', 'Monto Perce', '%', 'Est Guia', 'Est comprobante');
        $sharedStyle1 = new PHPExcel_Style();
        $sharedStyle1->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FFCCFFCC')
            ),
            'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle0 = new PHPExcel_Style();
        $sharedStyle0->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('argb' => 'FF81BEF7')
            ),
            'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            )
                )
        );

        $sharedStyle2 = new PHPExcel_Style();
        $sharedStyle2->applyFromArray(array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'FFFFFFFF')
            ),
            'borders' => array(
                'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN),
                'right' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM)
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
            )
                )
        );


        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);

        $contador = 0;

        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':S' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":S" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFill()->setRotation(1);
        if ($url_opcion == 0) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($contador), "REPORTE DE VENTAS DE LO FACTURADO Y NO FACTURADO DEL " . $url_fechaini . " AL " . $url_fechafin);
        } else if ($url_opcion == 1) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($contador), "REPORTE DE VENTAS DE LO FACTURADO DEL " . $url_fechaini . " AL " . $url_fechafin);
        } else if ($url_opcion == 2) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($contador), "REPORTE DE VENTAS DE LO NO FACTURADO DEL " . $url_fechaini . " AL " . $url_fechafin);
        }

        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;
        $contador++;
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':S' . $contador);
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle2, "A" . ($contador) . ":S" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFill()->setRotation(1);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($contador), $filtro);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador))->getFont()->setBold(true);
        $contador++;



        $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . ($contador), $titulos[0])
                ->setCellValue('B' . ($contador), $titulos[1])
                ->setCellValue('C' . ($contador), $titulos[2])
                ->setCellValue('D' . ($contador), $titulos[3])
                ->setCellValue('E' . ($contador), $titulos[4])
                ->setCellValue('F' . ($contador), $titulos[5])
                ->setCellValue('G' . ($contador), $titulos[6])
                ->setCellValue('H' . ($contador), $titulos[7])
                ->setCellValue('I' . ($contador), $titulos[8])
                ->setCellValue('J' . ($contador), $titulos[9])
                ->setCellValue('K' . ($contador), $titulos[10])
                ->setCellValue('L' . ($contador), $titulos[11])
                ->setCellValue('M' . ($contador), $titulos[12])
                ->setCellValue('N' . ($contador), $titulos[13])
                ->setCellValue('O' . ($contador), $titulos[14])
                ->setCellValue('P' . ($contador), $titulos[15])
                ->setCellValue('Q' . ($contador), $titulos[16])
                ->setCellValue('R' . ($contador), $titulos[17])
                ->setCellValue('S' . ($contador), $titulos[18]);


        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle0, "A" . ($contador) . ":S" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFill()->setRotation(1);
        $contador ++;


        $nro_aumentador = 0;
        $sum_importeGuia_soles = 0.00;
        $sum_biBoleta_soles = 0.00;
        $sum_totalcomprobante_soles = 0.00;
        $sum_subtotalFactura_soles = 0.00;
        $sum_igvFactura_soles = 0.00;
        $sum_percepcion_soles = 0.00;
        $sum_importeGuia_dolares = 0.00;
        $sum_biBoleta_dolares = 0.00;
        $sum_totalcomprobante_dolares = 0.00;
        $sum_subtotalFactura_dolares = 0.00;
        $sum_igvFactura_dolares = 0.00;
        $sum_percepcion_dolares = 0.00;
        $idordenventaTemp = -1;
        for ($i = 0; $i < count($data); $i++) {
            $temporal_condicion_venta = '';
            if ($data[$i]['es_contado'] == 1) {
                $temporal_condicion_venta = 'CONTADO';
            }
            if ($data[$i]['es_credito'] == 1) {
                if (!empty($temporal_condicion_venta)) {
                    $temporal_condicion_venta .= '/CREDITO';
                } else {
                    $temporal_condicion_venta = 'CREDITO';
                }
            }
            if ($data[$i]['es_letras'] == 1) {
                if (!empty($temporal_condicion_venta)) {
                    $temporal_condicion_venta .= '/LETRAS';
                } else {
                    $temporal_condicion_venta = 'LETRAS';
                }
            }
            
            $serieGRemision = '';
            $numGRemision = '';
            $serieFactura = "";
            $correlativoFactura = "";
            $comprobanteFactura = "";

            $importeGuia_soles = 0.00;
            $subtotalFactura_soles = 0.00;
            $igvFactura_soles = 0.00;
            $totalcomprobante_soles = 0.00;
            $percepcion_soles = 0.00;
            $biBoleta_soles = 0.00;
            $porcentaje_percepcion_soles = '';

            $importeGuia_dolares = 0.00;
            $subtotalFactura_dolares = 0.00;
            $igvFactura_dolares = 0.00;
            $totalcomprobante_dolares = 0.00;
            $percepcion_dolares = 0.00;
            $biBoleta_dolares = 0.00;
            $porcentaje_percepcion_dolares = '';

            $que_comprobante_se_sumara = "";
            $serieBoleta = "";
            $correlativoBoleta = "";
            $comprobanteBoleta = '';
            $tipocomprobante = '';
            $electronico = '';
            $moneda = '';
            $estado_ov = '';
            $estadoComprobante = '';
            $documento = new Documento();
            $listar_comprobantes = $documento->listar_comprobantes($data[$i]['idordenventa'], $esAnulado);


            $tiene_comprobantes = count($listar_comprobantes);
            $listar_guia_remision = $documento->listar_guia_remision($data[$i]['idordenventa']);
            $tiene_guia_remision = count($listar_guia_remision);


            if ($data[$i]['estado_ov'] == '1') {
                $estado_ov = 'Activo';
            }
            if ($data[$i]['estado_ov'] == '0') {
                $estado_ov = 'Anulado';
            }

            //*************SE HACE DOS IMPRESIONES porque cuando comprobantes es igual a CERO  solo se imprime una fila
            //*************PERO CUANDO TIENE COMPROBANTES NO SABEMOS CUANTOS COMPROBANTES TENDRA UNA MISMA GUIA
            //*************POR ENDE UNA GUIA PUEDE TENER VARIAS FACTURAS (VARIAS IMPRESIONES EN UNA MISMA GUIA)
            if ($tiene_comprobantes == 0 && ($url_opcion == 2 || $url_opcion == 0)) {
                if ($tiene_comprobantes == 0 and $tiene_guia_remision >= 1) {
                    $serieGRemision = $listar_guia_remision[0]['serie'];
                    $numGRemision = $listar_guia_remision[0]['numdoc'];
                    //start solo suma guia que esta activa y lo hace una sola vez por orden
                    if ($idordenventaTemp != $data[$i]['idordenventa'] and $data[$i]['estado_ov'] == '1') {
                        if ($data[$i]['idmoneda'] == '1') {
                            $importeGuia_soles = $data[$i]['importeov'];
                            $sum_importeGuia_soles = $sum_importeGuia_soles + $importeGuia_soles;
                        }
                        if ($data[$i]['idmoneda'] == '2') {
                            $importeGuia_dolares = $data[$i]['importeov'];
                            $sum_importeGuia_dolares = $sum_importeGuia_dolares + $importeGuia_dolares;
                        }
                    }
                    //end solo suma guia que esta activa y lo hace una sola vez por orden
                }

                //START  CUANDO SOLO TIENE ORDEN DE VENTA Y NO TIENE GUIA DE REMISION, NI FACTURA, NI BOLETA
                if ($tiene_comprobantes == 0 and $tiene_guia_remision == 0 and $data[$i]['estado_ov'] == '1') {
                    //start suma el vaor de la ov a la constantes del total de guias de remision y lo hace una sola vez por orden
                    if ($idordenventaTemp != $data[$i]['idordenventa'] and $data[$i]['estado_ov'] == '1') {
                        if ($data[$i]['idmoneda'] == '1') {
                            $importeGuia_soles = $data[$i]['importeov'];
                            $sum_importeGuia_soles = $sum_importeGuia_soles + $importeGuia_soles;
                        }
                        if ($data[$i]['idmoneda'] == '2') {
                            $importeGuia_dolares = $data[$i]['importeov'];
                            $sum_importeGuia_dolares = $sum_importeGuia_dolares + $importeGuia_dolares;
                        }
                    }
                    //end suma el vaor de la ov a la constantes del total de guias de remision y lo hace una sola vez por orden
                }
                //END  CUANDO SOLO TIENE ORDEN DE VENTA Y NO TIENE GUIA DE REMISION, NI FACTURA, NI BOLETA
                //          ***************************************************************************************************
                // START  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
                if ($data[$i]['ruc'] == '') {
                    $ruc_dni = $data[$i]['dni'];
                } else {
                    $ruc_dni = $data[$i]['ruc'];
                }

                if ($data[$i]['idmoneda'] == '1') {
                    $moneda = 'S/';
                }
                if ($data[$i]['idmoneda'] == '2') {
                    $moneda = 'US $';
                }

                if ($subtotalFactura_soles > 0) {
                    $percepcion_soles = $moneda . ' ' . number_format($percepcion_soles, 2);
                    $porcentaje_percepcion_soles = $data[$i]['percepcion'] * 100;
                    $porcentaje_percepcion_soles = $porcentaje_percepcion_soles . '%';
                } else {
                    $percepcion_soles = '';
                    $porcentaje_percepcion_soles = '';
                }

                if ($subtotalFactura_dolares > 0) {
                    $percepcion_dolares = $moneda . ' ' . number_format($percepcion_dolares, 2);
                    $porcentaje_percepcion_dolares = $data[$i]['percepcion'] * 100;
                    $porcentaje_percepcion_dolares = $porcentaje_percepcion_dolares . '%';
                } else {
                    $percepcion_dolares = '';
                    $porcentaje_percepcion_dolares = '';
                }

                $nro_aumentador = $nro_aumentador + 1;

                if ($data[$i]['idmoneda'] == '1') {
                    //***********************
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($contador), $nro_aumentador);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . ($contador), $data[$i]['fordenventa']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . ($contador), $data[$i]['fechadespacho']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . ($contador), $data[$i]['codigov']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . ($contador), $temporal_condicion_venta);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . ($contador), $ruc_dni);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . ($contador), $data[$i]['razonsocial']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . ($contador), $comprobanteFactura);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . ($contador), $comprobanteBoleta);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), $serieGRemision . '-' . $numGRemision);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), $moneda . ' ' . number_format($subtotalFactura_soles, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . ($contador), $moneda . ' ' . number_format($igvFactura_soles, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . ($contador), $moneda . ' ' . number_format($biBoleta_soles, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), $moneda . ' ' . number_format($importeGuia_soles, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . ($contador), $moneda . ' ' . number_format($totalcomprobante_soles, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . ($contador), $percepcion_soles);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . ($contador), $porcentaje_percepcion_soles);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . ($contador), $estado_ov);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . ($contador), $estadoComprobante);
                    $contador ++;
                    //***********************
                }
                if ($data[$i]['idmoneda'] == '2') {
                    //***********************
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($contador), $nro_aumentador);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . ($contador), $data[$i]['fordenventa']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . ($contador), $data[$i]['fechadespacho']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . ($contador), $data[$i]['codigov']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . ($contador), $temporal_condicion_venta);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . ($contador), $ruc_dni);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . ($contador), $data[$i]['razonsocial']);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . ($contador), $comprobanteFactura);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . ($contador), $comprobanteBoleta);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), $serieGRemision . '-' . $numGRemision);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), $moneda . ' ' . number_format($subtotalFactura_dolares, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . ($contador), $moneda . ' ' . number_format($igvFactura_dolares, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . ($contador), $moneda . ' ' . number_format($biBoleta_dolares, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), $moneda . ' ' . number_format($importeGuia_dolares, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . ($contador), $moneda . ' ' . number_format($totalcomprobante_dolares, 2));
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . ($contador), $percepcion_dolares);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . ($contador), $porcentaje_percepcion_dolares);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . ($contador), $estado_ov);
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . ($contador), $estadoComprobante);
                    $contador ++;
                    //***********************
                }

                // END  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
                //              ****************************************************************************************************
            }



            if ($tiene_comprobantes >= 1 && ($url_opcion == 1 || $url_opcion == 0)) { // recrrera comprobantes asi esten anulados o activos pero solo sumara los activos
                //start limpia variables por vuelta
                $serieGRemision = '';
                $numGRemision = '';
                $importeGuia_soles = 0.00;
                $importeGuia_dolares = 0.00;
                //end limpia variables por vuelta


                $serieGRemision = $listar_guia_remision[0]['serie'];
                $numGRemision = $listar_guia_remision[0]['numdoc'];
                //start solo suma guia que esta activa y lo hace una sola vez por orden
                if ($idordenventaTemp != $data[$i]['idordenventa'] and $data[$i]['estado_ov'] == '1') {
                    if ($data[$i]['idmoneda'] == '1') {
                        $importeGuia_soles = $data[$i]['importeov'];
                        $sum_importeGuia_soles = $sum_importeGuia_soles + $importeGuia_soles;
                    }
                    if ($data[$i]['idmoneda'] == '2') {
                        $importeGuia_dolares = $data[$i]['importeov'];
                        $sum_importeGuia_dolares = $sum_importeGuia_dolares + $importeGuia_dolares;
                    }
                }
                //end solo suma guia que esta activa y lo hace una sola vez por orden

                for ($j = 0; $j < count($listar_comprobantes); $j++) {
                    //start limpia variables por vuelta
                    $serieFactura = "";
                    $correlativoFactura = "";
                    $comprobanteFactura = "";

                    $subtotalFactura_soles = 0.00;
                    $igvFactura_soles = 0.00;
                    $totalcomprobante_soles = 0.00;
                    $percepcion_soles = 0.00;
                    $biBoleta_soles = 0.00;
                    $porcentaje_percepcion_soles = '';

                    $subtotalFactura_dolares = 0.00;
                    $igvFactura_dolares = 0.00;
                    $totalcomprobante_dolares = 0.00;
                    $percepcion_dolares = 0.00;
                    $biBoleta_dolares = 0.00;
                    $porcentaje_percepcion_dolares = '';

                    $que_comprobante_se_sumara = "";
                    $serieBoleta = "";
                    $correlativoBoleta = "";
                    $comprobanteBoleta = '';
                    $tipocomprobante = '';
                    $electronico = '';
                    $moneda = '';
                    $estado_ov = '';
                    $estadoComprobante = '';
                    //end limpia variables por vuelta
                    if ($data[$i]['estado_ov'] == '1') {
                        $estado_ov = 'Activo';
                    }
                    if ($data[$i]['estado_ov'] == '0') {
                        $estado_ov = 'Anulado';
                    }

                    if ($listar_comprobantes[$j]['nombredoc'] == 1) { // si tiene facturas
                        // START OBTENIENDO CORRELATIVO FACTURA
                        if ($listar_comprobantes[$j]["electronico"] == 1) {
                            $electronico = '';
                            $serieFactura = $documento->add_ceros($listar_comprobantes[$j]['serie'], 3);
                            $serieFactura = "F" . $serieFactura;
                            $correlativoFactura = $documento->add_ceros($listar_comprobantes[$j]['numdoc'], 8);
                        }

                        if ($listar_comprobantes[$j]["electronico"] == 0) {
                            $electronico = 'FISICA';
                            $serieFactura = $listar_comprobantes[$j]['serie'];
                            $correlativoFactura = $listar_comprobantes[$j]['numdoc'];
                        }
                        $comprobanteFactura = $serieFactura . ' - ' . $correlativoFactura;
                        $tipocomprobante = "FACTURA " . $electronico;
                        // END OBTENIENDO CORRELATIVO FACTURA

                        if ($data[$i]['idmoneda'] == '1') {
                            $subtotalFactura_soles = $listar_comprobantes[$j]['montofacturado'] - $listar_comprobantes[$j]['montoigv'];
                            $igvFactura_soles = $listar_comprobantes[$j]['montoigv'];
                            $totalcomprobante_soles = $listar_comprobantes[$j]['montofacturado'];
                            $percepcion_soles = $listar_comprobantes[$j]['montofacturado'] * $data[$i]['percepcion'];
                            $percepcion_soles = number_format($percepcion_soles, 2);
                        }
                        if ($data[$i]['idmoneda'] == '2') {
                            $subtotalFactura_dolares = $listar_comprobantes[$j]['montofacturado'] - $listar_comprobantes[$j]['montoigv'];
                            $igvFactura_dolares = $listar_comprobantes[$j]['montoigv'];
                            $totalcomprobante_dolares = $listar_comprobantes[$j]['montofacturado'];
                            $percepcion_dolares = $listar_comprobantes[$j]['montofacturado'] * $data[$i]['percepcion'];
                            $percepcion_dolares = number_format($percepcion_dolares, 2);
                        }
                        if ($listar_comprobantes[$j]['esAnulado'] == 0) {
                            $estadoComprobante = 'Activo';
                        }
                        if ($listar_comprobantes[$j]['esAnulado'] == 1) {
                            $estadoComprobante = 'Anulado';
                        }
                        $que_comprobante_se_sumara = 'FACTURA';
                    }

                    if ($listar_comprobantes[$j]['nombredoc'] == 2) { // si tiene boleta
                        // START OBTENIENDO CORRELATIVO BOLETA
                        if ($listar_comprobantes[$j]["electronico"] == 1) {
                            $electronico = '';
                            $serieBoleta = $documento->add_ceros($listar_comprobantes[$j]['serie'], 3);
                            $serieBoleta = 'B' . $serieBoleta;
                            $correlativoBoleta = $documento->add_ceros($listar_comprobantes[$j]['numdoc'], 8);
                        }
                        if ($listar_comprobantes[$j]["electronico"] == 0) {
                            $electronico = 'FISICA';
                            $serieBoleta = $listar_comprobantes[$j]['serie'];
                            $correlativoBoleta = $listar_comprobantes[$j]['numdoc'];
                        }
                        $comprobanteBoleta = $serieBoleta . ' - ' . $correlativoBoleta;
                        $tipocomprobante = "BOLETA " . $electronico;
                        // END OBTENIENDO CORRELATIVO BOLETA

                        if ($data[$i]['idmoneda'] == '1') {
                            $biBoleta_soles = $listar_comprobantes[$j]['montofacturado'];
                            $totalcomprobante_soles = $listar_comprobantes[$j]['montofacturado'];
                        }
                        if ($data[$i]['idmoneda'] == '2') {
                            $biBoleta_dolares = $listar_comprobantes[$j]['montofacturado'];
                            $totalcomprobante_dolares = $listar_comprobantes[$j]['montofacturado'];
                        }

                        if ($listar_comprobantes[$j]['esAnulado'] == 0) {
                            $estadoComprobante = 'Activo';
                        }
                        if ($listar_comprobantes[$j]['esAnulado'] == 1) {
                            $estadoComprobante = 'Anulado';
                        }
                        $que_comprobante_se_sumara = 'BOLETA';
                    }
                    //start solo sumando el comprobante que esta activo
                    if ($que_comprobante_se_sumara == 'BOLETA' and $listar_comprobantes[$j]['esAnulado'] == 0) {
                        if ($data[$i]['idmoneda'] == '1') {
                            $sum_biBoleta_soles = $sum_biBoleta_soles + $biBoleta_soles;
                            $sum_totalcomprobante_soles = $sum_totalcomprobante_soles + $totalcomprobante_soles;
                        }
                        if ($data[$i]['idmoneda'] == '2') {
                            $sum_biBoleta_dolares = $sum_biBoleta_dolares + $biBoleta_dolares;
                            $sum_totalcomprobante_dolares = $sum_totalcomprobante_dolares + $totalcomprobante_dolares;
                        }
                    }
                    if ($que_comprobante_se_sumara == 'FACTURA' and $listar_comprobantes[$j]['esAnulado'] == 0) {
                        if ($data[$i]['idmoneda'] == '1') {
                            $sum_subtotalFactura_soles = $sum_subtotalFactura_soles + $subtotalFactura_soles;
                            $sum_igvFactura_soles = $sum_igvFactura_soles + $igvFactura_soles;
                            $sum_totalcomprobante_soles = $sum_totalcomprobante_soles + $totalcomprobante_soles;
                            $sum_percepcion_soles = $sum_percepcion_soles + $percepcion_soles;
                        }
                        if ($data[$i]['idmoneda'] == '2') {
                            $sum_subtotalFactura_dolares = $sum_subtotalFactura_dolares + $subtotalFactura_dolares;
                            $sum_igvFactura_dolares = $sum_igvFactura_dolares + $igvFactura_dolares;
                            $sum_totalcomprobante_dolares = $sum_totalcomprobante_dolares + $totalcomprobante_dolares;
                            $sum_percepcion_dolares = $sum_percepcion_dolares + $percepcion_dolares;
                        }
                    }
                    //
                    $idordenventaTemp = $data[$i]['idordenventa'];

                    //            ***************************************************************************************************
                    // START  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
                    if ($data[$i]['ruc'] == '') {
                        $ruc_dni = $data[$i]['dni'];
                    } else {
                        $ruc_dni = $data[$i]['ruc'];
                    }

                    if ($data[$i]['idmoneda'] == '1') {
                        $moneda = 'S/';
                    }
                    if ($data[$i]['idmoneda'] == '2') {
                        $moneda = 'US $';
                    }

                    if ($subtotalFactura_soles > 0) {
                        $percepcion_soles = $moneda . ' ' . $percepcion_soles;
                        $porcentaje_percepcion_soles = $data[$i]['percepcion'] * 100;
                        $porcentaje_percepcion_soles = $porcentaje_percepcion_soles . '%';
                    } else {
                        $percepcion_soles = '';
                        $porcentaje_percepcion_soles = '';
                    }

                    if ($subtotalFactura_dolares > 0) {
                        $percepcion_dolares = $moneda . ' ' . $percepcion_dolares;
                        $porcentaje_percepcion_dolares = $data[$i]['percepcion'] * 100;
                        $porcentaje_percepcion_dolares = $porcentaje_percepcion_dolares . '%';
                    } else {
                        $percepcion_dolares = '';
                        $porcentaje_percepcion_dolares = '';
                    }

                    $nro_aumentador = $nro_aumentador + 1;

                    if ($data[$i]['idmoneda'] == '1') {
                        //***********************
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($contador), $nro_aumentador);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . ($contador), $data[$i]['fordenventa']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . ($contador), $data[$i]['fechadespacho']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . ($contador), $data[$i]['codigov']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . ($contador), $temporal_condicion_venta);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . ($contador), $ruc_dni);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . ($contador), $data[$i]['razonsocial']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . ($contador), $comprobanteFactura);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . ($contador), $comprobanteBoleta);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), $serieGRemision . '-' . $numGRemision);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), $moneda . ' ' . number_format($subtotalFactura_soles, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . ($contador), $moneda . ' ' . number_format($igvFactura_soles, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . ($contador), $moneda . ' ' . number_format($biBoleta_soles, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), $moneda . ' ' . number_format($importeGuia_soles, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . ($contador), $moneda . ' ' . number_format($totalcomprobante_soles, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . ($contador), $percepcion_soles);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . ($contador), $porcentaje_percepcion_soles);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . ($contador), $estado_ov);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . ($contador), $estadoComprobante);
                        $contador ++;
                        //***********************
                    }
                    if ($data[$i]['idmoneda'] == '2') {
                        //***********************
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . ($contador), $nro_aumentador);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . ($contador), $data[$i]['fordenventa']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . ($contador), $data[$i]['fechadespacho']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . ($contador), $data[$i]['codigov']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . ($contador), $temporal_condicion_venta);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . ($contador), $ruc_dni);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . ($contador), $data[$i]['razonsocial']);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . ($contador), $comprobanteFactura);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . ($contador), $comprobanteBoleta);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), $serieGRemision . '-' . $numGRemision);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), $moneda . ' ' . number_format($subtotalFactura_dolares, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . ($contador), $moneda . ' ' . number_format($igvFactura_dolares, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . ($contador), $moneda . ' ' . number_format($biBoleta_dolares, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), $moneda . ' ' . number_format($importeGuia_dolares, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . ($contador), $moneda . ' ' . number_format($totalcomprobante_dolares, 2));
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . ($contador), $percepcion_dolares);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . ($contador), $porcentaje_percepcion_dolares);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . ($contador), $estado_ov);
                        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . ($contador), $estadoComprobante);
                        $contador ++;
                        //***********************
                    }
                    // END  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
                    //            ****************************************************************************************************
                }
            }
        }

        $totalNoFacturadoSoles = $sum_importeGuia_soles - $sum_totalcomprobante_soles;
        $totalNoFacturadoDolares = $sum_importeGuia_dolares - $sum_totalcomprobante_dolares;


        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":S" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':I' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q' . $contador . ':S' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), 'TOTAL SOLES');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), 'S/ ' . number_format($sum_subtotalFactura_soles, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . ($contador), 'S/ ' . number_format($sum_igvFactura_soles, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . ($contador), 'S/ ' . number_format($sum_biBoleta_soles, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), 'S/ ' . number_format($sum_importeGuia_soles, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . ($contador), 'S/ ' . number_format($sum_totalcomprobante_soles, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . ($contador), 'S/ ' . number_format($sum_percepcion_soles, 2));
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "A" . ($contador) . ":S" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A" . ($contador) . ":S" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A' . $contador . ':I' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q' . $contador . ':S' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), 'TOTAL DOLARES');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), 'US $ ' . number_format($sum_subtotalFactura_dolares, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . ($contador), 'US $ ' . number_format($sum_igvFactura_dolares, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . ($contador), 'US $ ' . number_format($sum_biBoleta_dolares, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), 'US $ ' . number_format($sum_importeGuia_dolares, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . ($contador), 'US $ ' . number_format($sum_totalcomprobante_dolares, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . ($contador), 'US $ ' . number_format($sum_percepcion_dolares, 2));
        $contador++;
        $contador++;
        $contador++;
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "J" . ($contador) . ":P" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("J" . ($contador) . ":P" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("J" . ($contador) . ":P" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('K' . $contador . ':M' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N' . $contador . ':P' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), '');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), 'TOTAL EN VENTAS FACTURADO');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), 'TOTAL EN VENTAS NO FACTURADO');
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "J" . ($contador) . ":P" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("J" . ($contador) . ":P" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("J" . ($contador) . ":P" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('K' . $contador . ':M' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N' . $contador . ':P' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), 'TOTAL SOLES');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), 'S/ ' . number_format($sum_totalcomprobante_soles, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), 'S/ ' . number_format($totalNoFacturadoSoles, 2));
        $contador++;
        $objPHPExcel->getActiveSheet()->setSharedStyle($sharedStyle1, "J" . ($contador) . ":P" . ($contador));
        $objPHPExcel->getActiveSheet()->getStyle("J" . ($contador) . ":P" . ($contador))->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("J" . ($contador) . ":P" . ($contador))->getFill()->setRotation(1);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('K' . $contador . ':M' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N' . $contador . ':P' . $contador);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . ($contador), 'TOTAL DOLARES');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . ($contador), 'US $ ' . number_format($sum_totalcomprobante_dolares, 2));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . ($contador), 'US $ ' . number_format($totalNoFacturadoDolares, 2));

        $objPHPExcel->getActiveSheet()->setTitle('VENTAS FACTURADO Y NO FACTURADO');
        $objPHPExcel->setActiveSheetIndex(0);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save($filename);

        header('Content-Description: File Transfer');
        header('Content-type: application/force-download');
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Content-Transfer-Encoding: binary');
        header("Content-type: application/octet-stream");
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filename));
        ob_clean();
        flush();
        readfile($filename);
        unlink($filename);
    }

}