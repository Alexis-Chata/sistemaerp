<?php
	class Zona extends Applicationbase{
		private $tabla="wc_zona";	
		private $tablac="wc_categoria";
		private $tablazc="wc_zonacategoria";
		private $tablas="wc_zona z,wc_categoria c";
		function grabar($data){
			$id=$this->grabaRegistro($this->tabla,$data);
			return $id;
		}
		function buscarxid($id){
			$data=$this->leeRegistro($this->tabla,"nombrezona,idzona,observacion,idcategoria","idzona=".$id,"","");
			return $data;
		}
		public function GeneraCodigo(){
		$maxcodigo=$this->leeRegistro($this->tabla,"max(idzona)","","","");
		$data['codigo'] ='ZN'.str_pad($maxcodigo[0]['max(idzona)'] ,5,'0',STR_PAD_LEFT);	
		$this->actualizaRegistro($this->tabla,$data,"idzona=".$maxcodigo[0]['max(idzona)']);		
		return $data;
		}
		function actualizar($id,$data){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idzona=".$id);
			return $exito;			
		}
		function eliminar($id){
			$respuesta=$this->eliminaRegistro($this->tabla,"idzona='".$id."'");
			return $respuesta;
		}
		function cambiaEstadoZona($idzona){
			$exito=$this->cambiaEstado($this->tabla,"idzona='$idzona'");
			return $exito;
		}
		function cambiaEstadoCategoria($idcategoria){
			$exito=$this->cambiaEstado($this->tablac,"idcategoria='$idcategoria'");
			return $exito;
		}

		function buscazonaautocomplete($tex,$idcategoria=''){
			$tex=htmlentities($tex,ENT_QUOTES,'UTF-8');
			$condicion=($idcategoria!='')?" and idcategoria=$idcategoria ":'';
			$datos=$this->leeRegistro($this->tabla,"codigoz,nombrezona,idzona,observacion","concat(codigoz,' ',nombrezona) LIKE '%$tex%' $condicion","");
			foreach($datos as $valor){
				$dato[]=array("value"=>$valor['codigoz'].' '.$valor['nombrezona'],"label"=>$valor['codigoz'].' '.$valor['nombrezona'],"id"=>$valor['idzona']);
			}
			return $dato;
		}
		function listado(){
			$data=$this->leeRegistro($this->tabla,"","","nombrezona","");
			return $data;
		}
		function listadoTotalZona(){
			$data=$this->leeRegistro($this->tabla,"","estado=1","nombrezona","");
			return $data;
		}
		function buscarxtext($text){
			$tex=htmlentities($tex,ENT_QUOTES,'UTF-8');
			$condicion=($idcategoria!=''?" and idcategoria=$idcategoria ":'');
			$data=$this->leeRegistro($this->tabla,"nombrezona,idzona,observacion","concat(codigoz,' ',nombrezona) LIKE '%$text%' condicion","","");
			return $data;
		}
		function buscarxnombre($inicio,$tamanio,$nombre,$idcategoria=''){
			$nombre=htmlentities($nombre,ENT_QUOTES,'UTF-8');
			$condicion=($idcategoria!=''?" and (z.idcategoria=$idcategoria or c.idpadrec=$idcategoria) ":'');
			$inicio=($inicio-1)*$tamanio;
			if($inicio<0){
				$inicio=0;
			}
			$data=$this->leeRegistro($this->tablas,"z.nombrezona,z.idzona,c.nombrec categoria,z.codigoz,z.idcategoria","concat(z.codigoz,' ',
					z.nombrezona) like '%$nombre%' and z.estado=1 and z.idcategoria=c.idcategoria $condicion","z.idzona","limit $inicio,$tamanio");
			return $data;
		}
		function Paginacion($tamanio,$condicion="",$idcategoria=''){
			$condicion.=($idcategoria!=''?" and (z.idcategoria=$idcategoria or c.idpadrec=$idcategoria) ":'');
			$data=$this->leeRegistro($this->tablas,"","concat(z.codigoz,' ',
					z.nombrezona) like '%$nombre%' and z.estado=1 and z.idcategoria=c.idcategoria $condicion","","");
			$paginas=intval((count($data)/$tamanio))+1;
			return $paginas;
		}
		function grabacategoria($data){
			$data['estado']=1;
			$id=$this->grabaRegistro($this->tablac,$data);
			return $id;
		}
		function asignarcategoria($idzona,$idcategoria){
			$data['idzona']=$idzona;
			$data['idcategoria']=$idcategoria;
			$data['estado']=1;
			$exito=$this->grabaRegistro($this->tablazc,$data);
			return $exito;
		}
		function zonasxcategoria($idcategoria){
			$data=$this->leeRegistro($this->tablas,"","zc.idcategoria=$idcategoria and zc.estado=1 and c.estado=1 and c.idcategoria=$idcategoria z.idzona=zc.idzona","");
			return $data;
		}
		function listacategorias(){
			$data=$this->leeRegistro($this->tablac,"idcategoria,codigoc,nombrec,idpadrec","estado=1","");
			return $data;
		}
		function listacategoriaHijo(){
			$data=$this->leeRegistro($this->tablac,"idcategoria,nombrec,codigoc,idpadrec","idpadrec!=0 and estado=1","");
			return $data;
		}
		function actualizacategoria($id,$data){
			$exito=$this->actualizaRegistro($this->tablac,$data,"idcategoria=$id");
			return $exito;
		}
		function buscarcategoria($id){
			$data=$this->leeRegistro($this->tablac,"","idcategoria=$id","");
			return $data;
		}
		function eliminarcategoria($id){
			$exito=$this->eliminaRegistro($this->tablac,"idcategoria=".$id);
			return $exito;
		}
		function nombrexid($id){
			$data=$this->leeRegistro($this->tabla,"nombrezona","idzona=".$id,"","");
			return $data[0]['nombrezona'];
		}
		function listadoTotal($condicion){
			$condicion.=($condicion!="")?" and ":"";
			$data=$this->leeRegistro($this->tablas,"z.idzona,z.nombrezona,z.codigoz,z.observacion,c.nombrec","$condicion z.estado=1 and c.estado=1 and z.idcategoria=c.idcategoria","z.idzona asc");
			return $data;
		}
		function listaZonaPaginado($pagina){
			$data=$this->leeRegistroPaginado(
				$this->tablas,
				"z.idzona,z.nombrezona,z.codigoz,z.observacion,c.nombrec",
				"z.estado=1 and c.estado=1 and z.idcategoria=c.idcategoria",
				"z.idzona asc",$pagina);
			return $data;
		}
		function listaZonaPaginadoxCategoria($pagina,$condicion=""){
			$condicion=($condicion!="")?$condicion:"";
			$data=$this->leeRegistroPaginado(
				$this->tablas,
				"z.idzona,z.nombrezona,z.codigoz,z.observacion,c.nombrec",
				"z.estado=1 and c.estado=1 and z.idcategoria=c.idcategoria and (z.idcategoria='$condicion')",
				"z.idzona asc",$pagina);

			return $data;
		}
		function paginadoZona(){
			return $this->paginado($this->tablas,
				//"z.idzona,z.nombrezona,z.codigoz,z.observacion,c.nombrec",
				"z.estado=1 and c.estado=1 and z.idcategoria=c.idcategoria"//,
				//"z.idzona asc"
                                );
		}
		function paginadoZonaxCategoria($condicion=""){
			$condicion=($condicion!="")?$condicion:"";
			return $this->paginado($this->tablas,
				//"z.idzona,z.nombrezona,z.codigoz,z.observacion,c.nombrec",
				"z.estado=1 and c.estado=1 and z.idcategoria=c.idcategoria and (z.idcategoria='$condicion')"//,
				//"z.idzona asc"
                                );
		}
		function TotalzonasxCategoria($condicion=""){
			$condicion=($condicion!="")?$condicion:"";
			$data=$this->leeRegistro($this->tablas,
					"count(*)",
					"z.estado=1 and c.estado=1 and z.idcategoria=c.idcategoria and (z.idcategoria='$condicion')",
					"");
			return $data[0]['count(*)'];
		}
		function listaCategoriaPrincipal(){
			$data=$this->leeRegistro($this->tablac,
					"idcategoria,nombrec",
					"estado=1 and idpadrec=0",
					"");
			return $data;
		}
		function listaCategoriaPaginado($pagina){
			$data=$this->leeRegistroPaginado(
				$this->tablac,
				"idcategoria,nombrec,codigoc,idpadrec",
				"idpadrec!=0 and estado=1",
				"",$pagina);
			return $data;
		}
		function paginadoCategoria(){
			return $this->paginado($this->tablac,
				"idpadrec!=0 and estado=1");
		}
		function paginadoCategoriaxParametro($condicion=""){
			$condicion=($condicion!="")?$condicion:"";
			return $this->paginado($this->tablac,
				"idpadrec!=0 and estado=1 and (idpadrec='$condicion')"//,
				//"z.idzona asc"
                                );
		}
		function listaCategoriaPaginadoxParametro($pagina,$condicion=""){
			$condicion=($condicion!="")?$condicion:"";
			$data=$this->leeRegistroPaginado(
				$this->tablac,
				"idcategoria,nombrec,codigoc,idpadrec",
				"idpadrec!=0 and estado=1 and (idpadrec='$condicion')",
				"",$pagina);

			return $data;
		}
		function TotalCategoriasxParametro($condicion=""){
			$condicion=($condicion!="")?$condicion:"";
			$data=$this->leeRegistro($this->tablac,
					"count(*)",
					"idpadrec!=0 and estado=1 and (idpadrec='$condicion')",
					"");
			return $data[0]['count(*)'];
		}

		function buscarZonasxIdCategoria($parametro){
			$cantidad=$this->leeRegistro($this->tabla,"count(*)","idcategoria='$parametro' and estado=1","");
			return $cantidad[0]['count(*)'];
		}
		function verificarCodigoZona($parametro){
			$parametro=($parametro!="")?$parametro:"";
			$data=$this->leeRegistro($this->tabla,
					"count(*)",
					" estado=1 and (codigoz='$parametro')",
					"");
			return $data[0]['count(*)'];
		}
		function verificarCodigoCategoria($parametro){
			$parametro=($parametro!="")?$parametro:"";
			$data=$this->leeRegistro($this->tablac,
					"count(*)",
					" estado=1 and (codigoc='$parametro')",
					"");
			return $data[0]['count(*)'];
		}
		function buscaCategoriaxPadre($idpadrec){
			$data=$this->leeRegistro($this->tablac,"","idpadrec='$idpadrec'  and estado=1","");
			return $data;
		}
		function buscaZonasxCategoria($idcategoria){
			$data=$this->leeRegistro($this->tabla,"","idcategoria='$idcategoria' and estado=1","");
			return $data;
		}
		function buscaCategoria($idcategoria){
			$data=$this->leeRegistro($this->tablac,"","idcategoria='$idcategoria'","");
			return $data;
		}
	}

?>
