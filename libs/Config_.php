<?php
class configjob
{

    private $server = "localhost";
    private $user = "root";
    private $password = "";
    private $database1 = "bdcelestium";
    private $database="";
    private $_numPagina;
    private $_resultados_cantidad;
    private $_total_registros;
    private $_sql1;
    private $_sql2;


    public function begin1()
    {
        $this->db1();
        $resultado = $this->filtro("BEGIN");
        return $resultado;
    }

    public function mysql_errores()
    {
        $resultado = "codigo de error--> " . mysql_errno() . "<br> " . "descripcion de error--> " . mysql_error() . "<br><br><br>";
        return $resultado;
    }

    public function commit1()
    {
        $this->db1();
        $resultado = $this->filtro("COMMIT");
        return $resultado;
    }

    public function rollback1()
    {
        $this->db1();
        $resultado = $this->filtro("ROLLBACK");
        return $resultado;
    }

    public function db1()
    {

        $conx = mysql_connect($this->server, $this->user, $this->password);
        $conx = mysql_select_db($this->database1);
			 return $conx;
    }

    public function filtro($sql)
    {
             $resultado = mysql_query($sql);
            return $resultado;
    }

    public function lisFila1($resultado)
    {
//        este devuelve varias filas  pero se va a usar para devolver una fila para evitar each

        $row_array = mysql_fetch_object($resultado);
        return $row_array;
//stdClass Object ( [cod_venta] => 83 [cod_empresa] => [do_serdocum] => [do_numdocum] => [serie] => [numero] => 789 [serie_ticket] => [numerot_ticket] =>)
    }

    public function lisObject($resultado)
    {
        $row_array = mysql_fetch_row($resultado);
        return $row_array;
//        SIEMPRE devuelve una fila
//      solo se lee con los indices
//        se extrae datos asi:
//$fila = mysql_fetch_row($resultado);
//$fila[0]; // campo 1
//$fila[1]; // campo 2
//        Array ( [0] => 80 [1] => [2] => [3] => [4] => [5] => 789 [6] => [7] => [8] => 0 [9] => 0 [10] => [11] => 0000-00-00 [12] => )
    }

    public function filtr0($sql)
    {
        $resultado = mysql_query($sql);
        return $resultado;
    }

    public function lisAsos1($resultado)
    {
        $row_array = array();
        while ($row = mysql_fetch_assoc($resultado)) {
            $row_array[] = $row;
        }
        return $row_array;
        //array asociativo para recorrer en un each de manera simple
    }

    public function lisAsos2($resultado)
    {
        $row_array = array();
        while ($row = mysql_fetch_assoc($resultado)) {
            $row_array[] = array_map('utf8_encode', $row);
        }
        return $row_array;
        //se usa para que el json asincrono no de errores
    }

    public function ultPK()
    {
        return mysql_insert_id();
        //DEVUELVE EL PRIMARY KEY INSERTADO DE ESA SESION ASI AYA VARIOS TERMINALES CONECTADOS
    }

    public function cnReg($resultado)
    {
        $cantidad = mysql_num_rows($resultado);
        return $cantidad;
        //cantidad de registros de una consulta
    }

    public function regAfecIUD()
    {
        return mysql_affected_rows();
        // uántas filas fueron INSERT, UPDATEo DELETE
    }

    public function libMemo($resultado)
    {
        return mysql_free_result($resultado);
    }

    public function cierraConexion($conexion)
    {
        mysql_close($conexion);
    }

    public function database()
    {

        return $this->get_database();
    }


//    public function filtro_especial($sql, $conexion)
//    {
//        $conexion->multi_query($sql);
//        $conexion->next_result();
//        $resultado = $conexion->store_result();
//        return $resultado;
//        mysqli_close($resultado);
//    }

    public function set_numPagina($value)
    {
        $this->_numPagina = $value;
    }

    public function set_resultados_cantidad($value)
    {
        $this->_resultados_cantidad = $value;
    }

    public function set_total_registros($value)
    {
        $this->_total_registros = $value;
    }

    public function set_sql1($value)
    {
        $this->_sql1 = $value;
    }

    public function set_sql2($value)
    {
        $this->_sql2 = $value;
    }

    public function get_numPagina()
    {
        return $this->_numPagina;
    }

    public function get_resultados_cantidad()
    {
        return $this->_resultados_cantidad;
    }

    public function get_total_registros()
    {
        return $this->_total_registros;
    }

