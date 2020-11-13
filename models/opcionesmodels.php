<?php
Class Opciones extends Applicationbase{
	private $tabla;
	function __construct(){
		$this->tabla="wc_opciones";
	}
	
	function ListadoOpciones(){
	
		$data=$this->leeRegistro($this->tabla,
		"idopciones,nivel,orden,nombre,url,icono,estado,inicio,idmodulo","estado=1","idmodulo,nivel,orden","");
		$tdata=count($data);
		for($i=0;$i<$tdata;$i++){
			$datos[$data[$i]['idmodulo']][]=$data[$i];
		}
		$datos2=array_keys($datos);
		for($i=0;$i<count($datos2);$i++){
			$data3[]=$datos[$datos2[$i]]	;
		}
		return $data3;
	}
	
	function ListaOpciones(){
	
		$data=$this->leeRegistro($this->tabla,
		"idopciones,nivel,orden,nombre,url,icono,estado,inicio,idmodulo","","idmodulo,nivel,orden","");
		$tdata=count($data);
		for($i=0;$i<$tdata;$i++){
			$datos[$data[$i]['idmodulo']][]=$data[$i];
		}
		$datos2=array_keys($datos);
		for($i=0;$i<count($datos2);$i++){
			$data3[]=$datos[$datos2[$i]]	;
		}
		return $data3;
	}
	
	function ListaModulos(){
		$data=$this->leeRegistro($this->tabla,
		"idopciones,idmodulo,nombre","nivel=1 and estado=1","idmodulo,nivel,nombre","");
		return $data;
	}
	
	
	
	function EstadoOpciones($idOpciones){
		$exito=$this->cambiaEstado($this->tabla,"idopciones=".$idOpciones);
		return $exito;		
	}
	
	function EliminaOpcion($idOpciones){
		$exito=$this->eliminaRegistro("wc_opcionesrol","idopciones=".$idOpciones);
		$exito=$this->eliminaRegistro($this->tabla,"idopciones=".$idOpciones);
		return $exito;
	}
	
	function grabaOpciones($data){
		$exito=$this->grabaRegistro($this->tabla,$data);
		return $exito;
	}
	function buscar($id){
		$opciones=$this->leeRegistro($this->tabla,"","idopciones=".$id,"","");
		return $opciones;
	}
	function actualizaOpciones($data,$filtro){
		$exito=$this->actualizaRegistro($this->tabla,$data,$filtro);
		return $exito;
	}
	
	function OpcionesCombo(){
		$data=$this->leeRegistro($this->tabla,"idopciones,nombre","nivel='2'","","");
		return $data;
	}
	function buscaOpcionxURL($url){
		$url=htmlentities($url,ENT_QUOTES,'UTF-8');
		$data=$this->leeRegistro($this->tabla,"","estado=1 and url='$url' ","","");
		return $data;
	}
        function listarOpcionesxVerificacion($idmodulo = "") {
            $condicion = '';
            if (!empty($idmodulo)) {
                $condicion .= " and idopciones='" . $idmodulo . "'";
            }
            $data=$this->leeRegistro($this->tabla, "","nivel=2 and estado=1 and verificacion=1" . $condicion,"","");
            return $data;
	}

}

?>