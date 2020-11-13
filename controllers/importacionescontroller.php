<?php

Class importacionescontroller extends ApplicationGeneral {

    function ordenCompra() {
        $ordenCompra = new Ordencompra();
        $url = "/" . $_REQUEST['url'];
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        $data['Ordencompra'] = $ordenCompra->listaOrdenCompraPaginado($_REQUEST['id']);
        $paginacion = $ordenCompra->paginadoOrdenCompra();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show("/importaciones/listadoordencompra.phtml", $data);
    }

    /* Registro de nueva orden de compra */

    function nuevaordencompra() {
        $ordCom = new Ordencompra();
        $empresa = new Almacen();
        $proveedor = new Proveedor();
        //$data['CodigoOrden']=$ordCom->generaCodigo();
        $data['Empresa'] = $empresa->listadoAlmacen();
        $data['Proveedor'] = $proveedor->listadoProveedores();
        $data['RutaImagen'] = $this->rutaImagenesProducto();
        $this->view->show("/importaciones/nuevaordencompra.phtml", $data);
    }

    //Reporte de orden de compra
    function reportordcompra() {
        //$this->view->template="reporteordencompra";
        $this->view->show('/reporte/ordencompra.phtml');
    }

    function reporteOrdenCompra() {
        $idProducto = $_REQUEST['id'];
        $repote = new Reporte();
        $data = $repote->reporteOrdenCompra($idProducto);
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            echo    '<td>' . date("d/m/Y", strtotime($data[$i]['fechacompra'])) . '</td>';
            echo    '<td>' . $data[$i]['idalmacen'] . '</td>';
            echo    '<td>' . $data[$i]['idproveedor'] . '</td>';
            echo    '<td>' . $data[$i]['cantidadsolicitada'] . '</td>';
            echo    '<td>' . $data[$i]['fob'] . '</td>';
            echo "</tr>";
        }
    }

    //Lista de precios
    function reporteListaPrecio() {
        $idLinea = $_REQUEST['linea'];
        $idSubLinea = $_REQUEST['sublinea'];
        $producto = new Producto();
        $data = $producto->listaPrecio($idLinea, $idSubLinea);
        for ($i = 0; $i < count($data); $i++) {
            echo '<tr>';
            echo    "<td>" . $data[$i]['codigo'] . "</td>";
            echo    "<td>" . $data[$i]['nompro'] . "</td>";
            echo    "<td>" . $data[$i]['preciolista'] . "</td>";
            echo    "<td>" . $data[$i]['stockactual'] . "</td>";
            echo    "<td>" . $data[$i]['nomum'] . "</td>";
            echo    "<td>" . $data[$i]['nomemp'] . "</td>";
            echo '<tr>';
        }
    }

    function exportarordencompra() {
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=ventas" . date('YmdHis') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $id = $_REQUEST['id'];
        if (!empty($_REQUEST['id']) && $_REQUEST['id'] > 0) {
            $ordenCompra = new Ordencompra();
            $detalleOrdenCompra = new Detalleordencompra();
            $almacen = new Almacen();
            $proveedor = new Proveedor();
            $empresa = $this->AutoLoadModel('empresa');
            $rutaImagen = $this->rutaImagenesProducto();
            $data['Ordencompra'] = $ordenCompra->editaOrdenCompra($id);
            $Detalleordencompra = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
            $data['Empresa'] = $almacen->listadoAlmacen();
            $data['RutaImagen'] = $rutaImagen;
            $data['Proveedor'] = $proveedor->listadoProveedores();
            $data['Flete'] = $empresa->listadoEmpresaxIdTipoEmpresa(1);
            $data['Aduanas'] = $empresa->listadoEmpresaxIdTipoEmpresa(3);
            $data['Seguro'] = $empresa->listadoEmpresaxIdTipoEmpresa(2);
            echo '<table id="tblDetalleOrdenCompra" border="1">
                    <thead>
                        <tr>
                            <th rowspan="2">N°</th>
                            <th rowspan="2">Codigo</th>
                            <th rowspan="2">Descripcion</th>
                            <th rowspan="2">Marca</th>
                            <th rowspan="2">QTY</th>
                            <th rowspan="2">Unit</th>
                            <th rowspan="2">Vol. m3</th>
                            <th rowspan="2">CBM. m3</th>
                            <th rowspan="2">FOB<br>Unit.</th>
                            <th rowspan="2">FOB<br>Total</th>
                            <th colspan="7">Costos Fijos</th>
                            <th colspan="6">Costos Variables</div></th>
                            <th rowspan="2">Agente<br>Aduana</th>
                            <th rowspan="2">Flete<br>Interno</th>			
                            <th colspan="2">Costo Puesto <br>Nuestro Almacen</th>
                            <th rowspan="2">Costo<br>(%)</th>
                        </tr>
                        <tr>
                            <th>Flt.</th>
                            <th>Seg.</th>
                            <th>CIF</th>
                            <th>CIF<br>Unit.</th>
                            <th>ADV<br>%</th>
                            <th>ADV</th>
                            <th>TD</th>
                            <th>Flat</th>
                            <th>V°B°</th>
                            <th>Gate In</th>
                            <!--<th >Box Fee</th>
                            <th>Ins.<br> Fee</th>
                            <th>Sobre<BR>estadia</th>
                            <th>Doc Fee</th>
                            <th>Gastos Adm.</th>-->
                            <th>CA-1</th>
                            <th>CA-2</th>
                            <th>CA-3</th>
                            <th>Total</th>
                            <th>Unitario</th>
                        </tr>
                    </thead>
		<tbody>';
            for ($i = 0; $i < count($Detalleordencompra); $i++) {
                echo '<tr>';
                $tamano = '30px';
                $tamanoM = '55px';
                $tamanoG = '70px';
                $tamanoGG = "100px";
                echo    '<td class="">' . ($i + 1) . '</td>';
                //Codigo,Cantidad,Volumen,Fob,Fob Total
                $iddetalleOC = $Detalleordencompra[$i]['iddetalleordencompra'];
                $idproductoOC = $Detalleordencompra[$i]['idproducto'];
                $cantidad = $Detalleordencompra[$i]['cantidadrecibidaoc'];
                $volumenxUnidad = $Detalleordencompra[$i]['vol'];
                $volumen = $Detalleordencompra[$i]['cbm'];
                $fob = $Detalleordencompra[$i]['fobdoc'];
                $piezas = !empty($Detalleordencompra[$i]['piezas']) ? $Detalleordencompra[$i]['piezas'] : 0;
                $carton = !empty($Detalleordencompra[$i]['carton']) ? $Detalleordencompra[$i]['carton'] : 0;
                $fobTotal = $fob * $cantidad;
                echo    '<td class="codigo" style="width:' . $tamanoGG . '">' . $Detalleordencompra[$i]['codigopa'] . '</td>';
                //echo '<input type="hidden" name="Detalleordencompra['.($i+1).'][iddetalleordencompra]" value="'.$iddetalleOC.'">';
                //echo '<input type="hidden" name="Detalleordencompra['.($i+1).'][idproducto]" value="'.$idproductoOC.'">';
                echo    '<td class="codigo" style="width:' . $tamanoGG . '">' . $Detalleordencompra[$i]['nompro'] . '</td>';
                echo    '<td class="codigo" style="width:' . $tamanoGG . '">' . $Detalleordencompra[$i]['marca'] . '</td>';
                echo    '<td class="center">' . $cantidad . '</td>';
                echo    '<td class="codigo" style="width:' . $tamanoGG . '">' . $Detalleordencompra[$i]['unidadmedida'] . '</td>';
//		echo '<input class="piezas" type="hidden" name="Detalleordencompra['.($i+1).'][piezas]" value="'.$piezas.'"> <input type="hidden" name="Producto['.($i+1).'][preciocosto]"  value="'.$Detalleordencompra[$i]['preciocosto'].'">';
//		echo '<input class="carton" type="hidden" name="Detalleordencompra['.($i+1).'][carton]" value="'.$carton.'">';
                echo    '<td>' . $volumenxUnidad . '</td>';
                echo    '<td>' . number_format($volumen, 2) . '</td>';
                echo    '<td>' . $fob . '</td>';
                echo    '<td>' . number_format($fobTotal, 2) . '</td>';
                //Flete,Seguro,Cif,Cif Unit.
                $flete = !empty($Detalleordencompra[$i]['fleted']) ? ($Detalleordencompra[$i]['fleted']) : "0.00";
                $seguro = !empty($Detalleordencompra[$i]['seguro']) ? ($Detalleordencompra[$i]['seguro']) : "0.00";
                $ciftotal = $fobTotal + $seguro + $flete;
                $cifunitario = $ciftotal / $cantidad;
                echo    '<td>' . $flete . '</td>';
                echo    '<td>' . $seguro . '</td>';
                echo    '<td>' . number_format($ciftotal, 2) . '</td>';
                echo    '<td>' . number_format($cifunitario, 2) . '</td>';
                //%AdValorem,AdValorem,Tasa Desapacho
                $advaloremporcentaje = !empty($Detalleordencompra[$i]['advaloremporcentaje']) ? ($Detalleordencompra[$i]['advaloremporcentaje']) : "0";
                $advaloremvalor = !empty($Detalleordencompra[$i]['advaloremvalor']) ? ($Detalleordencompra[$i]['advaloremvalor']) : "0.00";
                $tasadespacho = !empty($Detalleordencompra[$i]['costotasadesp']) ? ($Detalleordencompra[$i]['costotasadesp']) : "0.00";
                echo    '<td>' . $advaloremporcentaje . '</td>';
                echo    '<td>' . $advaloremvalor . '</td>';
                echo    '<td>' . $tasadespacho . '</td>';
                //Gastos Variables:
                $flat = !empty($Detalleordencompra[$i]['flat']) ? ($Detalleordencompra[$i]['flat']) : "0.00";
                $VoBo = !empty($Detalleordencompra[$i]['VoBo']) ? ($Detalleordencompra[$i]['VoBo']) : "0.00";
                $gate_in = !empty($Detalleordencompra[$i]['gate_in']) ? ($Detalleordencompra[$i]['gate_in']) : "0.00";
                //$box_fee=!empty($Detalleordencompra[$i]['box_fee'])?($Detalleordencompra[$i]['box_fee']):"0.00";
                //$insurance_fee=!empty($Detalleordencompra[$i]['insurance_fee'])?($Detalleordencompra[$i]['insurance_fee']):"0.00";
                //$sobre_estadia=!empty($Detalleordencompra[$i]['sobre_estadia'])?($Detalleordencompra[$i]['sobre_estadia']):"0.00";
                //$doc_fee=!empty($Detalleordencompra[$i]['doc_fee'])?($Detalleordencompra[$i]['doc_fee']):"0.00";
                $agenteaduanas = !empty($Detalleordencompra[$i]['agenteaduanas']) ? ($Detalleordencompra[$i]['agenteaduanas']) : "0.00";
                $gas_adm = !empty($Detalleordencompra[$i]['gas_adm']) ? ($Detalleordencompra[$i]['gas_adm']) : "0.00";
                $cadic1 = !empty($Detalleordencompra[$i]['cv1']) ? ($Detalleordencompra[$i]['cv1']) : "0.00";
                $cadic2 = !empty($Detalleordencompra[$i]['cv2']) ? ($Detalleordencompra[$i]['cv2']) : "0.00";
                $cadic3 = !empty($Detalleordencompra[$i]['cv3']) ? ($Detalleordencompra[$i]['cv3']) : "0.00";
                $fleteInterno = !empty($Detalleordencompra[$i]['fleteInterno']) ? ($Detalleordencompra[$i]['fleteInterno']) : "0.00";
                $advalorenvalor = !empty($Detalleordencompra[$i]['advaloremvalor']) ? ($Detalleordencompra[$i]['advaloremvalor']) : "0.00";
                echo    '<td class="">' . $flat . '</td>';
                echo    '<td class="">' . $VoBo . '</td>';
                echo    '<td class="">' . $gate_in . '</td>';
                //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][box_fee]" value="'.$box_fee.'" class="txtBoxFeeDetalle numeric required" style="width:'.$tamano.'" ></td>';
                //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][insurance_fee]" value="'.$insurance_fee.'" class="txtInsuranceFeeDetalle numeric required" style="width:'.$tamano.'" ></td>';
                //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][sobre_estadia]" value="'.$sobre_estadia.'" class="txtSobreestadiaDetalle numeric required" style="width:'.$tamano.'" ></td>';
                //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][doc_fee]" value="'.$doc_fee.'" class="txtDocFeeDetalle numeric required" style="width:'.$tamano.'" ></td>';
                //echo '<td class=""><input type="text" name="Detalleordencompra['.($i+1).'][gas_adm]" value="'.$gas_adm.'" class="txtGasAdmDetalle numeric required" style="width:'.$tamano.'" ></td>';
                echo    '<td class="">' . $cadic1 . '</td>';
                echo    '<td class="">' . $cadic2 . '</td>';
                echo    '<td class="">' . $cadic3 . '</td>';
                echo    '<td class="">' . $agenteaduanas . '</td>';
                echo    '<td class="">' . $fleteInterno . '</td>';
                //Calculos Finales
                //$total=$ciftotal+$advalorenvalor+$tasadespacho+$flat+$VoBo+$gate_in+$box_fee+$insurance_fee+$sobre_estadia+$doc_fee+$gas_adm+$fleteInterno+$agenteaduanas+$cadic1+$cadic2+$cadic3;
                $total = $ciftotal + $advalorenvalor + $tasadespacho + $fleteInterno + $flat + $VoBo + $gate_in + $agenteaduanas + $cadic1 + $cadic2 + $cadic3;
                //$total=$total;
                $totalunitario = $total / $cantidad;
                $porcentaje = (($totalunitario - $fob) / $fob) * 100;
                echo    '<td class="">' . $total . '</td>';
                echo    '<td class="">' . number_format($totalunitario, 2) . '</td>';
                echo    '<td class="">' . number_format($porcentaje, 2) . '</td>';
                echo '</tr>';
                //Totales
                $fobTotalOC += $fobTotal;
                $fleteOC += $flete;
                $seguroOC += $seguro;
                $cifOC += $ciftotal;
                $AdValoremOC += $advalorenvalor;
                $tasadespachoOC += $tasadespacho;
                //$cf1OC+=$cf1;
                //$cf2OC+=$cf2;
                $flatOC += $flat;
                $VoBoOC += $VoBo;
                $gate_inOC += $gate_in;
                /* $box_feeOC+=$box_fee;
                  $insurance_feeOC+=$insurance_fee;
                  $sobre_estadiaOC+=$sobre_estadia;
                  $doc_feeOC+=$doc_fee;
                  $gas_admOC+=$gas_adm; */
                $fleteinternoOC += $fleteInterno;
                $agenteaduanasOC += $agenteaduanas;
                $cadic1OC += $cadic1;
                $cadic2OC += $cadic2;
                $cadic3OC += $cadic3;
                $importeTotalOC += $total;
            }
            echo '</tbody>';
            echo '<tfoot>
			<tr>
                            <th colspan="7" class="right bold important">Moneda US $</th>
                            <td colspan="2" class="right bold important">Totales:</td>';
            echo            '<td class="txtFobTotalOC right bold">' . number_format($fobTotalOC, 2) . '</td>';
            echo            '<td class="txtFleteOC right bold">' . number_format($fleteOC, 2) . '</td>';
            echo            '<td class="txtSeguroOC right bold">' . number_format($seguroOC, 2) . '</td>';
            echo            '<td class=" right bold">' . number_format($cifOC, 2) . '</td>';
            echo            '<td class="right bold">&nbsp;</td>';
            echo            '<td class="right bold">&nbsp;</td>';
            echo            '<td class="right bold">' . number_format($AdValoremOC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($tasadespachoOC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($flatOC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($gate_inOC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($VoBoOC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($cadic1OC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($cadic2OC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($cadic3OC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($agenteaduanasOC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($fleteinternoOC, 2) . '</td>';
            echo            '<td class="right bold">' . number_format($importeTotalOC, 2) . '</td>';
            echo            '<td class="right bold">&nbsp;</td>';
            echo            '<td class="right bold">&nbsp;</td>';
            echo        '</tr>';
            echo    '</tfoot>';
            echo '</table>';
        }
    }

    function registroDuas() {
        $ordenCompra = new Ordencompra();
        $url = "/" . $_REQUEST['url'];
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        $data['Ordencompra'] = $ordenCompra->listaOrdenCompraPaginado($_REQUEST['id']);
        $paginacion = $ordenCompra->paginadoOrdenCompra();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $this->view->show('/ordencompra/registroDuas.phtml', $data);
    }

    function detalleDua() {
        $id = $_REQUEST['id'];
        $detalle = new Detalleordencompra();
        $proveedor = new Proveedor();
        $dataProveedor = $proveedor->buscaProveedorxOdenCompra($id);
        $data = $detalle->listaDetalleOrdenCompra($id);
        for ($i = 0; $i < count($data); $i++) {
            echo "<tr>";
            echo    "<td>" . ($i + 1) . "</td>";
            echo    "<td>" . $data[$i]['codigopa'] . "</td>";
            echo    "<td>" . $data[$i]['nompro'] . "</td>";
            echo    "<td>" . $data[$i]['fobdoc'] . "</td>";
            echo    "<td>" . $data[$i]['cantidadsolicitadaoc'] . "</td>";
            echo "</tr>";
        }
        echo '<script>$("#lblProveedor").html("' . $dataProveedor[0]['razonsocialp'] . '");</script>';
    }

    function editarDua() {
        $id = $_REQUEST['id'];
        if (!empty($_REQUEST['id']) && $_REQUEST['id'] > 0) {
            $ordenCompra = new Ordencompra();
            $detalleOrdenCompra = new Detalleordencompra();
            $almacen = new Almacen();
            $proveedor = new Proveedor();
            $rutaImagen = $this->rutaImagenesProducto();
            $data['Ordencompra'] = $ordenCompra->editaOrdenCompra($id);
            $data['ListaPagosOC'] = $detalleOrdenCompra->listaPagoOrdenCompra($id);
            $data['Empresa'] = $almacen->listadoAlmacen();
            $data['RutaImagen'] = $rutaImagen;
            $data['Proveedor'] = $proveedor->listadoProveedores();
            $this->view->show("/ordencompra/editarDua.phtml", $data);
        } else {
            $ruta['ruta'] = "/importaciones/registroDuas";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function actualizaDua() {
        $idordencompra = $_POST['idOrdenCompra'];
        $nroDua = $_POST['nroDua'];
        $fechaCompraOC = $_POST['fechaCompraOC'];
        $CostoTotal = $_POST['CostoTotal'];
        $codigo = $_POST['codigo'];
        $fecha = $_POST['fecha'];
        $modalidad = $_POST['modalidad'];
        $monto = $_POST['monto'];
        $data1['nroDua'] = $nroDua;
        $data1['fechaCompraOC'] = $fechaCompraOC;
        $data1['CostoTotal'] = $CostoTotal;
        $ordenCompra = new Ordencompra();
        $contador = count($codigo);
        echo $contador;
        $exito = $ordenCompra->actualizaOrdenCompra($data1, $idordencompra);
        for ($i = 0; $i < $contador; $i++) {
            $data['idordencompra'] = $idordencompra;
            $data['codigo'] = $codigo[$i];
            $data['fecha'] = $fecha[$i];
            $data['monto'] = $monto[$i];
            $data['modalidad'] = $modalidad[$i];
            $exito = $ordenCompra->grabaPagoOrdenCompra($data);
        }
        $ruta['ruta'] = "/importaciones/registroDuas";
        $this->view->show("ruteador.phtml", $ruta);
    }
    
    function exportarordencompranuevo() {
        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=ventas" . date('YmdHis') . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        $id = $_REQUEST['id'];
        if (!empty($_REQUEST['id']) && $_REQUEST['id'] > 0) {
            $ordenCompra = new Ordencompra();
            $detalleOrdenCompra = new Detalleordencompra();
            $empresa = $this->AutoLoadModel('empresa');
            $rutaImagen = $this->rutaImagenesProducto();
            $Ordencompra = $ordenCompra->editaOrdenCompra($id);
            $Detalleordencompra = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
            echo '<table id="tblDetalleOrdenCompra">
                    <thead>
                        <tr>
                            <th rowspan="2">N°</th>
                            <th rowspan="2">Codigo</th>
                            <th rowspan="2">Descripcion</th>
                            <th rowspan="2">Marca</th>
                            <th rowspan="2">Empaque</th>
                            <th rowspan="2">QTY</th>
                            <th rowspan="2">Unit</th>
                            <th rowspan="2" colspan="2">PCSXCTN</th>
                            <th rowspan="2">FOB<br>Unit.</th>
                            <th rowspan="2">FOB<br>Total</th>                        
                            <th colspan="9">COSTOS LOGISTICOS LOCALES</th>                        
                            <th rowspan="2">ADV%</th>
                            <th rowspan="2">ADV<br>($.)</th>			
                            <th rowspan="2">GO<br>EMP</th>
                            <th rowspan="2" colspan="2">Costo Total</th>
                            <th rowspan="2">CIF CPA<br>(30%)</th>
                        </tr>
                        <tr>
                            <th>Flt.</th>
                            <th>Seg.</th>
                            <th>CIF</th>                        
                            <th>*SADA</th>
     
                            <th>*FARGO</th>
                            <th>V°B°</th>
                            <th>DEV<br>CTNDR</th>                        
                            <th>Flete<br>Interno</th>
                            <th>Ag.<br>Aduanas</th>
                        </tr>
                    </thead>               
                    <tbody>';
            $archivoConfig = parse_ini_file("config.ini", true);
            $TOTALfobTotal = 0;
            $TOTALflete = 0;
            $TOTALseguro = 0;
            $TOTALciftotal = 0;
            $TOTALsada = 0;
            $TOTALscto = 0;
            $TOTALfargo = 0;
            $TOTALVoBo = 0;
            $TOTALdevctndr = 0;
            $TOTALfleteInterno = 0;
            $TOTALagenteaduanas = 0;
            $TOTALadvaloremvalor = 0;
            $TOTALgoemp = 0;
            $TOTALcostototal = 0;
            $TOTALporcentaje = 0;
            $tamanioDetalle = count($Detalleordencompra);
            for ($i = 0; $i < $tamanioDetalle; $i++) {
                if (empty($Detalleordencompra[$i]['imagen'])) {
                    $rutaCompleta = '/public/imagenes/sinFoto.jpg';
                } else {
                    $rutaCompleta = $RutaImagen . $Detalleordencompra[$i]['codigopa'] . '/' . $Detalleordencompra[$i]['imagen'];
                }
                $tamano = '30px';
                $tamanoM = '55px';
                $tamanoG = '70px';
                $tamanoGG = "100px";
                echo '<tr>';
                echo    '<td class="">' . ($i + 1) . '</td>';
                //Codigo,Cantidad,Volumen,Fob,Fob Total
                $iddetalleOC = $Detalleordencompra[$i]['iddetalleordencompra'];
                $idproductoOC = $Detalleordencompra[$i]['idproducto'];
                $cantidad = $Detalleordencompra[$i]['cantidadrecibidaoc'];

                $fob = $Detalleordencompra[$i]['fobdoc'];
                $piezas = !empty($Detalleordencompra[$i]['piezas']) ? $Detalleordencompra[$i]['piezas'] : 0;
                $carton = !empty($Detalleordencompra[$i]['carton']) ? $Detalleordencompra[$i]['carton'] : 0;
                $fobTotal = $fob * $cantidad;
                echo    '<td class="codigo" style="width:' . $tamanoGG . '">' . $Detalleordencompra[$i]['codigopa'] . "</td>";
                echo    '<td class="codigo" style="width:150px">' . $Detalleordencompra[$i]['nompro'] . '</td>';
                echo    '<td class="codigo" style="width:' . $tamano . '">' . $Detalleordencompra[$i]['marca'] . '</td>';
                echo    '<td class="codigo" style="width:' . $tamano . '">' . $Detalleordencompra[$i]['codempaque'] . '</td>';
                echo    '<td class="center">' . $cantidad . '"</td>';
                echo    '<td class="codigo" style="width:' . $tamano . '">' . $Detalleordencompra[$i]['unidadmedida'] . '</td>';
                echo    '<td>' . $piezas . '</td>';
                echo    '<td>' . $carton . '</td>';
                echo    '<td>' . $fob . '</td>';
                echo    '<td>' . round($fobTotal, 2) . '"</td>';
                $TOTALfobTotal += $fobTotal;
                //Flete,Seguro,Cif,Cif Unit.
                $flete = !empty($Detalleordencompra[$i]['fleted']) ? ($Detalleordencompra[$i]['fleted']) : "0.00";
                $seguro = !empty($Detalleordencompra[$i]['seguro']) ? ($Detalleordencompra[$i]['seguro']) : "0.00";
                $ciftotal = $fobTotal + $seguro + $flete;
                $TOTALflete += $flete;
                $TOTALseguro += $seguro;
                $TOTALciftotal += $ciftotal;
                echo    '<td>' . $flete . '</td>';
                echo    '<td>' . $seguro . '</td>';
                echo    '<td>' . round($ciftotal, 2) . '</td>';
                $TOTALsada += $Detalleordencompra[$i]['sada'];
                $TOTALscto += $Detalleordencompra[$i]['scto'];
                $TOTALfargo += $Detalleordencompra[$i]['fargo'];
                echo    '<td>' . $Detalleordencompra[$i]['sada'] . '</td>';
                //echo '<td>' . $Detalleordencompra[$i]['scto'] . '</td>';
                echo    '<td>' . $Detalleordencompra[$i]['fargo'] . '</td>';
                $VoBo = !empty($Detalleordencompra[$i]['VoBo']) ? ($Detalleordencompra[$i]['VoBo']) : "0.00";
                $fleteInterno = !empty($Detalleordencompra[$i]['fleteInterno']) ? ($Detalleordencompra[$i]['fleteInterno']) : "0.00";
                $agenteaduanas = !empty($Detalleordencompra[$i]['agenteaduanas']) ? ($Detalleordencompra[$i]['agenteaduanas']) : "0.00";
                $TOTALVoBo += $VoBo;
                $TOTALdevctndr += $Detalleordencompra[$i]['devctndr'];
                $TOTALfleteInterno += $fleteInterno;
                $TOTALagenteaduanas += $agenteaduanas;
                echo    '<td>' . $VoBo . '</td>';
                echo    '<td>' . $Detalleordencompra[$i]['devctndr'] . '</td>';
                echo    '<td>' . $fleteInterno . '</td>';
                echo    '<td>' . $agenteaduanas . '</td>';
                $advaloremporcentaje = !empty($Detalleordencompra[$i]['advalorporcentaje']) ? ($Detalleordencompra[$i]['advalorporcentaje']) : "0";
                $advalorenvalor = !empty($Detalleordencompra[$i]['advaloremvalor']) ? ($Detalleordencompra[$i]['advaloremvalor']) : "0.00";                        
                $TOTALadvaloremvalor += $advalorenvalor;
                $TOTALgoemp += $Detalleordencompra[$i]['goemp'];                       
                echo    '<td>' . $advaloremporcentaje . '</td>';
                echo    '<td>' . $advalorenvalor . '</td>';
                echo    '<td>' . $Detalleordencompra[$i]['goemp'] . '</td>';
                //Calculos Finales
                //$total=$ciftotal+$advalorenvalor+$tasadespacho+$flat+$VoBo+$gate_in+$box_fee+$insurance_fee+$sobre_estadia+$doc_fee+$gas_adm+$fleteInterno+$agenteaduanas+$cadic1+$cadic2+$cadic3;
                $total = $ciftotal + $Detalleordencompra[$i]['sada'] + $Detalleordencompra[$i]['scto'] + $Detalleordencompra[$i]['fargo'] + $VoBo + $Detalleordencompra[$i]['devctndr'] + $fleteInterno + $agenteaduanas + $advalorenvalor + $Detalleordencompra[$i]['goemp'];
                //$total=$total;
                $totalunitario = $fob*1.3;
                $porcentaje = round((($total/$ciftotal) - 1), 2)*100 ;
                $TOTALporcentaje += $porcentaje;
                $TOTALcostototal += $total;
                echo    '<td>' . $total . '</td>';
                echo    '<td>' . round($porcentaje, 2) . '</td>';
                echo    '<td>' . round($totalunitario, 2) . '</td>';
                echo '</tr>';
            }

            echo '</tbody>
                 <tfoot>
                    <tr>
                        <th colspan="7" class="right bold important">Moneda US $</th>
                        <td colspan="3" class="right bold important">Totales:</td>
                        <td class="right bold">' . round($TOTALfobTotal, 2) . '</td>
                        <td class="right bold">' . round($TOTALflete, 2) . '</td>
                        <td class="right bold">' . round($TOTALseguro, 2) . '</td>
                        <td class="right bold">' . round($TOTALciftotal, 2) . '</td>
                        <td class="right bold">' . round($TOTALsada, 2) . '</td>
                        <td class="right bold">' . round($TOTALfargo, 2) . '</td>
                        <td class="right bold">' . round($TOTALVoBo, 2) . '</td>
                        <td class="right bold">' . round($TOTALdevctndr, 2) . '</td>
			<td class="right bold">' . round($TOTALfleteInterno, 2) . '</td>
                        <td class="right bold">' . round($TOTALagenteaduanas, 2) . '</td>
                        <td class="right bold"></td>
                        <td class="right bold">' . round($TOTALadvaloremvalor, 2) . '</td>                        
                        <td class="right bold">' . round($TOTALgoemp, 2) . '</td>
                        <td class="right bold">' . round($TOTALcostototal, 2) . '</td>
                        <td class="right bold">' . ($tamanioDetalle > 0 ? round($TOTALporcentaje/$tamanioDetalle, 2) : 0) . '</td>
                    </tr>	
                    <tr>';
                    $importeTotalOCsoles = $TOTALcostototal * $Ordencompra[0]['tipocambiovigente'];
            echo       '<th colspan="9" class="right"><h3>Importe Valorizado en US $ ' . round($TOTALcostototal, 2) . '</h3></th>
                        <th colspan="8" class="right"><h3>Tipo de Cambio Grabado: S/ ' . round($Ordencompra[0]['tipocambiovigente'], 2) . '</h3></th>
                        <th colspan="9" class="right"><h3>Importe Valorizado en S/ ' . round($importeTotalOCsoles, 2) . '</h3></th>
                    </tr>
                </tfoot>
            </table>';
        }
    }

    function productosxagotar() {
        $linea = new Linea();
        $almacen = new Almacen();
        $data['Linea'] = $linea->listadoLineas('idpadre=0');
        $data['Almacen'] = $almacen->listadoAlmacen();
        $this->view->show("/importaciones/productosxagotar.phtml",$data);
    }
    
    function productosxagotar_consultar() {
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $porcentaje = $_REQUEST['procentaje'];
        $idLinea = $_REQUEST['idLinea'];
        $idSubLinea = $_REQUEST['idSubLinea'];
        $idAlmacen = $_REQUEST['idAlmacen'];
        $idProducto = $_REQUEST['idProducto'];
        $cantidadveces = $_REQUEST['lstCantidadVeces'];
        if ($cantidadveces < 0 || $cantidadveces > 3) {
            $cantidadveces = 1;
        }
        $detalleOrdenCompra = new Detalleordencompra();
        $DetalleProductos = $detalleOrdenCompra->productosxagotar($idAlmacen, $idLinea, $idSubLinea, $idProducto);
        if ($porcentaje < 0 || $porcentaje > 100) {
            $porcentaje = 1;
        }
        $tam = count($DetalleProductos);
        $idlinea = -1;
        $idproducto = -1;
        for ($i = 0; $i < $tam; $i++) {
            $mostrar = 0;
            if ($idproducto != $DetalleProductos[$i]['idproducto']) {
                $idproducto = $DetalleProductos[$i]['idproducto'];
                if (!empty($DetalleProductos[$i]['fordencompra'])) {
                    $mostrar = 1;
                    $fechacompra = strtotime(date($DetalleProductos[$i]['fordencompra'], time()));
                    if (!empty($fechaInicio)) {
                        $fecha_entrada = strtotime(date($fechaInicio, time()));
                        if($fechacompra >= $fecha_entrada){
                            $mostrar = 1;
                        } else {
                            $mostrar = 0;
                        }
                    }
                    if (!empty($fechaFinal)&&$mostrar==1) {
                        $fecha_final = strtotime(date($fechaFinal, time()));
                        if($fechacompra <= $fecha_final){
                            $mostrar = 1;
                        } else {
                            $mostrar = 0;
                        }
                    }
                }
            }
            if ($mostrar == 1) {
                $cantidadtotal = $DetalleProductos[$i]['cantidadrecibidaoc'];
                if ($cantidadveces > 1) {
                    for ($j = 1; $j < $cantidadveces; $j++) {
                        if (isset($DetalleProductos[$i+$j]['idproducto']) && $idproducto == $DetalleProductos[$i+$j]['idproducto']) {
                            if (isset($DetalleProductos[$i+$j]['cantidadrecibidaoc'])) {
                                $cantidadtotal += $DetalleProductos[$i+$j]['cantidadrecibidaoc'];
                            }
                        }
                    }
                }
                if ($DetalleProductos[$i]['stockactual'] <= $cantidadtotal*($porcentaje/100)) {
                    if ($idlinea != $DetalleProductos[$i]['idlinea']) {
                        echo '<tr>' .
                                    '<th>LINEA: </th>' .
                                    '<td colspan="8">' . $DetalleProductos[$i]['nombrelinea'] . '</td>' .
                             '</tr>' . 
                             '<tr>' .
                                    '<th>Codigo</th>' .
                                    '<th>Descripción</th>' .
                                    '<th>Sublinea</th>' .
                                    '<th>Ult. Fecha Compra</th>' .
                                    '<th>FOB($)</th>' .
                                    '<th>CIF($)</th>' .
                                    '<th>P.L.($)</th>' .
                                    '<th>Compra</th>' .
                                    '<th>Stock Actual</th>' .
                             '</tr>'; 
                        $idlinea = $DetalleProductos[$i]['idlinea'];
                    }
                    echo '<tr>' .
                            '<td>' . $DetalleProductos[$i]['codigopa'] . '</td>' .
                            '<td>' . $DetalleProductos[$i]['nompro'] . '</td>' .
                            '<td>' . $DetalleProductos[$i]['nomlin'] . '</td>' .
                            '<td class="center">' . $DetalleProductos[$i]['fordencompra'] . '</td>' .
                            '<td class="right">' . $DetalleProductos[$i]['fobdoc'] . '</td>' .
                            '<td class="right">' . $DetalleProductos[$i]['cifventasdolares'] . '</td>' .
                            '<td class="right">' . $DetalleProductos[$i]['preciolistadolares'] . '</td>' .
                            '<td class="right">' . $cantidadtotal . '</td>' .
                            '<td class="right">' . $DetalleProductos[$i]['stockactual'] . '</td>' .
                         '</tr>';
                }
            }
        }

    }

}

?>
