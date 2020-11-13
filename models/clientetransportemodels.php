<?php
	class ClienteTransporte extends Applicationbase{
		private $tabla="wc_clientetransporte";
		function grabaClienteTransporte($data){
			$data['estado']=1;
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function nombreclientexidcliente($id){
			$data=$this->leeRegistro($this->tabla,"nombre");

		}
		function buscarxid($id){
			$data=$this->leeRegistro($this->tabla,"idcliente,idtransporte","idcliente='$id'","","");
			return $data;
		}
		function buscaTransportexIdCliente($idCliente){
			$data=$this->leeRegistro("wc_clientetransporte ct inner join wc_transporte t on ct.idtransporte=t.idtransporte","","idcliente='$idCliente'","","");
			return $data;
		}
		function actualizaClienteTransporte($id,$data){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idclientetransporte=".$id);
			return $exito;
		}
	}
?>