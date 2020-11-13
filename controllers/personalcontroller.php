<?php
class PersonalController extends ApplicationGeneral{			
		function listar(){
			$pais=new Pais();
			$depa=new Departamento();
			$prov=new Provincia();
			$dist=new Distrito();
			$personal=new Personal();			
			$data['pais']=$pais->listadopais();
			$data['departamento']=$depa->listado();
			$data['provincia']=$prov->listado();
			$data['distrito']=$dist->listado();
			$this->view->show("/personal/listar.phtml",$data);
		}
		function graba(){
			$data=$_REQUEST['Personal'];
			$d=$_REQUEST['idpersonal'];
			$personal=new Personal();
			if($d!=null){
				//$exito=$personal->actualizapersonal($data,"idpersonal=".$d);	
			}else{
				$exito=$personal->grabapersonal($data);
			}
			if($exito){
				$ruta['ruta']="/ordenventa/nuevo";
				$this->view->show("ruteador.phtml",$ruta);
			}
		}
		function buscarautocomplete(){
			$tex=$_REQUEST['term'];
			$personal=new Personal();
			$data=$personal->buscapersonalautocomplete($tex);
			echo json_encode($data);
		}
		function buscarautocompletevendedor(){
			$tex=$_REQUEST['term'];
			$vendedor=new Personal();
			$data=$vendedor->buscavendedorautocomplete($tex);
			echo json_encode($data);
		}
		function buscarclieruc(){
			$ruc=$_REQUEST['ruc'];
			$personal= new Personal();
			$data=$personal->buscarcliordenventa($ruc);
			echo'{
				"razonsocial":"'.$data[0]['razonsocial'].'",
				"codigo":"'.$data[0]['idpersonal'].'",
				"telefono":"'.$data[0]['telefonofijo'].'",
				"direccion":"'.$data[0]['direccion'].'",
				"celular":"'.$data[0]['celular'].'",
				"ruc":"'.$data[0]['ruc'].'",
				"email":"'.$data[0]['email'].'",
				"apaterno":"'.$data[0]['apaterno'].'",
				"amaterno":"'.$data[0]['amaterno'].'",
				"nombre":"'.$data[0]['nombres'].'",
				"dni":"'.$data[0]['dni'].'",
				"estadocivil":"'.$data[0]['estadocivil'].'",
				"fechanac":"'.$data[0]['fechanac'].'",
				"genero":"'.$data[0]['genero'].'",
				"provincia":"'.$data[0]['nompro'].'",
				"departamento":"'.$data[0]['iddepartamento'].'",
				"distrito":"'.$data[0]['iddistrito'].'",
				"nomdis":"'.$data[0]['nomdis'].'",
				"pais":"'.$data[0]['idpais'].'"
			}';
		}
		function buscarcliedni(){
			$dni=$_REQUEST['dni'];
			$personal= new Personal();
			$data=$personal->buscarcliordenventadni($dni);
			echo'{
				"nombres":"'.$data[0]['nombres'].' '.$data[0]['apaterno'].' '.$data[0]['amaterno'].'",
				"codigo":"'.$data[0]['codigo'].'",
				"telefono":"'.$data[0]['telefono'].'",
				"direccion":"'.$data[0]['direccion'].'",
				"celular":"'.$data[0]['celular'].'",
				"email":"'.$data[0]['email'].'",
				"nombre":"'.$data[0]['nombres'].'",
				"apaterno":"'.$data[0]['apellidopaterno'].'",
				"amaterno":"'.$data[0]['apellidomaterno'].'",
				"ruc":"'.$data[0]['ruc'].'",
				"dni":"'.$data[0]['dni'].'",
				"estadocivil":"'.$data[0]['estadocivil'].'",
				"razonsocial":"'.$data[0]['razonsocial'].'",
				"fechanac":"'.$data[0]['fechanacimiento'].'",
				"genero":"'.$data[0]['genero'].'",
				"provincia":"'.$data[0]['idprovincia'].'",
				"departamento":"'.$data[0]['iddepartamento'].'",
				"distrito":"'.$data[0]['iddistrito'].'",
				"nomdis":"'.$data[0]['nomdis'].'",
				"pais":"'.$data[0]['idpais'].'"
			}';			
		}
		function listaroptions(){
			$codpais=$_REQUEST['id'];
			//echo $codpais;
			//exit();
		    $departamento=new Departamento();
			$personal=new Personal();
			$data=$departamento->listadoOptionsdepartamento($codpais);			
			for($i=0;$i<count($data);$i++){
				echo '<option value="'.$data[$i]['id'].'">'.$data[$i]['nombre'].'</option>';
			}
		}
		function listaroptionsdepar(){
			$iddepartamento=$_REQUEST['id'];
		    $provincia=new Provincia();
			$personal=new Personal();
			$data=$provincia->listadoOptionsprovincia($iddepartamento);
			for($i=0;$i<count($data);$i++){
				echo '<option value="'.$data[$i]['id'].'">'.$data[$i]['nombre'].'</option>';
			}
		}
		function listaroptionsprov(){
			$idprovincia=$_REQUEST['id'];
		    $distrito=new Distrito();
			$data=$distrito->listadoOptionsdistrito($idprovincia);
			for($i=0;$i<count($data);$i++){
				echo '<option value="'.$data[$i]['id'].'">'.$data[$i]['nombre'].'</option>';
			}
		}
		
}
?>