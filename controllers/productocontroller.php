<?php
class ProductoController extends ApplicationGeneral
{
    private $mostrar = 5;
    function lista()
    {
        /*$producto=new Producto();
          $totalRegistro=$producto->contarProducto();
          $pagina=($_REQUEST['id'])?$_REQUEST['id']:1;
          $inicio=($pagina-1)*$this->mostrar;
          $paginas=ceil($totalRegistro/$this->mostrar);
          $paginacion=array('Paginas'=>$paginas,'Pagina'=>$pagina);
          $data['producto']=$producto->listadoProductos($inicio,$this->mostrar);
          $data['paginacion']=$paginacion;
          $data['rutaImagen']=$this->rutaImagenesProducto();*/
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $productos = $this->AutoLoadModel('producto');
        $marca = $this->AutoLoadModel('marca');
        $_SESSION['P_Producto'] = "";
        $dataProducto = $productos->listaProductosPaginado($_REQUEST['id']);
        
        $cantidadProducto = count($dataProducto);
        for ($i = 0; $i < $cantidadProducto; $i++) {
            $dataMarca = $marca->listaxId($dataProducto[$i]['idmarca']);
            $dataProducto[$i]['Marca'] = $dataMarca[0]['nombre'];
            
            $dataTipoproducto = $productos->listatipoproductoxId($dataProducto[$i]['idtipoproducto']);
            $dataProducto[$i]['Tipoproducto'] = $dataTipoproducto[0]['nombretipoproducto'];
        }
        
        $data['producto'] = $dataProducto;
        $paginacion = $productos->paginadoProducto();
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        
        $this->view->show("producto/lista.phtml", $data);
    }
    function listar2()
    {
        $producto = new Producto();
        $nombre = $_REQUEST['nombre'];
        $data = $producto->listadoProductos2($nombre);
        $objeto = $this->formatearparakui($data);
        header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        echo json_encode($objeto);
    }
    function buscarordencompra()
    {
        $idProducto = $_REQUEST['id'];
        $producto = new Producto();
        $data = $producto->buscaProductoOrdenCompra($idProducto);
        $unidadMedida = $this->unidadMedida();
        $empaque = $this->empaque();
        
        $dataRespuesta['codigo'] = !empty($data[0]['codigopa']) ? $data[0]['codigopa'] : "";
        $dataRespuesta['idproducto'] = !empty($data[0]['idproducto']) ? $data[0]['idproducto'] : "";
        $dataRespuesta['foto'] = !empty($data[0]['imagen']) ? $data[0]['imagen'] : "";
        $dataRespuesta['nompro'] = !empty($data[0]['nompro']) ? $data[0]['nompro'] : "";
        $dataRespuesta['nomemp'] = !empty($data[0]['empaque']) ? $data[0]['empaque'] : "";
        $dataRespuesta['marca'] = !empty($data[0]['marca']) ? $data[0]['marca'] : "";
        $dataRespuesta['nomum'] = !empty($data[0]['unidadmedida']) ? $data[0]['unidadmedida'] : "";
        $dataRespuesta['fob'] = !empty($data[0]['fob']) ? $data[0]['fob'] : "";
        $dataRespuesta['preciolista'] = !empty($data[0]['preciolista']) ? $data[0]['preciolista'] : "";
        $dataRespuesta['descuentosolicitado'] = !empty($data[0]['dunico']) ? $data[0]['dunico'] : "";
        $dataRespuesta['descuentovalor'] = !empty($data[0]['valor']) ? $data[0]['valor'] : "";
        $dataRespuesta['stockactual'] = !empty($data[0]['stockactual']) ? $data[0]['stockactual'] : "";
        
        echo json_encode($dataRespuesta);
    }

    function buscarxIdProducto()
    {
        $idProducto = $_REQUEST['idvalor'];
        $producto = new Producto();
        $marca = new Marca();
        $data = $producto->buscaProductoxId($idProducto);
        if (!empty($data[0]['idmarca'])) {
            $dataMarca = $marca->listaxId($data[0]['idmarca']);
            $marca = $dataMarca[0]['nombre'];
        } else {
            $marca = "";
        }
        $dataRespuesta['valor'] = $idProducto;
        $dataRespuesta['codigo'] = $data[0]['codigopa'];
        $dataRespuesta['idproducto'] = $data[0]['idproducto'];
        $dataRespuesta['nompro'] = !empty($data[0]['nompro']) ? $data[0]['nompro'] : "";
        $dataRespuesta['marca'] = !empty($marca) ? $marca : "";
        $dataRespuesta['precio'] = !empty($data[0]['preciocosto']) ? $data[0]['preciocosto'] : "";
        $dataRespuesta['preciolista'] = !empty($data[0]['preciolista']) ? $data[0]['preciolista'] : "";
        $dataRespuesta['precioreferencia01'] = !empty($data[0]['precioreferencia01']) ? $data[0]['precioreferencia01'] : 0;
        $dataRespuesta['precioreferencia02'] = !empty($data[0]['precioreferencia02']) ? $data[0]['precioreferencia02'] : 0;
        $dataRespuesta['precioreferencia03'] = !empty($data[0]['precioreferencia03']) ? $data[0]['precioreferencia03'] : 0;
        $dataRespuesta['stockdisponible'] = !empty($data[0]['stockdisponible']) ? $data[0]['stockdisponible'] : 0;
        $dataRespuesta['stockactual'] = !empty($data[0]['stockactual']) ? $data[0]['stockactual'] : 0;
        echo json_encode($dataRespuesta);
    }

