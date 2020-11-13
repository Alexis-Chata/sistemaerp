<?php
	class Unidadmedida extends Applicationbase{
		private $tabla="wc_unidadmedida";
		function listadoUnidadmedidas(){
			$dato=$this->leeRegistro($this->tabla,"","estado=1","");
			return $dato;
		}
		function listadoTotal(){
			$dato=$this->leeRegistro($this->tabla,"","","");
			return $dato;
		}
		function grabaUnidadmedida($dato){
			$exito=$this->grabaRegistro($this->tabla,$dato);
			return $exito;
		}
		function buscaUnidadMedida($idUnidadMedida){
			$data=$this->leeRegistro($this->tabla,"","idunidadmedida=$idUnidadMedida","");
			return $data;
		}
		function actualizaUnidadMedida($data,$idUnidadMedida){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idunidadmedida=$idUnidadMedida");
			return $exito;
		}
		function eliminaUnidadMedida($idUnidadMedida){
			$exito=$this->cambiaEstado($this->tabla,"idunidadmedida=$idUnidadMedida");
			return $exito;
		}
	}
?>