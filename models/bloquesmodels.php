<?php 

	class bloques extends Applicationbase{
		private $tabla='wc_bloques';

		function listadoInventarioxId($idbloque){
			$movimiento=$this->leeRegistro($this->tabla,"","idbloque='$idbloque'","","");
			return $movimiento;
		}

		function listado(){
			$data=$this->leeRegistro($this->tabla,"","estado=1","");
			return $data;
		}
		function buscaxId($idbloque){
			$data=$this->leeRegistro($this->tabla,"","idbloque='$idbloque' and estado=1","");
			return $data;
		}
		function actualiza($data,$idbloque){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idbloque=$idbloque");
			return $exito;
		}
		function cambiaEstado($idbloque){
			$exito=$this->inactivaRegistro($this->tabla,"idbloque=$idbloque");
			return $exito;
		}
		function graba($data){
			$estado=$this->grabaRegistro($this->tabla,$data);

			return $estado;
		}
                function listarBloqueFormato($idbloque){
                    $sql="select * from wc_bloques where idbloque='".$idbloque."'";
                    $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
                    return $scriptArrayCompleto;
            }
	}

 ?>