    function productosoferta()
    {
        $idProducto = $_REQUEST['idproducto'];
        $producto = new Producto();
        $data = $producto->buscaProductoOrdenCompra($idProducto);
        $ofertamodel = $this->AutoLoadModel('oferta');
        $ofertasproducto = $ofertamodel->listaxIdproducto($idProducto);
        $tempOfertas = '';
        $tam = count($ofertasproducto);
        if ($tam > 0) {
            $archivoConfig = parse_ini_file("config.ini", true);
            $arrayTipocobro = $archivoConfig['TipoCobro'];
            for ($i = 0; $i < $tam; $i++) {
                $tempOfertas .= '<tr>' .
                    '<td style="text-align: center;">' . $arrayTipocobro[$ofertasproducto[$i]['tipocobro']] . '</td>' .
                    '<td style="text-align: center;">S/ ' . $ofertasproducto[$i]['precio'] . '</td>' .
                    '<td style="text-align: center;"><b><a href="#" class="copiarOfertaPrecio" data-id="' . $ofertasproducto[$i]['idproducto'] . '" data-precio="' . $ofertasproducto[$i]['precio'] . '">[ Copiar ]</a></b></td>' .
                    '<td style="text-align: center;">US $ ' . $ofertasproducto[$i]['preciodolares'] . '</td>' .
                    '<td style="text-align: center;"><b><a href="#" class="copiarOfertaPrecio" data-id="' . $ofertasproducto[$i]['idproducto'] . '" data-precio="' . $ofertasproducto[$i]['preciodolares'] . '">[ Copiar ]</a></b></td>' .
                    '<td>' . $ofertasproducto[$i]['descripcion'] . '</td>' .
                    '</tr>';
            }
        }
        $dataRespuesta['ofertas'] = $tempOfertas;
        $dataRespuesta['codigo'] = $data[0]['codigopa'];
        $dataRespuesta['nompro'] = str_replace('"', '\"', $data[0]['nompro']);
        echo json_encode($dataRespuesta);
    }

    function buscarproducto_venta()
    {
        if (!empty($_REQUEST['idvalor'])) {
            $idProducto = $_REQUEST['idvalor'];
        } else {
            $idProducto = $_REQUEST['id'];
            $descuento = new Descuento();
            $dscto = $descuento->buscarxid($_REQUEST['parameters'][1]);
        }
        $producto = new Producto();
        $data = $producto->buscaProductoOrdenCompra($idProducto);
        $ofertamodel = $this->AutoLoadModel('oferta');
        $ofertasproducto = $ofertamodel->listaxIdproducto($idProducto);
        $tam = count($ofertasproducto);
        if ($tam > 0) {
            $archivoConfig = parse_ini_file("config.ini", true);
            $arrayTipocobro = $archivoConfig['TipoCobro'];
            $tempOfertas = '';
            for ($i = 0; $i < $tam; $i++) {
                $tempOfertas .= '<tr>' .
                    '<td style="text-align: center;">' . $arrayTipocobro[$ofertasproducto[$i]['tipocobro']] . '</td>' .
                    '<td style="text-align: center;">S/ ' . $ofertasproducto[$i]['precio'] . '</td>' .
                    '<td style="text-align: center;"><b><a href="#" class="copiarOfertaPrecio" data-id="' . $ofertasproducto[$i]['idproducto'] . '" data-precio="' . $ofertasproducto[$i]['precio'] . '">[ Copiar ]</a></b></td>' .
                    '<td style="text-align: center;">US $ ' . $ofertasproducto[$i]['preciodolares'] . '</td>' .
                    '<td style="text-align: center;"><b><a href="#" class="copiarOfertaPrecio" data-id="' . $ofertasproducto[$i]['idproducto'] . '" data-precio="' . $ofertasproducto[$i]['preciodolares'] . '">[ Copiar ]</a></b></td>' .
                    '<td>' . $ofertasproducto[$i]['descripcion'] . '</td>' .
                    '</tr>';
            }
            $dataRespuesta['ofertas'] = $tempOfertas;
        }
        $dataRespuesta['codigo'] = $data[0]['codigopa'];
        $dataRespuesta['nompro'] = str_replace('"', '\"', $data[0]['nompro']);
        $dataRespuesta['idproducto'] = $data[0]['idproducto'];
        $dataRespuesta['foto'] = (empty($data[0]['imagen']) ? "" : $data[0]['imagen']);
        $dataRespuesta['descuentosolicitado'] = (empty($dscto[0]['dunico']) ? "0" : $dscto[0]['dunico']);
        $dataRespuesta['descuentovalor'] = (empty($dscto[0]['valor']) ? "" : $dscto[0]['valor']);
        $dataRespuesta['fob'] = (empty($data[0]['fob']) ? "" : $data[0]['fob']);
        $dataRespuesta['preciolista'] = (empty($data[0]['preciolista']) ? "" : $data[0]['preciolista']);
        $dataRespuesta['preciolistadolares'] = (empty($data[0]['preciolistadolares']) ? "" : $data[0]['preciolistadolares']);
        $dataRespuesta['preciocosto'] = (empty($data[0]['preciocosto']) ? "" : $data[0]['preciocosto']);
        $dataRespuesta['stockactual'] = (empty($data[0]['stockdisponible']) ? "" : $data[0]['stockdisponible']);
        echo json_encode($dataRespuesta);
    }