    public function get_sql1()
    {
        return $this->_sql1;
    }

    public function get_sql2()
    {
        return $this->_sql2;
    }

    public function listar_tabla_paginada_cadenax($cadenax, $numero_pagina, $paramOrdenar, $filasPagina, $asc_desc)
    {
        if ($asc_desc == "ASC") {
            $sql1 = $cadenax . ' ' . ' ORDER BY ' . $paramOrdenar . ' ASC';
        }
        if ($asc_desc == "DESC") {
            $sql1 = $cadenax . ' ' . ' ORDER BY ' . $paramOrdenar . ' DESC';
        }
        $this->set_sql1($sql1);
        $this->db1();
        $consulta_cantidad = $this->filtro($sql1);
        $resultados_cantidad = $this->cnReg($consulta_cantidad);


        if ($resultados_cantidad > 0) {
            $campo_de_inicio = ($numero_pagina - 1) * $filasPagina;
            $total_registros = ceil($resultados_cantidad / $filasPagina);
            $this->set_resultados_cantidad($resultados_cantidad);
            $this->set_total_registros($total_registros);
            if ($asc_desc == "ASC") {
                $sql2 = $cadenax . ' ' . ' ORDER BY ' . $paramOrdenar . ' ASC LIMIT ' . $campo_de_inicio . ',' . $filasPagina;
            }
            if ($asc_desc == "DESC") {
                $sql2 = $cadenax . ' ' . ' ORDER BY ' . $paramOrdenar . ' DESC LIMIT ' . $campo_de_inicio . ',' . $filasPagina;
            }
            $this->set_sql2($sql2);
            $resultado = $this->filtro($sql2);
            $listaArrayAsociativo = $this->lisAsos1($resultado);
            return $listaArrayAsociativo;
        }
    }
}

function secure($cadena)
{
    $cadena = mysql_real_escape_string($cadena);
    $cadena = str_ireplace("SELECT", "", $cadena);
    $cadena = str_ireplace("COPY", "", $cadena);
    $cadena = str_ireplace("DELETE", "", $cadena);
    $cadena = str_ireplace("DROP", "", $cadena);
    $cadena = str_ireplace("DUMP", "", $cadena);
    $cadena = str_ireplace("LIKE", "", $cadena);
    $cadena = str_ireplace("DATABASE", "", $cadena);
		$cadena = str_ireplace("UNION", "", $cadena);
		$cadena = str_ireplace("FOR", "", $cadena);
		$cadena = str_ireplace("CASE", "", $cadena);
		$cadena = str_ireplace("SWITCH", "", $cadena);
		$cadena = str_ireplace("FROM", "", $cadena);
		$cadena = str_ireplace("USERS", "", $cadena);
		$cadena = str_ireplace("USUARIOS", "", $cadena);
		$cadena = str_ireplace("UNION", "", $cadena);
		$cadena = str_ireplace("DATABASE", "", $cadena);
		$cadena = str_ireplace("TRUNCATE", "", $cadena);
		$cadena = str_ireplace("EACH", "", $cadena);
		$cadena = str_ireplace("WHILE", "", $cadena);
		$cadena = str_ireplace("CREATE", "", $cadena);
		$cadena = str_ireplace("TABLE", "", $cadena);
	  $cadena = str_ireplace("select", "", $cadena);
    $cadena = str_ireplace("copy", "", $cadena);
    $cadena = str_ireplace("delete", "", $cadena);
    $cadena = str_ireplace("drop", "", $cadena);
    $cadena = str_ireplace("dump", "", $cadena);
    $cadena = str_ireplace("like", "", $cadena);
    $cadena = str_ireplace("database", "", $cadena);
		$cadena = str_ireplace("union", "", $cadena);
		$cadena = str_ireplace("for", "", $cadena);
		$cadena = str_ireplace("case", "", $cadena);
		$cadena = str_ireplace("switch", "", $cadena);
		$cadena = str_ireplace("from", "", $cadena);
		$cadena = str_ireplace("user", "", $cadena);
		$cadena = str_ireplace("usuarios", "", $cadena);
		$cadena = str_ireplace("union", "", $cadena);
		$cadena = str_ireplace("database", "", $cadena);
		$cadena = str_ireplace("truncate", "", $cadena);
		$cadena = str_ireplace("each", "", $cadena);
		$cadena = str_ireplace("while", "", $cadena);
		$cadena = str_ireplace("create", "", $cadena);
		$cadena = str_ireplace("table", "", $cadena);
    return $cadena;
}

