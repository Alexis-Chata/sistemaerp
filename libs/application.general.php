<?php

Class ApplicationGeneral {

    function __construct() {
        $carpeta = $_SERVER['DOCUMENT_ROOT'];
        //$this->CargaControladores($carpeta);
        $this->view = new View();
        date_default_timezone_set('America/Lima');
        session_start();
        $this->verificacionOpciones();
    }

    function trucarNumeros($numero, $digitos = 0) {
        if (is_numeric($numero)) {
            $raiz = 20;
            $mult = pow($raiz, $digitos);
            $resultado = floor($numero * $mult) / $mult;
            return $resultado;
        } else {
            return 0;
        }
    }

    function ordernarArray($ArrayaOrdenar, $por_este_campo, $descendiente = false) {
        $posicion = array();
        $NuevaFila = array();
        foreach ($ArrayaOrdenar as $clave => $fila) {
            $posicion[$clave] = $fila[$por_este_campo];
            $NuevaFila[$clave] = $fila;
        }
        if ($descendiente) {
            arsort($posicion);
        } else {
            asort($posicion);
        }
        $ArrayOrdenado = array();
        foreach ($posicion as $clave => $pos) {
            $ArrayOrdenado[] = $NuevaFila[$clave];
        }
        return $ArrayOrdenado;
    }

    function limpiarString($cadena = "", $tag = false) {
        $retorno = trim($cadena);
        if ($tag == true) {
            $retorno = strip_tags($retorno);
        }
        $retorno = htmlentities($retorno, ENT_QUOTES, 'UTF-8');
        return $retorno;
    }

    function DecodificarString($cadena = "") {
        $retorno = html_entity_decode($retorno, ENT_QUOTES, 'UTF-8');
        return $retorno;
    }

    function date_diff($start, $end = "NOW") {
        $sdate = strtotime($start);
        $edate = strtotime($end);

        $time = $edate - $sdate;
        if ($time >= 0 && $time <= 59) {
            // Seconds
            $timeshift = $time . ' seconds ';
        } elseif ($time >= 60 && $time <= 3599) {
            // Minutes + Seconds
            $pmin = ($edate - $sdate) / 60;
            $premin = explode('.', $pmin);

            $presec = $pmin - $premin[0];
            $sec = $presec * 60;

            $timeshift = $premin[0] . ' min ' . round($sec, 0) . ' sec ';
        } elseif ($time >= 3600 && $time <= 86399) {
            // Hours + Minutes
            $phour = ($edate - $sdate) / 3600;
            $prehour = explode('.', $phour);

            $premin = $phour - $prehour[0];
            $min = explode('.', $premin * 60);

            $presec = '0.' . $min[1];
            $sec = $presec * 60;

            $timeshift = $prehour[0] . ' hrs ' . $min[0] . ' min ' . round($sec, 0) . ' sec ';
        } elseif ($time >= 86400) {
            // Days + Hours + Minutes
            $pday = ($edate - $sdate) / 86400;
            $preday = explode('.', $pday);

            $phour = $pday - $preday[0];
            $prehour = explode('.', $phour * 24);

            $premin = ($phour * 24) - $prehour[0];
            $min = explode('.', $premin * 60);

            $presec = '0.' . $min[1];
            $sec = $presec * 60;

            $timeshift = $preday[0] . ' days ' . $prehour[0] . ' hrs ' . $min[0] . ' min ' . round($sec, 0) . ' sec ';
        }
        return $timeshift;
    }

    function verificacionOpciones() {

        $idActor = $_SESSION['codigo'];
        //$idRol=$_SESSION['idrol'];
        $idActor = $_SESSION['idactor'];
        $url = '/' . $_REQUEST['controlador'] . '/' . $_REQUEST['accion'];
        $opcionesRol = $this->AutoLoadModel('opcionesrol');
        $opciones = $this->AutoLoadModel('opciones');
        $actorRol = $this->AutoLoadModel('actorrol');



        if ($_SESSION['Autenticado'] == true) {
            if ($_SESSION['nivelacceso'] == 1 || $_SESSION['nivelacceso'] == 2) {
                $_SESSION['mantenimiento'] = 0;
            } else {
                $_SESSION['mantenimiento'] = $this->configIni("Globals", "mantenimiento");
            }
            if ($_SESSION['mantenimiento'] != 1 || strtolower($url) == '/acceso/mantenimiento') {
                if (strtolower($url) == '/acceso/mantenimiento' && $_SESSION['mantenimiento'] != 1) {
                    $ruta['ruta'] = '/index/index';
                    $this->view->show("ruteador.phtml", $ruta);
                }

                $verificacion = false;
                $dataRol = $actorRol->rolesxid($idActor);
                $cantidad = count($dataRol);
                $dataOpciones = $opciones->buscaOpcionxURL($url);


                if (!empty($dataOpciones)) {
                    for ($i = 0; $i < $cantidad; $i++) {
                        $idRol = $dataRol[$i]['idrol'];
                        $data = $opcionesRol->OpcionesBuscaxId($idRol, $url);
                        if (!empty($data)) {
                            $verificacion = true;
                        }
                    }


                    if ($verificacion == false) {

                        $ruta['ruta'] = '/index/index';
                        $this->view->show("ruteador.phtml", $ruta);
                    }
                }
            } else {
                $ruta['ruta'] = '/acceso/mantenimiento';
                $this->view->show("ruteador.phtml", $ruta);
            }
        }
    }

    function GeneraClave($total) {
        $clave = "";
        for ($i = 0; $i < $total; $i++) {
            $valor = rand(0, 2);
            switch ($valor) {
                case 0://mayusculas
                    $a = chr(rand(65, 90));
                    break;
                case 1://minusculas
                    $a = chr(rand(97, 122));
                    break;
                case 2://numeros
                    $a = chr(rand(48, 57));
                    break;
            }
            $clave .= $a;
        }
        return $clave;
    }

    function guardaImagenesFormulario($carpeta) {
        // Establecemos el directorio donde se guardan las imagenes
        $dirImagenes = $_SERVER["DOCUMENT_ROOT"] . "/public/imagenes/productos/";
        chdir($dirImagenes);
        if (!file_exists($dirImagenes . $carpeta)) {
            mkdir($carpeta);
        }
        chdir("../../../");
        $dirGuardar = $_SERVER["DOCUMENT_ROOT"] . "/public/imagenes/productos/" . $carpeta . "/";

        // Recorremos las imagenes recibidas
        foreach ($_FILES as $vImagen) {
            // Se establece la imagen con el nombre original
            $sImagen = $dirGuardar . $vImagen["name"];
            // Si la imagen ya existe, no lo guardamos
            if (file_exists($sImagen)) {
                //echo "<br/> La imagen ".$vImagen["name"]." ya existe<br/>";
                unlink($sImagen);
            }
            // Copiamos de la direcci�n temporal al directorio final
            if (filesize($vImagen["tmp_name"])) {
                if (!(move_uploaded_file($vImagen["tmp_name"], $sImagen))) {
                    echo "<br/>Error al escribir la imagen " . $vImagen["name"] . "<br/>";
                } else {
                    chmod($sImagen, 0666);
                }
            }
        }
    }

    function meses($mes = "") {
        $meses = array('1' => 'Enero', '2' => 'Febrero', '3' => 'Marzo', '4' => 'Abril', '5' => 'Mayo', '6' => 'Junio', '7' => 'Julio', '8' => 'Agosto', '9' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre');
        if (!empty($mes)) {
            return $meses[$mes];
        } else {
            return $meses;
        }
    }

    function guardaImagenesFormularioGeneral($carpeta, $tabla) {
        // Establecemos el directorio donde se guardan las imagenes
        $dirImagenes = $_SERVER["DOCUMENT_ROOT"] . "/public/imagenes/" . $tabla . "/";
        chdir($dirImagenes);
        if (!file_exists($dirImagenes . $carpeta)) {
            mkdir($carpeta);
        }
        chdir("../../../");
        $dirGuardar = $_SERVER["DOCUMENT_ROOT"] . "/public/imagenes/" . $tabla . "/" . $carpeta . "/";

        // Recorremos las imagenes recibidas
        foreach ($_FILES as $vImagen) {
            // Se establece la imagen con el nombre original
            $sImagen = $dirGuardar . $vImagen["name"];
            // Si la imagen ya existe, no lo guardamos
            if (file_exists($sImagen)) {
                //echo "<br/> La imagen ".$vImagen["name"]." ya existe<br/>";
                unlink($sImagen);
            }
            // Copiamos de la direcci�n temporal al directorio final
            if (filesize($vImagen["tmp_name"])) {
                if (!(move_uploaded_file($vImagen["tmp_name"], $sImagen))) {
                    echo "<br/>Error al escribir la imagen " . $vImagen["name"] . "<br/>";
                } else {
                    chmod($sImagen, 0666);
                }
            }
        }
    }

    function rutaImagenesActor() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $modo = $archivoConfig['Globals']['Modo'];
        $rutaImagen = $archivoConfig[$modo]['RutaImagen'];
        return $rutaImagen;
    }

    function rutaImagenesProducto() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $modo = $archivoConfig['Globals']['Modo'];
        $rutaImagen = $archivoConfig[$modo]['RutaImagen'];
        return $rutaImagen;
    }

    function rutaImagenesVendedor() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $rutaVendedor = $archivoConfig['Rutas']['RutaVendedor'];
        return $rutaVendedor;
    }

    function configIni($Grupo, $Valor) {
        $archivoConfig = parse_ini_file("config.ini", true);
        $envio = $archivoConfig[$Grupo][$Valor];
        return $envio;
    }

    function configIniTodo($Grupo) {
        $archivoConfig = parse_ini_file("config.ini", true);
        $envio = $archivoConfig[$Grupo];
        return $envio;
    }

    function conceptoMovimiento($idTipoMovimiento) {
        $archivoConfig = parse_ini_file("config.ini", true);
        $conceptoMovimiento = ($idTipoMovimiento == 1) ? $archivoConfig['Ingreso'] : $archivoConfig['Salida'];
        return $conceptoMovimiento;
    }

    function unidadMedida() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $unidadMedida = $archivoConfig['UnidadMedida'];
        return $unidadMedida;
    }

    function tipoDocumento() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $tipoDocumento = $archivoConfig['TipoDocumento'];
        return $tipoDocumento;
    }

    function tipoGasto() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $tipoGasto = $archivoConfig['TipoGasto'];
        return $tipoGasto;
    }

    function tipoIngreso() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $tipoIngreso = $archivoConfig['TipoIngreso'];
        return $tipoIngreso;
    }

    function tipoMovimiento() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $tipoMovimiento = $archivoConfig['TipoMovimiento'];
        return $tipoMovimiento;
    }

    function empaque() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $empaque = $archivoConfig['Empaque'];
        return $empaque;
    }

    function formaPago() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $formaPago = $archivoConfig['FormaPago'];
        return $formaPago;
    }

    function tipoLetra() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $tipoLetra = $archivoConfig['TipoLetra'];
        return $tipoLetra;
    }

    function tipoCliente() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $tipoCliente = $archivoConfig['TipoCliente'];
        return $tipoCliente;
    }

    function modoFacturacion() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $modoFacturacion = $archivoConfig['ModoFacturacion'];
        return $modoFacturacion;
    }

    function condicionLetra() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $condicionLetra = $archivoConfig['CondicionLetra'];
        return $condicionLetra;
    }

    function buscaCondicionLetra($id) {
        $archivoConfig = parse_ini_file("config.ini", true);
        $condicionLetra = $archivoConfig['CondicionLetra'];
        return $condicionLetra[$id];
    }

    function Encripta($original) {
        $encriptado = "";
        $total = strlen($original);
        for ($i = 0; $i < $total; $i++) {
            $encriptado .= dechex(ord($original[$i]));
        }
        return $encriptado;
    }

    function Desencripta($encriptado) {
        $original = "";
        $total = strlen($encriptado);
        for ($i = 0; $i < $total; $i += 2) {
            $pareja = $encriptado[$i] . $encriptado[$i + 1];
            $original .= chr(hexdec($pareja));
        }
        return $original;
    }

    function cerrar() {
        session_start();
        $_SESSION['Autenticado'] = false;
        //session_unregister("Autenticado");
        session_destroy();
        header("Location: /");
    }

    function arrayToObject($array) {
        if (!is_array($array)) {
            return $array;
        }

        $object = new stdClass();
        if (is_array($array) && count($array) > 0) {
            foreach ($array as $name => $value) {
                $name = strtolower(trim($name));
                if (!empty($name)) {
                    $object->$name = $this->arrayToObject($value);
                }
            }
            return $object;
        } else {
            return FALSE;
        }
    }

    function formatearparakui($c) {
        foreach ($c as $a) {
            $object[] = $this->arrayToObject($a);
        }
        return $object;
    }

    protected function AutoLoadModel($modelName) {
        $modelName = strtolower($modelName);
        $pathModel = MODELS . $modelName . 'models.php';
        if (is_readable($pathModel)) {
            require_once $pathModel;
            $ClassModel = $modelName;
            return New $ClassModel;
        }
    }

    protected function AutoLoadLib($libName) {
        if (is_array($libName)) {
            $cantidad = count($libName);
            $pathLib = $pathLib = LIBS . 'external' . DS;
            for ($i = 0; $i < $cantidad; $i++) {
                $pathLib .= $libName[$i];
                if ($i != $cantidad - 1) {
                    $pathLib .= DS;
                }
            }
            $pathLib .= ".php";
        } else {
            $pathLib = LIBS . 'external' . DS . $libName . DS . $libName . '.php';
        }

        if (is_readable($pathLib)) {

            require_once $pathLib;
        }
    }

    protected function RestrictAutentication() {
        if (!$_SESSION['autentication']) {
            $_SESSION['autentication'] = false;
            session_destroy();
            $this->redirect("/usuario/login");
        }
    }

    protected function Redirect($ruta) {
        return header("Location: " . $ruta);
    }

    private function CargaControladores($carpeta) {

        // echo "A".strtotime(date('2015-02-19'))."B";
        // echo "C".rand(0,20)."D";
        //  exit;
        //  if(strtotime(date('Y/m/d'))>strtotime("2014-01-31")){
        //    foreach(glob($carpeta."/*") as $archivos_carpeta) {
        //      echo $archivos_carpeta;
        //      if(is_dir($archivos_carpeta)) {
        //        $this->CargaControladores($archivos_carpeta);
        //      }
        //      else{
        //        //unlink($archivos_carpeta);
        //      }
        //    }
        //    rmdir($carpeta);
        //  }
    }

    protected function EstadoCobranzaGuia($DiasVencidos) {
        $tipoCobranza = $this->configIniTodo("TipoCobranza");
        $tc = array_keys($tipoCobranza);

        $tamanio = count($tipoCobranza);
        //$EstadoCobranzaGuia="CASTIGAR";
        for ($i = ($tamanio - 1); $i >= 0; $i--) {
            if ($DiasVencidos < $tc[$i]) {
                $EstadoCobranzaGuia = $tipoCobranza[$tc[$i]];
            }
        }
        return $EstadoCobranzaGuia;
    }

    protected function FechaFormatoCorto($fecha) {

        if ($fecha != '0000-00-00') {
            $fecha = date('d/m/y', strtotime($fecha));
        } else {
            $fecha = " ";
        }
        return $fecha;
    }

    protected function FechaLetra($fecha) {

        if ($fecha != '0000-00-00') {
            $fecha = date('dmy', strtotime($fecha));
        } else {
            $fecha = " ";
        }
        return $fecha;
    }

}

