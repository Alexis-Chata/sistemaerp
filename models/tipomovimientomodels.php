<?php
	class Tipomovimiento extends Applicationbase{
		private $tabla="wc_tipomovimiento";
		function listadoTiposmovimiento(){
			$data=$this->leeRegistro($this->tabla,"","","");
			return $data;
		}
		function grabaTipomovimiento($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function editaTipoMovimiento($idTipoMovimiento){
			$data=$this->leeRegistro($this->tabla,"","idtipomovimiento=$idTipoMovimiento","");
			return $data;
		}
		function actualizaTipoMovimiento($data,$idTipoMovimiento){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idtipomovimiento=$idTipoMovimiento");
			return $exito;
		}
		function eliminaTipoMovimiento($idTipoMovimiento){
			$exito=$this->cambiaEstado($this->tabla,"idtipomovimiento=$idTipoMovimiento");
			return $exito;
		}
}
?>