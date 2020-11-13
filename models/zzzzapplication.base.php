<?php
Class Applicationbase{
	
	private $localhost;
	private $usuario_BD;
	private $clave_BD;
	private $basedatos;
	
	function __construct(){
		$var_config=parse_ini_file("config.ini",true);
		$modo=$var_config['Globals']['Modo'];
		$this->localhost=$var_config[$modo]['Servidor'];
		$this->usuario_BD=$var_config[$modo]['Usuario'];
		$this->clave_BD=$var_config[$modo]['Clave'];
		$this->basedatos=$var_config[$modo]['NombreBBDD'];
	}
	
	private function conectar(){
		$var_config=parse_ini_file("config.ini",true);
		$modo=$var_config['Globals']['Modo'];
		$this->localhost=$var_config[$modo]['Servidor'];
		$this->usuario_BD=$var_config[$modo]['Usuario'];
		$this->clave_BD=$var_config[$modo]['Clave'];
		$this->basedatos=$var_config[$modo]['NombreBBDD'];
		$link=mysql_connect($this->localhost,$this->usuario_BD,$this->clave_BD) or die("Error al conectar :".mysql_error());
		mysql_query("SET NAMES utf8", $link);
		mysql_select_db($this->basedatos,$link) or die("Error al elegir la BBDD :".mysql_error());
	}
	
	private function desconectar(){
		mysql_close() or die("Error al intentar desconectar del servidor de BBDD : ".mysql_error());	
	}
	
	protected function EjecutaConsulta($sql){
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			$num_resultado=mysql_num_rows($resultado);
			for($i=0;$i<$num_resultado;$i++){
				$data[]=mysql_fetch_assoc($resultado);
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
	
	protected function leeRegistro($tabla,$columnas,$filtro,$orden,$opciones=""){
		$tabla=strtolower($tabla);
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." from ".$tabla;
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }			
		$this->conectar();
		$sql=strtolower($sql);
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			$num_resultado=mysql_num_rows($resultado);
			for($i=0;$i<$num_resultado;$i++){
				$fila=mysql_fetch_array($resultado);
				$data[]=$fila;
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
        
        protected function devuelveSQL($tabla,$columnas,$filtro,$orden,$opciones=""){
		$tabla=strtolower($tabla);
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." from ".$tabla;
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }			
	
		$sql=strtolower($sql);
		return $sql;
	}
	
	protected function leeRegistro1($tabla,$columnas,$filtro,$orden,$opciones=""){
		$tabla=strtolower($tabla);
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." from ".$tabla;
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }
		$data=array();		
		$this->conectar();
		
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			while ($row = mysql_fetch_array($resultado)){
				$data[]=array("value" => $row['codigo'],
								"label" => $row['codigo']." ".$row['nompro']);
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
	
	protected function leeRegistro2($tablas,$columnas,$filtro,$orden,$opciones=""){
		$tablas_s=split(",",strtolower($tablas));
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." From ".$tablas_s[0]." as t1 ";
		$sql.="Inner Join ".$tablas_s[1]." as t2 on t1.id".substr($tablas_s[1],3)."=t2.id".substr($tablas_s[1],3);
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			$num_resultado=mysql_num_rows($resultado);
			for($i=0;$i<$num_resultado;$i++){
				$fila=mysql_fetch_array($resultado);
				$data[]=$fila;
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
	//Recupera uniendo las tablas en forma cadena
	
	protected function leeRegistro3($tablas,$columnas,$filtro,$orden,$tipo=1,$opciones=""){
		$tablas_s=split(",",strtolower($tablas));
		if(empty($columnas)){$columnas="*";}
		if($tipo==1){
			$sql="Select ".$columnas." From ".$tablas_s[0]." as t1 ";
			$sql.="Inner Join ".$tablas_s[1]." as t2 on t1.id".substr($tablas_s[1],3)."=t2.id".substr($tablas_s[1],3);
			$sql.=" Inner Join ".$tablas_s[2]." as t3 on t2.id".substr($tablas_s[2],3)."=t3.id".substr($tablas_s[2],3);
		}else{
			$sql="Select ".$columnas." From ".$tablas_s[0]." as t1 ";
			$sql.="Inner Join ".$tablas_s[1]." as t2 on t1.id".substr($tablas_s[1],3)."=t2.id".substr($tablas_s[1],3);
			$sql.=" Inner Join ".$tablas_s[2]." as t3 on t1.id".substr($tablas_s[2],3)."=t3.id".substr($tablas_s[2],3);
		}
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			$num_resultado=mysql_num_rows($resultado);
			for($i=0;$i<$num_resultado;$i++){
				$fila=mysql_fetch_array($resultado);
				$data[]=$fila;
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
	
	protected function leeRegistro4($tablas,$columnas,$filtro,$orden,$opciones=""){
		$tablas_s=split(",",strtolower($tablas));
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." From ".$tablas_s[0]." as t1 ";
		$sql.="Inner Join ".$tablas_s[1]." as t2 on t1.id".substr($tablas_s[1],3)."=t2.id".substr($tablas_s[1],3);
		$sql.=" Inner Join ".$tablas_s[2]." as t3 on t2.id".substr($tablas_s[2],3)."=t3.id".substr($tablas_s[2],3);
		$sql.=" Inner Join ".$tablas_s[3]." as t4 on t3.id".substr($tablas_s[3],3)."=t4.id".substr($tablas_s[3],3);
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }				
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			$num_resultado=mysql_num_rows($resultado);
			for($i=0;$i<$num_resultado;$i++){
				$fila=mysql_fetch_array($resultado);
				$data[]=$fila;
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
	
	protected function leeRegistro40($tablas,$columnas,$filtro,$orden,$opciones=""){
		$tablas_s=split(",",strtolower($tablas));
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." From ".$tablas_s[0]." as t1 ";
		$sql.="Inner Join ".$tablas_s[1]." as t2 on t1.id"."=t2.id".substr($tablas_s[1],3);
		$sql.=" Inner Join ".$tablas_s[2]." as t3 on t1.id"."=t3.id".substr($tablas_s[2],3);
		$sql.=" Inner Join ".$tablas_s[3]." as t4 on t3.id".substr($tablas_s[3],3)."=t4.id".substr($tablas_s[3],3);
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }				
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			$num_resultado=mysql_num_rows($resultado);
			for($i=0;$i<$num_resultado;$i++){
				$fila=mysql_fetch_array($resultado);
				$data[]=$fila;
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
	
	protected function leeRegistro42($tablas,$columnas,$filtro,$orden,$opciones=""){
		$tablas_s=split(",",strtolower($tablas));
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." From ".$tablas_s[0]." as t1 ";
		$sql.="Inner Join ".$tablas_s[1]." as t2 on t1.id".substr($tablas_s[0],3)."=t2.id".substr($tablas_s[0],3);
		$sql.=" Inner Join ".$tablas_s[2]." as t3 on t1.id".substr($tablas_s[2],3)."=t3.id".substr($tablas_s[2],3);
		$sql.=" Inner Join ".$tablas_s[3]." as t4 on t1.id".substr($tablas_s[3],3)."=t4.id".substr($tablas_s[3],3);
		if(!empty($filtro)){ $sql.=" WHERE ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }		
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			$num_resultado=mysql_num_rows($resultado);
			for($i=0;$i<$num_resultado;$i++){
				$fila=mysql_fetch_array($resultado);
				$data[]=$fila;
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
	
	protected function leeRegistro5($tablas,$columnas,$filtro,$orden,$opciones=""){
		$tablas_s=split(",",strtolower($tablas));
		if(empty($columnas)){$columnas="*";}
		$sql="Select ".$columnas." From ".$tablas_s[0]." as t1 ";
		$sql.="Inner Join ".$tablas_s[1]." as t2 on t1.id".substr($tablas_s[1],3)."=t2.id".substr($tablas_s[1],3);
		$sql.=" Inner Join ".$tablas_s[2]." as t3 on t2.id".substr($tablas_s[2],3)."=t3.id".substr($tablas_s[2],3);
		$sql.=" Inner Join ".$tablas_s[3]." as t4 on t3.id".substr($tablas_s[3],3)."=t4.id".substr($tablas_s[3],3);
		$sql.=" Inner Join ".$tablas_s[4]." as t5 on t4.id".substr($tablas_s[4],3)."=t5.id".substr($tablas_s[4],3);
		if(!empty($filtro)){ $sql.=" where ".$filtro; }
		if(!empty($orden)){ $sql.=" order by ".$orden; }
		if(!empty($opciones)){ $sql.=" ".$opciones; }				
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());
		if($resultado){
			$num_resultado=mysql_num_rows($resultado);
			for($i=0;$i<$num_resultado;$i++){
				$fila=mysql_fetch_array($resultado);
				$data[]=$fila;
			}
			$this->desconectar();
			return $data;
		}
		else{
			return mysql_error();
		}
	}
	
	protected function grabaRegistro($tabla,$data){
		$data['estado']=1;
		$tabla=strtolower($tabla);
		$columnas=array_keys($data);
		$sql="Insert Into ".$tabla."(";
		for($i=0;$i<count($columnas);$i++){
			$sql.=$columnas[$i].",";
		}
		$sql.="fechacreacion,usuariocreacion) ";
		$sql.="values(";
		for($i=0;$i<count($data);$i++){
			$sql.="'".$data[$columnas[$i]]."',";
		}
		$sql.="Now(),".$_SESSION['idactor'].")";
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());
		$id=mysql_insert_id();
		$this->desconectar();
		if($resultado){
			return $id;
		}
		else {
			return false;
		}
	}
        
        protected function EjecutarGrabarSentencia($sentencia) {
            $this->conectar();
            $resultado = mysql_query($sentencia) or die(mysql_error());
            if ($resultado) {
                return true;
            } else {
                return mysql_error();
            }
        }


    protected function actualizaRegistro($tabla,$data,$filtro){
		$tabla=strtolower($tabla);
		$columnas=array_keys($data);
		$sql="Update ".$tabla." set ";
		for($i=0;$i<count($columnas);$i++){
			$sql.=$columnas[$i]."='".$data[$columnas[$i]]."',";
		}
		$sql.="fechamodificacion=Now() , usuariomodificacion=".$_SESSION['idactor']."";
		$sql.=" Where ".$filtro;
		$this->conectar();
		$resultado=mysql_query($sql);
		$this->desconectar();
		if($resultado){
			return True;
		}
		else {
			return false;
		}
	}
	
	protected function inactivaRegistro($tabla,$filtro){
		$tabla=strtolower($tabla);
		$sql="Update ".$tabla." set estado=0,";
		$sql.="fechamodificacion=Now() , usuariomodificacion=".$_SESSION['idactor']."";
		if(!empty($filtro)){	
			$sql.=" Where ".$filtro;
		}
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());	
		$this->desconectar();
		if($resultado){
			return true;
		}
		else {
			return false;
		}
	}

	protected function eliminaRegistro($tabla,$filtro){
		$tabla=strtolower($tabla);
		$sql="Delete from ".$tabla." ";
		if(!empty($filtro)){	
			$sql.=" Where ".$filtro;
		}
		$this->conectar();
		$resultado=mysql_query($sql);	
		$this->desconectar();
		return $resultado;	
	}
	
	protected function cambiaEstado($tabla,$filtro){
		$tabla=strtolower($tabla);
		$sql="Update ".$tabla." set 
			estado=ABS((estado-1)*(-1)),";
		$sql.="fechamodificacion=Now() , usuariomodificacion=".$_SESSION['idactor']."";
		if(!empty($filtro)){	
			$sql.=" Where ".$filtro;
		}
		$this->conectar();
		$resultado=mysql_query($sql) or die(mysql_error());	
		$this->desconectar();
		if($resultado){
			return true;
		}
		else {
			return false;
		}
	}
	
	protected function contarRegistro($tabla,$filtro=""){
		$tabla=strtolower($tabla);
		$sql="SELECT * FROM ".$tabla;
		if(!empty($filtro)){
			$sql.=" WHERE ".$filtro;
		}
		$this->conectar();
		$resultado=mysql_query($sql) or die (mysql_error());
		if($resultado){
			$numRegistro=mysql_num_rows($resultado);
			$this->desconectar();
			return $numRegistro;
		}else{
			return mysql_error();
		}
	}
	
	protected function exiteRegistro($tabla,$filtro=""){
		$tabla=strtolower($tabla);
		$sql="SELECT * FROM ".$tabla;
		if(!empty($filtro)){
			$sql.=" WHERE ".$filtro;
		}
		$this->conectar();
		$resultado=mysql_query($sql) or die (mysql_error());
		$exite=0;
		if($resultado){
			if(mysql_num_rows($resultado)>0){
				$exite=1;
			}
			$this->desconectar();
			return $exite;
		}else{
			return mysql_error();
		}
	}
	
	protected function modoFacturacion(){
		$archivoConfig=parse_ini_file("config.ini",true);
		$modoFacturacion=$archivoConfig['ModoFacturacion'];
		return $modoFacturacion;
    }
    
    protected function condicionLetra(){
		$archivoConfig=parse_ini_file("config.ini",true);
		$condicionLetra=$archivoConfig['CondicionLetra'];
		return $condicionLetra;
    }
    
    protected function tipoLetra(){
		$archivoConfig=parse_ini_file("config.ini",true);
		$tipoLetra=$archivoConfig['TipoLetra'];
		return $tipoLetra;
    }
} 
?>