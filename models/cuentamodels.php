<?php
	class Cuenta extends  Applicationbase{
		private $tabla="wc_cuenta";
		private $tablas='wc_actor,wc_cuenta,wc_ordenventa';		
		function listado(){
			$cuenta=$this->leeRegistro($this->tabla,"","","");
			return $cuenta;
		}
		function graba($data){
			$id=$this->grabaRegistro($this->tabla,$data);
			return $id;
		}
		function actualiza($data,$filtro){
			$exito=$this->actualizaRegistro($this->tabla,$data,$filtro);
			return $exito;
		}
		function buscar($id){
			$data=$this->leeRegistro($this->tabla,"","idcuenta=$id","");
			return $data;
		}
		function eliminar($id){
			$exito=$this->cambiaEstado($this->tabla,"idcuenta=$id");
			return $exito;
		}
		function listarxvendedor($idvendedor){
			$data=$this->leeRegistro($this->tabla,"","situacionpago=0 and idvendedor=".$idvendedor,"");
			return $data; 
		}
		function listarxzona($idzona){
			$data=$this->leeRegistro($this->tabla,"","idzona=".$idzona,"");
			return $data;
		}
		function listarxcobrar(){
			$data=$this->leeRegistro($this->tabla,"","situacionpago=0","");
			return $data;
		}		
		function contarCuenta(){
			$cantidadMovimiento=$this->contarRegistro($this->tabla);
			return $cantidadMovimiento;
		}
		function listadoCuentasxCobrar($inicio,$pagina){
			$data=$this->leeRegistro($this->tabla,"","not situacionpago=1","","LIMIT $inicio,$pagina");
			return $data;
		}		
		function cobrarCuenta($id){			
			$exito=$this->actualizaRegistro($this->tabla,"situacionpago=1","idcuenta=".$id);
			return $exito;
		}
		function cancelarCobro($id){
			$exito=$this->actualizaRegistro($this->tabla,"situacionpago=0","idcuenta=$id");
			return $exito;
		}
		function listarxcliente($idcliente){
			$data=$this->leeRegistro($this->tabla,"","idcliente=".$idcliente,"");
			return $data; 
		}
		function listarxguia($id){
			$data=$this->leeRegistro($this->tabla,"","idordenventa=".$id,"");
			return $data; 
		}
	}
?>