<?php 

	class tipocobranzacontroller extends ApplicationGeneral{
		private $name='tipocobranza';
		public function lista()
		{
			$pagina=$_REQUEST['id'];
			if (empty($_REQUEST['id'])) {
				$pagina=1;
			}
			$object=$this->AutoLoadModel($this->name);
			$data['data']=$object->lista();
			$data['titulo']=$this->name;
			$this->view->show('/'.$this->name.'/lista.phtml',$data);
			
		}
		public function nuevo(){
			$data['titulo']=$this->name;
			$this->view->show('/'.$this->name.'/nuevo.phtml',$data);
		}
		public function grabar(){
			$object=$this->AutoLoadModel($this->name);
			$data=$_REQUEST[$this->name];
			$data['codigo']=$object->GeneraCodigo();
			$graba=$object->graba($data);
			$ruta['ruta']="/".$this->name."/lista";
			$this->view->show("ruteador.phtml",$ruta);
		}
	}

 ?>