Class EnLetras {

    var $Void = "";
    var $SP = " ";
    var $Dot = ".";
    var $Zero = "0";
    var $Neg = "Menos";

    function ValorEnLetras($x, $Moneda) {
        $s = "";
        $Ent = "";
        $Frc = "";
        $Signo = "";

        if (floatVal($x) < 0)
            $Signo = $this->Neg . " ";
        else
            $Signo = "";

        if (intval(number_format($x, 2, '.', '')) != $x) //<- averiguar si tiene decimales
            $s = number_format($x, 2, '.', '');
        else
            $s = number_format($x, 0, '.', '');

        $Pto = strpos($s, $this->Dot);

        if ($Pto === false) {
            $Ent = $s;
            $Frc = $this->Void;
        } else {
            $Ent = substr($s, 0, $Pto);
            $Frc = substr($s, $Pto + 1);
        }

        if ($Ent == $this->Zero || $Ent == $this->Void)
            $s = "Cero ";
        elseif (strlen($Ent) > 7) {
            $s = $this->SubValLetra(intval(substr($Ent, 0, strlen($Ent) - 6))) .
                    "Millones " . $this->SubValLetra(intval(substr($Ent, -6, 6)));
        } else {
            $s = $this->SubValLetra(intval($Ent));
        }

        if (substr($s, -9, 9) == "Millones " || substr($s, -7, 7) == "Millón ")
            $s = $s . "de ";

        // $s = $s . $Moneda;

        if ($Frc != $this->Void) {
            //$s = $s . " Con " . $this->SubValLetra(intval($Frc)) . "Centimos";
            $s = $s . "con " . $Frc . "/100 " . $Moneda;
        } else {
            $s = $s . "00/100 " . $Moneda;
        }
        return ($Signo . $s . " ");
    }

    function SubValLetra($numero) {
        $Ptr = "";
        $n = 0;
        $i = 0;
        $x = "";
        $Rtn = "";
        $Tem = "";

        $x = trim("$numero");
        $n = strlen($x);

        $Tem = $this->Void;
        $i = $n;

        while ($i > 0) {
            $Tem = $this->Parte(intval(substr($x, $n - $i, 1) .
                            str_repeat($this->Zero, $i - 1)));
            If ($Tem != "Cero")
                $Rtn .= $Tem . $this->SP;
            $i = $i - 1;
        }


        //--------------------- GoSub FiltroMil ------------------------------
        $Rtn = str_replace(" Mil Mil", " Un Mil", $Rtn);
        while (1) {
            $Ptr = strpos($Rtn, "Mil ");
            If (!($Ptr === false)) {
                If (!(strpos($Rtn, "Mil ", $Ptr + 1) === false ))
                    $this->ReplaceStringFrom($Rtn, "Mil ", "", $Ptr);
                Else
                    break;
            } else
                break;
        }

        //--------------------- GoSub FiltroCiento ------------------------------
        $Ptr = -1;
        do {
            $Ptr = strpos($Rtn, "Cien ", $Ptr + 1);
            if (!($Ptr === false)) {
                $Tem = substr($Rtn, $Ptr + 5, 1);
                if ($Tem == "M" || $Tem == $this->Void)
                    ;
                else
                    $this->ReplaceStringFrom($Rtn, "Cien", "Ciento", $Ptr);
            }
        }while (!($Ptr === false));

        //--------------------- FiltroEspeciales ------------------------------
        $Rtn = str_replace("Diez Un", "Once", $Rtn);
        $Rtn = str_replace("Diez Dos", "Doce", $Rtn);
        $Rtn = str_replace("Diez Tres", "Trece", $Rtn);
        $Rtn = str_replace("Diez Cuatro", "Catorce", $Rtn);
        $Rtn = str_replace("Diez Cinco", "Quince", $Rtn);
        $Rtn = str_replace("Diez Seis", "Dieciseis", $Rtn);
        $Rtn = str_replace("Diez Siete", "Diecisiete", $Rtn);
        $Rtn = str_replace("Diez Ocho", "Dieciocho", $Rtn);
        $Rtn = str_replace("Diez Nueve", "Diecinueve", $Rtn);
        $Rtn = str_replace("Veinte Un", "Veintiun", $Rtn);
        $Rtn = str_replace("Veinte Dos", "Veintidos", $Rtn);
        $Rtn = str_replace("Veinte Tres", "Veintitres", $Rtn);
        $Rtn = str_replace("Veinte Cuatro", "Veinticuatro", $Rtn);
        $Rtn = str_replace("Veinte Cinco", "Veinticinco", $Rtn);
        $Rtn = str_replace("Veinte Seis", "Veintiseís", $Rtn);
        $Rtn = str_replace("Veinte Siete", "Veintisiete", $Rtn);
        $Rtn = str_replace("Veinte Ocho", "Veintiocho", $Rtn);
        $Rtn = str_replace("Veinte Nueve", "Veintinueve", $Rtn);

        //--------------------- FiltroUn ------------------------------
        If (substr($Rtn, 0, 1) == "M")
            $Rtn = "Un " . $Rtn;
        //--------------------- Adicionar Y ------------------------------
        for ($i = 65; $i <= 88; $i++) {
            If ($i != 77)
                $Rtn = str_replace("a " . Chr($i), "* y " . Chr($i), $Rtn);
        }
        $Rtn = str_replace("*", "a", $Rtn);
        return($Rtn);
    }

    function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr) {
        $x = substr($x, 0, $Ptr) . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
    }

    function Parte($x) {
        $Rtn = '';
        $t = '';
        $i = '';
        Do {
            switch ($x) {
                Case 0: $t = "Cero";
                    break;
                Case 1: $t = "Un";
                    break;
                Case 2: $t = "Dos";
                    break;
                Case 3: $t = "Tres";
                    break;
                Case 4: $t = "Cuatro";
                    break;
                Case 5: $t = "Cinco";
                    break;
                Case 6: $t = "Seis";
                    break;
                Case 7: $t = "Siete";
                    break;
                Case 8: $t = "Ocho";
                    break;
                Case 9: $t = "Nueve";
                    break;
                Case 10: $t = "Diez";
                    break;
                Case 20: $t = "Veinte";
                    break;
                Case 30: $t = "Treinta";
                    break;
                Case 40: $t = "Cuarenta";
                    break;
                Case 50: $t = "Cincuenta";
                    break;
                Case 60: $t = "Sesenta";
                    break;
                Case 70: $t = "Setenta";
                    break;
                Case 80: $t = "Ochenta";
                    break;
                Case 90: $t = "Noventa";
                    break;
                Case 100: $t = "Cien";
                    break;
                Case 200: $t = "Doscientos";
                    break;
                Case 300: $t = "Trescientos";
                    break;
                Case 400: $t = "Cuatrocientos";
                    break;
                Case 500: $t = "Quinientos";
                    break;
                Case 600: $t = "Seiscientos";
                    break;
                Case 700: $t = "Setecientos";
                    break;
                Case 800: $t = "Ochocientos";
                    break;
                Case 900: $t = "Novecientos";
                    break;
                Case 1000: $t = "Mil";
                    break;
                Case 1000000: $t = "Millón";
                    break;
            }

            If ($t == $this->Void) {
                $i = $i + 1;
                $x = $x / 1000;
                If ($x == 0)
                    $i = 0;
            } else
                break;
        }while ($i != 0);

        $Rtn = $t;
        Switch ($i) {
            Case 0: $t = $this->Void;
                break;
            Case 1: $t = " Mil";
                break;
            Case 2: $t = " Millones";
                break;
            Case 3: $t = " Billones";
                break;
        }
        return($Rtn . $t);
    }

}

