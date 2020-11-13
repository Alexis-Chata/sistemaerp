<?php
define('DS', DIRECTORY_SEPARATOR);
define('LIBS',realpath(dirname(__FILE__)).DS.'libs'.DS);
define('PUBLIC',realpath(dirname(__FILE__)).DS.'public'.DS);
define('CONTROLLERS',realpath(dirname(__FILE__)).DS.'controllers'.DS);
define('MODELS',realpath(dirname(__FILE__)).DS.'models'.DS);
define('VIEWS',realpath(dirname(__FILE__)).DS.'views'.DS);


define('CONTROLLER_BASE','application.general');
define('MODEL_BASE','application.base');

//$config = Config::singleton();
//$config->set('controllersFolder', 'controllers/');
//$config->set('controllerGeneral', 'application.general');
//$config->set('modelsFolder', 'models/');
//$config->set('modelBase', 'application.base');
//$config->set('viewsFolder', 'views/');
?>