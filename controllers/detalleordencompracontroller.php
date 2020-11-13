<?php

class DetalleOrdenCompraController extends ApplicationGeneral {

    function listar() {
        $idOrdenCompra = $_REQUEST['id'];
        $detalleOrdenCompra = new Detalleordencompra();
        $data['Detalleordencompra'] = $detalleOrdenCompra->listaDetalleOrdenCompra($idOrdenCompra);
    }

    function listTable() {
        $id = $_REQUEST['id'];
        $RutaImagen = $this->rutaImagenesProducto();
        $detalleOrdenCompra = new Detalleordencompra();
        $movimiento = new Movimiento();
        $data = $detalleOrdenCompra->listaDetalleOrdenCompra($id);
        $cantDetOrdCom = count($data);
        $archivoConfig = parse_ini_file("config.ini", true);
        for ($i = 0; $i < $cantDetOrdCom; $i++) {
            echo "<tr>";
            if ($i == ($cantDetOrdCom - 1)) {
                echo    '<input type="hidden" name="cantidadDetalles" value="' . $cantDetOrdCom . '">';
                echo    '<input type="hidden" name="vbimportaciones" value="' . $data[$i]['vbimportaciones'] . '">';
            }
            echo        '<input type="hidden" name="Detallemovimiento[' . ($i + 1) . '][iddetalleordencompra]" value="' . $data[$i]['iddetalleordencompra'] . '">';
            echo        '<input type="hidden" name="Detallemovimiento[' . ($i + 1) . '][idproducto]" value="' . $data[$i]['idproducto'] . '">';
            echo        '<input type="hidden" name="Detallemovimiento[' . ($i + 1) . '][preciocosto]" value="' . $data[$i]['totalunitario'] . '">';
            echo        '<input type="hidden" name="Producto[' . ($i + 1) . '][stockdisponible]" value="' . $data[$i]['stockdisponible'] . '">';
            echo        '<input type="hidden" name="Producto[' . ($i + 1) . '][precioactual]" value="' . $data[$i]['preciocosto'] . '">';
            echo        '<input type="hidden" value="1" name="Detallemovimiento[' . ($i + 1) . '][estado]" class="txtEstado">';
            echo    '<td>' . '<img src="' . $RutaImagen . $data[$i]['codigo'] . '/' . $data[$i]['imagen'] . '" width="50" height="40">' . '</td>';
            echo    "<td>" . $data[$i]['codigopa'] . "</td>";
            echo    "<td>" . $data[$i]['nomemp'] . "</td>";
            echo    "<td>" . $data[$i]['marca'] . "</td>";
            echo    '<td><input type="text" name="Detallemovimiento[' . ($i + 1) . '][cantidadsolicitadaoc]" value="' . $data[$i]['cantidadsolicitadaoc'] . '" readonly style="width:50px" class="txtCantidadSolicitada"></td>';
            echo    '<td><input type="text" name="Detallemovimiento[' . ($i + 1) . '][cantidadrecibidaoc]" value="' . $data[$i]['cantidadsolicitadaoc'] . '" readonly style="width:50px" class="txtCantidadRecibida required numeric"></td>';
            echo    "<td>" . $archivoConfig['UnidadMedida'][($data[$i]['unidadmedida'])] . "</td>";
            echo    '<td><input type="text" class="text-50 txtStockActual" name="Producto[' . ($i + 1) . '][stockactual]" value="' . $data[$i]['stockactual'] . '" readonly></td>';
            echo    '<td style="text-align:center"><input type="checkbox" value="1" class="chkVerificacion" checked></td>';
            echo    '<td><a href="#" class="btnQuitaDetalleOrdenCompra"><img src="/imagenes/eliminar.gif"></a></td>';
            echo "</tr>";
        }
    }

}

?>