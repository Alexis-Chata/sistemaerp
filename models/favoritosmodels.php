<?php 

	class Favoritos extends Applicationbase{
		private $tabla='wc_favoritos';
		function buscaxIdActor($idactor){
			$data=$this->leeRegistro($this->tabla,"","idactor='$idactor' and estado=1","");
			return $data;
		}

		function listado(){
			$data=$this->leeRegistro($this->tabla,"","estado=1","");
			return $data;
		}
		function buscaxId($idfavorito){
			$data=$this->leeRegistro($this->tabla,"","idfavorito='$idfavorito' and estado=1","");
			return $data;
		}
		function actualiza($data,$idfavorito){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idfavorito=$'idfavorito'");
			return $exito;
		}
		function cambiaEstado($idfavorito){
			$exito=$this->inactivaRegistro($this->tabla,"idfavorito='$idfavorito'");
			return $exito;
		}
		function graba($data){
			$estado=$this->grabaRegistro($this->tabla,$data);

			return $estado;
		}
	}

 ?>