    function buscar()
    {
        if (!empty($_REQUEST['idvalor'])) {
            $idProducto = $_REQUEST['idvalor'];
        } else {
            $idProducto = $_REQUEST['id'];
            $descuento = new Descuento();
            $dscto = $descuento->buscarxid($_REQUEST['parameters'][1]);
        }
        $marca = $this->AutoLoadModel('marca');
        $unidad = $this->AutoLoadModel('unidadmedida');
        $almacen = $this->AutoLoadModel('almacen');
        $producto = new Producto();
        $data = $producto->buscaProductoOrdenCompra($idProducto);
        

        if ($data[0]['unidadmedida']) {
            $unidadMedida = $unidad->buscaUnidadMedida($data[0]['unidadmedida']);
        }
        if ($data[0]['idalmacen']) {
            $dataAlmacen = $almacen->buscaAlmacen($data[0]['idalmacen']);
        }
        
        $empaque = $this->empaque();
        $dataMarca = $marca->listado();
        for ($i = 0; $i < count($dataMarca); $i++) {
            if (!empty($data[0]['idmarca']) && $data[0]['idmarca'] == $dataMarca[$i]['idmarca']) {
                $data[0]['idmarca'] = $dataMarca[$i]['nombre'];
            }
        }
        /*echo '{
          "codigo":"'.$data[0]['codigopa'].'",
          "idproducto":"'.$data[0]['idproducto'].'",
          "foto":"'.$data[0]['imagen'].'",
          "nompro":"'.str_replace('"', '\"', $data[0]['nompro']).'",
          "nomemp":"'.$empaque[($data[0]['empaque'])].'",
          "marca":"'.$data[0]['idmarca'].'",
          "nomum":"'.$unidadMedida[($data[0]['unidadmedida'])].'",
          "fob":"'.$data[0]['fob'].'",
          "preciolista":"'.$data[0]['preciolista'].'",
          "descuentosolicitado":"'.$dscto[0]['dunico'].'",
          "descuentovalor":"'.$dscto[0]['valor'].'",
          "stockactual":"'.$data[0]['stockdisponible'].'"
          }';*/
        //$dataJson['codigo']=$data[0]['codigopa'];
        
        $dataRespuesta['codigo'] = $data[0]['codigopa'];
        $dataRespuesta['nompro'] = str_replace('"', '\"', $data[0]['nompro']);
        $dataRespuesta['idproducto'] = $data[0]['idproducto'];
        $dataRespuesta['foto'] = (empty($data[0]['imagen']) ? "" : $data[0]['imagen']);
        $dataRespuesta['marca'] = (empty($data[0]['idmarca']) ? "" : $data[0]['idmarca']);
        $dataRespuesta['nomum'] = (empty($unidadMedida[0]['codigo']) ? "" : $unidadMedida[0]['codigo']);
        $dataRespuesta['codigoalmacen'] = (empty($dataAlmacen[0]['codigoalmacen']) ? "" : $dataAlmacen[0]['codigoalmacen']);
        $dataRespuesta['fob'] = (empty($data[0]['fob']) ? "" : $data[0]['fob']);
        $dataRespuesta['preciolista'] = (empty($data[0]['preciolista']) ? "" : $data[0]['preciolista']);
        $dataRespuesta['preciolistadolares'] = (empty($data[0]['preciolistadolares']) ? "" : $data[0]['preciolistadolares']);
        $dataRespuesta['preciocosto'] = (empty($data[0]['preciocosto']) ? "" : $data[0]['preciocosto']);
        $dataRespuesta['descuentosolicitado'] = (empty($dscto[0]['dunico']) ? "0" : $dscto[0]['dunico']);
        $dataRespuesta['descuentovalor'] = (empty($dscto[0]['valor']) ? "" : $dscto[0]['valor']);
        $dataRespuesta['stockactual'] = (empty($data[0]['stockdisponible']) ? "" : $data[0]['stockdisponible']);
        echo json_encode($dataRespuesta);
    }
    function buscarCodigo()
    {
        $codigoProducto = $_REQUEST['id'];
        $codigoProducto = html_entity_decode($codigoProducto, ENT_QUOTES, 'UTF-8');
        $producto = new Producto();
        $data = $producto->buscaxcodigo($codigoProducto);
        $dataR['codigo'] = $data[0]['codigopa'];
        echo json_encode($dataR);
        //echo '{"codigo":"'.$data[0]['codigopa'].'"}';
    }

    function contar()
    {
        $cod = $_REQUEST['codPro'];
        $producto = new Producto();
        $cant = $producto->contarProducto($cod);
        echo '{"cant":"' . $cant . '"}';
    }
    function existe()
    {
        $codigoProducto = $_REQUEST['id'];
        $producto = new Producto();
        $existe = $producto->existeProducto($codigoProducto);
        echo '{"existe":"' . $existe . '"}';
    }
    function buscarAutocomplete()
    {
        $texIni = $_REQUEST['term'];
        $idLinea = $_REQUEST['idlinea'];
        $producto = new Producto();
        if ($_REQUEST['idlinea']) {
            $data = $producto->buscaProductoAutocomplete($texIni, $idLinea);
        } else {
            $data = $producto->buscaProductoAutocomplete($texIni);
        }
        echo json_encode($data);
    }

    function buscarAutocompleteRepuesto()
    {
        $texIni = $_REQUEST['term'];
        $idLinea = $_REQUEST['idlinea'];
        $producto = new Producto();
        if ($_REQUEST['idlinea']) {
            $data = $producto->buscaProductoAutocompleteRepuesto($texIni, $idLinea);
        } else {
            $data = $producto->buscaProductoAutocompleteRepuesto($texIni);
        }
        echo json_encode($data);
        //var_dump($data);
    }

    function buscarAutocompletej()
    {
        $texIni = $_REQUEST['term'];
        $idLinea = $_REQUEST['idlinea'];
        $producto = new Producto();
        if ($_REQUEST['idlinea']) {
            $data = $producto->buscaProductoAutocompletej($texIni, $idLinea);
        } else {
            $data = $producto->buscaProductoAutocompletej($texIni);
        }
        echo json_encode($data);
    }

    function buscarAutocompleteLimpio()
    {
        $texIni = $_REQUEST['term'];
        $idLinea = $_REQUEST['idlinea'];
        $producto = new Producto();
        if ($_REQUEST['idlinea']) {
            $data = $producto->buscaProductoAutocompleteLimpio($texIni, $idLinea);
        } else {
            $data = $producto->buscaProductoAutocompleteLimpio($texIni);
        }
        echo json_encode($data);
    }

