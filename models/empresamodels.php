<?php 

	class empresa extends Applicationbase{
		private $tabla='wc_empresa';
		private $tabla2="wc_tipoempresa";
		function listadoTipoEmpresa(){
			$data=$this->leeRegistro($this->tabla2,"","estado=1","");
			return $data;
		}
		function listadoEmpresaxIdTipoEmpresa($idtipoempresa){
			$data=$this->leeRegistro($this->tabla,"","idtipoempresa='$idtipoempresa' and estado=1","");
			return $data;
		}


		function listado(){
			$data=$this->leeRegistro($this->tabla,"","estado=1","");
			return $data;
		}
		function buscaxId($idempresa){
			$data=$this->leeRegistro($this->tabla,"","idempresa='$idempresa' and estado=1","");
			return $data;
		}
		function actualiza($data,$idempresa){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idempresa=$idempresa");
			return $exito;
		}
		function cambiaEstado($idempresa){
			$exito=$this->inactivaRegistro($this->tabla,"idempresa=$idempresa");
			return $exito;
		}
		function graba($data){
			$estado=$this->grabaRegistro($this->tabla,$data);

			return $estado;
		}
	}

 ?>