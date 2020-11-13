<?php

class CredencialesController extends ApplicationGeneral
{

    function asignar()
    {
        $inventario = $this->AutoLoadModel('inventario');
        $bloques = $this->AutoLoadModel('bloques');
        $data['inventario'] = $inventario->listado();
        $data['bloques'] = $bloques->listado();

        $this->view->show('/Credenciales/asignarcredenciales.phtml', $data);
    }

    function buscarAutocompleteUrlModulo()
    {
        $get_cadena = $_REQUEST['term'];
        $credenciales = new Credenciales();
        $data = $credenciales->buscarAutocompleteUrlModulo($get_cadena);
        echo json_encode($data);
    }

    function grillaAsignacionCredenciales()
    {
        $urlmodulo = $_REQUEST['txtModulo1'];
        $idactor = $_REQUEST['txtIdActor1'];
        $idmodulo = $_REQUEST['txtIdModulo1'];

        $credenciales = new Credenciales();
        $listaCredenciales = $credenciales->listaCredenciales($urlmodulo, $idactor, $idmodulo);
        $listaCredencialesDesc = $credenciales->listaCredencialesDesc($idmodulo);

        foreach ($listaCredenciales as $v) {

            $urlmodulo = $v['urlmodulo'];
            $nombres = $v['nombres'];
            $idmodulo = $v['idmodulo'];
            $idactor = $v['idactor'];
            $c1 = $v['c1'];
            $c2 = $v['c2'];
            $c3 = $v['c3'];
            $c4 = $v['c4'];
            $c5 = $v['c5'];
            $c6 = $v['c6'];
            $c7 = $v['c7'];
            $c8 = $v['c8'];
            $c9 = $v['c9'];
            $c10 = $v['c10'];
            $c11 = $v['c11'];
            $c12 = $v['c12'];
            $c13 = $v['c13'];
            $c14 = $v['c14'];
            $c15 = $v['c15'];
        }
        if ($idactor == 0) {

        } else {

            $tabla = '<table id="tblProducto">
			<thead>
				<tr>
					<th>Usuario</th>
                    <th>url</th>
					<th>c1</th>
					<th>c2</th>
					<th>c3</th>
					<th>c4</th>
					<th>c5</th>
					<th>c6</th>
					<th>c7</th>
					<th>c8</th>
					<th>c9</th>
					<th>c10</th>
					<th>c11</th>
					<th>c12</th>
					<th>c13</th>
					<th>c14</th>
					<th>todo</th>
				</tr>
                <tr style="text-align:center !important;">';
            $tabla .= '<td>' . $nombres . ' <input type="hidden" id="grillaModulo" value="' . $idmodulo . '"> <input type="hidden" id="grillaUsuario" value="' . $idactor . '"></td>';
            $tabla .= '<td>' . $urlmodulo . '</td>';
            if ($c1 == 0) {
                $checked_c1 = '';
            } else {
                $checked_c1 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c1 . ' class="asignarCredencial" id="c1" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c2 == 0) {
                $checked_c2 = '';
            } else {
                $checked_c2 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c2 . ' class="asignarCredencial" id="c2" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c3 == 0) {
                $checked_c3 = '';
            } else {
                $checked_c3 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c3 . ' class="asignarCredencial" id="c3" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c4 == 0) {
                $checked_c4 = '';
            } else {
                $checked_c4 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c4 . ' class="asignarCredencial" id="c4" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c5 == 0) {
                $checked_c5 = '';
            } else {
                $checked_c5 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c5 . ' class="asignarCredencial" id="c5" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c6 == 0) {
                $checked_c6 = '';
            } else {
                $checked_c6 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c6 . ' class="asignarCredencial" id="c6" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c7 == 0) {
                $checked_c7 = '';
            } else {
                $checked_c7 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c7 . ' class="asignarCredencial" id="c7" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c8 == 0) {
                $checked_c8 = '';
            } else {
                $checked_c8 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c8 . ' class="asignarCredencial" id="c8" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c9 == 0) {
                $checked_c9 = '';
            } else {
                $checked_c9 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c9 . ' class="asignarCredencial" id="c9"  x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c10 == 0) {
                $checked_c10 = '';
            } else {
                $checked_c10 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c10 . ' class="asignarCredencial" id="c10" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c11 == 0) {
                $checked_c11 = '';
            } else {
                $checked_c11 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c11 . ' class="asignarCredencial" id="c11" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c12 == 0) {
                $checked_c12 = '';
            } else {
                $checked_c12 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c12 . ' class="asignarCredencial" id="c12" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c13 == 0) {
                $checked_c13 = '';
            } else {
                $checked_c13 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c13 . ' class="asignarCredencial" id="c13" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c14 == 0) {
                $checked_c14 = '';
            } else {
                $checked_c14 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c14 . ' class="asignarCredencial" id="c14" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            if ($c15 == 0) {
                $checked_c15 = '';
            } else {
                $checked_c15 = 'checked="true"';
            }
            $tabla .= '<td><input type="checkbox" ' . $checked_c15 . ' class="asignarCredencial" id="c15" x1=' . $idmodulo . ' x2=' . $idactor . '></td>';

            $tabla .= '</tr>
			</thead>

		</table>';
        }


        $tabla .= '<br><center><table id="tblLeyenda"  style="width:600px !important;">
			<thead>
				<tr >
					<th colspan="8" style="font-weigh:800;font-size:20px !important;">LEYENDA DEL PROGRAMA <span style="margin-left: 50px;position: absolute;"><input type="checkbox" id="chkONOFF" checked="true"/><span style="color:red !important;" id="labelONOFF">OFF</span></span></th>
                </tr>
                <tr style="text-align:center !important;">
                    <td colspan="4" style="font-weigh:800 !important;font-size:11px !important;">idmodulo=' . $idmodulo . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $urlmodulo . '    /</td>

			    </tr>
                <tr style="text-align:center !important;">
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C1</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas" id="desc1" value="' . $listaCredencialesDesc[0]['desc1'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc1"></a> </td>
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C8</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc8" value="' . $listaCredencialesDesc[0]['desc8'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc8"></a> </td>
			    </tr>
                <tr style="text-align:center !important;">
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C2</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc2" value="' . $listaCredencialesDesc[0]['desc2'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc2"></a> </td>
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C9</td>
					<td><input disabled type"text" style="text-align:right;"" class="cajas"  id="desc9" value="' . $listaCredencialesDesc[0]['desc9'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc9"></a> </td>
			    </tr>
                <tr style="text-align:center !important;">
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C3</td>
         			<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc3" value="' . $listaCredencialesDesc[0]['desc3'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc3"></a> </td>
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C10</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc10" value="' . $listaCredencialesDesc[0]['desc10'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc10"></a> </td>
			    </tr>
                <tr style="text-align:center !important;">
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C4</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas" id="desc4" value="' . $listaCredencialesDesc[0]['desc4'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc4"></a> </td>
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C11</td>
					<td><input disabled type"text" style="text-align:right;"  class="cajas"  id="desc11" value="' . $listaCredencialesDesc[0]['desc11'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc11"></a> </td>
			    </tr>
                <tr style="text-align:center !important;">
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C5</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc5" value="' . $listaCredencialesDesc[0]['desc5'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc5"></a> </td>
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C12</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc12" value="' . $listaCredencialesDesc[0]['desc12'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc12"></a> </td>
                </tr>
                <tr style="text-align:center !important;">
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C6</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc6" value="' . $listaCredencialesDesc[0]['desc6'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc6"></a> </td>
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C13</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc13" value="' . $listaCredencialesDesc[0]['desc13'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc13"></a> </td>
			    </tr>
                <tr style="text-align:center !important;">
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C7</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas"  id="desc7" value="' . $listaCredencialesDesc[0]['desc7'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc7"></a> </td>
                    <td  style="font-weigh:800 !important;font-size:18px !important;">C14</td>
					<td><input disabled type"text" style="text-align:right;" class="cajas" id="desc14" value="' . $listaCredencialesDesc[0]['desc14'] . '" val="' . $idmodulo . '"> <a href="#" title="Guardar"><img style="margin-bottom:-5px !important;" width="20" height="20" src="/imagenes/grabar.gif" class="credencialesdesc" name="desc14"></a> </td>
                </tr>

			</thead>
		</table></center>';
        echo $tabla;
    }

