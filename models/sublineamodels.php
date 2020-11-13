<?php
	class Sublinea extends Applicationbase{
		private $tabla="wc_linea";
		function listaSubLineaPaginado($pagina){
			$data=$this->leeRegistroPaginado(
				$this->tabla,
				"idpadre,nomlin,idlinea",
				"estado=1 and idpadre!=0",
				"",$pagina);
			return $data;
		}
		function listaSubLineaPaginadoxnombre($pagina,$condicion=""){
			$condicion=($condicion!="")?$condicion:"";
			$data=$this->leeRegistroPaginado(
				$this->tabla,
				"idpadre,idlinea,nomlin",
				"idpadre like '%$condicion%' or nomlin like '%$condicion%' and estado=1 and idpadre!=0 ",
				"",$pagina);
			return $data;
		}
		function paginadoSubLinea(){
			return $this->paginado($this->tabla,"estado=1 and idpadre!=0");
		}
		function paginadoSubLineaxnombre($condicion=""){
			$condicion=($condicion!="")?$condicion:"";
			return $this->paginado($this->tabla,"idpadre like '%$condicion%' or nomlin like '%$condicion%' and estado=1 and idpadre!=0");
		}
		function listadoSublinea($idLineaPadre=""){
			$condicion="";
			if(!empty($idLineaPadre)){$condicion="idpadre=$idLineaPadre";}
			$data=$this->leeRegistro($this->tabla,"","$condicion","");
			return $data;
		}
		function listaSublinea($condicion=""){
			$condicion.=($condicion!="")?" and ":"";
			$data=$this->leeRegistro($this->tabla,"idLinea,nomlin,idpadre"," $condicion  estado=1","");		
			return $data;
		}
		/*function listadoOptionsSublineas($idLinea){
			$data=$this->leeRegistro($this->tabla,"","idpadre=".$idLinea,"");
			return $data;
		}*/
		function grabaSublinea($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function buscaSublinea($idSublinea){
			$data=$this->leeRegistro($this->tabla,"","idlinea=$idSublinea","");
			return $data;
		}
		function contarSublinea(){
			$subLinea=new Sublinea();
			$numRegistros=$subLinea->contarRegistro($this->tabla,"idpadre!=0");
			return $numRegistros;
		}
		function eliminaSublinea($idLinea){
			$exito=$this->cambiaEstado($this->tabla,"idLinea=$idLinea");
			return $exito;
		}
		function actualizaSublinea($data,$idLinea){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idlinea=$idLinea");
			return $exito;
		}
		function buscarxnombre($inicio,$tamanio,$nombre,$idpadre=''){
			$nombre=htmlentities($nombre,ENT_QUOTES,'UTF-8');
			$condicion=(($idpadre!='')?" idpadre=$idpadre and ":'idpadre!=0 and ');
			$inicio=($inicio-1)*$tamanio;
			if($inicio<0){
				$inicio=0;
			}
			$data=$this->leeRegistro($this->tabla,""," $condicion nomlin like '%$nombre%' and 
					estado=1","","limit $inicio,$tamanio");
			return $data;
		}
	}
?>