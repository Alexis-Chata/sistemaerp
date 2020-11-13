<?php
	class TemplateController extends ApplicationGeneral{
		function listar(){
			$template = new Template();
			$data['Template'] = $template->listadoTemplates();
			$this->view->show("template/listar.phtml", $data);
		}
		function nuevo(){
			$this->view->show("template/nuevo.phtml");
		}
		function graba(){
			$data = $_REQUEST['Template'];
			$data['Estado'] = 1;
			$template = new Template();
			$exito = $template->grabaTemplate($data);
			if($exito){
				$ruta['ruta'] = "/template/listar";
				$this->view->show("ruteador.phtml", $ruta);
			}
		}
		function editar(){
			$id = $_REQUEST['id'];
			$template = new Template();
			$data['Template'] = $template->buscaTemplate($id);
			$this->view->show("template/editar.phtml", $data);
		}
		function actualiza(){
			$data = $_REQUEST['Template'];
			$id = $_REQUEST['idTemplate'];
			$template = new Template();
			$exito = $template->actualizaTemplate($data,"idtemplate=".$id);
			if($exito){
				$ruta['ruta'] = "/template/listar";
				$this->view->show("ruteador.phtml", $ruta);
			}
		}
		function eliminar(){
			$id=$_REQUEST['id'];
			$template=new Template();
			$estado=$template->cambiaEstadoTemplate($id);
			if($estado){
				$ruta['ruta']="/template/listar";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
	}
?>