<?php
	/*Class dataBase{
		
		private $localhost;
		private $usuario_BD;
		private $clave_BD;
		private $basedatos;
		
		private function conectar(){
			$var_config=parse_ini_file("config.ini",true);
			$modo=$var_config['Globals']['Modo'];
			$this->localhost=$var_config[$modo]['Servidor'];
			$this->usuario_BD=$var_config[$modo]['Usuario'];
			$this->clave_BD=$var_config[$modo]['Clave'];
			$this->basedatos=$var_config[$modo]['NombreBBDD'];
			$link=mysql_connect($this->localhost,$this->usuario_BD,$this->clave_BD) or die("Error al conectar :".mysql_error());
			mysql_select_db($this->basedatos,$link) or die("Error al elegir la BBDD :".mysql_error());
		}
		 public function consult($sql){
			$this->conectar();
			$rpta=mysql_query($sql);
			while($f=mysql_fetch_assoc($rpta)){
				$salida[]=$f;
			}
			return $salida;
	    } 
	}
	$data=new dataBase();
	$codDepartamento=$_REQUEST['cod'];*/
	echo "Bienvenido";
?>