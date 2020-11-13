<?php
	class PaisController extends ApplicationGeneral{
		
		function buscarautocompletepais(){
			$tex=$_REQUEST['term'];
			$pais=new Pais();
			$data=$pais->buscapaisautocomplete($tex);
			echo json_encode($data);
		}
		function buscarautocompletedepartamento(){
			$tex=$_REQUEST['term'];
			$codpais=$_REQUEST['term'];
			$pais=new Pais();
			$data=$pais->buscadepautocomplete($tex,$codpais);
			echo json_encode($data);
		}
		function listaroptions(){
			
		}
}
?>