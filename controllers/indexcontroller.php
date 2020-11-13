<?php
Class IndexController extends ApplicationGeneral{
	function bienvenido(){
		$this->view->show("bienvenidos.phtml");
	}

	function Index(){
		$favoritos=$this->AutoLoadModel('favoritos');
		session_start();
		$idactor=$_SESSION['idactor'];
		$data['favoritos']=$favoritos->buscaxIdActor($idactor);
		

		$this->view->show("index/index.phtml",$data);
	}
	function agregarFavoritos(){
		$ruta=$_REQUEST['Iruta'];
		$descripcion=$_REQUEST['Inombre'];
		session_start();
		$idactor=$_SESSION['idactor'];
		$favoritos=$this->AutoLoadModel('favoritos');
		$data['idactor']=$idactor;
		$data['url']=$ruta;
		$data['descripcion']=$descripcion;
		$exito=$favoritos->graba($data);
		echo $exito;
	}
	function eliminarFavorito(){
		$idFavorito=$_REQUEST['id'];
		$favoritos=$this->AutoLoadModel('favoritos');

		$exito=$favoritos->cambiaEstado($idFavorito);

		$ruta['ruta']="/index/index";
		$this->view->show("ruteador.phtml",$ruta);

		
	}
}
