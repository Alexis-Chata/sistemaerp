<?php
	class Proveedorproducto extends Applicationbase{
		private $tabla="wc_proveedorproducto";
		function listado(){
			$data=$this->leeRegistro($this->tabla,"idproveedorproducto,idproveedor,idproducto","estado=1","");
			return $data;
		}
		function grabaProveedorProducto($data){
			$id=$this->grabaRegistro($this->tabla,$data);
			return $id;
		}
		function actualizaProveedorProducto($data,$idMarca){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idmarca=$idMarca");
			return $exito;
		}
		function eliminaProveedorProducto($id){
			$exito=$this->cambiaEstado($this->tabla,"idproveedorproducto=$id");
			return $exito;
		}
		function getnombrexid($id){
			$data=$this->leeRegistro($this->tabla,"","idproveedorproducto=$id","");
			return $data[0][0];
		}
	}
?>