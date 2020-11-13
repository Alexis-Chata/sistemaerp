<?php
Class Modulo extends Applicationbase{
	private $tabla;
	function __construct(){
		$this->tabla="wc_modulo";
	}
	function ListadoModulo(){
		$a=$this->leeRegistro($this->tabla,"","","Estado desc","");
		return $a;
	}
	function ListadoPadre(){
		$a=$this->leeRegistro($this->tabla,"","","","");
		return $a;
	}
	
	function EstadoModulo($idModulo){
		$exito=$this->cambiaEstado($this->tabla,"Id=".$idModulo);
		return $exito;		
	}
	function grabaModulo($data){
		$exito=$this->grabaRegistro($this->tabla,$data);
		return $exito;
	}
	function buscar($id){
		$modulo=$this->leeRegistro($this->tabla,"","Id=".$id,"","");
		return $modulo;
	}
	function actualizaModulo($data,$filtro){
		$exito=$this->actualizaRegistro($this->tabla,$data,$filtro);
		return $exito;
	}
}

?>