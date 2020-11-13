<?php 
	class banco extends  Applicationbase{
		private $tabla="wc_banco";

		function listado(){
			$data=$this->leeRegistro($this->tabla,"","estado=1","");
			return $data;
		}
		function buscaxId($idbanco){
			$data=$this->leeRegistro($this->tabla,"","idbanco='$idbanco' and estado=1","");
			return $data;
		}
		function actualiza($data,$idbanco){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idbanco=$idbanco");
			return $exito;
		}
		function cambiaEstado($idbanco){
			$exito=$this->inactivaRegistro($this->tabla,"idbanco=$idbanco");
			return $exito;
		}
		function graba($data){
			$estado=$this->grabaRegistro($this->tabla,$data);

			return $estado;
		}
	}

 ?>