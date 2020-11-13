<?php
	class Detallemovimiento extends applicationBase{
		private $tabla="wc_detallemovimiento";
		private $tabla1="wc_detallerepuesto";
		private $tablas="wc_detallemovimiento,wc_movimiento,wc_producto";
		private $tablas1="wc_detallerepuesto,wc_repuesto,wc_producto";
		function grabaDetalleMovimieto($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function grabaDetalleMovimietoRep($data){
			$exito=$this->grabaRegistro($this->tabla1,$data);
			return $exito;
		}
		function buscaDetalleMovimiento($idMovimiento){
			$data=$this->leeRegistro3($this->tablas,"","t2.idmovimiento=$idMovimiento","",2);
			return $data;
		}
		function buscaDetalleMovimientoRep($idRepuesto){
			$data=$this->leeRegistro3($this->tablas1,"*","t2.idrepuesto=$idRepuesto","",2);
			return $data;
		}
		function buscaDetalleMovimientoxFiltro($filtro){
			$data=$this->leeRegistro("wc_detallemovimiento as dm inner join wc_movimiento as m on dm.idmovimiento=m.idmovimiento",
									"",
									$filtro,
									"",
									"");
			return $data;
		}
		function actualizaDetalleMovimientoxFiltro($filtro,$data){
			$data=$this->actualizaRegistro($this->tabla,$data,$filtro);
			return $data;
		}
		function actualizaDetalleMovimientoxid($id,$data){
			$data=$this->actualizaRegistro($this->tabla,$data,"iddetallemovimiento='$id'");
			return $data;
		}

    
    }

?>