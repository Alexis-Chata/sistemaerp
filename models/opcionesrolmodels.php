<?php
Class OpcionesRol extends Applicationbase{

	private $tabla;
	private	$tablaor="wc_opcionesrol opr";
	private $tablao="wc_opciones o";
	private $tablar="wc_rol r";
	
	function __construct(){
		$this->tabla="wc_opcionesrol";
	}

	function OpcionesxId($idRol){
		
		$data=$this->leeRegistro($this->tablaor.",".$this->tablao,
		"o.idopciones,opr.idopciones,o.nivel,o.orden,o.nombre,o.url,o.icono,opr.estado,o.inicio,o.idmodulo","opr.estado=1 and opr.idrol=".$idRol." and opr.idopciones=o.idopciones","idmodulo,nivel,orden","");
		
		$tdata=count($data);
		if($tdata>0){
				for($i=0;$i<$tdata;$i++){
			
		//	$datos[$data[$i]['idmodulo']][]=array($data[$i]['nivel'],$data[$i]['orden'],$data[$i]['nombre'],$data[$i]['url'],$data[$i]['icono'],$data[$i]['estado'],$data[$i]['inicio'],$data[$i]['idmodulo']);
				$datos[$data[$i]['idmodulo']][]=$data[$i];
			}
			$datos2=array_keys($datos);
	
			for($i=0;$i<count($datos2);$i++){
				$data3[]=$datos[$datos2[$i]]	;
			}
			return $data3;
		}else {
			return false;
		}
	
	}
	
	function OpcionesListaxId($idRol){
		
		$data=$this->leeRegistro($this->tablaor.",".$this->tablao,
		"o.idopciones,opr.idopciones,o.nivel,o.orden,o.nombre,o.url,o.icono,opr.estado,o.inicio,o.idmodulo","opr.estado=1 and opr.idrol=".$idRol." and opr.idopciones=o.idopciones","idmodulo,nivel,orden","");
		return $data;
	}
	function BucaOpcionesRol($idOpRol){
	
		$data=$this->leeRegistro($this->tablaor,
				"","opr.idopcionesrol='".$idOpRol."'","","");
		return $data;
	}
	function OpcionesBuscaxId($idRol,$url){
		$url=htmlentities($url,ENT_QUOTES,'UTF-8');
		$data=$this->leeRegistro($this->tablaor.",".$this->tablao,
		"o.idopciones,opr.idopciones,o.nivel,o.orden,o.nombre,o.url,o.icono,opr.estado,o.inicio,o.idmodulo","opr.estado=1 and opr.idrol=".$idRol." and opr.idopciones=o.idopciones and o.url='$url' ","idmodulo,nivel,orden","");
		return $data;
	}

	
	function OpcionesxRoles($DataRol){
			$totalRoles=count($DataRol);
			$in="(";
			for($i=0;$i<$totalRoles;$i++){
				$in.=$DataRol[$i][1];
				if(($i+1)!=$totalRoles){
					$in.=",";
				}
			}
			$in.=")";
		$data=$this->leeRegistro($this->tablaor.",".$this->tablao.",".$this->tablar,
		"Distinct o.idopciones,o.nivel,o.orden,o.nombre,o.descripcion,o.url,o.icono,opr.estado,o.inicio,o.idmodulo","opr.idrol in ".$in." and opr.idopciones=o.idopciones and opr.idrol=r.idrol and o.estado=1 ","o.idmodulo,o.nivel,o.orden","");
		$tdata=count($data);
		for($i=0;$i<$tdata;$i++){
			$datos[$data[$i]['idmodulo']][]=$data[$i];
		}
		$datos2=array_keys($datos);
		for($i=0;$i<count($datos2);$i++){
			$data3[]=$datos[$datos2[$i]]	;
		}
		//echo '<pre>';
		//print_r($data3);
		//exit;
		return $data3;
	}
	
	
	
	
	function GrabaOpcionesRol($data){
		//Verificando Modulo
		$modulo=$this->leeRegistro($this->tablao,"idopciones,idmodulo","idopciones=".$data['idopciones']."","");
		$idModulo=$modulo[0][1];
		
		$OpcionModulo=$this->leeRegistro($this->tablao,"idopciones,idmodulo,nivel","idmodulo=".$idModulo." and nivel=1","");
		$idOpcionModulo=$OpcionModulo[0][0];
		
		$Verificacion=$this->leeRegistro($this->tablaor,"","idrol=".$data['idrol']." and idopciones=".$idOpcionModulo."","","");
		$cVer=count($Verificacion);
		if($cVer==0){
			$dataModulo['idrol']=$data['idrol'];
			$dataModulo['idopciones']=$idOpcionModulo;
			$dataModulo['estado']=1;
			$grabaModulo=$this->grabaRegistro($this->tabla,$dataModulo);
		}
		$exito=$this->grabaRegistro($this->tabla,$data);
		return $exito;
	}
	
	
	function EliminaOpcionesRol($data){
		$filtro="idrol=".$data['idrol']." and idopciones=".$data['idopciones']." and estado=".$data['estado'];
		
		$idRol=$data['idrol'];
		$modulo=$this->leeRegistro($this->tablao,"idopciones,idmodulo","idopciones=".$data['idopciones']."","");
		$idModulo=$modulo[0][1]; //idmodulo
		$idOpcionModulo=$modulo[0][1];//idopcion delmodulo
		//Consultar item del modulo asignado.
		$otrasopciones=$this->leeRegistro($this->tablaor.",".$this->tablao,"o.idopciones","
								o.idmodulo=".$idModulo." and 
								o.idopciones not in (".$idOpcionModulo.",".$data['idopciones'].") 
								and o.idopciones=opr.idopciones and opr.idrol=".$idRol."","","");
		if(count($otrasopciones)==0){
			$filtro1="idrol=".$data['idrol']." and idopciones=".$idOpcionModulo." and estado=1";
			$this->eliminaRegistro($this->tabla,$filtro1);
		}
		
		$exito=$this->eliminaRegistro($this->tabla,$filtro);
		
		return $exito;
	}
	

}
?>