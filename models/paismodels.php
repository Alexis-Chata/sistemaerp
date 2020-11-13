<?php
	class Pais extends Applicationbase{
		private $tabla="wc_pais";
		private $tabla1="wc_departamento";
		function grabar($data){
			$this->grabaRegistro($this->tabla,$data);
		}
		function buscarxid($id){
			$this->leeRegistro($this->tabla,"","id=".$id,"","");
		}
		function actualizar($id,$data){
			$this->actualizaRegistro($this->tabla,$data,"id=".$id);
		}
		function eliminar($id){
			$this->eliminaRegistro($this->tabla,"id=".$id);
		}
		function buscapaisautocomplete($tex){
			$tex=htmlspecialchars($tex,ENT_QUOTES);
			$pais=$this->leeRegistroubigeo($this->tabla,"nombre","nombre LIKE '%$tex%'","");
			return $pais;
		}
		function buscadepautocomplete($tex,$codpais){
			$tex=htmlspecialchars($tex,ENT_QUOTES);
			$pais=$this->leeRegistroubige9($this->tabla1,"nombre","idpais=1 and nomdep LIKE '%$tex%'","");
			return $pais;
		}
		function listadopais(){
			$data=$this->leeRegistro($this->tabla,"","","nombrepais","");
			return $data;
		}
}
?>