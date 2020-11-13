<?php
Class opcionescontroller extends ApplicationGeneral{
	function Listado(){
		$data=New Opciones();
		$Opciones['Listado']=$data->ListaOpciones();
		$this->view->show("opciones/listado.phtml",$Opciones);
	}
	
	function cambiaEstado(){
		$idOpciones=$_REQUEST['id'];
		$a=New Opciones();
		$exito=$a->EstadoOpciones($idOpciones);
		if ($exito){
			$ruta['ruta']="/opciones/listado";
			$this->view->show("ruteador.phtml",$ruta);
		}		
	}
	
	function Eliminar(){
		$idOpcion=$_REQUEST['id'];
		$a=New Opciones();
		$exito=$a->EliminaOpcion($idOpcion);
		if ($exito){
			$ruta['ruta']="/opciones/listado";
			$this->view->show("ruteador.phtml",$ruta);
		}		
	}
	
	function AgregarOpcion(){
		$data['idModulo']=$_REQUEST['id'];
		$data['idNivel']=$_REQUEST['all_parameters'][3];
		$this->view->show("opciones/formOpciones.phtml",$data);
	}
	
	function grabaOpcion(){
		$data=$_REQUEST['Opciones'];
		//$data['Nivel']=2;
		$data['estado']=1;
		$a=New Opciones();
		$exito=$a->grabaOpciones($data);
		$ruta['ruta']="/opciones/listado";
		$this->view->show("ruteador.phtml",$ruta);
	}
	
	function editar(){
		$id=$_REQUEST['id'];
		$a=New Opciones();
		$data['modulos']=$a->ListaModulos();
		$data['opciones']=$a->buscar($id);	
		$this->view->show("opciones/actualizar.phtml",$data);
	}
	
	function actualiza(){
		$data=$_REQUEST['Opciones'];
		$id=$_REQUEST['idOpciones'];
		$a=New Opciones();
		$exito=$a->actualizaOpciones($data,"idopciones=".$id);
			$ruta['ruta']="/opciones/listado";
			$this->view->show("ruteador.phtml",$ruta);
	}
}

?>