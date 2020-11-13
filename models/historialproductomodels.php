<?php
	class Historialproducto extends Applicationbase{
		private $tabla="wc_historiaproducto";
		function grabaHistorialProducto($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function actualizaHistorialProducto($data,$idHistorialProducto){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idhistoriaproducto=$idHistorialProducto");
			return $exito;
		}
		function buscaxordenxproducto($idOrdenCompra,$idproducto){
			$data=$this->leeRegistro($this->tabla,"","idordencompra=$idOrdenCompra and idproducto=$idproducto","");
			return $data;
		}
	}
?>