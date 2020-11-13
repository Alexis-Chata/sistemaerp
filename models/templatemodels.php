<?php
	class Template extends Applicationbase{
		private $tabla='template';
		function listadoTemplates(){
			$template=$this->leeRegistro($this->tabla,"","estado=1","");
			return $template;
		}
		function actualizaTemplate($data,$filtro){
			$exito=$this->actualizaRegistro($this->tabla,$data,$filtro);
			return $exito;
		}
		function buscaTemplate($idTemplate){
			$template=$this->leeRegistro($this->tabla,"","id=".$idTemplate." AND estado='1'","");
			return $template;
		}
		function cambiaEstadoTemplate($idTemplate){
			$estado=$this->cambiaEstado($this->tabla,"id=".$idTemplate);
			return $estado;
		}
		function grabaTemplate($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
	}
?>