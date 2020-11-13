<?php
	class ActorTransporte extends Applicationbase{
		private $tabla="wc_actortransporte";
		function grabaActorTransporte($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
	}
?>