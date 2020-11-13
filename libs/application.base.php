<?php

Class Applicationbase {

    private $localhost;
    private $usuario_BD;
    private $clave_BD;
    private $basedatos;
    private $paginacion;

    function __construct() {
        $var_config = parse_ini_file("config.ini", true);
        $modo = $var_config['Globals']['Modo'];
        $this->localhost = $var_config[$modo]['Servidor'];
        $this->usuario_BD = $var_config[$modo]['Usuario'];
        $this->clave_BD = $var_config[$modo]['Clave'];
        $this->basedatos = $var_config[$modo]['NombreBBDD'];
    }

    private function conectar() {
        $var_config = parse_ini_file("config.ini", true);
        $modo = $var_config['Globals']['Modo'];
        $this->localhost = $var_config[$modo]['Servidor'];
        $this->usuario_BD = $var_config[$modo]['Usuario'];
        $this->clave_BD = $var_config[$modo]['Clave'];
        $this->basedatos = $var_config[$modo]['NombreBBDD'];
        $link = mysql_connect($this->localhost, $this->usuario_BD, $this->clave_BD) or die("Error al conectar :" . mysql_error());
        mysql_query("SET NAMES 'utf8'", $link);
        mysql_query("SET sql_mode = 'NO_BACKSLASH_ESCAPES'", $link);
        mysql_select_db($this->basedatos, $link) or die("Error al elegir la BBDD :" . mysql_error());
    }

    private function desconectar() {
        mysql_close() or die("Error al intentar desconectar del servidor de BBDD : " . mysql_error());
    }

    protected function EjecutaConsulta($sql) {
        $this->conectar();
        //$sql=strtolower($sql);
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $data[] = mysql_fetch_assoc($resultado);
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }
    
    protected function EjecutaConsultaBoolean($sql) {
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        $this->desconectar();
        if ($resultado) {
            return true;
        } else {
            return false;
        }
    }
    
    protected function devuelveSQL($tabla,$columnas,$filtro,$orden,$opciones=""){
		$tabla=strtolower($tabla);
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." from ".$tabla;
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }			
		$this->conectar();
		$sql=strtolower($sql);
		return $sql;
	}

    protected function leeRegistro($tabla, $columnas, $filtro, $orden, $opciones = "") {

        if (empty($columnas)) {
            $columnas = "*";
        }
        $sql = "Select " . $columnas . " from " . $tabla;
        if (!empty($filtro)) {
            $sql.=" where " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }//echo $sql."</br>";
        $this->conectar();
        $sql = strtolower($sql);
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }

    protected function leeRegistroA($tabla, $columnas, $filtro, $orden, $opciones = "") {

        if (empty($columnas)) {
            $columnas = "*";
        }
        $sql = "Select " . $columnas . " from " . $tabla;
        if (!empty($filtro)) {
            $sql.=" where " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }
        $this->conectar();
        $sql = strtolower($sql);
        //echo $sql."<br>";
        exit;
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }

    protected function leeRegistroPaginado($tabla, $columnas, $filtro, $orden, $pagina) {
        $tabla = strtolower($tabla);
        $var_config = parse_ini_file("config.ini", true);
        $tamanio = $var_config['Parametros']['Paginacion'];
        if (!empty($pagina)) {
            $inicio = ($pagina - 1) * $tamanio;
            if ($inicio < 0) {
                $inicio = 0;
            }
            $limit = "Limit " . $inicio . "," . $tamanio;
        } else {
            $limit = "Limit 0," . $tamanio;
        }
        return $this->leeRegistro($tabla, $columnas, $filtro, $orden, $limit);
    }

    protected function paginado($tabla, $filtro, $campos="") {

        $data = $this->leeRegistro($tabla, "count(".(empty($campos) ? "*" : "distinct ".$campos).") as coun", $filtro, "", "");

        $var_config = parse_ini_file("config.ini", true);
        $tamanio = $var_config['Parametros']['Paginacion'];
        $paginas = ceil($data[0]['coun'] / $tamanio);
        return $paginas;
    }

    protected function leeRegistro1($tabla, $columnas, $filtro, $orden, $opciones = "") {
        $tabla = strtolower($tabla);
        if (empty($columnas)) {
            $columnas = "*";
        }
        $sql = "Select " . $columnas . " from " . $tabla;
        if (!empty($filtro)) {
            $sql.=" where " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }
        $data = array();
        $this->conectar();

        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            while ($row = mysql_fetch_array($resultado)) {
                $data[] = array("value" => $row['codigo'],
                    "label" => $row['codigo'] . " " . $row['nompro']);
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }
    
    protected function leeRegistro2($tablas, $columnas, $filtro, $orden, $opciones = "") {
        $tablas_s = split(",", strtolower($tablas));
        if (empty($columnas)) {
            $columnas = "*";
        }
        $sql = "Select " . $columnas . " From " . $tablas_s[0] . " as t1 ";
        $sql.="Inner Join " . $tablas_s[1] . " as t2 on t1.id" . substr($tablas_s[1], 3) . "=t2.id" . substr($tablas_s[1], 3);
        if (!empty($filtro)) {
            $sql.=" where " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }

        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());

        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }

    //Recupera uniendo las tablas en forma cadena

    protected function leeRegistro3($tablas, $columnas, $filtro, $orden, $tipo = 1, $opciones = "") {
        $tablas_s = split(",", strtolower($tablas));
        if (empty($columnas)) {
            $columnas = "*";
        }
        if ($tipo == 1) {
            $sql = "Select " . $columnas . " From " . $tablas_s[0] . " as t1 ";
            $sql.="Inner Join " . $tablas_s[1] . " as t2 on t1.id" . substr($tablas_s[1], 3) . "=t2.id" . substr($tablas_s[1], 3);
            $sql.=" Inner Join " . $tablas_s[2] . " as t3 on t2.id" . substr($tablas_s[2], 3) . "=t3.id" . substr($tablas_s[2], 3);
        } else {
            $sql = "Select " . $columnas . " From " . $tablas_s[0] . " as t1 ";
            $sql.="Inner Join " . $tablas_s[1] . " as t2 on t1.id" . substr($tablas_s[1], 3) . "=t2.id" . substr($tablas_s[1], 3);
            $sql.=" Inner Join " . $tablas_s[2] . " as t3 on t1.id" . substr($tablas_s[2], 3) . "=t3.id" . substr($tablas_s[2], 3);
        }
        if (!empty($filtro)) {
            $sql.=" where " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }//echo $sql."<br>";
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }
protected function scriptArrayCompleto($sql) {

        
        $this->conectar();
        $sql = strtolower($sql);

        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }
    protected function leeRegistro4($tablas, $columnas, $filtro, $orden, $opciones = "") {
        $tablas_s = split(",", strtolower($tablas));
        if (empty($columnas)) {
            $columnas = "*";
        }
        $sql = "Select " . $columnas . " From " . $tablas_s[0] . " as t1 ";
        $sql.="Inner Join " . $tablas_s[1] . " as t2 on t1.id" . substr($tablas_s[1], 3) . "=t2.id" . substr($tablas_s[1], 3);
        $sql.=" Inner Join " . $tablas_s[2] . " as t3 on t2.id" . substr($tablas_s[2], 3) . "=t3.id" . substr($tablas_s[2], 3);
        $sql.=" Inner Join " . $tablas_s[3] . " as t4 on t3.id" . substr($tablas_s[3], 3) . "=t4.id" . substr($tablas_s[3], 3);
        if (!empty($filtro)) {
            $sql.=" where " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }

    protected function leeRegistro40($tablas, $columnas, $filtro, $orden, $opciones = "") {
        $tablas_s = split(",", strtolower($tablas));
        if (empty($columnas)) {
            $columnas = "*";
        }
        $sql = "Select " . $columnas . " From " . $tablas_s[0] . " as t1 ";
        $sql.="Inner Join " . $tablas_s[1] . " as t2 on t1.id" . "=t2.id" . substr($tablas_s[1], 3);
        $sql.=" Inner Join " . $tablas_s[2] . " as t3 on t1.id" . "=t3.id" . substr($tablas_s[2], 3);
        $sql.=" Inner Join " . $tablas_s[3] . " as t4 on t3.id" . substr($tablas_s[3], 3) . "=t4.id" . substr($tablas_s[3], 3);
        if (!empty($filtro)) {
            $sql.=" where " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }

    protected function leeRegistro42($tablas, $columnas, $filtro, $orden, $opciones = "") {
        $tablas_s = split(",", strtolower($tablas));
        if (empty($columnas)) {
            $columnas = "*";
        }
        $sql = "Select " . $columnas . " From " . $tablas_s[0] . " as t1 ";
        $sql.="Inner Join " . $tablas_s[1] . " as t2 on t1.id" . substr($tablas_s[0], 3) . "=t2.id" . substr($tablas_s[0], 3);
        $sql.=" Inner Join " . $tablas_s[2] . " as t3 on t1.id" . substr($tablas_s[2], 3) . "=t3.id" . substr($tablas_s[2], 3);
        $sql.=" Inner Join " . $tablas_s[3] . " as t4 on t1.id" . substr($tablas_s[3], 3) . "=t4.id" . substr($tablas_s[3], 3);
        if (!empty($filtro)) {
            $sql.=" WHERE " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }

    protected function leeRegistro5($tablas, $columnas, $filtro, $orden, $opciones = "") {
        $tablas_s = split(",", strtolower($tablas));
        if (empty($columnas)) {
            $columnas = "*";
        }
        $sql = "Select " . $columnas . " From " . $tablas_s[0] . " as t1 ";
        $sql.="Inner Join " . $tablas_s[1] . " as t2 on t1.id" . substr($tablas_s[1], 3) . "=t2.id" . substr($tablas_s[1], 3);
        $sql.=" Inner Join " . $tablas_s[2] . " as t3 on t2.id" . substr($tablas_s[2], 3) . "=t3.id" . substr($tablas_s[2], 3);
        $sql.=" Inner Join " . $tablas_s[3] . " as t4 on t3.id" . substr($tablas_s[3], 3) . "=t4.id" . substr($tablas_s[3], 3);
        $sql.=" Inner Join " . $tablas_s[4] . " as t5 on t4.id" . substr($tablas_s[4], 3) . "=t5.id" . substr($tablas_s[4], 3);
        if (!empty($filtro)) {
            $sql.=" where " . $filtro;
        }
        if (!empty($orden)) {
            $sql.=" order by " . $orden;
        }
        if (!empty($opciones)) {
            $sql.=" " . $opciones;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $num_resultado = mysql_num_rows($resultado);
            for ($i = 0; $i < $num_resultado; $i++) {
                $fila = mysql_fetch_array($resultado);
                $data[] = $fila;
            }
            $this->desconectar();
            return $data;
        } else {
            return mysql_error();
        }
    }

        protected function EjecutarGrabarSentencia($sentencia) {
            $this->conectar();
            $resultado = mysql_query($sentencia) or die(mysql_error());
            if ($resultado) {
                $this->desconectar();
                return true;
            } else {
                return mysql_error();
            }
        }
    
    protected function grabaRegistro($tabla, $data) {
        $data['estado'] = 1;
        $tabla = mb_strtolower($tabla);
        $columnas = array_keys($data);
        $sql = "Insert Into " . $tabla . "(";
        for ($i = 0; $i < count($columnas); $i++) {
            $sql.=$columnas[$i] . ",";
        }
        $sql.="fechacreacion,usuariocreacion) ";
        $sql.="values(";
        for ($i = 0; $i < count($data); $i++) {
            $sql.="'" . str_replace("'", "&#39;", mb_strtoupper($data[$columnas[$i]])) . "',";
        }
        $sql.="Now()," . $_SESSION['idactor'] . ")";

        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        $id = mysql_insert_id();
        $this->desconectar();
        if ($resultado) {
            return $id;
        } else {
            return false;
        }
    }

    protected function actualizaRegistro($tabla, $data, $filtro) {
        $tabla = strtolower($tabla);
        $columnas = array_keys($data);
        $sql = "Update " . $tabla . " set ";
        for ($i = 0; $i < count($columnas); $i++) {
            $sql.=$columnas[$i] . "='" . htmlentities($data[$columnas[$i]], ENT_QUOTES, 'UTF-8') . "',";
        }
        $sql.="fechamodificacion=Now() , usuariomodificacion=" . $_SESSION['idactor'] . "";
        $sql.=" Where " . $filtro;
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        $this->desconectar();
        if ($resultado) {
            return True;
        } else {
            return false;
        }
    }

    protected function inactivaRegistro($tabla, $filtro) {
        $tabla = strtolower($tabla);
        $sql = "Update " . $tabla . " set estado=0,";
        $sql.="fechamodificacion=Now() , usuariomodificacion=" . $_SESSION['idactor'] . "";
        if (!empty($filtro)) {
            $sql.=" Where " . $filtro;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        $this->desconectar();
        if ($resultado) {
            return true;
        } else {
            return false;
        }
    }

    protected function eliminaRegistro($tabla, $filtro) {
        $tabla = strtolower($tabla);
        $sql = "Delete from " . $tabla . " ";
        if (!empty($filtro)) {
            $sql.=" Where " . $filtro;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        $this->desconectar();
        return $resultado;
    }

    protected function cambiaEstado($tabla, $filtro) {
        $tabla = strtolower($tabla);
        $sql = "Update " . $tabla . " set 
			estado=ABS((estado-1)*(-1)),";
        $sql.="fechamodificacion=Now() , usuariomodificacion=" . $_SESSION['idactor'] . "";
        if (!empty($filtro)) {
            $sql.=" Where " . $filtro;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        $this->desconectar();
        if ($resultado) {
            return true;
        } else {
            return false;
        }
    }

    protected function contarRegistro($tabla, $filtro = "") {
        $tabla = strtolower($tabla);
        $sql = "SELECT * FROM " . $tabla;
        if (!empty($filtro)) {
            $sql.=" WHERE " . $filtro;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        if ($resultado) {
            $numRegistro = mysql_num_rows($resultado);
            $this->desconectar();
            return $numRegistro;
        } else {
            return mysql_error();
        }
    }

    protected function exiteRegistro($tabla, $filtro = "") {
        $tabla = strtolower($tabla);
        $sql = "SELECT * FROM " . $tabla;
        if (!empty($filtro)) {
            $sql.=" WHERE " . $filtro;
        }
        $this->conectar();
        $resultado = mysql_query($sql) or die(mysql_error());
        $exite = 0;
        if ($resultado) {
            if (mysql_num_rows($resultado) > 0) {
                $exite = 1;
            }
            $this->desconectar();
            return $exite;
        } else {
            return mysql_error();
        }
    }

    protected function modoFacturacion() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $modoFacturacion = $archivoConfig['ModoFacturacion'];
        return $modoFacturacion;
    }

    protected function condicionLetra() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $condicionLetra = $archivoConfig['CondicionLetra'];
        return $condicionLetra;
    }

    protected function tipoLetra() {
        $archivoConfig = parse_ini_file("config.ini", true);
        $tipoLetra = $archivoConfig['TipoLetra'];
        return $tipoLetra;
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
   public function add_ceros($numero, $ceros)
{
    $order_diez = explode(".", $numero);
    $dif_diez = $ceros - strlen($order_diez[0]);
    for ($m = 0; $m < $dif_diez; $m++) {
        @$insertar_ceros .= 0;
    }
    return $insertar_ceros .= $numero;
}
    public function cantidad_dias_entre_dos_fechas($inicio, $fin)
    {
    $inicio = strtotime($inicio);
    $fin = strtotime($fin);
    $dif = $fin - $inicio;
    $diasFalt = (( ( $dif / 60 ) / 60 ) / 24);
    return ceil($diasFalt);
    }
    
    function arrayComprobantesCorrectos($ncomprobante){
//start extraendo las variables para calcular cada numero factura segunel explode()
    $porciones = explode("-", $ncomprobante);
    foreach($porciones as $key=> $valor){
            if($key==0){  $numdocUltimo=$valor; }
            if($key>0){
                $ntemp=strlen($valor);
                $digitosAnterior = substr($numdocAnterior, -$ntemp);
                $digitosUltimo = substr($valor, -$ntemp);
                if($digitosUltimo>$digitosAnterior){ //remplazar
                    $numdocUltimo=substr($numdocAnterior,0, -$ntemp);
                    $numdocUltimo=$numdocUltimo.$valor;
                }else{ //operar +1
                      $numdocAnterior=(substr($numdocAnterior,0, -$ntemp))+1;
                      $numdocUltimo=$numdocAnterior.$valor;
                }
            }
            $numdocAnterior=$numdocUltimo;
            //start almacenando un array con comprobantes correctos
            $tempComprobantes[]=array("numdoc"=>$numdocUltimo);
            //end almacenando un array con comprobantes correctos
    }
//end extraendo las variables para calcular cada numero factura segunel explode()
    return  $tempComprobantes;
}
}

?>