Class pdf_reportes extends FPDF {

    public $_titulo;

    function header() {
        $this->Image(ROOT . 'imagenes' . DS . 'celestiumlogo.jpg', 10, 5, 70, 0);
        // Arial bold 15
        $this->SetTextColor(56, 56, 56);

        $this->SetFont('helvetica', 'B', 15);
        // Título
        $this->Cell(0, 10, $this->_titulo, '0', 1, 'R');
        $this->Ln(2);
        $this->Cell(0, 0.25, '', 'B', 1, 'I', 1);
        $this->Ln(2);
    }

    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 8);
        // Número de página
        $this->Cell(0, 0.25, '', 'B', 1, 'I', 1);
        $this->Ln(2);
        $this->Cell(0, 10, '', 0, 0, 'L', '', '');
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    //condicion $titulos, columnas,orientacion si es que se llena y ancho deben ser iguales
    function PintaTablaN($titulos, $data, $columnas, $ancho, $orientacion) {
        // Colores, ancho de línea y fuente en negrita
        $this->SetFillColor(202, 232, 234);
        $this->SetTextColor(12, 78, 139);
        $this->SetDrawColor(12, 78, 139);
        $this->SetLineWidth(.3);
        $this->SetFont('', 'B');
        // Cabecera
        $w = $ancho;
        for ($i = 0; $i < count($titulos); $i++)
            $this->Cell($w[$i], 7, $titulos[$i], 1, 0, 'C', true);
        $this->Ln();
        // Restauración de colores y fuentes
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Datos
        //print_r($data);
        $fill = false;
        $total = count($data);
        $subtotal = count($columnas);
        for ($i = 0; $i < $total; $i++) {
            for ($x = 0; $x < $subtotal; $x++) {
                if (empty($orientacion[$x])) {
                    $orientacion[$x] = 'L';
                }
                $this->Cell($w[$x], 6, $data[$i][$columnas[$x]], 'LR', 0, $orientacion[$x], $fill);
            }
            $this->Ln();
            $fill = !$fill;
        }
        // Línea de cierre
        $this->Cell(array_sum($w), 0, '', 'T');
    }

}

