<?php
	class ClienteSucursal extends Applicationbase{
		private $tabla="wc_clientesucursal";
		function grabaClienteSucursal($data){
			$data['estado']=1;
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function nombreclientexidcliente($id){
			$data=$this->leeRegistro($this->tabla,"nombre");

		}
		function buscarxid($id){
			$data=$this->leeRegistro($this->tabla,"","idcliente='$id' and estado=1","","");
			return $data;
		}
		function buscarxidclientesucursal($idclientesucursal){
			$data=$this->leeRegistro($this->tabla,"","idclientesucursal='$idclientesucursal'","","");
			return $data;
		}
		function actualizaClienteSucursal($data,$id){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idcliente=".$id);
			
			return $exito;
		}
		function actualizaSucursal($data,$id){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idclientesucursal=".$id);
			
			return $exito;
		}
		function cambiaEstado($idclientesucursal){
			$exito=$this->inactivaRegistro($this->tabla,"idclientesucursal='$idclientesucursal'");
			return $exito;
		}

	}
?>