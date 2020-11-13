<?php
Class ActorRol extends Applicationbase{
	private $tabla1="wc_actorrol";
	private $tabla2="wc_actorrol,wc_actor";
	private	$tablaar="wc_actorrol ar";
	private $tablar="wc_rol r";
	private $tabla="wc_actor";
	
	function __construct(){
		$this->tabla="wc_actorrol";
	}
	function ListadoActorRol(){
		$a=$this->leeRegistro($this->tabla,"","","Estado desc","");
		return $a;
	}
	
	function EstadoActorRol($idActorRol){
		$exito=$this->cambiaEstado($this->tabla,"idactorrol=".$idActorRol);
		return $exito;		
	}
	function grabaActorRol($data){
		$exito=$this->grabaRegistro($this->tabla1,$data);
		return $exito;
	}
	function eliminaActorRol($filtro){
		$exito=$this->eliminaRegistro($this->tabla1,$filtro);
		return $exito;
	}
	function buscarxid($id){
		$actorrol=$this->leeRegistro($this->tabla,"","idactorrol=".$id,"","");
		return $actorrol;
	}
	function buscar($id){
		$actorrol=$this->leeRegistro($this->tabla,"","idactorrol=".$id,"","");
		return $actorrol;
	}
	function actualizaActorRol($data,$filtro){
		$exito=$this->actualizaRegistro($this->tabla,$data,$filtro);
		return $exito;
	}
	
	function rolesxid($idactor){
		$data=$this->leeregistro($this->tablaar.",".$this->tablar,
						"r.nombre,r.idrol","ar.idactor =".$idactor." and ar.idrol=r.idrol and ar.estado=1","","");
		return $data;
	}

	
	function verificaPostulante($idActor){
		$data=$this->leeRegistro($this->tabla,"Id","IdActor=".$idActor." and idRol=7","","");
		if($data!=Null){
			return true;
		}
		else{
			return false;
		}
	}
	
	public function listadoclientes(){
		$data=$this->leeregistro($this->tabla.",".$this->tablaar,"","wc_actor.idactor=wc_actorrol.idactor and wc_actorrol.idrol=26","codigo asc","");
		return $data;
	}
	
	public function listadovendedores(){
		$data=$this->leeregistro2($this->tabla2,"","idrol=26","");
		return $data;
	}

	function buscaActorxRol($idactor){
		$actorrol=$this->leeRegistro("wc_actorrol ar inner join wc_actor a on ar.idactor=a.idactor ","","a.idactor=".$idactor,"","");
		return $actorrol;
	}

	function actoresxRol($idrol){
		$dato=$this->leeRegistro("wc_actorrol ar inner join wc_actor a on ar.idactor=a.idactor ","","ar.idrol='".$idrol."'  and a.estado=1" ,"","");

		return $dato;
	}
	function actoresxRolxNombre($idrol){
		$actorrol=$this->leeRegistro("wc_actorrol ar inner join wc_actor a on ar.idactor=a.idactor ","","ar.idrol='$idrol' and a.estado=1" ,"","group by a.idactor order by a.nombres asc, a.apellidopaterno asc, a.apellidomaterno asc");
		
		return $actorrol;
	}
        function actoresxRolxNombreSinconEstado($idrol){
		$actorrol=$this->leeRegistro("wc_actorrol ar inner join wc_actor a on ar.idactor=a.idactor ","","ar.idrol='$idrol'" ,"","group by a.idactor order by a.nombres asc, a.apellidopaterno asc, a.apellidomaterno asc");
		
		return $actorrol;
	}
	function actoresxfiltro($filtro){
		$actorrol=$this->leeRegistro("wc_actorrol ar inner join wc_actor a on ar.idactor=a.idactor ","",$filtro,"","group by a.idactor");
		
		return $actorrol;
	}
}
?>