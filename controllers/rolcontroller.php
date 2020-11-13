<?php
Class rolcontroller extends ApplicationGeneral{
	
	function Listado(){
		$rol=New Rol();
		$id=$_REQUEST['id'];
		$tamanio=10;
		$data['roles']=$roles=$rol->listadoRol($id,$tamanio);
		$data['paginacion']=$rol->Paginacion($tamanio);
		$this->view->show("/rol/listado.phtml",$data);
	}
		
	function cambiaEstado(){
		$idRol=$_REQUEST['id'];
		$a=New Rol();
		$exito=$a->EstadoRol($idRol);
		if ($exito){
			$ruta['ruta']="/rol/listado";
			$this->view->show("ruteador.phtml",$ruta);
		}		
	}
	
	function Nuevo(){
		$this->view->show("rol/nuevo.phtml");
	}
	
	function Grabar(){
		$Rol=$_REQUEST['Rol'];
		$Rol['estado']=1;
		$Accion=New Rol();
		$grabo=$Accion->grabaRol($Rol);
		$ruta['ruta']="/rol/listado";
		$this->view->show("ruteador.phtml",$ruta);
	}
	
	function Editar(){
		$id=$_REQUEST['id'];
		$Accion=New Rol();
		$data['roles']=$Accion->buscaRolesxId($id);
		$this->view->show("/rol/editar.phtml",$data);
	}
	
	function Actualizar(){
		$id=$_REQUEST['idRol'];
		$dataRol=$_REQUEST['Rol'];
		$Accion=New Rol();
		$actualizo=$Accion->actualizaRol($dataRol,$id);
		$ruta['ruta']="/rol/listado";
		$this->view->show("ruteador.phtml",$ruta);
	}
	
	function desactivar(){
		$id=$_REQUEST['id'];
		$accion=New Rol();
		$exito=$accion->cambiaestadoRol($id);
		$ruta['ruta']="/rol/listado";
		$this->view->show("ruteador.phtml",$ruta);
	}
	
	function mostrarMenu(){
		$data['idActor']=$_REQUEST['id'];
		$rol=New Rol();
		$data['listadoRoles']=$rol->ListadoRoles();				
		$actorrol=New ActorRol();
		$data['listadoActorRol']=$actorrol->ListadoActorRol();	
		
		$this->view->show("rol/menu.phtml",$data);
	}
	
	function buscarxid(){
		$idRol=$_REQUEST['Rol'];
		$r=New Rol();
		$data=$r->buscar($idRol);
		echo "<h3>Rol: ".$data[0]['Nombre']."</h3>";
	}
	
	function Busqueda(){
		$nrol=$_REQUEST['txtBusqueda'];
		$data=New Rol();
		$rol=$data->buscarxrol($nrol);
		$datos['roles']=$rol;
		$this->view->show("rol/listado.phtml",$datos);
	}
}

?>