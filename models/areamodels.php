<?php
	class Area extends  Applicationbase{
		private $tabla="wc_area";
		function listado(){
			$data=$this->leeRegistro($this->tabla,"","","");
			return $data;
		}
		function grabar($data){
			$estado=$this->grabaRegistro($this->tabla,$data);
			return $estado;
		}
		function actualiza($data,$filtro){
			$exito=$this->actualizaRegistro($this->tabla,$data,$filtro);
			return $exito;
		}
		function buscar($idArea){
			$data=$this->leeRegistro($this->tabla,"","idarea=$idArea","");
			return $data;
		}
		function cambiaEstado($idArea){
			$exito=$this->cambiaEstado($this->tabla,"idarea=$idArea");
			return $exito;
		}
		function listadoOptionsarea($idDistrito){
			$data=$this->leeRegistro($this->tabla,"","iddistrito=".$idDistrito,"");
			return $data;
		}
	}
?>