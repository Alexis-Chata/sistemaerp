<?php 
class Tipogasto extends Applicationbase{
	private $tabla="wc_tipogasto";

	function graba($data){
		$exito=$this->grabaRegistro($this->tabla,$data);
		return $exito;
	}
	function actualiza($data,$idTipoGasto){
		$exito=$this->actualizaRegistro($this->tabla,$data,"idtipogasto=$idTipoGasto");
		return $exito;
	}
	function buscaxid($idTipoGasto){
		$data=$this->leeRegistro($this->tabla,"","idtipogasto=$idTipoGasto","");
		return $data;
	}
	function nombreGasto($idTipoGasto){
		$data=$this->leeRegistro($this->tabla,"nombre","idtipogasto=$idTipoGasto","");
		return $data[0]['nombre'];
	}
	function lista(){
		$data=$this->leeRegistro($this->tabla,"","estado=1","");
			
		return $data;
	}
	function listaPaginado($pagina){
		$data=$this->leeRegistroPaginado($this->tabla,"","","",$pagina);
		return $data;
	}
	function listaxCriterio($criterio){
		$data=$this->leeRegistro($this->tabla,"","idcriterio='$criterio' and estado=1","");
		return $data;
	}
	
}

?>