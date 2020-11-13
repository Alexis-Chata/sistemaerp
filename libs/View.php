<?php
class View
{
	public $template;
	function __construct()
	{
	}
 
	public function show($name, $vars = array())
	{
		$config = Config::singleton();
		$path = VIEWS. $name;
		if (file_exists($path) == false)
		{
			trigger_error ('PLantilla ' . $path . ' NO EXISTE.', E_USER_NOTICE);
			return false;
		}
 
		if(is_array($vars))
		{
                    foreach ($vars as $key => $value)
                    {
                	$$key = $value;
                    }
        }
     $default="views/plantillas/celestium.php";

		if(isset($this->template) &&  !empty($this->template)){
			$rutap="views/plantillas/".$this->template.".phtml";
			if(is_file($rutap)){
				include($rutap);
			}
		}else{
			include($default);
		}
	
	}

	public function newshow($name, $vars = array())
	{
		$config = Config::singleton();
		$path = VIEWS. $name.".phtml";
		if (file_exists($path) == false)
		{
			trigger_error ('PLantilla ' . $path . ' NO EXISTE.', E_USER_NOTICE);
			return false;
		}
 
		if(is_array($vars))
		{
                    foreach ($vars as $key => $value)
                    {
                	$$key = $value;
                    }
        }
     $default="views/plantillas/celestium.php";

		if(isset($this->template) &&  !empty($this->template)){
			$rutap="views/plantillas/".$this->template.".phtml";
			if(is_file($rutap)){
				include($rutap);
			}
		}else{
			include($default);
		}
	
	}	
        
        public function showImpr($name, $vars = array())
	{
		$config = Config::singleton();
		$path = VIEWS. $name;
		if (file_exists($path) == false)
		{
			trigger_error ('PLantilla ' . $path . ' NO EXISTE.', E_USER_NOTICE);
			return false;
		}
 
		if(is_array($vars))
		{
                    foreach ($vars as $key => $value)
                    {
                	$$key = $value;
                    }
        }
     $default="views/plantillas/impresion.php";

		if(isset($this->template) &&  !empty($this->template)){
			$rutap="views/plantillas/".$this->template.".phtml";
			if(is_file($rutap)){
				include($rutap);
			}
		}else{
			include($default);
		}
	
	}
}
?>