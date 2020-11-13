<?php
class WCController
{
	static function main()
	{
 		/**
 		 * Cargando librearias principales.
 		 */
		require LIBS.'Config.php'; //de configuracion
		require LIBS.'View.php'; //Mini motor de plantillas
		require 'config.php'; //Archivo con configuraciones.	
		
		/**
		 * Cargando librearias de terceros
		 */
		
		define('FPDF_FONTPATH',Libs.'font/');
		require('fpdf.php');
		
		
		/**
		 * Cargando a los Controladores y Modelos Principales.
		 * Controller ---> AppliactionPrincipal
		 * Model -----> ApplicationBase
		 */
			require(LIBS.CONTROLLER_BASE . '.php');
			require(LIBS.MODEL_BASE . '.php');
/*
		 		$controllerGeneral=$config->get('controllersFolder') . $config->get('controllerGeneral') . '.php';
 				$modelBase=$config->get('modelsFolder') . $config->get('modelBase') . '.php';
 				require $controllerGeneral;
 				require $modelBase;*/


/**
 *Definiendo a nuestros controladores:
 */
	$controllerName=(!empty($_REQUEST['controlador'])?$_REQUEST['controlador'].'Controller':'indexController');
	$modelName=(!empty($_REQUEST['controlador'])?$_REQUEST['controlador'].'models':'');
	$actionName=(!empty($_REQUEST['accion'])?$_REQUEST['accion']:'index');
	
//		Formamos el nombre del Controlador o en su defecto, tomamos que es el IndexController
//		if(!empty($_GET['controlador'])){
//		      	$controllerName = $_GET['controlador'] . 'controller';
//		      	$modelname=$_GET['controlador'].'models';
//		      if(!empty($_GET['accion'])){
//				
//		      	$actionName = $_GET['accion'];
//				}else{
//					$actionName = "index";
//				}
//		}
//		else{
//		     $controllerName = "indexcontroller";
//		     $_GET['controlador']=$_GET['accion']="index";
//		     $actionName = "index";
//		}

/**
 * Defino la ruta del controaldor y verifico si existe el fichero, luego lo cargo 
 */
	$controllerPath=CONTROLLERS.$controllerName.'.php';
	$modelPath=MODELS. $modelname . '.php';	
	if(is_readable($controllerPath)){
		
		require($controllerPath);
		(is_readable($modelPath)?(require($modelPath)):'');
		
		if(is_callable($controllerName,$actionName)){

			/**
			 * Verifico inicio de Sesion.
			 */
			if($_SESSION['Autenticado']==true){
				
				$midir=opendir(MODELS);
			      while($archivo=readdir($midir))
			      {
					if(ereg("models.php",$archivo)){
			      		$models=MODELS.$archivo;
			      		require_once($models);
					} 
			      }
				closedir();
				
				$controller = new $controllerName();
				$controller->$actionName();	
				
			}else {
				require(CONTROLLERS.'actorController.php');
				$controller = new actorcontroller();
				$controller->login();	
			}
			
		}
		else{
			$_REQUEST['error']=true;
			$datos['mensaje']="Error :: Llamada a una accion : (".$actionName.") que no existe o esta vacia.";
			$datos['descripciom']="Por favor revisar la sintaxis o la existencia del metodo en la clase.";
			$vistaDefault=New View();
			$vistaDefault->show('error.php', $datos);
			return false;
		}
		
	}else {
		$datos['mensaje']="Error :: Llamada a un controlador : (".$controllerName.") que no existe.";
		$datos['descripciom']="Por favor revisar la sintaxis o la existencia del archivo en la carpeta de controladores.";
		$vistaDefault=New View();
		$vistaDefault->show('error.php', $datos);
		return false;
	}

//				 $controllerPath = $config->get('controllersFolder') . $controllerName . '.php';
//					//Incluimos el fichero que contiene nuestra clase controladora solicitada.
//					if(is_file($controllerPath)){
//					     require $controllerPath;
//					}
//					else{
//						$datos['mensaje']="Error :: Llamada a un controlador : (".$controllerName.") que no existe.";
//						$datos['descripciom']="Por favor revisar la sintaxis o la existencia del archivo en la carpeta de controladores.";
//						$vistaDefault=New View();
//						$vistaDefault->show('error.php', $datos);
//						return false;
//					}
				
/*					$modelPath=$config->get('modelsFolder') . $modelname . '.php';
					if(is_file($modelPath)){
						require $modelPath;
					}*/

//					if (!is_callable(array($controllerName, $actionName))){
//						$_REQUEST['error']=true;
//						$datos['mensaje']="Error :: Llamada a una accion : (".$controllerName.") que no existe o esta vacia.";
//						$datos['descripciom']="Por favor revisar la sintaxis o la existencia del archivo en la carpeta de controladores.";
//						$vistaDefault=New View();
//						$vistaDefault->show('error.php', $datos);
//						return false;
//					}else{
//	
//						//Verifico inicios de SESION
//						$ruta=$_GET['controlador']."/".$_GET['accion'];
//						echo $ruta;
//						switch ($ruta){
//							case "actor/login":
//							case "actor/salir":
//							case "actor/valida":
//							case "index/index":
//								session_start();
//								$_SESSION['Autenticado']=true;
//								break;
//						}
//						session_start();
//			      		if($_SESSION['Autenticado']){
//
//						//Cargo todos los modelos
//							$midir=opendir($config->get('modelsFolder'));
//						      while($archivo=readdir($midir))
//						      {
//								if(ereg("models.php",$archivo)){
//						      		$models=$config->get('modelsFolder').$archivo;
//						      		require_once($models);
//								} 
//						      }
//							closedir();		
//						//Fin de cargo todos los modelos
//							$controller = new $controllerName();
//							$controller->$actionName();
//
//						}else{
//
//								$datos['mensaje']="Error :: Acceso Incorrecto al Sistema";
//								$datos['descripcion']="Debe autenticarse con las credenciales usuario y clave correctos antes de acceder al Sistema @Celestium";
//								$vistaDefault=New View();
//								$_REQUEST['error']=true;
//								$vistaDefault->show('autenticate.php', $datos);
//								return false;
//						}
//					}
	}
}
?>