function add_ceros($numero, $ceros)
{
    $order_diez = explode(".", $numero);
    $dif_diez = $ceros - strlen($order_diez[0]);
    for ($m = 0; $m < $dif_diez; $m++) {
        @$insertar_ceros .= 0;
    }
    return $insertar_ceros .= $numero;
}

function convert_num($var)
{
    $_SESSION["database"] = intval($var);
    return $_SESSION["database"];
}

function reemplazarCaracteres($cadena, $remplazar, $por)
{
    $cadena = str_replace($remplazar, $por, $cadena);
    return $cadena;
}

function redondeado($numero, $decimales)
{
    $factor = pow(10, $decimales);
    return (round($numero * $factor) / $factor);
}

function convert_minus($cadena)
{ // a minúsculas
    $cadena = strtolower($cadena);
    return $cadena;
}

function convert_mayus($cadena)
{ // a mayusculas
    $cadena = strtoupper($cadena);
    return $cadena;
}

function convert_mayus_First($cadena)
{ // Para pasar a mayúsculas solo la primera letra de toda la cadena
    $cadena = ucfirst($cadena);
    return $cadena;
}

function fecha()
{
    $fecha = date("Y-m-d");
    return $fecha;
}

function dia()
{
    $fecha = date("j");
    return $fecha;
}

function mes()
{
    $fecha = date("Y");
    return $fecha;
}

function ano()
{
    $fecha = date("Y");
    return $fecha;
}

function hora()
{
    $fecha = date("H:i:s");
    return $fecha;
}