    function buscarAutocompleteLimpioRep()
    {
        $texIni = $_REQUEST['term'];
        $idLinea = $_REQUEST['idlinea'];
        $producto = new Producto();
        if ($_REQUEST['idlinea']) {
            $data = $producto->buscaProductoAutocompleteLimpioRep($texIni, $idLinea);
        } else {
            $data = $producto->buscaProductoAutocompleteLimpioRep($texIni);
        }
        echo json_encode($data);
    }

    function buscarAutocompleteCompras()
    {
        $texIni = $_REQUEST['term'];
        $idLinea = $_REQUEST['idlinea'];
        $producto = new Producto();
        if ($_REQUEST['idlinea']) {
            $data = $producto->buscaProductoAutocompleteCompras($texIni, $idLinea);
        } else {
            $data = $producto->buscaProductoAutocompleteCompras($texIni);
        }
        echo json_encode($data);
    }

    function nuevo()
    {
        $almacen = new Almacen();
        
        $linea = new Linea();
        $proveedor = new Proveedor();
        $sublinea = new sublinea();
        $marca = new marca();
        $empaque = $this->AutoLoadModel('empaque');
        $unidadmedida = $this->AutoLoadModel('unidadmedida');
        $data['Almacen'] = $almacen->listado();
        $data['Linea'] = $linea->listadoLineas();
        $data['sublinea'] = $sublinea->listaSublinea('idpadre!=0');
        $data['Empaque'] = $empaque->listarEmpaque();
        $data['Unidadmedida'] = $unidadmedida->listadoUnidadmedidas();
        $data['Proveedor'] = $proveedor->listadoProveedores();
        $data['marca'] = $marca->listado();
        $this->view->show("producto/nuevo.phtml", $data);
    }
    function graba()
    {
        $producto = new Producto();
        $pp = new Proveedorproducto();
        $movimiento = $this->AutoLoadModel('movimiento');
        $detallemovimiento = $this->AutoLoadModel('detallemovimiento');
        
        $data = $_REQUEST['Producto'];
        if ($data['idtipoproducto'] == 1) {
            $data['idlinea'] = '1116';
            $data['idalmacen'] = '8';
            $data['empaque'] = '1';
            $data['unidadmedida'] = '4';
            $data['idmarca'] = '7';
        }
        //var_dump($data);die();        
        $data2 = $_REQUEST['ProductoProveedor'];
        $data['imagen'] = $_FILES['foto']['name'];
        $data['estado'] = 1;
        $exito = $producto->grabaProducto($data);
        $data2['idProducto'] = $exito;
        
        if ($exito) {
            if (!empty($data['preciolista']) && !empty($data['preciocosto'])) {
                $dataMovimiento['conceptomovimiento'] = 1;
                $dataMovimiento['tipomovimiento'] = 3;
                $dataMovimiento['idtipooperacion'] = 7;
                $dataMovimiento['observaciones'] = 'Carga de Saldos Iniciales del Producto';
                $dataMovimiento['fechamovimiento'] = date('Y-m-d');

                $graba = $movimiento->grabaMovimiento($dataMovimiento);
                if ($graba) {
                    $dataDetalleMovimiento['idmovimiento'] = $graba;
                    $dataDetalleMovimiento['idproducto'] = $exito;
                    $dataDetalleMovimiento['stockactual'] = 0;
                    $dataDetalleMovimiento['stockdisponibledm'] = 0;
                    $dataDetalleMovimiento['cantidad'] = 0;
                    $dataDetalleMovimiento['preciovalorizado'] = $data['preciocosto'];
                    $dataDetalleMovimiento['pu'] = $data['preciocosto'];
                    $dataDetalleMovimiento['importe'] = ($data['preciocosto'] * $data['stockactual']);
                    $dataDetalleMovimiento['estado'] = 1;
                    $graba2 = $detallemovimiento->grabaDetalleMovimieto($dataDetalleMovimiento);
                }
            }
            $data3 = array_unique($_REQUEST['productospadres']);
            $indice = 0;
            foreach ($data3 as $prodpad) {
                if ($prodpad != "") {
                    $data4[$indice]['idproductorepuesto'] = $data2['idProducto'];
                    $data4[$indice]['idproducto'] = $prodpad;
                    $indice++;
                }
            }
            //var_dump($data3);var_dump($data4);
            foreach ($data4 as $dt) {
                $producto->grabaProductoPadre($dt);
            }
            $codigo = $producto->GeneraCodigo(); //die();
            $this->guardaImagenesFormulario($data['codigopa']);
            if ($data['idtipoproducto'] != "1") {
            $pp->grabaProveedorProducto($data2);
            }

            $ruta['ruta'] = "/producto/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }
    function editar()
    {
        $id = $_REQUEST['id'];
        $producto = new Producto();
        $almacen = new Almacen();
        $linea = new Linea();
        $sublinea = new Sublinea();
        $marca = new marca();
        $empaque = $this->AutoLoadModel('empaque');
        $unidadmedida = $this->AutoLoadModel('unidadmedida');
        $dataProducto = $producto->buscaProducto($id);
        $idLinea = $linea->buscaLineaPorSublinea($dataProducto[0]['idlinea']);
        $data['Producto'] = $producto->buscaProducto($id);
        $productopadre = $producto->buscaProductoPadre($id);
        foreach ($productopadre as $producpadre) {
            $dataproducpadre[] = ($producto->obteneridnombre($producpadre['idproducto']));
        } //var_dump($productopadre);var_dump($data);die();
        $data['Productospadre'] = $dataproducpadre;
        $data['Almacen'] = $almacen->listadoAlmacen();
        $data['Linea'] = $linea->listadoLineas();
        $data['Sublinea'] = $sublinea->listadoSublinea($idLinea);
        $data['Empaque'] = $empaque->listarEmpaque();
        $data['Unidadmedida'] = $unidadmedida->listadoTotal();
        $data['RutaImagen'] = $this->rutaImagenesProducto();
        $data['marca'] = $marca->listado();
        $this->view->show("/producto/editar.phtml", $data);
    }

    function actualiza()
    {
        $producto = new Producto();
        $id = $_REQUEST['idProducto'];
        $data = $_REQUEST['Producto'];
        $data3 = array_unique($_REQUEST['productospadres']);
        //var_dump($data);die();
        $producto->eliminaProductoPadre($id);
        $indice = 0;
        foreach ($data3 as $prodpad) {
            if ($prodpad != "") {
                $data4[$indice]['idproductorepuesto'] = $id;
                $data4[$indice]['idproducto'] = $prodpad;
                $indice++;
            }
        }
        //var_dump($data3);var_dump($data4);die();
        foreach ($data4 as $dt) {
            $producto->grabaProductoPadre($dt);
        }

        if (count($_FILES)) {
            $data['imagen'] = $_FILES['foto']['name'];
        }
        
        $this->guardaImagenesFormulario($data['codigopa']);
        $exito = $producto->actualizaProducto($data, $id);
        /* print_r($_FILES);
          echo '<pre>';
          print_r($data);
          echo 'exito= '.$exito .'</br>';
          echo $id;
          echo exit; */
        if ($exito) {
            $ruta['ruta'] = "/producto/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function elimina()
    {
        $id = $_REQUEST['id'];
        $producto = new Producto();
        $producto->eliminaProductoPadre($id);
        $exito = $producto->eliminaProducto($id);
        if ($exito) {
            $ruta['ruta'] = "/producto/lista/";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }
    function repuesto()
    {

        $this->view->show("/producto/repuesto.phtml");
    }
    function cantidadStock()
    {
        $idProducto = $_REQUEST['id'];
        $producto = new Producto();
        $data = $producto->buscaProducto($idProducto);
        echo '{"stockDisponible":"' . $data[0]['stockdisponible'] . '", "stockActual":"' . $data[0]['stockactual'] . '"}';
    }

    function cantidadStockFisico()
    {
        $idProducto = $_REQUEST['id'];
        $producto = new Producto();
        $data = $producto->buscaProducto($idProducto);
        echo '{"stockDisponible":"' . $data[0]['stockactual'] . '"}';
    }

    function busqueda()
    {
        $producto = new Producto();
        $Producto = $producto->listadoProductos();
        $objeto = $this->formatearparakui($Producto);
        header("Content-type: application/json");
        //echo "{\"data\":" .json_encode($objeto). "}";
        echo json_encode($objeto);
    }

    function autocomplete()
    {
        $producto = new Producto();
        $text = $_REQUEST['id'];
        $datos = $producto->autocomplete($text);
        echo json_encode($datos);
    }

    function busca()
    {
        $productos = $this->AutoLoadModel('producto');
        if (empty($_REQUEST['id'])) {
            $_REQUEST['id'] = 1;
        }
        session_start();
        $_SESSION['P_Producto'];
        if (!empty($_REQUEST['txtBusqueda'])) {
            $_SESSION['P_Producto'] = $_REQUEST['txtBusqueda'];
        }
        $parametro = $_SESSION['P_Producto'];
        $paginacion = $productos->paginadoProductosxnombre($parametro);
        $data['retorno'] = $this->limpiarString($parametro);
        $data['producto'] = $productos->listaProductosPaginadoxnombre($_REQUEST['id'], $parametro);
        $data['paginacion'] = $paginacion;
        $data['blockpaginas'] = round($paginacion / 10);
        $data['totregistros'] = count($productos->BuscarRegistrosxnombre($parametro));
        $this->view->show("/producto/busca.phtml", $data);
    }

    function validarCodigo()
    {
        $productos = $this->AutoLoadModel('producto');
        $codigo = $_POST['Producto'];
        $valorEncontrado = $productos->buscaxcodigo($codigo['codigopa']);
        if (empty($valorEncontrado) || $valorEncontrado = "") {
            $codigo['error'] = "Codigo Aceptado";
            $codigo['verificado'] = true;
            echo json_encode($codigo);
        } else {
            $codigo['verificado'] = false;
            $codigo['error'] = "El Codigo del Producto ya Existe";
            echo json_encode($codigo);
        }
    }

    function agregaStockdisponible()
    {
        $idproducto = $_REQUEST['idproducto'];
        $cantidad = $_REQUEST['cantidad'];
        $producto = $this->AutoLoadModel('producto');
        //recuperamos el stockdisponible
        $dataBusqueda = $producto->buscaProducto($idproducto);
        $stockdisponibleA = $dataBusqueda[0]['stockdisponible'];
        $nuevostockdisponible = $stockdisponibleA + $cantidad;
        $data['stockdisponible'] = $nuevostockdisponible;
        $exito = $producto->actualizaProducto($data, $idproducto);
        echo $exito;
    }

    function disminuyeStockdisponible()
    {
        $idproducto = $_REQUEST['idproducto'];
        $cantidad = $_REQUEST['cantidad'];
        $producto = $this->AutoLoadModel('producto');
        //recuperamos el stockdisponible
        $dataBusqueda = $producto->buscaProducto($idproducto);
        $stockdisponibleA = $dataBusqueda[0]['stockdisponible'];
        $nuevostockdisponible = $stockdisponibleA - $cantidad;
        $data['stockdisponible'] = $nuevostockdisponible;
        $exito = $producto->actualizaProducto($data, $idproducto);
        echo $exito;
    }

    function GenerarListaPrecios()
    {
        $compras = $this->AutoLoadModel("ordencompra");
    }

    /*
      Funcion: Permite actualizar la imagen de algun producto.
      Creado por: Fernando García Atúncar
      Fecha: 04.04.2013
      Descripcion: Se utilzia en Productos/ModificarImagen
     */

    function actualizarImagen()
    {
        $this->view->show("/producto/cambiarImagen.phtml", $data);
    }

    function creaSaldoInicial()
    {
        $movimiento = $this->AutoLoadModel('movimiento');
        $detallemovimiento = $this->AutoLoadModel('detallemovimiento');
        $producto = $this->AutoLoadModel('producto');
        $dataProducto = $producto->listadoProductos();
        $cantidadProducto = count($dataProducto);
        $cont = 0;
        $cont2 = 0;
        $dataMovimiento['conceptomovimiento'] = 1;
        $dataMovimiento['tipomovimiento'] = 3;
        $dataMovimiento['idtipooperacion'] = 7;
        $dataMovimiento['observaciones'] = 'Carga de Saldos Iniciales del Producto';
        $dataMovimiento['fechamovimiento'] = date('Y-m-d');
        $graba = $movimiento->grabaMovimiento($dataMovimiento);
        if ($graba) {
            for ($i = 0; $i < $cantidadProducto; $i++) {
                $data['stockdisponible'] = $dataProducto[$i]['stockactual'];
                $exito = $producto->actualizaProducto($data, $dataProducto[$i]['idproducto']);
                if ($exito) {
                    $dataDetalleMovimiento['idmovimiento'] = $graba;
                    $dataDetalleMovimiento['idproducto'] = $dataProducto[$i]['idproducto'];
                    $dataDetalleMovimiento['stockactual'] = $dataProducto[$i]['stockactual'];
                    $dataDetalleMovimiento['stockdisponibledm'] = $dataProducto[$i]['stockdisponible'];
                    $dataDetalleMovimiento['cantidad'] = $dataProducto[$i]['stockactual'];
                    $dataDetalleMovimiento['preciovalorizado'] = $dataProducto[$i]['preciocosto'];
                    $dataDetalleMovimiento['pu'] = $dataProducto[$i]['preciocosto'];
                    $dataDetalleMovimiento['importe'] = ($dataProducto[$i]['preciocosto'] * $dataProducto[$i]['stockactual']);
                    $dataDetalleMovimiento['estado'] = 1;

                    $graba2 = $detallemovimiento->grabaDetalleMovimieto($dataDetalleMovimiento);
                    if (!$graba2) {
                        $cont2++;
                    }
                } else {
                    $cont++;
                }
            }
        }
        if (!$graba) {
            echo 'No se grabo Nada';
        } else {
            echo 'Errores en Actualizar Productos: ' . $cont;
            echo '<br>';
            echo 'Errores en Grabar Detalles de Movimiento: ' . $cont2;
        }
    }

    function precioreferencia()
    {
        $this->view->show('/producto/precioReferencia.phtml', $data);
    }

    function actualizaProductoJson()
    {
        $producto = $this->AutoLoadModel('producto');
        $id = $_REQUEST['idProducto'];
        $data['precioreferencia01'] = $_REQUEST['precioreferencia01'];
        $data['precioreferencia02'] = $_REQUEST['precioreferencia02'];
        $data['precioreferencia03'] = $_REQUEST['precioreferencia03'];
        $exito = $producto->actualizaProducto($data, $id);
        if ($exito) {
            $dataRespuesta['respuesta'] = true;
        } else {
            $dataRespuesta['respuesta'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function busqueda_productos()
    {
        $this->view->show('/producto/busqueda_productos.phtml', $data);
    }

    function productosAgotados()
    {
        $reporte = $this->AutoLoadModel('reporte');
        $idLinea = $_REQUEST['lstLinea'];
        $idSubLinea = $_REQUEST['lstSubLinea'];
        $idMarca = $_REQUEST['lstMarca'];
        $idAlmacen = $_REQUEST['lstAlmacen'];
        $idProducto = $_REQUEST['idProducto'];
        if (!empty($_REQUEST['fechaInicio'])) {
            $fechaInicio = date('Y-m-d', strtotime($_REQUEST['fechaInicio']));
        } else {
            $fechaInicio = $_REQUEST['fechaInicio'];
        }
        if (!empty($_REQUEST['fechaFinal'])) {
            $fechaFinal = date('Y-m-d', strtotime($_REQUEST['fechaFinal']));
        } else {
            $fechaFinal = $_REQUEST['fechaFinal'];
        }
        $dataBusqueda = $reporte->reporteProductoAgotados($idLinea, $idSubLinea, $idMarca, $idAlmacen, $idProducto, $fechaInicio, $fechaFinal);
        $cantidad = count($dataBusqueda);
        if ($cantidad > 0) {
            $fila = "<tr>";
            $fila .= "<th>Codigo</th>";
            $fila .= "<th>Nombre Producto</th>";
            $fila .= "<th>Nombre Almacen</th>";
            $fila .= "<th>Nombre Marca</th>";
            $fila .= "<th>Nombre SubLinea</th>";
            $fila .= "<th>Precio Lista</th>";
            $fila .= "<th>Fecha Agotada</th>";
            $fila .= "</tr>";
            for ($i = 0; $i < $cantidad; $i++) {
                $fila .= "<tr>";
                $fila .= "<td>" . $dataBusqueda[$i]['codigopa'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nompro'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['razsocalm'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nombre'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nomlin'] . "</td>";
                $fila .= "<td>S/." . number_format($dataBusqueda[$i]['preciolista'], 2) . "</td>";
                $fila .= "<td>" . date("Y-m-d", strtotime($dataBusqueda[$i]['fechaagotado'])) . "</td>";
                $fila .= "</tr>";
            }
        }
        echo $fila;
    }

    function productosAgotados2()
    {
        $reporte = $this->AutoLoadModel('reporte');
        $idLinea = $_REQUEST['lstLinea'];
        $idSubLinea = $_REQUEST['lstSubLinea'];
        $idMarca = $_REQUEST['lstMarca'];
        $idAlmacen = $_REQUEST['lstAlmacen'];
        $idProducto = $_REQUEST['idProducto'];
        if (!empty($_REQUEST['fechaInicio'])) {
            $fechaInicio = date('Y-m-d', strtotime($_REQUEST['fechaInicio']));
        } else {
            $fechaInicio = $_REQUEST['fechaInicio'];
        }
        if (!empty($_REQUEST['fechaFinal'])) {
            $fechaFinal = date('Y-m-d', strtotime($_REQUEST['fechaFinal']));
        } else {
            $fechaFinal = $_REQUEST['fechaFinal'];
        }
        $dataBusqueda = $reporte->reporteProductoAgotados2($idLinea, $idSubLinea, $idMarca, $idAlmacen, $idProducto, $fechaInicio, $fechaFinal);
        $cantidad = count($dataBusqueda);
        if ($cantidad > 0) {
            $fila = "<tr>";
            $fila .= "<th>Codigo</th>";
            $fila .= "<th>Nombre Producto</th>";
            $fila .= "<th>Nombre Almacen</th>";
            $fila .= "<th>Nombre Marca</th>";
            $fila .= "<th>Nombre SubLinea</th>";
            $fila .= "<th>Precio Lista</th>";
            $fila .= "<th>Fecha Agotada</th>";
            $fila .= "</tr>";
            for ($i = 0; $i < $cantidad; $i++) {
                $fila .= "<tr>";
                $fila .= "<td>" . $dataBusqueda[$i]['codigopa'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nompro'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['razsocalm'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nombre'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nomlin'] . "</td>";
                $fila .= "<td>S/." . number_format($dataBusqueda[$i]['preciolista'], 2) . "</td>";
                $fila .= "<td>" . date("Y-m-d", strtotime($dataBusqueda[$i]['fechaagotado'])) . "</td>";
                $fila .= "</tr>";
            }
        }
        echo $fila;
    }

    function productosVendidos()
    {
        $reporte = $this->AutoLoadModel('reporte');
        $idLinea = $_REQUEST['lstLinea'];
        $idSubLinea = $_REQUEST['lstSubLinea'];
        $idMarca = $_REQUEST['lstMarca'];
        $idAlmacen = $_REQUEST['lstAlmacen'];
        $fechaInicio = $_REQUEST['fechaInicio'];
        $fechaFinal = $_REQUEST['fechaFinal'];
        $idProducto = $_REQUEST['idProducto'];

        $dataBusqueda = $reporte->reporteProductoVendidos($idLinea, $idSubLinea, $idMarca, $idAlmacen, $idProducto, $fechaInicio, $fechaFinal);
        //print_r($dataBusqueda);
        //exit;
        $cantidad = count($dataBusqueda);
        if ($cantidad > 0) {
            $fila = "<tr>";
            $fila .= "<th>Codigo</th>";
            $fila .= "<th>Nombre Producto</th>";
            $fila .= "<th>Nombre Almacen</th>";
            $fila .= "<th>Nombre Marca</th>";
            $fila .= "<th>Nombre SubLinea</th>";
            $fila .= "<th>Precio Lista</th>";
            $fila .= "<th title='Stock Actual'>S. A.</th>";
            $fila .= "<th title='Stock Disponible'>S. Dis.</th>";
            $fila .= "<th title=''>C. Vendida</th>";
            $fila .= "</tr>";
            for ($i = 0; $i < $cantidad; $i++) {
                $fila .= "<tr>";
                $fila .= "<td>" . $dataBusqueda[$i]['codigopa'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nompro'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['razsocalm'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nombre'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['nomlin'] . "</td>";
                $fila .= "<td>S/." . number_format($dataBusqueda[$i]['preciolista'], 2) . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['stockactual'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['stockdisponible'] . "</td>";
                $fila .= "<td>" . $dataBusqueda[$i]['cantidadvendida'] . "</td>";
                $fila .= "</tr>";
            }
        }
        echo $fila;
    }

function valorizaxlinea()
    {
        $idFecha = $_REQUEST['txtFecha'];
        $dataProducto = array();
        if (!empty($idFecha)) {
            $producto = $this->AutoLoadModel("Producto");
            $dataProducto = $producto->ValorizadoxLinea($idFecha);
            $data['fechaseleccionada'] = date('Y/m/d', strtotime($idFecha));
        } else {
            $data['fechaseleccionada'] = date('Y/m/d');
        }
        //if ($idFecha == null) {
          /*  $producto = $this->AutoLoadModel("Producto");
            $dataProducto = $producto->ValorizadoxLinea(date('y-m-d'));*/
            
            $this->AutoLoadLib(array('GoogChart', 'GoogChart.class'));
            $data['datos'] = $dataProducto;
            $data['grafico'] = new GoogChart();
            $this->view->show("/producto/valorizadoxlinea.phtml", $data);
        /*} else {
            $producto = $this->AutoLoadModel("Producto");
            $dataProducto = $producto->ValorizadoxLinea($idFecha);
            $this->AutoLoadLib(array('GoogChart', 'GoogChart.class'));
            $grafico = new GoogChart();

            echo '<tr>';
            echo '<th colspan = "3">&nbsp;';
            echo '</th>';
            echo '</tr>';
            $tam = count($dataProducto);
            for ($j = 0; $j < $tam; $j++) {
                if ($dataProducto[$j]['valorizado'] <= 0) {
                    $dataProducto[$j]['valorizado'] = 0.00;
                }
                # code...
                $graf[$dataProducto[$j]['nomlin']] = $dataProducto[$j]['valorizado'];
                $total += $dataProducto[$j]['valorizado'];
            }
            for ($i = 0; $i < $tam; $i++) {
                echo '<tr>';
                echo '<td>' . $dataProducto[$i]['nomlin'] . '</td>';
                echo '<td>' . " " . number_format($dataProducto[$i]['valorizado'], 2) . "\n" . '</td>';
                echo '<td rowspan="' . $tam . '" align="right">';

                if ($i == 0) {
                    $color = array('#ccff00', '#7498e9', '#000faa',);
                    $date = date('\a \l\a\s g:i a \d\e\l d.m.Y ');
                    $grafico->setChartAttrs(
                        array(
                            'type' => 'pie',
                            //'title' => 'VALORIZADO POR LINEA al: '.$fecha, 
                            'title' => 'VALORIZADO POR CIF VENTAS ' . $date,
                            'data' => $graf,
                            'size' => array(550, 300),
                            'color' => $color
                        )
                    );
                    echo $grafico;
                }
                echo '</td></tr>';
            }
            echo '<tr>';
            echo '<td colspan="2"><hr></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td>&nbsp;</td>';
            echo '<td>US $ ' . number_format($total, 2) . '</td>';
            echo '</tr>';
        }*/
    } 
    

    function durezaproducto()
    {
        $idLinea = $_REQUEST['txtidLinea'];
        $idSubLinea = $_REQUEST['txtidSubLinea'];
        $idProducto = $_REQUEST['txtidproducto'];
        $formato = $_REQUEST['formato'];
        $fecha = $_REQUEST['fecha'];
        $stockactual = $_REQUEST['stockactual'];
        if (empty($fecha))
            $fecha = date('Y-m-d');
        $repote = new Reporte();
        $producto = new Producto();
        $data = $repote->reporteProductoDureza($idLinea, $idSubLinea, $idProducto, $stockactual);
        $data3 = array();
        for ($i = 0; $i < count($data); $i++) {
            $data2 = $producto->durezaProducto($data[$i]['idproducto'], 1);
            if (!empty($data2[0]['codigopa'])) {
                $datetime1 = new DateTime($fecha);
                $datetime2 = new DateTime($data2[0]['fecha']);
                $interval = $datetime1->diff($datetime2);
                if ($data2[0]['stockactual'] > 0) {
                    $PVM = ($data2[0]['stockinicial'] - $data2[0]['stockactual']) / (($interval->y * 12) + ($interval->m * 30) + $interval->d);
                    $PVM = $PVM * 30;
                    if ($PVM == 0)
                        $PVM = 1;
                    $dureza = $data2[0]['stockactual'] / $PVM;
                } else {
                    $PVM = 0;
                    $dureza = 0;
                }
                if ($formato == 1) {
                    $data3[$i]['codigo'] = $data2[0]['codigopa'];
                    $data3[$i]['nompro'] = $data2[0]['nompro'];
                    $data3[$i]['nomlin'] = $data2[0]['linea'];
                    $data3[$i]['nomsublin'] = $data2[0]['sublinea'];
                    $data3[$i]['unidadmedida'] = $data2[0]['nommedida'];
                    $data3[$i]['llegada'] = $data2[0]['fecha'];
                    $data3[$i]['inicial'] = $data2[0]['stockinicial'];
                    $data3[$i]['disponible'] = $data2[0]['stockactual']; //
                    $data3[$i]['pvm'] = number_format($PVM, 2);
                    $data3[$i]['dureza'] = number_format($dureza, 2);
                } else {
                    echo "<tr>";
                    echo "<td>" . $data2[0]['codigopa'] . "</td>";
                    echo "<td>" . $data2[0]['nompro'] . "</td>";
                    echo "<td>" . $data2[0]['linea'] . "</td>";
                    echo "<td>" . $data2[0]['sublinea'] . "</td>";
                    echo "<td>" . $data2[0]['nommedida'] . "</td>";
                    echo "<td>" . $data2[0]['fecha'] . "</td>";
                    echo "<td>" . $data2[0]['stockinicial'] . "</td>";
                    echo "<td>" . $data2[0]['stockactual'] . "</td>";
                    echo "<td>" . number_format($PVM, 2) . "</td>";
                    echo "<td>" . number_format($dureza, 2) . "</td>";
                    echo "<td><center><input type='checkbox' class='seleccionDureza' name='pseleccionPDF[]' value='" . $data[$i]['idproducto'] . "'></center></td>";
                    echo "</tr>";
                }
            } else {
                $data2 = $producto->durezaProducto($data[$i]['idproducto'], 2);
                if (!empty($data2[0]['codigopa']) && !empty($data2[0]['cif'])) {
                    if ($formato == 1) {
                        $data3[$i]['codigo'] = $data2[0]['codigopa'];
                        $data3[$i]['nompro'] = $data2[0]['nompro'];
                        $data3[$i]['nomlin'] = $data2[0]['linea'];
                        $data3[$i]['nomsublin'] = $data2[0]['sublinea'];
                        $data3[$i]['unidadmedida'] = $data2[0]['nommedida'];
                        $data3[$i]['llegada'] = '---';
                        $data3[$i]['inicial'] = '---';
                        $data3[$i]['disponible'] = $data2[0]['stockactual']; //
                        $data3[$i]['pvm'] = '---';
                        $data3[$i]['dureza'] = 'CIF: ' . $data2[0]['cif'];
                    } else {
                        echo "<tr style='background: #D8E8C6;'>";
                        echo "<td>" . $data2[0]['codigopa'] . "</td>";
                        echo "<td>" . $data2[0]['nompro'] . "</td>";
                        echo "<td>" . $data2[0]['linea'] . "</td>";
                        echo "<td>" . $data2[0]['sublinea'] . "</td>";
                        echo "<td>" . $data2[0]['nommedida'] . "</td>";
                        echo "<td colspan='2'><center>---</center></td>";
                        echo "<td>" . $data2[0]['stockactual'] . "</td>";
                        echo "<td colspan='2'><center><b>CIF: " . $data2[0]['cif'] . "</b></center></td>";
                        echo "<td><center><input type='checkbox' class='seleccionDureza' name='pseleccionPDF[]' value='" . $data[$i]['idproducto'] . "'></center></td>";
                        echo "</tr>";
                    }
                }
            }
        }
        if ($formato == 1) {
            $objeto = $this->formatearparakui($data3);
            header("Content-type: application/json");
            echo json_encode($objeto);
        }
    }
}
