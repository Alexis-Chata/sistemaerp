<?php
	class Transporte extends Applicationbase{
		private $tabla="wc_transporte";
		private $tabla2="wc_clientetransporte,wc_transporte";
		
		function listado($inicio=0,$tamanio){
			$inicio=($inicio-1)*$tamanio;
			if($inicio<0){
				$inicio=0;
			}
			$data=$this->leeRegistro($this->tabla,"","estado=1","","limit $inicio,$tamanio");
			return $data;
		}
		function listaTodo(){
			$data=$this->leeRegistro($this->tabla,"","estado=1","trazonsocial asc","");
			return $data;
		}
		function grabar($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function verificaExiste($codicion){
			$data=$this->leeRegistro($this->tabla,"idtransporte","$codicion","");
			if(!count($data)){
				return 0;
			}else{
				return 1;
			}
		}
		function buscarxnombre($inicio,$tamanio,$nombre){
			$inicio=($inicio-1)*$tamanio;
			if($inicio<0){
				$inicio=0;
			}
			$data=$this->leeRegistro($this->tabla,"","concat(trazonsocial,' ',truc) like '%$nombre%' and estado=1","","limit $inicio,$tamanio");
			return $data;
		}
		function buscarxCliente($id){
			$data=$this->leeRegistro2($this->tabla2,"","idcliente=".$id,"","");
			return $data;
		}
		function buscarxId($id){
			$data=$this->leeRegistro($this->tabla,"","idtransporte=".$id,"","");
			return $data;
		}
		function buscarxIdClienteTransporte($id){
			$data=$this->leeRegistro2($this->tabla2,"","idclientetransporte=".$id,"","");
			return $data;
		}
		function buscarAutocomplete($iniRazonSocial){
			$iniRazonSocial=htmlentities($iniRazonSocial,ENT_QUOTES,'UTF-8');
			$transporte=$this->leeRegistro($this->tabla,"","trazonsocial like '%$iniRazonSocial%' and estado=1","","limit 0,15");
			foreach($transporte as $valor){
				$data[]=array("value"=>html_entity_decode($valor['trazonsocial'],ENT_QUOTES,'UTF-8').' '.$valor['truc'],"label"=>html_entity_decode($valor['trazonsocial'],ENT_QUOTES,'UTF-8').' '.$valor['truc'],"id"=>$valor['idtransporte'],"ruc"=>$valor['truc'],"telefono"=>$valor['ttelefono'],"direccion"=>$valor['tdireccion']);
			}
			return $data;
		}
		function buscaautocomplete($tex){
			$tex=htmlentities($tex,ENT_QUOTES,'UTF-8');
			$datos=$this->leeRegistro($this->tabla,"trazonsocial,idtransporte,truc","concat(trazonsocial,' ',truc) LIKE '%$tex%'","","limit 0,15");
			foreach($datos as $valor){
				$dato[]=array("value"=>html_entity_decode($valor['trazonsocial'],ENT_QUOTES,'UTF-8').' '.$valor['truc'],"label"=>html_entity_decode($valor['trazonsocial'],ENT_QUOTES,'UTF-8').' '.$valor['truc'],"id"=>$valor['idtransporte']);
			}
			return $dato;
		}
		function actualizar($id,$data){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idtransporte=".$id);
			return $exito;
		}
		function cambiarEstado($id){
			$exito=$this->cambiaEstado($this->tabla,"idtransporte=$id");
			return $exito;
		}
		function paginacion($tamanio,$condicion=""){
			$condicion=($condicion!="")?$condicion.=" and":"";
			$data=$this->leeRegistro($this->tabla,"idtransporte","$condicion estado=1","","");
			$paginas=ceil(count($data)/$tamanio);
			return $paginas;
		}
		function listaTransportePaginado($pagina){
			$data=$this->leeRegistroPaginado(
				$this->tabla,
				"",
				"estado=1",
				"",$pagina);
			return $data;
		}
		function paginadoTransporte(){
			return $this->paginado($this->tabla,"estado=1");
		}
		function listaTransportePaginadoxnombre($pagina,$condicion=""){
			$condicion=htmlentities($condicion,ENT_QUOTES,'UTF-8');
			$condicion=($condicion!="")?$condicion:"";
			$data=$this->leeRegistroPaginado(
				$this->tabla,
				"",
				"(trazonsocial like '%$condicion%') and estado=1",
				"",$pagina);
			return $data;
		}
                function listaTransportePaginadoxnombreymasfiltros($pagina,$condicion="", $arrayFiltro = array()){
			$condicion=htmlentities($condicion,ENT_QUOTES,'UTF-8');
			$condicion=($condicion!="")?$condicion:"";
                        $filtro = " and estado=1";
                        if (!empty($arrayFiltro['txtIdentificador'])) {
                            $filtro .= " and idtransporte like '%" . $arrayFiltro['txtIdentificador'] . "%'";
                        }
                        if (!empty($arrayFiltro['txtRazonSocial'])) {
                            $filtro .= " and trazonsocial like '%" . $arrayFiltro['txtRazonSocial'] . "%'";
                        }
                        if (!empty($arrayFiltro['txtRuc'])) {
                            $filtro .= " and truc like '%" . $arrayFiltro['txtRuc'] . "%'";
                        }
                        if (!empty($arrayFiltro['txtDireccion'])) {
                            $filtro .= " and tdireccion like '%" . $arrayFiltro['txtDireccion'] . "%'";
                        }
			$data=$this->leeRegistroPaginado(
				$this->tabla,
				"",
				"(trazonsocial like '%$condicion%')" . $filtro,
				"",$pagina);
			return $data;
		}
		function paginadoTransportexnombre($condicion=""){
			$condicion=htmlentities($condicion,ENT_QUOTES,'UTF-8');
			$condicion=($condicion!="")?$condicion:"";
			return $this->paginado($this->tabla,"trazonsocial like '%$condicion%' and estado=1");

		}
                function paginadoTransportexnombreymasfiltros($condicion="", $arrayFiltro = array()){
			$condicion=htmlentities($condicion,ENT_QUOTES,'UTF-8');
			$condicion=($condicion!="")?$condicion:"";
                        
                        $filtro = " and estado=1";
                        if (!empty($arrayFiltro['txtIdentificador'])) {
                            $filtro .= " and idtransporte like '%" . $arrayFiltro['txtIdentificador'] . "%'";
                        }
                        if (!empty($arrayFiltro['txtRazonSocial'])) {
                            $filtro .= " and trazonsocial like '%" . $arrayFiltro['txtRazonSocial'] . "%'";
                        }
                        if (!empty($arrayFiltro['txtRuc'])) {
                            $filtro .= " and truc like '%" . $arrayFiltro['txtRuc'] . "%'";
                        }
                        if (!empty($arrayFiltro['txtDireccion'])) {
                            $filtro .= " and tdireccion like '%" . $arrayFiltro['txtDireccion'] . "%'";
                        }
			return $this->paginado($this->tabla,"trazonsocial like '%$condicion%'" . $filtro);

		}
		function buscarxParametro($data){
			$data=htmlentities($data,ENT_QUOTES,'UTF-8');
		$condicion="(trazonsocial like '%$data%') and estado=1";
		$data=$this->leeRegistro($this->tabla,"",$condicion, "","");
		return $data;
		}
	}	
?>