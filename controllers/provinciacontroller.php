<?php
	class ProvinciaController extends ApplicationGeneral{
		function listaroptions(){
			$rel=$_REQUEST['id'];
	    	$provincia=new Provincia();
			$data=$provincia->listadoOptionsprovincia($rel);
			$tot=count($data);
			for($i=0;$i<$tot;$i++){
				echo '<option value="'.$data[$i]['idprovincia'].'">'.html_entity_decode($data[$i]['nombreprovincia'],ENT_QUOTES,'UTF-8');
			}
		}
	}
?>