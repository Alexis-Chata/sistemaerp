<?php
	class LineaController extends ApplicationGeneral{
		
		function nuevo(){
			echo $data['datajason'];
			$linea=new Linea();
			$data['LineaPadre']=$linea->listadolineas("idpadre=0");
			$this->view->show("linea/nuevo.phtml",$data);
		}
		function graba(){
			$data=$_REQUEST['Linea'];
			$data['estado']=1;
			$linea=new Linea();
			$exito=$linea->grabaLinea($data);
			echo json_encode($exito);
			if($exito){
				$ruta['ruta']="/linea/lista/1/";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function editar(){
			$idLinea=$_REQUEST['id'];
			$linea=new Linea();
			$data['Linea']=$linea->buscaLinea($idLinea);
			$data['LineaPadre']=$linea->listadolineas("idpadre=0");
			$this->view->show("/linea/editar.phtml",$data);
		}

		function lista(){
			$linea=$this->AutoLoadModel('linea');
			session_start();
			$_SESSION['P_linea']="";

			if (empty($_REQUEST['id'])) {
				$_REQUEST['id']=1;
			}
			$data['linea']=$linea->listaLineaPaginado($_REQUEST['id']);
			$paginacion=$linea->paginadoLinea();
			$data['paginacion']=$paginacion;
			$data['blockpaginas']=round($paginacion/10);
			$this->view->show("/linea/lista.phtml",$data);

		}

		function actualiza(){
			$data=$_REQUEST['Linea'];
			$idLinea=$_REQUEST['idlinea'];
			$linea=new Linea();
			$exito=$linea->actualizaLinea($data,$idLinea);
			if($exito){
				$ruta['ruta']="/linea/lista/1/";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function elimina(){
			$idLinea=$_REQUEST['id'];
			$linea=new Linea();
			$cantidad=$linea->buscarSublineaxIdpadre($idLinea);

			if ($cantidad==0) {
				$exito=$linea->cambiaEstadoLinea($idLinea);
				if($exito){
					$ruta['ruta']="/linea/lista/";
					$this->view->show("ruteador.phtml",$ruta);
				}
			}else{
				
				$ruta['ruta']="/linea/lista/";
				$this->view->show("ruteador.phtml",$ruta);
			}

			
		}
		function buscar(){
			$linea=$this->AutoLoadModel('linea');
			
			if (empty($_REQUEST['id'])) {
				$_REQUEST['id']=1;
			}

			session_start();
			$_SESSION['P_linea'];
			if (!empty($_REQUEST['txtBusqueda'])) {
				
				$_SESSION['P_linea']=$_REQUEST['txtBusqueda'];
			}

			$parametro=$_SESSION['P_linea'];
			$filtro="nomlin like '%$parametro%' and idpadre=0";
			$data['retorno']=$parametro;
			$data['totregistros']=count($linea->listadolineas($filtro));
			$data['linea']=$linea->listaLineaPaginadoxnombre($_REQUEST['id'],$parametro);
			$paginacion=$linea->paginadoLineaxnombre($parametro);
			$data['paginacion']=$paginacion;
			$data['blockpaginas']=round($paginacion/10);

			$this->view->show("/linea/buscar.phtml",$data);
			
		}
		function autocomplete(){
			$id=$_REQUEST['id'];
			$linea=new Linea();
			$data=$linea->autocomplete($id);
			echo json_encode($data);
		}

		/*linea padre */
		function nuevalineapadre(){
			$data['padre']=1;
			$this->view->show("linea/nuevo.phtml",$data);
		}
		function editarlineapadre(){
			$idLinea=$_REQUEST['id'];
			$linea=new Linea();
			$data['Linea']=$linea->buscaLinea($idLinea);
			$data['padre']=1;
			$this->view->show("/linea/editar.phtml",$data);
		}
		public function proceso(){
			$datajason=$_POST['ruta'];
			if (empty($datajason)) {
				echo "error";
			}else{
			echo $datajason;
			}
		}
		//funcion para saber si la linea tienes vinculado 
		//sublineas para poder ser eliminado
		function verificaSublinea(){
			$linea=$this->AutoLoadModel('linea');
			//$idlinea=$_REQUEST('idlinea');
			$cantidad=$linea->buscarSublineaxIdpadre($linea);
			echo json_encode($cantidad);

		}
		function listaLinea(){
			$linea=$this->AutoLoadModel('linea');
			$dataBusqueda=$linea->listaLineas();
			$cantidad=count($dataBusqueda);
			$option="<option value=''>Seleccione Linea</option>";
			for ($i=0; $i <$cantidad ; $i++) { 
				$option.="<option value=".$dataBusqueda[$i]['idlinea'].">".$dataBusqueda[$i]['nomlin']."</option>";
			}
			echo $option;
		}
		function listaSubLinea(){
			$idLinea=$_REQUEST['idLinea'];
			$linea=$this->AutoLoadModel('linea');
			$dataBusqueda=$linea->buscarSublinea($idLinea);
			$cantidad=count($dataBusqueda);
			$option="<option value=''>Seleccione Sub-Linea</option>";
			for ($i=0; $i <$cantidad ; $i++) { 
				$option.="<option value=".$dataBusqueda[$i]['idlinea'].">".$dataBusqueda[$i]['nomlin']."</option>";
			}
			echo $option;
		}

		function grabaJason(){
			$linea=$this->AutoLoadModel('linea');
			$data['nomlin']=$_REQUEST['nomlin'];
			$data['estado']=1;
			$exito=$linea->grabaLinea($data);
			if($exito){
				$dataResp['valid']=true;
				$dataResp['resp']='Dato Agregado';
				$dataResp['idlinea']=$exito;
				echo json_encode($dataResp);
			}else{
				$dataResp['valid']=false;
				$dataResp['resp']='No se pudo Agregar';
				echo json_encode($dataResp);
			}

		}
	}
?>