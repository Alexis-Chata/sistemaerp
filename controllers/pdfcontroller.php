<?php

Class PDFController extends applicationgeneral {

    function __construct() {
        parent::__construct();
        ob_clean();
    }

    public function obtenerFinMes($mes, $anio) {
        if ($mes == 2) {
            if (($anio%4) == 0) {
                return 29;
            }
            else {
                return 28;
            }
        }
        else if ($mes == 1 || $mes == 3 || $mes == 5 || $mes == 7 || $mes == 8 || $mes == 10 || $mes == 12) {
            return 31;
        }
        else {
            return 30;
        }
    }

    function index() {
        $this->pdf_reportes = New pdf_reportes("L", "mm", "A4");
        $this->pdf_reportes->_titulo = "Reporte de Vendedores";
        $this->pdf_reportes->AliasNbPages();
        $this->pdf_reportes->AddPage();
        $this->pdf_reportes->SetFont('Arial', 'B', 10); //
        $this->pdf_reportes->Cell(0, 10, 'Hola, Mundo');
        $this->pdf_reportes->Output(); //'ReporteVendedores.pdf','D'
    }

    function listaLinea() {
        $this->pdf_reportes = New pdf_reportes("L", "mm", "A4");
        $linea = $this->AutoLoadModel('linea');
        $data = $linea->listadoLineas();
        $titulos = array('id', 'nombre');
        $columnas = array('idlinea', 'nomlin');
        $ancho = array(40, 70);
        $this->pdf_reportes->_titulo = "Reporte de Linea";
        $this->pdf_reportes->AddPage();
        $this->pdf_reportes->SetFont('Arial', 'B', 10); //
        $this->pdf_reportes->ln();
        $this->pdf_reportes->PintaTablaN($titulos, $data, $columnas, $ancho);
        $this->pdf_reportes->AliasNbPages();
        $this->pdf_reportes->Output();
    }

    function inventario() {
        $idAlmacen = $_REQUEST['idAlmacen'];
        $idLinea = $_REQUEST['idLinea'];
        $idSubLinea = $_REQUEST['idSubLinea'];
        $idProducto = $_REQUEST['idProducto'];
        $producto = new Producto();
        $ordenCompra = new Ordencompra();
        $ordenVenta = new OrdenVenta();
        $dataProducto = $producto->inventario($idAlmacen, $idLinea, $idSubLinea, $idProducto);
        $dataOrdenCompra = $ordenCompra->inventario($idAlmacen, $idLinea, $idSubLinea, $idProducto);
        $dataOrdenVenta = $ordenVenta->inventario($idAlmacen, $idLinea, $idSubLinea, $idProducto);
        $rutaImagen = $this->rutaImagenesProducto();
        $unidadMedida = $this->unidadMedida();
        $empaque = $this->empaque();
        $data = array();
        $indice = 0;
        if (empty($dataProducto)) {
            $data[$indice]['codigo'] = "";
            $data[$indice]['nompro'] = "";
            $data[$indice]['preciolista'] = "";
            $data[$indice]['stockactual'] = "";
            $data[$indice]['stockporllegar'] = "";
            $data[$indice]['stockpordespachar'] = "";
            $data[$indice]['unidadmedida'] = "";
            $data[$indice]['empaque'] = "";
        } else {
            foreach ($dataProducto as $dato) {
                if (count($dataOrdenCompra)) {
                    foreach ($dataOrdenCompra as $doc) {
                        if ($doc['idproducto'] == $dato['idproducto']) {
                            $dato['stockporllegar'] = $doc['cantidadsolicitadaoc'];
                            break;
                        }
                    }
                }
                if (count($dataOrdenVenta)) {
                    foreach ($dataOrdenVenta as $dop) {
                        if ($dop['idproducto'] == $dato['idproducto']) {
                            $dato['stockpordespachar'] = $dop['cantaprobada'];
                            break;
                        }
                    }
                }
                //	echo '<td><img src="'.$rutaImagen.$dato['codigo'].'/'.$dato['imagen'].'" width="50" height="50"></td>';
                $data[$indice]['codigo'] = $dato['codigopa'];
                $data[$indice]['nompro'] = $dato['nompro'];
                $data[$indice]['preciolista'] = $dato['preciolista'];
                $data[$indice]['stockactual'] = $dato['stockactual'];
                $data[$indice]['stockporllegar'] = $dato['stockporllegar'];
                $data[$indice]['stockpordespachar'] = $dato['stockpordespachar'];
                $data[$indice]['unidadmedida'] = $dato['unidadmedida'];
                $data[$indice]['empaque'] = $empaque[($dato['empaque'])];
                $indice++;
            }
        }
        $cantidadData = count($data);
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array('Codigo', 'nombre', 'Pre Lista', 'S/Atc', 'S/Llegar', 'S/Desp', 'U. M.', 'Empaque');
        $columnas = array('codigo', 'nompro', 'preciolista', 'stockactual', 'stockporllegar', 'stockpordespachar', 'unidadmedida', 'empaque');
        $ancho = array(25, 132, 25, 20, 20, 20, 15, 20);
        $orientacion = array('', '', 'R', 'R', 'R', 'R', 'C', 'C');
        $pdf->_titulo = "Reporte de Inventario";
        $pdf->AddPage();
        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', '8');
        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho

        $pdf->SetWidths($ancho);
        $valor = "Reporte de Ventas";
        $pdf->titlees($valor);
        //un arreglo con alineacion de cada celda

        $pdf->SetAligns($orientacion);

        for ($i = 0; $i < count($titulos); $i++) {
            $pdf->Cell($ancho[$i], 7, $titulos[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        for ($i = 0; $i < $cantidadData; $i++) {

            $fila = array($data[$i]['codigo'], $data[$i]['nompro'], utf8_decode($data[$i]['preciolista']), $data[$i]['stockactual'], $data[$i]['stockporllegar'], utf8_decode($data[$i]['stockpordespachar']), $data[$i]['unidadmedida'], $data[$i]['empaque']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function cantidadventasxmes() {
        $idLinea = $_REQUEST['idLinea'];
        $idSubLinea = $_REQUEST['idSubLinea'];
        $idProducto = $_REQUEST['idProducto'];
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $ancho = array(18, 72, 40, 45, 10, 20, 18, 18, 14, 25);
        $orientacion = array('C', '', '', '', 'R', 'R', 'C', 'R', 'R');
        $pdf->_titulo = "REPORTE - CANTIDAD DE VENTAS POR MES";
        $pdf->AddPage();

        $pdf->fill(true);
        $pdf->SetWidths($ancho);
        $pdf->SetAligns($orientacion);

        $mes = array(1 => 'ENERO', 2 => 'FEBRERO', 3 => 'MARZO', 4 => 'ABRIL', 5 => 'MAYO', 6 => 'JUNIO', 7 => 'JULIO', 8 => 'AGOSTO', 9 => 'SEPTIEMBRE', 10 => 'OCTUBRE', 11 => 'NOVIEMBRE', 12 => 'DICIEMBRE');
        $repote = new Reporte();
        $ordencompra = new Ordencompra();
        $ordenventa = new OrdenVenta();
        $productos = $repote->reporteIdproductos($idLinea, $idSubLinea, $idProducto);
        for ($p = 0; $p < count($productos); $p++) {
            $oc = $ordencompra->reporteOrdenCompraProducto($productos[$p]['idproducto']);
            if (count($oc) == 1) {
                $ov = $ordenventa->ultimaOrdenVentaxProducto($productos[$p]['idproducto']);
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetTextColor(12, 78, 139);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->Cell(277, 7, $oc[0]['nompro'], 1, 0, 'C', true);
                $pdf->Ln();
                $pdf->Cell(20, 7, 'CODIGO', 1, 0, 'C', true);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $pdf->Cell(40, 7, $oc[0]['codigopa'] , 1, 0, 'C', true);
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetTextColor(12, 78, 139);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->Cell(20, 7, 'LINEA', 1, 0, 'C', true);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $pdf->Cell(52, 7, $oc[0]['linea'], 1, 0, 'C', true);
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetTextColor(12, 78, 139);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->Cell(25, 7, 'SUB-LINEA', 1, 0, 'C', true);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $pdf->Cell(80, 7, $oc[0]['sublinea'], 1, 0, 'C', true);
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetTextColor(12, 78, 139);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->Cell(20, 7, 'U/M', 1, 0, 'C', true);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $pdf->Cell(20, 7, $oc[0]['nommedida'], 1, 0, 'C', true);
                $pdf->Ln();
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetTextColor(12, 78, 139);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->Cell(139, 7, 'INGRESO DEL PRODUCTO', 1, 0, 'C', true);
                $pdf->Cell(138, 7, 'ULTIMA SALIDA DEL PRODUCTO', 1, 0, 'C', true);
                $pdf->Ln();
                $pdf->Cell(30, 7, 'ORDEN DE COMPRA', 1, 0, 'C', true);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $pdf->Cell(30, 7, $oc[0]['codigooc'], 1, 0, 'C', true);
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetTextColor(12, 78, 139);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->Cell(35, 7, 'FECHA DE LLEGADA', 1, 0, 'C', true);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $pdf->Cell(44, 7, $oc[0]['fecha'], 1, 0, 'C', true);
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetTextColor(12, 78, 139);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->Cell(30, 7, 'ORDEN DE VENTA', 1, 0, 'C', true);
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                if (count($ov) == 1) {
                    $pdf->Cell(35, 7, $ov[0]['codigov'], 1, 0, 'C', true);
                    $pdf->SetFillColor(202, 232, 234);
                    $pdf->SetTextColor(12, 78, 139);
                    $pdf->SetDrawColor(12, 78, 139);
                    $pdf->SetLineWidth(.3);
                    $pdf->SetFont('Helvetica', 'B', 8);
                    $pdf->Cell(30, 7, 'FECHA DE SALIDA', 1, 0, 'C', true);
                    $pdf->SetFillColor(224, 235, 255);
                    $pdf->SetTextColor(0);
                    $pdf->SetFont('');
                    $pdf->Cell(43, 7, $ov[0]['fordenventa'], 1, 0, 'C', true);
                    $pdf->Ln();
                    $pdf->Ln();
                    $pdf->SetFillColor(202, 232, 234);
                    $pdf->SetTextColor(12, 78, 139);
                    $pdf->SetDrawColor(12, 78, 139);
                    $pdf->SetLineWidth(.3);
                    $pdf->SetFont('Helvetica', 'B', 8);
                    $pdf->Cell(277, 7, "CANTIDAD DE VENTAS POR MES", 1, 0, 'C', true);
                    $pdf->Ln();
                    $pdf->SetFillColor(224, 235, 255);
                    $pdf->SetTextColor(0);
                    $pdf->SetFont('');
                    $pdf->Cell(139, 7, 'MES', 1, 0, 'C', true);
                    $pdf->Cell(138, 7, 'CANTIDAD', 1, 0, 'C', true);
                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->Ln();
                    $canOv = $ordenventa->ListarCantidadVendida($productos[$p]['idproducto'], $oc[0]['fecha'], $ov[0]['fordenventa']);
                    $total = 0;
                        $auxMes = explode("-", $oc[0]['fecha']);
                        $mesActual = $auxMes[1];
                        $cantidad = 0;
                        for ($i = 0; $i < count($canOv); $i++) {
                            $auxMes = explode("-", $canOv[$i]['fordenventa']);
                            if ($mesActual == $auxMes[1]) {
                                $cantidad = $cantidad + $canOv[$i]['cantidad'];
                            } else {
                                $total = $total + $cantidad;
                                $pdf->Cell(139, 7, $mes[$mesActual * 1], 1, 0, 'C', true);
                                $pdf->Cell(138, 7, $cantidad, 1, 0, 'C', true);
                                $pdf->Ln();
                                $cantidad = 0;
                                if ($mesActual != 12)
                                    $mesActual++;
                                else
                                    $mesActual = 1;
                                $i = $i - 1;
                            }
                        }
                        if ($i > 1) {
                            $total = $total + $cantidad;
                            $pdf->Cell(139, 7, $mes[$mesActual * 1], 1, 0, 'C', true);
                            $pdf->Cell(138, 7, $cantidad, 1, 0, 'C', true);
                            $pdf->Ln();
                        }
                        $pdf->SetFillColor(224, 235, 255);
                        $pdf->SetTextColor(0);
                        $pdf->SetFont('');
                        $pdf->Cell(139, 7, 'CANTIDAD TOTAL', 1, 0, 'C', true);
                        $pdf->Cell(138, 7, $total, 1, 0, 'C', true);
                } else {
                    $pdf->Cell(35, 7, 'INDEFINIDO', 1, 0, 'C', true);
                    $pdf->SetFillColor(202, 232, 234);
                    $pdf->SetTextColor(12, 78, 139);
                    $pdf->SetDrawColor(12, 78, 139);
                    $pdf->SetLineWidth(.3);
                    $pdf->SetFont('Helvetica', 'B', 8);
                    $pdf->Cell(30, 7, 'FECHA DE SALIDA', 1, 0, 'C', true);
                    $pdf->SetFillColor(224, 235, 255);
                    $pdf->SetTextColor(0);
                    $pdf->SetFont('');
                    $pdf->Cell(43, 7, 'INDEFINIDO', 1, 0, 'C', true);

                    $pdf->Ln();
                    $pdf->Ln();
                    $pdf->SetFillColor(202, 232, 234);
                    $pdf->SetTextColor(12, 78, 139);
                    $pdf->SetDrawColor(12, 78, 139);
                    $pdf->SetLineWidth(.3);
                    $pdf->SetFont('Helvetica', 'B', 8);
                    $pdf->Cell(277, 7, "CANTIDAD DE VENTAS POR MES", 1, 0, 'C', true);
                    $pdf->Ln();
                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->SetTextColor(0);
                    $pdf->SetFont('');
                    $pdf->Cell(277, 7, 'ESTE PRODUCTO AUN NO HA SIDO VENDIDO', 1, 0, 'C', true);
                }
            } else {
                if (!empty($_REQUEST['idProducto'])) {
                    $pdf->SetFillColor(255, 255, 255);
                    $pdf->SetTextColor(12, 78, 139);
                    $pdf->SetDrawColor(12, 78, 139);
                    $pdf->Cell(277, 7, "NO SE DETECTO ORDEN DE COMPRA", 1, 0, 'C', true);
                }
            }
            $pdf->Ln();
            $pdf->Ln();
            $pdf->Ln();
        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function durezaproductoseleccionado() {
        $selecccionados = $_REQUEST['pseleccionPDF'];

        $fecha = date('Y-m-d');

        $repote = new Reporte();
        $producto=new Producto();


        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $cabecera = array('ALMACEN', 'FECHA DE CONSULTA');
        $titulos = array('CODIGO', 'PRODUCTO', 'LINEA', 'SUB-LINEA', 'U.M', 'F. LLEGADA', 'S. INICIAL', 'S. DISPO.', 'PVM', 'DUREZA (meses)');
        $ancho = array(18, 72, 40, 45, 10, 20, 18, 18, 13, 25);
        $ancho0 = array(120, 50);
        $orientacion = array('C', '', '', '', 'R', 'R', 'C', 'R', 'R');
        $pdf->_titulo = "REPORTE - DUREZA DE PRODUCTOS";
        $pdf->AddPage();

        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill(true);

        $pdf->SetWidths($ancho);
        $pdf->SetAligns($orientacion);

        for ($i = 0; $i < 2; $i++) {
            $pdf->Cell($ancho0[$i], 7, $cabecera[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');

        $pdf->Cell($ancho0[0], 7, 'CORPORACION POWER ACOUSTIK S.A.C.', 1, 0, 'C', true);
        $pdf->Cell($ancho0[1], 7, $fecha, 1, 0, 'C', true);

        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->fill(true);

        for ($i = 0; $i < count($titulos); $i++) {
            $pdf->Cell($ancho[$i], 7, $titulos[$i], 1, 0, 'C', true);
        }

        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $tam = count($selecccionados);
        for ($i = 0; $i < $tam; $i++) {
            $data2=$producto->durezaProducto($selecccionados[$i], 1);
            if(!empty($data2[0]['codigopa'])) {
                $datetime1 = new DateTime($fecha);
                $datetime2 = new DateTime($data2[0]['fecha']);
                $interval = $datetime1->diff($datetime2);

                if($data2[0]['stockactual'] > 0) {
                    $PVM = ($data2[0]['stockinicial'] - $data2[0]['stockactual'])/(($interval->y * 12 ) + ($interval->m * 30) + $interval->d);
                    $PVM = $PVM*30;
                    if($PVM == 0) $PVM = 1;
                    $dureza = $data2[0]['stockactual'] / $PVM;
                } else {
                    $PVM = 0;
                    $dureza = 0;
                }
                $fila = array(html_entity_decode($data2[0]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[0]['nompro'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[0]['linea'], ENT_QUOTES, 'UTF-8'), (html_entity_decode(utf8_decode($data2[0]['sublinea']), ENT_QUOTES, 'UTF-8')), $data2[0]['nommedida'], $data2[0]['fecha'], $data2[0]['stockinicial'], $data2[0]['stockactual'], number_format($PVM, 2), number_format($dureza, 2));
                $pdf->Row($fila);
                $relleno = !$relleno;
                $pdf->fill($relleno);
            } else {
                $data2=$producto->durezaProducto($selecccionados[$i], 2);
                if(!empty($data2[0]['codigopa']) && !empty($data2[0]['cif'])) {
                    $pdf->SetFillColor(216, 232, 198);
                    $fila = array(html_entity_decode($data2[0]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[0]['nompro'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[0]['linea'], ENT_QUOTES, 'UTF-8'), (html_entity_decode(utf8_decode($data2[0]['sublinea']), ENT_QUOTES, 'UTF-8')), $data2[0]['nommedida'], '---', '---', $data2[0]['stockactual'], '---', 'CIF: '.$data2[0]['cif']);
                    $pdf->Row($fila);
                    $relleno = !$relleno;
                    $pdf->fill($relleno);
                    $pdf->SetFillColor(255, 255, 255);
                }
            }
        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function durezaproducto() {
        $idLinea = $_REQUEST['idLinea'];
        $idSubLinea = $_REQUEST['idSubLinea'];
        $idProducto = $_REQUEST['idProducto'];
        $fecha = $_REQUEST['fecha'];
        $stockactual = $_REQUEST['stockactual'];
        if(empty($fecha)) $fecha = date('Y-m-d');

        $repote = new Reporte();
        $producto=new Producto();
        $data = $repote->reporteProductoDureza($idLinea, $idSubLinea, $idProducto, $stockactual);

        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $cabecera = array('ALMACEN', 'FECHA DE CONSULTA', 'PRODUCTOS');
        $titulos = array('CODIGO', 'PRODUCTO', 'LINEA', 'SUB-LINEA', 'U.M', 'F. LLEGADA', 'S. INICIAL', 'S. DISPO.', 'PVM', 'DUREZA (meses)');
        $ancho = array(18, 72, 40, 45, 10, 20, 18, 18, 13, 25);
        $ancho0 = array(120, 50, 50);
        $orientacion = array('C', '', '', '', 'R', 'R', 'C', 'R', 'R');
        $pdf->_titulo = "REPORTE - DUREZA DE PRODUCTOS";
        $pdf->AddPage();

        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill(true);

        $pdf->SetWidths($ancho);
        $pdf->SetAligns($orientacion);

        for ($i = 0; $i < 3; $i++) {
            $pdf->Cell($ancho0[$i], 7, $cabecera[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $textStock = "TODOS";
        if ($stockactual == 2) $textStock = "DISPONIBLE";
        else if ($stockactual == 3) $textStock = "AGOTADOS";
        $pdf->Cell($ancho0[0], 7, 'CORPORACION POWER ACOUSTIK S.A.C.', 1, 0, 'C', true);
        $pdf->Cell($ancho0[1], 7, $fecha, 1, 0, 'C', true);
        $pdf->Cell($ancho0[2], 7, $textStock, 1, 0, 'C', true);

        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->fill(true);

        for ($i = 0; $i < count($titulos); $i++) {
            $pdf->Cell($ancho[$i], 7, $titulos[$i], 1, 0, 'C', true);
        }

        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');

        for ($i = 0; $i < count($data); $i++) {
            $data2=$producto->durezaProducto($data[$i]['idproducto'], 1);
            if(!empty($data2[0]['codigopa'])) {
                $datetime1 = new DateTime($fecha);
                $datetime2 = new DateTime($data2[0]['fecha']);
                $interval = $datetime1->diff($datetime2);

                if($data2[0]['stockactual'] > 0) {
                    $PVM = ($data2[0]['stockinicial'] - $data2[0]['stockactual'])/(($interval->y * 12 ) + ($interval->m * 30) + $interval->d);
                    $PVM = $PVM*30;
                    if($PVM == 0) $PVM = 1;
                    $dureza = $data2[0]['stockactual'] / $PVM;
                } else {
                    $PVM = 0;
                    $dureza = 0;
                }
                $fila = array(html_entity_decode($data2[0]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[0]['nompro'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[0]['linea'], ENT_QUOTES, 'UTF-8'), (html_entity_decode(utf8_decode($data2[0]['sublinea']), ENT_QUOTES, 'UTF-8')), $data2[0]['nommedida'], $data2[0]['fecha'], $data2[0]['stockinicial'], $data2[0]['stockactual'], number_format($PVM, 2), number_format($dureza, 2));
                $pdf->Row($fila);
                $relleno = !$relleno;
                $pdf->fill($relleno);
            } else {
                $data2=$producto->durezaProducto($data[$i]['idproducto'], 2);
                if(!empty($data2[0]['codigopa']) && !empty($data2[0]['cif'])) {
                    $pdf->SetFillColor(216, 232, 198);
                    $fila = array(html_entity_decode($data2[0]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[0]['nompro'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[0]['linea'], ENT_QUOTES, 'UTF-8'), (html_entity_decode(utf8_decode($data2[0]['sublinea']), ENT_QUOTES, 'UTF-8')), $data2[0]['nommedida'], '---', '---', $data2[0]['stockactual'], '---', 'CIF: '.$data2[0]['cif']);
                    $pdf->Row($fila);
                    $relleno = !$relleno;
                    $pdf->fill($relleno);
                    $pdf->SetFillColor(255, 255, 255);
                }
            }

        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function StockProducto() {

        $idAlmacen = $_REQUEST['idAlmacen'];
        $idLinea = $_REQUEST['idLinea'];
        $idSubLinea = $_REQUEST['idSubLinea'];
        $idProducto = $_REQUEST['idProducto'];
        $repote = new Reporte();
        $data = $repote->reporteStockProducto($idAlmacen, $idLinea, $idSubLinea, $idProducto);
        // print_r($data[0]);
        // exit;
        $unidadMedida = $this->unidadMedida();
        $totalStock = 0;
        $data2 = array();
        $i = 0;
        for ($i = 0; $i < count($data); $i++) {
            $data2[$i]['codigo'] = $data[$i]['codigopa'];
            $data2[$i]['nompro'] = $data[$i]['nompro'];
            $data2[$i]['nomalm'] = $data[$i]['nomalm'];
            $data2[$i]['nomlin'] = $data[$i]['nomlin'];
            $data2[$i]['preciolista'] = $data[$i]['preciolista'];
            $data2[$i]['preciolistadolares'] = $data[$i]['preciolistadolares'];
            $data2[$i]['unidadmedida'] = $data[$i]['unidadmedida'];
            $data2[$i]['stockactual'] = $data[$i]['stockactual'];
            $data2[$i]['stockdisponible'] = ($data[$i]['stockdisponible']);
            $totalStock+=$data[$i]['stockactual'];
        }

        $cantidadData = count($data2);

        /**/
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array('Codigo', 'Descripcion', 'Almacen', 'Linea', 'P. L.(S/.)', 'P. L.(US $)', 'U.M', 'S/Act', 'S/Desp');
        $columnas = array('codigo', 'nompro', 'nomalm', 'nomlin', 'preciolista', 'preciolistadolares', 'unidadmedida', 'stockactual', 'stockdisponible');
        $ancho = array(20, 75, 55, 50, 16, 16, 15, 13, 13);
        $orientacion = array('C', '', '', '', 'R', 'R', 'C', 'R', 'R');
        $pdf->_titulo = "Reporte de Stock Producto";


        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho

        $pdf->SetWidths($ancho);

        //un arreglo con alineacion de cada celda

        $pdf->SetAligns($orientacion);

        for ($i = 0; $i < count($titulos); $i++) {
            $pdf->Cell($ancho[$i], 7, $titulos[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        for ($i = 0; $i < $cantidadData; $i++) {

            $fila = array(html_entity_decode($data2[$i]['codigo'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[$i]['nompro'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[$i]['nomalm'], ENT_QUOTES, 'UTF-8'), (html_entity_decode(utf8_decode($data2[$i]['nomlin']), ENT_QUOTES, 'UTF-8')), $data2[$i]['preciolista'], $data2[$i]['preciolistadolares'], $data2[$i]['unidadmedida'], $data2[$i]['stockactual'], $data2[$i]['stockdisponible']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function StockProductoRep() {

        $idAlmacen = $_REQUEST['idAlmacen'];
        $idLinea = $_REQUEST['idLinea'];
        $idSubLinea = $_REQUEST['idSubLinea'];
        $idProducto = $_REQUEST['idProducto'];
        $repote = new Reporte();
        $data = $repote->reporteStockProductoRep($idAlmacen, $idLinea, $idSubLinea, $idProducto);
        // print_r($data[0]);
        // exit;
        $unidadMedida = $this->unidadMedida();
        $totalStock = 0;
        $data2 = array();
        $i = 0;
        for ($i = 0; $i < count($data); $i++) {
            $data2[$i]['codigo'] = $data[$i]['codigopa'];
            $data2[$i]['nompro'] = $data[$i]['nompro'];
            $data2[$i]['nomalm'] = $data[$i]['nomalm'];
            $data2[$i]['nomlin'] = $data[$i]['nomlin'];
            $data2[$i]['preciolista'] = $data[$i]['preciolista'];
            $data2[$i]['preciolistadolares'] = $data[$i]['preciolistadolares'];
            $data2[$i]['unidadmedida'] = $data[$i]['unidadmedida'];
            $data2[$i]['stockactual'] = $data[$i]['stockactual'];
            $data2[$i]['stockdisponible'] = ($data[$i]['stockdisponible']);
            $totalStock+=$data[$i]['stockactual'];
        }

        $cantidadData = count($data2);

        /**/
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array('Codigo', 'Descripcion', 'Almacen', 'Linea', 'P. L.(S/.)', 'P. L.(US $)', 'U.M', 'S/Act', 'S/Desp');
        $columnas = array('codigo', 'nompro', 'nomalm', 'nomlin', 'preciolista', 'preciolistadolares', 'unidadmedida', 'stockactual', 'stockdisponible');
        $ancho = array(20, 75, 55, 50, 16, 16, 15, 13, 13);
        $orientacion = array('C', '', '', '', 'R', 'R', 'C', 'R', 'R');
        $pdf->_titulo = "Reporte de Stock Producto";


        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho

        $pdf->SetWidths($ancho);

        //un arreglo con alineacion de cada celda

        $pdf->SetAligns($orientacion);

        for ($i = 0; $i < count($titulos); $i++) {
            $pdf->Cell($ancho[$i], 7, $titulos[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        for ($i = 0; $i < $cantidadData; $i++) {

            $fila = array(html_entity_decode($data2[$i]['codigo'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[$i]['nompro'], ENT_QUOTES, 'UTF-8'), html_entity_decode($data2[$i]['nomalm'], ENT_QUOTES, 'UTF-8'), (html_entity_decode(utf8_decode($data2[$i]['nomlin']), ENT_QUOTES, 'UTF-8')), $data2[$i]['preciolista'], $data2[$i]['preciolistadolares'], $data2[$i]['unidadmedida'], $data2[$i]['stockactual'], $data2[$i]['stockdisponible']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function ventas() {

        $idLinea = $_REQUEST['linea'];
        $idVendedor = $_REQUEST['vendedor'];
        $fInicial = $_REQUEST['fechaInicial'];
        $fFinal = $_REQUEST['fechaFinal'];
        $ordenVenta = new OrdenVenta();
        $data = $ordenVenta->listadoReporteVentas($idLinea, $idVendedor, $fInicial, $fFinal);
        $cantidadData = count($data);

        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array('Fecha', 'Orden de Venta', 'Cliente', 'Importe', 'Saldo', 'Condicion', 'Vendedor', 'Vencimiento');
        $columnas = array('fordenventa', 'codigov', 'razonsocial', 'importeordencobro', 'importedoc', 'condicion', 'vendedor', 'fvencimiento');
        $ancho = array(20, 28, 75, 20, 18, 21, 70, 27);
        $orientacion = array('C', 'C', '', 'R', 'R', 'C', '', 'C');
        $pdf->_titulo = "Reporte de Ventas";


        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho

        $pdf->SetWidths($ancho);

        //un arreglo con alineacion de cada celda

        $pdf->SetAligns($orientacion);

        for ($i = 0; $i < count($titulos); $i++) {
            $pdf->Cell($ancho[$i], 7, $titulos[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        for ($i = 0; $i < $cantidadData; $i++) {

            $fila = array($data[$i]['fordenventa'], $data[$i]['codigov'], utf8_decode($data[$i]['razonsocial']), $data[$i]['importeordencobro'], $data[$i]['importedoc'], $data[$i]['condicion'], utf8_decode($data[$i]['vendedor']), $data[$i]['fvencimiento']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function ListaPrecios() {
        $idAlmacen = $_REQUEST['idAlmacen'];
        $idLinea = $_REQUEST['idLinea'];
        $idSubLinea = $_REQUEST['idSubLinea'];
        $idProducto = $_REQUEST['idProducto'];
        $idmoneda = $_REQUEST['idmoneda'];


        $reporte = new Reporte();
        $linea = $this->AutoLoadModel('linea');
        $tipoCambio = $this->AutoLoadModel('tipocambio');
        $data = $reporte->reporteListaPrecio($idAlmacen, $idLinea, $idSubLinea, $idProducto);

        $rutaImagen = $this->rutaImagenesProducto();
        $unidadMedida = $this->unidadMedida();

        $data2 = array();
        for ($i = 0; $i < count($data); $i++) {
            $data2[$i]['codigo'] = $data[$i]['codigopa'];
            $data2[$i]['nompro'] = $data[$i]['nompro'];


            if ($idmoneda == 1) {
                $data2[$i]['preciolista'] = $data[$i]['preciolista'];
                $simbmadel = "S/. ";
                $simbmatras = "";
            }
            if ($idmoneda == 2) {
                $data2[$i]['preciolista'] = $data[$i]['preciolistadolares'];
                $simbmadel = "";
                $simbmatras = "US $ ";
            }

            $data2[$i]['stockactual'] = $data[$i]['stockactual'];
            $data2[$i]['unidadmedida'] = $data[$i]['nombremedida'];
            $data2[$i]['empaque'] = empty($data[$i]['idempaque']) ? 'Sin/Emp.' : $data[$i]['codempaque'];
            $data2[$i]['idpadre'] = $data[$i]['idpadre'];
            $data2[$i]['idlinea'] = $data[$i]['idlinea'];
            $data2[$i]['nomlin'] = $data[$i]['nomlin'];
        }

        $valorCambio = $this->configIni($this->configIni("Globals", "Modo"), "TipoCambio");

        $cantidadData = count($data2);
        $pdf = new PDF_Mc_Table("P", "mm", "A4");
        //$titulos=array('Codigo','Descipcion','P. L.(S/.)','P.L.($)','Stock','U/M','Empaque');
        //$columnas=array('codigo','nompro','preciolista','stockactual','unidadmedida','empaque');
        $titulos = array('Codigo', 'Descipcion', 'P. L.', 'Stock', 'U/M', 'Empaque');
        $columnas = array('codigo', 'nompro', 'preciolista', 'stockactual', 'unidadmedida', 'empaque');
        $ancho = array(25, 95, 20, 10, 15, 15);
        $orientacion = array('', 'F', 'C', 'C', 'C', 'C');

        $pdf->_titulo = "Lista de Precios";
        $pdf->_fecha = date("d-m-Y");
        $pdf->AddPage();
        $relleno = true;
        $pdf->SetFillColor(200, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho
        $pdf->SetWidths($ancho);
        //un arreglo con alineacion de cada celda
        $pdf->SetAligns($orientacion);
        $cantidadTitulos = count($titulos);
        for ($i = 0; $i < $cantidadTitulos; $i++) {
            $pdf->Cell($ancho[$i], 7, $titulos[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(204, 102, 0);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $lineaA = 0;
        $subLineaA = 0;
        for ($i = 0; $i < $cantidadData; $i++) {

            if ($lineaA != $data2[$i]['idpadre']) {
                $lineaA = $data2[$i]['idpadre'];
                if ($i != 0) {
                    //en este espacio entraria los anexos
                    $pdf->AddPage();
                    for ($x = 0; $x < $cantidadTitulos; $x++) {
                        $pdf->Cell($ancho[$x], 7, $titulos[$x], 1, 0, 'C', true);
                    }
                    $pdf->Ln();
                }

                $dataLinea = $linea->buscaLinea($lineaA);
                $pdf->SetFillColor(0, 0, 255);

                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->_datoPie = $dataLinea[0]['nomlin'];
                $pdf->Cell(180, 6, "LINEA : " . $dataLinea[0]['nomlin'], 'B', 0, 'C', 0);
                $pdf->SetFont('Helvetica', 'B', 7);
                $pdf->Ln();
                $pdf->Cell(180, 1, "", 'B', 0, 'C', 0);

                $pdf->Ln();
            }
            if ($subLineaA != $data2[$i]['idlinea']) {
                $subLineaA = $data2[$i]['idlinea'];
                $pdf->SetFillColor(224, 224, 224);
                $pdf->Ln();
                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->Cell(180, 6, utf8_decode("Sub Linea : " . $data2[$i]['nomlin']), 1, 0, 'C', 1);
                $pdf->SetFont('Helvetica', 'B', 7);
                $pdf->Ln();

                $pdf->SetFillColor(255, 255, 255);
            }
            $fila = array(utf8_decode(html_entity_decode($data2[$i]['codigo'], ENT_QUOTES, 'UTF-8')), utf8_decode(html_entity_decode($data2[$i]['nompro'], ENT_QUOTES, 'UTF-8')), $simbmadel . $simbmatras . number_format($data2[$i]['preciolista'], 2), $data2[$i]['stockactual'], $data2[$i]['unidadmedida'], $data2[$i]['empaque']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function kardex() {
        $idAlmacen = $_REQUEST['idAlmacen'];
        $idLinea = $_REQUEST['idLinea'];
        $idSubLinea = $_REQUEST['idSubLinea'];
        $idProducto = $_REQUEST['idProducto'];
        $reporteKardex = new Reporte();
        $cliente = new Cliente();
        $orden = new Orden();
        $data = $reporteKardex->reporteKardex($idAlmacen, $idLinea, $idSubLinea, $idProducto);
        $unidadMedida = $this->unidadMedida();
        $tipoMovimiento = $this->tipoMovimiento();
        $data2 = array();
        for ($i = 0; $i < count($data); $i++) {
            $conceptoMovimiento = $this->conceptoMovimiento($data[$i]['tipomovimiento']);
            $nombreCliente = "";
            if ($data[$i]['idorden'] != null) {
                $do = $orden->buscarxid($data[$i]['idorden']);
                if ($do[0]['idcliente']) {
                    $dc = $cliente->buscaCliente($do[0]['idcliente']);
                    $nombreCliente = ($dc[0]['razonsocial'] != "") ? (html_entity_decode($dc[0]['razonsocial'], ENT_QUOTES, 'UTF-8')) : $dc[0]['nombres'] . " " . $dc[0]['apellidopaterno'] . " " . $dc[0]['apellidomaterno'];
                }
            }
            $data2[$i]['ndocumento'] = $data[$i]['ndocumento'];
            $data2[$i]['fechamovimiento'] = date('d/m/Y', strtotime($data[$i]['fechamovimiento']));
            $data2[$i]['conceptomovimiento'] = $conceptoMovimiento[($data[$i]['conceptomovimiento'])];
            $data2[$i]['tipomovimiento'] = substr($tipoMovimiento[($data[$i]['tipomovimiento'])], 0, 1);
            $data2[$i]['cantidad'] = $data[$i]['cantidad'];
            $data2[$i]['nombrecliente'] = $nombreCliente;
            $data2[$i]['stockdisponible'] = $data[$i]['stockdisponibledm'];
            $data2[$i]['unidadmedida'] = $unidadMedida[($data[$i]['unidadmedida'])];
            $data2[$i]['pu'] = number_format($data[$i]['pu'], 2);
            $data2[$i]['estadopedido'] = $data[$i]['estadopedido'];
        }

        $this->pdf_reportes = New pdf_reportes("L", "mm", "A4");
        $titulos = array('N Doc.', 'Fecha', 'Tipo', 'Concepto', 'Cant.', 'Origen/Destino', 'S/Disp', 'Medida', 'Precio', 'Estado');
        $columnas = array('ndocumento', 'fechamovimiento', 'conceptomovimiento', 'tipomovimiento', 'cantidad', 'nombrecliente', 'stockdisponible', 'unidadmedida', 'pu', 'estadopedido');
        $ancho = array(15, 20, 35, 20, 15, 100, 20, 15, 20, 20);
        $orientacion = array('', 'C', 'C', 'C', 'C', '', 'C', 'C', 'R', 'C');

        $this->pdf_reportes->_titulo = "Reporte de kardex";
        $this->pdf_reportes->AddPage();
        $this->pdf_reportes->SetFont('Arial', 'B', 10); //

        $this->pdf_reportes->ln();

        $this->pdf_reportes->PintaTablaN($titulos, $data2, $columnas, $ancho, $orientacion);

        $this->pdf_reportes->AliasNbPages();
        $this->pdf_reportes->Output();
    }

    function agotados() {
        $fecha = $_REQUEST['fecha'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $idProducto = $_REQUEST['idProducto'];

        $repote = new Reporte();
        $ordenCompra = new Ordencompra();
        $linea = new Linea();
        $cantidadDoc = 0;
        $rutaImagen = $this->rutaImagenesProducto();
        $data = $repote->reporteAgotados($fecha, $fechaInicio, $fechaFinal, $idProducto);

        //$data=$repote->reporteAgotados('','','','');
        $unidadMedida = $this->unidadMedida();
        $cantidadData = count($data);
        for ($i = 0; $i < $cantidadData; $i++) {
            $fu = ''; //Fecha ultima compra
            $fp = ''; //Fecha penultima compra
            $c1 = 0; //Cantidad 1
            $c2 = 0; //Cantidad 2
            $doc = $ordenCompra->lista2UltimasCompras($data[$i]['idproducto']);
            $cantidadDoc = count($doc);
            //Data orden compra
            if ($cantidadDoc) {
                if ($cantidadDoc == 2) {
                    $fu = $doc[0]['fordencompra'];
                    $fp = $doc[1]['fordencompra'];
                    $c1 = $doc[0]['cantidadsolicitadaoc'];
                    $c2 = $doc[1]['cantidadsolicitadaoc'];
                } else {
                    $fu = $doc[0]['fordencompra'];
                    $c1 = $doc[0]['cantidadsolicitadaoc'];
                }
            }


            //><img src="'.$rutaImagen.$data[$i]['codigo'].'/'.$data[$i]['imagen'].'"></td>';
            $data[$i]['codigo'] = $data[$i]['codigop'];
            $data[$i]['nompro'] = $data[$i]['nompro'];
            $data[$i]['fechaultima'] = date("d/m/Y", strtotime($fu));
            $data[$i]['cantidadultima'] = $c1;
            $data[$i]['fechapenultima'] = date("d/m/Y", strtotime($fp));
            $data[$i]['cantidadpenultima'] = $c2;
            $data[$i]['nomlin'] = $linea->nombrexid($data[$i]['idlinea']);
        }

        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array('Penultima', 'C.Penul', 'Ultima', 'C. Ultima', 'codigo', 'Descripcion', 'Sublinea');
        $columnas = array('fechapenultima', 'cantidadpenultima', 'fechaultima', 'cantidadultima', 'codigo', 'nompro', 'nomlin');
        $ancho = array(20, 15, 25, 18, 22, 110, 70);
        $orientacion = array('C', 'C', 'C', 'C', 'C', '', '');

        $pdf->_titulo = "Reporte de Agotados";


        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho

        $pdf->SetWidths($ancho);
        $valor = "Reporte de Ventas";
        $pdf->titlees($valor);
        //un arreglo con alineacion de cada celda

        $pdf->SetAligns($orientacion);

        for ($i = 0; $i < count($titulos); $i++) {
            $pdf->Cell($ancho[$i], 7, $titulos[$i], 1, 0, 'C', true);
        }
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        for ($i = 0; $i < $cantidadData; $i++) {

            $fila = array($data[$i]['fechapenultima'], $data[$i]['cantidadpenultima'], utf8_decode($data[$i]['fechaultima']), $data[$i]['cantidadultima'], $data[$i]['codigo'], utf8_decode($data[$i]['nompro']), $data[$i]['nomlin']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function generaFactura() {
        $pdf = $this->AutoLoadModel('pdf');
        $ordenventa = $this->AutoLoadModel('documento');

        $buscaFactura = $ordenventa->buscaDocumento($_REQUEST['id'], "");


        $idDoc = $_REQUEST['id'];

        $porcentaje = $buscaFactura[0]['porcentajefactura'];
        $modo = $buscaFactura[0]['modofacturado'];
        $numeroFactura = $buscaFactura[0]['numdoc'];
        $numeroRelacionado = $buscaFactura[0]['numeroRelacionado'];
        $serieFactura = str_pad($buscaFactura[0]['serie'], 3, '0', STR_PAD_LEFT);


        $dataFactura = $pdf->buscarxOrdenVenta($buscaFactura[0]['idordenventa']);
        $dataFactura[0]['numeroFactura'] = $numeroFactura;
        $dataFactura[0]['serieFactura'] = $serieFactura;
        $dataFactura[0]['fecha'] = date('d/m/Y');
        $dataFactura[0]['referencia'] = 'VEN: ' . $dataFactura[0]['idvendedor'] . ' DC: ' . $dataFactura[0]['idordenventa'];
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

        $total = 0;
        for ($i = 0; $i < count($data); $i++) {
            if ($porcentaje != "") {
                if ($modo == 1) {
                    $precio = $data[$i]['preciofinal'];
                    $data[$i]['preciofinal'] = (($precio * $porcentaje) / 100);
                } elseif ($modo == 2) {
                    $cantidad = $data[$i]['cantdespacho'];
                    $data[$i]['catdespacho'] = ceil(($cantidad * $porcentaje) / 100);
                }
            }

            $precioTotal = ($data[$i]['preciofinal']) * ($data[$i]['cantdespacho']);
            $data[$i]['subtotal'] = number_format($precioTotal, 2);
            $total+=$precioTotal;
        }

        $dataFactura[0]['importeov'] = $total;
        $dataFactura[0]['imprimir'] = 'no';
        $dataFactura[0]['numeroRelacionado'] = $numeroRelacionado;
        $this->pdf_facturas = New pdf_facturas("P", "mm", "A4");
        $this->pdf_facturas->SetLeftMargin(14.5);
        $this->pdf_facturas->SetAutoPageBreak(true, 0);
        $this->pdf_facturas->SetTextColor(0);
        $this->pdf_facturas->SetFont('Times', '', 8.5);
        $this->pdf_facturas->AddPage();

        $this->pdf_facturas->ln();
        $this->pdf_facturas->ImprimeFactura($dataFactura, $data);
        $this->pdf_facturas->AliasNbPages();
        $this->pdf_facturas->Output();
    }

    function imprimirFactura() {
        $pdf = $this->AutoLoadModel('pdf');
        $ordenventa = $this->AutoLoadModel('documento');
        $cobro = $this->AutoLoadModel('ordencobro');
        $idDoc = $_REQUEST['idDoc'];
        $buscaFactura = $ordenventa->buscaDocumento($idDoc, "");
        $numeroRelacionado = $_REQUEST['numeroRelacionado'];
        $tipodocumentorelacionado = $_REQUEST['tipodocumento'];

        //obtemos la condicion y tiempo de credito
        //obtemos los porcenajes y modo que fue facturado
        $porcentaje = $buscaFactura[0]['porcentajefactura'];
        $modo = $buscaFactura[0]['modofacturado'];
        $numeroFactura = $buscaFactura[0]['numdoc'];
        $serieFactura = str_pad($buscaFactura[0]['serie'], 3, '0', STR_PAD_LEFT);


        //acutalizamos Documento que ya fue impreso,numero Relacionado y su tipo
        $dataV['esimpreso'] = 1;
        $dataV['numerorelacionado'] = $numeroRelacionado;
        $dataV['tipoDocumentoRelacionado'] = $tipodocumentorelacionado;
        $filtro = "iddocumento='" . $idDoc . "'";
        $exitoE = $ordenventa->actualizarDocumento($dataV, $filtro);

        //Grabamos en
        //*********************//
        $dataFactura = $pdf->buscarxOrdenVenta($buscaFactura[0]['idordenventa']);
        $dataFactura[0]['numeroRelacionado'] = $numeroRelacionado;
        $dataFactura[0]['numeroFactura'] = $numeroFactura;
        $dataFactura[0]['serieFactura'] = $serieFactura;
        $dataFactura[0]['fecha'] = date('d/m/Y');
        $dataFactura[0]['referencia'] = 'VEN: ' . $dataFactura[0]['idvendedor'] . ' DC: ' . $dataFactura[0]['idordenventa'];
        $data = $pdf->buscarDetalleOrdenVenta($buscaFactura[0]['idordenventa']);

        $dataCobro = $pdf->buscarOrdenCompraxId($buscaFactura[0]['idordenventa']);
        if ($dataCobro[0]['escontado'] == 1 && $dataCobro[0]['escredito'] == 0 && $dataCobro[0]['esletras'] == 0) {
            $dataFactura[0]['condicion'] = 'CONTADO';
        } elseif ($dataCobro[0]['escredito'] == 1) {
            $dataFactura[0]['condicion'] = 'CREDITO';
        } elseif ($dataCobro[0]['escredito'] == 1) {
            $dataFactura[0]['condicion'] = 'LETRAS';
            //$dataFactura[0]['fechavencimiento']=$pdf->listaDetalleOrdenCompraxId($dataCobro[0]['idordencobro'],3);
        }



        $total = 0;
        for ($i = 0; $i < count($data); $i++) {
            if ($porcentaje != "") {
                if ($modo == 1) {
                    $precio = $data[$i]['precioaprobado'];
                    $data[$i]['precioaprobado'] = (($precio * $porcentaje) / 100);
                } elseif ($modo == 2) {
                    $cantidad = $data[$i]['cantaprobada'];
                    $data[$i]['cantaprobada'] = ceil(($cantidad * $porcentaje) / 100);
                }
            }

            $precioTotal = (($data[$i]['precioaprobado']) * ($data[$i]['cantaprobada'])) - ($data[$i]['tdescuentoaprovado']);
            $data[$i]['subtotal'] = number_format($precioTotal, 2);
            $total+=$precioTotal;
        }

        $dataFactura[0]['importeov'] = $total;

        $this->pdf_facturas = New pdf_facturas("P", "mm", "A4");
        $this->pdf_facturas->SetLeftMargin(14.5);
        $this->pdf_facturas->SetAutoPageBreak(true, 0);
        $this->pdf_facturas->SetTextColor(0);
        $this->pdf_facturas->SetFont('Times', '', 8.5);
        $this->pdf_facturas->AddPage();

        $this->pdf_facturas->ln();
        $this->pdf_facturas->ImprimeFactura($dataFactura, $data);
        $this->pdf_facturas->AliasNbPages();
        $this->pdf_facturas->Output();
    }

    function generaGuiaRemision() {
        $pdf = $this->AutoLoadModel('pdf');
        $ordenventa = $this->AutoLoadModel('documento');
        $idDoc = $_REQUEST['idDoc'];
        $numero = $_REQUEST['numeroRelacionado'];
        $numeroRelacionado = $_REQUEST['numeroRelacionado'];
        $tipodocumentorelacionado = $_REQUEST['tipodocumento'];
        $imprimir = "";
        $tipo = $this->tipoDocumento();

        if (!empty($_REQUEST['id'])) {
            $idDoc = $_REQUEST['id'];
            $imprimir = 'no';
        } else {
            //acutalizamos Documento que ya fue impreso
        }
        $buscaGuia = $ordenventa->buscaDocumento($idDoc, "");


        session_start();
        $usuario = $_SESSION['nombres'] . $_SESSION['apellidopaterno'];

        $dataGuia = $pdf->buscarxOrdenVenta($buscaGuia[0]['idordenventa']);
        $dataGuia[0]['imprimir'] = $imprimir;

        if (!empty($_REQUEST['id'])) {
            $dataGuia[0]['tipo'] = $buscaGuia[0]['tipoDocumentoRelacionado'];
            $dataGuia[0]['numeroRelacionado'] = $buscaGuia[0]['numeroRelacionado'];
        } else {
            $dataGuia[0]['tipo'] = $tipodocumentorelacionado;
            $dataGuia[0]['numeroRelacionado'] = $numero;
        }
        $dataGuia[0]['tipo'] = $tipo[$dataGuia[0]['tipo']];

        $dataGuia[0]['numeroFactura'] = $buscaGuia[0]['numdoc'];
        $dataGuia[0]['serieFactura'] = str_pad($buscaGuia[0]['serie'], 3, '0', STR_PAD_LEFT);
        $dataGuia[0]['fecha'] = date('d/m/Y');
        $dataGuia[0]['referencia'] = ' REF: ' . $dataGuia[0]['idordenventa'] . '    VEN: ' . $dataGuia[0]['idvendedor'] . '     ' . $usuario . '  --  ' . date('H:i:s');
        $dataGuia[0]['domiPartida'] = 'JR. ALFREDEZ DE FRAGATA RICARDO HERRERA 665 - LIMA';
        $data = $pdf->buscarDetalleOrdenVenta($buscaGuia[0]['idordenventa']);

        $pdf_guias = New pdf_facturas("P", "mm", "A4");
        $pdf_guias->SetLeftMargin(11.5);
        $pdf_guias->SetAutoPageBreak(true, 0);
        $pdf_guias->SetTextColor(0);

        $pdf_guias->AddPage();


        $pdf_guias->ImprimeGuia($dataGuia, $data);
        $pdf_guias->AliasNbPages();
        $pdf_guias->Output();
    }

    function generaBoleta() {
        $pdf = $this->AutoLoadModel('pdf');
        $documento = $this->AutoLoadModel('documento');

        //$numeroRelacionado=$_REQUEST['numeroRelacionado'];
        //$tipodocumentorelacionado=$_REQUEST['tipodocumento'];

        if (!empty($_REQUEST['id'])) {
            $idDoc = $_REQUEST['id'];
            $imprimir = 'no';
        } else {
            //acutalizamos Documento que ya fue impreso
            //$dataB['esimpreso']=1;
            //$dataB['numeroRelacionado']=$numeroRelacionado;
            //$dataB['tipoDocumentoRelacionado']=$tipodocumentorelacionado;
            //$filtro="iddocumento='".$idDoc."'";
            //$exitoE=$documento->actualizarDocumento($dataB,$filtro);
        }
        //*****************************//
        $buscaBoleta = $documento->buscaDocumento($idDoc, "");
        $dataBoleta = $pdf->buscarxOrdenVenta($buscaBoleta[0]['idordenventa']);
        $dataBoleta[0]['imprimir'] = $imprimir;
        $dataBoleta[0]['numeroBoleta'] = $buscaBoleta[0]['numdoc'];
        $dataBoleta[0]['serieBoleta'] = str_pad($buscaBoleta[0]['serie'], 3, '0', STR_PAD_LEFT);
        $dataBoleta[0]['fecha'] = date('d/m/Y');
        $dataBoleta[0]['referencia'] = ' REF: ' . $dataBoleta[0]['idordenventa'] . '    VEN: ' . $dataBoleta[0]['idvendedor'] . '     ' . $usuario . '  --  ' . date('H:i:s');
        $data = $pdf->buscarDetalleOrdenVenta($buscaBoleta[0]['idordenventa']);


        $pdf_boleta = New pdf_facturas("P", "mm", array(0 => 210, 1 => 146));
        $pdf_boleta->SetLeftMargin(13);
        $pdf_boleta->SetAutoPageBreak(true, 0);
        $pdf_boleta->SetTextColor(0);
        $pdf_boleta->AddPage();
        $pdf_boleta->ImprimeBoleta($dataBoleta, $data);
        $pdf_boleta->AliasNbPages();
        $pdf_boleta->Output();
    }

    function devolucion() {
        $devolucion = $this->AutoLoadModel('devolucion');
        $iddevolucion = $_REQUEST['id'];
        //obtenemos la orden de venta
        $dataDevolucion = $devolucion->listaDevolucionxid($iddevolucion);
        $idordenventa = $dataDevolucion[0]['idordenventa'];
        //$data=$pdf->buscarDetalleOrdenVenta($buscaBoleta[0]['idordenventa']);
        $pdf_boleta = New pdf_facturas("P", "mm", 'A4');
        $pdf_boleta->SetLeftMargin(13);
        $pdf_boleta->SetAutoPageBreak(true, 0);
        $pdf_boleta->SetTextColor(0);
        $pdf_boleta->AddPage();
        $pdf_boleta->ImprimeBoleta($dataDevolucion, $data);
        $pdf_boleta->AliasNbPages();
        $pdf_boleta->Output();
    }

    function reporteventas() {
        $fecha = $_REQUEST['fecha'];
        if (!empty($_REQUEST['txtFechaAprobadoInicio'])) {
            $txtFechaAprobadoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaAprobadoInicio']));
        }

        if (!empty($_REQUEST['txtFechaAprobadoFinal'])) {
            $txtFechaAprobadoFinal = date('Y-m-d', strtotime($_REQUEST['txtFechaAprobadoFinal']));
        }
        if (!empty($_REQUEST['txtFechaGuiadoInicio'])) {
            $txtFechaGuiadoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaGuiadoInicio']));
        }

        if (!empty($_REQUEST['txtFechaGuiadoFin'])) {
            $txtFechaGuiadoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaGuiadoFin']));
        }

        if (!empty($_REQUEST['txtFechaDespachoInicio'])) {
            $txtFechaDespachoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaDespachoInicio']));
        }

        if (!empty($_REQUEST['txtFechaDespachoFin'])) {
            $txtFechaDespachoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaDespachoFin']));
        }

        if (!empty($_REQUEST['txtFechaCanceladoInicio'])) {
            $txtFechaCanceladoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaCanceladoInicio']));
        }

        if (!empty($_REQUEST['txtFechaCanceladoFin'])) {
            $txtFechaCanceladoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaCanceladoFin']));
        }

        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $idCliente = $_REQUEST['idCliente'];
        $idVendedor = $_REQUEST['idVendedor'];
        $idpadre = $_REQUEST['idpadre'];
        $idcategoria = $_REQUEST['idcategoria'];
        $idzona = $_REQUEST['idzona'];
        $condicion = $_REQUEST['condicion'];
        $aprobados = $_REQUEST['aprobados'];
        $desaprobados = $_REQUEST['desaprobados'];
        $pendiente = $_REQUEST['pendiente'];
        $condVenta=$_REQUEST['condVenta'];
        $idmoneda=$_REQUEST['idmoneda'];
        $filtrocliente=$_REQUEST['filtrocliente'];
        $condicionVenta = "";
        if ($condicion == 1) {
            $condicionVenta = " and ov.es_contado='1' and ov.es_credito!='1' and ov.es_letras!='1' ";
        } elseif ($condicion == 2) {
            $condicionVenta = " and ov.es_credito='1' and ov.es_letras!='1' ";
        } elseif ($condicion == 3) {
            $condicionVenta = "  and ov.es_letras='1' and  ov.tipo_letra=1";
        } elseif ($condicion == 4) {
            $condicionVenta = "  and ov.es_letras='1' and ov.tipo_letra=2";
        }

        $reporte = $this->AutoLoadModel('reporte');

        $dataReporte = $reporte->reporteVentas($txtFechaAprobadoInicio, $txtFechaAprobadoFinal, $txtFechaGuiadoInicio, $txtFechaGuiadoFin, $txtFechaDespachoInicio, $txtFechaDespachoFin, $txtFechaCanceladoInicio, $txtFechaCanceladoFin, $idOrdenVenta, $idCliente, $idVendedor, $idpadre, $idcategoria, $idzona, $condicionVenta, $aprobados, $desaprobados, $pendiente, $idmoneda, $condVenta, $filtrocliente);
        $cantidad = count($dataReporte);
        $totalAprobado = 0;
        $totalDespachado = 0;
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array('Fecha Guiado', 'Fecha Despacho', 'Fecha Cancelado', 'Orden Venta', 'Nombre Cliente', 'Nombre Vendedor', 'Importe Aprobado', 'Importe Despachado', 'Estado', 'Condicion Venta', 'Detalle');
        $ancho = array(15, 15, 15, 18, 40, 40, 18, 18, 17, 17, 60);
        $orientacionTitulos = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $orientacion = array('C', 'C', 'C', '', '', '', '', '', '', '', '');
        $pdf->_titulo = "Reporte de Ventas";
        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho
        $pdf->SetWidths($ancho);
        $valor = "Reporte de Ventas";
        //un arreglo con alineacion de cada celda
        $pdf->SetAligns($orientacionTitulos);


        $fila = $titulos;
        $pdf->Row($fila);
        $relleno = !$relleno;
        $pdf->fill($relleno);
        $pdf->SetAligns($orientacion);
        $pdf->Ln();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');

        $totalAprobaDol = 0;
        $totalDespachadoDol = 0;
        $totalDevolucionDol = 0;

        $totalAprobaSol = 0;
        $totalDespachadoSol = 0;
        $totalDevolucionSol = 0;
        for ($i = 0; $i < $cantidad; $i++) {
            $situtacion = "";
            if ($dataReporte[$i]['es_contado'] == 1 && $dataReporte[$i]['es_credito'] != 1 && $dataReporte[$i]['es_letras'] != 1) {
                $situtacion = "Contado";
            } elseif ($dataReporte[$i]['es_credito'] == 1 && $dataReporte[$i]['es_letras'] != 1) {
                $situtacion = "Credito";
            } elseif ($dataReporte[$i]['es_letras'] == 1 && $dataReporte[$i]['tipo_letra'] == 1) {
                $situtacion = "Letra Banco";
            } elseif ($dataReporte[$i]['es_letras'] == 1 && $dataReporte[$i]['tipo_letra'] == 2) {
                $situtacion = "Letra Cartera";
            }
            $estado = "Pendiente";
            if ($dataReporte[$i]['desaprobado'] == 1) {
                $estado = "Desaprobado";
            } elseif ($dataReporte[$i]['vbcreditos'] == 1) {
                $estado = "Aprobado";
            }
            if ($dataReporte[$i]['vbcreditos'] != 1) {
                $valorImporte = 0.00;
                $devolucion = 0;
            } else {
                $devolucion = $dataReporte[$i]['importedevolucion'];
                $valorImporte = $dataReporte[$i]['importeov'];
            }

            if ($dataReporte[$i]['simbolo'] == 'US $') {
                $totalAprobaDol+=$dataReporte[$i]['importeaprobado'];
                $totalDespachadoDol+=$valorImporte;
                $totalDevolucionDol+=$devolucion;
            } else {
                $totalAprobaSol+=$dataReporte[$i]['importeaprobado'];
                $totalDespachadoSol+=$valorImporte;
                $totalDevolucionSol+=$devolucion;
            }

            $fila = array($dataReporte[$i]['fordenventa'], $dataReporte[$i]['fechadespacho'], $dataReporte[$i]['fechaCancelado'], $dataReporte[$i]['codigov'], html_entity_decode($dataReporte[$i]['razonsocial'], ENT_QUOTES, 'UTF-8'), ($dataReporte[$i]['apellidopaterno'] . ' ' . $dataReporte[$i]['apellidomaterno'] . ' ' . $dataReporte[$i]['nombres']), $dataReporte[$i]['simbolo'] . ' ' . $dataReporte[$i]['importeaprobado'], $dataReporte[$i]['simbolo'] . ' ' . $valorImporte, $estado, $situtacion, strip_tags(html_entity_decode($dataReporte[$i]['observaciones'], ENT_QUOTES, 'UTF-8')));
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->Cell(45, 8, '', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'MONEDA', 1, 0, 'C', true);
        $pdf->Ln();
        $pdf->Cell(45, 8, 'IMPORTE TOTAL APROBADO: ', 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(25, 8, "US $. " . number_format($totalAprobaDol, 2), 1, 0, 'R', true);
        $pdf->Cell(25, 8, "S/. " . number_format($totalAprobaSol, 2), 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->Cell(45, 8, 'IMPORTE TOTAL DESPACHADO: ', 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(25, 8, "US $. " . number_format($totalDespachadoDol, 2), 1, 0, 'R', true);
        $pdf->Cell(25, 8, "S/. " . number_format($totalDespachadoSol, 2), 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->Cell(45, 8, 'IMPORTE DEVOLUCION: ', 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(25, 8, "US $. " . number_format($totalDevolucionDol, 2), 1, 0, 'R', true);
        $pdf->Cell(25, 8, "S/. " . number_format($totalDevolucionSol, 2), 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->Cell(45, 8, 'VENTA NETA: ', 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(25, 8, "US $. " . number_format($totalDespachadoDol-$totalDevolucionDol, 2), 1, 0, 'R', true);
        $pdf->Cell(25, 8, "S/. " . number_format($totalDespachadoSol-$totalDevolucionsol, 2), 1, 0, 'R', true);
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteInventario() {
        $reporte = $this->AutoLoadModel('reporte');
        $actor = $this->AutoLoadModel('actor');
        $idInventario = $_REQUEST['lstInventario'];
        $idBloque = $_REQUEST['lstBloques'];
        $idProducto = $_REQUEST['idProducto'];

        $data = $reporte->reporteInventario($idInventario, $idBloque, $idProducto);
        $dataRolesPorBloque = $reporte->dataRolesPorBloque($idInventario, $idBloque);
        $cantidadData = count($data);

        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array(utf8_decode('N°'), 'Codigo', 'Descripcion', 'Conteo 1','Conteo 2','Conteo 3', 'Merma', 'Serv.Tec', 'S.Room', 'Total', 'Stock.Venta','Stock.Cierre','Fecha Registro');
        $ancho = array(7, 22, 85, 15,15,15, 14, 16, 14, 13, 19,19, 25, 12);
        $orientacion = array('C', '', '', 'C','C','C', 'C', 'C', 'C', 'C', 'C', 'C', 'R');
        $pdf->_titulo = "INVENTARIO GENERAL  ".$data[0]['codigoinv']."  || BLOQUE Y/O ANAQUEL  ".'"'.$data[0]['codigo'].'"                                                      ';
        $pdf->AddPage();
        //un arreglo con su medida  a lo ancho
        $pdf->SetWidths($ancho);
        //un arreglo con alineacion de cada celda
        $pdf->SetAligns($orientacion);


if($cantidadData==0){
        $consultar_producto_de_inventario_segun_bloque = $reporte->consultar_producto_de_inventario_segun_bloque($idInventario,$idProducto);
        $bloque= $consultar_producto_de_inventario_segun_bloque[0]['bloque'];
        $ancho = array(275);
        $pdf->SetWidths($ancho);
        $orientacion = array('C');
        $pdf->ln();
if($idProducto!==''){
        if($consultar_producto_de_inventario_segun_bloque>1){
        $fila3 = array('El producto que ha seleccionado pertenece al bloque  "'.$bloque.'" ');
        }else{
        $fila3 = array('El producto que ha seleccionado no esta en ningun bloque del inventario');
        }
}else{
            $fila3 = array('No existen productos en el bloque del inventario selecionado');

}

       $pdf->Row($fila3);
}



        $contB = 0;
        $contS = 0;

           //
            $valorizado_cnProductosMerma=0;
            $valorizado_cnProductosReparacion=0;
            $valorizado_cnProductosVenta=0;

           //
           $valorizado_FinalMerma =0;
           $valorizado_FinalReparacion =0;
           $valorizado_FinalVenta =0;

            //
            $cnProductosMerma =0;
            $cnProductosReparacion =0;
            $cnProductosVenta=0;

        for ($i = 0; $i < $cantidadData; $i++) {
            $bloqueA = $data[$i - 1]['idbloque'];
            if ($i == 0) {
                if ($i != 0) {
                    $pdf->AddPage();
                }
                $relleno = true;
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetTextColor(12, 78, 139);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->fill($relleno);

                $pdf->Cell(25, 7, 'Fecha Inicio', 1, 0, 'C', true);
                $pdf->Cell(32, 7, $data[$i]['fechainv'], 1, 0, 'C', true);
                $pdf->Cell(28, 7, 'Fecha Termino', 1, 0, 'C', true);
                $pdf->Cell(54, 7, 'El inventario aun no se ha cerrado', 1, 0, 'C', true);
                $pdf->Cell(25, 7, 'Jefe de Sistemas', 1, 0, 'C', true);
                $pdf->Cell(32, 7,$dataRolesPorBloque[0]["responsable1"], 1, 0, '', false);
                $pdf->Cell(28, 7, 'Rol o Funcion', 1, 0, 'C', true);
                $pdf->Cell(51, 7,substr($dataRolesPorBloque[0]["funcionresponsable1"],0,37), 1, 0, '', false);
                $pdf->ln();

                $pdf->Cell(25, 7, 'Auditor', 1, 0, 'C', true);
                $pdf->Cell(32, 7,$dataRolesPorBloque[0]["responsable2"], 1, 0, '', false);
                $pdf->Cell(28, 7, 'Rol o Funcion', 1, 0, 'C', true);
                $pdf->Cell(54, 7, $dataRolesPorBloque[0]["funcionresponsable2"], 1, 0, '', false);
                $pdf->Cell(25, 7, 'Jefe de Almacen', 1, 0, 'C', true);
                $pdf->Cell(32, 7,$dataRolesPorBloque[0]["responsable3"], 1, 0, '', false);
                $pdf->Cell(28, 7, 'Rol o Funcion', 1, 0, 'C', true);
                $pdf->Cell(51, 7, $dataRolesPorBloque[0]["funcionresponsable3"], 1, 0, '', false);
                $pdf->ln();
                $pdf->Cell(15, 7, 'Veedor 1', 1, 0, 'C', true);
                $pdf->Cell(50, 7,$dataRolesPorBloque[0]["auxiliar1"], 1, 0, '', false);
                $pdf->Cell(20, 7, 'Rol o Funcion', 1, 0, 'C', true);
                $pdf->Cell(54, 7, $dataRolesPorBloque[0]["funcionauxiliar1"], 1, 0, '', false);
                $pdf->Cell(15, 7, 'Veedor 2', 1, 0, 'C', true);
                $pdf->Cell(50, 7,$dataRolesPorBloque[0]["auxiliar2"], 1, 0, '', false);
                $pdf->Cell(20, 7, 'Rol o Funcion', 1, 0, 'C', true);
                $pdf->Cell(51, 7, $dataRolesPorBloque[0]["funcionauxiliar2"], 1, 0, '', false);
                $pdf->ln();
                $pdf->Cell(15, 7, 'Veedor 3', 1, 0, 'C', true);
                $pdf->Cell(50, 7,$dataRolesPorBloque[0]["auxiliar3"], 1, 0, '', false);
                $pdf->Cell(20, 7, 'Rol o Funcion', 1, 0, 'C', true);
                $pdf->Cell(190, 7, $dataRolesPorBloque[0]["funcionauxiliar3"], 1, 0, '', false);
                $pdf->ln();
                $pdf->ln();
                $fila = $titulos;
                $pdf->Row($fila);
        }
//obtenemos el valor de cada producto segun condicion :sea merma, reparacion, venta
            $valorizado_cnProductosMerma=$data[$i]['malos']*$data[$i]['precioinventario'];
            $valorizado_cnProductosReparacion=$data[$i]['servicio']*$data[$i]['precioinventario'];
            $valorizado_cnProductosVenta=$data[$i]['stockinventario']*$data[$i]['precioinventario'];

//sumamos el valorizado con el valorizado anterior segun condicion: sea merma ,reparacion, venta
           $valorizado_FinalMerma =$valorizado_cnProductosMerma+$valorizado_FinalMerma;
           $valorizado_FinalReparacion =$valorizado_cnProductosReparacion+$valorizado_FinalReparacion;
           $valorizado_FinalVenta =$valorizado_cnProductosVenta+$valorizado_FinalVenta;


           //solo cantidades
            $cnProductosMerma =$data[$i]['malos']+$cnProductosMerma;
            $cnProductosReparacion =$data[$i]['servicio']+$cnProductosReparacion;
            $cnProductosVenta=$data[$i]['stockinventario']+$cnProductosVenta;



            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('Helvetica', '',7);
            $fila = array($i + 1, html_entity_decode($data[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode(utf8_decode($data[$i]['nompro']), ENT_QUOTES, 'UTF-8'), $data[$i]['buenos'],$data[$i]['buenos2'], $data[$i]['buenos3'], $data[$i]['malos'], $data[$i]['servicio'], $data[$i]['showroom'],$data[$i]['productosTotal'],$data[$i]['stockinventario'],$data[$i]['stockanterior'],$data[$i]['reg_det_inv']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
  if($cantidadData>=1){
        /*start fila valorizados*/
        $ancho = array(36, 30, 16, 46, 32, 26, 52, 37, 41);
        $pdf->SetWidths($ancho);
        $orientacion = array('C', '', '', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $pdf->ln();
        $fila3 = array("Productos para Merma",$cnProductosMerma, "", "Productos para Reparacion",$cnProductosReparacion ," ",  "Productos para venta",$cnProductosVenta);
        $pdf->Row($fila3);
   //     $fila4 = array("               Valorizado", "S/." . number_format($valorizado_FinalMerma, 2), "", "                         Valorizado", "S/." . number_format($valorizado_FinalReparacion, 2)," ",  "                Valorizado ", "S/." . number_format($valorizado_FinalVenta, 2));
       $fila4 = array("           Valorizado CIF ", "$. " . number_format($valorizado_FinalMerma, 2, '.', ''), "", "                       Valorizado CIF ", "$. " . number_format($valorizado_FinalReparacion, 2, '.', '')," ",  "            Valorizado CIF", "$. " . number_format($valorizado_FinalVenta, 2, '.', ''));
        $pdf->Row($fila4);
        $pdf->ln();
        $pdf->ln();
        /*fin fila valorizado*/

        /*start fila firmas*/
        $pdf->Cell(65, 7, 'AUDITOR CONTABILIDAD', 0, 0, 'C', FALSE);
        $pdf->Cell(4, 7, '', 0, 0, 'C', FALSE);
        $pdf->Cell(5, 7, '',0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, 'JEFE DE SISTEMAS', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, 'JEFE DE ALMACEN', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, 'GERENCIA GENERAL', 0, 0, 'C', FALSE);
        $pdf->ln();
        $pdf->Cell(65, 7, '__________________________', 0, 0, 'C', FALSE);
        $pdf->Cell(4, 7, '', 0, 0, 'C', FALSE);
        $pdf->Cell(5, 7, '',0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, '__________________________', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, '__________________________', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, '__________________________', 0, 0, 'C', FALSE);
        /*end filas firmas*/
    }

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function kardexTotalxProducto() {

        $mesInicial = !empty($_REQUEST['mesInicial']) ? $_REQUEST['mesInicial'] : 1;
        $mesFinal = !empty($_REQUEST['mesFinal']) ? $_REQUEST['mesFinal'] : 12;
        $ano = !empty($_REQUEST['periodo']) ? $_REQUEST['periodo'] : date('Y');



        //$sunat=$_REQUEST['sunat'];
        $movimiento = new Movimiento();

        $dataKardex = $movimiento->kardexTotalxProducto($ano, $mesInicial, $mesFinal);
        $total = count($dataKardex);

        $idproducto = 0;
        $cont = -1;
        $totIngreso = 0;
        $totSalida = 0;
        $datos = array();
        for ($i = 0; $i < $total; $i++) {

            if ($idproducto != $dataKardex[$i]['idproducto']) {
                $idproducto = $dataKardex[$i]['idproducto'];

                if ($dataKardex[$i]['codigotipooperacion'] != 16) {
                    $cont++;
                    $datos[$cont]['idproducto'] = $idproducto;
                    $datos[$cont]['nompro'] = $dataKardex[$i]['nompro'];
                    $datos[$cont]['codigopa'] = $dataKardex[$i]['codigopa'];
                    if ($dataKardex[$i]['tipomovimiento'] == 1) {

                        $cantidad = round($dataKardex[$i]['SaldoCantidad'] - round($dataKardex[$i]['cantidad']));
                        if ($cantidad < 0) {
                            $cantidad = 0;
                        }

                        $datos[$cont]['saldoInicial'] = $cantidad * $dataKardex[$i]['SaldoPrecio'];
                    } elseif ($dataKardex[$i]['tipomovimiento'] == 2) {

                        $cantidad = round($dataKardex[$i]['SaldoCantidad'] + round($dataKardex[$i]['cantidad']));
                        $datos[$cont]['saldoInicial'] = $cantidad * $dataKardex[$i]['SaldoPrecio'];
                    }
                } elseif ($dataKardex[$i]['codigotipooperacion'] == 16) {
                    $cont++;
                    $datos[$cont]['idproducto'] = $dataKardex[$i]['idproducto'];
                    $datos[$cont]['nompro'] = $dataKardex[$i]['nompro'];
                    $datos[$cont]['codigopa'] = $dataKardex[$i]['codigopa'];
                    $datos[$cont]['saldoInicial'] = $dataKardex[$i]['SaldoPrecio'] * $dataKardex[$i]['SaldoCantidad'];
                }
            }

            if ($idproducto == $dataKardex[$i]['idproducto']) {
                $totIngreso+=$dataKardex[$i]['EntradaCosto'];
                $totSalida+=$dataKardex[$i]['SalidaCosto'];
                if ($idproducto != $dataKardex[$i + 1]['idproducto']) {

                    $datos[$cont]['EntradaTotal'] = $totIngreso;
                    $datos[$cont]['SalidaTotal'] = $totSalida;
                    $datos[$cont]['SaldoFinal'] = $dataKardex[$i]['SaldoPrecio'] * $dataKardex[$i]['SaldoCantidad'];

                    $totIngreso = 0;
                    $totSalida = 0;
                }
            }
        }

        $cantidadData = count($datos);
        $totIngresoSum = 0;
        $totInicialSum = 0;
        $totFinalSum = 0;
        /**/
        $pdf = new PDF_Mc_Table("P", "mm", "A4");
        $titulos = array('Codigo', 'Descripcion', 'Saldo Inicial', 'Compras', 'Total Existencias', 'Existencia Final', 'Costo de Ventas');
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(25, 70, 20, 20, 20, 20, 20);
        $orientacion = array('', '', 'R', 'R', 'R', 'R', 'R');
        $pdf->_titulo = "Reporte de Total de Kardex Valorizado en S/.";
        $pdf->_datoPie = "Periodo: " . $ano . " de " . $this->meses($mesInicial) . "  a  " . $this->meses($mesFinal);
        //$pdf->_imagenCabezera = ROOT . 'imagenes' . DS . 'POWER-ACOUSTIK.jpg';

        $pdf->SetWidths($ancho);
        $pdf->_titulos = $titulos;
        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);

        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho
        //un arreglo con alineacion de cada celda
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        for ($i = 0; $i < $cantidadData; $i++) {

            $totInicialSum+=$datos[$i]['saldoInicial'];
            $totIngresoSum+=$datos[$i]['EntradaTotal'];
            $totFinalSum+=$datos[$i]['SaldoFinal'];
            $fila = array(html_entity_decode($datos[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode($datos[$i]['nompro'], ENT_QUOTES, 'UTF-8'), number_format($datos[$i]['saldoInicial'], 2), number_format($datos[$i]['EntradaTotal'], 2), number_format($datos[$i]['saldoInicial'] + $datos[$i]['EntradaTotal'], 2), number_format($datos[$i]['SaldoFinal'], 2), number_format(($datos[$i]['saldoInicial'] + $datos[$i]['EntradaTotal']) - $datos[$i]['SaldoFinal'], 2));
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $relleno = false;
        $pdf->fill($relleno);
        $fila = array('', 'TOTALES', number_format($totInicialSum, 2), number_format($totIngresoSum, 2), number_format($totInicialSum + $totIngresoSum, 2), number_format($totFinalSum, 2), number_format($totInicialSum + $totIngresoSum - $totFinalSum, 2));
        $pdf->Row($fila);
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteOrdenCompra() {
        $ordenCompra = $this->AutoLoadModel('reporte');
        $idOrdenCompra = $_REQUEST['idOrdenCompra'];
        $idProducto = $_REQUEST['idProducto'];

        $datos = $ordenCompra->reporteOrdenCompraRevision($idOrdenCompra, $idProducto);
        $cantidadData = count($datos);
        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $titulos = array('Codigo', 'Descripcion', 'FOB($)', 'CIF($)', 'P.L.($)', 'S.A', 'S.D', 'Compra', 'U.M', 'Pcs', 'Ctn');
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(20, 70, 13, 13, 13, 13, 12, 12, 10, 7, 8);
        $orientacion = array('', '', 'R', 'R', 'R', 'C', 'C', 'C', '', 'C', 'C');


        $pdf->SetWidths($ancho);
        $pdf->_titulos = $titulos;
        $pdf->_titulo = "Reporte Orden Compra";
        $pdf->_fecha = $datos[0]['codigooc'];
        $pdf->_datoPie = "F. Ingreso : " . $datos[0]['fordencompra'] . " - F. A .LLegada: " . $datos[0]['faproxllegada'] . " - F. Impresion: " . date('Y-m-d H:i:s');
        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);

        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho
        //un arreglo con alineacion de cada celda
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $importe = 0;
        for ($i = 0; $i < $cantidadData; $i++) {
            if ($datos[$i]['preciolistadolares'] > 0) {
                $preciolistadolares = $datos[$i]['preciolistadolares'];
            } else {
                $preciolistadolares = $datos[$i]['preciolista']*$datos[$i]['valortipocambio'];
            }

            $fila = array(html_entity_decode($datos[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode($datos[$i]['nompro'], ENT_QUOTES, 'UTF-8'), number_format($datos[$i]['fobdoc'], 2), number_format($datos[$i]['cifventasdolares'], 2), number_format($preciolistadolares, 2), round($datos[$i]['stockactual']), $datos[$i]['stockdisponible'], $datos[$i]['cantidadrecibidaoc'], html_entity_decode($datos[$i]['codigo'], ENT_QUOTES, 'UTF-8'), $datos[$i]['piezas'], $datos[$i]['carton']);
            $importe+=$preciolistadolares * $datos[$i]['cantidadrecibidaoc'];
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $relleno = false;
        $pdf->fill($relleno);
        $fila = array('TOTAL $.', number_format($importe, 2));
        $pdf->Row($fila);
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    
    function historialOrdenCompra() {
        $ordenCompra = $this->AutoLoadModel('reporte');
        $idProducto = $_REQUEST['idProducto'];

        $datos = $ordenCompra->historialProducto($idProducto);
        $cantidadData = count($datos);
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $titulos = array('C. Orden', 'Fecha Ingreso', 'F. Aprox. Llegada', 'Codigo', 'Descripcion', 'FOB($)', 'CIF($)', 'P.L.($)', 'Compra', 'U.M');
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(20, 20, 22, 25, 100, 15, 15, 15, 15, 15);
        $orientacion = array('C', 'C', 'C', '', '', 'R', 'R', 'R', 'C', '');


        $pdf->SetWidths($ancho);
        $pdf->_titulos = $titulos;
        $pdf->_titulo = "Historial Compras del Producto";
        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);

        $pdf->fill($relleno);
        //un arreglo con su medida  a lo ancho
        //un arreglo con alineacion de cada celda
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $importe = 0;
        for ($i = 0; $i < $cantidadData; $i++) {
            if ($datos[$i]['preciolistadolares'] > 0) {
                $preciolistadolares = $datos[$i]['preciolistadolares'];
            } else {
                $preciolistadolares = $datos[$i]['preciolista']*$datos[$i]['valortipocambio'];
            }
            $fila = array($datos[$i]['codigooc'], $datos[$i]['fordencompra'], $datos[$i]['faproxllegada'], html_entity_decode($datos[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode($datos[$i]['nompro'], ENT_QUOTES, 'UTF-8'), number_format($datos[$i]['fobdoc'], 2), number_format($datos[$i]['cifventasdolares'], 2), number_format($preciolistadolares, 2), $datos[$i]['cantidadrecibidaoc'], html_entity_decode($datos[$i]['codigo'], ENT_QUOTES, 'UTF-8'));
            $importe+=$preciolistadolares * $datos[$i]['cantidadrecibidaoc'];
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteCarteraClientes() {
        set_time_limit(500);

        $zonasseleccion = $_REQUEST['zonaSelec'];
        $condZona = '';
        if (!empty($zonasseleccion)){
            $condZona = ' and c.zona IN ('.$zonasseleccion[0];
            $tamZon = count($zonasseleccion);
            for($i = 1; $i < $tamZon; $i++) {
                $condZona .= ", ".$zonasseleccion[$i];
            }
            $condZona .= ')';
        }
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
        $datos = $cartcli->listarCartera($condZona);

        $titulos = array('N°', 'CLIENTE', 'RUC', 'TELF.', 'DIRECCION', 'EMAIL', 'ULT. GUIA', 'FECHA GUIA', 'PROM.COMPRA', "MAX.MORA", "DEUDA", "CALIFICACION");
        $ancho = array(10, 45, 20, 33, 70, 30, 20, 20, 23, 20, 20, 23);
        $orientacion = array('R', 'L', 'C', 'C', 'L', 'C', 'C', 'C', 'R', 'C', 'R', 'C');
        $cantidadData = count($datos);

        $pdf = new PDF_MC_Table("L", "mm", "Legal");
        $pdf->SetWidths($ancho);
        if (!empty($idvend)) {
            $vendedor = new Actor();
            $reg = $vendedor->buscarxid($idvend);
            $pdf->_titulo = "VENDEDOR: ".$reg[0]['nombres']." ".$reg[0]['apellidopaterno'];
        }
        else {
            $pdf->_titulo = "CARTERA DE CLIENTES";
        }

        $fecha = "";
        if (!empty($fecini)) {
            $fecha .= "GUIAS DESDE ".$fecini;
        }
        else {
            $fecha .= "TODAS LAS GUIAS";
        }
        $fecha.=" HASTA ";
        if (!empty($fecfin)) {
            $fecha .= $fecfin;
        }
        else {
            $fecha .= date('Y/m/d');
        }
        $pdf->_fecha = $fecha;

        $pdf->_datoPie = "FECHA IMPRESION: " . date('Y/m/d');
        $pdf->AddPage();
        $relleno = false;
        $pdf->fill($relleno);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $cond2 = "";
        if ($aprobados == 2) $cond2 = " - APROBADOS" ;
        else if ($aprobados == 3) $cond2 = " - PENDIENTES" ;
        else if ($aprobados == 4) $cond2 = " - DESAPROBADOS" ;
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->Cell(150, 7, "CONDICION: ".(empty($condicion) ? "TODOS" : ($condicion==1 ? 'CONTADO' : ($condicion==2 ? "CREDITO" : ($condicion==3 ? 'LETRAS BANCO' : 'LETRAS CARTERA')))).$cond2, 0);

        $zona = "";
        $total = 0.0;
        $cant = 0;

        $linea = new Linea();
        $ordenventa = $this->AutoLoadModel('ordenventa');
        for ($i = 0; $i < $cantidadData; $i++) {
            /*$reg = $linea->lineasCliente($datos[$i]['idcliente']);
            $j = count($reg);
            $ejecuta = FALSE;
            if (empty($linprod)) {
                $ejecuta = TRUE;
            }
            else {
                for($k = 0; $k<$j; $k++) {
                    if ($reg[$k]['idlinea'] == $linprod) {
                        $ejecuta = TRUE;
                        break;
                    }
                }
            }

            $lineas = "";
            for($k = 0; $k<$j; $k++) {
                if ($k!=0) {
                    $lineas .= ", ";
                }
                $lineas .= $reg[$k]['nomlin'];
            }

            if ($ejecuta) {*/
            $datosOrden = $ordenventa->detalleOrden($datos[$i]['idcliente'], 2);
            $datosOrden2 = $ordenventa->detalleOrden($datos[$i]['idcliente'], 1);
            $tienedeuda = (($datosOrden['deuda'] >=1.0) || ($datosOrden2['deuda'] >=1.0));
            if ((empty($mostrar))||($mostrar == 'D' && $tienedeuda)||($mostrar == 'N' && !$tienedeuda)) {
                $tot=0;
                if ($datosOrden['sumtotal']<100 || $datosOrden2['sumtotal']<500) {
                    $tot += 3;
                }
                elseif (($datosOrden['sumtotal']>=100 && $datosOrden['sumtotal']<900) || ($datosOrden2['sumtotal']>=500 && $datosOrden2['sumtotal']<1200)) {
                    $tot += 2;
                }
                else {
                    $tot += 1;
                }

                if ($datosOrden['diasmora']<5 || $datosOrden2['diasmora']<5) {
                    $tot += 1;
                }
                elseif (($datosOrden['diasmora']>=5 && $datosOrden['diasmora']<90) || ($datosOrden2['diasmora']>=5 && $datosOrden2['diasmora']<90)) {
                    $tot += 2;
                }
                else {
                    $tot += 3;
                }
                $datosOrden['calif'] = $tot;

                if ($zona != $datos[$i]['nombrezona']) {
                    $pdf->ln();
                    $zona = $datos[$i]['nombrezona'];
                    $pdf->SetFont('Helvetica', 'B', 8.5);
                    $pdf->Cell(150, 7, "ZONA: " . $datos[$i]['nombrezona'], 0);
                    $pdf->ln();
                    $pdf->SetFont('Helvetica', 'B', 7.5);
                    $relleno = true;
                    $pdf->fill($relleno);
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetDrawColor(12, 78, 139);
                    $pdf->SetLineWidth(.3);
                    $pdf->Row($titulos);
                }

                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');


                $fila = array(($i+1), utf8_decode(html_entity_decode($datos[$i]['cliente'], ENT_QUOTES, 'UTF-8')), $datos[$i]['ruc'], $datos[$i]['telefono'] . (empty($datos[$i]['telefono']) || empty($datos[$i]['celular']) ? "" : " / ") . $datos[$i]['celular'], utf8_decode(html_entity_decode($datos[$i]['direccion'] . ", " . $datos[$i]['dist'] . " - " . $datos[$i]['prov'] . " - " . $datos[$i]['depa'], ENT_QUOTES, 'UTF-8')), $datos[$i]['email'], $datos[$i]['codigov'], $datos[$i]['fordenventa'], 'US$'.round($datosOrden['sumtotal'], 2), round($datosOrden['diasmora']), 'US$'.round($datosOrden['deuda'], 2), ($datosOrden['calif']==2 || $datosOrden['calif']==3 ? 'BUENO' : ($datosOrden['calif']==4 || $datosOrden['calif']==5 ? 'REGULAR' : 'CLASE C')));
                $pdf->Row($fila);
                $fila = array('', '', '', '', '', '', '', '', 'S/.'.round($datosOrden2['sumtotal'], 2), round($datosOrden2['diasmora']), 'S/.'.round($datosOrden2['deuda'], 2), '');
                $pdf->Row($fila);
                $relleno = !$relleno;
                $pdf->fill($relleno);
            }
            //}
        }
        $pdf->ln();
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteHistorialVentasxProducto() {
        $idVendedor = $_REQUEST['idVendedor'];
        $idProducto = $_REQUEST['idProducto'];
        $idCliente = $_REQUEST['idCliente'];
        $reporte = $this->AutoLoadModel('reporte');

        if ($idProducto == 0) {
            $idProducto = "";
        }
        $datos = $reporte->historialVentasxProducto($idProducto, $idVendedor, $idCliente);
        $cantidadData = count($datos);

        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $titulos = array('Orden Venta', 'FECHA', 'CLIENTE', 'VENDEDOR', 'ORIG.', 'U.M.', 'PRECIO', 'CANT.', 'IMPORTE');
          
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(17, 14, 50, 50, 10, 11, 15, 13, 15);
        $orientacion = array('C', 'C', '', '', 'C', 'C', 'R', 'C', 'R', '');
 
        $pdf->SetWidths($ancho);
        $pdf->_titulos = $titulos;
        $pdf->_titulo = "Ventas x Producto";
        $pdf->_fecha = $datos[0]['codigopa'];
        $pdf->_datoPie = date('Y-m-d');
        $pdf->AddPage();


        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
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
            $fila = array($datos[$i]['codigov'], $datos[$i]['fordenventa'], utf8_decode(html_entity_decode($datos[$i]['razonsocial'], ENT_QUOTES, 'UTF-8')), utf8_decode(html_entity_decode($datos[$i]['nombres'] . ' ' . $datos[$i]['apellidopaterno'] . ' ' . $datos[$i]['apellidomaterno'], ENT_QUOTES, 'UTF-8')), $datos[$i]['codigoalmacen'], $datos[$i]['nombremedida'], $simbolo . number_format($datos[$i]['preciofinal'], 2), $datos[$i]['cantdespacho'], $simbolo . number_format(round($datos[$i]['preciofinal'], 2) * $datos[$i]['cantdespacho'], 2));
          
            $importe+=round($datos[$i]['preciofinal'], 2) * $datos[$i]['cantdespacho'];
            $cantidadP+=$datos[$i]['cantdespacho'];
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
            $simbolo = '';
        }

        $pdf->ln();
        $pdf->Cell(110);
        $pdf->Cell(25, 5, "TOTAL x PRODUCTO", 1, 0, 'R', false);
        $pdf->Cell(20, 5, $cantidadP, 1, 0, 'R', false);
        $pdf->Cell(20, 5, 'S/ ' . number_format($importeSOLES, 2), 1, 0, 'R', false);
        $pdf->Cell(20, 5, 'US $ ' . number_format($importeDOLARES, 2), 1, 0, 'R', false);
        $pdf->AliasNbPages();
        $pdf->Output();
    }
    

    function reporteCobranzaxEmpresa() {
        set_time_limit(1000);
        $reporte = $this->AutoLoadModel('reporte');
        $ordenGasto = $this->AutoLoadModel('ordengasto');

        $idPadre = $_REQUEST['lstCategoriaPrincipal'];
        $idCategoria = $_REQUEST['lstZonaCobranza'];
        $idZona = $_REQUEST['lstZona'];
        $idVendedor = $_REQUEST['idVendedor'];
        $idCliente = $_REQUEST['idCliente'];
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $situacion = $_REQUEST['lstSituacion'];
        $fechaInicio = $_REQUEST['txtFechaInicio'];
        $fechaFin = $_REQUEST['txtFechaFin'];
        $tipoCobranza = $_REQUEST['lstTipoCobranza'];
        $idAlmacen = $_REQUEST['lstEmpresa'];
        $encabezado = $_REQUEST['encabezado'];
        if ($situacion == "cancelado" or $situacion == "anulado") {
            $situ = " and wc_ordenventa.`situacion`='$situacion' and wc_detalleordencobro.`situacion`='$situacion' ";
        } elseif ($situacion == "pendiente") {
            $situ = " and wc_ordenventa.`situacion`='$situacion' and wc_detalleordencobro.`situacion`='' ";
        } else {
            $situ = " and wc_detalleordencobro.`situacion`='' ";
        }

        if (!empty($idCliente)) {
            $filtro = " wc_cliente.`idcliente`='$idCliente' ";
        }

        $datos = $reporte->cobranzaxEmpresa($filtro, $idZona, $idPadre, $idCategoria, $idVendedor, $tipoCobranza, $fechaInicio, $fechaFinal, $situ, $idAlmacen);
        $cantidadData = count($datos);

        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $titulos = array('Codigo', 'Ven', 'F. Des.', 'Cliente', 'Total', 'Pagado', 'Devol.', 'Deuda', 'Empresa', 'M.Fac.', 'M.Bole', '%');
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(17, 8, 15, 51, 14, 14, 14, 14, 11, 14, 14, 8);
        $ancho2 = array(91, 12, 16, 14, 14, 11, 14, 22);
        $orientacion = array('C', 'C', 'C', '', 'R', 'R', 'R', 'R', 'C', 'R', 'R', 'C');
        $orientacion2 = array('', '', 'C', 'C', 'C', 'R', 'R', '');

        $pdf->SetWidths($ancho);
        $pdf->_anchoColumna = $ancho;
        $pdf->_titulos = $titulos;
        $pdf->_titulo = "Cobranza x Empresa S/.";
        $pdf->_relleno = false;
        if (!empty($idAlmacen)) {/*
            if ($idAlmacen == 4) {
                $pdf->_imagenCabezera = ROOT . 'imagenes' . DS . 'DAKKARS.jpg';
            } elseif ($idAlmacen == 8) {
                $pdf->_imagenCabezera = ROOT . 'imagenes' . DS . 'POWER-ACOUSTIK.jpg';
            } elseif ($idAlmacen == 10) {
                $pdf->_imagenCabezera = ROOT . 'imagenes' . DS . 'KAZO.jpg';
            }*/
        } else {
            $pdf->_fecha = 'Todos';
        }

        $pdf->_datoPie = date('Y-m-d');
        $pdf->AddPage();


        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $totalImporte = 0;
        $totalPagado = 0;
        $totalDevuelto = 0;
        $totalFacturado = 0;
        $totalContado = 0;
        $totalCredito = 0;
        $totalLetras = 0;
        $totalBoleta = 0;
        $idOrden = 0;
        $direccion = "";

        for ($i = 0; $i < $cantidadData; $i++) {
            if ($idOrden != $datos[$i]['idordenventa']) {
                $idOrden = $datos[$i]['idordenventa'];
                $importeOrden = round($ordenGasto->totalGuia($idOrden), 2);
                $totalImporte+=$importeOrden;
                $totalPagado+=round($datos[$i]['importepagado'], 2);
                $totalDevuelto+=round($datos[$i]['importedevuelto'], 2);
                $direccion = $datos[$i]['direccion_envio'];
                if ($datos[$i]['nombredoc'] == 1) {
                    $montoFactura = round($datos[$i]['montofacturado']);
                    $totalFacturado+=$montoFactura;
                    $montoBoleta = 0;
                } else {
                    $montoBoleta = round($datos[$i]['montofacturado']);
                    $totalBoleta+=$montoBoleta;
                    $montoFactura = 0;
                }
                if ($datos[$i]['es_letras'] == 1) {
                    $totalLetras+=$importeOrden;
                } elseif ($datos[$i]['es_credito'] == 1) {
                    $totalCredito+=$importeOrden;
                } elseif ($datos[$i]['es_contado'] == 1) {
                    $totalContado+=$importeOrden;
                }
                $pdf->_relleno = true;
                $relleno = true;
                $pdf->fill($relleno);
                $pdf->SetWidths($ancho);
                $pdf->_original = $ancho;
                $pdf->SetAligns($orientacion);
                $pdf->_orientacion = $orientacion;
                $fila = array($datos[$i]['codigov'], $datos[$i]['codigoa'], $datos[$i]['fechadespacho'], utf8_decode(html_entity_decode($datos[$i]['razonsocial'], ENT_QUOTES, 'UTF-8')), number_format($importeOrden, 2), number_format($datos[$i]['importepagado'], 2), number_format($datos[$i]['importedevolucion'], 2), number_format($importeOrden - round($datos[$i]['importepagado'], 2), 2), $datos[$i]['codigoalmacen'], number_format($montoFactura, 2), number_format($montoBoleta), $datos[$i]['porcentajefactura']);
                $pdf->Row($fila);
            }
            $pdf->_relleno = false;
            $pdf->SetWidths($ancho2);
            $pdf->_original = $ancho2;
            $pdf->_orientacion = $orientacion2;
            $pdf->SetAligns($orientacion2);
            $forma = "";
            $relleno = false;
            $pdf->fill($relleno);
            if ($datos[$i]['formacobro'] == 1) {
                $forma = 'Contado';
            } elseif ($datos[$i]['formacobro'] == 2) {
                $forma = "Credito";
            } elseif ($datos[$i]['formacobro'] == 3) {
                $forma = "Letras";
            }
            $fila = array($direccion, $forma, $datos[$i]['numeroletra'], $datos[$i]['fechagiro'], $datos[$i]['fvencimiento'], number_format($datos[$i]['importedoc'], 2), number_format($datos[$i]['saldodoc'], 2), empty($datos[$i]['situacion']) ? 'Pendiente' : $datos[$i]['situacion']);
            $direccion = "";
            $pdf->Row($fila);
        }

        $pdf->ln();

        $pdf->Cell(25, 5, "Total Guia", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalImporte, 2), 1, 0, 'R', false);

        $pdf->Cell(25, 5, "Total Pagado", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalPagado, 2), 1, 0, 'R', false);

        $pdf->Cell(25, 5, "Total Devuelto", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalDevuelto, 2), 1, 0, 'R', false);

        $pdf->Cell(20, 5, "Total Deuda", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalImporte - $totalPagado, 2), 1, 0, 'R', false);

        $pdf->ln();

        $pdf->Cell(25, 5, "Total Factura", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalFacturado, 2), 1, 0, 'R', false);

        $pdf->Cell(25, 5, "Total Boleta", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalBoleta, 2), 1, 0, 'R', false);

        $pdf->Cell(25, 5, "Total Contado", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalContado, 2), 1, 0, 'R', false);

        $pdf->Cell(20, 5, "Total Credito", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalCredito, 2), 1, 0, 'R', false);

        $pdf->ln();

        $pdf->Cell(25, 5, "Total Letras", 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($totalLetras, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->ln();

        $pdf->SetAligns('', '', '', '', '', '', '', '');
        $pdf->SetWidths(array(25, 25, 25, 25, 25, 25, 20, 25));
        $fila = array('Zona Geografica', $encabezado['categoriaPrincipal'], 'Zona Cobranza', $encabezado['zonaCobranza'], 'Zona', $encabezado['zona'], 'T.Cobranza', $encabezado['tipoCobranza']);
        $pdf->Row($fila);

        $pdf->SetAligns('', '', '', '', '', '');
        $fila = array('Vendedor', $encabezado['vendedor'], 'Cliente', $encabezado['cliente'], 'Orden Venta', $encabezado['ordenVenta'], 'Situacion', $encabezado['situacion']);
        $pdf->Row($fila);

        $pdf->SetAligns('', '', '', '', '', '');
        $fila = array();
        $pdf->Row($fila);

        $pdf->Cell(25, 5, "Rango Fechas", 1, 0, 'L', false);
        $pdf->Cell(50, 5, $encabezado[fecha], 1, 0, 'R', false);

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function rankingVendedor() {
        set_time_limit(1500);
        $fecha = $_REQUEST['fecha'];
        if (!empty($_REQUEST['txtFechaAprobadoInicio'])) {
            $txtFechaAprobadoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaAprobadoInicio']));
        }

        if (!empty($_REQUEST['txtFechaAprobadoFinal'])) {
            $txtFechaAprobadoFinal = date('Y-m-d', strtotime($_REQUEST['txtFechaAprobadoFinal']));
        }
        if (!empty($_REQUEST['txtFechaGuiadoInicio'])) {
            $txtFechaGuiadoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaGuiadoInicio']));
        }

        if (!empty($_REQUEST['txtFechaGuiadoFin'])) {
            $txtFechaGuiadoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaGuiadoFin']));
        }

        if (!empty($_REQUEST['txtFechaDespachoInicio'])) {
            $txtFechaDespachoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaDespachoInicio']));
        }

        if (!empty($_REQUEST['txtFechaDespachoFin'])) {
            $txtFechaDespachoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaDespachoFin']));
        }

        if (!empty($_REQUEST['txtFechaCanceladoInicio'])) {
            $txtFechaCanceladoInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaCanceladoInicio']));
        }

        if (!empty($_REQUEST['txtFechaCanceladoFin'])) {
            $txtFechaCanceladoFin = date('Y-m-d', strtotime($_REQUEST['txtFechaCanceladoFin']));
        }

        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $idCliente = $_REQUEST['idCliente'];
        $idVendedor = $_REQUEST['idVendedor'];
        $idpadre = $_REQUEST['idpadre'];
        $idcategoria = $_REQUEST['idcategoria'];
        $idzona = $_REQUEST['idzona'];
        $condicion = $_REQUEST['condicion'];
        $aprobados = $_REQUEST['aprobados'];
        $desaprobados = $_REQUEST['desaprobados'];
        $pendiente = $_REQUEST['pendiente'];
        $idmoneda = $_REQUEST['idmoneda'];
        //echo "moneda: ".$idmoneda."--/";
        $simbolomoneda = $idmoneda == 1 ? "S/" : "US $";



        //echo $fecha."-".$txtFechaAprobadoInicio."-".$txtFechaAprobadoFinal."-".$txtFechaGuiadoInicio."-".$txtFechaGuiadoFin;
        //echo "/".$idOrdenventa."/";

        $condicionVenta = "";
        if ($condicion == 1) {
            $condicionVenta = " and ov.es_contado='1' and ov.es_credito!='1' and ov.es_letras!='1' ";
        } elseif ($condicion == 2) {
            $condicionVenta = " and ov.es_credito='1' and ov.es_letras!='1' ";
        } elseif ($condicion == 3) {
            $condicionVenta = "  and ov.es_letras='1' and  ov.tipo_letra=1";
        } elseif ($condicion == 4) {
            $condicionVenta = "  and ov.es_letras='1' and ov.tipo_letra=2";
        }

        $reporte = $this->AutoLoadModel('reporte');
        $categoria = $this->AutoLoadModel('zona');
        $data = $reporte->rankingVendedor($txtFechaAprobadoInicio, $txtFechaAprobadoFinal, $txtFechaGuiadoInicio, $txtFechaGuiadoFin, $txtFechaDespachoInicio, $txtFechaDespachoFin, $txtFechaCanceladoInicio, $txtFechaCanceladoFin, $idOrdenVenta, $idCliente, $idVendedor, $idpadre, $idcategoria, $idzona, $condicionVenta, $aprobados, $desaprobados, $pendiente, $idmoneda);
        //var_dump($data);
        // echo "<pre>";
        // print_r($data);
        // exit;

        if (!empty($idpadre)) {
            $dataCategoria = $categoria->buscaCategoria($idpadre);
        }

        $cantidad = count($data);
        $dataReporte = array();
        $idVendedor = 0;
        $importeContado = 0;
        $totalContado = 0;
        $importeCredito = 0;
        $totalCredito = 0;
        $importeLetraB = 0;
        $totalLetraB = 0;
        $importeLetraC = 0;
        $totalLetraC = 0;
        $importePagado = 0;
        $subTotal = 0;
        $stfinal = 0;
        $importeTotal = 0;
        $totalPagado = 0;
        $importeLima = 0;
        $importeAnulado = 0;
        $importeDevolucion = 0;
        $totalDevolucion = 0;
        $importeProvincia = 0;
        $cont = 0;
        $totalGuias = 0;
        $num = 0;
        $contIdOv = 0;
        $arraysIdOv = array();
        $detalleordencobro = new detalleOrdenCobro();
        //$tipoCambio=$this->configIni($this->configIni('Globals','Modo'),'TipoCambio');

        //echo $cantidad."/";
        //echo "cantidad:".$cantidad."<br>";
        for ($i = 0; $i <= $cantidad; $i++) { // se le agrego el <=
            //$ordenGasto=$this->AutoLoadModel('ordengasto');
            //$importeov = $ordenGasto->totalGuia($data[$i]['total']);
            //echo "idVendedor: ".$data[$i]['idvendedor']."==".$idVendedor."<br>";
            if ($data[$i]['idvendedor'] != $idVendedor || $i == ($cantidad)) {
                //echo "i".$i."<br>";
                if ($i != ($cantidad+1)) { // se le agrego el +1
                    //echo "i.".$i."<br>";
                    //echo "uno<br>";
                    if ($i != 0) {
                        //echo "<hr><hr>vendedor: " . $nombres . " :::: <<br>";
                        $resultadoTotal = $this->concatenerIddetalleordencobro($idmoneda, $arraysIdOv);
                        //$dataDocs = $detalleordencobro->listadoxiddetalleordencobro($textIDOC);
                        /*$tamanioDocs = count($dataDocs);
                        for($idocs = 0; $idocs < $tamanioDocs; $idocs++) {
                            
                        }*/
                        unset($arraysIdOv);
                        $contIdOv = 0;
                        //echo "N:".$num."<br>";
                        //echo "ENTRA:<br>";
                        //var_dump($dataReporte);
                        $dataReporte[$num]['idvendedor'] = $idVendedor;
                        $dataReporte[$num]['importecontado'] = $resultadoTotal[1];
                        $dataReporte[$num]['importecredito'] = $resultadoTotal[2];
                        $dataReporte[$num]['importeletrab'] = $resultadoTotal[3];
                        $dataReporte[$num]['importeletrac'] = $importeLetraC;
                        $dataReporte[$num]['importepagado'] = $importePagado;
                        $dataReporte[$num]['importedevolucion'] = $importeDevolucion;
                        $dataReporte[$num]['subtotal'] = $subTotal;
                        $dataReporte[$num]['stfinal'] = $stfinal;
                        $dataReporte[$num]['cantidadguia'] = $cont;
                        $dataReporte[$num]['vendedor'] = $nombres;
                        $num++;
                        
                        $totalContado += $resultadoTotal[1];
                        $totalCredito += $resultadoTotal[2];
                        $totalLetraB += $resultadoTotal[3];
        
                        $importeContado = 0;
                        $importeCredito = 0;
                        $importeLetraB = 0;
                        $importeLetraC = 0;
                        $importePagado = 0;
                        $importeDevolucion = 0;
                        $subTotal = 0;
                        $stfinal = 0;
                        $cont = 0;
                        
                        //echo "SALE:<br>";
                        //var_dump($dataReporte);

                    }
                    $idVendedor = $data[$i]['idvendedor'];
                    $nombres = $data[$i]['nombres'] . ' ' . $data[$i]['apellidopaterno'] . ' ' . $data[$i]['apellidomaterno'];
                }
            }

            //calcular simbolo y importes:
            if ($i != $cantidad) {
                $arraysIdOv[$contIdOv]['idordenventa'] = $data[$i]['idordenventa'];
                $arraysIdOv[$contIdOv]['fordenventa'] = $data[$i]['fordenventa'];
                $arraysIdOv[$contIdOv]['idmoneda'] = $data[$i]['idmoneda'];
                $arraysIdOv[$contIdOv]['importeov'] = $data[$i]['importeov'];
                $contIdOv++;
                //calcula el simbolo y total
                /*
                if ($data[$i]['es_letras'] == 1 && $data[$i]['tipo_letra'] == 1) {
                    $importeLetraB+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                    $totalLetraB+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                } else if ($data[$i]['es_letras'] == 1 && $data[$i]['tipo_letra'] == 2) {
                    $importeLetraC+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                    $totalLetraC+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                } else if ($data[$i]['es_credito'] == 1) {
                    $importeCredito+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                    $totalCredito+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                } else if ($data[$i]['es_contado'] == 1) {
                    $importeContado+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                    $totalContado+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                }*/

                /*if ($data[$i]['idpadrec'] == 1) {
                    $importeProvincia+=$data[$i]['importeov'];
                } else if ($data[$i]['idpadrec'] == 2) {
                    $importeLima+=$data[$i]['importeov'];
                }
                if ($data[$i]['esanulado'] == 1) {
                    $importeAnulado+=$data[$i]['importeov'];
                }*/
                $subTotal+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                $stfinal+=$data[$i]['total'];

                $totalGuias++;
                $totalDevolucion+=$data[$i]['importedevolucion'];
                $importeDevolucion+=$data[$i]['importedevolucion'];
                $totalPagado+=($data[$i]['importepagado'] > $data[$i]['importeov']) ? $data[$i]['importeov'] : $data[$i]['importepagado'];
                $importePagado+=($data[$i]['importepagado'] > $data[$i]['importeov']) ? $data[$i]['importeov'] : $data[$i]['importepagado'];
                $importeTotal+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                $cont++;
            }
        }

        //var_dump($dataReporte);

        $cantidadReporte = count($dataReporte);
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array(utf8_decode('Nº'), 'Vendedor', 'VTA. NETA', '%', 'CONTADO', '%', 'L.BANCO', '%', 'L.CARTERA', '%', 'CREDITO', '%', 'Devolucion', 'Pagado', 'Pendiente', 'Guias');
        $ancho = array(7, 55, 20, 9, 20, 9, 20, 9, 20, 9, 20, 9, 20, 20, 20, 10);
        $orientacionTitulos = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $orientacion = array('', '', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'C');
        $pdf->_titulo = "Ranking de Ventas " . $simbolomoneda;
        $pdf->_fecha = $dataCategoria[0]['nombrec'] . ' (' . $_REQUEST['txtFechaGuiadoInicio'] . ' - ' . $_REQUEST['txtFechaGuiadoFin'] . ') ';
        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->fill($relleno);
        $pdf->SetWidths($ancho);
        $pdf->SetAligns($orientacionTitulos);

        $fila = $titulos;
        $pdf->Row($fila);
        $relleno = !$relleno;
        $pdf->fill($relleno);
        $pdf->SetAligns($orientacion);
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $dataReporte = $this->ordernarArray($dataReporte, "subtotal", true);
        //var_dump($dataReporte);

        $totaldeuda = 0;
        for ($i = 0; $i < $cantidadReporte; $i++) {
            $deuda = $dataReporte[$i]['subtotal'] - $dataReporte[$i]['importedevolucion'] - $dataReporte[$i]['importepagado'];
            $totaldeuda += $deuda;
            $fila = array(($i + 1), $dataReporte[$i]['vendedor'], number_format($dataReporte[$i]['subtotal'], 2), $importeTotal == 0 ? 0 : round($dataReporte[$i]['subtotal'] * 100 / $importeTotal, 2), number_format($dataReporte[$i]['importecontado'], 2), $totalContado == 0 ? 0 : round($dataReporte[$i]['importecontado'] * 100 / $totalContado, 2), number_format($dataReporte[$i]['importeletrab'], 2), $totalLetraB == 0 ? 0 : round($dataReporte[$i]['importeletrab'] * 100 / $totalLetraB, 2), number_format($dataReporte[$i]['importeletrac'], 2), $totalLetraC == 0 ? 0 : round($dataReporte[$i]['importeletrac'] * 100 / $totalLetraC, 2), number_format($dataReporte[$i]['importecredito'], 2), $totalCredito == 0 ? 0 : round($dataReporte[$i]['importecredito'] * 100 / $totalCredito, 2), number_format($dataReporte[$i]['importedevolucion'], 2), number_format($dataReporte[$i]['importepagado'], 2), number_format($deuda, 2), $dataReporte[$i]['cantidadguia']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $relleno = false;
        $pdf->fill($relleno);
        $fila = array("", "TOTALES EN " . $simbolomoneda . " ", number_format($importeTotal, 2), 100, number_format($totalContado, 2), 100, number_format($totalLetraB, 2), 100, number_format($totalLetraC, 2), 100, number_format($totalCredito, 2), 100, number_format($totalDevolucion, 2), number_format($totalPagado, 2), number_format($totaldeuda, 2), $totalGuias);
        $pdf->Row($fila);

        // $relleno=false;
        // $pdf->fill($relleno);
        // $fila=array("","TOTALES EN $.(".$tipoCambio.")",number_format($importeTotal/$tipoCambio,2),100,number_format($totalContado/$tipoCambio,2),100,number_format($totalLetraB/$tipoCambio,2),100,number_format($totalLetraC/$tipoCambio,2),100,number_format($totalCredito/$tipoCambio,2),100,number_format($totalDevolucion/$tipoCambio,2),number_format($totalPagado/$tipoCambio,2),number_format(($importeTotal-$totalPagado)/$tipoCambio,2),$totalGuias);
        // $pdf->Row($fila);

        $pdf->AliasNbPages();
        $pdf->Output();
    }
    
    

function concatenerIddetalleordencobro($idmoneda, $array_ovs = array()) {

        $resultadoTotal[0] = 0;
        $resultadoTotal[1] = 0;
        $resultadoTotal[2] = 0;
        $resultadoTotal[3] = 0;
        if ($idmoneda != -1) {            
            $detalleordencobro = new detalleOrdenCobro();
            $tipocambio = new TipoCambio();
            foreach ($array_ovs as $value) {                
                //$array_iddetalleordencobro = '';
                $diferencia = 0;
                $i = 0;
                //$lista_tope_venta = $detalleordencobro->lista_tope_venta($value['idordenventa']);
                if ($idmoneda == $value['idmoneda']) {
                    $tipocambiovigente = 0;
                    $total_tope_venta = $value['importeov']; 
                } else {
                    $tipocambiovigente = $tipocambio->consultatipocambioXfecha($value['fordenventa']);
                    if ($idmoneda == 1 && $value['idmoneda'] == 2) {
                        $total_tope_venta = $value['importeov']; 
                        $tipocambiovigente=$tipocambiovigente*-1;
                    }
                    if ($idmoneda == 2 && $value['idmoneda'] == 1) {
                        $total_tope_venta = $value['importeov'];
                    }
                }
                
                $lista_info_cobro = $detalleordencobro->lista_info_cobro($value['idordenventa']);
                $tamnioInfoCobro = count($lista_info_cobro);
                //echo "<br><br>Id Ordenventa: " . $value['idordenventa'] . " ==>imp. aprobado: " . $total_tope_venta .  " Moneda:" . $value['idmoneda'] . " [[[Tmño: " . $tamnioInfoCobro .  "<br>";
                $total_pulpa_venta = 0;
                $ultimaformacobro = 0;
                while ($total_pulpa_venta < $total_tope_venta && $i < $tamnioInfoCobro) {
                    if ($tipocambiovigente < 0) {
                        $lista_info_cobro[$i]['importedoc'] = $lista_info_cobro[$i]['importedoc']*$tipocambiovigente*(-1);
                    } else if ($tipocambiovigente > 0) {
                        $lista_info_cobro[$i]['importedoc'] = $lista_info_cobro[$i]['importedoc']/$tipocambiovigente;
                    } else {
                        $lista_info_cobro[$i]['importedoc'] = $lista_info_cobro[$i]['importedoc'];
                    }                    
                    $total_pulpa_venta = $lista_info_cobro[$i]['importedoc'] + $total_pulpa_venta;
                    //echo $lista_info_cobro[$i]['importedoc'] . " ||| Acumulado: " . $total_pulpa_venta . "";
                    if ($total_tope_venta > $total_pulpa_venta) {
                        $diferencia = $total_tope_venta - $total_pulpa_venta;
                    }
                    if ($total_pulpa_venta > $total_tope_venta) {
                        $diferencia = $total_pulpa_venta - $total_tope_venta;
                    }
                    if ($diferencia >= 0 and $diferencia <= 0.09) {
                        if ($total_tope_venta > $total_pulpa_venta) {
                            $total_pulpa_venta = $total_tope_venta;
                        }
                        if ($total_pulpa_venta > $total_tope_venta) {
                            $total_tope_venta = $total_pulpa_venta;
                        }
                        //echo " [IGUAL]";
                    }/*
                    if ($i == 20) {
                        echo " => [LLEGO AL 20 Y NADA]";
                    }
                    echo "<br>";*/
                    $resultadoTotal[$lista_info_cobro[$i]['formacobro']] += $lista_info_cobro[$i]['importedoc'];
                    $ultimaformacobro = $lista_info_cobro[$i]['formacobro'];
                    //$array_iddetalleordencobro[] = array("iddetalleordencobro" => $lista_info_cobro[$i]['iddetalleordencobro']);
                    $i++;
                }
                $resultadoTotal[0] += $total_tope_venta;/*
                if ($total_pulpa_venta < $total_tope_venta) {
                    echo " => [[[[[LLEGO AL FINAL Y NADA DE NADA]]]]] <br><br>";
                }*/
                if ($total_pulpa_venta != $total_tope_venta && $ultimaformacobro != 0) {
                    //$total_tope_venta = $total_tope_venta + $lista_tope_venta[1]['pulpa'];
                    if ($total_tope_venta > $total_pulpa_venta) {
                        $diferencia = $total_tope_venta - $total_pulpa_venta;
                        $resultadoTotal[$ultimaformacobro] = $resultadoTotal[$ultimaformacobro] + $diferencia;
                        //echo "Diferencia Mayor: " . $diferencia. " => " . ($total_pulpa_venta + $diferencia);
                    } else {
                        $diferencia = $total_pulpa_venta - $total_tope_venta;
                        $resultadoTotal[$ultimaformacobro] = $resultadoTotal[$ultimaformacobro] - $diferencia;
                        //echo "Diferencia Menor: " . $diferencia . " => " . ($total_pulpa_venta - $diferencia);
                    }
                    
                     /*
                      if ($diferencia >= 0 and $diferencia <= 0.09) {
                      if ($total_tope_venta > $total_pulpa_venta) {
                      $total_pulpa_venta = $total_tope_venta;
                      }
                      if ($total_pulpa_venta > $total_tope_venta) {
                      $total_tope_venta = $total_pulpa_venta;
                      }
                      } */
                }/*
                  if ($total_tope_venta == $total_pulpa_venta) {
                  foreach ($array_iddetalleordencobro as $v) {
                  $iddetalleordencobro = $v['iddetalleordencobro'];
                  $cadena .= $iddetalleordencobro . ', ';
                  }
                  } */
            }
        }/*
        echo "<hr><hr><table border='1' stule='padding: 15px'>
                <tbody stule='padding: 15px'>
                    <tr stule='padding: 15px'>
			<th stule='padding: 15px'>" .$resultadoTotal [1] . "</th>
			<th stule='padding: 15px'>" .$resultadoTotal [2] . "</th>
			<th stule='padding: 15px'>" .$resultadoTotal [3] . "</th>
			<th stule='padding: 15px'>" .$resultadoTotal [0] . "</th>

		</tr>
            </tbody></table></hr></hr>";*/
        return $resultadoTotal;
    }

    function cuadroavance() {
        //echo "CUADRO DE AVANCE";
        set_time_limit(500);
        $fecha = $_REQUEST['fecha'];
        if (!empty($_REQUEST['txtFechaInicio'])) {
            $txtFechaInicio = date('Y-m-d', strtotime($_REQUEST['txtFechaInicio']));
        }

        if (!empty($_REQUEST['txtFechaFinal'])) {
            $txtFechaFinal = date('Y-m-d', strtotime($_REQUEST['txtFechaFinal']));
        }

        $idVendedor = $_REQUEST['idVendedor'];
        //$idmoneda = $_REQUEST['idmoneda'];
        $idmoneda = 2;//en dolares
        $idmes = $_REQUEST['lstMes'];
        $simbolomoneda = $idmoneda == 1 ? "S/" : "US $";

//        $idpadre = $_REQUEST['idpadre'];
//        $idcategoria = $_REQUEST['idcategoria'];
//        $idzona = $_REQUEST['idzona'];
//        $condicion = $_REQUEST['condicion'];
//        $aprobados = $_REQUEST['aprobados'];
//        $desaprobados = $_REQUEST['desaprobados'];
//        $pendiente = $_REQUEST['pendiente'];
        //echo "moneda: ".$idmoneda."--/";

//        echo "fecha Inicio: ".$txtFechaInicio." - fecha Fin: ".$txtFechaFinal." - moneda: ".$idmoneda." - simbolo: ".$simbolomoneda;
//        echo "<br> vendedor:".$idVendedor;
        //echo $fecha."-".$txtFechaAprobadoInicio."-".$txtFechaAprobadoFinal."-".$txtFechaGuiadoInicio."-".$txtFechaGuiadoFin;
        //echo "/".$idOrdenventa."/";

//        $condicionVenta = "";
//        if ($condicion == 1) {
//            $condicionVenta = " and ov.es_contado='1' and ov.es_credito!='1' and ov.es_letras!='1' ";
//        } elseif ($condicion == 2) {
//            $condicionVenta = " and ov.es_credito='1' and ov.es_letras!='1' ";
//        } elseif ($condicion == 3) {
//            $condicionVenta = "  and ov.es_letras='1' and  ov.tipo_letra=1";
//        } elseif ($condicion == 4) {
//            $condicionVenta = "  and ov.es_letras='1' and ov.tipo_letra=2";
//        }

        $reporte = $this->AutoLoadModel('reporte');
        $categoria = $this->AutoLoadModel('zona');
        $data = $reporte->cuadroavance($txtFechaAprobadoInicio, $txtFechaAprobadoFinal, $txtFechaInicio, $txtFechaFin, $txtFechaDespachoInicio, $txtFechaDespachoFin, $txtFechaCanceladoInicio, $txtFechaCanceladoFin, $idOrdenVenta, $idCliente, $idVendedor, $idpadre, $idcategoria, $idzona, $condicionVenta, $aprobados, $desaprobados, $pendiente, $idmoneda);
        //var_dump($data);
        // echo "<pre>";
        // print_r($data);
        // exit;

        if (!empty($idpadre)) {
            $dataCategoria = $categoria->buscaCategoria($idpadre);
        }

        $cantidad = count($data);
        $dataReporte = array();
        $idVendedor = 0;
        $importeContado = 0;
        $totalContado = 0;
        $importeCredito = 0;
        $totalCredito = 0;
        $importeLetraB = 0;
        $totalLetraB = 0;
        $importeLetraC = 0;
        $totalLetraC = 0;
        $importePagado = 0;
        $subTotal = 0;
        $stfinal = 0;
        $importeTotal = 0;
        $totalPagado = 0;
        $importeLima = 0;
        $importeAnulado = 0;
        $importeDevolucion = 0;
        $totalDevolucion = 0;
        $importeProvincia = 0;
        $cont = 0;
        $totalGuias = 0;
        $num = 0;
        //$tipoCambio=$this->configIni($this->configIni('Globals','Modo'),'TipoCambio');

        //echo $cantidad."/";
        //echo "cantidad:".$cantidad."<br>";
        for ($i = 0; $i <= $cantidad; $i++) { // se le agrego el <=
            //$ordenGasto=$this->AutoLoadModel('ordengasto');
            //$importeov = $ordenGasto->totalGuia($data[$i]['total']);
            //echo "idVendedor: ".$data[$i]['idvendedor']."==".$idVendedor."<br>";
            if ($data[$i]['idvendedor'] != $idVendedor || $i == ($cantidad)) {
                //echo "i".$i."<br>";
                if ($i != ($cantidad+1)) { // se le agrego el +1
                    //echo "i.".$i."<br>";
                    //echo "uno<br>";
                    if ($i != 0) {
                        //echo "N:".$num."<br>";
                        //echo "ENTRA:<br>";
                        //var_dump($dataReporte);
                        $dataReporte[$num]['idvendedor'] = $idVendedor;
                        $dataReporte[$num]['importecontado'] = $importeContado;
                        $dataReporte[$num]['importecredito'] = $importeCredito;
                        $dataReporte[$num]['importeletrab'] = $importeLetraB;
                        $dataReporte[$num]['importeletrac'] = $importeLetraC;
                        $dataReporte[$num]['importepagado'] = $importePagado;
                        $dataReporte[$num]['importedevolucion'] = $importeDevolucion;
                        $dataReporte[$num]['subtotal'] = $subTotal;
                        $dataReporte[$num]['stfinal'] = $stfinal;
                        $dataReporte[$num]['cantidadguia'] = $cont;
                        $dataReporte[$num]['vendedor'] = $nombres;
                        $num++;

                        $importeContado = 0;
                        $importeCredito = 0;
                        $importeLetraB = 0;
                        $importeLetraC = 0;
                        $importePagado = 0;
                        $importeDevolucion = 0;
                        $importePagado = 0;
                        $subTotal = 0;
                        $stfinal = 0;
                        $cont = 0;
                        //echo "SALE:<br>";
                        //var_dump($dataReporte);

                    }
                    $idVendedor = $data[$i]['idvendedor'];
                    $nombres = $data[$i]['nombres'] . ' ' . $data[$i]['apellidopaterno'] . ' ' . $data[$i]['apellidomaterno'];
                }
            }

            //calcular simbolo y importes:
            if ($i != $cantidad) {
                //calcula el simbolo y total
                if ($data[$i]['es_letras'] == 1 && $data[$i]['tipo_letra'] == 1) {
                    $importeLetraB+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                    $totalLetraB+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                } else if ($data[$i]['es_letras'] == 1 && $data[$i]['tipo_letra'] == 2) {
                    $importeLetraC+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                    $totalLetraC+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                } else if ($data[$i]['es_credito'] == 1) {
                    $importeCredito+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                    $totalCredito+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                } else if ($data[$i]['es_contado'] == 1) {
                    $importeContado+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                    $totalContado+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                }

                /*if ($data[$i]['idpadrec'] == 1) {
                    $importeProvincia+=$data[$i]['importeov'];
                } else if ($data[$i]['idpadrec'] == 2) {
                    $importeLima+=$data[$i]['importeov'];
                }
                if ($data[$i]['esanulado'] == 1) {
                    $importeAnulado+=$data[$i]['importeov'];
                }*/
                $subTotal+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                $stfinal+=$data[$i]['total'];

                $totalGuias++;
                $totalDevolucion+=$data[$i]['importedevolucion'];
                $importeDevolucion+=$data[$i]['importedevolucion'];
                $totalPagado+=($data[$i]['importepagado'] > $data[$i]['importeov']) ? $data[$i]['importeov'] : $data[$i]['importepagado'];
                $importePagado+=($data[$i]['importepagado'] > $data[$i]['importeov']) ? $data[$i]['importeov'] : $data[$i]['importepagado'];
                $importeTotal+=$data[$i]['importeov']-$data[$i]['importedevolucion'];
                $cont++;
            }
        }

        //var_dump($dataReporte);

        $cantidadReporte = count($dataReporte);
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array(utf8_decode('Nº'),utf8_decode('Línea'),'Vendedor', 'CUOTA.$', 'AVANCE', 'DIFERENCIA', '% DE DIFERENCIA', 'AVANCE EN %', 'DIAS HABILES DEL MES','DIAS AVANZADOS','DIAS QUE FALTA', 'CUOTADIARIA X CUMPLIR');
        $ancho = array(7,30,55, 20, 20, 20, 15, 15, 15, 20, 15, 30);
        $orientacionTitulos = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $orientacion = array('', '', '', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'C');
        $pdf->_titulo = "CUADRO DE AVANCE " . $simbolomoneda;
        $pdf->_fecha = $dataCategoria[0]['nombrec'] . ' (' . $_REQUEST['txtFechaInicio'] . ' - ' . $_REQUEST['txtFechaFin'] . ') ';
        $pdf->AddPage();

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->fill($relleno);
        $pdf->SetWidths($ancho);
        $pdf->SetAligns($orientacionTitulos);

        $fila = $titulos;
        $pdf->Row($fila);
        $relleno = !$relleno;
        $pdf->fill($relleno);
        $pdf->SetAligns($orientacion);
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $dataReporte = $this->ordernarArray($dataReporte, "subtotal", true);
        //var_dump($dataReporte);

        $totaldeuda = 0;
        for ($i = 0; $i < $cantidadReporte; $i++) {
            //Si la linea cambia se muestra una fila con el resultado de la anterior linea.

            //se calcula los demas columnas
            $linea = "AUTOPARTES";
            $cuota = 50000.00;//$dataReporte[$i]['cuota'];
            $diferencia = $cuota - $dataReporte[$i]['subtotal'];
            $porcAvance = ($dataReporte[$i]['subtotal']/$cuota)*100;
            $porcDiferencia = ($diferencia/$cuota)*100;
            $diasHabiles = 26;//obtener de la base de datos
            $diasAvance = 3;//number_format(data('d'));
            $diasFaltan = $diasHabiles-$diasAvance;
            $cuotaTotal += $cuota;

            $deuda = $dataReporte[$i]['stfinal'] - $dataReporte[$i]['importepagado'];
            $totaldeuda += $deuda;
            $fila = array(($i + 1),$linea, $dataReporte[$i]['vendedor'],number_format($cuota,2 ),number_format($dataReporte[$i]['subtotal'],2),$diferencia,utf8_decode(round($porcDiferencia,2)."%"), utf8_decode(round($porcAvance,2)."%"),$diasHabiles,$diasAvance,$diasFaltan,$diferencia/$diasFaltan);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $relleno = false;
        $pdf->fill($relleno);
        $fila = array("", "", "TOTALES EN " . $simbolomoneda . " ", number_format($cuotaTotal, 2), 100, number_format($totalContado, 2), 100, number_format($totalLetraB, 2), 100, number_format($totalLetraC, 2), 100, number_format($totalCredito, 2), 100, number_format($totalDevolucion, 2), number_format($totalPagado, 2), number_format($totaldeuda, 2), $totalGuias);
        $pdf->Row($fila);

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteUtilidadesComision() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];

        $detalleOrdenVenta = $this->AutoLoadModel('detalleordenventa');
        $ordenVenta = $this->AutoLoadModel('ordenventa');
        $datos = $detalleOrdenVenta->listaDetalleProductos($idOrdenVenta);
        // echo "<pre>";
        // print_r($datos);
        // exit;
        $dataOrden = $ordenVenta->buscarOrdenVentaxId($idOrdenVenta);

        $idmoneda = $dataOrden[0]['IdMoneda'];
        $cantidadData = count($datos);

        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $titulos = array('CODIGO', 'DESCRIPCION', 'CIF', 'P.LISTA', 'DESC.', 'P.NETO', 'UTILIDAD');
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(25, 92, 15, 15, 15, 15, 15);



        $pdf->SetWidths($ancho);

        $pdf->_titulo = "Reporte Utilidades Comision x Orden Venta";
        $pdf->_datoPie = date('Y-m-d');
        $pdf->AddPage();
        $pdf->_titulos = $titulos;


        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $utilidad = 0;

        $pdf->Cell(25, 5, "Orden Venta", 1, 0, 'C', true);
        $pdf->Cell(25, 5, $dataOrden[0]['codigov'], 1, 0, 'R', false);
        $pdf->Cell(25, 5, "Fecha de Pedido", 1, 0, 'C', true);
        $pdf->Cell(25, 5, $dataOrden[0]['fordenventa'], 1, 0, 'R', false);
        $pdf->Cell(25, 5, "Importe OV", 1, 0, 'C', true);
        $pdf->Cell(25, 5, $dataOrden[0]['Simbolo'] . ' ' . $dataOrden[0]['importeov'], 1, 0, 'R', false);
        $pdf->ln();
        $pdf->ln();



        for ($i = 0; $i < $cantidadData; $i++) {
            if ($i == 0) {
                $orientacion = array('C', 'C', 'C', 'C', 'C', 'C', 'C');
                $pdf->SetAligns($orientacion);
                $pdf->SetFillColor(202, 232, 234);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetTextColor(12, 78, 139);
                $fila = $titulos;
                $pdf->Row($fila);
                $pdf->SetFillColor(0224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetDrawColor(12, 78, 139);
                $orientacion = array('', '', 'R', 'R', '', 'R', 'R');
                $pdf->SetAligns($orientacion);
            }
            $preciofinal = empty($datos[$i]['preciofinal']) ? 0 : round($datos[$i]['preciofinal'], 2);

            $cif = ($idmoneda == 1) ? ($datos[$i]['cifventas']) : ($datos[$i]['cifventasdolares']);
            $preciolista = ($idmoneda == 1) ? $datos[$i]['preciolista'] : $datos[$i]['preciolistadolares'];
            if ($cif > 0) {
                $utilidad = (round((($preciofinal - $cif) / $cif) * 100, 2));
            } else {
                $utilidad = 0;
            }

            $fila = array(utf8_decode(html_entity_decode($datos[$i]['codigopa'], ENT_QUOTES, 'UTF-8')), utf8_decode(html_entity_decode($datos[$i]['nompro'], ENT_QUOTES, 'UTF-8')), number_format($cif, 2), number_format($preciolista, 2), $datos[$i]['descuentoaprobadotexto'], number_format($datos[$i]['preciofinal'], 2), number_format($utilidad, 2) . '%');
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function letraspendientes(){
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $ancho = array(18, 72, 40, 45, 10, 20, 18, 18, 13, 25);
        $orientacion = array('C', '', '', '', 'R', 'R', 'C', 'R', 'R');
        $pdf->_titulo = date('d/m/Y');
        $pdf->AddPage();

        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill(true);

        $pdf->SetWidths($ancho);
        $pdf->SetAligns($orientacion);
        $pdf->Cell(277, 6, "REPORTE - LETRAS PENDIENTES", 1, 0, 'C', true);
        $pdf->Ln();
        $pdf->Cell(46, 6, "LETRA", 1, 0, 'C', true);
        $pdf->Cell(50, 6, "MONTO", 1, 0, 'C', true);
        $pdf->Cell(50, 6, "SALDO", 1, 0, 'C', true);
        $pdf->Cell(65, 6, "FECHA GIRO", 1, 0, 'C', true);
        $pdf->Cell(66, 6, "FECHA VENCIMIENTO", 1, 0, 'C', true);
        $pdf->Ln();
        set_time_limit(500);
        session_start();
        if(!empty($msg)) $data['msg'] = $msg;

        $dataGuia = $this->AutoLoadModel("OrdenVenta");
        $ov = $dataGuia->listarLetrasPendientes(1, 1);
        $total = count($ov);
        $resp = "";
        $temp = "";
        for ($i=0; $i < $total; $i++) {
            if(strcmp($ov[$i]['codigov'], $temp) != 0) {
                if($i > 0) $pdf->Ln();

                $pdf->Ln();
                $pdf->SetFillColor(198, 220, 249);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');

                $pdf->Cell(46, 4, "Cliente", 1, 0, 'C', true);
                $pdf->SetFillColor(224, 238, 255);
                $pdf->Cell(100, 4, "[" . $ov[$i]['codantiguo']."] ".$ov[$i]['razonsocial'], 1, 0, 'C', true);
                $pdf->SetFillColor(198, 220, 249);
                $pdf->Cell(65, 4, "Orden de Venta", 1, 0, 'C', true);
                $pdf->SetFillColor(224, 238, 255);
                $pdf->Cell(66, 4, $ov[$i]['codigov'], 1, 0, 'C', true);
                $temp = $ov[$i]['codigov'];

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
            }
            $pdf->Ln();
            $pdf->Cell(46, 4, $ov[$i]['numeroletra'], 1, 0, 'C', true);
            $pdf->Cell(50, 4, $ov[$i]['simbolo'] . ' ' . number_format($ov[$i]['importedoc'], 2), 1, 0, 'C', true);
            $pdf->Cell(50, 4, $ov[$i]['simbolo'] . ' ' . number_format($ov[$i]['saldodoc'], 2), 1, 0, 'C', true);
            $pdf->Cell(65, 4, $ov[$i]['fechagiro'], 1, 0, 'C', true);
            $pdf->Cell(66, 4, $ov[$i]['fvencimiento'], 1, 0, 'C', true);
        }


        $pdf->AliasNbPages();
        $pdf->Output();

        /*





        $data['resp'] = $resp;
        $paginacion = $dataGuia->paginarListarLetrasPendientes();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 30);

        $this->view->show('/letras/letraspendientes.phtml', $data);*/
    }

    function reporteFacturacion() {
        $idOrdenVenta = $_REQUEST['idOrdenVenta'];
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : null;
        $idVendedor = $_REQUEST['idVendedor'];
        $idTipoDoc = $_REQUEST['idTipoDoc'];
        $idCliente = $_REQUEST['idCliente'];
        $lstSituacion = $_REQUEST['lstSituacion'];
        $orden = $_REQUEST['lstOrden'];
        $reporte = $this->AutoLoadModel('reporte');
        $datos = $reporte->reporteFacturacion($txtFechaInicio, $txtFechaFinal, $idVendedor, $idOrdenVenta, $idCliente, $idTipodoc, $lstSituacion, $orden);
        $cantidadData = count($datos);
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $titulos = array('#', 'ORDEN VENTA', 'S-NUM.', 'F. EMI.', 'EMP', 'CLIENTE', 'MONTO (S/)', 'MONTO ($)', '%', 'T', 'TIPO', 'VENDEDOR', 'SITUACION');
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(8, 20, 20, 15, 8, 70, 20, 20, 8, 5, 16, 55, 20);
        $orientacion = array('', '', '', 'C', 'C', '', 'R', 'R', 'C', 'C', '', '', 'C');

        $tipoCambioVentas = $this->configIni($this->configIni('Globals', 'Modo'), 'TipoCambio');
        $pdf->SetWidths($ancho);

        if (!empty($txtFechaFinal) || !empty($txtFechaInicio)) {
            $fecha1 = !empty($txtFechaInicio) ? $txtFechaInicio : utf8_decode('?');
            $fecha2 = !empty($txtFechaFinal) ? $txtFechaFinal : utf8_decode('?');
            $pdf->_fecha = 'Rango Fecha: ' . $fecha1 . ' - ' . $fecha2;
        }

        $pdf->_titulo = "REPORTE DE FACTURACION";
        $pdf->_datoPie = 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->_titulos = $titulos;


        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetTitulos($titulos);

        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);

        $importeFactura = 0;
        $importeBoleta = 0;

        for ($i = 0; $i < $cantidadData; $i++) {
            $modoFactura = "";
            if ($datos[$i]['modofactura'] == 1) {
                $modoFactura = "Precio";
            } else if ($datos[$i]['modofactura'] == 2) {
                $modoFactura = "Cantidad";
            }
            if ($datos[$i]['nombredoc'] == 1) {
                $importeFactura+=round($datos[$i]['montofacturado'], 2);
                $valorLetra = "F";
            } else {
                $importeBoleta+=round($datos[$i]['montofacturado'], 2);
                $valorLetra = "B";
            }
            $vendedor = $datos[$i]['nombres'] . ' ' . $datos[$i]['apellidopaterno'] . ' ' . $datos[$i]['apellidomaterno'];
            $fila = array(($i + 1), $datos[$i]['codigov'], str_pad($datos[$i]['serie'], 3, '0', STR_PAD_LEFT) . '-' . $datos[$i]['numdoc'], $datos[$i]['fechadoc'], $datos[$i][codigoalmacen], html_entity_decode($datos[$i]['razonsocial'], ENT_QUOTES, 'UTF-8'), number_format($datos[$i]['montofacturado'], 2), number_format(round($datos[$i]['montofacturado'], 2) / $tipoCambioVentas, 2), $datos[$i]['porcentajefactura'], $valorLetra, $modoFactura, $vendedor, $datos[$i]['situacion']);
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $pdf->Cell(25, 5, "MONTO FACTURA S/.", 1, 0, 'C', true);
        $pdf->Cell(25, 5, number_format($importeFactura, 2), 1, 0, 'R', false);
        $pdf->Cell(25, 5, "MONTO BOLETA S/.", 1, 0, 'C', true);
        $pdf->Cell(25, 5, number_format($importeBoleta, 2), 1, 0, 'R', false);
        $pdf->Cell(25, 5, "TOTAL S/.", 1, 0, 'C', true);
        $pdf->Cell(25, 5, number_format($importeBoleta + $importeFactura, 2), 1, 0, 'R', false);
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteKardexProduccion() {
        $txtFechaInicio = $_REQUEST['txtFechaInicio'];
        $txtFechaFinal = $_REQUEST['txtFechaFinal'];
        $idProducto = $_REQUEST['idProducto'];
        $idTipoMovimiento = $_REQUEST['idTipoMovimiento'];
        $idTipoOperacion = $_REQUEST['idTipoOperacion'];
        $txtDescripcion = $_REQUEST['txtDescripcion'];
        $reporte = $this->AutoLoadModel('reporte');
        $datos = $reporte->reporteKardexProduccion($txtFechaInicio, $txtFechaFinal, $idProducto, $idTipoMovimiento, $idTipoOperacion);

        $cantidadData = count($datos);
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $titulos = array('#', 'FECHA', 'T. MOV.', 'CONCEPTO', 'ORDEN', 'RAZON SOCIAL', 'DEVOLUCION', 'PRECIO', 'CANT. ', 'SALDO', 'IMPORTE S/.');
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $ancho = array(8, 18, 20, 30, 18, 70, 30, 20, 20, 15, 15, 20);
        $orientacion = array('', 'C', 'C', '', 'C', '', 'C', 'C', 'R', 'C', 'C', 'R');

        $tipoCambioVentas = $this->configIni($this->configIni('Globals', 'Modo'), 'TipoCambio');
        $pdf->SetWidths($ancho);

        if (!empty($txtFechaFinal) || !empty($txtFechaInicio)) {
            $fecha1 = !empty($txtFechaInicio) ? $txtFechaInicio : utf8_decode('?');
            $fecha2 = !empty($txtFechaFinal) ? $txtFechaFinal : utf8_decode('?');
            $pdf->_fecha = 'Rango Fecha: ' . $fecha1 . ' - ' . $fecha2;
        }

        $pdf->_titulo = "REPORTE::KARDEX DE PRODUCCION";
        $pdf->_datoPie = $txtDescripcion . '     ' . 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->_titulos = $titulos;


        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetTitulos($titulos);

        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);

        $importeFactura = 0;
        $importeBoleta = 0;
        for ($i = 0; $i < $cantidadData; $i++) {
            $modoFactura = "";
            $fila = array(($i + 1), $datos[$i]['fecha'], $datos[$i]['tipo movimiento'], $datos[$i]['concepto movimiento'], $datos[$i]['codigov'], html_entity_decode($datos[$i]['razon social'], ENT_QUOTES, 'UTF-8'), $datos[$i]['devolucion'], number_format($datos[$i]['precio'], 2), $datos[$i]['cantidad'], $datos[$i]['saldo'], number_format(round($datos[$i]['precio'], 2) * $datos[$i]['cantidad'], 2));
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteKardexProduccionRep() {
        $txtFechaInicio = $_REQUEST['txtFechaInicio'];
        $txtFechaFinal = $_REQUEST['txtFechaFinal'];
        $codigoRep = $_REQUEST['codigoRep'];
        $idProducto = $_REQUEST['idProducto'];
        $idTipoMovimiento = $_REQUEST['idTipoMovimiento'];
        $idTipoOperacion = $_REQUEST['idTipoOperacion'];
        $txtDescripcion = $_REQUEST['txtDescripcion'];
        $reporte = $this->AutoLoadModel('reporte');
        $datos = $reporte->reporteKardexProduccionRepuesto($txtFechaInicio, $txtFechaFinal, $idProducto, $idTipoMovimiento, $idTipoOperacion);
        $cantidadData = count($datos);
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $titulos = array('#', 'FECHA', 'T. MOV.', 'OBSERVACION', 'ORDEN', 'RAZON SOCIAL',  'PRECIO', 'CANT. ', 'SALDO', 'IMPORTE S/.');
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $ancho = array(10, 20, 20, 35, 25, 70, 25, 20, 20, 25, 15, 20);
        $orientacion = array('', 'C', 'C', '', 'C', '', 'C', 'C', 'C', 'C', 'C', 'R');

        $tipoCambioVentas = $this->configIni($this->configIni('Globals', 'Modo'), 'TipoCambio');
        $pdf->SetWidths($ancho);
        if (!empty($codigoRep)) {
        $pdf->_fecha = 'Codigo Repuesto: '.$codigoRep. '         ';
        }

        if (!empty($txtFechaFinal) || !empty($txtFechaInicio)) {
            $fecha1 = !empty($txtFechaInicio) ? $txtFechaInicio : utf8_decode('?');
            $fecha2 = !empty($txtFechaFinal) ? $txtFechaFinal : utf8_decode('?');
            $pdf->_fecha .= 'Rango Fecha: ' . $fecha1 . ' - ' . $fecha2;
        }

        $pdf->_titulo = "REPORTE::KARDEX DE REPUESTO". '             ';
        $pdf->_datoPie = $txtDescripcion . '     ' . 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->_titulos = $titulos;


        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetTitulos($titulos);

        $pdf->SetFillColor(0224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);

        $importeFactura = 0;
        $importeBoleta = 0;
        for ($i = 0; $i < $cantidadData; $i++) {
            $modoFactura = "";
            $fila = array(($i + 1), $datos[$i]['fecha'], $datos[$i]['tipo movimiento'], $datos[$i]['observacion'], $datos[$i]['codigooc'], html_entity_decode($datos[$i]['razonsocialp'], ENT_QUOTES, 'UTF-8'), number_format($datos[$i]['precio'], 2), $datos[$i]['cantidad'], $datos[$i]['saldo'], number_format(round($datos[$i]['precio'], 2) * $datos[$i]['cantidad'], 2));
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteVentasXMes() {
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : date('Y-m-01');
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-').''.  $this->obtenerFinMes(date('n'), date('Y'));
        $idtipodocumento = $_REQUEST['lstTipoDocumento'];

        $reporte = $this->AutoLoadModel('reporte');
        $datos = $reporte->reporteVentasxMes($txtFechaInicio, $txtFechaFinal, $idtipodocumento);
        $cantidadData = count($datos);
        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $titulos = array('FECHA', 'SERIE', 'NUMERO', 'CODIGO', 'PRODUCTO');
        $pdf->SetFont('Helvetica', 'B', 10);
        $ancho = array(15, 10, 15, 40, 110);
        $orientacion = array('C', 'C', 'C', 'L', 'L');

        //$tipoCambioVentas = $this->configIni($this->configIni('Globals', 'Modo'), 'TipoCambio');
        $doc = $this->tipoDocumento();
        $doc = $doc[$idtipodocumento];
        $pdf->SetWidths($ancho);

        $pdf->_fecha = $txtFechaInicio . ' - ' . $txtFechaFinal;
        $pdf->_titulo = "REPORTE DE VENTAS - " . $doc;
        $pdf->_datoPie = 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->_titulos = $titulos;

        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(175, 217, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(60, 77, 255);
        $pdf->SetLineWidth(.4);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetTitulos($titulos);

        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);

        $sernum = "";
        for ($i = 0; $i < $cantidadData; $i++) {
            if ($sernum != ($datos[$i]['serie'].$datos[$i]['numero'])) {
                $fila = array($datos[$i]['fecha'], str_pad($datos[$i]['serie'], 3, "0", STR_PAD_LEFT), str_pad($datos[$i]['numero'], 8, "0", STR_PAD_LEFT), $datos[$i]['codigo'], $datos[$i]['producto']);
                $sernum = $datos[$i]['serie'].$datos[$i]['numero'];
            }
            else {
                $fila = array('', '', '', $datos[$i]['codigo'], $datos[$i]['producto']);
            }
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteguiasydocumentosOLD() {

        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : date('Y-m-01');
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-').''.  $this->obtenerFinMes(date('n'), date('Y'));

        $reporte = $this->AutoLoadModel('reporte');
        $datos = $reporte->reporteguias2($txtFechaInicio, $txtFechaFinal);
        //var_dump($datos);
        $cantidadData = count($datos);
        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $titulos = array('FECHA GUIA', 'NUM GUIA', 'FECHA DOC', 'SERIE', 'NUM FACT', 'NUM BOL', 'IGV S/F', 'IGV FACT', 'TOTAL');
        $pdf->SetFont('Helvetica', 'B', 10);
        $ancho = array(25, 25, 25, 15, 22, 22, 16, 15, 25);
        $orientacion = array('C', 'C', 'C', 'C', 'C', 'C', 'R', 'R', 'R');

        $pdf->SetWidths($ancho);

        $pdf->_fecha = $txtFechaInicio . ' - ' . $txtFechaFinal;
        $pdf->_titulo = "REPORTE GUIA/DOCUMENTO";
        $pdf->_datoPie = 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->_titulos = $titulos;

        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(175, 217, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(60, 77, 255);
        $pdf->SetLineWidth(.4);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetTitulos($titulos);
        $relleno = false;

        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $sernum = "";
        /*$totalfactura = 0.0;
        $totalboleta = 0.0;
        $totalguia = 0.0;*/
        $igvcd = 0.0;
        $igvsd = 0.0;
        $sumtot = 0.0;
        for ($i = 0; $i < $cantidadData; $i++) {
            //echo $datos[$i]['codigov']."<br>";
            if ($datos[$i]['desaprobado'] == 1) {
                $fila = array($datos[$i]['fordenventa'], $datos[$i]['codigov'], 'DESAPROBADO', '---', '---', '---', '0.00', '0.00');
            }
            else {
                $datosDocumento = $reporte->reportedocumentos2($datos[$i]['idordenventa']);
                $total = 0.0;
                if ($datosDocumento != null && $datosDocumento['esanulado'] != '1') {
                    if (!empty($datosDocumento['numdocfac'])) {
                        //$totalfactura += $datosDocumento['montofacturado'];
                        $igvcd += $datosDocumento['montoigv'];
                    }
                    else {
                        //$totalboleta += $datosDocumento['montofacturado'];
                        //$igvsd += $datosDocumento['montoigv'];
                        $igvcd += $datosDocumento['montoigv'];
                    }
                    //$fila = array($datos[$i]['fordenventa'], $datos[$i]['codigov'], $datosDocumento['fechadoc'], $datosDocumento['serie'], $datosDocumento['numdocfac'], $datosDocumento['numdocbol'], !empty($datosDocumento['numdocbol']) ? number_format($datosDocumento['montoigv'], 2) : '', empty($datosDocumento['numdocbol']) ? number_format($datosDocumento['montoigv'], 2) : '', number_format($datosDocumento['montofacturado'], 2));
                    $fila = array($datos[$i]['fordenventa'], $datos[$i]['codigov'], $datosDocumento['fechadoc'], $datosDocumento['serie'], $datosDocumento['numdocfac'], $datosDocumento['numdocbol'], '', number_format($datosDocumento['montoigv'], 2), number_format($datosDocumento['montofacturado'], 2));
                    $total = $datosDocumento['montofacturado'];
                } else {
                    $total = $datos[$i]['importeov'];
                    $igv = number_format($total-($total/1.18),2);
                    $fila = array($datos[$i]['fordenventa'], $datos[$i]['codigov'], '', '', '', '', $igv, '', $total);
                    //$totalguia += $total;
                    $igvsd += $igv;
                }
                $sumtot += $total;
            }
            $pdf->Row($fila);
            $pdf->fill($relleno);
            $relleno = !$relleno;
        }
        $pdf->Cell(112, 7, "", 0);
        $pdf->Cell(22, 7, "TOTAL:", 0, 0, 'R');
        $pdf->Cell(16, 7, number_format($igvsd, 2), 1, 0, 'R');
        $pdf->Cell(15, 7, number_format($igvcd, 2), 1, 0, 'R');
        $pdf->Cell(25, 7, number_format($sumtot, 2), 1, 0, 'R');
        /*$pdf->Cell(16, 7, number_format($totalfactura, 2), 0);
        $pdf->Cell(15, 7, number_format($totalboleta, 2), 0);
        $pdf->Cell(25, 7, number_format($totalguia, 2), 0);*/

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteguiasydocumentos() {

        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : date('Y-m-01');
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-').''.  $this->obtenerFinMes(date('n'), date('Y'));

        $reporte = $this->AutoLoadModel('reporte');
        $datos = $reporte->reporteguias2($txtFechaInicio, $txtFechaFinal);
        //var_dump($datos);
        $cantidadData = count($datos);
        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $titulos = array('FECHA GUIA', 'NUM GUIA', 'FECHA DOC', 'G R', 'NUM FACT', 'NUM BOL', 'IGV S/F', 'IGV FACT', 'TOTAL');
        $pdf->SetFont('Helvetica', 'B', 10);
        $ancho = array(25, 25, 25, 15, 22, 22, 16, 15, 25);
        $orientacion = array('C', 'C', 'C', 'C', 'C', 'C', 'R', 'R', 'R');

        $pdf->SetWidths($ancho);

        $pdf->_fecha = $txtFechaInicio . ' - ' . $txtFechaFinal;
        $pdf->_titulo = "REPORTE GUIA/DOCUMENTO";
        $pdf->_datoPie = 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->_titulos = $titulos;

        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(175, 217, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(60, 77, 255);
        $pdf->SetLineWidth(.4);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $pdf->SetTitulos($titulos);
        $relleno = false;

        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);

        $sernum = "";
        $igvcd = 0.0;
        $igvsd = 0.0;
        $sumtot = 0.0;
        for ($i = 0; $i < $cantidadData; $i++) {
            $guiaRemision = $reporte->reportedocumentos3($datos[$i]['idordenventa']);
            if ($datos[$i]['desaprobado'] == 1) {
                $fila = array($datos[$i]['fordenventa'], $datos[$i]['codigov'], 'DESAPROBADO', '---', '---', '---', '0.00', '0.00');
            }else {
                $datosDocumento = $reporte->reportedocumentos2($datos[$i]['idordenventa']);
                $total = 0.0;
                if ($datosDocumento != null && $datosDocumento['esanulado'] != '1') {
                    $total = $datosDocumento['montofacturado'];
                    $igvAux = ($total/1.18)*0.18;
                    $fila = array($datos[$i]['fordenventa'], $datos[$i]['codigov'], $datosDocumento['fechadoc'], $guiaRemision['ndoc'], $datosDocumento['numdocfac'], $datosDocumento['numdocbol'], '', number_format($igvAux,2), number_format($total,2));
                    $igvcd += $igvAux;
                } else {
                    $total = $datos[$i]['importeov'];
                    $igvAux = $total-($total/1.18);
                    $fila = array($datos[$i]['fordenventa'], $datos[$i]['codigov'], '', $guiaRemision['ndoc'], '', '', number_format($igvAux,2), '', $total);
                    $igvsd += $igvAux;
                }
                $sumtot += $total;
            }
            $pdf->Row($fila);
            $pdf->fill($relleno);
            $relleno = !$relleno;
        }
        $pdf->Cell(112, 7, "", 0);
        $pdf->Cell(22, 7, "TOTAL:", 0, 0, 'R');
        $pdf->Cell(16, 7, number_format($igvsd, 2), 1, 0, 'R');
        $pdf->Cell(15, 7, number_format($igvcd, 2), 1, 0, 'R');
        $pdf->Cell(25, 7, number_format($sumtot, 2), 1, 0, 'R');
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function estadoproductos() {
        if (!empty($_REQUEST['txtIdOrdenCompra'])) {
            $ordcom = $_REQUEST['txtIdOrdenCompra'];
            $producto = !empty($_REQUEST['txtIdProducto']) ? $_REQUEST['txtIdProducto'] : "";

            $reporte = $this->AutoLoadModel('reporte');
            $datos = $reporte->reporteEstadoProductos($ordcom, $producto);
            $cantidadData = count($datos);
            $pdf = new PDF_MC_Table("P", "mm", "A4");
            $titulos = array('NUMERO ORDEN', 'FECHA ORDEN', 'DETALLES', 'CANT.');
            $pdf->SetFont('Helvetica', 'B', 10);
            $ancho = array(30, 30, 110, 20);
            $orientacion = array('C', 'C', 'L');

            $pdf->SetWidths($ancho);

            $pdf->_fecha = $txtFechaInicio . ' - ' . $txtFechaFinal;
            $pdf->_titulo = "REPORTE";
            $pdf->_datoPie = 'Impreso el :' . date('Y-m-d H:m:s');
            $pdf->AddPage();
            $pdf->_titulos = $titulos;

            $sernum = "";
            for ($i = 0; $i < $cantidadData; $i++) {
                if($sernum!=$datos[$i]['codigopa']) {
                    $pdf->ln();
                    $pdf->SetFont('Helvetica', 'B', 10);
                    $pdf->Cell(150, 7, "Producto: " . $datos[$i]['codigopa']."-".utf8_decode(html_entity_decode($datos[$i]['nompro'], ENT_QUOTES, 'UTF-8')));
                    $pdf->ln();
                    $pdf->SetFillColor(175, 217, 255);
                    $pdf->SetTextColor(0);
                    $pdf->SetDrawColor(60, 77, 255);
                    $pdf->SetLineWidth(.4);
                    $pdf->_orientacion = $orientacion;
                    $pdf->SetAligns($orientacion);
                    $pdf->SetTitulos($titulos);

                    $pdf->SetFillColor(224, 235, 255);
                    $pdf->SetTextColor(0);
                    $pdf->SetDrawColor(12, 78, 139);
                    $relleno = false;
                    $sernum=$datos[$i]['codigopa'];
                }

                $datos[$i]['observaciones'] = str_replace('&lt;/li&gt;&lt;li&gt;', ", ", $datos[$i]['observaciones']);
                $fila = array($datos[$i]['codigov'], $datos[$i]['fordenventa'], $bodytag = str_replace(array('&lt;ul&gt;', '&lt;li&gt;', '&lt;strong&gt;', '&lt;/strong&gt;', '&lt;/li&gt;', '&lt;/ul&gt'), "", $datos[$i]['observaciones']), $datos[$i]['cantidad']);

                $pdf->Row($fila);
                $pdf->fill($relleno);
                $relleno = !$relleno;
            }

            $pdf->AliasNbPages();
            $pdf->Output();
        }
        else {
            echo "Ingrese al menos el número de orden";
        }
    }
    
    function resumencobranzas() {
        set_time_limit(2500);
        $cmtEtapa = $_REQUEST['lstEtapa'];
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-d');
        
        //Estructura Moneda|Zona
        $reporte = $this->AutoLoadModel('reporte');
        $dataLetrasSegunCobro = $reporte->resumenLetras_mirabymejorado($cmtEtapa, $txtFechaInicio, $txtFechaFinal);
        //$dataLetrasSegunCobro = array();
        $tamLSC = count($dataLetrasSegunCobro);
        
        //Estructura del array SIN PA: categoria | moneda
        $arrayLetrasSinEnviarBanco[1][1] = 0;
        $arrayLetrasSinEnviarBanco[1][2] = 0;
        $arrayLetrasSinEnviarBanco[2][1] = 0;
        $arrayLetrasSinEnviarBanco[2][2] = 0;

        //Estructura del array CON PA: categoria | moneda
        $arrayLetrasEnviadasBanco[1][1]['BBVA'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['BBVA'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['BBVA'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['BBVA'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['BCP'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['BCP'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['BCP'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['BCP'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['CPA'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['CPA'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['CPA'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['CPA'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['R-CL'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['R-CL'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['R-CL'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['R-CL'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['A/C | BCP-D'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['A/C | BCP-D'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['A/C | BCP-D'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['A/C | BCP-D'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['A/C | BCP-CL'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['A/C | BCP-CL'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['A/C | BCP-CL'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['A/C | BCP-CL'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['A/C | BBVA-D'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['A/C | BBVA-D'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['A/C | BBVA-D'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['A/C | BBVA-D'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['D/C | BCP-CL'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['D/C | BCP-CL'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['D/C | BCP-CL'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['D/C | BCP-CL'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['D/C | BCP-D'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['D/C | BCP-D'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['D/C | BCP-D'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['D/C | BCP-D'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['D/C | BBVA-D'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['D/C | BBVA-D'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['D/C | BBVA-D'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['D/C | BBVA-D'] = 0;

        $arrayLetrasEnviadasBanco[1][1]['BCP CL'] = 0;
        $arrayLetrasEnviadasBanco[1][2]['BCP CL'] = 0;
        $arrayLetrasEnviadasBanco[2][1]['BCP CL'] = 0;
        $arrayLetrasEnviadasBanco[2][2]['BCP CL'] = 0;

        for ($i = 0; $i < $tamLSC; $i++) {
            if ($dataLetrasSegunCobro[$i]['recepcionletras'] != 'PA') {
                $arrayLetrasSinEnviarBanco[$dataLetrasSegunCobro[$i]['idpadrec']][$dataLetrasSegunCobro[$i]['idmoneda']] += $dataLetrasSegunCobro[$i]['saldodoc'];
            } else {
                $arrayLetrasEnviadasBanco[$dataLetrasSegunCobro[$i]['idpadrec']][$dataLetrasSegunCobro[$i]['idmoneda']][$dataLetrasSegunCobro[$i]['numerounico']] += $dataLetrasSegunCobro[$i]['saldodoc'];
            }
        }
        $LEB_BCP_LIMA_SOLES = $arrayLetrasEnviadasBanco[1][1]['BCP'] + $arrayLetrasEnviadasBanco[1][1]['A/C | BCP-D'] + $arrayLetrasEnviadasBanco[1][1]['A/C | BCP-CL'] + $arrayLetrasEnviadasBanco[1][1]['D/C | BCP-CL'] + $arrayLetrasEnviadasBanco[1][1]['D/C | BCP-D'] + $arrayLetrasEnviadasBanco[1][1]['BCP CL'];
        $LEB_BCP_LIMA_DOLARES = $arrayLetrasEnviadasBanco[1][2]['BCP'] + $arrayLetrasEnviadasBanco[1][2]['A/C | BCP-D'] + $arrayLetrasEnviadasBanco[1][2]['A/C | BCP-CL'] + $arrayLetrasEnviadasBanco[1][2]['D/C | BCP-CL'] + $arrayLetrasEnviadasBanco[1][2]['D/C | BCP-D'] + $arrayLetrasEnviadasBanco[1][2]['BCP CL'];
        $LEB_BCP_PROVINCIA_SOLES = $arrayLetrasEnviadasBanco[2][1]['BCP'] + $arrayLetrasEnviadasBanco[2][1]['A/C | BCP-D'] + $arrayLetrasEnviadasBanco[2][1]['A/C | BCP-CL'] + $arrayLetrasEnviadasBanco[2][1]['D/C | BCP-CL'] + $arrayLetrasEnviadasBanco[2][1]['D/C | BCP-D'] + $arrayLetrasEnviadasBanco[2][1]['BCP CL'];
        $LEB_BCP_PROVINCIA_DOLARES = $arrayLetrasEnviadasBanco[2][2]['BCP'] + $arrayLetrasEnviadasBanco[2][2]['A/C | BCP-D'] + $arrayLetrasEnviadasBanco[2][2]['A/C | BCP-CL'] + $arrayLetrasEnviadasBanco[2][2]['D/C | BCP-CL'] + $arrayLetrasEnviadasBanco[2][2]['D/C | BCP-D'] + $arrayLetrasEnviadasBanco[2][2]['BCP CL'];
                
        $LEB_BBVA_LIMA_SOLES = $arrayLetrasEnviadasBanco[1][1]['BBVA'] + $arrayLetrasEnviadasBanco[1][1]['A/C | BBVA-D'] + $arrayLetrasEnviadasBanco[1][1]['D/C | BBVA-D'];
        $LEB_BBVA_LIMA_DOLARES = $arrayLetrasEnviadasBanco[1][2]['BBVA'] + $arrayLetrasEnviadasBanco[1][2]['A/C | BBVA-D'] + $arrayLetrasEnviadasBanco[1][2]['D/C | BBVA-D'];
        $LEB_BBVA_PROVINCIA_SOLES = $arrayLetrasEnviadasBanco[2][1]['BBVA'] + $arrayLetrasEnviadasBanco[2][1]['A/C | BBVA-D'] + $arrayLetrasEnviadasBanco[2][1]['D/C | BBVA-D'];
        $LEB_BBVA_PROVINCIA_DOLARES = $arrayLetrasEnviadasBanco[2][2]['BBVA'] + $arrayLetrasEnviadasBanco[2][2]['A/C | BBVA-D'] + $arrayLetrasEnviadasBanco[2][2]['D/C | BBVA-D']; 
        
        $LSC_LIMA_SOLES = $LEB_BBVA_LIMA_SOLES + $LEB_BCP_LIMA_SOLES + $arrayLetrasEnviadasBanco[1][1]['R-CL'] + $arrayLetrasEnviadasBanco[1][1]['CPA'];
        $LSC_LIMA_DOLARES = $LEB_BBVA_LIMA_DOLARES + $LEB_BCP_LIMA_DOLARES + $arrayLetrasEnviadasBanco[1][2]['R-CL'] + $arrayLetrasEnviadasBanco[1][2]['CPA'];
        $LSC_PROVINCIA_SOLES = $LEB_BBVA_PROVINCIA_SOLES + $LEB_BCP_PROVINCIA_SOLES + $arrayLetrasEnviadasBanco[2][1]['R-CL'] + $arrayLetrasEnviadasBanco[2][1]['CPA'];
        $LSC_PROVINCIA_DOLARES = $LEB_BBVA_PROVINCIA_DOLARES + $LEB_BCP_PROVINCIA_DOLARES + $arrayLetrasEnviadasBanco[2][2]['R-CL'] + $arrayLetrasEnviadasBanco[2][2]['CPA'];

        $dataLetrasProtestadas =  $reporte->detalladoLetrasProtestadas_mirabymejorado($cmtEtapa, $txtFechaInicio, $txtFechaFinal);
        //$dataLetrasProtestadas =  array();
        $tamLP = count($dataLetrasProtestadas);

        //Estructura del array LETRAS PROTESTADAS: categoria | moneda
        $arrayLetrasProtestadas[1][1] = 0;
        $arrayLetrasProtestadas[1][2] = 0;
        $arrayLetrasProtestadas[2][1] = 0;
        $arrayLetrasProtestadas[2][2] = 0;
        for ($i = 0; $i < $tamLP; $i++) {
            $arrayLetrasProtestadas[$dataLetrasProtestadas[$i]['idpadrec']][$dataLetrasProtestadas[$i]['idmoneda']] += $dataLetrasProtestadas[$i]['saldodoc'];
        }

        $arrayCreditosVencer[1][1]['TOTAL'] = 0;
        $arrayCreditosVencer[1][2]['TOTAL'] = 0;
        $arrayCreditosVencer[2][1]['TOTAL'] = 0;
        $arrayCreditosVencer[2][2]['TOTAL'] = 0;
        $arrayDias = array(30, 60, 90, 91);
        //$arrayDias = array();
        foreach ($arrayDias as $diavencer) {
            $arrayCreditosVencer[1][1][$diavencer] = 0;
            $arrayCreditosVencer[1][2][$diavencer] = 0;
            $arrayCreditosVencer[2][1][$diavencer] = 0;
            $arrayCreditosVencer[2][2][$diavencer] = 0;
            $dataCreditosVencer[$diavencer] = $reporte->resumenDetalladoCreditos_mirabymejorado($cmtEtapa, $txtFechaInicio, $txtFechaFinal, 2, 0, $diavencer);
            $tamCV = count($dataCreditosVencer[$diavencer]);
            for ($i = 0; $i < $tamCV; $i++) {
                $arrayCreditosVencer[$dataCreditosVencer[$diavencer][$i]['idpadrec']][$dataCreditosVencer[$diavencer][$i]['idmoneda']][$diavencer] += $dataCreditosVencer[$diavencer][$i]['saldodoc'];
            }
            $arrayCreditosVencer[1][1]['TOTAL'] += $arrayCreditosVencer[1][1][$diavencer];
            $arrayCreditosVencer[1][2]['TOTAL'] += $arrayCreditosVencer[1][2][$diavencer];
            $arrayCreditosVencer[2][1]['TOTAL'] += $arrayCreditosVencer[2][1][$diavencer];
            $arrayCreditosVencer[2][2]['TOTAL'] += $arrayCreditosVencer[2][2][$diavencer];
        }
        
        $arrayCreditosVencidos[1][1]['TOTAL'] = 0;
        $arrayCreditosVencidos[1][2]['TOTAL'] = 0;
        $arrayCreditosVencidos[2][1]['TOTAL'] = 0;
        $arrayCreditosVencidos[2][2]['TOTAL'] = 0;
        foreach ($arrayDias as $diavencido) {
            $arrayCreditosVencidos[1][1][$diavencido] = 0;
            $arrayCreditosVencidos[1][2][$diavencido] = 0;
            $arrayCreditosVencidos[2][1][$diavencido] = 0;
            $arrayCreditosVencidos[2][2][$diavencido] = 0;
            $dataCreditosVencidos[$diavencido] = $reporte->resumenDetalladoCreditos_mirabymejorado($cmtEtapa, $txtFechaInicio, $txtFechaFinal, 1, $diavencido, 0);
            $tamCV = count($dataCreditosVencidos[$diavencido]);
            for ($i = 0; $i < $tamCV; $i++) {
                $arrayCreditosVencidos[$dataCreditosVencidos[$diavencido][$i]['idpadrec']][$dataCreditosVencidos[$diavencido][$i]['idmoneda']][$diavencido] += $dataCreditosVencidos[$diavencido][$i]['saldodoc'];
            }
            $arrayCreditosVencidos[1][1]['TOTAL'] += $arrayCreditosVencidos[1][1][$diavencido];
            $arrayCreditosVencidos[1][2]['TOTAL'] += $arrayCreditosVencidos[1][2][$diavencido];
            $arrayCreditosVencidos[2][1]['TOTAL'] += $arrayCreditosVencidos[2][1][$diavencido];
            $arrayCreditosVencidos[2][2]['TOTAL'] += $arrayCreditosVencidos[2][2][$diavencido];
        }
        
        //Estructura del array PESADOS: categoria | moneda | formacobro
        $arrayPesados[40][1][1] = 0;
        $arrayPesados[40][2][1] = 0;
        $arrayPesados[48][1][1] = 0;
        $arrayPesados[48][2][1] = 0;
        
        $arrayPesados[40][1][2] = 0;
        $arrayPesados[40][2][2] = 0;
        $arrayPesados[48][1][2] = 0;
        $arrayPesados[48][2][2] = 0;
        
        $arrayPesados[40][1]['protesto'] = 0;
        $arrayPesados[40][2]['protesto'] = 0;
        $arrayPesados[48][1]['protesto'] = 0;
        $arrayPesados[48][2]['protesto'] = 0;
        
        $arrayPesados[40][1][3] = 0;
        $arrayPesados[40][2][3] = 0;
        $arrayPesados[48][1][3] = 0;
        $arrayPesados[48][2][3] = 0;
        
        $arrayPesados[40][1]['TOTAL'] = 0;
        $arrayPesados[40][2]['TOTAL'] = 0;
        $arrayPesados[48][1]['TOTAL'] = 0;
        $arrayPesados[48][2]['TOTAL'] = 0;
        //$dataPesados = array();
        $dataPesados = $reporte->resumenPesados_mirabymejorado($cmtEtapa, $txtFechaInicio, $txtFechaFinal);
        $tamPesado = count($dataPesados);
        for ($i = 0; $i < $tamPesado; $i++) {
            if ($dataPesados[$i]['formacobro'] == 2) {
                if ($dataPesados[$i]['referencias'][0] != 'P' && $dataPesados[$i]['referencias'][1] != 'P') {
                    $arrayPesados[$dataPesados[$i]['idpadrec']][$dataPesados[$i]['idmoneda']][$dataPesados[$i]['formacobro']] += $dataPesados[$i]['saldodoc'];
                } else {
                    $arrayPesados[$dataPesados[$i]['idpadrec']][$dataPesados[$i]['idmoneda']]['protesto'] += $dataPesados[$i]['saldodoc'];
                }
            } else {
                $arrayPesados[$dataPesados[$i]['idpadrec']][$dataPesados[$i]['idmoneda']][$dataPesados[$i]['formacobro']] += $dataPesados[$i]['saldodoc'];
            }
            $arrayPesados[$dataPesados[$i]['idpadrec']][$dataPesados[$i]['idmoneda']]['TOTAL'] += $dataPesados[$i]['saldodoc'];
        }
        
        $arrayEmpresa[136][1] = 0;
        $arrayEmpresa[136][2] = 0;

        $arrayEmpresa[241][1] = 0;
        $arrayEmpresa[241][2] = 0;

        $arrayEmpresa[152][1] = 0;
        $arrayEmpresa[152][2] = 0;

        $arrayEmpresa[184][1] = 0;
        $arrayEmpresa[184][2] = 0;

        $arrayEmpresa[264][1] = 0;
        $arrayEmpresa[264][2] = 0;

        $arrayEmpresa[59][1] = 0;
        $arrayEmpresa[59][2] = 0;

        $arrayEmpresa[391][1] = 0;
        $arrayEmpresa[391][2] = 0;

        $arrayEmpresa[445][1] = 0;
        $arrayEmpresa[445][2] = 0;

        $arrayEmpresa[540][1] = 0;
        $arrayEmpresa[540][2] = 0;
        
        $arrayEmpresa[557][1] = 0;
        $arrayEmpresa[557][2] = 0;
        
        $arrayEmpresa[558][1] = 0;
        $arrayEmpresa[558][2] = 0;
        
        $arrayEmpresa[554][1] = 0;
        $arrayEmpresa[554][2] = 0;
        
        $arrayEmpresa[1]['TOTAL'] = 0;
        $arrayEmpresa[2]['TOTAL'] = 0;

        //$dataEmpresa = array();
        $dataEmpresa = $reporte->resumenEmpresas_mirabymejorado($cmtEtapa, $txtFechaInicio, $txtFechaFinal);
        $tamEmpresa = count($dataEmpresa);
        for ($i = 0; $i < $tamEmpresa; $i++) {
            $arrayEmpresa[$dataEmpresa[$i]['idvendedor']][$dataEmpresa[$i]['idmoneda']] += $dataEmpresa[$i]['saldodoc'];
            $arrayEmpresa[$dataEmpresa[$i]['idmoneda']]['TOTAL'] += $dataEmpresa[$i]['saldodoc'];
        }
        
        $arrayIncobrables[28][1][1] = 0;
        $arrayIncobrables[28][2][1] = 0;
        $arrayIncobrables[30][1][1] = 0;
        $arrayIncobrables[30][2][1] = 0;
        
        $arrayIncobrables[28][1][2] = 0;
        $arrayIncobrables[28][2][2] = 0;
        $arrayIncobrables[30][1][2] = 0;
        $arrayIncobrables[30][2][2] = 0;
        
        $arrayIncobrables[28][1][2]['protesto'] = 0;
        $arrayIncobrables[28][2][2]['protesto'] = 0;
        $arrayIncobrables[30][1][2]['protesto'] = 0;
        $arrayIncobrables[30][2][2]['protesto'] = 0;
        
        $arrayIncobrables[28][1][3] = 0;
        $arrayIncobrables[28][2][3] = 0;
        $arrayIncobrables[30][1][3] = 0;
        $arrayIncobrables[30][2][3] = 0;
        
        $arrayIncobrables[28][1]['TOTAL'] = 0;
        $arrayIncobrables[28][2]['TOTAL'] = 0;
        $arrayIncobrables[30][1]['TOTAL'] = 0;
        $arrayIncobrables[30][2]['TOTAL'] = 0;
        
        $dataIncobrables = $reporte->resumenIncobrables_mirabymejorado($cmtEtapa, $txtFechaInicio, $txtFechaFinal);
        $tamIncobrable = count($dataIncobrables);
        for ($i = 0; $i < $tamIncobrable; $i++) {
            if ($dataIncobrables[$i]['formacobro'] == 2) {
                if ($dataIncobrables[$i]['referencias'][0] != 'P' && $dataIncobrables[$i]['referencias'][1] != 'P') {
                    $arrayIncobrables[$dataIncobrables[$i]['idcategoria']][$dataIncobrables[$i]['idmoneda']][$dataIncobrables[$i]['formacobro']] += $dataIncobrables[$i]['saldodoc'];
                } else {
                    $arrayIncobrables[$dataIncobrables[$i]['idcategoria']][$dataIncobrables[$i]['idmoneda']]['protesto'] += $dataIncobrables[$i]['saldodoc'];
                }
            } else {
                $arrayIncobrables[$dataIncobrables[$i]['idcategoria']][$dataIncobrables[$i]['idmoneda']][$dataIncobrables[$i]['formacobro']] += $dataIncobrables[$i]['saldodoc'];
            }
            $arrayIncobrables[$dataIncobrables[$i]['idcategoria']][$dataIncobrables[$i]['idmoneda']]['TOTAL'] += $dataIncobrables[$i]['saldodoc'];
        }
        
        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 10);
        $ancho = array(90, 50, 50);
        $pdf->SetWidths($ancho);
        
        $complementotitulo = " GENERAL";
        if ($cmtEtapa == 1) {
            $complementotitulo = " ANTES DE LA PANDEMIA";
        } else if ($cmtEtapa == 2) {
            $complementotitulo = " DESPUES DE LA PANDEMIA";
        }

        $pdf->_fecha = (!empty($txtFechaInicio) ? $txtFechaInicio . ' - ' : '') . $txtFechaFinal;
        $pdf->_titulo = "RESUMEN DE COBRANZAS" . $complementotitulo;
        $pdf->_datoPie = 'Impreso el: ' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.4);
        $orientacion = array('C','L', 'R', 'R');
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $ancho = array(40,50, 50, 50);
        $pdf->SetWidths($ancho);
        
        $pdf->ln();
        $pdf->Cell(9, 7, "LETRAS ENVIADAS AL BANCO");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('BANCO','ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BCP','LIMA', number_format($LEB_BCP_LIMA_SOLES, 2), number_format($LEB_BCP_LIMA_DOLARES, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BCP','PROVINCIA', number_format($LEB_BCP_PROVINCIA_SOLES, 2), number_format($LEB_BCP_PROVINCIA_DOLARES, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('BCP','TOTAL', number_format($LEB_BCP_LIMA_SOLES + $LEB_BCP_PROVINCIA_SOLES, 2), number_format($LEB_BCP_LIMA_DOLARES + $LEB_BCP_PROVINCIA_DOLARES, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BBVA','LIMA', number_format($LEB_BBVA_LIMA_SOLES, 2), number_format($LEB_BBVA_LIMA_DOLARES, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BBVA','PROVINCIA ', number_format($LEB_BBVA_PROVINCIA_SOLES, 2), number_format($LEB_BBVA_PROVINCIA_DOLARES, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('BBVA','TOTAL', number_format($LEB_BBVA_LIMA_SOLES + $LEB_BBVA_PROVINCIA_SOLES, 2), number_format($LEB_BBVA_LIMA_DOLARES + $LEB_BBVA_PROVINCIA_DOLARES, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL','', number_format($LEB_BCP_LIMA_SOLES + $LEB_BCP_PROVINCIA_SOLES + $LEB_BBVA_LIMA_SOLES + $LEB_BBVA_PROVINCIA_SOLES, 2), number_format($LEB_BCP_LIMA_DOLARES + $LEB_BCP_PROVINCIA_DOLARES + $LEB_BBVA_LIMA_DOLARES + $LEB_BBVA_PROVINCIA_DOLARES, 2));
        $pdf->Row($fila);
        $pdf->ln();
        $pdf->setxy(10,36.5);
        $pdf->MultiCell(40,15, 'BCP', 1, 'C', true);
        $pdf->MultiCell(40,15, 'BBVA', 1, 'C', true);
        $pdf->MultiCell(90,5, 'IMPORTE TOTAL', 1, 'C', true);
        $pdf->setxy(10,70);
        
        $pdf->ln();
        $pdf->Cell(90, 7, "LETRAS SEGUN COBRO");
        $pdf->ln();
        $pdf->fill(false);

        $fila = array('ZONA GEOGRAFICA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('BANCO', 'TIPO', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BBVA', 'LETRA DESCUENTO', number_format($arrayLetrasEnviadasBanco[1][1]['BBVA'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['BBVA'], 2));
        $pdf->Row($fila);
        $fila = array('BCP', 'LETRA DESCUENTO', number_format($arrayLetrasEnviadasBanco[1][1]['BCP'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['BCP'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BCP', 'COBRANZA LIBRE', number_format($arrayLetrasEnviadasBanco[1][1]['BCP CL'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['BCP CL'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CPA', 'CPA LIMA', number_format($arrayLetrasEnviadasBanco[1][1]['CPA'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['CPA'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BCP-D', 'BCP-D LIMA', number_format($arrayLetrasEnviadasBanco[1][1]['A/C | BCP-D'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['A/C | BCP-D'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BCP-CL', 'BCP-CL LIMA', number_format($arrayLetrasEnviadasBanco[1][1]['A/C | BCP-CL'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['A/C | BCP-CL'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BBVA-D', 'BBVA-D LIMA', number_format($arrayLetrasEnviadasBanco[1][1]['A/C | BBVA-D'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['A/C | BBVA-D'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BCP-CL', 'BCP-CL LIMA', number_format($arrayLetrasEnviadasBanco[1][1]['D/C | BCP-CL'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['D/C | BCP-CL'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BCP-D', 'BCP-D LIMA', number_format($arrayLetrasEnviadasBanco[1][1]['D/C | BCP-D'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['D/C | BCP-D'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BBVA-D', 'BBVA-D LIMA', number_format($arrayLetrasEnviadasBanco[1][1]['D/C | BBVA-D'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['D/C | BBVA-D'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('R-CL', 'R-CL LIMA', number_format($arrayLetrasEnviadasBanco[1][1]['R-CL'], 2), number_format($arrayLetrasEnviadasBanco[1][2]['R-CL'], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', '', number_format($LSC_LIMA_SOLES, 2), number_format(($LSC_LIMA_DOLARES), 2));
        
        $arrayLetrasEnviadasBanco['CPA'][1] = $arrayLetrasEnviadasBanco[1][1]['CPA'] + $arrayLetrasEnviadasBanco[2][1]['CPA'];
        $arrayLetrasEnviadasBanco['CPA'][2] = $arrayLetrasEnviadasBanco[1][2]['CPA'] + $arrayLetrasEnviadasBanco[2][2]['CPA'];
        $arrayLetrasEnviadasBanco['A/C | BCP-D'][1] = $arrayLetrasEnviadasBanco[1][1]['A/C | BCP-D'] + $arrayLetrasEnviadasBanco[2][1]['A/C | BCP-D'];
        $arrayLetrasEnviadasBanco['A/C | BCP-D'][2] = $arrayLetrasEnviadasBanco[1][2]['A/C | BCP-D'] + $arrayLetrasEnviadasBanco[2][2]['A/C | BCP-D'];
        $arrayLetrasEnviadasBanco['A/C | BCP-CL'][1] = $arrayLetrasEnviadasBanco[1][1]['A/C | BCP-CL'] + $arrayLetrasEnviadasBanco[2][1]['A/C | BCP-CL'];
        $arrayLetrasEnviadasBanco['A/C | BCP-CL'][2] = $arrayLetrasEnviadasBanco[1][2]['A/C | BCP-CL'] + $arrayLetrasEnviadasBanco[2][2]['A/C | BCP-CL'];
        $arrayLetrasEnviadasBanco['A/C | BBVA-D'][1] = $arrayLetrasEnviadasBanco[1][1]['A/C | BBVA-D'] + $arrayLetrasEnviadasBanco[2][1]['A/C | BBVA-D'];
        $arrayLetrasEnviadasBanco['A/C | BBVA-D'][2] = $arrayLetrasEnviadasBanco[1][2]['A/C | BBVA-D'] + $arrayLetrasEnviadasBanco[2][2]['A/C | BBVA-D'];
        $arrayLetrasEnviadasBanco['D/C | BCP-CL'][1] = $arrayLetrasEnviadasBanco[1][1]['D/C | BCP-CL'] + $arrayLetrasEnviadasBanco[2][1]['D/C | BCP-CL'];
        $arrayLetrasEnviadasBanco['D/C | BCP-CL'][2] = $arrayLetrasEnviadasBanco[1][2]['D/C | BCP-CL'] + $arrayLetrasEnviadasBanco[2][2]['D/C | BCP-CL'];
        $arrayLetrasEnviadasBanco['D/C | BCP-D'][1] = $arrayLetrasEnviadasBanco[1][1]['D/C | BCP-D'] + $arrayLetrasEnviadasBanco[2][1]['D/C | BCP-D'];
        $arrayLetrasEnviadasBanco['D/C | BCP-D'][2] = $arrayLetrasEnviadasBanco[1][2]['D/C | BCP-D'] + $arrayLetrasEnviadasBanco[2][2]['D/C | BCP-D'];
        $arrayLetrasEnviadasBanco['D/C | BBVA-D'][1] = $arrayLetrasEnviadasBanco[1][1]['D/C | BBVA-D'] + $arrayLetrasEnviadasBanco[2][1]['D/C | BBVA-D'];
        $arrayLetrasEnviadasBanco['D/C | BBVA-D'][2] = $arrayLetrasEnviadasBanco[1][2]['D/C | BBVA-D'] + $arrayLetrasEnviadasBanco[2][2]['D/C | BBVA-D'];
        $arrayLetrasEnviadasBanco['R-CL'][1] = $arrayLetrasEnviadasBanco[1][1]['R-CL'] + $arrayLetrasEnviadasBanco[2][1]['R-CL'];
        $arrayLetrasEnviadasBanco['R-CL'][2] = $arrayLetrasEnviadasBanco[1][2]['R-CL'] + $arrayLetrasEnviadasBanco[2][2]['R-CL'];
        
        $pdf->Row($fila);
        $ancho = array(190);
        $pdf->SetWidths($ancho);
        $pdf->fill(false);
        $fila = array('');
        $pdf->Row($fila);
        $ancho = array(40,50, 50, 50);
        $pdf->SetWidths($ancho);
        $pdf->fill(false);
        $fila = array('ZONA GEOGRAFICA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('BANCO', 'TIPO', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BBVA', 'LETRA DESCUENTO', number_format($arrayLetrasEnviadasBanco[2][1]['BBVA'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['BBVA'], 2));
        $pdf->Row($fila);
        $fila = array('BCP', 'LETRA DESCUENTO', number_format($arrayLetrasEnviadasBanco[2][1]['BCP'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['BCP'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BCP', 'COBRANZA LIBRE', number_format($arrayLetrasEnviadasBanco[2][1]['BCP CL'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['BCP CL'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CPA', 'CPA PROVINCIA', number_format($arrayLetrasEnviadasBanco[2][1]['CPA'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['CPA'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BCP-D', 'BCP-D PROVINCIA', number_format($arrayLetrasEnviadasBanco[2][1]['A/C | BCP-D'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['A/C | BCP-D'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BCP-CL', 'BCP-CL PROVINCIA', number_format($arrayLetrasEnviadasBanco[2][1]['A/C | BCP-CL'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['A/C | BCP-CL'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BBVA-D', 'BBVA-D PROVINCIA', number_format($arrayLetrasEnviadasBanco[2][1]['A/C | BBVA-D'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['A/C | BBVA-D'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BCP-CL', 'BCP-CL PROVINCIA', number_format($arrayLetrasEnviadasBanco[2][1]['D/C | BCP-CL'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['D/C | BCP-CL'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BCP-D', 'BCP-D PROVINCIA', number_format($arrayLetrasEnviadasBanco[2][1]['D/C | BCP-D'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['D/C | BCP-D'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BBVA-D', 'BBVA-D PROVINCIA', number_format($arrayLetrasEnviadasBanco[2][1]['D/C | BBVA-D'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['D/C | BBVA-D'], 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('R-CL', 'R-CL PROVINCIA', number_format($arrayLetrasEnviadasBanco[2][1]['R-CL'], 2), number_format($arrayLetrasEnviadasBanco[2][2]['R-CL'], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', '', number_format($LSC_PROVINCIA_SOLES, 2), number_format(($LSC_PROVINCIA_DOLARES), 2));
        
        $pdf->Row($fila);
        $pdf->setxy(10, 82);
        $pdf->MultiCell(40, 5, 'ZONA GEOGRAFICA', 1, 'C', true);
        $pdf->setxy(50, 82);
        $pdf->MultiCell(150, 5, 'LIMA', 1, 'C', false);

        $pdf->setxy(10, 92);
        $pdf->MultiCell(40, 5, 'BBVA', 1, 'C', true);
        $pdf->setxy(10, 97);
        $pdf->MultiCell(40, 10, 'BCP', 1, 'C', true);
        
        $pdf->setxy(10, 107);
        $pdf->MultiCell(40, 5, 'CPA', 1, 'C', true);
        $pdf->MultiCell(40, 15, 'A/C', 1, 'C', true);
        $pdf->MultiCell(40, 15, 'D/C', 1, 'C', true);
        $pdf->MultiCell(40, 5, 'R-CL', 1, 'C', true);     
        $pdf->MultiCell(90, 5, 'TOTAL', 1, 'C', true);

        $pdf->setxy(10, 157);
        $pdf->MultiCell(40, 5, 'ZONA GEOGRAFICA', 1, 'C', true);
        $pdf->setxy(50, 157);
        $pdf->MultiCell(150, 5, 'PROVINCIA', 1, 'C', false);

        $pdf->setxy(10, 167);
        $pdf->MultiCell(40, 5, 'BBVA', 1, 'C', true);
        $pdf->MultiCell(40, 10, 'BCP', 1, 'C', true);
        $pdf->MultiCell(40, 5, 'CPA', 1, 'C', true);
        $pdf->MultiCell(40, 15, 'A/C', 1, 'C', true);
        $pdf->MultiCell(40, 15, 'D/C', 1, 'C', true);
        $pdf->MultiCell(40, 5, 'R-CL', 1, 'C', true);
        $pdf->MultiCell(90, 5, 'TOTAL', 1, 'C', true);

        $pdf->setxy(10, 220);
        $pdf->ln(8);
        $ancho = array(90, 50, 50);
        $pdf->SetWidths($ancho);
        $orientacion = array('L', 'R', 'R');
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        
        $pdf->ln();
        $pdf->Cell(90, 7, "LETRAS SIN ENVIAR AL BANCO");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayLetrasSinEnviarBanco[1][1], 2), number_format($arrayLetrasSinEnviarBanco[1][2], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayLetrasSinEnviarBanco[2][1], 2), number_format($arrayLetrasSinEnviarBanco[2][2], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($arrayLetrasSinEnviarBanco[1][1] + $arrayLetrasSinEnviarBanco[2][1], 2), number_format($arrayLetrasSinEnviarBanco[1][2] + $arrayLetrasSinEnviarBanco[2][2], 2));
        $pdf->Row($fila);
        
        $pdf->ln();
        $pdf->ln();
        $pdf->Cell(90, 7, "LETRAS PROTESTADAS");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayLetrasProtestadas[1][1], 2), number_format($arrayLetrasProtestadas[1][2], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayLetrasProtestadas[2][1], 2), number_format($arrayLetrasProtestadas[2][2], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($arrayLetrasProtestadas[1][1] + $arrayLetrasProtestadas[2][1], 2), number_format($arrayLetrasProtestadas[1][2] + $arrayLetrasProtestadas[2][2], 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS POR VENCER                                              (la sumatoria contempla todos los creditos que van a vencer)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencer[1][1]['TOTAL'], 2), number_format($arrayCreditosVencer[1][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencer[2][1]['TOTAL'], 2), number_format($arrayCreditosVencer[2][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencer[1][1]['TOTAL'] + $arrayCreditosVencer[2][1]['TOTAL']), 2), number_format(($arrayCreditosVencer[1][2]['TOTAL'] + $arrayCreditosVencer[2][2]['TOTAL']), 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS POR VENCER EN 30 DIAS               (contempla creditos que van a vencer entre hoy y 30 dias despues)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencer[1][1][30], 2), number_format($arrayCreditosVencer[1][2][30], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencer[2][1][30], 2), number_format($arrayCreditosVencer[2][2][30], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencer[1][1][30] + $arrayCreditosVencer[2][1][30]), 2), number_format(($arrayCreditosVencer[1][2][30] + $arrayCreditosVencer[2][2][30]), 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS POR VENCER EN 60 DIAS                                              (la sumatoria no contempla los primeros 30 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencer[1][1][60], 2), number_format($arrayCreditosVencer[1][2][60], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencer[2][1][60], 2), number_format($arrayCreditosVencer[2][2][60], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencer[1][1][60] + $arrayCreditosVencer[2][1][60]), 2), number_format(($arrayCreditosVencer[1][2][60] + $arrayCreditosVencer[2][2][60]), 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS POR VENCER EN 90 DIAS                                              (la sumatoria no contempla los primeros 60 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencer[1][1][90], 2), number_format($arrayCreditosVencer[1][2][90], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencer[2][1][90], 2), number_format($arrayCreditosVencer[2][2][90], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencer[1][1][90] + $arrayCreditosVencer[2][1][90]), 2), number_format(($arrayCreditosVencer[1][2][90] + $arrayCreditosVencer[2][2][90]), 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS POR VENCER MAYOR A 90 DIAS                                  (la sumatoria no contempla los primeros 90 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencer[1][1][91], 2), number_format($arrayCreditosVencer[1][2][91], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencer[2][1][91], 2), number_format($arrayCreditosVencer[2][2][91], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencer[1][1][91] + $arrayCreditosVencer[2][1][91]), 2), number_format(($arrayCreditosVencer[1][2][91] + $arrayCreditosVencer[2][2][91]), 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS VENCIDOS                                                               (la sumatoria contempla todos los creditos vencidos)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencidos[1][1]['TOTAL'], 2), number_format($arrayCreditosVencidos[1][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencidos[2][1]['TOTAL'], 2), number_format($arrayCreditosVencidos[2][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($arrayCreditosVencidos[1][1]['TOTAL'] + $arrayCreditosVencidos[2][1]['TOTAL'], 2), number_format($arrayCreditosVencidos[1][2]['TOTAL'] + $arrayCreditosVencidos[2][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS VENCIDOS HACE 30 DIAS                                  (contempla creditos vencidos entre hoy y 30 dias atras)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencidos[1][1][30], 2), number_format($arrayCreditosVencidos[1][2][30], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencidos[2][1][30], 2), number_format($arrayCreditosVencidos[2][2][30], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencidos[1][1][30] + $arrayCreditosVencidos[2][1][30]), 2), number_format($arrayCreditosVencidos[1][2][30] + $arrayCreditosVencidos[2][2][30], 2));
        $pdf->Row($fila);
        $pdf->ln();
       
        $pdf->Cell(90, 7, "CREDITOS VENCIDOS HACE 60 DIAS                                             (la sumatoria no contempla los primeros 30 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencidos[1][1][60], 2), number_format($arrayCreditosVencidos[1][2][60], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencidos[2][1][60], 2), number_format($arrayCreditosVencidos[2][2][60], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencidos[1][1][60] + $arrayCreditosVencidos[2][1][60]), 2), number_format($arrayCreditosVencidos[1][2][60] + $arrayCreditosVencidos[2][2][60], 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS VENCIDOS HACE 90 DIAS                                             (la sumatoria no contempla los primeros 60 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencidos[1][1][90], 2), number_format($arrayCreditosVencidos[1][2][90], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencidos[2][1][90], 2), number_format($arrayCreditosVencidos[2][2][90], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencidos[1][1][90] + $arrayCreditosVencidos[2][1][90]), 2), number_format($arrayCreditosVencidos[1][2][90] + $arrayCreditosVencidos[2][2][90], 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "CREDITOS VENCIDOS MAYOR A 90 DIAS                                       (la sumatoria no contempla los primeros 90 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($arrayCreditosVencidos[1][1][91], 2), number_format($arrayCreditosVencidos[1][2][91], 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($arrayCreditosVencidos[2][1][91], 2), number_format($arrayCreditosVencidos[2][2][91], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($arrayCreditosVencidos[1][1][91] + $arrayCreditosVencidos[2][1][91]), 2), number_format($arrayCreditosVencidos[1][2][91] + $arrayCreditosVencidos[2][2][91], 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "COBRANZA PESADA");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'LIMA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CONTADO', number_format($arrayPesados[40][1][1], 2), number_format($arrayPesados[40][2][1], 2));
        $pdf->Row($fila);
        $fila = array('CREDITO', number_format($arrayPesados[40][1][2], 2), number_format($arrayPesados[40][2][2], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS', number_format($arrayPesados[40][1][3], 2), number_format($arrayPesados[40][2][3], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($arrayPesados[40][1]['protesto'], 2), number_format($arrayPesados[40][2]['protesto'], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL EN LIMA', number_format($arrayPesados[40][1]['TOTAL'], 2), number_format($arrayPesados[40][2]['TOTAL'], 2));
        $pdf->Row($fila);

        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'PROVINCIA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CONTADO', number_format($arrayPesados[48][1][1], 2), number_format($arrayPesados[48][2][1], 2));
        $pdf->Row($fila);
        $fila = array('CREDITO', number_format($arrayPesados[48][1][2], 2), number_format($arrayPesados[48][2][2], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS', number_format($arrayPesados[48][1][3], 2), number_format($arrayPesados[48][2][3], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($arrayPesados[48][1]['protesto'], 2), number_format($arrayPesados[48][2]['protesto'], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL EN PROVINCIA', number_format($arrayPesados[48][1]['TOTAL'], 2), number_format($arrayPesados[48][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $PESADO_CONTADO_SOLES = $arrayPesados[40][1][1] + $arrayPesados[48][1][1];
        $PESADO_CONTADO_DOLARES = $arrayPesados[40][2][1] + $arrayPesados[48][2][1];
        $PESADO_CREDITO_SOLES = $arrayPesados[40][1][2] + $arrayPesados[48][1][2];
        $PESADO_CREDITO_DOLARES = $arrayPesados[40][2][2] + $arrayPesados[48][2][2];
        $PESADO_LETRA_SOLES = $arrayPesados[40][1][3] + $arrayPesados[48][1][3];
        $PESADO_LETRA_DOLARES = $arrayPesados[40][2][3] + $arrayPesados[48][2][3];
        $PESADO_PROTESTO_SOLES = $arrayPesados[40][1]['protesto'] + $arrayPesados[48][1]['protesto'];
        $PESADO_PROTESTO_DOLARES = $arrayPesados[40][2]['protesto'] + $arrayPesados[48][2]['protesto'];
        
        $pdf->fill(true);
        $fila = array('TOTAL PESADO', number_format($arrayPesados[40][1]['TOTAL'] + $arrayPesados[48][1]['TOTAL'], 2), number_format($arrayPesados[40][2]['TOTAL'] + $arrayPesados[48][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "RESUMEN GENERAL");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LETRAS SIN ENVIAR AL BANCO', number_format($arrayLetrasSinEnviarBanco[1][1] + $arrayLetrasSinEnviarBanco[2][1], 2), number_format($arrayLetrasSinEnviarBanco[1][2] + $arrayLetrasSinEnviarBanco[2][2], 2));
        $pdf->Row($fila);
        $fila = array('BANCO CPA', number_format($arrayLetrasEnviadasBanco['CPA'][1], 2), number_format($arrayLetrasEnviadasBanco['CPA'][2], 2));
        $pdf->Row($fila);
        $fila = array('A/C | BCP-D', number_format($arrayLetrasEnviadasBanco['A/C | BCP-D'][1] , 2), number_format($arrayLetrasEnviadasBanco['A/C | BCP-D'][2], 2));
        $pdf->Row($fila);
        $fila = array('A/C | BCP-CL', number_format($arrayLetrasEnviadasBanco['A/C | BCP-CL'][1], 2), number_format($arrayLetrasEnviadasBanco['A/C | BCP-CL'][2], 2));
        $pdf->Row($fila);
        $fila = array('A/C | BBVA-D', number_format($arrayLetrasEnviadasBanco['A/C | BBVA-D'][1], 2), number_format($arrayLetrasEnviadasBanco['A/C | BBVA-D'][2], 2));
        $pdf->Row($fila);
        $fila = array('D/C | BCP-D', number_format($arrayLetrasEnviadasBanco['D/C | BCP-D'][1], 2), number_format($arrayLetrasEnviadasBanco['D/C | BCP-D'][2], 2));
        $pdf->Row($fila);
        $fila = array('D/C | BCP-CL', number_format($arrayLetrasEnviadasBanco['D/C | BCP-CL'][1], 2), number_format($arrayLetrasEnviadasBanco['D/C | BCP-CL'][2], 2));
        $pdf->Row($fila);
        $fila = array('D/C | BBVA-D', number_format($arrayLetrasEnviadasBanco['D/C | BBVA-D'][1], 2), number_format($arrayLetrasEnviadasBanco['D/C | BBVA-D'][2], 2));
        $pdf->Row($fila);
        $fila = array('R-CL', number_format($arrayLetrasEnviadasBanco['R-CL'][1], 2), number_format($arrayLetrasEnviadasBanco['R-CL'][2], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($arrayLetrasProtestadas[1][1] + $arrayLetrasProtestadas[2][1], 2), number_format($arrayLetrasProtestadas[1][2] + $arrayLetrasProtestadas[2][2], 2));
        $pdf->Row($fila);
        $fila = array('CREDITOS VENCIDOS', number_format($arrayCreditosVencidos[1][1]['TOTAL'] + $arrayCreditosVencidos[2][1]['TOTAL'], 2), number_format($arrayCreditosVencidos[1][2]['TOTAL'] + $arrayCreditosVencidos[2][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $fila = array('CREDITOS POR VENCER', number_format($arrayCreditosVencer[1][1]['TOTAL'] + $arrayCreditosVencer[2][1]['TOTAL'], 2), number_format($arrayCreditosVencer[1][2]['TOTAL'] + $arrayCreditosVencer[2][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $fila = array('CONTADO PESADO', number_format($PESADO_CONTADO_SOLES, 2), number_format($PESADO_CONTADO_DOLARES, 2));
        $pdf->Row($fila);
        $fila = array('CREDITO PESADO', number_format($PESADO_CREDITO_SOLES, 2), number_format($PESADO_CREDITO_DOLARES, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PESADO', number_format($PESADO_LETRA_SOLES, 2), number_format($PESADO_LETRA_DOLARES, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS PESADO', number_format($PESADO_PROTESTO_SOLES, 2), number_format($PESADO_PROTESTO_DOLARES, 2));
        $pdf->Row($fila);
        
        $GENERALTOTAL_SOLES = $LSC_LIMA_SOLES + $LSC_PROVINCIA_SOLES + $arrayLetrasSinEnviarBanco[1][1] + $arrayLetrasSinEnviarBanco[2][1] + $arrayLetrasProtestadas[1][1] + $arrayLetrasProtestadas[2][1] + $arrayCreditosVencidos[1][1]['TOTAL'] + $arrayCreditosVencidos[2][1]['TOTAL'] + $arrayCreditosVencer[1][1]['TOTAL'] + $arrayCreditosVencer[2][1]['TOTAL'] + $PESADO_CONTADO_SOLES + $PESADO_CREDITO_SOLES + $PESADO_LETRA_SOLES + $PESADO_PROTESTO_SOLES;
        $GENERALTOTAL_DOLARES = $LSC_LIMA_DOLARES + $LSC_PROVINCIA_DOLARES + $arrayLetrasSinEnviarBanco[1][2] + $arrayLetrasSinEnviarBanco[2][2] + $arrayLetrasProtestadas[1][2] + $arrayLetrasProtestadas[2][2] + $arrayCreditosVencidos[1][2]['TOTAL'] + $arrayCreditosVencidos[2][2]['TOTAL'] + $arrayCreditosVencer[1][2]['TOTAL'] + $arrayCreditosVencer[2][2]['TOTAL'] + $PESADO_CONTADO_DOLARES + $PESADO_CREDITO_DOLARES + $PESADO_LETRA_DOLARES + $PESADO_PROTESTO_DOLARES;
        
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($GENERALTOTAL_SOLES, 2), number_format($GENERALTOTAL_DOLARES, 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "EMPRESA CORPORACION POWER ACOUSTIK");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('EMPRESA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('TIENDA PARURO', number_format($arrayEmpresa[136][1], 2), number_format($arrayEmpresa[136][2], 2));
        $pdf->Row($fila);
        $fila = array('TIENDA PARURO (C.PUSE)', number_format($arrayEmpresa[241][1], 2), number_format($arrayEmpresa[241][2], 2));
        $pdf->Row($fila);/*
        $fila = array('TIENDA PARURO 3', number_format($arrayEmpresa[391][1], 2), number_format($arrayEmpresa[391][2], 2));
        $pdf->Row($fila);*/
        $fila = array('TIENDA PARURO 4', number_format($arrayEmpresa[445][1], 2), number_format($arrayEmpresa[445][2], 2));
        $pdf->Row($fila);/*
        $fila = array('TIENDA PARURO 5', number_format($arrayEmpresa[540][1], 2), number_format($arrayEmpresa[540][2], 2));
        $pdf->Row($fila);*/
        $fila = array('USO EXCLUSIVO DE LA EMPRESA', number_format($arrayEmpresa[152][1], 2), number_format($arrayEmpresa[152][2], 2));
        $pdf->Row($fila);
        $fila = array('PRESTAMO AL PERSONAL ACTUAL', number_format($arrayEmpresa[184][1], 2), number_format($arrayEmpresa[184][2], 2));
        $pdf->Row($fila);
        $fila = array('PRESTAMO AL PERSONAL ANTIGUO', number_format($arrayEmpresa[558][1], 2), number_format($arrayEmpresa[558][2], 2));
        $pdf->Row($fila);
        $fila = array('PRESTAMO AL VENDEDOR ACTUAL', number_format($arrayEmpresa[264][1], 2), number_format($arrayEmpresa[264][2], 2));
        $pdf->Row($fila);
        $fila = array('PRESTAMO AL VENDEDOR ANTIGUO', number_format($arrayEmpresa[557][1], 2), number_format($arrayEmpresa[557][2], 2));
        $pdf->Row($fila);
        $fila = array('MUESTRAS GENERAL ACTUAL', number_format($arrayEmpresa[59][1], 2), number_format($arrayEmpresa[59][2], 2));
        $pdf->Row($fila);
        $fila = array('MUESTRAS GENERAL ANTIGUO', number_format($arrayEmpresa[554][1], 2), number_format($arrayEmpresa[554][2], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($arrayEmpresa[1]['TOTAL'], 2), number_format($arrayEmpresa[2]['TOTAL'], 2));
        $pdf->Row($fila);
       
        $pdf->ln();
        $pdf->Cell(90, 7, "RESUMEN INCOBRABLES");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'LIMA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CONTADO', number_format($arrayIncobrables[30][1][1], 2), number_format($arrayIncobrables[30][2][1], 2));
        $pdf->Row($fila);
        $fila = array('CREDITO', number_format($arrayIncobrables[30][1][2], 2), number_format($arrayIncobrables[30][2][2], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS', number_format($arrayIncobrables[30][1][3], 2), number_format($arrayIncobrables[30][2][3], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($arrayIncobrables[30][1]['protesto'], 2), number_format($arrayIncobrables[30][2]['protesto'], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL EN LIMA', number_format($arrayIncobrables[30][1]['TOTAL'], 2), number_format($arrayIncobrables[30][2]['TOTAL'], 2));
        $pdf->Row($fila);
                
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'PROVINCIA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CONTADO', number_format($arrayIncobrables[28][1][1], 2), number_format($arrayIncobrables[28][2][1], 2));
        $pdf->Row($fila);
        $fila = array('CREDITO', number_format($arrayIncobrables[28][1][2], 2), number_format($arrayIncobrables[28][2][2], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS', number_format($arrayIncobrables[28][1][3], 2), number_format($arrayIncobrables[28][2][3], 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($arrayIncobrables[28][1]['protesto'], 2), number_format($arrayIncobrables[28][2]['protesto'], 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL EN PROVINCIA', number_format($arrayIncobrables[28][1]['TOTAL'], 2), number_format($arrayIncobrables[28][2]['TOTAL'], 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->fill(true);
        $fila = array('TOTAL INCOBRABLE', number_format($arrayIncobrables[28][1]['TOTAL'] + $arrayIncobrables[30][1]['TOTAL'], 2), number_format($arrayIncobrables[28][2]['TOTAL'] + $arrayIncobrables[30][2]['TOTAL'], 2));
        $pdf->Row($fila);
       
        $pdf->ln();
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function resumencobranzas1() {
        set_time_limit(1500);
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-d');

        $reporte = $this->AutoLoadModel('reporte');
        $detalleordencobroingreso = $this->AutoLoadModel('detalleordencobroingreso');
        $ordenGasto=$this->AutoLoadModel('ordengasto');

        $dataL = $reporte->resumenLetras($txtFechaInicio, $txtFechaFinal);
        $cant = count($dataL);
        $sLSLS = 0.0; //
        $sLSLD = 0.0;
        $sLSPS = 0.0;
        $sLSPD = 0.0;

        $sLCLSBBVA = 0.0; // suma de bbva de lima y en soles
        $sLCLDBBVA = 0.0; // suma de bbva de lima y en dolares

        $sLCPSBBVA = 0.0; // suma de bbva de Provincia y en soles
        $sLCPDBBVA = 0.0; // suma de bbva de Provincia y en dolares

        $sLCLS = 0.0;
        $sLCLD = 0.0;
        $sLCPS = 0.0;
        $sLCPD = 0.0;
        
        $sLCLSCPA = 0.0; // suma de CPA de lima y en soles
        $sLCLDCPA = 0.0; // suma de CPA de lima y en dolares
        $sLCPSCPA = 0.0; // suma de CPA de Provincia y en soles
        $sLCPDCPA = 0.0; // suma de CPA de Provincia y en dolares
       
        $sLCLS_AC_BCP_D = 0.0; // suma de R-BBVA de lima y en soles
        $sLCLD_AC_BCP_D = 0.0; // suma de R-BBVA de lima y en dolares
        $sLCPS_AC_BCP_D = 0.0; // suma de R-BBVA de Provincia y en soles
        $sLCPD_AC_BCP_D = 0.0; // suma de R-BBVA de Provincia y en dolares
        
        $sLCLS_AC_BCP_CL = 0.0; // suma de R-BBVA de lima y en soles
        $sLCLD_AC_BCP_CL = 0.0; // suma de R-BBVA de lima y en dolares
        $sLCPS_AC_BCP_CL = 0.0; // suma de R-BBVA de Provincia y en soles
        $sLCPD_AC_BCP_CL = 0.0; // suma de R-BBVA de Provincia y en dolares
        
        $sLCLS_AC_BBVA_D = 0.0; // suma de R-BBVA de lima y en soles
        $sLCLD_AC_BBVA_D = 0.0; // suma de R-BBVA de lima y en dolares
        $sLCPS_AC_BBVA_D = 0.0; // suma de R-BBVA de Provincia y en soles
        $sLCPD_AC_BBVA_D = 0.0; // suma de R-BBVA de Provincia y en dolares
        
        $sLCLS_DC_BCP_D = 0.0; // suma de R-BBVA de lima y en soles
        $sLCLD_DC_BCP_D = 0.0; // suma de R-BBVA de lima y en dolares
        $sLCPS_DC_BCP_D = 0.0; // suma de R-BBVA de Provincia y en soles
        $sLCPD_DC_BCP_D = 0.0; // suma de R-BBVA de Provincia y en dolares
        
        $sLCLS_DC_BCP_CL = 0.0; // suma de R-BBVA de lima y en soles
        $sLCLD_DC_BCP_CL = 0.0; // suma de R-BBVA de lima y en dolares
        $sLCPS_DC_BCP_CL = 0.0; // suma de R-BBVA de Provincia y en soles
        $sLCPD_DC_BCP_CL = 0.0; // suma de R-BBVA de Provincia y en dolares
        
        $sLCLS_DC_BBVA_D = 0.0; // suma de R-BBVA de lima y en soles
        $sLCLD_DC_BBVA_D = 0.0; // suma de R-BBVA de lima y en dolares
        $sLCPS_DC_BBVA_D = 0.0; // suma de R-BBVA de Provincia y en soles
        $sLCPD_DC_BBVA_D = 0.0; // suma de R-BBVA de Provincia y en dolares
        
        $sLCLS_R_CL = 0.0; // suma de R-CL de lima y en soles
        $sLCLD_R_CL = 0.0; // suma de R-CL de lima y en dolares
        $sLCPS_R_CL = 0.0; // suma de R-CL de Provincia y en soles
        $sLCPD_R_CL = 0.0; // suma de R-CL de Provincia y en dolares
        
        $SclD = 0.0;
        $SDsctoD = 0.0;

        for($i = 0; $i<$cant; $i++) {
            if ($dataL[$i]['recepcionletras']!='PA') {
                //Sin PA
                if ($dataL[$i]['idpadrec'] == 1) {
                    //de lima
                    if ($dataL[$i]['idmoneda'] == 1) {
                        //soles:
                        $sLSLS += $dataL[$i]['saldodoc'];
                        //evaluacion
                        if ($dataL[$i]['evaluacion'] == 1) {
                            $sLSLS_eva+=$dataL[$i]['saldodoc'];
                        }
                    } else if ($dataL[$i]['idmoneda'] == 2){
                        //dólares
                        $sLSLD += $dataL[$i]['saldodoc'];
                        //evaluacion
                        if ($dataL[$i]['evaluacion'] == 1) {
                            $sLSLD_eva+=$dataL[$i]['saldodoc'];
                        }
                    }
                }else {
                    //de provincia
                    if ($dataL[$i]['idmoneda'] == 1) {
                        //soles:
                        $sLSPS += $dataL[$i]['saldodoc'];
                        //evaluacion
                        if ($dataL[$i]['evaluacion'] == 1) {
                            $sLSPS_eva+=$dataL[$i]['saldodoc'];
                        }
                    } else if ($dataL[$i]['idmoneda'] == 2){
                        //dólares
                        $sLSPD += $dataL[$i]['saldodoc'];
                        //evaluacion
                        if ($dataL[$i]['evaluacion'] == 1) {
                            $sLSPD_eva+=$dataL[$i]['saldodoc'];
                        }
                    }
                }
            } else {
                // con PA
                if ($dataL[$i]['idpadrec'] == 1) {
                    //de Lima
                    if ($dataL[$i]['idmoneda'] == 1) {
                        // en soles
                        if ($dataL[$i]['numerounico'] == 'BBVA') {
                            //Está en BBVA
                            $sLCLSBBVA += $dataL[$i]['saldodoc']; // NADA
                        } else {
                            //Está en BCP
                            // $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            if ($dataL[$i]['numerounico'] == 'BCP') {
                                $SDsctoSLima += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'CPA'){
                                //Estás en CPA
                                $sLCLSCPA += $dataL[$i]['saldodoc'];
                            } else if($dataL[$i]['numerounico'] == 'A/C | BCP-D'){
                                $sLCLS_AC_BCP_D += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'A/C | BCP-CL'){
                                //Estás en R-BBVA
                                $sLCLS_AC_BCP_CL += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'A/C | BBVA-D'){
                                //Estás en R-BBVA
                                $sLCLS_AC_BBVA_D += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BCP-CL'){
                                //Estás en R-BBVA
                                $sLCLS_DC_BCP_CL += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            }  else if($dataL[$i]['numerounico'] == 'D/C | BCP-D'){
                                //Estás en R-BBVA
                                $sLCLS_DC_BCP_D += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BBVA-D'){
                                //Estás en R-BBVA
                                $sLCLS_DC_BBVA_D += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'R-CL'){
                                //Estás en R-BBVA
                                $sLCLS_R_CL += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else{
                                $SclSLima += $dataL[$i]['saldodoc'];
                                $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            }
                        }
                    } else {
                        // en dolares
                        if ($dataL[$i]['numerounico'] == 'BBVA') {
                            //Está en BBVA
                            $sLCLDBBVA += $dataL[$i]['saldodoc'];
                        } else {
                            //Está en BCP
                            //$sLCLD += $dataL[$i]['saldodoc']; //CL DOLARES
                            if ($dataL[$i]['numerounico'] == 'BCP') {
                                $SDsctoDLima += $dataL[$i]['saldodoc'];
                                $sLCLD += $dataL[$i]['saldodoc']; //CL DOLARES
                            } else if($dataL[$i]['numerounico'] == 'CPA'){
                                //Estás en CPA
                                $sLCLDCPA += $dataL[$i]['saldodoc'];    
                            } else if($dataL[$i]['numerounico'] == 'A/C | BCP-D'){
                                $sLCLD_AC_BCP_D += $dataL[$i]['saldodoc'];
                                $sLCLD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'A/C | BCP-CL'){
                                //Estás en R-BBVA
                                $sLCLD_AC_BCP_CL += $dataL[$i]['saldodoc'];
                                $sLCLD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'A/C | BBVA-D'){
                                //Estás en R-BBVA
                                $sLCLD_AC_BBVA_D += $dataL[$i]['saldodoc'];
                                $sLCLD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BCP-CL'){
                                //Estás en R-BBVA
                                $sLCLD_DC_BCP_CL += $dataL[$i]['saldodoc'];
                                $sLCLD += $dataL[$i]['saldodoc'];//CL SOLES
                            }  else if($dataL[$i]['numerounico'] == 'D/C | BCP-D'){
                                //Estás en R-BBVA
                                $sLCLD_DC_BCP_D += $dataL[$i]['saldodoc'];
                                $sLCLD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BBVA-D'){
                                //Estás en R-BBVA
                                $sLCLD_DC_BBVA_D += $dataL[$i]['saldodoc'];
                                $sLCLD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'R-CL'){
                                //Estás en R-BBVA
                                $sLCLD_R_CL += $dataL[$i]['saldodoc'];
                                $sLCLD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else{
                                $SclDLima += $dataL[$i]['saldodoc'];    
                                $sLCLD += $dataL[$i]['saldodoc']; //CL DOLARES
                            }
                        }
                    }
                } else {
                    //provincia
                    if ($dataL[$i]['idmoneda'] == 1) {
                        // en soles
                        if ($dataL[$i]['numerounico'] == 'BBVA') {
                            //Está en BBVA
                            $sLCPSBBVA += $dataL[$i]['saldodoc'];
                        } else {
                            //Está en BCP
                            //$sLCPS += $dataL[$i]['saldodoc'];
                            if ($dataL[$i]['numerounico'] == 'BCP') {
                                $SDsctoSProvincia += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];
                            } else if ($dataL[$i]['numerounico'] == 'CPA') {
                                //Estás en CPA
                                $sLCPSCPA += $dataL[$i]['saldodoc'];
                            } else if($dataL[$i]['numerounico'] == 'A/C | BCP-D'){
                                $sLCPS_AC_BCP_D += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'A/C | BCP-CL'){
                                //Estás en R-BBVA
                                $sLCPS_AC_BCP_CL += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'A/C | BBVA-D'){
                                //Estás en R-BBVA
                                $sLCPS_AC_BBVA_D += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BCP-CL'){
                                //Estás en R-BBVA
                                $sLCPS_DC_BCP_CL += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];//CL SOLES
                            }  else if($dataL[$i]['numerounico'] == 'D/C | BCP-D'){
                                //Estás en R-BBVA
                                $sLCPS_DC_BCP_D += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BBVA-D'){
                                //Estás en R-BBVA
                                $sLCPS_DC_BBVA_D += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'R-CL'){
                                //Estás en R-BBVA
                                $sLCPS_R_CL += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];//CL SOLES
                            } else {
                                $SclSProvincia += $dataL[$i]['saldodoc'];
                                $sLCPS += $dataL[$i]['saldodoc'];
                            }
                        }
                    } else {
                        if ($dataL[$i]['numerounico'] == 'BBVA') {
                            //Está en BBVA
                            $sLCPDBBVA += $dataL[$i]['saldodoc'];
                        } else {
                            //Está en BCP
                            //$sLCPD += $dataL[$i]['saldodoc'];
                            if ($dataL[$i]['numerounico'] == 'BCP') {
                                $SDsctoDProvincia += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];
                            } else if ($dataL[$i]['numerounico'] == 'CPA') {
                                //Estás en CPA
                                $sLCPDCPA += $dataL[$i]['saldodoc'];
                            } else if($dataL[$i]['numerounico'] == 'A/C | BCP-D'){
                                $sLCPD_AC_BCP_D += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'A/C | BCP-CL'){
                                //Estás en R-BBVA
                                $sLCPD_AC_BCP_CL += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'A/C | BBVA-D'){
                                //Estás en R-BBVA
                                $sLCPD_AC_BBVA_D += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BCP-CL'){
                                //Estás en R-BBVA
                                $sLCPD_DC_BCP_CL += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BCP-D'){
                                //Estás en R-BBVA
                                $sLCPD_DC_BCP_D += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'D/C | BBVA-D'){
                                //Estás en R-BBVA
                                $sLCPD_DC_BBVA_D += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else if($dataL[$i]['numerounico'] == 'R-CL'){
                                //Estás en R-BBVA
                                $sLCPD_R_CL += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];//CL SOLES
                            } else {
                                $SclDProvincia += $dataL[$i]['saldodoc'];
                                $sLCPD += $dataL[$i]['saldodoc'];
                            }
                        }
                    }
                }
            }
        }

        $dataLP = $reporte->detalladoLetrasProtestadas($txtFechaInicio, $txtFechaFinal);
        $cant = count($dataLP);
        $sLPLS = 0.0;
        $sLPLD = 0.0;
        $sLPPS = 0.0;
        $sLPPD = 0.0;
        for($i = 0; $i<$cant; $i++) {
            //$pagosCredito = $detalleordencobroingreso->pagos($dataLP[$i]['iddetalleordencobro']);
            if ($dataLP[$i]['idpadrec'] == 1) {
                if ($dataLP[$i]['idmoneda'] == 1) {
                    $sLPLS += $dataLP[$i]['saldodoc'];// - $pagosCredito[0]['suma'];
                } else {
                    $sLPLD += $dataLP[$i]['saldodoc'];// - $pagosCredito[0]['suma'];
                }
            } else {
                if ($dataLP[$i]['idmoneda'] == 1) {
                    $sLPPS += $dataLP[$i]['saldodoc'];// - $pagosCredito[0]['suma'];
                } else {
                    $sLPPD += $dataLP[$i]['saldodoc'];// - $pagosCredito[0]['suma'];
                }
            }
        }

        $dataEmpresas = $reporte->resumenEmpresas($txtFechaInicio,$txtFechaFinal);
        $cant = count($dataEmpresas);
        $idOrden=0;
        $sTPFS = 0.0;
        $sTPFD = 0.0;
        $sTPPS = 0.0;
        $sTPPD = 0.0;
        $sTP3S = 0.0;
        $sTP3D = 0.0;
        $sTP4S = 0.0;
        $sTP4D = 0.0;
        $sUEES = 0.0;
        $sUEED = 0.0;
        $sPAPS = 0.0;
        $sPAPD = 0.0;
        $sPAVS = 0.0;
        $sPAVD = 0.0;
        $sMS = 0.0;
        $sMD = 0.0;
        for ($i = 0; $i < $cant; $i++) {
            if ($idOrden!=$dataEmpresas[$i]['idordenventa']) {
                $idOrden=$dataEmpresas[$i]['idordenventa'];
                $importe=$ordenGasto->totalGuia($dataEmpresas[$i]['idordenventa']);
                if ($dataEmpresas[$i]['idvendedor'] == '136') {
                    if ($dataEmpresas[$i]['idmoneda'] == 1) {
                        $sTPFS += $importe - $dataEmpresas[$i]['importepagado'];
                    } else {
                        $sTPFD += $importe - $dataEmpresas[$i]['importepagado'];
                    }
                } else if ($dataEmpresas[$i]['idvendedor'] == '241') {
                    if ($dataEmpresas[$i]['idmoneda'] == 1) {
                        $sTPPS += $importe - $dataEmpresas[$i]['importepagado'];
                    } else {
                        $sTPPD += $importe - $dataEmpresas[$i]['importepagado'];
                    }
                } else if ($dataEmpresas[$i]['idvendedor'] == '152') {
                    if ($dataEmpresas[$i]['idmoneda'] == 1) {
                        $sUEES += $importe - $dataEmpresas[$i]['importepagado'];
                    } else {
                        $sUEED += $importe - $dataEmpresas[$i]['importepagado'];
                    }
                } else if ($dataEmpresas[$i]['idvendedor'] == '184') {
                    if ($dataEmpresas[$i]['idmoneda'] == 1) {
                        $sPAPS += $importe - $dataEmpresas[$i]['importepagado'];
                    } else {
                        $sPAPD += $importe - $dataEmpresas[$i]['importepagado'];
                    }
                } else if ($dataEmpresas[$i]['idvendedor'] == '264') {
                    if ($dataEmpresas[$i]['idmoneda'] == 1) {
                        $sPAVS += $importe - $dataEmpresas[$i]['importepagado'];
                    } else {
                        $sPAVD += $importe - $dataEmpresas[$i]['importepagado'];
                    }
                } else if ($dataEmpresas[$i]['idvendedor'] == '59') {
                    if ($dataEmpresas[$i]['idmoneda'] == 1) {
                        $sMS += $importe - $dataEmpresas[$i]['importepagado'];
                    } else {
                        $sMD += $importe - $dataEmpresas[$i]['importepagado'];
                    }
                } else if ($dataEmpresas[$i]['idvendedor'] == '391') {
                    if ($dataEmpresas[$i]['idmoneda'] == 1) {
                        $sTP3S += $importe - $dataEmpresas[$i]['importepagado'];
                    } else {
                        $sTP3D += $importe - $dataEmpresas[$i]['importepagado'];
                    }
                } else if ($dataEmpresas[$i]['idvendedor'] == '445') {
                    if ($dataEmpresas[$i]['idmoneda'] == 1) {
                        $sTP4S += $importe - $dataEmpresas[$i]['importepagado'];
                    } else {
                        $sTP4D += $importe - $dataEmpresas[$i]['importepagado'];
                    }
                }
            }
        }

        $dataCreditos = $reporte->resumenDetalladoCreditos($txtFechaInicio,$txtFechaFinal, "", "", "", "", "", "", "", "");
        $cant = count($dataCreditos);
        $idOrden=$dataCreditos[-1]['idordenventa'];
        $sCLS = 0.0;
        $sCLD = 0.0;
        $sCPS = 0.0;
        $sCPD = 0.0;

        $sCLSporVencer = 0.0;
        $sCLDporVencer = 0.0;
        $sCPSporVencer = 0.0;
        $sCPDporVencer = 0.0;

        $sCLSporVencer30 = 0.0;
        $sCLDporVencer30 = 0.0;
        $sCPSporVencer30 = 0.0;
        $sCPDporVencer30 = 0.0;

        $sCLSporVencer60 = 0.0;
        $sCLDporVencer60 = 0.0;
        $sCPSporVencer60 = 0.0;
        $sCPDporVencer60 = 0.0;

        $sCLSporVencer90 = 0.0;
        $sCLDporVencer90 = 0.0;
        $sCPSporVencer90 = 0.0;
        $sCPDporVencer90 = 0.0;

        $sCLSporVencermas90 = 0.0;
        $sCLDporVencermas90 = 0.0;
        $sCPSporVencermas90 = 0.0;
        $sCPDporVencermas90 = 0.0;

        $sCLS30=0.00;
        $sCLD30=0.00;
        $sCPS30=0.00;
        $sCPD30=0.00;

        $sCLS60 = 0.0;
        $sCLD60 = 0.0;
        $sCPS60 = 0.0;
        $sCPD60 = 0.0;

        $sCLS90 = 0.0;
        $sCLD90 = 0.0;
        $sCPS90 = 0.0;
        $sCPD90 = 0.0;

        $sCLSmas91 = 0.0;
        $sCLDmas91 = 0.0;
        $sCPSmas91 = 0.0;
        $sCPDmas91 = 0.0;

        $diaActual = date('Y-m-d');
        for ($i = 0; $i < $cant; $i++) {
            
            $tempRespuesta = ($reporte->cantidad_dias_entre_dos_fechas($dataCreditos[$i]['fvencimiento'],$diaActual))*-1;
            if ($dataCreditos[$i]['idpadrec'] == 1) {
                if ($dataCreditos[$i]['idmoneda'] == 1) {
                    if ($tempRespuesta>=0) {
                        $sCLSporVencer += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=0&&$tempRespuesta<=30) {
                        $sCLSporVencer30 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=31&&$tempRespuesta<=60) {
                        $sCLSporVencer60 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=61&&$tempRespuesta<=90) {
                        $sCLSporVencer90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=91) {
                        $sCLSporVencermas90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-30&&$tempRespuesta<0) {
                        $sCLS30 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-60&&$tempRespuesta<=-31) {
                        $sCLS60 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-90&&$tempRespuesta<=-61) {
                        $sCLS90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta<=-91&&$tempRespuesta<0) {
                        $sCLSmas91 += $dataCreditos[$i]['saldodoc'];
                    }
                    //$sCLS += $dataCreditos[$i]['saldodoc'];
                } else {
                    if ($tempRespuesta>=0) {
                        $sCLDporVencer += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=0&&$tempRespuesta<=30) {
                        $sCLDporVencer30 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=31&&$tempRespuesta<=60) {
                        $sCLDporVencer60 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=61&&$tempRespuesta<=90) {
                        $sCLDporVencer90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=91) {
                        $sCLDporVencermas90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-30&&$tempRespuesta<0) {
                        $sCLD30 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-60&&$tempRespuesta<=-31) {
                        $sCLD60 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-90&&$tempRespuesta<=-61) {
                        $sCLD90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta<=-91&&$tempRespuesta<0) {
                        $sCLDmas91 += $dataCreditos[$i]['saldodoc'];
                    }
                    //$sCLD += $dataCreditos[$i]['saldodoc'];
                }
            } else {
                if ($dataCreditos[$i]['idmoneda'] == 1) {
                    if ($tempRespuesta>=0) {
                        $sCPSporVencer += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=0&&$tempRespuesta<=30) {
                        $sCPSporVencer30 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=31&&$tempRespuesta<=60) {
                        $sCPSporVencer60 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=61&&$tempRespuesta<=90) {
                        $sCPSporVencer90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=91) {
                        $sCPSporVencermas90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-30&&$tempRespuesta<0) {
                        $sCPS30 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-60&&$tempRespuesta<=-31) {
                        $sCPS60 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-90&&$tempRespuesta<=-61) {
                        $sCPS90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta<=-91&&$tempRespuesta<0) {
                        $sCPSmas91 += $dataCreditos[$i]['saldodoc'];
                    }
                    //$sCLS += $dataCreditos[$i]['saldodoc'];
                } else {
                    if ($tempRespuesta>=0) {
                        $sCPDporVencer += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=0&&$tempRespuesta<=30) {
                        $sCPDporVencer30 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=31&&$tempRespuesta<=60) {
                        $sCPDporVencer60 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=61&&$tempRespuesta<=90) {
                        $sCPDporVencer90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=91) {
                        $sCPDporVencermas90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-30&&$tempRespuesta<0) {
                        $sCPD30 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-60&&$tempRespuesta<=-31) {
                        $sCPD60 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta>=-90&&$tempRespuesta<=-61) {
                        $sCPD90 += $dataCreditos[$i]['saldodoc'];
                    }
                    if ($tempRespuesta<=-91&&$tempRespuesta<0) {
                        $sCPDmas91 += $dataCreditos[$i]['saldodoc'];
                    }

                    //$sCLD += $dataCreditos[$i]['saldodoc'];
                }
            }
            //}
        }
        
        $dataIncobrables = $reporte->resumenIncobrables($txtFechaInicio, $txtFechaFinal);
        $cantI = count($dataIncobrables);
        
        $sIProvCont = 0;
        $dIProvCont = 0;
        $sILimCont = 0;
        $dILimvCont = 0;
        
        $sIProvCredi = 0;
        $dIProvCredi = 0;
        $sILimCredi = 0;
        $dILimvCredi = 0;
        
        $sIProvLet = 0;
        $dIProvLet = 0;
        $sILimLet = 0;
        $dILimvLet = 0;
        
        $sIProvLetPro = 0;
        $dIProvLetPro = 0;
        $sILimLetPro = 0;
        $dILimvLetPro = 0;
        //formacobro
        
        
        for ($i = 0; $i < $cantI; $i++) {
            $dataIncobrables[$i]['referencia1'] = strtoupper($dataIncobrables[$i]['referencia1']);
            $dataIncobrables[$i]['referencia2'] = strtoupper($dataIncobrables[$i]['referencia2']);
            if ($dataIncobrables[$i]['idcategoria'] == 28) {
                if ($dataIncobrables[$i]['idmoneda'] == 1) {
                    if ($dataIncobrables[$i]['formacobro'] == 1) {
                        $sIProvCont += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 2 && $dataIncobrables[$i]['referencia1'] != 'P' && $dataIncobrables[$i]['referencia2'] != 'P') {
                        $sIProvCredi += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 2 && ($dataIncobrables[$i]['referencia1'] == 'P' || $dataIncobrables[$i]['referencia2'] == 'P')) {
                        $sIProvLetPro += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 3) {
                        $sIProvLet += $dataIncobrables[$i]['saldodoc'];
                    }
                } else {
                    if ($dataIncobrables[$i]['formacobro'] == 1) {
                        $dIProvCont += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 2 && $dataIncobrables[$i]['referencia1'] != 'P' && $dataIncobrables[$i]['referencia2'] != 'P') {
                        $dIProvCredi += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 2 && ($dataIncobrables[$i]['referencia1'] == 'P' || $dataIncobrables[$i]['referencia2'] == 'P')) {
                        $dIProvLetPro += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 3) {
                        $dIProvLet += $dataIncobrables[$i]['saldodoc'];
                    }
                }
            } else if ($dataIncobrables[$i]['idcategoria'] == 30) {
                if ($dataIncobrables[$i]['idmoneda'] == 1) {
                    if ($dataIncobrables[$i]['formacobro'] == 1) {
                        $sILimCont += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 2 && $dataIncobrables[$i]['referencia1'] != 'P' && $dataIncobrables[$i]['referencia2'] != 'P') {
                        $sILimCredi += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 2 && ($dataIncobrables[$i]['referencia1'] == 'P' || $dataIncobrables[$i]['referencia2'] == 'P')) {
                        $sILimLetPro += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 3) {
                        $sILimLet += $dataIncobrables[$i]['saldodoc'];
                    }
                } else {
                    if ($dataIncobrables[$i]['formacobro'] == 1) {
                        $dILimvCont += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 2 && $dataIncobrables[$i]['referencia1'] != 'P' && $dataIncobrables[$i]['referencia2'] != 'P') {
                        $dILimvCredi += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 2 && ($dataIncobrables[$i]['referencia1'] == 'P' || $dataIncobrables[$i]['referencia2'] == 'P')) {
                        $dILimvLetPro += $dataIncobrables[$i]['saldodoc'];
                    } else if ($dataIncobrables[$i]['formacobro'] == 3) {
                        $dILimvLet += $dataIncobrables[$i]['saldodoc'];
                    }
                }
            }
        }
        
        $dataPesados = $reporte->resumenPesados($txtFechaInicio, $txtFechaFinal);
        $cantPes = count($dataPesados);

        $sContLimPes = 0;
        $dContLimPes = 0;
        $sCredLimPes = 0;
        $dCredLimPes = 0;
        $sLetLimPes = 0;
        $dLetLimPes = 0;
        $sLetProLimPes = 0;
        $dLetProLimPes = 0;
        
        $sContProvPes = 0;
        $dContProvPes = 0;
        $sCredProvPes = 0;
        $dCredProvPes = 0;
        $sLetProvPes = 0;
        $dLetProvPes = 0;
        $sLetProProvPes = 0;
        $dLetProProvPes = 0;

        for ($i = 0; $i < $cantPes; $i++) {
            if ($dataPesados[$i]['idpadrec'] == 40) {
                if ($dataPesados[$i]['idmoneda'] == 1) {
                    if ($dataPesados[$i]['formacobro'] == 1) {
                        $sContLimPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 2 && $dataPesados[$i]['referencia1'] != 'P' && $dataPesados[$i]['referencia2'] != 'P') {
                        $sCredLimPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 2 && ($dataPesados[$i]['referencia1'] == 'P' || $dataPesados[$i]['referencia2'] == 'P')) {
                        $sLetProLimPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 3) {
                        $sLetLimPes += $dataPesados[$i]['saldodoc'];
                    }
                } else {
                    if ($dataPesados[$i]['formacobro'] == 1) {
                        $dContLimPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 2 && $dataPesados[$i]['referencia1'] != 'P' && $dataPesados[$i]['referencia2'] != 'P') {
                        $dCredLimPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 2 && ($dataPesados[$i]['referencia1'] == 'P' || $dataPesados[$i]['referencia2'] == 'P')) {
                        $dLetProLimPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 3) {
                        $dLetLimPes += $dataPesados[$i]['saldodoc'];
                    }
                }
            } else if ($dataPesados[$i]['idpadrec'] == 48) {
                if ($dataPesados[$i]['idmoneda'] == 1) {
                    if ($dataPesados[$i]['formacobro'] == 1) {
                        $sContProvPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 2 && $dataPesados[$i]['referencia1'] != 'P' && $dataPesados[$i]['referencia2'] != 'P') {
                        $sCredProvPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 2 && ($dataPesados[$i]['referencia1'] == 'P' || $dataPesados[$i]['referencia2'] == 'P')) {
                        $sLetProProvPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 3) {
                        $sLetProvPes += $dataPesados[$i]['saldodoc'];
                    }
                } else {
                    if ($dataPesados[$i]['formacobro'] == 1) {
                        $dContProvPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 2 && $dataPesados[$i]['referencia1'] != 'P' && $dataPesados[$i]['referencia2'] != 'P') {
                        $dCredProvPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 2 && ($dataPesados[$i]['referencia1'] == 'P' || $dataPesados[$i]['referencia2'] == 'P')) {
                        $dLetProProvPes += $dataPesados[$i]['saldodoc'];
                    } else if ($dataPesados[$i]['formacobro'] == 3) {
                        $dLetProvPes += $dataPesados[$i]['saldodoc'];
                    }
                }
            }
        }
        
        $sCLS = $sCLS30+$sCLS60+$sCLS90+$sCLSmas91;
        $sCLD = $sCLD30+$sCLD60+$sCLD90+$sCLDmas91;

        $sCPS = $sCPS30+$sCPS60+$sCPS90+$sCPSmas91;
        $sCPD = $sCPD30+$sCPD60+$sCPD90+$sCPDmas91;

        //$sCLS -= ($sTPFS + $sTPPS + $sUEES + $sPAPS + $sPAVS + $sMS);
        //$sCLD -= ($sTPFD + $sTPPD + $sUEED + $sPAPD + $sPAVD + $sMD);

        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 10);
        $ancho = array(90, 50, 50);
        $pdf->SetWidths($ancho);

        $pdf->_fecha = (!empty($txtFechaInicio) ? $txtFechaInicio . ' - ' : '') . $txtFechaFinal;
        $pdf->_titulo = "RESUMEN COBRANZAS";
        $pdf->_datoPie = 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.4);
        $orientacion = array('C','L', 'R', 'R');
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $ancho = array(40,50, 50, 50);
        $pdf->SetWidths($ancho);

        //INICIO DE CAMBIO:
        $sLCS = $sLCLS + $sLCPS;
        $sLCD = $sLCLD + $sLCPD;
        $sLSSBBVA = $sLCLSBBVA + $sLCPSBBVA;
        $sLSDBBVA = $sLCLDBBVA + $sLCPDBBVA;
        $totalesS = $sLCS + $sLSSBBVA;
        $totalesD = $sLCD + $sLSDBBVA;
        $pdf->ln();
        $pdf->Cell(9, 7, "LETRAS ENVIADAS AL BANCO");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('BANCO','ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BCP','LIMA', number_format($sLCLS, 2), number_format($sLCLD, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BCP','PROVINCIA', number_format($sLCPS, 2), number_format($sLCPD, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('BCP','TOTAL', number_format($sLCS, 2), number_format($sLCD, 2));
        $pdf->Row($fila);
        $pdf->fill(false); // cLCLSBBVA
        $fila = array('BBVA','LIMA', number_format($sLCLSBBVA, 2), number_format($sLCLDBBVA, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BBVA','PROVINCIA ', number_format($sLCPSBBVA, 2), number_format($sLCPDBBVA, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('BBVA','TOTAL', number_format($sLSSBBVA, 2), number_format($sLSDBBVA, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL','', number_format($totalesS, 2), number_format($totalesD, 2));
        $pdf->Row($fila);
        $pdf->ln();
        //FIN DEL CAMBIO
        $pdf->setxy(10,36.5);
        $pdf->MultiCell(40,15, 'BCP', 1, 'C', true);
        //$pdf->setxy(10,36.5);
        $pdf->MultiCell(40,15, 'BBVA', 1, 'C', true);
        $pdf->setxy(10,60);

        $pdf->ln();
        $pdf->Cell(90, 7, "LETRAS SEGUN COBRO");
        $pdf->ln();
        $pdf->fill(false);

        $fila = array('ZONA GEOGRAFICA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('BANCO', 'TIPO', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BBVA', 'LETRA DESCUENTO', number_format($sLCLSBBVA, 2), number_format($sLCLDBBVA, 2));
        $pdf->Row($fila);
        $fila = array('BCP', 'LETRA DESCUENTO', number_format($SDsctoSLima, 2), number_format($SDsctoDLima, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BCP', 'COBRANZA LIBRE', number_format($SclSLima, 2), number_format($SclDLima, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CPA', 'CPA LIMA', number_format($sLCLSCPA, 2), number_format($sLCLDCPA, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BCP-D', 'BCP-D LIMA', number_format($sLCLS_AC_BCP_D, 2), number_format($sLCLD_AC_BCP_D, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BCP-CL', 'BCP-CL LIMA', number_format($sLCLS_AC_BCP_CL, 2), number_format($sLCLD_AC_BCP_CL, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BBVA-D', 'BBVA-D LIMA', number_format($sLCLS_AC_BBVA_D, 2), number_format($sLCLD_AC_BBVA_D, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BCP-CL', 'BCP-CL LIMA', number_format($sLCLS_DC_BCP_CL, 2), number_format($sLCLD_DC_BCP_CL, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BCP-D', 'BCP-D LIMA', number_format($sLCLS_DC_BCP_D, 2), number_format($sLCLD_DC_BCP_D, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BBVA-D', 'BBVA-D LIMA', number_format($sLCLS_DC_BBVA_D, 2), number_format($sLCLD_DC_BBVA_D, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('R-CL', 'R-CL LIMA', number_format($sLCLS_R_CL, 2), number_format($sLCLD_R_CL, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $totalzonageograficalima_soles = $sLCLSCPA + $sLCLS_AC_BCP_D + $sLCLS_AC_BCP_CL + $sLCLS_AC_BBVA_D + $sLCLS_DC_BCP_CL + $sLCLS_DC_BCP_D + $sLCLS_DC_BBVA_D + $sLCLS_R_CL + $sLCLSBBVA + $SDsctoSLima + $SclSLima;
        $totalzonageograficalima_dolares = $sLCLDCPA + $sLCLD_AC_BCP_D + $sLCLD_AC_BCP_CL + $sLCLD_AC_BBVA_D + $sLCLD_DC_BCP_CL + $sLCLD_DC_BCP_D + $sLCLD_DC_BBVA_D + $sLCLD_R_CL + $sLCLDBBVA+$SDsctoDLima+$SclDLima;
        $fila = array('TOTAL', '', number_format($totalzonageograficalima_soles, 2), number_format(($totalzonageograficalima_dolares), 2));
        $pdf->Row($fila);
        $ancho = array(190);
        $pdf->SetWidths($ancho);
        $pdf->fill(false);
        $fila = array('');
        $pdf->Row($fila);
        $ancho = array(40,50, 50, 50);
        $pdf->SetWidths($ancho);
        $pdf->fill(false);
        $fila = array('ZONA GEOGRAFICA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('BANCO', 'TIPO', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BBVA', 'LETRA DESCUENTO', number_format($sLCPSBBVA, 2), number_format($sLCPDBBVA, 2));
        $pdf->Row($fila);
        $fila = array('BCP', 'LETRA DESCUENTO', number_format($SDsctoSProvincia, 2), number_format($SDsctoDProvincia, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('BCP', 'COBRANZA LIBRE', number_format($SclSProvincia, 2), number_format($SclDProvincia, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CPA', 'CPA PROVINCIA', number_format($sLCPSCPA, 2), number_format($sLCPDCPA, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BCP-D', 'BCP-D PROVINCIA', number_format($sLCPS_AC_BCP_D, 2), number_format($sLCPD_AC_BCP_D, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BCP-CL', 'BCP-CL PROVINCIA', number_format($sLCPS_AC_BCP_CL, 2), number_format($sLCPD_AC_BCP_CL, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('A/C | BBVA-D', 'BBVA-D PROVINCIA', number_format($sLCPS_AC_BBVA_D, 2), number_format($sLCPD_AC_BBVA_D, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BCP-CL', 'BCP-CL PROVINCIA', number_format($sLCPS_DC_BCP_CL, 2), number_format($sLCPD_DC_BCP_CL, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BCP-D', 'BCP-D PROVINCIA', number_format($sLCPS_DC_BCP_D, 2), number_format($sLCPD_DC_BCP_D, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('D/C | BBVA-D', 'BBVA-D PROVINCIA', number_format($sLCPS_DC_BBVA_D, 2), number_format($sLCPD_DC_BBVA_D, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('R-CL', 'R-CL PROVINCIA', number_format($sLCPS_R_CL, 2), number_format($sLCPD_R_CL, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $totalzonageograficaprovincia_soles = $sLCPSCPA + $sLCPS_AC_BCP_D + $sLCPS_AC_BCP_CL + $sLCPS_AC_BBVA_D + $sLCPS_DC_BCP_CL + $sLCPS_DC_BCP_D + $sLCPS_DC_BBVA_D + $sLCPS_R_CL + $sLCPSBBVA + $SDsctoSProvincia + $SclSProvincia;
        $totalzonageograficaprovincia_dolares = $sLCPDCPA + $sLCPD_AC_BCP_D + $sLCPD_AC_BCP_CL +  $sLCPD_AC_BBVA_D + $sLCPD_DC_BCP_CL + $sLCPD_DC_BCP_D + $sLCPD_DC_BBVA_D + $sLCPD_R_CL + $sLCPDBBVA+$SDsctoDProvincia+$SclDProvincia;
        $fila = array('TOTAL', '', number_format($totalzonageograficaprovincia_soles, 2), number_format(($totalzonageograficaprovincia_dolares), 2));

        $pdf->Row($fila);
        $pdf->setxy(10, 82); // $pdf->setxy(10, 82);
        $pdf->MultiCell(40, 5, 'ZONA GEOGRAFICA', 1, 'C', true);
        $pdf->setxy(50, 82);
        $pdf->MultiCell(150, 5, 'LIMA', 1, 'C', false);

        $pdf->setxy(10, 92);
        $pdf->MultiCell(40, 5, 'BBVA', 1, 'C', true);

        $pdf->setxy(10, 97);
        $pdf->MultiCell(40, 10, 'BCP', 1, 'C', true);
        
        $pdf->setxy(10, 107);
        $pdf->MultiCell(40, 5, 'CPA', 1, 'C', true);
        $pdf->MultiCell(40, 15, 'A/C', 1, 'C', true);
        $pdf->MultiCell(40, 15, 'D/C', 1, 'C', true);
        $pdf->MultiCell(40, 5, 'R-CL', 1, 'C', true);     
        $pdf->MultiCell(90, 5, 'TOTAL', 1, 'C', true);

        $pdf->setxy(10, 157);
        $pdf->MultiCell(40, 5, 'ZONA GEOGRAFICA', 1, 'C', true);
        $pdf->setxy(50, 157);
        $pdf->MultiCell(150, 5, 'PROVINCIA', 1, 'C', false);

        $pdf->setxy(10, 167);
        $pdf->MultiCell(40, 5, 'BBVA', 1, 'C', true);
        $pdf->MultiCell(40, 10, 'BCP', 1, 'C', true);
        $pdf->MultiCell(40, 5, 'CPA', 1, 'C', true);
        $pdf->MultiCell(40, 15, 'A/C', 1, 'C', true);
        $pdf->MultiCell(40, 15, 'D/C', 1, 'C', true);
        $pdf->MultiCell(40, 5, 'R-CL', 1, 'C', true);
        $pdf->MultiCell(90, 5, 'TOTAL', 1, 'C', true);
        //$pdf->ln();
        $pdf->setxy(10, 220);
        $pdf->ln(8);
        $ancho = array(90, 50, 50);
        $pdf->SetWidths($ancho);
        $orientacion = array('L', 'R', 'R');
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $sLSS = $sLSLS + $sLSPS;
        $sLSD = $sLSLD + $sLSPD;
        $pdf->ln();
        $pdf->Cell(90, 7, "LETRAS SIN ENVIAR AL BANCO");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sLSLS, 2), number_format($sLSLD, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sLSPS, 2), number_format($sLSPD, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($sLSS, 2), number_format($sLSD, 2));
        $pdf->Row($fila);

        $sLPS = $sLPLS + $sLPPS;
        $sLPD = $sLPLD + $sLPPD;
        $pdf->ln();
        $pdf->Cell(90, 7, "LETRAS PROTESTADAS");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sLPLS, 2), number_format($sLPLD, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sLPPS, 2), number_format($sLPPD, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($sLPS, 2), number_format($sLPD, 2));
        $pdf->Row($fila);
        $pdf->ln();

        $sCS = $sCLS + $sCPS;
        $sCD = $sCLD + $sCPD;

        $pdf->Cell(90, 7, "CREDITOS POR VENCER                                              (la sumatoria contempla todos los creditos que van a vencer)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLSporVencer, 2), number_format($sCLDporVencer, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPSporVencer, 2), number_format($sCPDporVencer, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLSporVencer+$sCPSporVencer), 2), number_format(($sCLDporVencer+$sCPDporVencer), 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS POR VENCER EN 30 DIAS               (contempla creditos que van a vencer entre hoy y 30 dias despues)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLSporVencer30, 2), number_format($sCLDporVencer30, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPSporVencer30, 2), number_format($sCPDporVencer30, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLSporVencer30+$sCPSporVencer30), 2), number_format(($sCLDporVencer30+$sCPDporVencer30), 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS POR VENCER EN 60 DIAS                                              (la sumatoria no contempla los primeros 30 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLSporVencer60, 2), number_format($sCLDporVencer60, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPSporVencer60, 2), number_format($sCPDporVencer60, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLSporVencer60+$sCPSporVencer60), 2), number_format(($sCLDporVencer60+$sCPDporVencer60), 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS POR VENCER EN 90 DIAS                                              (la sumatoria no contempla los primeros 60 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLSporVencer90, 2), number_format($sCLDporVencer90, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPSporVencer90, 2), number_format($sCPDporVencer90, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLSporVencer90+$sCPSporVencer90), 2), number_format(($sCLDporVencer90+$sCPDporVencer90), 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS POR VENCER MAYOR A 90 DIAS                                  (la sumatoria no contempla los primeros 90 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLSporVencermas90, 2), number_format($sCLDporVencermas90, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPSporVencermas90, 2), number_format($sCPDporVencermas90, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLSporVencermas90+$sCPSporVencermas90), 2), number_format(($sCLDporVencermas90+$sCPDporVencermas90), 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS VENCIDOS                                                               (la sumatoria contempla todos los creditos vencidos)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLS, 2), number_format($sCLD, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPS, 2), number_format($sCPD, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($sCS, 2), number_format($sCD, 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS VENCIDOS HACE 30 DIAS                                  (contempla creditos vencidos entre hoy y 30 dias atras)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLS30, 2), number_format($sCLD30, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPS30, 2), number_format($sCPD30, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLS30+$sCPS30), 2), number_format(0.00, 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS VENCIDOS HACE 60 DIAS                                             (la sumatoria no contempla los primeros 30 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLS60, 2), number_format($sCLD60, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPS60, 2), number_format($sCPD60, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLS60+$sCPS60), 2), number_format(($sCLD60+$sCPD60), 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS VENCIDOS HACE 90 DIAS                                             (la sumatoria no contempla los primeros 60 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLS90, 2), number_format($sCLD90, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPS90, 2), number_format($sCPD90, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLS90+$sCPS90), 2), number_format(($sCLD90+$sCPD90), 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->Cell(90, 7, "CREDITOS VENCIDOS MAYOR A 90 DIAS                                       (la sumatoria no contempla los primeros 90 dias)");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', number_format($sCLSmas91, 2), number_format($sCLDmas91, 2));
        $pdf->Row($fila);
        $fila = array('PROVINCIA', number_format($sCPSmas91, 2), number_format($sCPDmas91, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format(($sCLSmas91+$sCPSmas91), 2), number_format(($sCLDmas91+$sCPDmas91), 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $pdf->Cell(90, 7, "COBRANZA PESADA");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'LIMA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CONTADO', number_format($sContLimPes, 2), number_format($dContLimPes, 2));
        $pdf->Row($fila);
        $fila = array('CREDITO', number_format($sCredLimPes, 2), number_format($dCredLimPes, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS', number_format($sLetLimPes, 2), number_format($dLetLimPes, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($sLetProLimPes, 2), number_format($dLetProLimPes, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL EN LIMA', number_format($sLetProLimPes + $sContLimPes + $sCredLimPes + $sLetLimPes, 2), number_format($dLetProLimPes + $dContLimPes + $dCredLimPes + $dLetLimPes, 2));
        $pdf->Row($fila);
        
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'PROVINCIA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CONTADO', number_format($sContProvPes, 2), number_format($dContProvPes, 2));
        $pdf->Row($fila);
        $fila = array('CREDITO', number_format($sCredProvPes, 2), number_format($dCredProvPes, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS', number_format($sLetProvPes, 2), number_format($dLetProvPes, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($sLetProProvPes, 2), number_format($dLetProProvPes, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL EN PROVINCIA', number_format($sContProvPes + $sCredProvPes + $sLetProvPes + $sLetProProvPes, 2), number_format($dLetProProvPes + $dContProvPes + $dCredProvPes + $dLetProvPes, 2));
        $pdf->Row($fila);
        $pdf->ln();
        
        $totalpesadoensoles = $sLetProLimPes + $sLetProProvPes + $sContLimPes + $sCredLimPes + $sLetLimPes + $sContProvPes + $sCredProvPes + $sLetProvPes;
        $totalpesadoendolares = $dLetProLimPes + $dLetProProvPes + $dContLimPes + $dCredLimPes + $dLetLimPes + $dContProvPes + $dCredProvPes + $dLetProvPes;
        $pdf->fill(true);
        $fila = array('TOTAL PESADO', number_format($totalpesadoensoles, 2), number_format($totalpesadoendolares, 2));
        $pdf->Row($fila);
        $pdf->ln();

        $sTPS = $sTPFS + $sTPPS + $sUEES + $sPAPS + $sPAVS + $sMS + $sTP3S + $sTP4S;
        $sTPD = $sTPFD + $sTPPD + $sUEED + $sPAPD + $sPAVD + $sMD + $sTP3D + $sTP4D;
        
        
        $pdf->Cell(90, 7, "RESUMEN GENERAL");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LETRAS ENVIADAS AL BANCO', number_format($totalesS, 2), number_format($totalesD, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS SIN ENVIAR AL BANCO', number_format($sLSS, 2), number_format($sLSD, 2));
        $pdf->Row($fila);
        
                
        $fila = array('BANCO CPA', number_format($sLCLSCPA + $sLCPSCPA, 2), number_format($sLCLDCPA + $sLCPDCPA, 2));
        $pdf->Row($fila);
        $fila = array('A/C | BCP-D', number_format($sLCLS_AC_BCP_D + $sLCPS_AC_BCP_D, 2), number_format($sLCLD_AC_BCP_D + $sLCPD_AC_BCP_D, 2));
        $pdf->Row($fila);
        $fila = array('A/C | BCP-CL', number_format($sLCLS_AC_BCP_CL + $sLCPS_AC_BCP_CL, 2), number_format($sLCLD_AC_BCP_CL + $sLCPD_AC_BCP_CL, 2));
        $pdf->Row($fila);
        $fila = array('A/C | BBVA-D', number_format($sLCLS_AC_BBVA_D + $sLCPS_AC_BBVA_D, 2), number_format($sLCLD_AC_BBVA_D + $sLCPD_AC_BBVA_D, 2));
        $pdf->Row($fila);
        $fila = array('D/C | BCP-D', number_format($sLCLS_DC_BCP_D + $sLCPS_DC_BCP_D, 2), number_format($sLCLD_DC_BCP_D + $sLCPD_DC_BCP_D, 2));
        $pdf->Row($fila);
        $fila = array('D/C | BCP-CL', number_format($sLCLS_DC_BCP_CL + $sLCPS_DC_BCP_CL, 2), number_format($sLCLD_DC_BCP_CL + $sLCPD_DC_BCP_CL, 2));
        $pdf->Row($fila);
        $fila = array('D/C | BBVA-D', number_format($sLCLS_DC_BBVA_D + $sLCPS_DC_BBVA_D, 2), number_format($sLCLD_DC_BBVA_D + $sLCPD_DC_BBVA_D, 2));
        $pdf->Row($fila);
        $fila = array('R-CL', number_format($sLCLS_R_CL + $sLCPS_R_CL, 2), number_format($sLCLD_R_CL + $sLCPD_R_CL, 2));
        $pdf->Row($fila);
        $totalNuevosBancosSoles = $sLCLSCPA + $sLCPSCPA + $sLCLS_AC_BCP_D + $sLCPS_AC_BCP_D + $sLCLS_AC_BCP_CL + $sLCPS_AC_BCP_CL + $sLCLS_AC_BBVA_D + $sLCPS_AC_BBVA_D + $sLCLS_DC_BCP_D + $sLCPS_DC_BCP_D + $sLCLS_DC_BCP_CL + $sLCPS_DC_BCP_CL + $sLCLS_DC_BBVA_D + $sLCPS_DC_BBVA_D + $sLCLS_R_CL + $sLCPS_R_CL;
        $totalNuevosBancosDolares = $sLCLDCPA + $sLCPDCPA + $sLCLD_AC_BCP_D + $sLCPD_AC_BCP_D + $sLCLD_AC_BCP_CL + $sLCPD_AC_BCP_CL + $sLCLD_AC_BBVA_D + $sLCPD_AC_BBVA_D + $sLCLD_DC_BCP_D + $sLCPD_DC_BCP_D + $sLCLD_DC_BCP_CL + $sLCPD_DC_BCP_CL + $sLCLD_DC_BBVA_D + $sLCPD_DC_BBVA_D + $sLCLD_R_CL + $sLCPD_R_CL;
        
        $fila = array('LETRAS PROTESTADAS', number_format($sLPS, 2), number_format($sLPD, 2));
        $pdf->Row($fila);
        $fila = array('CREDITOS VENCIDOS', number_format($sCS, 2), number_format($sCD, 2));
        $pdf->Row($fila);
        $fila = array('CREDITOS POR VENCER', number_format($sCLSporVencer+$sCPSporVencer, 2), number_format($sCLDporVencer+$sCPDporVencer, 2));
        $pdf->Row($fila);
        
        $fila = array('CONTADO PESADO', number_format($sContLimPes+$sContProvPes, 2), number_format($dContLimPes+$dContProvPes, 2));
        $pdf->Row($fila);
        $fila = array('CREDITO PESADO', number_format($sCredLimPes+$sCredProvPes, 2), number_format($dCredLimPes+$dCredProvPes, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PESADO', number_format($sLetLimPes+$sLetProvPes, 2), number_format($dLetLimPes+$dLetProvPes, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS PESADO', number_format($sLetProLimPes+$sLetProProvPes, 2), number_format($dLetProLimPes+$dLetProProvPes, 2));
        $pdf->Row($fila);

        $pdf->fill(true);
        $fila = array('TOTAL', number_format($totalNuevosBancosSoles + $totalpesadoensoles + $totalesS + $sLSS + $sLPS + $sCS +$sCLSporVencer+$sCPSporVencer, 2), number_format($totalNuevosBancosDolares + $totalesD + $sLSD + $sLPD + $sCD + $sCLDporVencer+$sCPDporVencer + $totalpesadoendolares, 2));
        $pdf->Row($fila);
        $pdf->ln();
        $pdf->ln();
        $pdf->ln();
        
        
        $pdf->Cell(90, 7, "EMPRESA CORPORACION POWER ACOUSTIK");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('EMPRESA', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('TIENDA PARURO', number_format($sTPFS, 2), number_format($sTPFD, 2));
        $pdf->Row($fila);
        $fila = array('TIENDA PARURO (C.PUSE)', number_format($sTPPS, 2), number_format($sTPPD, 2));
        $pdf->Row($fila);
        $fila = array('TIENDA PARURO 3', number_format($sTP3S, 2), number_format($sTP3D, 2));
        $pdf->Row($fila);
        $fila = array('TIENDA PARURO 4', number_format($sTP4S, 2), number_format($sTP4D, 2));
        $pdf->Row($fila);
        $fila = array('USO EXCLUSIVO DE LA EMPRESA', number_format($sUEES, 2), number_format($sUEED, 2));
        $pdf->Row($fila);
        $fila = array('PRESTAMO AL PERSONAL', number_format($sPAPS, 2), number_format($sPAPD, 2));
        $pdf->Row($fila);
        $fila = array('PRESTAMO AL VENDEDOR', number_format($sPAVS, 2), number_format($sPAVD, 2));
        $pdf->Row($fila);
        $fila = array('MUESTRAS GENERAL', number_format($sMS, 2), number_format($sMD, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', number_format($sTPS, 2), number_format($sTPD, 2));
        $pdf->Row($fila);

        
                       
        $pdf->Cell(90, 7, "RESUMEN INCOBRABLES");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'LIMA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CONTADO', number_format($sILimCont, 2), number_format($dILimvCont, 2));
        $pdf->Row($fila);
        $fila = array('CREDITO', number_format($sILimCredi, 2), number_format($dILimvCredi, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS', number_format($sILimLet, 2), number_format($dILimvLet, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($sILimLetPro, 2), number_format($dILimvLetPro, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL EN LIMA', number_format($sILimCont + $sILimCredi + $sILimLet + $sILimLetPro, 2), number_format($dILimvCont + $dILimvCredi + $dILimvLet + $dILimvLetPro, 2));
        $pdf->Row($fila);
                
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('ZONA GEOGRAFICA', 'PROVINCIA');
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('DESCRIPCION', 'SOLES', 'DOLARES');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('CONTADO', number_format($sIProvCont, 2), number_format($dIProvCont, 2));
        $pdf->Row($fila);
        $fila = array('CREDITO', number_format($sIProvCredi, 2), number_format($dIProvCredi, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS', number_format($sIProvLet, 2), number_format($dIProvLet, 2));
        $pdf->Row($fila);
        $fila = array('LETRAS PROTESTADAS', number_format($sIProvLetPro, 2), number_format($dIProvLetPro, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL EN PROVINCIA', number_format($sIProvCont + $sIProvCredi + $sIProvLetPro + $sIProvLet, 2), number_format($dIProvCont + $dIProvCredi + $dIProvLet + $dIProvLetPro, 2));
        $pdf->Row($fila);
        $pdf->ln();

        $pdf->fill(true);
        $fila = array('TOTAL INCOBRABLE', number_format($sILimCont + $sILimCredi + $sILimLet + $sIProvCont + $sIProvCredi + $sIProvLet + $sIProvLetPro + $sILimLetPro, 2), number_format($dILimvCont + $dILimvCredi + $dILimvLet + $dIProvCont + $dIProvCredi + $dIProvLet + $dIProvLetPro + $dILimvLetPro, 2));
        $pdf->Row($fila);

        $pdf->ln();
        $pdf->AliasNbPages();
        $pdf->Output();
    }
     
    function reporteclientevendedor() {
        set_time_limit(500);
        $idvend = $_REQUEST['idVendedor'];
        $anio = $_REQUEST['cmbAnio'];
        $reporte = $this->AutoLoadModel('reporte');
        $datos = $reporte->clientesDeVendedor($idvend, $anio);
        $titulos = array('CLIENTE', 'RUC', 'TELF.', 'EMAIL', 'DIRECCION', 'ZONA', 'UBICACION', 'TOTAL');
        $ancho = array(50, 20, 25, 30, 40, 20, 30, 20);
        $orientacion = array('L', 'C', 'C', 'C', 'L', 'C', 'C', 'R');
        $cantidadData = count($datos);

        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $pdf->SetWidths($ancho);
        $vendedor = new Actor();
        $reg = $vendedor->buscarxid($idvend);
        $pdf->_titulo = "VENDEDOR: ".$reg[0]['nombres']." ".$reg[0]['apellidopaterno'];

        //$pdf->_fecha = date('d/m/Y');

        $pdf->_datoPie = "FECHA IMPRESION: " . date('Y/m/d');
        $pdf->AddPage();
        $relleno = false;
        $pdf->fill($relleno);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $pdf->ln();
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->Row($titulos);

        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        for ($i = 0; $i < $cantidadData; $i++) {
            $fila = array(utf8_decode(html_entity_decode($datos[$i]['razonsocial'], ENT_QUOTES, 'UTF-8')), $datos[$i]['ruc'], $datos[$i]['telefono'] . (empty($datos[$i]['telefono']) || empty($datos[$i]['celular']) ? "" : " / ") . $datos[$i]['celular'], $datos[$i]['email'], utf8_decode(html_entity_decode($datos[$i]['direccion'], ENT_QUOTES, 'UTF-8')), utf8_decode(html_entity_decode($datos[$i]['nombrezona'], ENT_QUOTES, 'UTF-8')), utf8_decode(html_entity_decode($datos[$i]['nombredistrito'] . " - " . $datos[$i]['nombreprovincia'] . " - " . $datos[$i]['nombredepartamento'], ENT_QUOTES, 'UTF-8')), number_format($datos[$i]['suma'], 2));
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function prestamosPersonal () {
        $detalle = $this->AutoLoadModel('DetalleOrdenVenta');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $ordenes = $detalle->importesProductoDeuda();
        $tam = count($ordenes);
        $nro = 1;
        $cliente = -1;
        $Sdeuda = 0;
        $Ddeuda = 0;

        $deudaSoles = 0;
        $deudaDolares = 0;
        $pdf = new PDF_Mc_Table("P", "mm", "A4");
        $ancho = array(18, 72, 40, 45, 10, 20, 18, 18, 13, 25);
        $orientacion = array('C', '', '', '', 'R', 'R', 'C', 'R', 'R');
        $pdf->_titulo = "PRESTAMOS AL PERSONAL";
        $pdf->AddPage();

        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill(true);

        $pdf->SetWidths($ancho);
        $pdf->SetAligns($orientacion);
        $pdf->Cell(50, 6, "FECHA IMPRESION: ", 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(140, 6, date('d/m/Y'), 1, 0, 'C', true);

        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(15, 6, "NRO.", 1, 0, 'C', true);
        $pdf->Cell(95, 6, "PERSONAL", 1, 0, 'C', true);
        $pdf->Cell(40, 6, "DEUDA (S/.)", 1, 0, 'C', true);
        $pdf->Cell(40, 6, "DEUDA (US $.)", 1, 0, 'C', true);
        $pdf->Ln();
        set_time_limit(500);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        for ($i = 0; $i < $tam; $i++) {
            if ($cliente == $ordenes[$i]['idcliente']) {
                if ($ordenes[$i]['simbolo'] == 'S/') $Sdeuda = ($ordenGasto->totalGuia($ordenes[$i]['idordenventa']) - $ordenes[$i]['importepagado']) + $Sdeuda;
                else $Ddeuda = ($ordenGasto->totalGuia($ordenes[$i]['idordenventa']) - $ordenes[$i]['importepagado']) + $Ddeuda;
            } else {
                if ($Sdeuda > 0 || $Ddeuda > 0) {
                    $pdf->Cell(15, 5, str_pad($nro, 3, "0", STR_PAD_LEFT) , 1, 0, 'C', true);
                    $pdf->Cell(95, 5, $ordenes[$i-1]['razonsocial'], 1, 0, 'L', true);
                    $pdf->Cell(40, 5, 'S/. '.number_format($Sdeuda, 2), 1, 0, 'C', true);
                    $pdf->Cell(40, 5, 'US $. '.number_format($Ddeuda, 2), 1, 0, 'C', true);
                    $pdf->Ln();
                    $nro ++;
                }
                $deudaSoles = $deudaSoles + $Sdeuda;
                $deudaDolares = $deudaDolares + $Ddeuda;
                $cliente = $ordenes[$i]['idcliente'];
                if ($ordenes[$i]['simbolo'] == 'S/') {
                    $Sdeuda = ($ordenGasto->totalGuia($ordenes[$i]['idordenventa']) - $ordenes[$i]['importepagado']);
                    $Ddeuda = 0;
                } else {
                    $Ddeuda = ($ordenGasto->totalGuia($ordenes[$i]['idordenventa']) - $ordenes[$i]['importepagado']);
                    $Sdeuda = 0;
                }
            }
        }

        if ($Sdeuda > 0 || $Ddeuda > 0) {
            $pdf->Cell(15, 5, str_pad($nro, 3, "0", STR_PAD_LEFT) , 1, 0, 'C', true);
            $pdf->Cell(95, 5, $ordenes[$i-1]['razonsocial'], 1, 0, 'L', true);
            $pdf->Cell(40, 5, 'S/. '.number_format($Sdeuda, 2), 1, 0, 'C', true);
            $pdf->Cell(40, 5, 'US $. '.number_format($Ddeuda, 2), 1, 0, 'C', true);
            $deudaSoles = $deudaSoles + $Sdeuda;
            $deudaDolares = $deudaDolares + $Ddeuda;
        }
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->Cell(110, 6, "DEUDA TOTAL: ", 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(40, 6, "S/. ". number_format($deudaSoles, 2), 1, 0, 'C', true);
        $pdf->Cell(40, 6, "US $. ". number_format($deudaDolares, 2), 1, 0, 'C', true);

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function listaReporteVentasXdiaPDF() {
        $anio = $_REQUEST['opcanio'];
        $mes = $_REQUEST['opcmes'];

        $auxiliarFecha = strtotime($anio."-".$mes."-01");
        $fin = date( "t", $auxiliarFecha );

        $ordenVenta=new OrdenVenta();
        $reporte = $this->AutoLoadModel('reporte');
        $documento = $this->AutoLoadModel('documento');
        $ingresos = $this->AutoLoadModel('ingresos');
        $tipocambioprom = $this->AutoLoadModel('Tcpromedio');
        $equivalentes = $ingresos->equivalencias($mes, $anio);
        $valortc = round($tipocambioprom->getTipocambio($mes, $anio), 2);

        $totalFacturado = $equivalentes[0]['factura'];
        $totalnoFacturado = $equivalentes[0]['nofacturado'];
        $totalcob_letras = $equivalentes[0]['letra'];
        $totalcob_efecdepo = $equivalentes[0]['efectdesp'];
        $totalAprobado = 0;
        $totalDespachado = 0;

        $totalFacturadoTemp = 0;
        $totalnoFacturadoTemp = 0;
        $totalcob_letrasTemp = 0;
        $totalcob_efecdepoTemp = 0;

        $pdf = new PDF_Mc_Table("P", "mm", "A4");
        $titulos = array('ITEM', 'FECHA', "Imp. Aprobado (U$ $.)", "Facturado (U$ $.)", "No Facturado (U$ $.)", "Efect./Depo. (U$ $.)", "Letras (U$ $.)", "Imp. Despachado (U$ $.)");
        $ancho = array(10, 20, 30, 23, 26, 26, 25, 30);
        $orientacion = array('C', 'C', 'R', 'R', 'R', 'R', 'R', 'R');
        $pdf->_titulo = "REPORTE - TOTAL DE VENTAS POR DIA";
        $pdf->AddPage();

        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill(true);

        $pdf->SetWidths($ancho);
        $pdf->SetAligns($orientacion);
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 7);
        $pdf->fill(true);
        $arrayMeses = array('01' => "ENERO", '02' => "FEBRERO", '03' => "MARZO", '04' => "ABRIL", '05' => "MAYO", '06' => "JUNIO", '07' => "JULIO", '08' => "AGOSTO", '09' => "SEPTIEMBRE", '10' => "OCTUBRE", '11' => "NOVIEMBRE", '12' => "DICIEMBRE",);
        $pdf->Cell(31, 8, "Fecha Consulta: ", 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(32, 8, $arrayMeses[$mes] . " - " . $anio, 1, 0, 'C', true);
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->Cell(31, 8, "MONEDA: ", 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(33, 8, "DOLARES (U$ $.)", 1, 0, 'C', true);
        $pdf->Ln();
        $pdf->Ln();
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->Cell(10, 8, '', 1, 0, 'C', true);
        $pdf->Cell(50, 8, 'VENTAS', 1, 0, 'C', true);
        $pdf->Cell(100, 8, 'COBRANZAS', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'TOTALES', 1, 0, 'C', true);
        $pdf->Ln();

        for ($i = 0; $i < count($titulos); $i++) {
            $pdf->Cell($ancho[$i], 8, $titulos[$i], 1, 0, 'C', true);
        }

        $pdf->Ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);

        for ($i = 1; $i <= $fin; $i++) {
                $fechabusqueda = $anio . "-" . $mes . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);

                $arreglo[$i][0] = $documento->getMonto($fechabusqueda, '1', 2);
                $arreglo[$i][1] = $ordenVenta->montoGuiaRemision($fechabusqueda, 2);
                $arreglo[$i][2] = $ingresos->getMontoTotal($fechabusqueda, 2, "1, 2, 3, 4");
                $arreglo[$i][3] = $ingresos->getMontoTotal($fechabusqueda, 2, "5, 9");

                $arreglo[$i][0] += $documento->getMonto($fechabusqueda, '1', 1)/$valortc;
                $arreglo[$i][1] += $ordenVenta->montoGuiaRemision($fechabusqueda, 1)/$valortc;
                $arreglo[$i][2] += $ingresos->getMontoTotal($fechabusqueda, 1, "1, 2, 3, 4")/$valortc;
                $arreglo[$i][3] += $ingresos->getMontoTotal($fechabusqueda, 1, "5, 9")/$valortc;

                $totalFacturadoTemp += $arreglo[$i][0];
                $totalnoFacturadoTemp += $arreglo[$i][1];
                $totalcob_efecdepoTemp += $arreglo[$i][2];
                $totalcob_letrasTemp += $arreglo[$i][3];
            }
            if ($totalFacturado > $totalFacturadoTemp) {
                $totalFacturadoDif = $totalFacturado - $totalFacturadoTemp;
                $porc1 = $totalFacturadoTemp/$totalFacturado;
            } else {
                $totalFacturadoDif = $totalFacturadoTemp - $totalFacturado;
                $porc1 = $totalFacturado/$totalFacturadoTemp;
            }
            if ($totalnoFacturado > $totalnoFacturadoTemp) {
                $totalnoFacturadoDif = $totalnoFacturado - $totalnoFacturadoTemp;
                $porc2 = $totalnoFacturadoTemp/$totalnoFacturado;
            } else {
                $totalnoFacturadoDif = $totalnoFacturadoTemp - $totalnoFacturado;
                $porc2 = $totalnoFacturado/$totalnoFacturadoTemp;
            }

            if ($totalcob_efecdepo > $totalcob_efecdepoTemp){
                $totalcob_efecdepoDif = $totalcob_efecdepo - $totalcob_efecdepoTemp;
                $porc3 = $totalcob_efecdepoTemp/$totalcob_efecdepo;
            } else{
                $totalcob_efecdepoDif = $totalcob_efecdepoTemp - $totalcob_efecdepo;
                $porc3 = $totalcob_efecdepo/$totalcob_efecdepoTemp;
            }

            if ($totalcob_letras > $totalcob_letrasTemp){
                $totalcob_letrasDif = $totalcob_letras - $totalcob_letrasTemp;
                $porc4 = $totalcob_letrasTemp/$totalcob_letras;
            } else{
                $totalcob_letrasDif = $totalcob_letrasTemp - $totalcob_letras;
                $porc4 = $totalcob_letras/$totalcob_letrasTemp;
            }

            while ($totalFacturadoDif > 0 || $totalnoFacturadoDif > 0 || $totalcob_efecdepoDif > 0 || $totalcob_letrasDif > 0) {
                for ($i = 1; $i <= $fin; $i++) {
                    if ($totalcob_efecdepo > $totalcob_efecdepoTemp) {
                        if ($totalcob_efecdepoDif > 0) {
                            if ($totalcob_efecdepoDif - $arreglo[$i][2] * $porc3 < 0) {
                                $arreglo[$i][2] = $arreglo[$i][2] + $totalcob_efecdepoDif;
                                $totalcob_efecdepoDif = 0;
                            } else{
                                $totalcob_efecdepoDif = $totalcob_efecdepoDif - $arreglo[$i][2] * $porc3;
                                $arreglo[$i][2] = $arreglo[$i][2] + $arreglo[$i][2] * $porc3;
                            }
                        }
                    } else {
                        if ($totalcob_efecdepoDif > 0) {
                            if ($totalcob_efecdepoDif - $arreglo[$i][2] * $porc3 < 0) {
                                $arreglo[$i][2] = $arreglo[$i][2] - $totalcob_efecdepoDif;
                                $totalcob_efecdepoDif = 0;
                            } else {
                                $totalcob_efecdepoDif = $totalcob_efecdepoDif - $arreglo[$i][2] * $porc3;
                                $arreglo[$i][2] = $arreglo[$i][2] - $arreglo[$i][2] * $porc3;
                            }
                        }
                    }
                    if ($totalcob_letras > $totalcob_letrasTemp) {
                        if ($totalcob_letrasDif > 0) {
                            if ($totalcob_letrasDif - $arreglo[$i][3] * $porc4 < 0) {
                                $arreglo[$i][3] = $arreglo[$i][3] + $totalcob_letrasDif;
                                $totalcob_letrasDif = 0;
                            } else {
                                $totalcob_letrasDif = $totalcob_letrasDif - $arreglo[$i][3] * $porc4;
                                $arreglo[$i][3] = $arreglo[$i][3] + $arreglo[$i][3] * $porc4;
                            }
                        }
                    } else {
                        if ($totalcob_letrasDif > 0) {
                            if ($totalcob_letrasDif - $arreglo[$i][3] * $porc4 < 0) {
                                $arreglo[$i][3] = $arreglo[$i][3] - $totalcob_letrasDif;
                                $totalcob_letrasDif = 0;
                            } else {
                                $totalcob_letrasDif = $totalcob_letrasDif - $arreglo[$i][3] * $porc4;
                                $arreglo[$i][3] = $arreglo[$i][3] - $arreglo[$i][3] * $porc4;
                            }
                        }
                    }

                    if ($totalFacturado > $totalFacturadoTemp) {
                        if ($totalFacturadoDif > 0) {
                            if ($totalFacturadoDif - $arreglo[$i][0] * $porc1 < 0 ) {
                                $arreglo[$i][0] = $arreglo[$i][0] + $totalFacturadoDif;
                                $totalFacturadoDif = 0;
                            } else {
                                $totalFacturadoDif = $totalFacturadoDif - $arreglo[$i][0] * $porc1;
                                $arreglo[$i][0] = $arreglo[$i][0] + $arreglo[$i][0] * $porc1;
                            }
                        }
                    } else {
                        if ($totalFacturadoDif > 0) {
                            if ($totalFacturadoDif - $arreglo[$i][0] * $porc1 < 0) {
                                $arreglo[$i][0] = $arreglo[$i][0] - $totalFacturadoDif;
                                $totalFacturadoDif = 0;
                            } else {
                                $totalFacturadoDif = $totalFacturadoDif - $arreglo[$i][0] * $porc1;
                                $arreglo[$i][0] = $arreglo[$i][0] - $arreglo[$i][0] * $porc1;
                            }
                        }
                    }
                    if ($totalnoFacturado > $totalnoFacturadoTemp) {
                        if ($totalnoFacturadoDif > 0) {
                            if ($totalnoFacturadoDif - $arreglo[$i][1] * $porc2 < 0) {
                                $arreglo[$i][1] = $arreglo[$i][1] + $totalnoFacturadoDif;
                                $totalnoFacturadoDif = 0;
                            } else {
                                $totalnoFacturadoDif = $totalnoFacturadoDif - $arreglo[$i][1] * $porc2;
                                $arreglo[$i][1] = $arreglo[$i][1] + $arreglo[$i][1] * $porc2;
                            }
                        }
                    } else {
                        if ($totalnoFacturadoDif > 0) {
                            if ($totalnoFacturadoDif - $arreglo[$i][1] * $porc2 < 0) {
                                $arreglo[$i][1] = $arreglo[$i][1] - $totalnoFacturadoDif;
                                $totalnoFacturadoDif = 0;
                            } else {
                                $totalnoFacturadoDif = $totalnoFacturadoDif - $arreglo[$i][1] * $porc2;
                                $arreglo[$i][1] = $arreglo[$i][1] - $arreglo[$i][1] * $porc2;
                            }
                        }
                    }
                }
            }

            for ($i = 1; $i <= $fin; $i++) {
                $fechabusqueda = $anio . "-" . $mes . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);

                $aprobado = $reporte->montoAprobado($fechabusqueda, 2);
                $despachado = $reporte->montoDespachado($fechabusqueda, 2);
                $aprobado += $reporte->montoAprobado($fechabusqueda, 1)/$valortc;
                $despachado += $reporte->montoDespachado($fechabusqueda, 1)/$valortc;
                $totalAprobado += $aprobado;
                $totalDespachado += $despachado;
                $fila = array(utf8_decode(html_entity_decode(($i), ENT_QUOTES, 'UTF-8')), $fechabusqueda, number_format($aprobado, 2), number_format($arreglo[$i][0], 2), number_format($arreglo[$i][1], 2), number_format($arreglo[$i][2], 2), number_format($arreglo[$i][3], 2), number_format($despachado, 2));
                $pdf->Row($fila);
                $relleno = !$relleno;
                $pdf->fill($relleno);
            }
            $pdf->Ln();
            $pdf->SetFillColor(202, 232, 234);
            $pdf->SetTextColor(12, 78, 139);
            $pdf->Cell(30, 8, "MONTO TOTAL (U$ $.)",1 , 0, 'C', true);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);$ancho = array(10, 20, 30, 23, 26, 26, 25, 30);
            $pdf->Cell(30, 8, "(U$ $.) ".number_format($totalAprobado, 2), 1, 0, 'R', true);
            $pdf->Cell(23, 8, "(U$ $.) ".number_format($totalFacturado, 2), 1, 0, 'R', true);
            $pdf->Cell(26, 8, "(U$ $.) ".number_format($totalnoFacturado, 2), 1, 0, 'R', true);
            $pdf->Cell(26, 8, "(U$ $.) ".number_format($totalcob_efecdepo, 2), 1, 0, 'R', true);
            $pdf->Cell(25, 8, "(U$ $.) ".number_format($totalcob_letras, 2), 1, 0, 'R', true);
            $pdf->Cell(30, 8, "(U$ $.) ".number_format($totalDespachado, 2), 1, 0, 'R', true);
            $pdf->AliasNbPages();
            $pdf->Output();
        }

    function costodeproductos() {
        $titulos = array('ITEM', 'CODIGO', 'DESCRIPCION', 'P.U. (U$)');
        $ancho = array(15, 30, 125, 20);
        $orientacion = array('C', 'L', 'L', 'R');

        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $pdf->SetWidths($ancho);

        $pdf->_titulo = "REPORTE - COSTO DE PRODUCTOS";
        $pdf->_datoPie = "FECHA IMPRESION: " . date('Y/m/d');
        $pdf->AddPage();
        $relleno = false;
        $pdf->fill($relleno);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $pdf->ln();
        $pdf->SetFont('Helvetica', 'B', 7.5);
        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->Row($titulos);

        $pdf->SetFont('Helvetica', 'B', 7.5);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');

        $ordencompra = new Ordencompra();
        $datos = $ordencompra->reporteCostodeProducto();
        $tam = count($datos);
        $idproducto = 0;
        $cant = 1;
        for ($i = 0; $i < $tam; $i++) {
            if ($idproducto != $datos[$i]['idproducto']) {
                $fila = array(STR_PAD($cant, 3, "0", STR_PAD_LEFT) , $datos[$i]['codigopa'], $datos[$i]['nompro'], "U$. " . number_format($datos[$i]['totalunitario'], 2));
                $pdf->Row($fila);
                $relleno = !$relleno;
                $pdf->fill($relleno);
                $cant ++;
                $idproducto = $datos[$i]['idproducto'];
            }
        }

        $pdf->ln();
        $pdf->AliasNbPages();
        $pdf->Output();
    }
    
    public function analisiscobranzageneral() {
        set_time_limit(2500);
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-d');
        $get_lstPrincipal = $_REQUEST['lstCategoriaPrincipal'];
        $get_lstCategoria = $_REQUEST['lstCategoria'];
        $get_lstZona = $_REQUEST['lstZona'];
        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 10);
        $ancho = array(90, 50, 50);
        $pdf->SetWidths($ancho);
        
        $reporte = $this->AutoLoadModel('reporte');
        
        echo "FECHA INICIO: " . $txtFechaInicio . "<br>";
        echo "FECHA FINAL: " . $txtFechaFinal . "<br>";
        echo "PRINCIPAL: " . $get_lstPrincipal . "<br>";
        echo "CATEGORIA: " . $get_lstCategoria . "<br>";
        echo "ZONA: " . $get_lstZona;
        echo "<hr>";
        
        $lstDeuda = $reporte->montoPorCobrar($txtFechaInicio, $get_lstPrincipal, $get_lstCategoria, $get_lstZona, 1);
        echo "<hr>" . $lstDeuda[0]['devuelveSQL'] . "<hr><hr>";
        $tam = count($lstDeuda);
        $sumaTodoS = 0;
        $sumaTodoD = 0;
        echo "tam: " . $tam . "<hr>"; 
        for ($i = 0; $i < $tam; $i++) {
            if ($lstDeuda[$i]['idmoneda'] == 1) {
                $sumaTodoS += $lstDeuda[$i]['saldodoc'];
            } else {
                $sumaTodoD += $lstDeuda[$i]['saldodoc'];
            } 
        }
        echo "<hr>" . $tam . " Suma Todo Causha: S/. " . number_format($sumaTodoS, 2) . "  -    U$. " . number_format($sumaTodoD, 2) . "<br>";
        
        // AQUI VAMOS A COMPARAR LA FECHA FIN CON LA FECHA DE HOY SI ESTA IGUAL ESTO YA NO ES NECESARIO
        $lstPagado = $reporte->montoPorCobrar($txtFechaInicio, $get_lstPrincipal, $get_lstCategoria, $get_lstZona, 0);
        $tam = count($lstPagado);

        
        echo "<br>Tamaño: " . $tam . "<br>";
        echo "<hr>" . $lstPagado[0]['devuelveSQL'] . "<hr><hr>";

        $i = 0;
        for ($i = 0; $i < $tam; $i++) {
            //if ($lstPagado[$i]['idordenventa']!=$idordenventa) {
             /*  $montoAsignado = $ingreso->sumaIngresosAsignadosPorFecha($lstPagado[$i]['idordenventa'], $txtFechaInicio);
                //echo " <> Ingreso: [" . number_format($dxIngreso[0]['totalasignado'], 2) . "]<br>";
                
                if ($lstPagado[$i]['idmoneda'] == 1) {
                    $sumaIngresoS += $montoAsignado;
                } else {
                    $sumaIngresoD += $montoAsignado;
                }*/
                /*
            } else {
                //echo "<br>";
            }*/
            //$idordenventa = $lstPagado[$i]['idordenventa'];
            if ($lstPagado[$i]['idmoneda'] == 1) {
                $sumaTodoS += $lstPagado[$i]['importedoc'];
            } else {
                $sumaTodoD += $lstPagado[$i]['importedoc'];
            }   /*
            echo "[" . $lstPagado[$i]['idmoneda'] . "] Iddetalle: " . $lstPagado[$i]['iddetalleordencobro'] . " " . $lstPagado[$i]['codigov'] . 
                 ": <b>" . number_format($lstPagado[$i]['totalasignado'], 2) . "</b><br> ";*/
        }
        $sumaIngresoS = 0;
        $sumaIngresoD = 0;
        $ingreso = $this->AutoLoadModel('ingresos');
        $lstIngresos = $ingreso->sumaIngresosAsignadosPorFecha($txtFechaInicio, $get_lstPrincipal, $get_lstCategoria, $get_lstZona);
        $tam = count($lstIngresos);
        echo "<hr>iNGRESOS: " . $tam . "<hr>";
        echo "<hr>" . $lstIngresos[0]['devuelveSQL'] . "<hr><hr>";
        $i = 0;
        for ($i = 0; $i < $tam; $i++) {           
            if ($lstIngresos[$i]['idmoneda'] == 1) {
                $sumaIngresoS += $lstIngresos[$i]['totalasignado'];
            } else {
                $sumaIngresoD += $lstIngresos[$i]['totalasignado'];
            } 
        }
        /*
        $montoAsignado = $ingreso->sumaIngresosAsignadosPorFecha($idordenventa, $txtFechaInicio, $txtFechaFinal);
        if ($lstPagado[$tam-1]['idmoneda'] == 1) {
            $sumaIngresoS += $montoAsignado;
            $sumaTodoS += $lstPagado[$tam-1]['importedoc'];
        } else {
            $sumaIngresoD += $montoAsignado;
            $sumaTodoD += $lstPagado[$tam-1]['importedoc'];
        }*/
        //echo " <> Ingreso: [" . $dxIngreso[0]['totalasignado'] . "]<br>";
        echo "<hr>Suma Todo Causha: S/. " . number_format($sumaTodoS, 2) . "  -    U$. " . number_format($sumaTodoD, 2) . "<br>";
        echo "<hr>Ingresos Todo Causha: S/. " . number_format($sumaIngresoS, 2) . "  -    U$. " . number_format($sumaIngresoD, 2) . "<br>";
        echo "<hr>Total de Todo Causha: S/. " . number_format($sumaTodoS - $sumaIngresoS, 2) . "  -    U$. " . number_format($sumaTodoD - $sumaIngresoD, 2) . "<br>";
        
        echo "<table>" . 
                "<tr>" .
                    "<td>COBRANZA INICIAL</td>" .
                    "<td>EFECTIVO</td>" .
                    "<td>DEVOLUCION</td>" .
                    "<td>SALDO X COBRAR</td>" .
                "<tr>".
                "<tr>" .
                    "<td>COBRANZA INICIAL</td>" .
                    "<td>EFECTIVO</td>" .
                    "<td>DEVOLUCION</td>" .
                    "<td>SALDO X COBRAR</td>" .
                "<tr>".
             "</table>";
        
        /*
        $pdf->_fecha = (!empty($txtFechaInicio) ? $txtFechaInicio . ' - ' : '') . $txtFechaFinal;
        $pdf->_titulo = "ANALISIS COBRANZAS GENERAL";
        $pdf->_datoPie = 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.4);
        $orientacion = array('C','R', 'R', 'R', 'R');
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $ancho = array(40,40, 40, 40, 50);
        $pdf->SetWidths($ancho);
        
        $pdf->Cell(90, 7, "Moneda: Soles");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('','LETRAS', 'CONTADO', 'CREDITO', 'TOTAL');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('PROVINCIA', 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2), 'S/. ' . number_format(0, 2));
        $pdf->Row($fila);        
        $pdf->ln();
        
        $pdf->AliasNbPages();
        $pdf->Output(); */
    }

    public function reportecobranzageneral() {
        set_time_limit(500);
        $txtFechaInicio = !empty($_REQUEST['txtFechaInicio']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaInicio'])) : null;
        $txtFechaFinal = !empty($_REQUEST['txtFechaFinal']) ? date('Y-m-d', strtotime($_REQUEST['txtFechaFinal'])) : date('Y-m-d');

        $reporte = $this->AutoLoadModel('reporte');
       
        $dataL = $reporte->resumenLetras($txtFechaInicio, $txtFechaFinal);
        $cant = count($dataL);
        $sLSLS = 0.0; //
        $sLSLD = 0.0;
        $sLSPS = 0.0;
        $sLSPD = 0.0;

        $sLCLSBBVA = 0.0; // suma de bbva de lima y en soles
        $sLCLDBBVA = 0.0; // suma de bbva de lima y en dolares

        $sLCPSBBVA = 0.0; // suma de bbva de Provincia y en soles
        $sLCPDBBVA = 0.0; // suma de bbva de Provincia y en dolares

        $sLCLS = 0.0;
        $sLCLD = 0.0;
        $sLCPS = 0.0;
        $sLCPD = 0.0;

        $SclD = 0.0;
        $SDsctoD = 0.0;

        for($i = 0; $i<$cant; $i++) {
            if ($dataL[$i]['recepcionletras']!='PA') {
                //Sin PA
                if ($dataL[$i]['idpadrec'] == 1) {
                    //de lima
                    if ($dataL[$i]['idmoneda'] == 1) {
                        //soles:
                        $sLSLS += $dataL[$i]['saldodoc'];
                    }else if ($dataL[$i]['idmoneda'] == 2){
                        //dólares
                        $sLSLD += $dataL[$i]['saldodoc'];
                    }
                }else {
                    //de provincia
                    if ($dataL[$i]['idmoneda'] == 1) {
                        //soles:
                        $sLSPS += $dataL[$i]['saldodoc'];
                    } else if ($dataL[$i]['idmoneda'] == 2){
                        //dólares
                        $sLSPD += $dataL[$i]['saldodoc'];
                    }
                }
            } else {
                // con PA
                if ($dataL[$i]['idpadrec'] == 1) {
                    //de Lima
                    if ($dataL[$i]['idmoneda'] == 1) {
                        // en soles
                        if($dataL[$i]['numerounico'] == 'BBVA'){
                            //Está en BBVA
                            $sLCLSBBVA += $dataL[$i]['saldodoc']; // NADA
                        }else{
                            //Está en BCP
                            $sLCLS += $dataL[$i]['saldodoc'];//CL SOLES
                            if($dataL[$i]['numerounico'] == 'BCP'){
                                $SDsctoS += $dataL[$i]['saldodoc'];
                            }else{
                                $SclS += $dataL[$i]['saldodoc'];
                            }
                        }
                    } else {
                        // en dolares
                        if($dataL[$i]['numerounico'] == 'BBVA'){
                            //Está en BBVA
                            $sLCLDBBVA += $dataL[$i]['saldodoc'];
                        }else{
                            //Está en BCP
                            $sLCLD += $dataL[$i]['saldodoc']; //CL DOLARES
                            if($dataL[$i]['numerounico'] == 'BCP'){
                                $SDsctoD += $dataL[$i]['saldodoc'];
                            }else{
                                $SclD += $dataL[$i]['saldodoc'];
                            }
                        }
                    }
                } else {
                    //provincia
                    if ($dataL[$i]['idmoneda'] == 1) {
                        // en soles
                        if($dataL[$i]['numerounico'] == 'BBVA'){
                            //Está en BBVA
                            $sLCPSBBVA += $dataL[$i]['saldodoc'];
                        }else{
                            //Está en BCP
                            $sLCPS += $dataL[$i]['saldodoc'];
                            if($dataL[$i]['numerounico'] == 'BCP'){
                                $SDsctoS += $dataL[$i]['saldodoc'];
                            }else{
                                $SclS += $dataL[$i]['saldodoc'];
                            }
                        }
                    } else {
                        if($dataL[$i]['numerounico'] == 'BBVA'){
                            //Está en BBVA
                            $sLCPDBBVA += $dataL[$i]['saldodoc'];
                        }else{
                            //Está en BCP
                            $sLCPD += $dataL[$i]['saldodoc'];
                            if($dataL[$i]['numerounico'] == 'BCP'){
                                $SDsctoD += $dataL[$i]['saldodoc'];
                            }else{
                                $SclD += $dataL[$i]['saldodoc'];
                            }
                        }
                    }
                }
            }
        }

        $dataCreditos = $reporte->resumenCreditosGeneral($txtFechaInicio,$txtFechaFinal);
        $cant = count($dataCreditos);
        $sCLS = 0.0;
        $sCLD = 0.0;
        $sCPS = 0.0;
        $sCPD = 0.0;
        for ($i = 0; $i < $cant; $i++) {
                if ($dataCreditos[$i]['idpadrec'] == 1) {
                    if ($dataCreditos[$i]['idmoneda'] == 1) {
                        $sCLS += $dataCreditos[$i]['importedoc'];
                    } else {
                        $sCLD += $dataCreditos[$i]['importedoc'];
                    }
                } else {
                    if ($dataCreditos[$i]['idmoneda'] == 1) {
                        $sCPS += $dataCreditos[$i]['importedoc'];
                    } else {
                        $sCPD += $dataCreditos[$i]['importedoc'];
                    }
                }
        }

        $dataContado = $reporte->resumenContadoGeneral($txtFechaInicio,$txtFechaFinal);
        $cant = count($dataContado);
        $sConLS = 0.0;
        $sConLD = 0.0;
        $sConPS = 0.0;
        $sConPD = 0.0;
        for ($i = 0; $i < $cant; $i++) {
                if ($dataCreditos[$i]['idpadrec'] == 1) {
                    if ($dataCreditos[$i]['idmoneda'] == 1) {
                        $sConLS += $dataCreditos[$i]['importedoc'];
                    } else {
                        $sConLD += $dataCreditos[$i]['importedoc'];
                    }
                } else {
                    if ($dataCreditos[$i]['idmoneda'] == 1) {
                        $sConPS += $dataCreditos[$i]['importedoc'];
                    } else {
                        $sConPD += $dataCreditos[$i]['importedoc'];
                    }
                }
        }
        
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 10);
        $ancho = array(90, 50, 50);
        $pdf->SetWidths($ancho);

        $pdf->_fecha = (!empty($txtFechaInicio) ? $txtFechaInicio . ' - ' : '') . $txtFechaFinal;
        $pdf->_titulo = "RESUMEN COBRANZAS GENERAL";
        $pdf->_datoPie = 'Impreso el :' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.4);
        $orientacion = array('C','R', 'R', 'R', 'R');
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $ancho = array(40,40, 40, 40, 50);
        $pdf->SetWidths($ancho);

        //INICIO DE CAMBIO:
        $sLCS = $sLCLS + $sLCPS;
        $sLCD = $sLCLD + $sLCPD;
        $sLSSBBVA = $sLCLSBBVA + $sLCPSBBVA;
        $sLSDBBVA = $sLCLDBBVA + $sLCPDBBVA;
        $totalesS = $sLCS + $sLSSBBVA;
        $totalesD = $sLCD + $sLSDBBVA;
        
        
        $sLSS = $sLSLS + $sLSPS;
        $sLSD = $sLSLD + $sLSPD;
        
        $sCS = $sCLS + $sCPS;
        $sCD = $sCLD + $sCPD;
        
        $sConS = $sConLS + $sConPS;
        $sConD = $sConLD + $sConPD;
        
        $pdf->Cell(90, 7, "Moneda: Soles");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('','LETRAS', 'CONTADO', 'CREDITO', 'TOTAL');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', 'S/. ' . number_format($sLCLS+$sLCLSBBVA+$sLSLS, 2), 'S/. ' . number_format($sConLS, 2), 'S/. ' . number_format($sCLS, 2), 'S/. ' . number_format($sLCLS+$sLCLSBBVA+$sLSLS+$sConLS+$sCLS, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('PROVINCIA', 'S/. ' . number_format($sLCPS+$sLCPSBBVA+$sLSPS, 2), 'S/. ' . number_format($sConPS, 2), 'S/. ' . number_format($sCPS, 2), 'S/. ' . number_format($sLCPS+$sLCPSBBVA+$sLSPS+$sConPS+$sCPS, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', 'S/. ' . number_format($totalesS+$sLSS, 2), 'S/. ' . number_format($sConS, 2), 'S/. ' . number_format($sCS, 2), 'S/. ' . number_format($totalesS+$sLSS+$sConS+$sCS, 2));
        $pdf->Row($fila);        
        $pdf->ln();

        $pdf->Cell(90, 7, "Moneda: Dolares");
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('','LETRAS', 'CONTADO', 'CREDITO', 'TOTAL');
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('LIMA', 'U$. ' . number_format($sLCLD+$sLCLDBBVA+$sLSLD, 2), 'U$. ' . number_format($sConLD, 2), 'U$. ' . number_format($sCLD, 2), 'U$. ' . number_format($sLCLD+$sLCLDBBVA+$sLSLD+$sConLD+$sCLD, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $fila = array('PROVINCIA', 'U$. ' . number_format($sLCPD+$sLCPDBBVA+$sLSPD, 2), 'U$. ' . number_format($sConPD, 2), 'U$. ' . number_format($sCPD, 2), 'U$. ' . number_format($sLCPD+$sLCPDBBVA+$sLSPD+$sConPD+$sCPD, 2));
        $pdf->Row($fila);
        $pdf->fill(true);
        $fila = array('TOTAL', 'U$. ' . number_format($totalesD+$sLSD, 2), 'U$. ' . number_format($sConD, 2), 'U$. ' . number_format($sCD, 2), 'U$. ' . number_format($totalesD+$sLSD+$sConD+$sCD, 2));
        $pdf->Row($fila);        
        $pdf->ln();
        
        $pdf->AliasNbPages();
        $pdf->Output();        
    }
    function reporteDevolucionesConta() {
        set_time_limit(500);
        $idproducto = $_REQUEST['idProducto'];
        if($idproducto==0){ $idproducto=''; }
        $idcliente=$_REQUEST['idCliente'];
        $idvendedor=$_REQUEST['idVendedor'];
        $orden=$_REQUEST['cmbOrden'];



        if($_REQUEST['txtFechaInicio']==""){ $fechaini='2000/01/01'; }else{    $fechaini =$_REQUEST['txtFechaInicio']; }
        if($_REQUEST['txtFechaFinal']==""){ $fechafin=date("Y-m-d").' 23:59:59'; }else{ $fechafin =$_REQUEST['txtFechaFinal'].' 23:59:59'; }

        $idordenventadiferente=-1;
        $session_idrol=$_SESSION['idrol'];
        $rolcontabilidad='17';


        $anoReporte = substr($fechaini, 0, 4);
        $mesReporte = substr($fechaini, 5, 2);
        if($mesReporte=='01'){ $nombreMes="ENERO"; }
        if($mesReporte=='02'){ $nombreMes="FEBRERO"; }
        if($mesReporte=='03'){ $nombreMes="MARZO"; }
        if($mesReporte=='04'){ $nombreMes="ABRIL"; }
        if($mesReporte=='05'){ $nombreMes="MAYO"; }
        if($mesReporte=='06'){ $nombreMes="JUNIO"; }
        if($mesReporte=='07'){ $nombreMes="JULIO"; }
        if($mesReporte=='08'){ $nombreMes="AGOSTO"; }
        if($mesReporte=='09'){ $nombreMes="SETIEMBRE"; }
        if($mesReporte=='10'){ $nombreMes="OCTUBRE"; }
        if($mesReporte=='11'){ $nombreMes="NOVIEMBRE"; }
        if($mesReporte=='12'){ $nombreMes="DICIEMBRE"; }

        $reporte = $this->AutoLoadModel('reporte');
        $producto = new Producto();
        $cliente = new Cliente();
        $actor = new Actor();
        if ($idProducto == 0) {
            $idProducto = "";
        }
        $data = $reporte->listaDevolucionesConta($fechaini,$fechafin,$idproducto,$idcliente,$idvendedor,$orden);
        $cantidadData = count($data);

        if($idproducto!=''){
            $dataProducto =  $producto->buscaProducto($idproducto);
            foreach ($dataProducto as $value) {
                $nombrepoducto=$value['nompro'];
            }
            $busquedaFiltro1="SOLO DEVOLUCIONES DEL PRODUCTO : ".html_entity_decode(substr($nombrepoducto,0,45));
        }
        if($idcliente!=''){
            $dataCliente= $cliente->buscaCliente($idcliente);
            foreach ($dataCliente as $value) {
                $nombrecliente=$value['razonsocial'];
            }
           $busquedaFiltro1="SOLO DEVOLUCIONES DEL CLIENTE : ".$nombrecliente;
        }
        if($idvendedor!=''){
            $dataActor= $actor->buscarxid($idvendedor);
            foreach ($dataActor as $value) {
                $nombrevendedor=$value['nombrecompleto'];
            }
            $busquedaFiltro1="SOLO DEVOLUCIONES DEL VENDEDOR : ".$nombrevendedor;
        }
        if($idproducto=='' and $idcliente=='' and $idvendedor==''){
            $busquedaFiltro2="DEVOLUCIONES DEL DIA ".$fechaini." AL DIA ".substr($fechafin, 0, 10)." DEL MES DE ".$nombreMes." DEL ".$anoReporte;
        }else{
            $busquedaFiltro2=$busquedaFiltro1." DEL DIA ".$fechaini." AL DIA ".substr($fechafin, 0, 10);
        }


        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $titulos = array('N','FECHA', 'PRODUCTO', 'UND', 'P.UNI', 'IMPORTE', 'IGV', 'TOTAL', '# DEVOL', 'ORD.VENTA', 'TIPO', '# COMPROBANTE', '# NOTA CREDITO');
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(8,14, 89, 7, 16, 17, 15, 17, 12,17,22,22,20);
        $orientacion = array('C', 'C', '', '', 'R', 'R', 'R', 'R', 'R', '');
//        $pdf->AddPage();

        $pdf->SetWidths($ancho);
        $pdf->_titulos = $titulos;
        $pdf->_titulo = " ".$busquedaFiltro2;

        $pdf->_fecha = $data[0]['codigopa'];
        $pdf->_datoPie = 'Fecha Impresion '.date('Y-m-d H:i:s');
        $pdf->AddPage();



        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $nro_aumentador=0;
        for ($i = 0; $i < $cantidadData; $i++) {
            $importe_soles_temp=0.00;
            $igv_soles_temp=0.00;
            $importe_dolares_temp=0.00;
            $igv_dolares_temp=0.00;
            $importe_temp=0.00;
            $igv_temp=0.00;

            // start extraccion de variables
             $comprobante='';
            $comprobanteNotaCredito='';
            $tipocomprobante='';
            $electronico='';
            $documento = new Documento();
            $data1 = $documento->verificasidevoluciontienefactura($data[$i]["idordenventa"]);
            $data2 = $documento->verificasidevoluciontienenotacredito($data[$i]["idordenventa"], $data[$i]["iddevolucion"]);
            $data3 = $documento->verificasidevoluciontieneboleta($data[$i]["idordenventa"], $data[$i]["iddevolucion"]);

                if(count($data1)>=1){ //FACTURA
                   if($data1[0]["electronico"]==1){
                      $electronico='';
                      $serieFactura=$documento->add_ceros($data1[0]['serie'], 3);
                      $serieFactura="F".$serieFactura;
                      $correlativoFactura=$documento->add_ceros($data1[0]['numdoc'], 8);
                  }

                  if($data1[0]["electronico"]==0){
                      $electronico='FISICA';
                      $serieFactura=$data1[0]['serie'];
                      $correlativoFactura=$data1[0]['numdoc'];
                  }
                  $comprobante=$serieFactura.' - '.$correlativoFactura;
                  $tipocomprobante="FACTURA ".$electronico;
                }


                if(count($data3)>=1){ //BOLETA
                     if($data3[0]["electronico"]==1){
                        $electronico='';
                        $serieBoleta=$documento->add_ceros($data3[0]['serie'], 3);
                        $serieBoleta='B'.$serieBoleta;
                        $correlativoBoleta=$documento->add_ceros($data3[0]['numdoc'], 8);
                    }
                    if($data3[0]["electronico"]==0){
                      $electronico='FISICA';
                      $serieBoleta=$data3[0]['serie'];
                      $correlativoBoleta=$data3[0]['numdoc'];
                    }
                    $comprobante=$serieBoleta.' - '.$correlativoBoleta;
                    $tipocomprobante="BOLETA ".$electronico;
                }

                if(count($data2)==1){ //NOTA CREDITO
                    if($data2[0]["electronico"]==1){
                        $electronico='';
                        $serieNotaCredito=$documento->add_ceros($data2[0]['serie'], 3);
                        $serieNotaCredito='F'.$serieNotaCredito;
                        $correlativoNotaCredito=$documento->add_ceros($data2[0]['numdoc'], 8);
                    }
                    if($data2[0]["electronico"]==0){

                        $serieNotaCredito=$data2[0]['serie'];
                        $correlativoNotaCredito=$data2[0]['numdoc'];
                    }

                  $comprobanteNotaCredito=$serieNotaCredito.' - '.$correlativoNotaCredito;
                }

                if(count($data1)==0 and count($data3)==0){
                    $tipocomprobante="GUIA MADRE";
                }
            // end extraccion de variables

            $nro_aumentador=$nro_aumentador+1;
            if($data[$i]["simbolo"]=="S/"){
                $importe_soles_temp=$data[$i]["total"]/1.18;
                $igv_soles_temp=$data[$i]["total"]-$importe_soles_temp;
                $importe_temp=$importe_soles_temp;
                $igv_temp=$igv_soles_temp;
            }
            if($data[$i]["simbolo"]=="US $"){
                $importe_dolares_temp=$data[$i]["total"]/1.18;
                $igv_dolares_temp=$data[$i]["total"]-$importe_dolares_temp;
                $importe_temp=$importe_dolares_temp;
                $igv_temp=$igv_dolares_temp;
            }

            $fila = array($nro_aumentador,substr($data[$i]["fechaaprobada"],0,10), html_entity_decode(substr($data[$i]["nompro"],0,55), ENT_QUOTES, 'UTF-8'),$data[$i]["cantidad"],$data[$i]["simbolo"].' '.number_format($data[$i]["precio"],2),$data[$i]["simbolo"].' '.number_format($importe_temp,2),$data[$i]["simbolo"].' '.number_format($igv_temp, 2),$data[$i]["simbolo"].' '.number_format($data[$i]["total"], 2),$data[$i]["iddevolucion"],$data[$i]["codigov"],$tipocomprobante,$comprobante,$comprobanteNotaCredito);
            if($data[$i]["simbolo"]=="S/"){
            $sumImporteSoles=$importe_soles_temp+$sumImporteSoles;
            $sumIgvSoles=$igv_soles_temp+$sumIgvSoles;
            $sumTotalSoles=$data[$i]["total"]+$sumTotalSoles;
            }
            if($data[$i]["simbolo"]=="US $"){
            $sumImporteDolares=$importe_dolares_temp+$sumImporteDolares;
            $sumIgvDolares=$igv_dolares_temp+$sumIgvDolares;
            $sumTotalDolares=$data[$i]["total"]+$sumTotalDolares;
            }


            // start salto por cada diferente orden venta siempre y cuando sea contabilidad
            if($session_idrol==$rolcontabilidad){ $nada=""; }else{
                if($data[$i]["idordenventa"]!=$idordenventadiferente){
                   if($i>=1){
                      $pdf->ln();
                      $pdf->ln();
                   }
                }
                $idordenventadiferente=$data[$i]["idordenventa"];
            }
            // end salto por cada diferente orden venta  siempre y cuando sea contabilidad


            $pdf->Row($fila);


        }

        $pdf->ln();
        //$pdf->Cell(115);
        $pdf->Cell(134, 5, "TOTAL SOLES", 1, 0, 'R', false);
        $pdf->Cell(17, 5, 'S/ ' .number_format($sumImporteSoles, 2), 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'S/ ' .number_format($sumIgvSoles, 2), 1, 0, 'R', false);
        $pdf->Cell(19, 5, 'S/ ' .number_format($sumTotalSoles, 2), 1, 0, 'R', false);


        $pdf->ln();
        //$pdf->Cell(115);
        $pdf->Cell(134, 5, "TOTAL DOLARES", 1, 0, 'R', false);
        $pdf->Cell(17, 5, 'US $ ' .number_format($sumImporteDolares, 2), 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'US $ ' .number_format($sumIgvDolares, 2), 1, 0, 'R', false);
        $pdf->Cell(19, 5, 'US $ ' .number_format($sumTotalDolares, 2), 1, 0, 'R', false);

        //***********
        $pdf->AliasNbPages();

        $pdf->Output();
    }
    
    
    function ventasfacturadonofacturado1() {
        set_time_limit(1000);
        $url_fechaini=$_REQUEST['txtFechaInicio'];
        $url_fechafin=$_REQUEST['txtFechaFinal'];
        $url_txtFechaEmisionInicio = $_REQUEST['txtFechaEmisionInicio'];
        $url_txtFechaEmisionFinal = $_REQUEST['txtFechaEmisionFinal'];
        $url_idmoneda=$_REQUEST['cmbMoneda'];
        $url_situacion=$_REQUEST['cmbSituacion'];
        $url_monto=$_REQUEST['cmbMonto'];
        $url_anulados=$_REQUEST['cmbAnulados'];
        $url_opcion = $_REQUEST['cmbFiltro']; // 0 = todo, 1 = facturado, 2 = no facturado
        $esAnulado=$_REQUEST['cmbEstado'];
        $filtro="";
        if($url_idmoneda==1){ $filtro="SOLO EN SOLES"; }
        if($url_idmoneda==2){ $filtro="SOLO EN DOLARES"; }

        $listar_ventasfacturadonofacturado1 = array();
        $reporte = $this->AutoLoadModel('reporte');

        if($url_fechaini || $url_fechafin || ( $url_fechaini && $url_fechafin)){

            $listar_ventasfacturadonofacturado1 = $reporte->ventasfacturadonofacturado1($url_fechaini,$url_fechafin,$url_idmoneda, $url_situacion,$url_monto,$url_anulados);
            //********************************* Proceso de trasmutacion de ovs generadas de otros dias pero facturadas segun la fecha enviada
            $get_segregado_idordenventas1='';
            for ($i = 0; $i < count($listar_ventasfacturadonofacturado1); $i++) {
                $idordenventa = $listar_ventasfacturadonofacturado1[$i]['idordenventa'];
                $get_segregado_idordenventas1.=$idordenventa.',';
            }
            $get_segregado_idordenventas1=substr($get_segregado_idordenventas1,0,-1);
        }

        if($url_txtFechaEmisionInicio || $url_txtFechaEmisionFinal || ( $url_txtFechaEmisionInicio && $url_txtFechaEmisionFinal)){
            $url_fechaini = $url_txtFechaEmisionInicio;
            $url_fechafin = $url_txtFechaEmisionFinal;
        }
        
        $esAnulado = $url_anulados;
        $listar_ovs_de_comprobantesFaltantes = array();
        if ($url_opcion != 2) {
            $listar_ovs_de_comprobantesFaltantes = $reporte->listar_ovs_de_comprobantesFaltantes($url_fechaini,$url_fechafin,$url_idmoneda,$get_segregado_idordenventas1, $url_situacion,$url_monto,$url_anulados);
        }
        $idordenventa=-1;
        $get_segregado_idordenventasFaltantes='';
        for ($i = 0; $i < count($listar_ovs_de_comprobantesFaltantes); $i++) {
            if($idordenventa!=$listar_ovs_de_comprobantesFaltantes[$i]['idordenventa']){
                $idordenventa = $listar_ovs_de_comprobantesFaltantes[$i]['idordenventa'];
                $get_segregado_idordenventasFaltantes.=$idordenventa.',';
            }
        }
        $get_segregado_idordenventasFaltantes=substr($get_segregado_idordenventasFaltantes,0,-1);
        if($get_segregado_idordenventas1!="" and $get_segregado_idordenventasFaltantes==""){
            $get_segregado_total=$get_segregado_idordenventas1;
        }
        if($get_segregado_idordenventas1=="" and $get_segregado_idordenventasFaltantes!=""){
            $get_segregado_total=$get_segregado_idordenventasFaltantes;
        }
        if($get_segregado_idordenventas1!="" and $get_segregado_idordenventasFaltantes!=""){
            $get_segregado_total=$get_segregado_idordenventas1.','.$get_segregado_idordenventasFaltantes;
        }
        $data = $reporte->ventasfacturadonofacturado2($get_segregado_total);
        //*********************************
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $titulos = array('N','FECHA.OV', 'FECHA.DES', 'ORD VENTA', 'RUC/DNI', 'CLIENTE', 'FECHA COMPROBANTE', 'FACTURA', 'BOLETA', 'GUIA REMI', 'BI FACTURA', 'IGV FACT', 'BI BOLETA', 'IMPORT GUIA', 'TOTAL', 'Monto Perce','%','Est Guia','Est comprobante');
        $pdf->SetFont('Helvetica', 'B', 5.3);
        $ancho = array(6,13, 15, 15, 14, 35, 19, 17, 17, 12,15,14,14,17,14,15,5,11,16);
        $orientacion = array('C', 'C', 'C', 'C', '', 'R', 'L', 'C', 'C', 'L', 'L','','','','','R','R','R','R');
//        $pdf->AddPage();

        $pdf->SetWidths($ancho);
        $pdf->SetMargins( 10.00125*0.625 , 10.00125*0.625 );
        $pdf->_titulos = $titulos;
        if ($url_opcion == 0) {
            $pdf->_titulo = "REPORTE DE VENTAS DE LO FACTURADO Y NO FACTURADO DEL ".$url_fechaini." AL ".$url_fechafin." ".$filtro;
        } else if ($url_opcion == 1) {
            $pdf->_titulo = "REPORTE DE VENTAS DE LO FACTURADO DEL ".$url_fechaini." AL ".$url_fechafin." ".$filtro;
        } else if ($url_opcion == 2) {
            $pdf->_titulo = "REPORTE DE VENTAS DE LO NO FACTURADO DEL ".$url_fechaini." AL ".$url_fechafin." ".$filtro;
        }
        

        $pdf->_fecha = $data[0]['codigopa'];
        $pdf->_datoPie = 'Fecha Impresion '.date('Y-m-d H:i:s');
        $pdf->AddPage();

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        $nro_aumentador=0;



        $sum_importeGuia_soles=0.00;
        $sum_biBoleta_soles=0.00;
        $sum_totalcomprobante_soles=0.00;
        $sum_subtotalFactura_soles=0.00;
        $sum_igvFactura_soles=0.00;
        $sum_percepcion_soles=0.00;
        $sum_importeGuia_dolares=0.00;
        $sum_biBoleta_dolares=0.00;
        $sum_totalcomprobante_dolares=0.00;
        $sum_subtotalFactura_dolares=0.00;
        $sum_igvFactura_dolares=0.00;
        $sum_percepcion_dolares=0.00;
        $idordenventaTemp=-1;

        for ($i = 0; $i < count($data); $i++) {
            $serieGRemision='';
            $numGRemision='';
            $serieFactura="";
            $correlativoFactura="";
            $comprobanteFactura="";

            $importeGuia_soles=0.00;
            $subtotalFactura_soles=0.00;
            $igvFactura_soles=0.00;
            $totalcomprobante_soles=0.00;
            $percepcion_soles=0.00;
            $biBoleta_soles=0.00;
            $porcentaje_percepcion_soles='';

            $importeGuia_dolares=0.00;
            $subtotalFactura_dolares=0.00;
            $igvFactura_dolares=0.00;
            $totalcomprobante_dolares=0.00;
            $percepcion_dolares=0.00;
            $biBoleta_dolares=0.00;
            $porcentaje_percepcion_dolares='';

            $que_comprobante_se_sumara="";
            $serieBoleta="";
            $correlativoBoleta="";
            $comprobanteBoleta='';
            $tipocomprobante='';
            $electronico='';
            $moneda='';
            $estado_ov='';
            $estadoComprobante='';
            $documento = new Documento();
            $listar_comprobantes=$documento->listar_comprobantes($data[$i]['idordenventa'],$esAnulado);


            $tiene_comprobantes=count($listar_comprobantes);
            $listar_guia_remision=$documento->listar_guia_remision($data[$i]['idordenventa']);
            $tiene_guia_remision=count($listar_guia_remision);


            if($data[$i]['estado_ov']=='1'){ $estado_ov='Activo'; }
            if($data[$i]['estado_ov']=='0'){ $estado_ov='Anulado'; }

//*************SE HACE DOS IMPRESIONES porque cuando comprobantes es igual a CERO  solo se imprime una fila
//*************PERO CUANDO TIENE COMPROBANTES NO SABEMOS CUANTOS COMPROBANTES TENDRA UNA MISMA GUIA
//*************POR ENDE UNA GUIA PUEDE TENER VARIAS FACTURAS (VARIAS IMPRESIONES EN UNA MISMA GUIA)
        if($tiene_comprobantes == 0 && ($url_opcion == 2 || $url_opcion == 0)) {
            if($tiene_comprobantes==0 and $tiene_guia_remision>=1){
                $serieGRemision=$listar_guia_remision[0]['serie'];
                $numGRemision=$listar_guia_remision[0]['numdoc'];
                //start solo suma guia que esta activa y lo hace una sola vez por orden
                if($idordenventaTemp!=$data[$i]['idordenventa'] and $data[$i]['estado_ov']=='1'){
                    if($data[$i]['idmoneda']=='1'){
                        $importeGuia_soles=$data[$i]['importeov'];
                        $sum_importeGuia_soles=$sum_importeGuia_soles+$importeGuia_soles;
                    }
                    if($data[$i]['idmoneda']=='2'){
                        $importeGuia_dolares=$data[$i]['importeov'];
                        $sum_importeGuia_dolares=$sum_importeGuia_dolares+$importeGuia_dolares;
                    }

                }
                //end solo suma guia que esta activa y lo hace una sola vez por orden
            }

            //START  CUANDO SOLO TIENE ORDEN DE VENTA Y NO TIENE GUIA DE REMISION, NI FACTURA, NI BOLETA
            if($tiene_comprobantes==0 and $tiene_guia_remision==0 and $data[$i]['estado_ov']=='1'){
                //start suma el vaor de la ov a la constantes del total de guias de remision y lo hace una sola vez por orden
                if($idordenventaTemp!=$data[$i]['idordenventa'] and  $data[$i]['estado_ov']=='1'){
                    if($data[$i]['idmoneda']=='1'){
                        $importeGuia_soles=$data[$i]['importeov'];
                        $sum_importeGuia_soles=$sum_importeGuia_soles+$importeGuia_soles;
                    }
                    if($data[$i]['idmoneda']=='2'){
                        $importeGuia_dolares=$data[$i]['importeov'];
                        $sum_importeGuia_dolares=$sum_importeGuia_dolares+$importeGuia_dolares;
                    }
                }
                //end suma el vaor de la ov a la constantes del total de guias de remision y lo hace una sola vez por orden
            }
            //END  CUANDO SOLO TIENE ORDEN DE VENTA Y NO TIENE GUIA DE REMISION, NI FACTURA, NI BOLETA

//            ***************************************************************************************************

            // START  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
                        if($data[$i]['ruc']==''){
                            $ruc_dni=$data[$i]['dni'];
                        }else{
                            $ruc_dni=$data[$i]['ruc'];
                        }

                        if($data[$i]['idmoneda']=='1'){
                          $moneda='S/';
                        }
                        if($data[$i]['idmoneda']=='2'){
                          $moneda='US $';
                        }

                        if($subtotalFactura_soles>0){
                         $percepcion_soles=$moneda.' '.number_format($percepcion_soles, 2);
                         $porcentaje_percepcion_soles=$data[$i]['percepcion']*100;
                         $porcentaje_percepcion_soles=$porcentaje_percepcion_soles.'%';
                        }else{
                          $percepcion_soles='';
                          $porcentaje_percepcion_soles='';
                        }

                        if($subtotalFactura_dolares>0){
                            $percepcion_dolares=$moneda.' '.number_format($percepcion_dolares, 2);
                            $porcentaje_percepcion_dolares=$data[$i]['percepcion']*100;
                            $porcentaje_percepcion_dolares=$porcentaje_percepcion_dolares.'%';
                        }else{
                            $percepcion_dolares='';
                            $porcentaje_percepcion_dolares='';
                        }

                        $nro_aumentador=$nro_aumentador+1;

                        if($data[$i]['idmoneda']=='1'){
                            $fila = array($nro_aumentador,$data[$i]['fordenventa'],$data[$i]['fechadespacho'],$data[$i]['codigov'],
                            $ruc_dni,html_entity_decode(substr($data[$i]['razonsocial'], 0, 27), ENT_QUOTES),
                            $fechaComprobante,$comprobanteFactura,$comprobanteBoleta,$serieGRemision.'-'.$numGRemision,
                            $moneda.' '.number_format($subtotalFactura_soles, 2),
                            $moneda.' '.number_format($igvFactura_soles, 2),
                            $moneda.' '.number_format($biBoleta_soles, 2),
                            $moneda.' '.number_format($importeGuia_soles, 2),
                            $moneda.' '.number_format($totalcomprobante_soles, 2),
                            $percepcion_soles,
                            $porcentaje_percepcion_soles,
                            $estado_ov,$estadoComprobante);
                        }
                        if($data[$i]['idmoneda']=='2'){
                            $fila = array($nro_aumentador,$data[$i]['fordenventa'],$data[$i]['fechadespacho'],$data[$i]['codigov'],
                            $ruc_dni,html_entity_decode(substr($data[$i]['razonsocial'], 0, 27), ENT_QUOTES, 'UTF-8'),
                            $fechaComprobante,$comprobanteFactura,$comprobanteBoleta,$serieGRemision.'-'.$numGRemision,
                            $moneda.' '.number_format($subtotalFactura_dolares, 2),
                            $moneda.' '.number_format($igvFactura_dolares, 2),
                            $moneda.' '.number_format($biBoleta_dolares, 2),
                            $moneda.' '.number_format($importeGuia_dolares, 2),
                            $moneda.' '.number_format($totalcomprobante_dolares, 2),
                            $percepcion_dolares,
                            $porcentaje_percepcion_dolares,
                            $estado_ov,$estadoComprobante);
                        }
                        $pdf->Row($fila);
             // END  ORDENADO DE VARIABLES PARA IMPRIMIR FILA


//            ****************************************************************************************************
        }

            if($tiene_comprobantes>=1 && ($url_opcion == 1 || $url_opcion == 0)) { // recrrera comprobantes asi esten anulados o activos pero solo sumara los activos
                                        //start limpia variables por vuelta
                                        $serieGRemision='';
                                        $numGRemision='';
                                        $importeGuia_soles=0.00;
                                        $importeGuia_dolares=0.00;
                                         //end limpia variables por vuelta

                $serieGRemision=$listar_guia_remision[0]['serie'];
                $numGRemision=$listar_guia_remision[0]['numdoc'];
                //start solo suma guia que esta activa y lo hace una sola vez por orden
                if($idordenventaTemp!=$data[$i]['idordenventa'] and $data[$i]['estado_ov']=='1'){
                    if($data[$i]['idmoneda']=='1'){
                        $importeGuia_soles=$data[$i]['importeov'];
                        $sum_importeGuia_soles=$sum_importeGuia_soles+$importeGuia_soles;
                    }
                    if($data[$i]['idmoneda']=='2'){
                        $importeGuia_dolares=$data[$i]['importeov'];
                        $sum_importeGuia_dolares=$sum_importeGuia_dolares+$importeGuia_dolares;
                    }
                }
                //end solo suma guia que esta activa y lo hace una sola vez por orden

                for($j=0;$j<count($listar_comprobantes);$j++){
                                        //start limpia variables por vuelta
                                        $serieFactura="";
                                        $correlativoFactura="";
                                        $comprobanteFactura="";

                                        $subtotalFactura_soles=0.00;
                                        $igvFactura_soles=0.00;
                                        $totalcomprobante_soles=0.00;
                                        $percepcion_soles=0.00;
                                        $biBoleta_soles=0.00;
                                        $porcentaje_percepcion_soles='';

                                        $subtotalFactura_dolares=0.00;
                                        $igvFactura_dolares=0.00;
                                        $totalcomprobante_dolares=0.00;
                                        $percepcion_dolares=0.00;
                                        $biBoleta_dolares=0.00;
                                        $porcentaje_percepcion_dolares='';

                                        $que_comprobante_se_sumara="";
                                        $serieBoleta="";
                                        $correlativoBoleta="";
                                        $comprobanteBoleta='';
                                        $tipocomprobante='';
                                        $electronico='';
                                        $moneda='';
                                        $estado_ov='';
                                        $estadoComprobante='';
                                        //end limpia variables por vuelta
                                        if($data[$i]['estado_ov']=='1'){ $estado_ov='Activo'; }
                                        if($data[$i]['estado_ov']=='0'){ $estado_ov='Anulado'; }

                    if($listar_comprobantes[$j]['nombredoc']==1){ // si tiene facturas
                            // START OBTENIENDO CORRELATIVO FACTURA
                                if($listar_comprobantes[$j]["electronico"]==1){
                                   $electronico='';
                                   $serieFactura=$documento->add_ceros($listar_comprobantes[$j]['serie'], 3);
                                   $serieFactura="F".$serieFactura;
                                   $correlativoFactura=$documento->add_ceros($listar_comprobantes[$j]['numdoc'], 8);
                                }

                               if($listar_comprobantes[$j]["electronico"]==0){
                                   $electronico='FISICA';
                                   $serieFactura=$listar_comprobantes[$j]['serie'];
                                   $correlativoFactura=$listar_comprobantes[$j]['numdoc'];
                                }
                               $comprobanteFactura=$serieFactura.' - '.$correlativoFactura;
                               $fechaComprobante=$listar_comprobantes[$j]['fechadoc'];
                               $tipocomprobante="FACTURA ".$electronico;
                            // END OBTENIENDO CORRELATIVO FACTURA

                    if($data[$i]['idmoneda']=='1'){
                        $subtotalFactura_soles=$listar_comprobantes[$j]['montofacturado']-$listar_comprobantes[$j]['montoigv'];
                        $igvFactura_soles=$listar_comprobantes[$j]['montoigv'];
                        $totalcomprobante_soles=$listar_comprobantes[$j]['montofacturado'];
                        $percepcion_soles=$listar_comprobantes[$j]['montofacturado']*$data[$i]['percepcion'];
                        $percepcion_soles=number_format($percepcion_soles, 2);
                    }
                    if($data[$i]['idmoneda']=='2'){
                        $subtotalFactura_dolares=$listar_comprobantes[$j]['montofacturado']-$listar_comprobantes[$j]['montoigv'];
                        $igvFactura_dolares=$listar_comprobantes[$j]['montoigv'];
                        $totalcomprobante_dolares=$listar_comprobantes[$j]['montofacturado'];
                        $percepcion_dolares=$listar_comprobantes[$j]['montofacturado']*$data[$i]['percepcion'];
                        $percepcion_dolares=number_format($percepcion_dolares, 2);
                    }
                        if($listar_comprobantes[$j]['esAnulado']==0){ $estadoComprobante='Activo'; }
                        if($listar_comprobantes[$j]['esAnulado']==1){ $estadoComprobante='Anulado'; }
                        $que_comprobante_se_sumara='FACTURA';
                    }

                    if($listar_comprobantes[$j]['nombredoc']==2){ // si tiene boleta
                            // START OBTENIENDO CORRELATIVO BOLETA
                                if($listar_comprobantes[$j]["electronico"]==1){
                                    $electronico='';
                                    $serieBoleta=$documento->add_ceros($listar_comprobantes[$j]['serie'], 3);
                                    $serieBoleta='B'.$serieBoleta;
                                    $correlativoBoleta=$documento->add_ceros($listar_comprobantes[$j]['numdoc'], 8);
                                }
                                if($listar_comprobantes[$j]["electronico"]==0){
                                    $electronico='FISICA';
                                    $serieBoleta=$listar_comprobantes[$j]['serie'];
                                    $correlativoBoleta=$listar_comprobantes[$j]['numdoc'];
                                }
                                $comprobanteBoleta=$serieBoleta.' - '.$correlativoBoleta;
                                $tipocomprobante="BOLETA ".$electronico;
                            // END OBTENIENDO CORRELATIVO BOLETA

                        if($data[$i]['idmoneda']=='1'){
                            $biBoleta_soles=$listar_comprobantes[$j]['montofacturado'];
                            $totalcomprobante_soles=$listar_comprobantes[$j]['montofacturado'];
                        }
                        if($data[$i]['idmoneda']=='2'){
                            $biBoleta_dolares=$listar_comprobantes[$j]['montofacturado'];
                            $totalcomprobante_dolares=$listar_comprobantes[$j]['montofacturado'];
                        }

                        if($listar_comprobantes[$j]['esAnulado']==0){ $estadoComprobante='Activo'; }
                        if($listar_comprobantes[$j]['esAnulado']==1){ $estadoComprobante='Anulado'; }
                        $que_comprobante_se_sumara='BOLETA';
                    }
                    //start solo sumando el comprobante que esta activo
                    if($que_comprobante_se_sumara=='BOLETA' and $listar_comprobantes[$j]['esAnulado']==0){
                        if($data[$i]['idmoneda']=='1'){
                            $sum_biBoleta_soles=$sum_biBoleta_soles+$biBoleta_soles;
                            $sum_totalcomprobante_soles=$sum_totalcomprobante_soles+$totalcomprobante_soles;
                        }
                        if($data[$i]['idmoneda']=='2'){
                            $sum_biBoleta_dolares=$sum_biBoleta_dolares+$biBoleta_dolares;
                            $sum_totalcomprobante_dolares=$sum_totalcomprobante_dolares+$totalcomprobante_dolares;
                        }
                    }
                    if($que_comprobante_se_sumara=='FACTURA' and $listar_comprobantes[$j]['esAnulado']==0){
                        if($data[$i]['idmoneda']=='1'){
                            $sum_subtotalFactura_soles=$sum_subtotalFactura_soles+$subtotalFactura_soles;
                            $sum_igvFactura_soles=$sum_igvFactura_soles+$igvFactura_soles;
                            $sum_totalcomprobante_soles=$sum_totalcomprobante_soles+$totalcomprobante_soles;
                            $sum_percepcion_soles=$sum_percepcion_soles+$percepcion_soles;
                        }
                        if($data[$i]['idmoneda']=='2'){
                            $sum_subtotalFactura_dolares=$sum_subtotalFactura_dolares+$subtotalFactura_dolares;
                            $sum_igvFactura_dolares=$sum_igvFactura_dolares+$igvFactura_dolares;
                            $sum_totalcomprobante_dolares=$sum_totalcomprobante_dolares+$totalcomprobante_dolares;
                            $sum_percepcion_dolares=$sum_percepcion_dolares+$percepcion_dolares;
                        }
                    }
                    //
                    $idordenventaTemp=$data[$i]['idordenventa'];

//            ***************************************************************************************************

            // START  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
                        if($data[$i]['ruc']==''){
                            $ruc_dni=$data[$i]['dni'];
                        }else{
                            $ruc_dni=$data[$i]['ruc'];
                        }

                        if($data[$i]['idmoneda']=='1'){
                          $moneda='S/';
                        }
                        if($data[$i]['idmoneda']=='2'){
                          $moneda='US $';
                        }

                        if($subtotalFactura_soles>0){
                         $percepcion_soles=$moneda.' '.$percepcion_soles;
                         $porcentaje_percepcion_soles=$data[$i]['percepcion']*100;
                         $porcentaje_percepcion_soles=$porcentaje_percepcion_soles.'%';
                        }else{
                          $percepcion_soles='';
                          $porcentaje_percepcion_soles='';
                        }

                        if($subtotalFactura_dolares>0){
                            $percepcion_dolares=$moneda.' '.$percepcion_dolares;
                            $porcentaje_percepcion_dolares=$data[$i]['percepcion']*100;
                            $porcentaje_percepcion_dolares=$porcentaje_percepcion_dolares.'%';
                        }else{
                            $percepcion_dolares='';
                            $porcentaje_percepcion_dolares='';
                        }

                        $nro_aumentador=$nro_aumentador+1;

                        if($data[$i]['idmoneda']=='1'){
                            $fila = array($nro_aumentador,$data[$i]['fordenventa'],$data[$i]['fechadespacho'],$data[$i]['codigov'],
                            $ruc_dni,html_entity_decode(substr($data[$i]['razonsocial'], 0, 27), ENT_QUOTES, 'UTF-8'),
                            $fechaComprobante,$comprobanteFactura,$comprobanteBoleta,$serieGRemision.'-'.$numGRemision,
                            $moneda.' '.number_format($subtotalFactura_soles, 2),
                            $moneda.' '.number_format($igvFactura_soles, 2),
                            $moneda.' '.number_format($biBoleta_soles, 2),
                            $moneda.' '.number_format($importeGuia_soles, 2),
                            $moneda.' '.number_format($totalcomprobante_soles, 2),
                            $percepcion_soles,
                            $porcentaje_percepcion_soles,
                            $estado_ov,$estadoComprobante);
                        }
                        if($data[$i]['idmoneda']=='2'){
                            $fila = array($nro_aumentador,$data[$i]['fordenventa'],$data[$i]['fechadespacho'],$data[$i]['codigov'],
                            $ruc_dni,html_entity_decode(substr($data[$i]['razonsocial'], 0, 27), ENT_QUOTES, 'UTF-8'),
                            $fechaComprobante,$comprobanteFactura,$comprobanteBoleta,$serieGRemision.'-'.$numGRemision,
                            $moneda.' '.number_format($subtotalFactura_dolares, 2),
                            $moneda.' '.number_format($igvFactura_dolares, 2),
                            $moneda.' '.number_format($biBoleta_dolares, 2),
                            $moneda.' '.number_format($importeGuia_dolares, 2),
                            $moneda.' '.number_format($totalcomprobante_dolares, 2),
                            $percepcion_dolares,
                            $porcentaje_percepcion_dolares,
                            $estado_ov,$estadoComprobante);
                        }
                        $pdf->Row($fila);
             // END  ORDENADO DE VARIABLES PARA IMPRIMIR FILA


//            ****************************************************************************************************

                }
            }

       }
        $pdf->ln();
              //$pdf->Cell(115);
        $pdf->Cell(151, 5, "TOTAL SOLES", 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'S/ ' .number_format($sum_subtotalFactura_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(14, 5, 'S/ ' .number_format($sum_igvFactura_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'S/ ' .number_format($sum_biBoleta_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(17, 5, 'S/ ' .number_format($sum_importeGuia_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(17, 5, 'S/ ' .number_format($sum_totalcomprobante_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(15, 5, 'S/ ' .number_format($sum_percepcion_soles, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->Cell(151, 5, "TOTAL DOLARES", 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'US $ ' .number_format($sum_subtotalFactura_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(14, 5, 'US $ ' .number_format($sum_igvFactura_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'US $ ' .number_format($sum_biBoleta_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(17, 5, 'US $ ' .number_format($sum_importeGuia_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(17, 5, 'US $ ' .number_format($sum_totalcomprobante_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(15, 5, 'US $ ' .number_format($sum_percepcion_dolares, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->ln();
        $pdf->ln();

        $totalNoFacturadoSoles=$sum_importeGuia_soles-$sum_totalcomprobante_soles;
        $totalNoFacturadoDolares=$sum_importeGuia_dolares-$sum_totalcomprobante_dolares;

        $pdf->Cell(151, 5, "", 0, 0, 'R', false);
        $pdf->Cell(46, 5, 'TOTAL EN VENTAS FACTURADO', 1, 0, 'C', true);
        $pdf->Cell(49, 5,  'TOTAL EN VENTAS NO FACTURADO', 1, 0, 'C', true);
        $pdf->ln();
        $pdf->Cell(130, 5, "", 0, 0, 'R', false);
        $pdf->Cell(21, 5, "TOTAL SOLES", 1, 0, 'R', false);
        $pdf->Cell(46, 5, 'S/ ' .number_format($sum_totalcomprobante_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(49, 5, 'S/ ' .number_format($totalNoFacturadoSoles, 2), 1, 0, 'R', false);
        $pdf->ln();

        $pdf->Cell(130, 5, "", 0, 0, 'R', false);
        $pdf->Cell(21, 5, "TOTAL DOLARES", 1, 0, 'R', false);
        $pdf->Cell(46, 5, 'US $ ' .number_format($sum_totalcomprobante_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(49, 5, 'US $ ' .number_format($totalNoFacturadoDolares, 2), 1, 0, 'R', false);
        $pdf->ln();
        //***********
        $pdf->AliasNbPages();

        $pdf->Output();
    } 
    
      function formatoInventario() {
        $reporte = $this->AutoLoadModel('reporte');
        $bloques = $this->AutoLoadModel('bloques');
        $idInventario = $_REQUEST['lstInventario'];
        $idBloque = $_REQUEST['lstBloques'];
        $idProducto = $_REQUEST['idProducto'];
        $numeroInventario = $_REQUEST['txtNumeroInventario'];
        $formatoparaconteo = $_REQUEST['chkformatoparaconteo'];
        $lstStock = $_REQUEST['lstStock'];
        if ($formatoparaconteo) {
            $formatoparaconteo = 1;
        } else {
            $formatoparaconteo = 0;
        }

        if ($numeroInventario == "") {
            $numeroInventario = "___________";
        }
        if ($_REQUEST['lstConteo'] == 1) {
            $msmConteoX = "CONTEO 1";
        }
        if ($_REQUEST['lstConteo'] == 2) {
            $msmConteoX = "CONTEO 2";
        }
        if ($_REQUEST['lstConteo'] == 3) {
            $msmConteoX = "CONTEO 3";
        }

        $contador = 0;
        $data = $reporte->reporteInventario($idInventario, $idBloque, $idProducto, $lstStock);
        $cantidadData = count($data);
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        if ($formatoparaconteo == 1) {
            $titulos = array(utf8_decode('N°'), 'CODIGO', 'DESCRIPCION', 'U.M.', $msmConteoX, 'MERMA', 'A REPARAR', 'SHOW ROOM', 'OBSERVACIONES');
        } else {
            $titulos = array(utf8_decode('N°'), 'CODIGO', 'DESCRIPCION', 'U.M.', 'Conteo Final', 'MERMA', 'A REPARAR', 'SHOW ROOM', 'OBSERVACIONES');
        }

        $pdf->SetFont('Helvetica', 'B', 14);
        $ancho = array(7, 30, 100, 19, 20, 17, 20, 22, 45);
        $orientacion = array('L', 'L', 'L', 'C', 'C', 'C', 'C', 'C', 'C');

        $pdf->SetWidths($ancho);
        $listadoBloque = $bloques->listarBloqueFormato($idBloque);

        foreach ($listadoBloque as $v) {
            $nombreBloque = $v['codigo'];
        }

        $pdf->_titulo = "INVENTARIO GENERAL  " . $numeroInventario . " || BLOQUE Y/O ANAQUEL  " . '"' . $nombreBloque . '"      ' . $msmConteoX . '                                                      ';

        $pdf->AddPage();
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);


        if ($cantidadData == 0) {
            $relleno = true;
            $pdf->SetFillColor(202, 232, 234);
            $pdf->SetTextColor(12, 78, 139);
            $pdf->SetDrawColor(12, 78, 139);
            $pdf->SetLineWidth(.3);
            $pdf->SetFont('Helvetica', 'B', 14);
            $pdf->fill($relleno);

            $pdf->Cell(25, 7, 'FECHA INICIO', 1, 0, 'C', true);
            $pdf->Cell(32, 7, '', 1, 0, 'C', true);
            $pdf->Cell(28, 7, 'FECHA TERMINO', 1, 0, 'C', true);
            $pdf->Cell(54, 7, '', 1, 0, 'C', true);
            $pdf->Cell(30, 7, 'JEFE DE SISTEMAS', 1, 0, 'C', true);
            $pdf->Cell(32, 7, '', 1, 0, '', false);
            $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
            $pdf->Cell(51, 7, '', 1, 0, '', false);
            $pdf->ln();

            $pdf->Cell(25, 7, 'AUDITOR', 1, 0, 'C', true);
            $pdf->Cell(32, 7, '', 1, 0, '', false);
            $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
            $pdf->Cell(54, 7, '', 1, 0, '', false);
            $pdf->Cell(30, 7, 'JEFE DE ALMACEN', 1, 0, 'C', true);
            $pdf->Cell(32, 7, '', 1, 0, '', false);
            $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
            $pdf->Cell(51, 7, '', 1, 0, '', false);
            $pdf->ln();
            $pdf->Cell(25, 7, 'VEEDOR 1', 1, 0, 'C', true);
            $pdf->Cell(40, 7, '', 1, 0, '', false);
            $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
            $pdf->Cell(54, 7, '', 1, 0, '', false);
            $pdf->Cell(30, 7, 'VEEDOR 2', 1, 0, 'C', true);
            $pdf->Cell(32, 7, '', 1, 0, '', false);
            $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
            $pdf->Cell(51, 7, '', 1, 0, '', false);
            $pdf->ln();
            $pdf->Cell(25, 7, 'VEEDOR 3', 1, 0, 'C', true);
            $pdf->Cell(40, 7, '', 1, 0, '', false);
            $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
            $pdf->Cell(54, 7, '', 1, 0, '', false);
            $pdf->ln();
            $pdf->ln();
            $fila = $titulos;
            $pdf->Row($fila);
            $filasFantasma = 81;
        }
        if ($cantidadData >= 1) {
            for ($i = 0; $i < $cantidadData; $i++) {
                $contador = $contador + 1;
                $bloqueA = $data[$i - 1]['idbloque'];
                if ($i == 0) {
                    //                if ($i != 0) {
                    //                    $pdf->AddPage();
                    //                }
                    $relleno = true;
                    $pdf->SetFillColor(202, 232, 234);
                    $pdf->SetTextColor(12, 78, 139);
                    $pdf->SetDrawColor(12, 78, 139);
                    $pdf->SetLineWidth(.3);
                    $pdf->SetFont('Helvetica', 'B', 8);
                    $pdf->fill($relleno);

                    $pdf->Cell(25, 7, 'FECHA INICIO', 1, 0, 'C', true);
                    $pdf->Cell(32, 7, '', 1, 0, 'C', true);
                    $pdf->Cell(28, 7, 'FECHA TERMINO', 1, 0, 'C', true);
                    $pdf->Cell(54, 7, '', 1, 0, 'C', true);
                    $pdf->Cell(30, 7, 'JEFE DE SISTEMAS', 1, 0, 'C', true);
                    $pdf->Cell(32, 7, '', 1, 0, '', false);
                    $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
                    $pdf->Cell(51, 7, '', 1, 0, '', false);
                    $pdf->ln();

                    $pdf->Cell(25, 7, 'AUDITOR', 1, 0, 'C', true);
                    $pdf->Cell(32, 7, '', 1, 0, '', false);
                    $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
                    $pdf->Cell(54, 7, '', 1, 0, '', false);
                    $pdf->Cell(30, 7, 'JEFE DE ALMACEN', 1, 0, 'C', true);
                    $pdf->Cell(32, 7, '', 1, 0, '', false);
                    $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
                    $pdf->Cell(51, 7, '', 1, 0, '', false);
                    $pdf->ln();
                    $pdf->Cell(25, 7, 'VEEDOR 1', 1, 0, 'C', true);
                    $pdf->Cell(32, 7, '', 1, 0, '', false);
                    $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
                    $pdf->Cell(54, 7, '', 1, 0, '', false);
                    $pdf->Cell(30, 7, 'VEEDOR 2', 1, 0, 'C', true);
                    $pdf->Cell(32, 7, '', 1, 0, '', false);
                    $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
                    $pdf->Cell(51, 7, '', 1, 0, '', false);
                    $pdf->ln();
                    $pdf->Cell(25, 7, 'VEEDOR 3', 1, 0, 'C', true);
                    $pdf->Cell(32, 7, '', 1, 0, '', false);
                    $pdf->Cell(28, 7, 'ROL O FUNCION', 1, 0, 'C', true);
                    $pdf->Cell(54, 7, '', 1, 0, '', false);
                    $pdf->ln();
                    $pdf->ln();
                    $fila = $titulos;
                    $pdf->Row($fila);
                }
                $pdf->_titulos = $titulos;
                //obtenemos el valor de cada producto segun condicion :sea merma, reparacion, venta
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('Helvetica', '', 8.5);
                if ($formatoparaconteo == 1) {
                    $fila = array($contador, html_entity_decode($data[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode(utf8_decode($data[$i]['nompro']), ENT_QUOTES, 'UTF-8'), $data[$i]['codigoum'], '', '', '', '', '');
                } else {
                    $fila = array($contador, html_entity_decode($data[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode(utf8_decode($data[$i]['nompro']), ENT_QUOTES, 'UTF-8'), $data[$i]['codigoum'], $data[$i]['stockinventario'], '', '', '', '');
                }

                $pdf->Row($fila);
                $relleno = !$relleno;
                $pdf->fill($relleno);
            }
            $filasFantasma = 10;
        }
        for ($x = ($contador + 1); $x < ($contador + $filasFantasma); $x++) {
            $pdf->_titulos = $titulos;
            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetFont('Helvetica', '', 11);
            $fila = array($x, '', '', '', '', '', '', '', '');
            $pdf->Row($fila);
            $relleno = !$relleno;
            $pdf->fill($relleno);
        }

        $pdf->ln();
        $pdf->Cell(65, 7, 'AUDITOR CONTABILIDAD', 0, 0, 'C', FALSE);
        $pdf->Cell(4, 7, '', 0, 0, 'C', FALSE);
        $pdf->Cell(5, 7, '', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, 'JEFE DE SISTEMAS', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, 'JEFE DE ALMACEN', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, 'GERENCIA GENERAL', 0, 0, 'C', FALSE);
        $pdf->ln();
        $pdf->Cell(65, 7, '__________________________', 0, 0, 'C', FALSE);
        $pdf->Cell(4, 7, '', 0, 0, 'C', FALSE);
        $pdf->Cell(5, 7, '', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, '__________________________', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, '__________________________', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, '__________________________', 0, 0, 'C', FALSE);



        $pdf->ln();
        $pdf->ln();
        $pdf->ln();
        $pdf->ln();
        $pdf->Cell(65, 7, '', 0, 0, 'C', FALSE);
        $pdf->Cell(4, 7, '', 0, 0, 'C', FALSE);
        $pdf->Cell(5, 7, '', 0, 0, 'C', FALSE);

        $pdf->Cell(125, 7, 'OBSERVACIONES DETECTADAS POR SISTEMAS EN EL LLENADO DE LAS PLANILLAS', 0, 0, 'C', FALSE);
        $pdf->Cell(67, 7, '', 0, 0, 'C', FALSE);
        $pdf->ln();
        $pdf->ln();
        $pdf->Cell(272, 7, '___________________________________________________________________________________________________________________________', 0, 0, 'C', FALSE);
        $pdf->ln();
        $pdf->ln();
        $pdf->Cell(272, 7, '___________________________________________________________________________________________________________________________', 0, 0, 'C', FALSE);
        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reportegeneralcobranzas() {
        set_time_limit(1500);
        $reporte = $this->AutoLoadModel('reporte');
        $tipo = $this->AutoLoadModel('tipocobranza');
        $ordenGasto = $this->AutoLoadModel('ordengasto');
        $tipoCobroIni = $this->configIniTodo('TipoCobro');
        $movimiento = $this->AutoLoadModel('movimiento');
        $detalleordencobro = $this->AutoLoadModel('detalleordencobro');
        $detalleordencobroingreso = $this->AutoLoadModel('detalleordencobroingreso');
                            
        $idzona = $_REQUEST['GC-idzona'];
        $idcategoriaprincipal = $_REQUEST['GC-idcategoriaprincipal'];
        $idcategoria = $_REQUEST['GC-idcategoria'];
        $idvendedor = $_REQUEST['GC-idvendedor'];
        $idtipocobranza = $_REQUEST['GC-idtipocobranza'];
        $idtipocobro = $_REQUEST['GC-idtipocobro'];
        $fechaInicio = $_REQUEST['GC-fechaInicio'];
        $fechaFinal = $_REQUEST['GC-fechaFinal'];
        $pendiente = $_REQUEST['GC-pendiente'];
        $cancelado = $_REQUEST['GC-cancelado'];
        $octava = $_REQUEST['GC-octava'];
        $novena = $_REQUEST['GC-novena'];
        $idcobrador = $_REQUEST['GC-idcobrador'];
        $IdCliente = $_REQUEST['GC-IdCliente'];
        $IdOrdenVenta = $_REQUEST['GC-IdOrdenVenta'];
        $vendedor = $_REQUEST['GC-vendedor'];
        $tipocobro = $_REQUEST['GC-tipocobro'];        
        $recepcionLetras = $_REQUEST['GC-recepcionLetras'];
        $ubicacion = $_REQUEST['GC-ubicacion'];
        
        $nombreCobrador = "-";
        if (!empty($idcobrador)) {
            $actor = $this->AutoLoadModel('actor');
            $dataCobrador = $actor->buscarxid($idcobrador);
            $nombreCobrador = $dataCobrador[0]['nombres'] . " " . $dataCobrador[0]['apellidopaterno'] . " " . $dataCobrador[0]['apellidomaterno'];
        }
        $nombrecliente = "-";
        if (!empty($IdCliente)) {
            $clientemodel = $this->AutoLoadModel('cliente');
            $dataCliente = $clientemodel->listadoxFiltro("idcliente='$IdCliente'");
            $nombrecliente = $dataCliente[0]['razonsocial'];
        }
        
        $octavaNovena = " ";
        if (!empty($octava) && !empty($novena)) {
            $octavaNovena.=" and (wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 8 DAY) or wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 9 DAY)) and wc_detalleordencobro.`situacion`='' ";
        } elseif (!empty($novena)) {
            $octavaNovena.=" and wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 9 DAY) and wc_detalleordencobro.`situacion`='' ";
        } elseif (!empty($octava)) {
            $octavaNovena.=" and wc_detalleordencobro.`fvencimiento`=DATE_SUB(CURDATE(), INTERVAL 8 DAY) and wc_detalleordencobro.`situacion`='' ";
        }

        $situacion = "";
        if (!empty($pendiente) && !empty($cancelado)) {
            $situacion.=" and (wc_detalleordencobro.`situacion`='' or wc_detalleordencobro.`situacion`='cancelado') ";
        } elseif (!empty($cancelado)) {
            $situacion.=" and wc_detalleordencobro.`situacion`='cancelado' ";
        } elseif (!empty($pendiente)) {
            $situacion.=" and wc_detalleordencobro.`situacion`='' ";
        }
        if ($_REQUEST['fechaInicio'] != "") {
            $fechaInicio = date('Y-m-d', strtotime($_REQUEST['fechaInicio']));
        }

        if ($_REQUEST['fechaFinal'] != "") {
            $fechaFinal = date('Y-m-d', strtotime($_REQUEST['fechaFinal']));
        }
        if ($_REQUEST['fechaPagoInicio'] != "") {
            $fechaPagoInicio = date('Y-m-d', strtotime($_REQUEST['fechaPagoInicio']));
        } else {
            $fechaPagoInicio = $_REQUEST['fechaPagoInicio'];
        }
        if ($_REQUEST['fechaPagoFinal'] != "") {
            $fechaPagoFinal = date('Y-m-d', strtotime($_REQUEST['fechaPagoFinal']));
        } else {
            $fechaPagoFinal = $_REQUEST['fechaPagoFinal'];
        }
        $idcategorias = "";
        if (!empty($idcobrador)) {
            $cobrador = $this->AutoLoadModel('cobrador');
            $dataCobrador = $cobrador->buscaZonasxCobrador($idcobrador);
            $cantidadCobrador = count($dataCobrador);
            if ($cantidadCobrador != 0) {
                $idcategorias.=" and (";
                for ($i = 0; $i < $cantidadCobrador; $i++) {
                    if ($i == 0) {
                        $idcategorias.=" wc_categoria.`idcategoria`='" . $dataCobrador[$i]['idzona'] . "' ";
                    } else {
                        $idcategorias.=" or wc_categoria.`idcategoria`='" . $dataCobrador[$i]['idzona'] . "' ";
                    }
                }
                $idcategorias.=" ) ";
            } else {
                $idcategorias.=" and  wc_categoria.`idcategoria`='0' ";
            }
        } elseif (!empty($idcategoria)) {
            $idcategorias = " and wc_categoria.`idcategoria`='" . $idcategoria . "' ";
        }
        
        $pdf = new PDF_MC_Table("P", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 7);
        $ancho = array(25, 92, 15, 15, 15, 15, 15);

        $pdf->SetWidths($ancho);
        $pdf->_titulo = "Resumen General de Cobranzas";
        $pdf->_datoPie = "Fecha de impresion: " . date('Y-m-d');
        $pdf->AddPage();

        $relleno = true;
        $pdf->fill($relleno);
        $pdf->SetFillColor(156, 213, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        
        $ubicacion = explode("//", $ubicacion);
        $tempUbicacion = "";
        if ($ubicacion[0] != "Zona Geografica") {
            $tempUbicacion = $ubicacion[0];
        }
        if ($ubicacion[1] != "Zona Cobranza-Categoria") {
            $tempUbicacion .= (!empty($tempUbicacion) ? ' - ' : '') . $ubicacion[1];
        }
        if ($ubicacion[2] != "Zona Cobranza-Detalle") {
            $tempUbicacion .= (!empty($tempUbicacion) ? ' - ' : '') . $ubicacion[2];
        }

        $pdf->Cell(25, 5, "Ubicacion: ", 1, 0, 'L', true);
        $pdf->Cell(70, 5, (!empty($tempUbicacion) ? $tempUbicacion : 'Todos'), 1, 0, 'C', false);
        $pdf->Cell(25, 5, "Cobrador: ", 1, 0, 'L', true);
        $pdf->Cell(70, 5, $nombreCobrador, 1, 0, 'C', false);
        $pdf->ln();
        
        $arrayidtipocobranza[0] = "-";
        $arrayidtipocobranza[14] = "Moroso";
        $arrayidtipocobranza[15] = "Cobranza Pesada";
        $arrayidtipocobranza[16] = "Problema Potencial";
        $arrayidtipocobranza[17] = "Incobrables";
        $arrayidtipocobranza[18] = "Por vencer";
                
        $pdf->Cell(25, 5, "Tipo Cobranza: ", 1, 0, 'L', true);
        $pdf->Cell(70, 5, $arrayidtipocobranza[$idtipocobranza], 1, 0, 'C', false);
        $pdf->Cell(25, 5, "Vendedor: ", 1, 0, 'L', true);
        $pdf->Cell(70, 5, (!empty($vendedor) ? $vendedor : '-'), 1, 0, 'C', false);
        $pdf->ln();
        
        $tempCodigoOV = "-";
        if (!empty($IdOrdenVenta)) {
            $ordenventaModel = $this->AutoLoadModel('ordenventa');
            $tempCodigoOV = $ordenventaModel->sacarCodigo($IdOrdenVenta);
        }
        
        $pdf->Cell(25, 5, "Cliente: ", 1, 0, 'L', true);
        $pdf->Cell(70, 5, $nombrecliente, 1, 0, 'C', false);
        $pdf->Cell(25, 5, "Orden Venta: ", 1, 0, 'L', true);
        $pdf->Cell(70, 5, $tempCodigoOV, 1, 0, 'C', false);
        $pdf->ln();

        $pdf->Cell(25, 5, "Fecha Vencimiento: ", 1, 0, 'L', true);
        $pdf->Cell(70, 5, $fechaInicio . " - " . $fechaFinal, 1, 0, 'C', false);
        $pdf->Cell(25, 5, "Fecha Pago: ", 1, 0, 'L', true);
        $pdf->Cell(70, 5, $fechaPagoInicio . " - " . $fechaPagoFinal, 1, 0, 'C', false);
        $pdf->ln();
        
        $filtroEstado = "";
        if (!empty($pendiente)) {
            $filtroEstado = "Pendiente";
        }
        if (!empty($cancelado)) {
            if (!empty($filtroEstado)) {
                $filtroEstado .= " - ";
            }
            $filtroEstado .= "Cancelado";
        }
        if (!empty($octava)) {
            if (!empty($filtroEstado)) {
                $filtroEstado .= " - ";
            }
            $filtroEstado .= "Octava";
        }
        if (!empty($novena)) {
            if (!empty($filtroEstado)) {
                $filtroEstado .= " - ";
            }
            $filtroEstado .= "Novena";
        }
        $pdf->Cell(25, 5, "Estado: ", 1, 0, 'L', true);
        $pdf->Cell(165, 5, (!empty($filtroEstado) ? $filtroEstado : "-"), 1, 0, 'C', false);
        
        $arrayTipoCobro[1] = "Contado";
        $arrayTipoCobro[2] = "Credito";
        $arrayTipoCobro[3] = "Letras Banco";
        $arrayTipoCobro[4] = "Letras Cartera";
        $arrayTipoCobro[5] = "Letras Protestadas";
        
        $iniciogeneral = 0;
        $inicio = 1;
        $fin = 5;        
        
        if (!empty($idtipocobro)) {
            $inicio = $idtipocobro;
            $fin = $idtipocobro;
        }
        while ($inicio <= $fin) {
            if ($inicio == 3) {//letras al banco
                $filtro = "wc_detalleordencobro.`formacobro`='3' and wc_ordencobro.`tipoletra`=1";
            } elseif ($inicio == 4) { //letras Cartera
                $filtro = "wc_detalleordencobro.`formacobro`='3' and  wc_ordencobro.`tipoletra`=2";
            } elseif ($inicio == 2) {//credito
                $filtro = "wc_detalleordencobro.`formacobro`='2' and wc_detalleordencobro.referencia=''";
            } elseif ($inicio == 1) {//al contado
                $filtro = "wc_detalleordencobro.`formacobro`='1' ";
            } elseif ($inicio == 5) {//letras protestadas
                $filtro = "wc_detalleordencobro.`formacobro`='2' and (substring( wc_detalleordencobro.referencia,9,1)='p' or substring( wc_detalleordencobro.referencia,11,1)='p')";
                $filtro .= "and wc_zona.`nombrezona` not like '%incobrab%'";
            }
            $datareporte = $reporte->reportetotalgeneralcobranzas($filtro, $idzona, $idcategoriaprincipal, $idcategorias, $idvendedor, $idtipocobranza, $fechaInicio, $fechaFinal, $octavaNovena, $situacion, $fechaPagoInicio, $fechaPagoFinal, $IdCliente, $IdOrdenVenta);
            $cantidadreporte = count($datareporte);

            if ($cantidadreporte > 0) {
                $pdf->ln();
                $pdf->ln();
                $pdf->SetFont('Helvetica', 'B', 9);
                $pdf->SetFillColor(218, 239, 255);
                $pdf->Cell(190, 5, strtoupper($arrayTipoCobro[$inicio]), 0, 0, 'C', true);
                $pdf->SetFillColor(156, 213, 255);
                $tempIdpadrec = -1;
                $iniciogeneral ++;
               
                for ($i = 0; $i < $cantidadreporte; $i++) {
                    if ($datareporte[$i]['idpadrec'] != $tempIdpadrec) {
                        $tempIdpadrec = $datareporte[$i]['idpadrec'];
                        
                        $pdf->ln();
                        $pdf->ln();
                        $pdf->SetFont('Helvetica', 'B', 7.5);
                        $pdf->Cell(30, 5, "ZONA GEOGRAFICA: ", 1, 0, 'C', true);
                        $pdf->SetFont('Helvetica', '', 7.5);
                        $pdf->Cell(64, 5, $datareporte[$i]['nombrec'], 1, 0, 'C', false);
                        
                        $pdf->ln();
                        $pdf->SetFont('Helvetica', 'B', 7.5);
                        $pdf->Cell(30, 5, "MONEDA", 1, 0, 'C', true);     
                        if ($inicio == 5) {
                            $pdf->Cell(32, 5, "IMPORTE COBRO", 1, 0, 'C', true);
                            $pdf->Cell(32, 5, "GASTO DE PROTESTO", 1, 0, 'C', true);
                        }
                        $pdf->Cell(32, 5, "IMPORTE TOTAL", 1, 0, 'C', true);
                        $pdf->Cell(32, 5, "IMPORTE PAGADO", 1, 0, 'C', true);
                        $pdf->Cell(32, 5, "DEUDA TOTAL", 1, 0, 'C', true);
                    }
                    $pdf->ln();
                    $pdf->SetFont('Helvetica', '', 7.5);
                    $pdf->Cell(30, 5, $datareporte[$i]['nommoneda'], 1, 0, 'C', false);
                    if ($inicio == 5) {
                        $pdf->Cell(32, 5, number_format($datareporte[$i]['importedoc'] - $datareporte[$i]['montoprotesto'], 2), 1, 0, 'R', false);
                        $pdf->Cell(32, 5, number_format($datareporte[$i]['montoprotesto'], 2), 1, 0, 'R', false);
                    }
                    $pdf->Cell(32, 5, number_format($datareporte[$i]['importedoc'], 2), 1, 0, 'R', false);
                    $pdf->Cell(32, 5, number_format($datareporte[$i]['importedoc'] - $datareporte[$i]['saldodoc'], 2), 1, 0, 'R', false);
                    $pdf->Cell(32, 5, number_format($datareporte[$i]['saldodoc'], 2), 1, 0, 'R', false);
                        
                    $arrayGeneral[$iniciogeneral]['titulo'][$datareporte[$i]['simbolo']] = strtoupper($arrayTipoCobro[$inicio]);
                    $arrayGeneral[$iniciogeneral]['total'][$datareporte[$i]['simbolo']] += $datareporte[$i]['importedoc'];
                    $arrayGeneral[$iniciogeneral]['saldo'][$datareporte[$i]['simbolo']] += $datareporte[$i]['saldodoc'];
                }
                $iniciogeneral++;
            }
            $inicio++;
        }
        if ($iniciogeneral >= 1) {
            $pdf->ln();
            $pdf->ln();
            $pdf->ln();
            
            $pdf->SetFont('Helvetica', 'B', 9);
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(190, 5, "RESUMEN GENERAL", 0, 0, 'C', true);
            
            $pdf->ln();
            $pdf->ln();
            
            $pdf->SetFont('Helvetica', 'B', 7.5);
            $pdf->SetFillColor(156, 213, 255);
            $pdf->SetTextColor(0);
            $pdf->SetDrawColor(12, 78, 139);
            $pdf->Cell(94, 5, "", 0, 0, 'C', false);
            $pdf->Cell(32, 5, "IMPORTE TOTAL", 1, 0, 'C', true);
            $pdf->Cell(32, 5, "IMPORTE PAGADO", 1, 0, 'C', true);
            $pdf->Cell(32, 5, "DEUDA TOTAL", 1, 0, 'C', true);
            
            $pdf->ln();

            for ($j=1; $j <= $iniciogeneral; $j++) {
                $indice = 0;
                $tempTitulo = "";                
                if (!empty($arrayGeneral[$j]['total']['S/'])) {
                    $tempTitulo = $arrayGeneral[$j]['titulo']['S/'];
                    $pdf->SetFillColor(156, 213, 255);
                    $pdf->Cell(62, 5, $tempTitulo, 1, 0, 'C', true);
                    $pdf->SetFillColor(226, 242, 255);
                    $pdf->Cell(32, 5, "Soles: ", 1, 0, 'C', true);
                    $pdf->Cell(32, 5, 'S/ ' . number_format($arrayGeneral[$j]['total']['S/'], 2), 1, 0, 'R', false);                    
                    $pdf->Cell(32, 5, 'S/ ' . number_format($arrayGeneral[$j]['total']['S/']-$arrayGeneral[$j]['saldo']['S/'], 2), 1, 0, 'R', false);
                    $pdf->Cell(32, 5, 'S/ ' . number_format($arrayGeneral[$j]['saldo']['S/'], 2), 1, 0, 'R', false);
                    $pdf->ln();
                    $indice++;
                }
                if (!empty($arrayGeneral[$j]['total']['US $'])) {
                     $tempTitulo = $arrayGeneral[$j]['titulo']['US $'];
                    $pdf->SetFillColor(156, 213, 255);
                    $pdf->Cell(62, 5, $tempTitulo, 1, 0, 'C', true);
                    $pdf->SetFillColor(226, 242, 255);
                    $pdf->Cell(32, 5, "Dolares Americanos: ", 1, 0, 'C', true);
                    $pdf->Cell(32, 5, 'US $ ' . number_format($arrayGeneral[$j]['total']['US $'], 2), 1, 0, 'R', false);
                    $pdf->Cell(32, 5, 'US $ ' . number_format($arrayGeneral[$j]['total']['US $']-$arrayGeneral[$j]['saldo']['US $'], 2), 1, 0, 'R', false);
                    $pdf->Cell(32, 5, 'US $ ' . number_format($arrayGeneral[$j]['saldo']['US $'], 2), 1, 0, 'R', false);
                    $pdf->ln();
                    $indice++;                    
                }
                if ($indice == 2) {
                    $pdf->SetFillColor(156, 213, 255);
                    $pdf->setxy(10, $pdf->GetY() - 10);
                    $pdf->MultiCell(62, 10, $tempTitulo, 1, 'C', true);
                }
            }
            
        }

        $pdf->AliasNbPages();
        $pdf->Output();
    }
    
      function rankingclientesseguncontabilidad() {
        set_time_limit(500);
        $url_fechaini=$_REQUEST['txtFechaInicio'];
        $url_fechafin=$_REQUEST['txtFechaFinal'];
        $url_idmoneda=$_REQUEST['cmbMoneda'];
        $url_mostrarclientes=$_REQUEST['cmbCantidadClientes'];
        $esAnulado=$_REQUEST['cmbEstado'];
        $filtro="";
        if($url_idmoneda==1){ $filtro="SOLO EN SOLES"; }
        if($url_idmoneda==2){ $filtro="SOLO EN DOLARES"; }
        $reporte = $this->AutoLoadModel('reporte');
        $listar_ventasfacturadonofacturado1_ranking = $reporte->ventasfacturadonofacturado1_ranking($url_fechaini,$url_fechafin,$url_idmoneda,$url_mostrarclientes);
        //********************************* Proceso de trasmutacion de ovs generadas de otros dias pero facturadas segun la fecha enviada
        $get_segregado_idclientes='';
        for ($i = 0; $i < count($listar_ventasfacturadonofacturado1_ranking); $i++) {
            $array_idclientes[]=array("idcliente"=>$listar_ventasfacturadonofacturado1_ranking[$i]['idcliente']);
        }
        foreach ($array_idclientes as $val) {
           $data[]=$reporte->ventasfacturadonofacturado2_ranking($url_fechaini,$url_fechafin,$val['idcliente']);
        }

        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $titulos = array('N','FECHA.OV', 'ORD VENTA','CLIENTE','RUC/DNI','TOTAL COMPRAS','Est Guia', 'GUIA REMI','FACTURA', 'BI FACTURA', 'IGV FACT','Monto Perce','%', 'BOLETA','BI BOLETA','Est comprobante');
        $pdf->SetFont('Helvetica', 'B', 5.4);
        $ancho = array(8,13,  15,38, 14, 25,12, 17, 17, 17,16,15,7,18,16,20);
        $orientacion = array('C', 'C','','L', 'R', 'R','R', 'C', 'C', 'R', 'R','R','R','','R','R');
//        $pdf->AddPage();

        $pdf->SetWidths($ancho);
        $pdf->_titulos = $titulos;
        $pdf->_titulo = "REPORTE DE RANKING DE CLIENTES DE ACUERDO A CONTABILIDAD DEL ".$url_fechaini." AL ".$url_fechafin." ".$filtro;

        $pdf->_fecha = $data[0]['codigopa'];
        $pdf->_datoPie = 'Fecha Impresion '.date('Y-m-d H:i:s');
        $pdf->AddPage();

        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $nro_aumentador=0;
        $sum_importeGuia_soles=0.00;
        $sum_biBoleta_soles=0.00;
        $sum_totalcomprobante_soles=0.00;
        $sum_subtotalFactura_soles=0.00;
        $sum_igvFactura_soles=0.00;
        $sum_percepcion_soles=0.00;
        $sum_importeGuia_dolares=0.00;
        $sum_biBoleta_dolares=0.00;
        $sum_totalcomprobante_dolares=0.00;
        $sum_subtotalFactura_dolares=0.00;
        $sum_igvFactura_dolares=0.00;
        $sum_percepcion_dolares=0.00;
        $idordenventaTemp=-1;
        $idclienteTemp=-1;
        $cont0=-1;
        for ($p = 0; $p < count($data); $p++) {
            for ($i = 0; $i <count($data[$p]); $i++) {
                //start DESPLEGADO**********
                $cont=$cont+1;
                if($data[$p][$i]['idcliente']!=$idclienteTemp){
                    $sum_importeGuia_soles_cliente=0;
                    $sum_subtotalFactura_soles_cliente=0;
                    $sum_igvFactura_soles_cliente=0;
                    $sum_percepcion_soles_cliente=0;
                    $sum_biBoleta_soles_cliente=0;
                    $sum_importeGuia_dolares_cliente=0;
                    $sum_subtotalFactura_dolares_cliente=0;
                    $sum_igvFactura_dolares_cliente=0;
                    $sum_percepcion_dolares_cliente=0;
                    $sum_biBoleta_dolares_cliente=0;

                    if($cont>1){
                        $puntero_anterior=count($totalesporcliente)-1;
                        $idcliente_anterior=$totalesporcliente[$puntero_anterior]['idcliente'];
                        foreach ($totalesporcliente as $v) {
                            if($idcliente_anterior==$v['idcliente']){
                                $sum_importeGuia_soles_cliente=$sum_importeGuia_soles_cliente+$v['sum_importeGuia_soles'];
                                $sum_subtotalFactura_soles_cliente=$sum_subtotalFactura_soles_cliente+$v['sum_subtotalFactura_soles'];
                                $sum_igvFactura_soles_cliente=$sum_igvFactura_soles_cliente+$v['sum_igvFactura_soles'];
                                $sum_percepcion_soles_cliente=$sum_percepcion_soles_cliente+$v['sum_percepcion_soles'];
                                $sum_biBoleta_soles_cliente=$sum_biBoleta_soles_cliente+$v['sum_biBoleta_soles'];
                                $sum_importeGuia_dolares_cliente=$sum_importeGuia_dolares_cliente+$v['sum_importeGuia_dolares'];
                                $sum_subtotalFactura_dolares_cliente=$sum_subtotalFactura_dolares_cliente+$v['sum_subtotalFactura_dolares'];
                                $sum_igvFactura_dolares_cliente=$sum_igvFactura_dolares_cliente+$v['sum_igvFactura_dolares'];
                                $sum_percepcion_dolares_cliente=$sum_percepcion_dolares_cliente+$v['sum_percepcion_dolares'];
                                $sum_biBoleta_dolares_cliente=$sum_biBoleta_dolares_cliente+$v['sum_biBoleta_dolares'];
                            }
                        }
                        $pdf->SetFillColor(0, 0, 0);
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Cell(88, 5, 'MONTOS TOTALES S/: ', 1, 0, 'R', true);
                        $pdf->SetFillColor(255, 255, 200);
                        $pdf->SetTextColor(0);
                        $pdf->Cell(25, 5, 'S/ ' .number_format($sum_importeGuia_soles_cliente, 2), 1, 0, 'R', false);
                        $pdf->Cell(12);
                        $pdf->Cell(17);
                        $pdf->Cell(17);
                        $pdf->Cell(17, 5, 'S/ ' .number_format($sum_subtotalFactura_soles_cliente, 2), 1, 0, 'R', false);
                        $pdf->Cell(16, 5, 'S/ ' .number_format($sum_igvFactura_soles_cliente, 2), 1, 0, 'R', false);
                        $pdf->Cell(15, 5, 'S/ ' .number_format($sum_percepcion_soles_cliente, 2), 1, 0, 'R', false);
                        $pdf->Cell(7);
                        $pdf->Cell(18);
                        $pdf->Cell(16, 5, 'S/ ' .number_format($sum_biBoleta_soles_cliente, 2), 1, 0, 'R', false);
                        $pdf->ln();
                        $pdf->SetFillColor(0, 0, 0);
                        $pdf->SetTextColor(255, 255, 255);
                        $pdf->Cell(88, 5, 'MONTOS TOTALES US $: ', 1, 0, 'R', true);
                        $pdf->SetFillColor(255, 255, 255);
                        $pdf->SetTextColor(0);
                        $pdf->Cell(25, 5, 'US $ ' .number_format($sum_importeGuia_dolares_cliente, 2), 1, 0, 'R', false);
                        $pdf->Cell(12);
                        $pdf->Cell(17);
                        $pdf->Cell(17);
                        $pdf->Cell(17, 5, 'US $ ' .number_format($sum_subtotalFactura_dolares_cliente, 2), 1, 0, 'R', false);
                        $pdf->Cell(16, 5, 'US $ ' .number_format($sum_igvFactura_dolares_cliente, 2), 1, 0, 'R', false);
                        $pdf->Cell(15, 5, 'US $ ' .number_format($sum_percepcion_dolares_cliente, 2), 1, 0, 'R', false);
                        $pdf->Cell(7);
                        $pdf->Cell(18);
                        $pdf->Cell(16, 5, 'US $ ' .number_format($sum_biBoleta_dolares_cliente, 2), 1, 0, 'R', false);
                        $pdf->ln();
                        $pdf->ln();
                    }
                }
                //end DESPLEGADO**********
                $entro_a='false';
                $serieGRemision='';
                $numGRemision='';
                $serieFactura="";
                $correlativoFactura="";
                $comprobanteFactura="";

                $importeGuia_soles=0.00;
                $subtotalFactura_soles=0.00;
                $igvFactura_soles=0.00;
                $totalcomprobante_soles=0.00;
                $percepcion_soles=0.00;
                $biBoleta_soles=0.00;
                $porcentaje_percepcion_soles='';

                $importeGuia_dolares=0.00;
                $subtotalFactura_dolares=0.00;
                $igvFactura_dolares=0.00;
                $totalcomprobante_dolares=0.00;
                $percepcion_dolares=0.00;
                $biBoleta_dolares=0.00;
                $porcentaje_percepcion_dolares='';

                $que_comprobante_se_sumara="";
                $serieBoleta="";
                $correlativoBoleta="";
                $comprobanteBoleta='';
                $tipocomprobante='';
                $electronico='';
                $moneda='';
                $estado_ov='';
                $estadoComprobante='';
                $documento = new Documento();
                $listar_comprobantes=$documento->listar_comprobantes($data[$p][$i]['idordenventa'],$esAnulado);


                $tiene_comprobantes=count($listar_comprobantes);
                $listar_guia_remision=$documento->listar_guia_remision($data[$p][$i]['idordenventa']);
                $tiene_guia_remision=count($listar_guia_remision);


                if($data[$p][$i]['estado_ov']=='1'){ $estado_ov='Activo'; }
                if($data[$p][$i]['estado_ov']=='0'){ $estado_ov='Anulado'; }

//*************SE HACE DOS IMPRESIONES porque cuando comprobantes es igual a CERO  solo se imprime una fila
//*************PERO CUANDO TIENE COMPROBANTES NO SABEMOS CUANTOS COMPROBANTES TENDRA UNA MISMA GUIA
//*************POR ENDE UNA GUIA PUEDE TENER VARIAS FACTURAS (VARIAS IMPRESIONES EN UNA MISMA GUIA)
//            ****************** NO TIENE COMPROBANTES*****************
                if($tiene_comprobantes==0){
                    $cont0=$cont0+1;
                    $totalesporcliente[$cont0]=array(
                            "idcliente"=>$data[$p][$i]['idcliente'],
                            "sum_importeGuia_soles"=>0.00,
                            "sum_subtotalFactura_soles"=>0.00,
                            "sum_igvFactura_soles"=>0.00,
                            "sum_percepcion_soles"=>0.00,
                            "sum_biBoleta_soles"=>0.00,
                            "sum_importeGuia_dolares"=>0.00,
                            "sum_subtotalFactura_dolares"=>0.00,
                            "sum_igvFactura_dolares"=>0.00,
                            "sum_percepcion_dolares"=>0.00,
                            "sum_biBoleta_dolares"=>0.00
                    );
                    if($tiene_comprobantes==0 and $tiene_guia_remision>=1){
                        $serieGRemision=$listar_guia_remision[0]['serie'];
                        $numGRemision=$listar_guia_remision[0]['numdoc'];
                        //start solo suma guia que esta activa y lo hace una sola vez por orden
                        if($idordenventaTemp!=$data[$p][$i]['idordenventa'] and $data[$p][$i]['estado_ov']=='1'){
                            if($data[$p][$i]['idmoneda']=='1'){
                                $importeGuia_soles=$data[$p][$i]['importeov'];
                                $sum_importeGuia_soles=$sum_importeGuia_soles+$importeGuia_soles;
                            }
                            if($data[$p][$i]['idmoneda']=='2'){
                                $importeGuia_dolares=$data[$p][$i]['importeov'];
                                $sum_importeGuia_dolares=$sum_importeGuia_dolares+$importeGuia_dolares;
                            }
                            foreach ($totalesporcliente as $v) {
                                if($data[$p][$i]['idcliente']==$v['idcliente']){
                                    $totalesporcliente[$cont0]=array(
                                        "idcliente"=>$v['idcliente'],
                                        "sum_importeGuia_soles"=>$importeGuia_soles,
                                        "sum_subtotalFactura_soles"=>$v['sum_subtotalFactura_soles'],
                                        "sum_igvFactura_soles"=>$v['sum_igvFactura_soles'],
                                        "sum_percepcion_soles"=>$v['sum_percepcion_soles'],
                                        "sum_biBoleta_soles"=>$v['sum_biBoleta_soles'],
                                        "sum_importeGuia_dolares"=>$importeGuia_dolares,
                                        "sum_subtotalFactura_dolares"=>$v['sum_subtotalFactura_dolares'],
                                        "sum_igvFactura_dolares"=>$v['sum_igvFactura_dolares'],
                                        "sum_percepcion_dolares"=>$v['sum_percepcion_dolares'],
                                        "sum_biBoleta_dolares"=>$v['sum_biBoleta_dolares']
                                    );
                                }
                            }
                        }
                        //end solo suma guia que esta activa y lo hace una sola vez por orden
                    }

                    //START  CUANDO SOLO TIENE ORDEN DE VENTA Y NO TIENE GUIA DE REMISION, NI FACTURA, NI BOLETA
                    if($tiene_comprobantes==0 and $tiene_guia_remision==0 and $data[$p][$i]['estado_ov']=='1'){
                        //start suma el vaor de la ov a la constantes del total de guias de remision y lo hace una sola vez por orden
                        if($idordenventaTemp!=$data[$p][$i]['idordenventa'] and  $data[$p][$i]['estado_ov']=='1'){
                            if($data[$p][$i]['idmoneda']=='1'){
                                $importeGuia_soles=$data[$p][$i]['importeov'];
                                $sum_importeGuia_soles=$sum_importeGuia_soles+$importeGuia_soles;
                            }
                            if($data[$p][$i]['idmoneda']=='2'){
                                $importeGuia_dolares=$data[$p][$i]['importeov'];
                                $sum_importeGuia_dolares=$sum_importeGuia_dolares+$importeGuia_dolares;
                            }
                            foreach ($totalesporcliente as $v) {
                                if($data[$p][$i]['idcliente']==$v['idcliente']){
                                    $totalesporcliente[$cont0]=array(
                                        "idcliente"=>$v['idcliente'],
                                        "sum_importeGuia_soles"=>$importeGuia_soles,
                                        "sum_subtotalFactura_soles"=>$v['sum_subtotalFactura_soles'],
                                        "sum_igvFactura_soles"=>$v['sum_igvFactura_soles'],
                                        "sum_percepcion_soles"=>$v['sum_percepcion_soles'],
                                        "sum_biBoleta_soles"=>$v['sum_biBoleta_soles'],
                                        "sum_importeGuia_dolares"=>$importeGuia_dolares,
                                        "sum_subtotalFactura_dolares"=>$v['sum_subtotalFactura_dolares'],
                                        "sum_igvFactura_dolares"=>$v['sum_igvFactura_dolares'],
                                        "sum_percepcion_dolares"=>$v['sum_percepcion_dolares'],
                                        "sum_biBoleta_dolares"=>$v['sum_biBoleta_dolares']
                                    );
                                }
                            }
                        }
                        //end suma el vaor de la ov a la constantes del total de guias de remision y lo hace una sola vez por orden
                    }
                    //END  CUANDO SOLO TIENE ORDEN DE VENTA Y NO TIENE GUIA DE REMISION, NI FACTURA, NI BOLETA

        //          ***************************************************************************************************

                    // START  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
                    if($data[$p][$i]['ruc']==''){
                        $ruc_dni=$data[$p][$i]['dni'];
                    }else{
                        $ruc_dni=$data[$p][$i]['ruc'];
                    }

                    if($data[$p][$i]['idmoneda']=='1'){
                      $moneda='S/';
                    }
                    if($data[$p][$i]['idmoneda']=='2'){
                      $moneda='US $';
                    }

                    if($subtotalFactura_soles>0){
                     $percepcion_soles=$moneda.' '.number_format($percepcion_soles, 2);
                     $porcentaje_percepcion_soles=$data[$p][$i]['percepcion']*100;
                     $porcentaje_percepcion_soles=$porcentaje_percepcion_soles.'%';
                    }else{
                      $percepcion_soles=$moneda.' '.'0.00';
                      $porcentaje_percepcion_soles='';
                    }

                    if($subtotalFactura_dolares>0){
                        $percepcion_dolares=$moneda.' '.number_format($percepcion_dolares, 2);
                        $porcentaje_percepcion_dolares=$data[$p][$i]['percepcion']*100;
                        $porcentaje_percepcion_dolares=$porcentaje_percepcion_dolares.'%';
                    }else{
                        $percepcion_dolares=$moneda.' '.'0.00';
                        $porcentaje_percepcion_dolares='';
                    }

                    $nro_aumentador=$nro_aumentador+1;

                    if($data[$p][$i]['idmoneda']=='1'){
                        $fila = array($nro_aumentador,$data[$p][$i]['fordenventa'],$data[$p][$i]['codigov'],
                        html_entity_decode(substr($data[$p][$i]['razonsocial'], 0, 27), ENT_QUOTES),$ruc_dni,
                        $moneda.' '.number_format($importeGuia_soles, 2),
                        $estado_ov,
                        $serieGRemision.'-'.$numGRemision,$listar_comprobantes[$j]['fechadoc'],$comprobanteFactura,
                        $moneda.' '.number_format($subtotalFactura_soles, 2),
                        $moneda.' '.number_format($igvFactura_soles, 2),
                        $percepcion_soles,
                        $porcentaje_percepcion_soles,
                        $comprobanteBoleta,
                        $moneda.' '.number_format($biBoleta_soles, 2),
                        $estadoComprobante);
                    }
                    if($data[$p][$i]['idmoneda']=='2'){
                        $fila = array($nro_aumentador,$data[$p][$i]['fordenventa'],$data[$p][$i]['codigov'],
                        html_entity_decode(substr($data[$p][$i]['razonsocial'], 0, 27), ENT_QUOTES),$ruc_dni,
                        $moneda.' '.number_format($importeGuia_dolares, 2),
                        $estado_ov,
                        $serieGRemision.'-'.$numGRemision,$comprobanteFactura,
                        $moneda.' '.number_format($subtotalFactura_dolares, 2),
                        $moneda.' '.number_format($igvFactura_dolares, 2),
                        $percepcion_dolares,
                        $porcentaje_percepcion_dolares,
                        $comprobanteBoleta,
                        $moneda.' '.number_format($biBoleta_dolares, 2),
                        $estadoComprobante);
                    }
                    $pdf->Row($fila);
                     // END  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
        //            ****************************************************************************************************
                }

                if($tiene_comprobantes>=1){ // recrrera comprobantes asi esten anulados o activos pero solo sumara los activos
                        $entro_a='true';
                        $cont0=$cont0+1;
                        $totalesporcliente[$cont0]=array(
                            "idcliente"=>$data[$p][$i]['idcliente'],
                            "sum_importeGuia_soles"=>0.00,
                            "sum_subtotalFactura_soles"=>0.00,
                            "sum_igvFactura_soles"=>0.00,
                            "sum_percepcion_soles"=>0.00,
                            "sum_biBoleta_soles"=>0.00,
                            "sum_importeGuia_dolares"=>0.00,
                            "sum_subtotalFactura_dolares"=>0.00,
                            "sum_igvFactura_dolares"=>0.00,
                            "sum_percepcion_dolares"=>0.00,
                            "sum_biBoleta_dolares"=>0.00
                        );
                        //start limpia variables por vuelta
                        $serieGRemision='';
                        $numGRemision='';
                        $importeGuia_soles=0.00;
                        $importeGuia_dolares=0.00;
                         //end limpia variables por vuelta

                        $serieGRemision=$listar_guia_remision[0]['serie'];
                        $numGRemision=$listar_guia_remision[0]['numdoc'];
                        //start solo suma guia que esta activa y lo hace una sola vez por orden
                        if($idordenventaTemp!=$data[$p][$i]['idordenventa'] and $data[$p][$i]['estado_ov']=='1'){
                            if($data[$p][$i]['idmoneda']=='1'){
                                $importeGuia_soles=$data[$p][$i]['importeov'];
                                $sum_importeGuia_soles=$sum_importeGuia_soles+$importeGuia_soles;
                            }
                            if($data[$p][$i]['idmoneda']=='2'){
                                $importeGuia_dolares=$data[$p][$i]['importeov'];
                                $sum_importeGuia_dolares=$sum_importeGuia_dolares+$importeGuia_dolares;
                            }
                            foreach ($totalesporcliente as $v) {
                                if($data[$p][$i]['idcliente']==$v['idcliente']){
                                    $totalesporcliente[$cont0]=array(
                                        "idcliente"=>$v['idcliente'],
                                        "sum_importeGuia_soles"=>$importeGuia_soles,
                                        "sum_subtotalFactura_soles"=>$v['sum_subtotalFactura_soles'],
                                        "sum_igvFactura_soles"=>$v['sum_igvFactura_soles'],
                                        "sum_percepcion_soles"=>$v['sum_percepcion_soles'],
                                        "sum_biBoleta_soles"=>$v['sum_biBoleta_soles'],
                                        "sum_importeGuia_dolares"=>$importeGuia_dolares,
                                        "sum_subtotalFactura_dolares"=>$v['sum_subtotalFactura_dolares'],
                                        "sum_igvFactura_dolares"=>$v['sum_igvFactura_dolares'],
                                        "sum_percepcion_dolares"=>$v['sum_percepcion_dolares'],
                                        "sum_biBoleta_dolares"=>$v['sum_biBoleta_dolares']
                                    );
                                }
                            }
                        }
                        //end solo suma guia que esta activa y lo hace una sola vez por orden

                        for($j=0;$j<count($listar_comprobantes);$j++){
                            if($entro_a!='true'){
                            $cont0=$cont0+1;
                                foreach ($totalesporcliente as $v) {
                                        if($data[$p][$i]['idcliente']==$v['idcliente']){
                                            $totalesporcliente[$cont0]=array(
                                                "idcliente"=>$v['idcliente'],
                                                "sum_importeGuia_soles"=>0.00,
                                                "sum_subtotalFactura_soles"=>0.00,
                                                "sum_igvFactura_soles"=>0.00,
                                                "sum_percepcion_soles"=>0.00,
                                                "sum_biBoleta_soles"=>0.00,
                                                "sum_importeGuia_dolares"=>0.00,
                                                "sum_subtotalFactura_dolares"=>0.00,
                                                "sum_igvFactura_dolares"=>0.00,
                                                "sum_percepcion_dolares"=>0.00,
                                                "sum_biBoleta_dolares"=>0.00,
                                            );
                                        }
                                }
                            }
                            //start limpia variables por vuelta
                            $serieFactura="";
                            $correlativoFactura="";
                            $comprobanteFactura="";

                            $subtotalFactura_soles=0.00;
                            $igvFactura_soles=0.00;
                            $totalcomprobante_soles=0.00;
                            $percepcion_soles=0.00;
                            $biBoleta_soles=0.00;
                            $porcentaje_percepcion_soles='';

                            $subtotalFactura_dolares=0.00;
                            $igvFactura_dolares=0.00;
                            $totalcomprobante_dolares=0.00;
                            $percepcion_dolares=0.00;
                            $biBoleta_dolares=0.00;
                            $porcentaje_percepcion_dolares='';

                            $que_comprobante_se_sumara="";
                            $serieBoleta="";
                            $correlativoBoleta="";
                            $comprobanteBoleta='';
                            $tipocomprobante='';
                            $electronico='';
                            $moneda='';
                            $estado_ov='';
                            $estadoComprobante='';
                            //end limpia variables por vuelta
                            if($data[$p][$i]['estado_ov']=='1'){ $estado_ov='Activo'; }
                            if($data[$p][$i]['estado_ov']=='0'){ $estado_ov='Anulado'; }

                            if($listar_comprobantes[$j]['nombredoc']==1){ // si tiene facturas
                                    // START OBTENIENDO CORRELATIVO FACTURA
                                        if($listar_comprobantes[$j]["electronico"]==1){
                                           $electronico='';
                                           $serieFactura=$documento->add_ceros($listar_comprobantes[$j]['serie'], 3);
                                           $serieFactura="F".$serieFactura;
                                           $correlativoFactura=$documento->add_ceros($listar_comprobantes[$j]['numdoc'], 8);
                                        }

                                       if($listar_comprobantes[$j]["electronico"]==0){
                                           $electronico='FISICA';
                                           $serieFactura=$listar_comprobantes[$j]['serie'];
                                           $correlativoFactura=$listar_comprobantes[$j]['numdoc'];
                                       }
                                       $comprobanteFactura=$serieFactura.' - '.$correlativoFactura;
                                       $tipocomprobante="FACTURA ".$electronico;
                                    // END OBTENIENDO CORRELATIVO FACTURA

                            if($data[$p][$i]['idmoneda']=='1'){
                                $subtotalFactura_soles=$listar_comprobantes[$j]['montofacturado']-$listar_comprobantes[$j]['montoigv'];
                                $igvFactura_soles=$listar_comprobantes[$j]['montoigv'];
                                $totalcomprobante_soles=$listar_comprobantes[$j]['montofacturado'];
                                $percepcion_soles=$listar_comprobantes[$j]['montofacturado']*$data[$p][$i]['percepcion'];

                            }
                            if($data[$p][$i]['idmoneda']=='2'){
                                $subtotalFactura_dolares=$listar_comprobantes[$j]['montofacturado']-$listar_comprobantes[$j]['montoigv'];
                                $igvFactura_dolares=$listar_comprobantes[$j]['montoigv'];
                                $totalcomprobante_dolares=$listar_comprobantes[$j]['montofacturado'];
                                $percepcion_dolares=$listar_comprobantes[$j]['montofacturado']*$data[$p][$i]['percepcion'];
                            }
                                if($listar_comprobantes[$j]['esAnulado']==0){ $estadoComprobante='Activo'; }
                                if($listar_comprobantes[$j]['esAnulado']==1){ $estadoComprobante='Anulado'; }
                                $que_comprobante_se_sumara='FACTURA';
                            }

                            if($listar_comprobantes[$j]['nombredoc']==2){ // si tiene boleta
                                    // START OBTENIENDO CORRELATIVO BOLETA
                                        if($listar_comprobantes[$j]["electronico"]==1){
                                            $electronico='';
                                            $serieBoleta=$documento->add_ceros($listar_comprobantes[$j]['serie'], 3);
                                            $serieBoleta='B'.$serieBoleta;
                                            $correlativoBoleta=$documento->add_ceros($listar_comprobantes[$j]['numdoc'], 8);
                                        }
                                        if($listar_comprobantes[$j]["electronico"]==0){
                                            $electronico='FISICA';
                                            $serieBoleta=$listar_comprobantes[$j]['serie'];
                                            $correlativoBoleta=$listar_comprobantes[$j]['numdoc'];
                                        }
                                        $comprobanteBoleta=$serieBoleta.' - '.$correlativoBoleta;
                                        $tipocomprobante="BOLETA ".$electronico;
                                    // END OBTENIENDO CORRELATIVO BOLETA

                                if($data[$p][$i]['idmoneda']=='1'){
                                    $biBoleta_soles=$listar_comprobantes[$j]['montofacturado'];
                                    $totalcomprobante_soles=$listar_comprobantes[$j]['montofacturado'];
                                }
                                if($data[$p][$i]['idmoneda']=='2'){
                                    $biBoleta_dolares=$listar_comprobantes[$j]['montofacturado'];
                                    $totalcomprobante_dolares=$listar_comprobantes[$j]['montofacturado'];
                                }

                                if($listar_comprobantes[$j]['esAnulado']==0){ $estadoComprobante='Activo'; }
                                if($listar_comprobantes[$j]['esAnulado']==1){ $estadoComprobante='Anulado'; }
                                $que_comprobante_se_sumara='BOLETA';
                            }
                            //start solo sumando el comprobante que esta activo
                            if($que_comprobante_se_sumara=='BOLETA' and $listar_comprobantes[$j]['esAnulado']==0){
                                if($data[$p][$i]['idmoneda']=='1'){
                                    $sum_biBoleta_soles=$sum_biBoleta_soles+$biBoleta_soles;
                                    $sum_totalcomprobante_soles=$sum_totalcomprobante_soles+$totalcomprobante_soles;
                                }
                                if($data[$p][$i]['idmoneda']=='2'){
                                    $sum_biBoleta_dolares=$sum_biBoleta_dolares+$biBoleta_dolares;
                                    $sum_totalcomprobante_dolares=$sum_totalcomprobante_dolares+$totalcomprobante_dolares;
                                }
                                foreach ($totalesporcliente as $v) {
                                    if($data[$p][$i]['idcliente']==$v['idcliente']){
                                        $totalesporcliente[$cont0]=array(
                                            "idcliente"=>$v['idcliente'],
                                            "sum_importeGuia_soles"=>$v['sum_importeGuia_soles'],
                                            "sum_subtotalFactura_soles"=>$v['sum_subtotalFactura_soles'],
                                            "sum_igvFactura_soles"=>$v['sum_igvFactura_soles'],
                                            "sum_percepcion_soles"=>$v['sum_percepcion_soles'],
                                            "sum_biBoleta_soles"=>$biBoleta_soles,
                                            "sum_importeGuia_dolares"=>$v['sum_importeGuia_dolares'],
                                            "sum_subtotalFactura_dolares"=>$v['sum_subtotalFactura_dolares'],
                                            "sum_igvFactura_dolares"=>$v['sum_igvFactura_dolares'],
                                            "sum_percepcion_dolares"=>$v['sum_percepcion_dolares'],
                                            "sum_biBoleta_dolares"=>$biBoleta_dolares
                                        );
                                    }
                                }
                            }
                            if($que_comprobante_se_sumara=='FACTURA' and $listar_comprobantes[$j]['esAnulado']==0){
                                if($data[$p][$i]['idmoneda']=='1'){
                                    $sum_subtotalFactura_soles=$sum_subtotalFactura_soles+$subtotalFactura_soles;
                                    $sum_igvFactura_soles=$sum_igvFactura_soles+$igvFactura_soles;
                                    $sum_totalcomprobante_soles=$sum_totalcomprobante_soles+$totalcomprobante_soles;
                                    $sum_percepcion_soles=$sum_percepcion_soles+$percepcion_soles;
                                    foreach ($totalesporcliente as $v) {
                                        if($data[$p][$i]['idcliente']==$v['idcliente']){
                                            $totalesporcliente[$cont0]=array(
                                                "idcliente"=>$v['idcliente'],
                                                "sum_importeGuia_soles"=>$v['sum_importeGuia_soles'],
                                                "sum_subtotalFactura_soles"=>$subtotalFactura_soles,
                                                "sum_igvFactura_soles"=>$igvFactura_soles,
                                                "sum_percepcion_soles"=>$percepcion_soles,
                                                "sum_biBoleta_soles"=>$v['sum_biBoleta_soles'],
                                                "sum_importeGuia_dolares"=>$v['sum_importeGuia_dolares'],
                                                "sum_subtotalFactura_dolares"=>$v['sum_subtotalFactura_dolares'],
                                                "sum_igvFactura_dolares"=>$v['sum_igvFactura_dolares'],
                                                "sum_percepcion_dolares"=>$v['sum_percepcion_dolares'],
                                                "sum_biBoleta_dolares"=>$v['sum_biBoleta_dolares'],
                                            );
                                        }
                                    }
                                }
                                if($data[$p][$i]['idmoneda']=='2'){
                                    $sum_subtotalFactura_dolares=$sum_subtotalFactura_dolares+$subtotalFactura_dolares;
                                    $sum_igvFactura_dolares=$sum_igvFactura_dolares+$igvFactura_dolares;
                                    $sum_totalcomprobante_dolares=$sum_totalcomprobante_dolares+$totalcomprobante_dolares;
                                    $sum_percepcion_dolares=$sum_percepcion_dolares+$percepcion_dolares;
                                    foreach ($totalesporcliente as $v) {
                                        if($data[$p][$i]['idcliente']==$v['idcliente']){
                                            $totalesporcliente[$cont0]=array(
                                                "idcliente"=>$v['idcliente'],
                                                "sum_importeGuia_soles"=>$v['sum_importeGuia_soles'],
                                                "sum_subtotalFactura_soles"=>$v['sum_subtotalFactura_soles'],
                                                "sum_igvFactura_soles"=>$v['sum_igvFactura_soles'],
                                                "sum_percepcion_soles"=>$v['sum_percepcion_soles'],
                                                "sum_biBoleta_soles"=>$v['sum_biBoleta_soles'],
                                                "sum_importeGuia_dolares"=>$v['sum_importeGuia_dolares'],
                                                "sum_subtotalFactura_dolares"=>$subtotalFactura_dolares,
                                                "sum_igvFactura_dolares"=>$igvFactura_dolares,
                                                "sum_percepcion_dolares"=>$percepcion_dolares,
                                                "sum_biBoleta_dolares"=>$v['sum_biBoleta_dolares'],
                                            );
                                        }
                                    }
                                }
                            }
                            //
                            $idordenventaTemp=$data[$p][$i]['idordenventa'];

        //            ***************************************************************************************************

                    // START  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
                            if($data[$p][$i]['ruc']==''){
                                $ruc_dni=$data[$p][$i]['dni'];
                            }else{
                                $ruc_dni=$data[$p][$i]['ruc'];
                            }

                            if($data[$p][$i]['idmoneda']=='1'){
                              $moneda='S/';
                            }
                            if($data[$p][$i]['idmoneda']=='2'){
                              $moneda='US $';
                            }

                            if($subtotalFactura_soles>0){
                             $percepcion_soles=$percepcion_soles;
                             $porcentaje_percepcion_soles=$data[$p][$i]['percepcion']*100;
                             $porcentaje_percepcion_soles=$porcentaje_percepcion_soles.'%';
                            }else{
                              $percepcion_soles=0.00;
                              $porcentaje_percepcion_soles='';
                            }

                            if($subtotalFactura_dolares>0){
                                $percepcion_dolares=$percepcion_dolares;
                                $porcentaje_percepcion_dolares=$data[$p][$i]['percepcion']*100;
                                $porcentaje_percepcion_dolares=$porcentaje_percepcion_dolares.'%';
                            }else{
                                $percepcion_dolares=0.00;
                                $porcentaje_percepcion_dolares='';
                            }

                            $nro_aumentador=$nro_aumentador+1;

                            if($data[$p][$i]['idmoneda']=='1'){
                                $fila = array($nro_aumentador,$data[$p][$i]['fordenventa'],$data[$p][$i]['codigov'],
                                html_entity_decode(substr($data[$p][$i]['razonsocial'], 0, 27), ENT_QUOTES),$ruc_dni,
                                $moneda.' '.number_format($importeGuia_soles, 2),
                                $estado_ov,
                                $serieGRemision.'-'.$numGRemision,$comprobanteFactura,
                                $moneda.' '.number_format($subtotalFactura_soles, 2),
                                $moneda.' '.number_format($igvFactura_soles, 2),
                                $moneda.' '.number_format($percepcion_soles,2),
                                $porcentaje_percepcion_soles,
                                $comprobanteBoleta,
                                $moneda.' '.number_format($biBoleta_soles, 2),
                                $estadoComprobante);
                            }
                            if($data[$p][$i]['idmoneda']=='2'){
                                $fila = array($nro_aumentador,$data[$p][$i]['fordenventa'],$data[$p][$i]['codigov'],
                                html_entity_decode(substr($data[$p][$i]['razonsocial'], 0, 27), ENT_QUOTES),$ruc_dni,
                                $moneda.' '.number_format($importeGuia_dolares, 2),
                                $estado_ov,
                                $serieGRemision.'-'.$numGRemision,$comprobanteFactura,
                                $moneda.' '.number_format($subtotalFactura_dolares, 2),
                                $moneda.' '.number_format($igvFactura_dolares, 2),
                                $moneda.' '.number_format($percepcion_dolares,2),
                                $porcentaje_percepcion_dolares,
                                $comprobanteBoleta,
                                $moneda.' '.number_format($biBoleta_dolares, 2),
                                $estadoComprobante);
                            }
                            $pdf->Row($fila);
                     // END  ORDENADO DE VARIABLES PARA IMPRIMIR FILA
        //            ****************************************************************************************************
                        $entro_a='false';
                    }
                }
                $idclienteTemp=$data[$p][$i]['idcliente'];
            }
        }



        $sum_importeGuia_soles_cliente=0;
        $sum_subtotalFactura_soles_cliente=0;
        $sum_igvFactura_soles_cliente=0;
        $sum_percepcion_soles_cliente=0;
        $sum_biBoleta_soles_cliente=0;
        $sum_importeGuia_dolares_cliente=0;
        $sum_subtotalFactura_dolares_cliente=0;
        $sum_igvFactura_dolares_cliente=0;
        $sum_percepcion_dolares_cliente=0;
        $sum_biBoleta_dolares_cliente=0;


        $puntero_anterior=count($totalesporcliente)-1;
        $idcliente_anterior=$totalesporcliente[$puntero_anterior]['idcliente'];
        foreach ($totalesporcliente as $v) {
            if($idcliente_anterior==$v['idcliente']){
                $sum_importeGuia_soles_cliente=$sum_importeGuia_soles_cliente+$v['sum_importeGuia_soles'];
                $sum_subtotalFactura_soles_cliente=$sum_subtotalFactura_soles_cliente+$v['sum_subtotalFactura_soles'];
                $sum_igvFactura_soles_cliente=$sum_igvFactura_soles_cliente+$v['sum_igvFactura_soles'];
                $sum_percepcion_soles_cliente=$sum_percepcion_soles_cliente+$v['sum_percepcion_soles'];
                $sum_biBoleta_soles_cliente=$sum_biBoleta_soles_cliente+$v['sum_biBoleta_soles'];
                $sum_importeGuia_dolares_cliente=$sum_importeGuia_dolares_cliente+$v['sum_importeGuia_dolares'];
                $sum_subtotalFactura_dolares_cliente=$sum_subtotalFactura_dolares_cliente+$v['sum_subtotalFactura_dolares'];
                $sum_igvFactura_dolares_cliente=$sum_igvFactura_dolares_cliente+$v['sum_igvFactura_dolares'];
                $sum_percepcion_dolares_cliente=$sum_percepcion_dolares_cliente+$v['sum_percepcion_dolares'];
                $sum_biBoleta_dolares_cliente=$sum_biBoleta_dolares_cliente+$v['sum_biBoleta_dolares'];
            }
        }
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(88, 5, 'MONTOS TOTALES S/: ', 1, 0, 'R', true);
        $pdf->SetFillColor(255, 255, 200);
        $pdf->SetTextColor(0);
        $pdf->Cell(25, 5, 'S/ ' .number_format($sum_importeGuia_soles_cliente, 2), 1, 0, 'R', false);
        $pdf->Cell(12);
        $pdf->Cell(17);
        $pdf->Cell(17);
        $pdf->Cell(17, 5, 'S/ ' .number_format($sum_subtotalFactura_soles_cliente, 2), 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'S/ ' .number_format($sum_igvFactura_soles_cliente, 2), 1, 0, 'R', false);
        $pdf->Cell(15, 5, 'S/ ' .number_format($sum_percepcion_soles_cliente, 2), 1, 0, 'R', false);
        $pdf->Cell(7);
        $pdf->Cell(18);
        $pdf->Cell(16, 5, 'S/ ' .number_format($sum_biBoleta_soles_cliente, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(88, 5, 'MONTOS TOTALES US $: ', 1, 0, 'R', true);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->Cell(25, 5, 'US $ ' .number_format($sum_importeGuia_dolares_cliente, 2), 1, 0, 'R', false);
        $pdf->Cell(12);
        $pdf->Cell(17);
        $pdf->Cell(17);
        $pdf->Cell(17, 5, 'US $ ' .number_format($sum_subtotalFactura_dolares_cliente, 2), 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'US $ ' .number_format($sum_igvFactura_dolares_cliente, 2), 1, 0, 'R', false);
        $pdf->Cell(15, 5, 'US $ ' .number_format($sum_percepcion_dolares_cliente, 2), 1, 0, 'R', false);
        $pdf->Cell(7);
        $pdf->Cell(18);
        $pdf->Cell(16, 5, 'US $ ' .number_format($sum_biBoleta_dolares_cliente, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(268, 5, 'TOTALES', 1, 0, 'C', true);
        $pdf->SetFillColor(255, 255, 200);
        $pdf->SetTextColor(0);




        $pdf->ln();
        $pdf->Cell(88);
        $pdf->Cell(25, 5, 'S/ ' .number_format($sum_importeGuia_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(12);
        $pdf->Cell(17);
        $pdf->Cell(17);
        $pdf->Cell(17, 5, 'S/ ' .number_format($sum_subtotalFactura_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'S/ ' .number_format($sum_igvFactura_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(15, 5, 'S/ ' .number_format($sum_percepcion_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(7);
        $pdf->Cell(18);
        $pdf->Cell(15, 5, 'S/ ' .number_format($sum_biBoleta_soles, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->Cell(88);
        $pdf->Cell(25, 5, 'US $ ' .number_format($sum_importeGuia_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(12);
        $pdf->Cell(17);
        $pdf->Cell(17);
        $pdf->Cell(17, 5, 'US $ ' .number_format($sum_subtotalFactura_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(16, 5, 'US $ ' .number_format($sum_igvFactura_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(15, 5, 'US $ ' .number_format($sum_percepcion_dolares, 2), 1, 0, 'R', false);
        $pdf->Cell(7);
        $pdf->Cell(18);
        $pdf->Cell(15, 5, 'US $ ' .number_format($sum_biBoleta_dolares, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->ln();
        $pdf->ln();

        $totalNoFacturadoSoles=$sum_importeGuia_soles-$sum_totalcomprobante_soles;
//        $totalNoFacturadoDolares=$sum_importeGuia_dolares-$sum_totalcomprobante_dolares;
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(46, 5, "", 0, 0, 'R', false);
        $pdf->Cell(28, 5, 'SOLES', 1, 0, 'C', true);
        $pdf->Cell(28, 5,  'DOLARES', 1, 0, 'C', true);
        $pdf->ln();
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(1, 5, "", 0, 0, 'R', false);
        $pdf->Cell(45, 5, "TOTAL EN GUIAS MADRES", 1, 0, 'R', false);
        $pdf->Cell(28, 5, 'S/ ' .number_format($sum_importeGuia_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(28, 5, 'US $ ' .number_format($sum_importeGuia_dolares, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->Cell(1, 5, "", 0, 0, 'R', false);
        $pdf->Cell(45, 5, "TOTAL FACTURADO DE LAS GUIAS MADRES", 1, 0, 'R', false);
        $pdf->Cell(28, 5, 'S/ ' .number_format($sum_subtotalFactura_soles+$sum_igvFactura_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(28, 5, 'US $ ' .number_format($sum_subtotalFactura_dolares+$sum_igvFactura_dolares, 2), 1, 0, 'R', false);
        $pdf->ln();
        $pdf->Cell(1, 5, "", 0, 0, 'R', false);
        $pdf->Cell(45, 5, "TOTAL BOLETEADO DE LAS GUIAS MADRES", 1, 0, 'R', false);
        $pdf->Cell(28, 5, 'S/ ' .number_format($sum_biBoleta_soles, 2), 1, 0, 'R', false);
        $pdf->Cell(28, 5, 'US $ ' .number_format($sum_biBoleta_dolares, 2), 1, 0, 'R', false);
        $pdf->ln();
        //***********
        $pdf->AliasNbPages();

        $pdf->Output();
    }

     function reporteProductosBloque() {
        $reporte = $this->AutoLoadModel('reporte');
        $bloques = $this->AutoLoadModel('bloques');
        $idInventario = $_REQUEST['lstInventario'];
        $idBloque = $_REQUEST['lstBloques'];
        $idProducto = $_REQUEST['idProducto'];
        $cmbCondicionStock = $_REQUEST['cmbCondicionStock'];
        IF($cmbCondicionStock==1){
        $msmConteoX="CON STOCK";
        }
        IF($cmbCondicionStock==2){
        $msmConteoX="SIN STOCK";
        }
        IF($cmbCondicionStock==3){
        $msmConteoX="CON Y SIN STOCK";
        }

        $contador=0;
        $data = $reporte->reporteInventario($idInventario, $idBloque,'');
        $cantidadData = count($data);
        $pdf = new PDF_Mc_Table("L", "mm", "A4");
        $titulos = array(utf8_decode('N°'), 'CODIGO', 'DESCRIPCION', 'U.M.', 'STOCK ACTUAL','OBSERVACIONES');

        $pdf->SetFont('Helvetica', 'B', 14);
        $ancho = array(7, 30, 145, 24, 30,38);
        $orientacion = array('L', 'L', 'L', 'C', 'C','C');

        $pdf->SetWidths($ancho);
        $listadoBloque= $bloques->listarBloqueFormato($idBloque);

        foreach($listadoBloque as $v){
            $nombreBloque=$v['codigo'];
        }

        $pdf->_titulo = "REPORTE DE PRODUCTOS POR BLOQUE O ANAQUEL ".'"'.$nombreBloque.'"      '.$msmConteoX.'   ';

        $pdf->AddPage();
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $relleno = true;
        $pdf->SetFillColor(202, 232, 234);
        $pdf->SetTextColor(12, 78, 139);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Helvetica', 'B', 8);
        $pdf->fill($relleno);
        $pdf->Cell(30, 7, 'FECHA IMPRESION', 1, 0, 'C', true);
        $pdf->Cell(32, 7,date("Y-m-d"), 1, 0, 'C', true);
        $pdf->Cell(30, 7, 'HORA IMPRESION', 1, 0, 'C', true);
        $pdf->Cell( 030, 7,date("H:i:s"), 1, 0, 'C', true);
        $pdf->Cell(158, 7, 'EL STOCK REFLEJADO PERTENECE AL MOMENTO EN QUE SE EXPORTO ESTE REPORTE DEL SISTEMA', 1, 0, 'C', FALSE);
        $pdf->ln();
        $pdf->ln();
        $fila = $titulos;
        $pdf->Row($fila);

        if($cantidadData>=1){
            for ($i = 0; $i < $cantidadData; $i++) {
                if($cmbCondicionStock==1){
                    if($data[$i]['stockactual_wc_producto']>=1){
                        $contador=$contador+1;
                        $pdf->_titulos = $titulos;
                        //obtenemos el valor de cada producto segun condicion :sea merma, reparacion, venta
                        $pdf->SetFillColor(224, 235, 255);
                        $pdf->SetTextColor(0);
                        $pdf->SetFont('Helvetica','',8.5);
                        $fila = array($contador, html_entity_decode($data[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode(utf8_decode($data[$i]['nompro']), ENT_QUOTES, 'UTF-8'),$data[$i]['codigoum'],$data[$i]['stockactual_wc_producto'],'');
                        $pdf->Row($fila);
                        $relleno = !$relleno;
                        $pdf->fill($relleno);
                    }
                }
                if($cmbCondicionStock==2){
                    if($data[$i]['stockactual_wc_producto']<=0){
                        $contador=$contador+1;
                        $pdf->_titulos = $titulos;
                        //obtenemos el valor de cada producto segun condicion :sea merma, reparacion, venta
                        $pdf->SetFillColor(224, 235, 255);
                        $pdf->SetTextColor(0);
                        $pdf->SetFont('Helvetica','',8.5);
                        $fila = array($contador, html_entity_decode($data[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode(utf8_decode($data[$i]['nompro']), ENT_QUOTES, 'UTF-8'),$data[$i]['codigoum'],$data[$i]['stockactual_wc_producto'],'');
                        $pdf->Row($fila);
                        $relleno = !$relleno;
                        $pdf->fill($relleno);
                    }
                }
                if($cmbCondicionStock==3){
                        $contador=$contador+1;
                        $pdf->_titulos = $titulos;
                        //obtenemos el valor de cada producto segun condicion :sea merma, reparacion, venta
                        $pdf->SetFillColor(224, 235, 255);
                        $pdf->SetTextColor(0);
                        $pdf->SetFont('Helvetica','',8.5);
                        $fila = array($contador, html_entity_decode($data[$i]['codigopa'], ENT_QUOTES, 'UTF-8'), html_entity_decode(utf8_decode($data[$i]['nompro']), ENT_QUOTES, 'UTF-8'),$data[$i]['codigoum'],$data[$i]['stockactual_wc_producto'],'');
                        $pdf->Row($fila);
                        $relleno = !$relleno;
                        $pdf->fill($relleno);
                }
            }
        }
        $pdf->ln();
        $pdf->ln();
        $pdf->Cell(272, 7, '___________________________________________________________________________________________________________________________', 0, 0, 'C', FALSE);
        $pdf->AliasNbPages();
        $pdf->Output();
    }
    
    function reporteClientesxvendedor() {
        set_time_limit(2000);
        //$linprod = $_REQUEST['lstLineaProductos'];

        $cliente = New Cliente();

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
        $datos = $cartcli->listarCarteraNuevoFormato3($mostrar);
        $cantidadData = count($datos);

        $titulos = array('Cliente', 'RUC', 'Telefonos', 'Direccion', 'Deuda', 'Linea', 'Condiciones', 'L/Credito');
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(50, 20, 29, 70, 24, 30, 30, 24);
        $orientacion = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $orientacion2 = array('L', 'C', 'L', 'L', 'R', 'C', 'C', 'R');
        $pdf->SetWidths($ancho);
        $pdf->_titulo = "Cartera de Clientes | Vendedor";
        $pdf->_fecha = date('Y-m-d H:i:s');
        $pdf->_datoPie = "Cartera de Clientes por Vendedor " . $datos[0]['faproxllegada'];
        $fecha = "";
        if (!empty($fecini)) {
            $fecha .= "GUIAS DESDE " . $fecini;
        } else {
            $fecha .= "TODAS LAS GUIAS";
        }
        $fecha .= " HASTA ";
        if (!empty($fecfin)) {
            $fecha .= $fecfin;
        } else {
            $fecha .= date('Y/m/d');
        }
        $pdf->AddPage();
        $relleno = false;
        $pdf->fill($relleno);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $vendedor = new Actor();
        $reg = $vendedor->buscarxid($idvend);
        $pdf->ln();
        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->Cell(9, 7, "VENDEDOR: " . $reg[0]['nombres'] . " " . $reg[0]['apellidopaterno'] . " " . $reg[0]['apellidomaterno']);
        $pdf->fill(true);

        $zona = "";
        $ordenventa = $this->AutoLoadModel('ordenventa');

        for ($i = 0; $i < $cantidadData; $i++) {
            if ($zona != $datos[$i]['nombrezona']) {
                $pdf->ln();
                $zona = $datos[$i]['nombrezona'];
                $pdf->SetFont('Helvetica', 'B', 8.5);
                $pdf->Cell(150, 7, "ZONA: " . $datos[$i]['nombrezona'], 0);
                $pdf->ln();
                $pdf->SetFont('Helvetica', 'B', 7.5);
                $relleno = true;
                $pdf->fill($relleno);
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetAligns($orientacion);
                $pdf->Row($titulos);
            }

            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetAligns($orientacion2);
            $pdf->SetFont('Helvetica', '', 7);
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
            $fila = array($datos[$i]['cliente'], $datos[$i]['ruc'], $datos[$i]['telefono'] . (empty($datos[$i]['telefono']) || empty($datos[$i]['celular']) ? "" : " / ") . $datos[$i]['celular'], $datos[$i]['direccion'] . ' ' . $datos[$i]['dist'] . ' - ' . $datos[$i]['prov'] . ' - ' . $datos[$i]['depa'], 'S/ ' . number_format($datos[$i]['deudatotal'], 2), $ordenventa->lineadeventa($datos[$i]['idcliente']), $Auxcondiciones, 'S/ ' . number_format($textLineacredito, 2));
            $pdf->Row($fila);

            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $pdf->ln();

        $pdf->ln();
        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->Cell(40, 7, "Total Clientes Exportados: ", 0);
        $pdf->SetFont('Helvetica', '', 8.5);
        $pdf->Cell(150, 7, $cantidadData, 0);
        $pdf->ln();

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteCarteraClientesxvendedor() {
        set_time_limit(2000);
        //$linprod = $_REQUEST['lstLineaProductos'];

        $cliente = New Cliente();

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
        $datos = $cartcli->listarCarteraNuevoFormato2($mostrar);
        $cantidadData = count($datos);

        $titulos = array('Cliente', 'RUC', 'Telefonos', 'Direccion', 'Deuda', 'Linea', 'Condiciones', 'L/Credito');
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(50, 20, 29, 70, 24, 30, 30, 24);
        $orientacion = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $orientacion2 = array('L', 'C', 'L', 'L', 'R', 'C', 'C', 'R');
        $pdf->SetWidths($ancho);
        $pdf->_titulo = "Cartera de Clientes | Vendedor";
        $pdf->_fecha = date('Y-m-d H:i:s');
        $fecha = "";
        if (!empty($fecini)) {
            $fecha .= $fecini;
        }
        else {
            $fecha .= "Inicio";
        }
        $fecha.=" - ";
        if (!empty($fecfin)) {
            $fecha .= $fecfin;
        } else {
            $fecha .= date('Y/m/d');
        }
        $pdf->_datoPie = "Cartera de Clientes por Vendedor | " . $fecha;
        
        $pdf->AddPage();
        $relleno = false;
        $pdf->fill($relleno);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $vendedor = new Actor();
        $reg = $vendedor->buscarxid($idvend);
        $pdf->ln();
        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->Cell(9, 7, "VENDEDOR: " . $reg[0]['nombres'] . " " . $reg[0]['apellidopaterno'] . " " . $reg[0]['apellidomaterno']);
        $pdf->fill(true);

        $zona = "";
        $ordenventa = $this->AutoLoadModel('ordenventa');

        for ($i = 0; $i < $cantidadData; $i++) {
            if ($zona != $datos[$i]['nombrezona']) {
                $pdf->ln();
                $zona = $datos[$i]['nombrezona'];
                $pdf->SetFont('Helvetica', 'B', 8.5);
                $pdf->Cell(150, 7, "ZONA: " . $datos[$i]['nombrezona'], 0);
                $pdf->ln();
                $pdf->SetFont('Helvetica', 'B', 7.5);
                $relleno = true;
                $pdf->fill($relleno);
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetTextColor(255, 255, 255);
                $pdf->SetDrawColor(12, 78, 139);
                $pdf->SetLineWidth(.3);
                $pdf->SetAligns($orientacion);
                $pdf->Row($titulos);
            }

            $pdf->SetFillColor(224, 235, 255);
            $pdf->SetTextColor(0);
            $pdf->SetAligns($orientacion2);
            $pdf->SetFont('Helvetica', '', 7);
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
            $fila = array($datos[$i]['cliente'], $datos[$i]['ruc'], $datos[$i]['telefono'] . (empty($datos[$i]['telefono']) || empty($datos[$i]['celular']) ? "" : " / ") . $datos[$i]['celular'], $datos[$i]['direccion'] . ' ' . $datos[$i]['dist'] . ' - ' . $datos[$i]['prov'] . ' - ' . $datos[$i]['depa'], 'S/ ' . number_format($datos[$i]['deudatotal'], 2), $ordenventa->lineadeventa($datos[$i]['idcliente']), $Auxcondiciones, 'S/ ' . number_format($textLineacredito, 2));
            $pdf->Row($fila);

            $relleno = !$relleno;
            $pdf->fill($relleno);
        }
        $pdf->ln();
        $pdf->ln();

        $pdf->ln();
        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->Cell(40, 7, "Total Clientes Exportados: ", 0);
        $pdf->SetFont('Helvetica', '', 8.5);
        $pdf->Cell(150, 7, $cantidadData, 0);
        $pdf->ln();

        $pdf->AliasNbPages();
        $pdf->Output();
    }

    function reporteCarteraClientesxzona() {
        set_time_limit(2000);
        //$linprod = $_REQUEST['lstLineaProductos'];
        $cliente = New Cliente();

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

        $ultimoVendedor = $_REQUEST['txtUltVend'];
        $lineadelliente = $_REQUEST['txtLinCli'];
        $condicioncompra = $_REQUEST['txtCondComp'];
        $verAuditoriaCobranza = $_REQUEST['lstVerAuditoriaCobranza'];
        
        $cartcli = new CarteraCliente($idvend, $condicion, $catprin, $regcobr, $zona, $fecini, $fecfin, $depa, $prov, $dist, $ordenar, $aprobados);
        $datos = $cartcli->listarCarteraNuevoFormato2($mostrar);
        $cantidadData = count($datos);
        
        $titulos = array('Cliente', 'RUC', 'Telefonos', 'Direccion', 'Deuda', 'Linea', 'Condiciones', 'L/Credito', 'Vendedor');
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 6.5);
        $ancho = array(40, 20, 20, 70, 24, 24, 24, 24, 31);
        $orientacion = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $orientacion2 = array('L', 'C', 'L', 'L', 'R', 'C', 'C', 'R', 'L');
        $pdf->SetWidths($ancho);
        $fecha = "";
        if (!empty($fecini)) {
            $fecha .= $fecini;
        }
        else {
            $fecha .= "Inicio";
        }
        $fecha.=" - ";
        if (!empty($fecfin)) {
            $fecha .= $fecfin;
        } else {
            $fecha .= date('Y/m/d');
        }
        $pdf->_titulo = "Cartera de Clientes | Zona";
        $pdf->_fecha = date('Y-m-d H:i:s');
        $pdf->_datoPie = "Cartera de Clientes por Zona | " . $fecha;
        
        $pdf->AddPage();
        $relleno = false;
        $pdf->fill($relleno);
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);
        /*
        $pdf->ln();
        $pdf->Cell(9, 7, "LETRAS ENVIADAS AL BANCO: " . $cantidadData);
        $pdf->fill(true);
        */
        $zona = "";
        $ordenventa = $this->AutoLoadModel('ordenventa');
        $actor = $this->AutoLoadModel('actor');

        for ($i = 0; $i < $cantidadData; $i++) {
                if ($zona != $datos[$i]['nombrezona']) {
                    $pdf->ln();
                    $zona = $datos[$i]['nombrezona'];
                    $pdf->SetFont('Helvetica', 'B', 8.5);
                    $pdf->Cell(150, 7, "ZONA: " . $datos[$i]['nombrezona'], 0);
                    $pdf->ln();
                    $pdf->SetFont('Helvetica', 'B', 7.5);
                    $relleno = true;
                    $pdf->fill($relleno);
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetTextColor(255, 255, 255);
                    $pdf->SetDrawColor(12, 78, 139);
                    $pdf->SetLineWidth(.3);
                    $pdf->SetAligns($orientacion);
                    $pdf->Row($titulos);
                }
                
                $pdf->SetFillColor(224, 235, 255);
                $pdf->SetTextColor(0);
                $pdf->SetAligns($orientacion2);
                $pdf->SetFont('Helvetica', '', 7);
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
                $tempVendedor = '';
                $dataVendedor = $actor->buscarxid($datos[$i]['idvendedor']);
                if (count($dataVendedor) > 0) {
                    $tempVendedor = $dataVendedor[0]['nombres'] . ' ' . $dataVendedor[0]['apellidopaterno'] . ' ' . $dataVendedor[0]['apellidomaterno'];
                }
                $textLineacredito = (!empty($datos[$i]['lineacredito']) ? $datos[$i]['lineacredito'] : $datos[$i]['lineacreditodisponible']);
                $fila = array($datos[$i]['cliente'], $datos[$i]['ruc'], $datos[$i]['telefono'] . (empty($datos[$i]['telefono']) || empty($datos[$i]['celular']) ? "" : " / ") . $datos[$i]['celular'], $datos[$i]['direccion'] . ' ' . $datos[$i]['dist']. ' - ' . $datos[$i]['prov'] . ' - ' . $datos[$i]['depa'], 'S/ ' . number_format($datos[$i]['deudatotal'], 2), $ordenventa->lineadeventa($datos[$i]['idcliente']), $Auxcondiciones, 'S/ ' . number_format($textLineacredito, 2), $tempVendedor);
                $pdf->Row($fila);
                
                $relleno = !$relleno;
                $pdf->fill($relleno);
        }
        $pdf->ln();

        $pdf->ln();
        $pdf->SetFont('Helvetica', 'B', 8.5);
        $pdf->Cell(40, 7, "Total Clientes Exportados: ", 0);
        $pdf->SetFont('Helvetica', '', 8.5);
        $pdf->Cell(150, 7, $cantidadData, 0);
        $pdf->ln();
        
        $pdf->AliasNbPages();
        $pdf->Output();
    }
    
    public function pendientesporvendedor() {
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
        
        $pdf = new PDF_MC_Table("L", "mm", "A4");
        $pdf->SetFont('Helvetica', 'B', 7);
        $ancho = array(90, 50, 50);
        $pdf->SetWidths($ancho);
        
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
        $pdf->_fecha = 'Fecha vencimiento: ' . $textReporte;
        $pdf->_titulo = "PENDIENTES POR VENDEDOR";
        $pdf->_datoPie = 'Impreso el: ' . date('Y-m-d H:m:s');
        $pdf->AddPage();
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(12, 78, 139);
        $pdf->SetLineWidth(.4);
        $orientacion = array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C');
        $pdf->_orientacion = $orientacion;
        $pdf->SetAligns($orientacion);

        $ancho = array(38, 34, 34, 34, 34, 34, 34, 36);
        $pdf->SetWidths($ancho);
        
        $nombrecliente = "";
        if (!empty($get_txtIdCliente)) {
            $clientemodel = $this->AutoLoadModel('cliente');
            $dataCliente = $clientemodel->listadoxFiltro("idcliente='$get_txtIdCliente'");
            $nombrecliente = $dataCliente[0]['razonsocial'];
        }
        $nombredevendedor = '';
        if (!empty($get_txtIdVendedor)) {
            $vendedor = new Actor();
            $reg = $vendedor->buscarxid($get_txtIdVendedor);
            $nombredevendedor = $reg[0]['nombres'] . " " . $reg[0]['apellidopaterno'] . " " . $reg[0]['apellidomaterno'];
        }
        
        $pdf->ln();
        $pdf->Cell(9, 7, "ZONA: " . $get_condiciones . (!empty($nombrecliente) ? ' | CLIENTE: ' . $nombrecliente : '') . (!empty($nombredevendedor) ? ' | VENDEDOR: ' . $nombredevendedor : ''));
        $pdf->ln();
        $pdf->fill(true);
        $fila = array('VENDEDOR', 'CONTADO', 'CREDITOS VENCIDOS', 'CREDITOS POR VENCER', 'LETRAS AL BANCO', 'LETRAS POR FIRMAR', 'LETRAS PROTESTADAS', 'TOTAL PENDIENTE');
        $pdf->Row($fila);
        $pdf->fill(false);
        $ancho2 = array(38, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 17, 18, 18);
        $pdf->SetWidths($ancho2);
        $pdf->SetFillColor(224, 235, 255);
        $fila = array('', 'S/', 'US $', 'S/', 'US $', 'S/', 'US $', 'S/', 'US $', 'S/', 'US $', 'S/', 'US $', 'S/', 'US $');
        $pdf->fill(true);
        $pdf->Row($fila);
        

        //FIN DEL CAMBIO
        $pdf->setxy(10, 31.5);
        $pdf->MultiCell(38, 10, 'VENDEDOR', 1, 'C', true);
      
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetDrawColor(12, 78, 139);
        $orientacion = array('L', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R', 'R');
        $pdf->SetAligns($orientacion);
        $pdf->SetFont('Helvetica', '', 7);
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
            $fila = array($arrayInformacion[$aInfo]['vendedor'], 
                            (isset($arrayInformacion[$aInfo]['contado']['soles']) ? $arrayInformacion[$aInfo]['contado']['soles'] : 0.00), 
                            (isset($arrayInformacion[$aInfo]['contado']['dolares']) ? $arrayInformacion[$aInfo]['contado']['dolares'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['creditovencidos']['soles']) ? $arrayInformacion[$aInfo]['creditovencidos']['soles'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['creditovencidos']['dolares']) ? $arrayInformacion[$aInfo]['creditovencidos']['dolares'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['creditoxvencer']['soles']) ? $arrayInformacion[$aInfo]['creditoxvencer']['soles'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['creditoxvencer']['dolares']) ? $arrayInformacion[$aInfo]['creditoxvencer']['dolares'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['letrapa']['soles']) ? $arrayInformacion[$aInfo]['letrapa']['soles'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['letrapa']['dolares']) ? $arrayInformacion[$aInfo]['letrapa']['dolares'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['letrasinpa']['soles']) ? $arrayInformacion[$aInfo]['letrasinpa']['soles'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['letrasinpa']['dolares']) ? $arrayInformacion[$aInfo]['letrasinpa']['dolares'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['protesto']['soles']) ? $arrayInformacion[$aInfo]['protesto']['soles'] : 0.00),
                            (isset($arrayInformacion[$aInfo]['protesto']['dolares']) ? $arrayInformacion[$aInfo]['protesto']['dolares'] : 0.00),
                            $totalVendedorSoles,
                            $totalVendedorDolares);
            $pdf->Row($fila);
            $pdf->fill(true);
            $totalGeneralSoles += $totalVendedorSoles;
            $totalGeneralDolares += $totalVendedorDolares;
 	}
        
        $pdf->SetFillColor(224, 235, 255);
        $pdf->SetFont('Helvetica', 'B', 7);
        $fila = array('MONTOS TOTALES:', 
                        'S/ ' . number_format($letraContadoSoles, 2),
                        'US $ ' . number_format($letraContadoDolares, 2),
                        'S/ ' . number_format($crediVencidoSoles, 2),
                        'US $ ' . number_format($crediVencidoDolares, 2),
                        'S/ ' . number_format($crediXVencerSoles, 2),
                        'US $ ' . number_format($crediXVencerDolares, 2),
                        'S/ ' . number_format($letraPASoles, 2),
                        'US $ ' . number_format($letraPADolares, 2),
                        'S/ ' . number_format($letrasinPASoles, 2),
                        'US $ ' . number_format($letrasinPADolares, 2),
                        'S/ ' . number_format($letraProtestoSoles, 2),
                        'US $ ' . number_format($letraProtestoDolares, 2),
                        'S/ ' . number_format($totalGeneralSoles, 2),
                        'US $ ' . number_format($totalGeneralDolares, 2));
        $pdf->Row($fila);
        $pdf->fill(false);
        $pdf->AliasNbPages();
        $pdf->Output();
    }


}

?>