function fechahora()
{
    $fecha = date("Y-m-d H:i:s");
    return $fecha;
}
/** Actual month first day **/
function primer_diadelmes() {
      $month = date('m');
      $year = date('Y');
      return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
}
function ultimo_diadelmes() {
      $month = date('m');
      $year = date('Y');
      $day = date("d", mktime(0,0,0, $month+1, 0, $year));
      return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
};
function cortarCadena($cadena, $inicio, $fin)
{
    $cadena = substr($cadena, $inicio, $fin);
    return $cadena;
}
function fnModel($regAfecIUD){
     if ($regAfecIUD == 1) {
            return 1;
        } else {
            return 0;
        }
}
function cadenaAleatoria($longitud,$tipo) {
	$longitud=$longitud-1;
	if($tipo=="numeros_mayusculas_minusculas"){
		    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i <=$longitud; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)]; }
	}
	if($tipo=="numeros_minusculas"){
		    $characters = '123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i <=$longitud; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)]; }
	}


    return $randomString;
}
function MAIL_NVLP($fromname, $fromaddress,$toname, $toaddress, $asunto, $message)
{

   $headers  = "MIME-Version: 1.0\n";
   $headers .= "Content-type: text/html; charset=iso-8859-1\n";
   $headers .= "X-Priority: 3\n";
   $headers .= "X-MSMail-Priority: Normal\n";
   $headers .= "X-Mailer: php\n";
   $headers .= "From: \"".$fromname."\" <".$fromaddress.">\n";
   return mail($toaddress,$asunto, $message, $headers);
}
function elimina_especiales_textarea($cadena,$mayus_minus){
 $find = array('á','é','í','ó','ú','â','ê','î','ô','û','ã','õ','ç','ñ','Á','É','Í','Ó','Ú','Â','Ê','Î','Ô','Û','Ã','Õ','Ç','Ñ');
 $repl = array('a','e','i','o','u','a','e','i','o','u','a','o','c','n','A','E','I','O','U','A','E','I','O','U','A','O','C','N');
 $cadena = str_replace($find, $repl, $cadena);
	$cadena = str_ireplace("SELECT", "", $cadena);
	$cadena = str_ireplace("COPY", "", $cadena);
	$cadena = str_ireplace("DELETE", "", $cadena);
	$cadena = str_ireplace("DROP", "", $cadena);
	$cadena = str_ireplace("DUMP", "", $cadena);
	$cadena = str_ireplace("LIKE", "", $cadena);
	$cadena = str_ireplace("DATABASE", "", $cadena);
	$cadena = str_ireplace("UNION", "", $cadena);
	$cadena = str_ireplace("CASE", "", $cadena);
	$cadena = str_ireplace("SWITCH", "", $cadena);
	$cadena = str_ireplace("FROM", "", $cadena);
	$cadena = str_ireplace("USERS", "", $cadena);
	$cadena = str_ireplace("USUARIOS", "", $cadena);
	$cadena = str_ireplace("UNION", "", $cadena);
	$cadena = str_ireplace("DATABASE", "", $cadena);
	$cadena = str_ireplace("TRUNCATE", "", $cadena);
	$cadena = str_ireplace("EACH", "", $cadena);
	$cadena = str_ireplace("WHILE", "", $cadena);
	$cadena = str_ireplace("CREATE", "", $cadena);
	$cadena = str_ireplace("TABLE", "", $cadena);
	$cadena = str_replace("nbsp;", "&nbsp;", $cadena);
	
	/*	$cadena = str_replace("?", "", $cadena);*/

	if($mayus_minus=="minusculas"){
	$cadena=strtolower($cadena);
	}
	if($mayus_minus=="mayusculas"){
	$cadena=strtoupper($cadena);
	}

	return $cadena;
}
function elimina_especiales($cadena,$mayus_minus){
 $find = array('á','é','í','ó','ú','â','ê','î','ô','û','ã','õ','ç','ñ','Á','É','Í','Ó','Ú','Â','Ê','Î','Ô','Û','Ã','Õ','Ç','Ñ');
 $repl = array('a','e','i','o','u','a','e','i','o','u','a','o','c','n','A','E','I','O','U','A','E','I','O','U','A','O','C','N');
 $cadena = str_replace($find, $repl, $cadena);
	$cadena = str_ireplace("SELECT", "", $cadena);
	$cadena = str_ireplace("COPY", "", $cadena);
	$cadena = str_ireplace("DELETE", "", $cadena);
	$cadena = str_ireplace("DROP", "", $cadena);
	$cadena = str_ireplace("DUMP", "", $cadena);
	$cadena = str_ireplace("LIKE", "", $cadena);
	$cadena = str_ireplace("DATABASE", "", $cadena);
	$cadena = str_ireplace("UNION", "", $cadena);
	$cadena = str_ireplace("CASE", "", $cadena);
	$cadena = str_ireplace("SWITCH", "", $cadena);
	$cadena = str_ireplace("FROM", "", $cadena);
	$cadena = str_ireplace("USERS", "", $cadena);
	$cadena = str_ireplace("USUARIOS", "", $cadena);
	$cadena = str_ireplace("UNION", "", $cadena);
	$cadena = str_ireplace("DATABASE", "", $cadena);
	$cadena = str_ireplace("TRUNCATE", "", $cadena);
	$cadena = str_ireplace("EACH", "", $cadena);
	$cadena = str_ireplace("WHILE", "", $cadena);
	$cadena = str_ireplace("CREATE", "", $cadena);
	$cadena = str_ireplace("TABLE", "", $cadena);
	$cadena = str_ireplace("TITLE", "", $cadena);
	$cadena = str_ireplace("nbsp;", "", $cadena);
	$cadena = str_ireplace("nbsp;", "", $cadena);
	$cadena = str_ireplace("@", "", $cadena);
	$cadena = str_ireplace("#", "", $cadena);
  $cadena = str_ireplace("∉", "", $cadena);
  $cadena = str_ireplace("%", "", $cadena);
	$cadena = str_ireplace("&", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("+", "", $cadena);
	$cadena = str_ireplace("(", "", $cadena);
	$cadena = str_ireplace(")", "", $cadena);
	$cadena = str_ireplace("¡", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("/", "", $cadena);

  /*$cadena=preg_replace("/([^A-Za-z0-9])/","", $cadena);*/
	//caracteres especiales
	$cadena=trim($cadena);
	$cadena=preg_replace('/[^a-zA-Z0-9\-]/', ' ', $cadena);
  //reemplaza varios espacios por uno solo
	$cadena = preg_replace('/( ){2,}/u',' ',$cadena);
	if($mayus_minus=="minusculas"){
	$cadena=strtolower($cadena);
	}
	if($mayus_minus=="mayusculas"){
	$cadena=strtoupper($cadena);
	}

	return $cadena;
}
/*function soloNumeros($cadena){
$cadena=trim($cadena);
$cadena = str_replace(".00", "", $cadena);
//caracteres especiales
$cadena=preg_replace('/[^a-zA-Z0-9\_]/', ' ', $cadena);
//elimina todo lo que sea diferente de un numero
$cadena =  ereg_replace("[a-zA-Z]", "", $cadena);
//reemplaza varios espacios por nada
$cadena = preg_replace('/( ){2,}/u','',$cadena);
return $cadena;
}*/
function soloNumeros($cadena){
//teclado celular
	$cadena = str_ireplace("@", "", $cadena);
	$cadena = str_ireplace("#", "", $cadena);
  $cadena = str_ireplace("∉", "", $cadena);
  $cadena = str_ireplace("%", "", $cadena);
	$cadena = str_ireplace("&", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("+", "", $cadena);
	$cadena = str_ireplace("(", "", $cadena);
	$cadena = str_ireplace(")", "", $cadena);
	$cadena = str_ireplace("¡", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("/", "", $cadena);

$cadena=trim($cadena);
$cadena = str_replace(".00", "", $cadena);
//caracteres especiales
$cadena=preg_replace('/[^a-zA-Z0-9]/', '', $cadena);
//elimina todo lo que sea diferente de un numero
$cadena =  ereg_replace("[a-zA-Z]", "", $cadena);
//reemplaza varios espacios por nada
$cadena = preg_replace('/( ){2,}/u','',$cadena);
return $cadena;
}
function elimina_especiales_delivery($cadena,$mayus_minus){
 $find = array('á','é','í','ó','ú','â','ê','î','ô','û','ã','õ','ç','ñ','Á','É','Í','Ó','Ú','Â','Ê','Î','Ô','Û','Ã','Õ','Ç','Ñ');
 $repl = array('a','e','i','o','u','a','e','i','o','u','a','o','c','n','A','E','I','O','U','A','E','I','O','U','A','O','C','N');
 $cadena = str_replace($find, $repl, $cadena);
	$cadena = str_ireplace("SELECT", "", $cadena);
	$cadena = str_ireplace("COPY", "", $cadena);
	$cadena = str_ireplace("DELETE", "", $cadena);
	$cadena = str_ireplace("DROP", "", $cadena);
	$cadena = str_ireplace("DUMP", "", $cadena);
	$cadena = str_ireplace("LIKE", "", $cadena);
	$cadena = str_ireplace("DATABASE", "", $cadena);
	$cadena = str_ireplace("UNION", "", $cadena);
	$cadena = str_ireplace("CASE", "", $cadena);
	$cadena = str_ireplace("SWITCH", "", $cadena);
	$cadena = str_ireplace("FROM", "", $cadena);
	$cadena = str_ireplace("USERS", "", $cadena);
	$cadena = str_ireplace("USUARIOS", "", $cadena);
	$cadena = str_ireplace("UNION", "", $cadena);
	$cadena = str_ireplace("DATABASE", "", $cadena);
	$cadena = str_ireplace("TRUNCATE", "", $cadena);
	$cadena = str_ireplace("EACH", "", $cadena);
	$cadena = str_ireplace("WHILE", "", $cadena);
	$cadena = str_ireplace("CREATE", "", $cadena);
	$cadena = str_ireplace("TABLE", "", $cadena);
	$cadena = str_ireplace("TITLE", "", $cadena);
	$cadena = str_replace("nbsp;", "", $cadena);
//teclado celular
	$cadena = str_ireplace("@", "", $cadena);
	$cadena = str_ireplace("#", "", $cadena);
  $cadena = str_ireplace("∉", "", $cadena);
  $cadena = str_ireplace("%", "", $cadena);
	$cadena = str_ireplace("&", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("+", "", $cadena);
	$cadena = str_ireplace("(", "", $cadena);
	$cadena = str_ireplace(")", "", $cadena);
	$cadena = str_ireplace("¡", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("*", "", $cadena);
	$cadena = str_ireplace("/", "", $cadena);

	/*$cadena=preg_replace("/([^A-Za-z0-9])/","", $cadena);*/
	//caracteres especiales
	$cadena=trim($cadena);
	$cadena=preg_replace('/[^a-zA-Z0-9,-]/', ' ', $cadena);
	//reemplaza varios espacios por nada
  $cadena = preg_replace('/( ){2,}/u','',$cadena);
	if($mayus_minus=="minusculas"){
	$cadena=strtolower($cadena);
	}
	if($mayus_minus=="mayusculas"){
	$cadena=strtoupper($cadena);
	}

	return $cadena;
}
?>
