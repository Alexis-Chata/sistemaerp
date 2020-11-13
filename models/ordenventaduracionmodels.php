<?php 

	Class ordenventaduracion extends Applicationbase{
		var $tabla='wc_ordenventaduracion';
		function grabaOrdenVentaDuracion($data){
			$this->grabaRegistro($this->tabla,$data);
		}
		function actualizaOrdenVentaDuracion($data,$filtro){
			$this->actualizaRegistro($this->tabla,$data,$filtro);
		}
		function listaOrdenVentaDuracion($id,$referencia){
			$data=$this->leeRegistro($this->tabla,"","idordenventa='".$id."' and referencia='".$referencia."'","","");
			return $data;
		}
		function listaOrdenVentaDuracionxOrdenVenta($idordenventa){
			$data=$this->leeRegistro($this->tabla,"","idordenventa='$idordenventa' ","","");
			return $data;
		}

	}
 ?>