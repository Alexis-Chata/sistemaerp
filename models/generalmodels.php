<?php
Class general extends Applicationbase{
	private $tabla="wc_opciones";
	function buscaOpcionexurl($url){
		$lista=$this->leeRegistro($this->tabla,"nombre","url='$url'","");
		return $lista[0]['nombre'];
	}
	function buscaModulosxurl($url){
		$lista=$this->leeRegistro($this->tabla,"nombre","orden=0 and idmodulo=(select idmodulo from wc_opciones where url='$url')","");
		return $lista[0]['nombre'];
	}
}
	