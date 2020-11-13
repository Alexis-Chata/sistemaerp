<?php
	class Orden extends Applicationbase{
		private $tabla="wc_orden";
		private $tabla2="wc_orden as o,wc_actor as a";

		function graba($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function listaxid($idOrden){
			$data=$this->leeRegistro($this->tabla,"","idorden=$idOrden","");
			return $data;
		}
		function buscarxid($id){
			$data=$this->leeRegistro($this->tabla,"","idorden=$id","");
			return $data;
		}
	}
?>