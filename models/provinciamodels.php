<?php
	class Provincia extends Applicationbase{
		private $tabla="wc_provincia";	
		private $tablaD="wc_distrito";	
		function listado($idDepartamento){
			$data=$this->leeRegistro($this->tabla,"","iddepartamento=$idDepartamento","","");
			return $data;
		}			
		function grabar($data){
			$this->grabaRegistro($this->tabla,$data);
		}
		function buscarxid($id){
			$data=$this->leeRegistro($this->tabla,"","idProvincia=".$id,"","");
			return $data;
		}
		function actualizar($id,$data){
			$this->actualizaRegistro($this->tabla,$data,"id=".$id);
		}
		function eliminar($id){
			$this->eliminaRegistro($this->tabla,"id=".$id);
		}
		function listadoOptionsprovincia($idDepartamento){			
			$data=$this->leeRegistro($this->tabla,"","iddepartamento=".$idDepartamento,"");
			return $data;
		}
	}
?>