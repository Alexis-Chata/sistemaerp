<?php 
	Class cobrador extends Applicationbase{
		private $tabla="wc_cobradorzona";
		private $tabla2="wc_actorrol";

		function graba($data){
			$exito=$this->grabaRegistro($this->tabla,$data);
			return $exito;
		}
		function actualiza($data,$idcobradorzona){
			$exito=$this->actualizaRegistro($this->tabla,$data,"idcobradorzona=$idcobradorzona");
			return $exito;
		}
		function buscaxActorxZona($idactor,$idzona){
			$data=$this->leeRegistro(
						$this->tabla,
						"",
						"idcobrador='$idactor' and idzona='$idzona'",
						"");
			return $data;
		}
		function buscaxid($idactor){
			$data=$this->leeRegistro(
						"`wc_actor` wc_actor inner join `wc_actorrol` wc_actorrol on wc_actor.`idactor`=wc_actorrol.`idactor` 
						",
						"",
						"wc_actor.`idactor`='$idactor' and  wc_actor.`estado`=1 and wc_actorrol.`idrol`=28",
						"");
			return $data;
		}
		function lista(){
			$data=$this->leeRegistro(
					" `wc_actor` wc_actor inner join `wc_actorrol` wc_actorrol on wc_actor.`idactor`=wc_actorrol.`idactor` 
					
					",
					"",
					"wc_actor.`estado`=1 and wc_actorrol.`idrol`=28",
					"");
			
			return $data;
		}
		function buscaxIdActorZonas($idactor){
			$data=$this->leeRegistro(
					"`wc_cobradorzona`  wc_cobradorzona  inner join `wc_categoria` wc_categoria on wc_categoria.`idcategoria`=wc_cobradorzona.`idzona`",
					"",
					"wc_cobradorzona.`estado`=1 and wc_categoria.`estado`=1 and wc_cobradorzona.`idcobrador`='$idactor'",
					"");
			
			return $data;
		}
		function buscaxIdActorZonasCompleto($idactor){
			$data=$this->leeRegistro(
					"`wc_cobradorzona`  wc_cobradorzona  inner join `wc_categoria` wc_categoria on wc_categoria.`idcategoria`=wc_cobradorzona.`idzona`
					inner join `wc_actor` wc_actor on wc_actor.`idactor`=wc_cobradorzona.`idcobrador`
					inner join `wc_actorrol` wc_actorrol on wc_actorrol.`idactor`=wc_actor.`idactor`
					inner join `wc_rol` wc_rol on wc_rol.`idrol`=wc_actorrol.`idrol`
					",
					"",
					"wc_cobradorzona.`estado`=1 and wc_rol.`idrol`=28 and wc_categoria.`estado`=1 and wc_cobradorzona.`idcobrador`='$idactor'",
					"");
			
			return $data;
		}
		function listaPaginado($pagina){
			$data=$this->leeRegistroPaginado($this->tabla,"","","",$pagina);
			return $data;
		}

		function elimina($filtro){
			$data=$this->eliminaRegistro($this->tabla,$filtro);
			return $data;
		}
		function buscaZonasxCobrador($idcobrador){
			$data=$this->leeRegistro(
						"wc_cobradorzona",
						"",
						"idcobrador='$idcobrador' and estado=1",
						"");
			return $data;
		}
		
	}

 ?>