<?php
	class Almacen extends  Applicationbase{
		private $tabla="wc_almacen";
		///****
		public function listadoAlmacen($inicio=0,$tamanio=10){
			$inicio=($inicio-1)*$tamanio;
			if($inicio<0){
				$inicio=0;
			}
			$data=$this->leeRegistro($this->tabla,"","estado=1","","Limit ".$inicio.",".$tamanio);
			return $data;
		}
		
		function buscarxnombre($inicio,$tamanio,$nombre){
			$nombre=htmlentities($nombre,ENT_QUOTES,'UTF-8');
			$inicio=($inicio-1)*$tamanio;
			if($inicio<0){
				$inicio=0;
			}
			$data=$this->leeRegistro($this->tabla,"","nomalm like '%$nombre%' and estado=1","","limit $inicio,$tamanio");
			return $data;
		}
		function grabaAlmacen($data){
			$estado=$this->grabaRegistro($this->tabla,$data);

			return $estado;
		}
		function actualizaAlmacen($data,$filtro){
			$exito=$this->actualizaRegistro($this->tabla,$data,$filtro);
			return $exito;
		}
		function buscaAlmacen($idAlmacen){
			$data=$this->leeRegistro($this->tabla,"","idalmacen=$idAlmacen","");
			return $data;
		}
		function cambiaEstadoAlmacen($idAlmacen){
			$exito=$this->cambiaEstado($this->tabla,"idalmacen=$idAlmacen");
			return $exito;
		}
		function paginacion($tamanio,$condicion=""){
			$condicion=($condicion!="")?$condicion.=" and":"";
			$data=$this->leeRegistro($this->tabla,"idalmacen","$condicion estado=1","","");
			$paginas=ceil(count($data)/$tamanio);
			$paginas=$paginas>0?$paginas:1;
			return $paginas;
		}
		function buscaautocomplete($tex){
			$tex=htmlentities($tex,ENT_QUOTES,'UTF-8');
			$datos=$this->leeRegistro($this->tabla,"nomalm,idalmacen","nomalm LIKE '%$tex%'","");
			foreach($datos as $valor){
				$dato[]=array("value"=>$valor['nomalm'],"label"=>$valor['nomalm'],"id"=>$valor['idalmacen']);
			}
			return $dato;
		}
		function listado(){
			$data=$this->leeRegistro($this->tabla,"","estado=1","","");
			return $data;
		}
		function listaAlmacenPaginado($pagina){
			$data=$this->leeRegistroPaginado(
				$this->tabla,
				"",
				"estado=1",
				"",$pagina);
			return $data;
		}
		function paginadoAlmacen(){
			return $this->paginado($this->tabla,"estado=1");
		}
	}
?>