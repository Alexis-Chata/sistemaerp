<?php
	class ClienteVendedor extends Applicationbase{
		private $tabla="wc_clientevendedor";
		function grabaClienteVendedor($data){
			$data['estado']=1;
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}

		function buscarxid($id){
			$data=$this->leeRegistro($this->tabla,"idcliente,idvendedor","idcliente='$id'","","");
			return $data;
		}
		function actualizaClienteVendedor($id,$data){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idcliente=".$id);
			return $exito;
		}
	}
?>