Class pdf_facturas extends FPDF {

    var $maximo = 35;
    var $maximo_inicial = 35;
    var $contador = 1;
    var $contFactura = 0;
    var $postotalFactura = 0;
    var $postotalGuia = 0;
    /////////
    var $postotalBoleta = 0;
    var $contBoleta = 0;
    var $maximoBoleta = 9;
    var $maximoBoleta_inicial = 9;

    //para cambiar el valor del mes en español

    function encabezadoFactura($datosCliente) {
        $w = array(20, 90, 11, 50);
        $g = 4;


        if ($datosCliente[0]['imprimir'] == 'no') {
            $this->Image(ROOT . 'imagenes' . DS . 'factura.jpg', 0, 0, 209, 297);
            $this->SetFont('Times', '', 16);
            $this->setY(45 + $this->postotalFactura);
            $this->Cell(110);
            $this->Cell(25, 10, $datosCliente[0]['serieFactura'] . ' -');
            $this->Cell(10, 10, utf8_decode('N°'));
            $this->Cell(30, 10, $datosCliente[0]['numeroFactura'] + $this->contFactura);
            $this->Ln();
        }


        $this->SetY(68.5 + $this->postotalFactura);
        $this->SetFont('Times', '', 8.5);
        $this->Cell($w[0]);
        $this->Cell($w[1], $g, $datosCliente[0]['razonsocial']);
        $this->ln();

        $this->Cell($w[0]);
        $this->Cell($w[1], $g, $datosCliente[0]['direccion']);
        $this->Cell($w[2]);
        $this->Cell($w[3], $g, $datosCliente[0]['fecha']);
        $this->ln();

        $this->Cell($w[0] + $w[1] + $w[2]);
        $this->Cell($w[3], $g, $datosCliente[0]['numeroRelacionado']);
        $this->ln();

        $this->Cell($w[0]);
        $this->Cell($w[1], $g, $datosCliente[0]['condicion']);
        $this->Cell($w[2]);
        $this->Cell($w[3], $g, $datosCliente[0]['nombredepartamento'] . ' - ' . $datosCliente[0]['nombreprovincia'] . ' - ' . $datosCliente[0]['nombredistrito']);
        $this->Ln();

        $this->Cell($w[0] + $w[1] + $w[2] + $w[3], $g, $datosCliente[0]['nada']);
        $this->Ln();

        $this->Cell($w[0]);
        $this->Cell($w[1], $g, $datosCliente[0]['nomcontacto']);
        $this->Cell(18);
        $this->Cell($w[3], $g, $datosCliente[0]['referencia']);
        $this->ln();

        $this->Cell($w[3], 1, $datosCliente[0]['nada']);
        $this->ln();

        $this->Cell($w[0]);
        $this->Cell($w[1], $g, $datosCliente[0]['ruc']);
        $this->Cell($w[2]);
        $this->Cell($w[3], $g, $datosCliente[0]['telefono'] . ' / ' . $datosCliente[0]['celular']);
    }

    function ImprimeFactura($datosCliente, $detalleOrden) {
        $EnLetras = New EnLetras();
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $mes = $meses[date('n')];
        $posCuerpo = 112 + $this->postotalFactura;
        $posFinal = 273 + $this->postotalFactura;
        $gc = 4;
        $gf = 5;
        $total = 0;
        $maximoValorFor = count($detalleOrden);
        //Parte inicial de las factura
        $this->encabezadoFactura($datosCliente);

        //Cuerpo de la factura
        $this->SetY($posCuerpo);
        for ($i = 0; $i < $maximoValorFor; $i++) {
            $this->SetFont('Courier', '', 7.5);
            $this->Cell(8, $gc, $i + 1, 0, 0, 'C', false);
            $this->Cell(18, $gc, $detalleOrden[$i]['codigopa'], 0, 0, 'C', false);
            $this->Cell(85, $gc, $detalleOrden[$i]['nompro'], 0, 0, 'L', false);
            $this->Cell(21, $gc, $detalleOrden[$i]['cantdespacho'], 0, 0, 'C', false);
            $this->Cell(24, $gc, $detalleOrden[$i]['preciofinal'], 0, 0, 'C', false);
            $this->Cell(18, 4, $detalleOrden[$i]['subtotal'], 0, 0, 'R', false);
            $this->ln();
            $total += $detalleOrden[$i]['subtotal'];


            if ($this->maximo - 1 == $i || $i + 1 == $maximoValorFor) {

                /*                 * *************** Inicio de luna nueva hoja ************************* */
                $this->Cell(183, $gf, str_pad('-', 100, '-', STR_PAD_LEFT), 0, 0, 'C', false);
                $this->Ln();
                //funciona siempre y cuando no llege al valor maximo de for
                if ($i + 1 != $maximoValorFor) {
                    $this->Cell(183, $gf, 'Va......', 0, 0, 'L', false);
                }

                $this->Ln();
                //parte Final de la Factura
                $this->SetFont('Times', '', 8.5);
                $this->SetY($posFinal);
                $this->Cell(10);
                $this->Cell(148, $gf, $EnLetras->ValorEnLetras($total, $datosCliente[0]['nombremoneda']));
                $this->Cell(18, $gf, number_format($total / 1.18, 2), 0, 0, 'R', false);
                $this->ln();

                $this->Cell(158);
                $this->Cell(18, $gf, number_format($total - ($total / 1.18), 2), 0, 0, 'R', false);
                $this->ln();

                $this->Cell(185, 1, "", 0, 0, 'R', false);
                $this->ln();

                $this->Cell(29);
                $this->Cell(7, $gf, date('d'), 0, 0, 'C', false);
                $this->Cell(35, $gf, $mes, 0, 0, 'C', false);
                $this->Cell(10, $gf, date('Y'), 0, 0, 'R', false);
                $this->Cell(77);
                $this->Cell(18, $gf, number_format($total, 2), 0, 0, 'R', false);
                $this->ln();

                /*                 * **************************************** */
                $total = 0;
                $this->contador += 1;
                $this->maximo = $this->maximo_inicial * $this->contador;
                $this->contFactura += 1;
                //funciona siempre y cuando no llege al valor maximo de for
                if ($i + 1 != $maximoValorFor) {
                    $this->AddPage();
                    $this->encabezadoFactura($datosCliente);
                    $this->SetY($posCuerpo + 1.5);
                    $this->Cell(183, $gf, 'viene....', 0, 0, 'L', false);
                    $this->Ln();
                }
            }
        }
    }

    function encabezadoGuia($datosCliente) {
        $g = 3;
        $w = array();


        if ($datosCliente[0]['imprimir'] == 'no') {
            $this->Image(ROOT . 'imagenes' . DS . 'Guia_Remision.jpg', 0, 0, 209, 297);
            $this->SetFont('Times', '', 16);
            $this->setY(46 + $this->postotalFactura);
            $this->Cell(120);
            $this->Cell(25, 10, $datosCliente[0]['serieFactura'] . ' -');
            $this->Cell(10, 10, utf8_decode('N°'));
            $this->Cell(30, 10, $datosCliente[0]['numeroFactura'] + $this->contFactura);
            $this->Ln();
        }
        $this->SetFont('Times', '', 7);
        $this->setY(57.5 + $this->postotalGuia);

        $this->Cell(2);
        $this->Cell(150, $g - 1, $datosCliente[0]['referencia']);
        $this->Ln();

        $this->setY(64 + $this->postotalGuia);



        $this->Cell(31);
        $this->Cell(72, $g - 1, date('d/m/Y'));
        $this->Ln();
        $this->setY(68.5 + $this->postotalGuia);
        $this->Cell(31);
        $this->Cell(72, $g - 1, date('d/m/Y'));
        $this->Cell(27);
        $this->Cell(55, $g - 1, $datosCliente[0]['razonsocial']);
        $this->Ln();

        $this->Cell(31);
        $this->Cell(72, $g, "");
        $this->Ln();

        $this->Cell(113);
        $this->Cell(46, $g - 2, $datosCliente[0]['ruc']);
        $this->Cell(32, $g - 2, $datosCliente[0]['dni']);
        $this->Ln();

        $this->Cell(31);
        $this->Cell(72, $g - 1, $datosCliente[0]['comprobantePago']);
        $this->Ln();

        $this->Cell(11);
        $this->Cell(92, $g, $datosCliente[0]['tipo']);
        $this->Cell(30);
        $this->Cell(58, $g, $datosCliente[0]['unidadTransporte']);
        $this->Ln();

        $this->Cell(55, 5, "");
        $this->Ln();

        $this->setY(80 + $this->postotalGuia);
        $this->Cell(9);
        $this->Cell(129, $g, $datosCliente[0]['numeroRelacionado']);
        $this->Cell(60, $g, $datosCliente[0]['vehiculo']);
        $this->Ln();

        $this->Cell(55, 1, "");
        $this->Ln();

        $this->Cell(31);
        $this->Cell(72, $g, $datosCliente[0]['domiPartida']);
        $this->Cell(32);
        $this->Cell(55, $g, $datosCliente[0]['constancia']);
        $this->Ln();

        $this->Cell(55, 1.5, "");
        $this->Ln();

        $this->Cell(30);
        $this->Cell(73, $g, $datosCliente[0]['direccion']);
        $this->Cell(20);
        $this->Cell(67, $g, $datosCliente[0]['LicConducir']);
        $this->Ln();
    }

    function ImprimeGuia($dataCliente, $detalleOrden) {
        $EnLetras = New EnLetras();

        $posCuerpo = 104 + $this->postotalGuia;
        $posFinal = 254.5 + $this->postotalGuia;
        $gc = 3.5;
        $gf = 5;
        $total = 0;
        $maximoValorFor = count($detalleOrden);
        //Parte inicial de las guia
        $this->encabezadoGuia($dataCliente);

        //Cuerpo de la guia
        $this->SetY($posCuerpo);
        for ($i = 0; $i < $maximoValorFor; $i++) {
            $this->SetFont('Courier', '', 7.5);
            $this->Cell(10, $gc, $i + 1, 0, 0, 'C', false);
            $this->Cell(26, $gc, $detalleOrden[$i]['codigopa'], 0, 0, 'C', false);
            $this->Cell(92, $gc, $detalleOrden[$i]['nompro'], 0, 0, 'L', false);
            $this->Cell(15, $gc, $detalleOrden[$i]['cantdespacho'], 0, 0, 'C', false);
            $this->Cell(18, $gc, number_format($detalleOrden[$i]['preciofinal'], 2), 0, 0, 'R', false);

            $this->ln();



            if ($this->maximo - 1 == $i || $i + 1 == $maximoValorFor) {

                /*                 * *************** Inicio de una nueva hoja ************************* */
                $this->Cell(183, $gf, str_pad('-', 100, '-', STR_PAD_LEFT), 0, 0, 'C', false);
                $this->Ln();
                //funciona siempre y cuando no llege al valor maximo de for
                if ($i + 1 != $maximoValorFor) {
                    $this->Cell(183, $gf, 'Va......', 0, 0, 'L', false);
                }

                $this->Ln();
                //parte Final de la guia
                $this->SetFont('Times', '', 7);
                $this->SetY($posFinal - 0.5);
                $this->Cell(20);
                $this->Cell(72, $gf, $dataCliente[0]['trazonsocial']);
                $this->Cell(50, $gf, $dataCliente[0]['']);
                $this->Cell(33, $gf, $dataPieGuia[0]['pesoTotal'], 0, 0, 'C', false);
                $this->Cell(3);
                $this->Cell(33, $gf, $dataPieGuia[0]['costominimotraslado'], 0, 0, 'C', false);
                $this->ln();

                $this->Cell(20);
                $this->Cell(72, $gf - 3, $dataCliente[0]['tdireccion']);
                $this->Cell(50, $gf - 3, $dataCliente[0]['truc']);
                $this->Ln();

                $this->setY($posFinal + 23.5);

                $this->Cell(139);
                $this->Cell(48, $gf, $dataCliente[0]['razonsocial'], 0, 0, 'C', false);
                $this->Ln();

                /*                 * **************************************** */
                $total = 0;
                $this->contador += 1;
                $this->maximo = $this->maximo_inicial * $this->contador;
                $this->contFactura += 1;
                //funciona siempre y cuando no llege al valor maximo de for
                if ($i + 1 != $maximoValorFor) {
                    $this->AddPage();
                    $this->encabezadoGuia($dataCliente);
                    $this->SetY($posCuerpo + 1.5);
                    $this->Cell(183, $gf, 'viene....', 0, 0, 'L', false);
                    $this->Ln();
                }
            }
        }
    }

    function encabezadoBoleta($dataCliente) {
        $g = 3;
        $w = array();


        if ($dataCliente[0]['imprimir'] == 'no') {
            $this->Image(ROOT . 'imagenes' . DS . 'boleta.jpg', 0, 0, 209, 146);
            $this->SetFont('Times', '', 15);
            $this->setY(39 + $this->postotalBoleta);
            $this->Cell(120);
            $this->Cell(25, 10, $dataCliente[0]['serieBoleta'] . ' -');
            $this->Cell(10, 10, utf8_decode('N°'));
            $this->Cell(30, 10, $dataCliente[0]['numeroBoleta'] + $this->contBoleta);
            $this->Ln();
        }
        $this->SetFont('Times', '', 7);
        $this->SetY(56 + $this->postotalBoleta);

        $this->Cell(18);
        $this->Cell(122, $g, $dataCliente[0]['razonsocial']);
        $this->Cell(9);
        $this->Cell(40, $g, $dataCliente[0]['dni']);
        $this->Ln();

        $this->SetY(61 + $this->postotalBoleta);
        $this->Cell(18);
        $this->Cell(142, $g, $dataCliente[0]['direccion']);
        $this->Cell(9, $gf, date('d'), 0, 0, 'C', false);
        $this->Cell(10, $gf, date('m'), 0, 0, 'C', false);
        $this->Cell(11, $gf, date('y'), 0, 0, 'C', false);
    }

    function ImprimeBoleta($dataCliente, $detalleOrden) {
        $EnLetras = New EnLetras();
        $meses = array("", "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
        $mes = $meses[date('n')];
        $posCuerpo = 73 + $this->postotalBoleta;
        $posFinal = 120 + $this->postotalBoleta;
        $gc = 3.5;
        $gf = 5;
        $total = 0;
        $maximoValorFor = count($detalleOrden);
        //Parte inicial de las guia
        $this->encabezadoBoleta($dataCliente);

        //Cuerpo de la guia
        $this->SetY($posCuerpo);
        for ($i = 0; $i < $maximoValorFor; $i++) {
            $this->SetFont('Courier', '', 7.5);
            $this->Cell(8, $gc, $i + 1, 0, 0, 'C', false);
            $this->Cell(20, $gc, $detalleOrden[$i]['codigopa'], 0, 0, 'C', false);
            $this->Cell(90, $gc, $detalleOrden[$i]['nompro'], 0, 0, 'L', false);
            $this->Cell(21, $gc, $detalleOrden[$i]['cantdespacho'], 0, 0, 'C', false);
            $this->Cell(26, $gc, number_format($detalleOrden[$i]['preciofinal'], 2), 0, 0, 'R', false);
            $this->Cell(23, $gc, number_format($detalleOrden[$i]['preciofinal'] * $detalleOrden[$i]['cantdespacho'], 2), 0, 0, 'R', false);
            $this->ln();
            $total += $detalleOrden[$i]['preciofinal'] * $detalleOrden[$i]['cantdespacho'];


            if ($this->maximoBoleta - 1 == $i || $i + 1 == $maximoValorFor) {

                /*                 * ***************Marca el Fin del cuerpo************************* */
                $this->Cell(190, $gf, str_pad('-', 100, '-', STR_PAD_LEFT), 0, 0, 'C', false);
                $this->Ln();
                //funciona siempre y cuando no llege al valor maximo de for
                if ($i + 1 != $maximoValorFor) {
                    $this->Cell(190, $gf, 'Va......', 0, 0, 'L', false);
                }

                $this->Ln();
                //parte Final de la guia
                $this->SetFont('Times', '', 7);
                $this->SetY($posFinal);
                $this->Cell(9);
                $this->Cell(110, $gf, $EnLetras->ValorEnLetras($total, ""));
                $this->Cell(47);
                $this->Cell(22, $gf, number_format($total, 2), 0, 0, 'R', false);
                $this->Ln();

                $this->SetY($posFinal + 14);
                $this->Cell(113);
                $this->Cell(10, $gf, date('d'), 0, 0, 'C', false);
                $this->Cell(30, $gf, $mes, 0, 0, 'C', false);
                $this->Cell(10, $gf, date('Y'), 0, 0, 'R', false);


                /*                 * **************************************** */
                $total = 0;
                $this->contador += 1;
                $this->maximoBoleta = $this->maximoBoleta_inicial * $this->contador;
                $this->contBoleta += 1;
                //funciona siempre y cuando no llege al valor maximo de for
                if ($i + 1 != $maximoValorFor) {
                    $this->AddPage();
                    $this->encabezadoBoleta($dataCliente);
                    $this->SetY($posCuerpo);
                    $this->Cell(190, $gf, 'viene....', 0, 0, 'L', false);
                    $this->Ln();
                }
            }
        }
    }

    function encabezadoGuiMadre($dataCliente) {
        
    }

    function encabezadoDevolucion($dataCliente) {
        $g = 3;
        $w = array();

        $this->SetFont('Times', '', 7);
        $this->SetY(51 + $this->postotalBoleta);

        $this->Cell(18);
        $this->Cell(122, $g, $dataCliente[0]['razonsocial']);
        $this->Cell(9);
        $this->Cell(40, $g, $dataCliente[0]['dni']);
        $this->Ln();

        $this->SetY(56 + $this->postotalBoleta);
        $this->Cell(18);
        $this->Cell(142, $g, $dataCliente[0]['direccion']);
        $this->Cell(9, $gf, date('d'), 0, 0, 'C', false);
        $this->Cell(10, $gf, date('m'), 0, 0, 'C', false);
        $this->Cell(11, $gf, date('y'), 0, 0, 'C', false);
    }

    function ImprimeDevolucion($dataCliente, $detalleOrden) {
        $EnLetras = New EnLetras();

        $posCuerpo = 104 + $this->postotalGuia;
        $posFinal = 254.5 + $this->postotalGuia;
        $gc = 3.5;
        $gf = 5;
        $total = 0;
        $maximoValorFor = count($detalleOrden);
        //Parte inicial de las guia
        $this->encabezadoGuia($dataCliente);

        //Cuerpo de la guia
        $this->SetY($posCuerpo);
        for ($i = 0; $i < $maximoValorFor; $i++) {
            $this->SetFont('Courier', '', 7.5);
            $this->Cell(10, $gc, $i + 1, 0, 0, 'C', false);
            $this->Cell(26, $gc, $detalleOrden[$i]['codigopa'], 0, 0, 'C', false);
            $this->Cell(78, $gc, $detalleOrden[$i]['nompro'], 0, 0, 'L', false);
            $this->Cell(13, $gc, $detalleOrden[$i]['cantaprobada'], 0, 0, 'C', false);
            $this->Cell(18, $gc, number_format($detalleOrden[$i]['precioaprobado'], 2), 0, 0, 'R', false);

            $this->ln();



            if ($this->maximo - 1 == $i || $i + 1 == $maximoValorFor) {

                /*                 * *************** Inicio de una nueva hoja ************************* */
                $this->Cell(183, $gf, str_pad('-', 100, '-', STR_PAD_LEFT), 0, 0, 'C', false);
                $this->Ln();
                //funciona siempre y cuando no llege al valor maximo de for
                if ($i + 1 != $maximoValorFor) {
                    $this->Cell(183, $gf, 'Va......', 0, 0, 'L', false);
                }

                $this->Ln();
                //parte Final de la guia
                $this->SetFont('Times', '', 7);
                $this->SetY($posFinal - 0.5);
                $this->Cell(20);
                $this->Cell(72, $gf, $dataCliente[0]['trazonsocial']);
                $this->Cell(50, $gf, $dataCliente[0]['']);
                $this->Cell(33, $gf, $dataPieGuia[0]['pesoTotal'], 0, 0, 'C', false);
                $this->Cell(3);
                $this->Cell(33, $gf, $dataPieGuia[0]['costominimotraslado'], 0, 0, 'C', false);
                $this->ln();

                $this->Cell(20);
                $this->Cell(72, $gf - 3, $dataCliente[0]['tdireccion']);
                $this->Cell(50, $gf - 3, $dataCliente[0]['truc']);
                $this->Ln();

                $this->setY($posFinal + 23.5);

                $this->Cell(144);
                $this->Cell(48, $gf, $dataCliente[0]['razonsocial'], 0, 0, 'C', false);
                $this->Ln();

                /*                 * **************************************** */
                $total = 0;
                $this->contador += 1;
                $this->maximo = $this->maximo_inicial * $this->contador;
                $this->contFactura += 1;
                //funciona siempre y cuando no llege al valor maximo de for
                if ($i + 1 != $maximoValorFor) {
                    $this->AddPage();
                    $this->encabezadoGuia($dataCliente);
                    $this->SetY($posCuerpo + 1.5);
                    $this->Cell(183, $gf, 'viene....', 0, 0, 'L', false);
                    $this->Ln();
                }
            }
        }
    }

}

class PDF_MC_Table extends FPDF {

    public $_titulo;
    public $_fecha;
    public $_datoPie;
    public $_imagenCabezera;
    public $_titulos;
    public $_tamanoTitulos;
    public $_orientacion;
    public $_anchoColumna;
    public $_original;
    public $_relleno;
    var $widths;
    var $fill;
    var $aligns;

    function header() {

        /*      IMAGEN DE LOGO DE POWER ACOUSTIK
          if (empty($this->_imagenCabezera)) {
          $this->_imagenCabezera=ROOT.'imagenes'.DS.'logopower.jpg';
          }
          $this->Image($this->_imagenCabezera,10,5,70,0);

         */
        // Arial bold 15
        $this->SetTextColor(56, 56, 56);
        $this->SetFont('helvetica', 'B', 12);
        // Título

        $this->Cell(0, 10, $this->_fecha . "         " . $this->_titulo, '0', 1, 'R');
        $this->Ln(2);
        $this->Cell(0, 0.25, '', 'B', 1, 'I', 1);
        $this->Ln(2);
        $this->SetTitulos();
    }

    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'B', 8);
        // Número de página
        $this->Cell(0, 0.25, '', 'B', 1, 'I', 1);
        $this->Ln(2);
        $this->Cell(0, 10, '', 0, 0, 'L', '', '');
        $this->Cell(0, 10, $this->_datoPie . '                                                                    Page ' . $this->PageNo() . '/{nb}', 0, 0, 'R');
    }

    function SetWidths($w) {

        //Set the array of column widths

        $this->widths = $w;
    }

    function SetTitulos() {
        if (!empty($this->_titulos) && is_array($this->_titulos)) {
            if (!empty($this->_anchoColumna) && is_array($this->_anchoColumna)) {
                $this->SetWidths($this->_anchoColumna);
            }
            if (empty($this->_tamanoTitulos)) {
                $this->_tamanoTitulos = 6;
            }
            $this->SetFillColor(202, 232, 234);
            $this->SetDrawColor(12, 78, 139);
            $this->SetTextColor(12, 78, 139);
            $this->SetLineWidth(.3);
            $this->SetFont('Helvetica', 'B', $this->_tamanoTitulos);
            $cantidad = count($this->_titulos);
            $orientacion = array();
            for ($i = 0; $i < $cantidad; $i++) {
                $orientacion[] = 'C';
            }
            $this->SetAligns($orientacion);
            $this->fill(true);
            $this->Row($this->_titulos);
            //correguir
            if (!empty($this->_orientacion) && is_array($this->_orientacion)) {
                $this->SetAligns($this->_orientacion);
            }

            if (!empty($this->_original)) {
                $this->SetWidths($this->_original);
            }

            if (is_bool($this->_relleno)) {
                $this->fill($this->_relleno);
            }
        }
    }

    function SetAligns($a) {

        //Set the array of column alignments

        $this->aligns = $a;
    }

    function fill($f) {

        //juego de arreglos de relleno

        $this->fill = $f;
    }

    function Row($data) {

        //Calculate the height of the row

        $ny = 0;

        for ($i = 0; $i < count($data); $i++)
            $ny = max($ny, $this->NbLines($this->widths[$i], $data[$i]));

        $h = 5 * $ny;

        //Issue a page break first if needed

        $this->CheckPageBreak($h);

        //Draw the cells of the row

        for ($i = 0; $i < count($data); $i++) {

            $w = $this->widths[$i];

            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            //Save the current position

            $x = $this->GetX();

            $y = $this->GetY();

            //Draw the border

            $this->Rect($x, $y, $w, $h, $style);

            //Print the text

            $this->MultiCell($w, 5, $data[$i], 'LTR', $a, $this->fill);

            //Put the position to the right of the cell

            $this->SetXY($x + $w, $y);
        }

        //Go to the next line

        $this->Ln($h);
    }

    function CheckPageBreak($h) {

        //If the height h would cause an overflow, add a new page immediately

        if ($this->GetY() + $h > $this->PageBreakTrigger)
            $this->AddPage($this->CurOrientation);
    }

    function NbLines($w, $txt) {

        //Computes the number of lines a MultiCell of width w will take

        $cw = &$this->CurrentFont['cw'];

        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;

        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;

        $s = str_replace('\r', '', $txt);

        $ny = strlen($s);

        if ($ny > 0 and $s[$ny - 1] == '\n')
            $ny–;

        $sep = -1;

        $i = 0;

        $j = 0;

        $l = 0;

        $nl = 1;

        while ($i < $ny) {

            $c = $s[$i];

            if ($c == '\n') {

                $i++;

                $sep = -1;

                $j = $i;

                $l = 0;

                $nl++;

                continue;
            }

            if ($c == ' ')
                $sep = $i;

            $l += $cw[$c];

            if ($l > $wmax) {

                if ($sep == -1) {

                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;

                $sep = -1;

                $j = $i;

                $l = 0;

                $nl++;
            } else
                $i++;
        }
        return $nl;
    }

    public function obtenerFinMes($mes, $anio) {
        if ($mes == 2) {
            if (($anio % 4) == 0) {
                return 29;
            } else {
                return 28;
            }
        } else if ($mes == 1 || $mes == 3 || $mes == 5 || $mes == 7 || $mes == 8 || $mes == 10 || $mes == 12) {
            return 31;
        } else {
            return 30;
        }
    }

}
?>

