<?php
error_reporting(0);
ini_set("memory_limit","1024M");
ini_set("diplay_error","on");
ob_start();
$url_items = explode("/", $_REQUEST['url']);
// $_REQUEST['controlador'] = DesencriptaURL($url_items[0]); 
// $_REQUEST['accion'] = DesencriptaURL($url_items[1]); 
// $_REQUEST['id'] = DesencriptaURL($url_items[2]);
$_REQUEST['controlador'] = $url_items[0]; 
$_REQUEST['accion'] = $url_items[1]; 
$_REQUEST['id'] = $url_items[2];
$_REQUEST['all_parameters'] = $url_items;

unset($url_items[0], $url_items[1]);
$_REQUEST['parameters'] = array_values($url_items);

$_REQUEST['controlador'] = escapeshellcmd($_REQUEST['controlador']);
$_REQUEST['accion'] = escapeshellcmd($_REQUEST['accion']);

//Valido para POST y GET
$_POST['accion'] = $_GET['accion'] = $_REQUEST['accion'];
$_POST['controlador'] = $_GET['controlador'] = $_REQUEST['controlador'];

/** reinicia las variables de aplicaci�n cuando cambiamos 
 * entre una aplicaci�n y otra
 */
chdir('..');
require('config.php');
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', realpath(dirname(__FILE__)) . DS);
//Incluimos el FrontController
require 'libs/WCController.php';
//Lo iniciamos con su método estático main.
//echo Encripta('index');
WCController::main();

	// function DesencriptaURL($encriptado){
	//   		$original="";
	//   		$total=strlen($encriptado);
	//   		for($i=0;$i<$total;$i+=2){
	//   			$pareja=$encriptado[$i].$encriptado[$i+1];
	//   			$original.=chr(hexdec($pareja));
	//   		}
	//   		return $original;
	// }
	// function Encripta($original){
	// 	$encriptado="";
	// 	$total=strlen($original);
	// 	for($i=0;$i<$total;$i++){
	// 		$encriptado.=dechex(ord($original[$i]));
	// 	}
	// 	return $encriptado;
	// }
?>

