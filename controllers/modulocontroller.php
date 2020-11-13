<?php
Class modulocontroller extends ApplicationGeneral{
	function Listado(){
		$data=New Modulo();
		$Modulo['Listado']=$data->ListadoModulo();
		$this->view->show("modulo/listado.phtml",$Modulo);
	}
	
	function cambiaEstado(){
		$idModulo=$_REQUEST['id'];
		$a=New Modulo();
		$exito=$a->EstadoModulo($idModulo);
		if ($exito){
			$ruta['ruta']="/modulo/listar";
			$this->view->show("ruteador.phtml",$ruta);
		}		
	}

	function Nuevo(){
		$this->view->show("modulo/formModulo.phtml");
	}
	
	function graba(){
		$data=$_REQUEST['Modulo'];
		$data['Estado']=1;
		$a=New Modulo();
		$exito=$a->grabaModulo($data);
			if ($exito){
				$ruta['ruta']="/modulo/listar";
				$this->view->show("ruteador.phtml",$ruta);
			}
	}
	
	function editar(){
		$id=$_REQUEST['id'];
		$a=New Modulo();
		$data['Modulo']=$a->buscar($id);		
		$this->view->show("modulo/actualizar.phtml",$data);
	}
	
	function actualiza(){
		$data=$_REQUEST['Modulo'];
		$id=$_REQUEST['idModulo'];
		$a=New Modulo();
		$exito=$a->actualizaModulo($data,"id=".$id);
		if ($exito){
			$ruta['ruta']="/modulo/listar";
			$this->view->show("ruteador.phtml",$ruta);
		}
	}
	
}

?>