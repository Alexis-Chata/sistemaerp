<?php
	class NotaCredito extends Applicationbase{
		private $tabla="wc_notacredito";
		function generaCodigo(){
			$codigo=$this->leeRegistro($this->tabla,"concat(date_format(now(),'%y'),lpad(Max(substr(`Usuario`,7,4)+1),4,0)) as codigo","substr(`Usuario`,5,2)=date_format(now(),'%y'","");
			return $codigo;
		}
	}
?>