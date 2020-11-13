<?php
	class ZonaController extends ApplicationGeneral{
		function nuevo(){
			$zona=$this->AutoLoadModel('zona');
			$data['categoriapadre']=$zona->listacategoriaHijo();

			$this->view->show("/zona/nuevo.phtml",$data);
		}
		function graba(){
			$data=$_REQUEST['Zona'];
			$z=new Zona();			
			$data['estado']=1;
			$exito=$z->grabar($data);
			if($exito){
				//$z->GeneraCodigo();
				$ruta['ruta']="/zona/lista/";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function editar(){
			$id=$_REQUEST['id'];
			$z=new Zona();
			$data['Zona']=$z->buscarxid($id);
			$data['Categoria']=$z->listacategoriaHijo();
			$this->view->show("/zona/editar.phtml",$data);
		}
		function actualiza(){
			$id=$_REQUEST['idZona'];
			$data=$_REQUEST['Zona'];			
			$z=new Zona();			
			$exito=$z->actualizar($id,$data);
			if($exito){				
				$ruta['ruta']="/zona/lista/";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function elimina(){
			$id=$_REQUEST['id'];
			$z=new Zona();
			$exito=$z->cambiaEstadoZona($id);
			echo $exito;
			if($exito){
				$ruta['ruta']="/zona/lista/";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function automcompletezona(){
			$zona=new Zona();
			$text=$_REQUEST['term'];
			$idcategoria=$_REQUEST['idcategoria'];
			$datoszona=$zona->buscazonaautocomplete($text,$idcategoria);
			echo json_encode($datoszona);
		}
		function buscarzonaxid(){
			$id=$_REQUEST['id'];
			$zona=new Zona();
			$Zonas=$zona->buscarxid($id);
			$total=count($Zonas);
			for($i=0;$i<$total;$i++){
				echo "<tr>";
					echo "<td>".$Zonas[$i]['idzona'];
					echo "<td>".$Zonas[$i]['nombrezona']."</td>";
					echo "<td>".$Zonas[$i]['observacion']."</td>";
					echo "<td><a href=\"/zona/editar/".$Zonas[$i]['idzona']."\" class=\"btnEditar\"><img src=\"/imagenes/editar.gif\"></a></td>";
					echo "<td><a href=\"/zona/eliminar/".$Zonas[$i]['idzona']."\" class=\"btnEliminar\"><img src=\"/imagenes/eliminar.gif\"></a></td>";
				echo "</tr>";
			}
		}

		function buscarzonaxnombre(){
			$text=$_REQUEST['id'];
			$zona=new Zona();
			$Zonas=$zona->buscarxtext($text);
			$total=count($Zonas);
			for($i=0;$i<$total;$i++){
				echo "<tr>";
					echo "<td>".$Zonas[$i]['idzona'];
					echo "<td>".$Zonas[$i]['nombrezona']."</td>";
					echo "<td>".$Zonas[$i]['observacion']."</td>";
					echo "<td><a href=\"/zona/editar/".$Zonas[$i]['idzona']."\" class=\"btnEditar\"><img src=\"/imagenes/editar.gif\"></a></td>";
					echo "<td><a href=\"/zona/eliminar/".$Zonas[$i]['idzona']."\" class=\"btnEliminar\"><img src=\"/imagenes/eliminar.gif\"></a></td>";
				echo "</tr>";
			}
		}
		function buscar(){
			if (empty($_REQUEST['id'])) {
				$_REQUEST['id']=1;
			}
			$filtro=$_REQUEST['id'];

			$filtro=($filtro!='')?"z.idcategoria=$filtro":"";
			$zona=new Zona();
			$data=$zona->listadoTotal("$filtro");
			$objeto=$this->formatearparakui($data);
			header("Content-type: application/json");
			//echo "{\"data\":" .json_encode($objeto). "}";
			echo json_encode($objeto);
		}

		/* categoria */
		function nuevacategoria(){
			$categoria=$this->AutoLoadModel('zona');
			$data['categorias']=$categoria->listaCategoriaPrincipal();
			$this->view->show("/zona/nuevacategoria.phtml",$data);	
		}
		function grabacategoria(){
			$data=$_REQUEST['Categoria'];
			$zona=new Zona();
			$id=$zona->grabacategoria($data);
			if($id){
				$ruta['ruta']="/zona/listacategoria/";
				$this->view->show("ruteador.phtml",$ruta);		
			}
		}
		function editarcategoria(){
			$zona=new Zona();
			$id=$_REQUEST['id'];
			$data['categorias']=$zona->listaCategoriaPrincipal();
			$data['Categoria']=$zona->buscarcategoria($id);
			$this->view->show("/zona/editarcategoria.phtml",$data);	
		}
		function actualizacategoria(){
			$zona=new Zona();
			$data=$_REQUEST['Categoria'];
			$idcategoria=$_REQUEST['idcategoria'];
			$exito=$zona->actualizacategoria($idcategoria,$data);
			if($exito){
				$ruta['ruta']="/zona/listacategoria/";
				$this->view->show("ruteador.phtml",$ruta);		
			}
		}
		function eliminacategoria(){
			$id=$_REQUEST['id'];
			$z=new Zona();
			$cantidad=$z->buscarZonasxIdCategoria($id);
			$ruta['ruta']="/zona/listacategoria/";
			if ($cantidad==0) {
				$exito=$z->cambiaEstadoCategoria($id);
				if($exito){
					$this->view->show("ruteador.phtml",$ruta);
				}
			}else{
				$this->view->show("ruteador.phtml",$ruta);
			}
			
		}
		function asignarcategoria(){
			$idzona=$_REQUEST['idzona'];
			$zona=new Zona();
			$data['Zona']=$zona->buscarxid($idzona);
			$data['Categorias']=$zona->listacategorias();
			$this->view->show("/zona/categoria.phtml",$data);
		}

		/*zona cliente*/
		function nuevoclientezona(){
			$cliente=new cliente();
			$zona= new Zona();
			$data['Cliente']=$cliente->listado();
			$data['Zona']=$zona->listado();
			$data['Categoria']=$zona->listacategorias();
			$this->view->show("/zona/nuevoclientezona.phtml",$data);
		}
		function editarclientezona(){
			$id=$_REQUEST['id'];
			$cliente=new cliente();
			$zona= new Zona();
			$data['Cliente']=$cliente->listado();
			$data['Zona']=$zona->listado();
			$data['Categoria']=$zona->listacategorias($id,$tamanio,"");
			$this->view->show("/zona/editar.phtml",$data);
		}
		function actualizaclientezona(){
			$id=$_REQUEST['idZona'];
			$data=$_REQUEST['Zona'];			
			$z=new Zona();			
			$exito=$z->actualizar($id,$data);
			if($exito){				
				$ruta['ruta']="/mantenimiento/clientezona";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function eliminaclientezona(){
			$id=$_REQUEST['id'];
			$z=new Zona();
			$exito=$z->eliminar($id);
			echo $exito;
			if($exito){
				$ruta['ruta']="/mantenimiento/clientezona/";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function lista(){
			$zona=$this->AutoLoadModel('zona');
			if (empty($_REQUEST['id'])) {
				$_REQUEST['id']=1;
			}
	
			$data['Categoria']=$zona->listacategoriaHijo();
			session_start();
			$_SESSION['P_zona']="";
			$data['zona']=$zona->listaZonaPaginado($_REQUEST['id']);

			$paginacion=$zona->paginadoZona();
			$data['paginacion']=$paginacion;
			$data['blockpaginas']=round($paginacion/10);

			$this->view->show("/zona/lista.phtml",$data);
		}
		function busca(){
			$zona=$this->AutoLoadModel('zona');
			if (empty($_REQUEST['id'])) {
				$_REQUEST['id']=1;
			}
			if (!empty($_REQUEST['txtBusqueda'])) {
				$_SESSION['P_zona']=$_REQUEST['txtBusqueda'];
			}
			$data['Categoria']=$zona->listacategoriaHijo();
			$parametro=$_SESSION['P_zona'];
			$paginacion=$zona->paginadoZonaxCategoria($parametro);
			$data['retorno']=$parametro;
			$data['zona']=$zona->listaZonaPaginadoxCategoria($_REQUEST['id'],$parametro);
			$data['paginacion']=$paginacion;
			$data['blockpaginas']=round($paginacion/10);
			$data['totregistros']=$zona->TotalzonasxCategoria($parametro);

			$this->view->show("/zona/busca.phtml",$data);
		}
		function listacategoria(){
			$categoria=$this->AutoLoadModel('zona');
			if (empty($_REQUEST['id'])) {
				$_REQUEST['id']=1;
			}
			$data['CategoriaPrincipal']=$categoria->listaCategoriaPrincipal();
			session_start();
			$_SESSION['P_categoria']="";
			$data['categoria']=$categoria->listaCategoriaPaginado($_REQUEST['id']);
			$paginacion=$categoria->paginadoCategoria();
			$data['paginacion']=$paginacion;
			$data['blockpaginas']=round($paginacion/10);
			$this->view->show("/zona/listacategoria.phtml",$data);
		}
		function buscacategoria(){
			$categoria=$this->AutoLoadModel('zona');
			if (empty($_REQUEST['id'])) {
				$_REQUEST['id']=1;
			}
			if (!empty($_REQUEST['txtBusqueda'])) {
				$_SESSION['P_categoria']=$_REQUEST['txtBusqueda'];
			}
			$data['CategoriaPrincipal']=$categoria->listaCategoriaPrincipal();
			$parametro=$_SESSION['P_categoria'];
			$paginacion=$categoria->paginadoCategoriaxParametro($parametro);
			$data['retorno']=$parametro;
			$data['categoria']=$categoria->listaCategoriaPaginadoxParametro($_REQUEST['id'],$parametro);
			$data['paginacion']=$paginacion;
			$data['blockpaginas']=round($paginacion/10);
			$data['totregistros']=$categoria->TotalCategoriasxParametro($parametro);

			$this->view->show("/zona/buscacategoria.phtml",$data);
		}
		function editarcategoriaprincipal(){
			
			$catprincipal=$this->AutoLoadModel('zona');
			$id=$_REQUEST['id'];
			
			$data['Categoria']=$catprincipal->buscarcategoria($id);
			$this->view->show("/zona/editarcategoriaprincipal.phtml",$data);	
		
		}

		function nuevocategoriaprincipal(){
			$this->view->show("/zona/nuevocategoriaprincipal.phtml",$data);	
		}
		function verificarExistenciaHijos(){
			$catprincipal=$this->AutoLoadModel('zona');
			$id=$_REQUEST['codigo'];
			$cantidadRegistros=$catprincipal->TotalCategoriasxParametro($id);
			if ($cantidadRegistros==0) {
				$data['error']='';
				$data['validado']=true;
				echo json_encode($data);
			}else{
				$data['error']='No se puede Eliminar por estar enlazado';
				$data['validado']=false;
				echo json_encode($data);
			}


		}
		function validarCodigoZona(){
			$zona=$this->AutoLoadModel('zona');
			$codigo=$_REQUEST['codigo'];
			$cantidadRegistros=$zona->verificarCodigoZona($codigo);
			if ($cantidadRegistros==0) {
				$data['error']='Codigo Aceptado';
				$data['validado']=true;
				echo json_encode($data);
			}else{
				$data['error']='El Codigo ya Existe';
				$data['validado']=false;
				echo json_encode($data);
			}
		}
		function validarCodigoCategoria(){
			$categoria=$this->AutoLoadModel('zona');
			$codigo=$_REQUEST['codigoc'];
			$cantidadRegistros=$categoria->verificarCodigoCategoria($codigo);
			if ($cantidadRegistros==0) {
				$data['error']='Codigo Aceptado';
				$data['validado']=true;
				echo json_encode($data);
			}else{
				$data['error']='El Codigo ya Existe';
				$data['validado']=false;
				echo json_encode($data);
			}
		}

		function listaCategoriaxPadre(){
			$idpadrec=$_REQUEST['idpadrec'];
			$categoria=$this->AutoLoadModel('zona');
			$dataBusqueda=$categoria->buscaCategoriaxPadre($idpadrec);
			
			$item="<option value=''>Zona Cobranza-Categoria</option>";
			if (!empty($dataBusqueda)) {
				foreach ($dataBusqueda as $value) {
					$item.="<option value=".$value['idcategoria'].">".$value['nombrec']."</option>";
				}
			}
			
			echo $item;
		}
		function listaZonasxCategoria(){
			$idcategoria=$_REQUEST['idzona'];
			$zona=$this->AutoLoadModel('zona');
			$dataBusqueda=$zona->buscaZonasxCategoria($idcategoria);
		
			$item="<option value=''>Zona Cobranza-Detalle</option>";
			if (!empty($dataBusqueda)) {
				foreach ($dataBusqueda as $value) {
					$item.="<option value=".$value['idzona'].">".$value['nombrezona']."</option>";
				}
			}
			
			echo $item;
		}
	}
?>