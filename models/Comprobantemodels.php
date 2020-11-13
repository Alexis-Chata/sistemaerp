<?php
	class Comprobante extends Applicationbase{
		private $tabla="wc_factura";
		function listar(){
			$data=$this->leeRegistro($this->tabla,"","","");
			return $data;
		}
		function grabar($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function buscar($id){
			$data=$this->leeRegistro($this->tabla,"","id=$id","");
			return $data;
		}
		function actualizar($data,$idEmpaque){
			$exito=$this->actualizaRegistro($this->tabla,$data,"id=$id");
			return $exito;
		}
		function eliminar($id){
			$exito=$this->cambiaEstado($this->tabla,"id=$id");
			return $exito;
		}
		function buscarxruc($ruc){
			$data=$this->leeRegistro($this->tabla,"","ruc=".$ruc,"","");
			return $data;
		}
	}
?>