<?php
	class TipomovimientoController extends ApplicationGeneral{
		function listar(){
			$tipoMovimiento=new Tipomovimiento();
			$data['Tipomovimiento']=$tipoMovimiento->listadoTiposmovimiento();
			$this->view->show("/tipomovimiento/listar.phtml",$data);
		}
		function nuevo(){
			$this->view->show("/tipomovimiento/nuevo.phtml");
		}
		function graba(){
			$data=$_REQUEST['Tipomovimiento'];
			$tipoMovimiento=new Tipomovimiento();
			$exito=$tipoMovimiento->grabaTipomovimiento($data);
			if($exito){
				$ruta['ruta']="/tipomovimiento/listar";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function editar(){
			$id=$_REQUEST['id'];
			$tipoMovimiento=new Tipomovimiento();
			$data['Tipomovimiento']=$tipoMovimiento->editaTipoMovimiento($id);
			$this->view->show("/tipomovimiento/editar.phtml",$data);
		}
		function actualiza(){
			$id=$_REQUEST['idTipoMovimiento'];
			$data=$_REQUEST['Tipomovimiento'];
			$tipoMovimiento=new Tipomovimiento();
			$exito=$tipoMovimiento->actualizaTipoMovimiento($data,$id);
			if($exito){
				$ruta['ruta']="/tipomovimiento/listar/";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function elimina(){
			$id=$_REQUEST['id'];
			$tipoMovimiento=new Tipomovimiento();
			$exito=$tipoMovimiento->eliminaTipoMovimiento($id);
			if($exito){
				$ruta['ruta']="/tipomovimiento/listar/";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
	}
?>