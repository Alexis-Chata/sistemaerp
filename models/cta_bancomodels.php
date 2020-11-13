<?php 
	class cta_banco extends  Applicationbase{
		private $tabla="wc_cta_banco";

		function listado(){
			$data=$this->leeRegistro($this->tabla,"","estado=1","");
			return $data;
		}
		function buscarxId($idctabanco){
			$data=$this->leeRegistro($this->tabla,"","estado=1 and idctabanco='$idctabanco'","");
			return $data;
		}
		function buscaxIdBanco($idbanco){
			$data=$this->leeRegistro("wc_cta_banco cb inner join wc_almacen a on cb.`idempresa`=a.`idalmacen`","","cb.`idbanco`='$idbanco' and a.`estado`=1 and cb.`estado`=1","");
			return $data;
		}
		function listaBanco(){
			$data=$this->leeRegistro("wc_cta_banco cb inner join wc_almacen a on cb.`idempresa`=a.`idalmacen` inner join wc_banco b on b.`idbanco`=cb.`idbanco`",""," a.`estado`=1 and cb.`estado`=1","");
			return $data;
		}
		function actualiza($data,$idctabanco){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idctabanco='$idctabanco'");
			return $exito;
		}
		function cambiaEstado($idctabanco){
			$exito=$this->inactivaRegistro($this->tabla,"idctabanco=$idctabanco");
			return $exito;
		}
		function graba($data){
			$estado=$this->grabaRegistro($this->tabla,$data);

			return $estado;
		}
		function buscarxBancoxCta($idbanco="",$idctabanco=""){
			$data=$this->leeRegistro("wc_cta_banco cb  inner join wc_banco b on b.`idbanco`=cb.`idbanco`","","","");
			return $data;
		}
	}

 ?>