    function asignarCredenciales()
    {
        $cx = $_REQUEST['cx'];
        $valor = $_REQUEST['cx_valor'];
        $idactor = $_REQUEST['grillausuario'];
        $idmodulo = $_REQUEST['grillamodulo'];

        $credenciales = new Credenciales();
        $consultarExistencia = $credenciales->consultarExistencia($idactor, $idmodulo);
        if ($consultarExistencia == 1) {
            $data["$cx"] = $valor;
            $grabacredencial = $credenciales->actualizarCredenciales($data, $idactor, $idmodulo);
        }
        if ($consultarExistencia == 0) {
            $data["$cx"] = $valor;
            $data["idactor"] = $idactor;
            $data["idmodulo"] = $idmodulo;
            $grabacredencial = $credenciales->insertarCredenciales($data);
        }


        if ($grabacredencial) {
            $dataRespuesta['exito'] = true;
        } else {
            $dataRespuesta['exito'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function asignarCredencialesDesc()
    {
        $descx = $_REQUEST['descx'];
        $valor = $_REQUEST['descx_valor'];
        $idmodulo = $_REQUEST['descx_idmodulo'];

        $credenciales = new Credenciales();
        $consultarExistenciaDesc = $credenciales->consultarExistenciaDesc($idmodulo);
        if ($consultarExistenciaDesc == 1) {
            $data["$descx"] = $valor;
            $grabacredencialdesc = $credenciales->actualizarCredencialesDesc($data, $idmodulo);
        }
        if ($consultarExistenciaDesc == 0) {
            $data["$descx"] = $valor;
            $data["idmodulo"] = $idmodulo;
            $grabacredencialdesc = $credenciales->insertarCredencialesDesc($data);
        }


        if ($grabacredencialdesc) {
            $dataRespuesta['exito'] = true;
        } else {
            $dataRespuesta['exito'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function traspasarcredenciales()
    {
        $this->view->show('/Credenciales/traspasarcredenciales.phtml', $data);
    }

    function listarResumenCredenciales()
    {
        $url_idactor_origen = $_REQUEST['txtidActor1'];
        $credenciales = new Credenciales();
        $v = $credenciales->listarResumenCredenciales($url_idactor_origen);
        $idmoduloTemp = "";
        for ($i = 0; $i < count($v); $i++) {
            if ($v[$i]['finta'] == 0) {
                if ($idmoduloTemp != $v[$i]['idmodulobusqueda']) {
                    $listarNombreModulos = $credenciales->listarNombreModulos($v[$i]['idmodulobusqueda']);
                    $nombreModulo = $listarNombreModulos[0]['nombre'];
                    $consultado = 1;
                } else {
                    $consultado = 0;
                }

                $cont = $cont + 1;
                if ($cont == 1) {
                    echo "<h1>El ''usuario origen'' SOLO tiene restricciones en el listado arrojado</h1>";
                    echo "<h4>El ''usuario origen'' tiene permiso a todos los botones de los programas que NO FIGURAN EN ESTE LISTADO</h4>";
                    echo "<h4 style='color:#029900;'>Si desea agregar mas restricciones al ''usuario origen'' IR AL MODULO [Asignar credenciales]</h4>";
                }
                $tabla = "";
                if ($consultado == 1) {
                    $tabla .= "</table><table>";
                    $tabla .= "<tr><th>" . $nombreModulo . "</th></tr>";
                }

                $tabla .= "<tr><th colspan='4'>" . $v[$i]['nombre'] . "</th><th colspan='4' style='text-align:left;'>" . $v[$i]['url'] . "</tr>";
                $tabla .= "<tr><td>" . (!empty($v[$i]['desc1']) ? $v[$i]['desc1'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc2']) ? $v[$i]['desc2'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc3']) ? $v[$i]['desc3'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc4']) ? $v[$i]['desc4'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc5']) ? $v[$i]['desc5'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc6']) ? $v[$i]['desc6'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc7']) ? $v[$i]['desc7'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc8']) ? $v[$i]['desc8'] : 'boton ?') . "</td></tr>";
                $tabla .= "<tr><td>" . (($v[$i]['c1'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c2'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c3'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c4'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c5'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c6'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c7'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c8'] == '1') ? 'si' : 'no') . "</td></tr>";
                $tabla .= "<tr><td>" . (!empty($v[$i]['desc9']) ? $v[$i]['desc9'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc10']) ? $v[$i]['desc10'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc11']) ? $v[$i]['desc11'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc12']) ? $v[$i]['desc12'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc13']) ? $v[$i]['desc13'] : 'boton ?') . "</td><td>" . (!empty($v[$i]['desc14']) ? $v[$i]['desc14'] : 'boton ?') . "</td><td colspan='2' style='font-weight:600;'>BOTONES ACTIVOS</td></tr>";
                $tabla .= "<tr><td>" . (($v[$i]['c9'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c10'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c11'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c12'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c13'] == '1') ? 'si' : 'no') . "</td><td>" . (($v[$i]['c14'] == '1') ? 'si' : 'no') . "</td><td colspan='2' style='font-weight:600;'>" . (($v[$i]['c15'] == '1') ? 'TODOS' : 'segun lo marcado') . "</tr>";
                $idmoduloTemp = $v[$i]['idmodulobusqueda'];
                echo $tabla;
            }
        }
        $listarNombreModulos = '';
        $nombreModulo = '';
        if ($cont == 0) {
            echo "<h1>El ''usuario origen'' no tiene restricciones en ningun programa</h1>";
        }
    }
     function volcadocredenciales(){
        $credenciales = new Credenciales();
        $volcadocredenciales=$credenciales->volcadocredenciales('volcadototal',$_REQUEST['txtIdActor1'],$_REQUEST['txtIdActor2']);
        $data['volcado']="1";
        $this->view->show('/Credenciales/traspasarcredenciales.phtml', $data);
     }
}

?>