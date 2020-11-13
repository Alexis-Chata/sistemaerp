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
		
                require_once('fpdf.php');
		define('FPDF_FONTPATH', LIBS.'font/');
		
		
		/**
		 * Cargando a los Controladores y Modelos Principales.
		 */
		require(LIBS.CONTROLLER_BASE . '.php');
		require(LIBS.MODEL_BASE . '.php');

		/**
		 *Definiendo a nuestros controladores:
		 */
		$controllerName=(!empty($_REQUEST['controlador'])?strtolower($_REQUEST['controlador']).'controller':'indexcontroller');
		$modelName=(!empty($_REQUEST['controlador'])?strtolower($_REQUEST['controlador']).'models':'');
		$actionName=(!empty($_REQUEST['accion'])?$_REQUEST['accion']:'index');
	
		/**
		 * Defino la ruta del controaldor y verifico si existe el fichero, luego lo cargo 
		 */
			$controllerPath=CONTROLLERS.$controllerName.'.php';
			$modelPath=MODELS. $modelname . '.php';
		
		if(is_readable($controllerPath)){
			
			require($controllerPath);
			(is_readable($modelPath)?(require($modelPath)):'');
			
			if(is_callable($controllerName,$actionName)){
				session_start();
				if($_SESSION['Autenticado']==true || $actionName=='valida'){
					
					

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
					require_once(CONTROLLERS.'actorcontroller.php');
					$controller = new actorcontroller();
					$controller->login();	
				}
				
			}
			else{
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
	}
}
?>