<?php
	class AreaController extends ApplicationGeneral{
		function listaroptions(){
			$id=$_REQUEST['id'];
			$area=new Area();
			$data=$area->listadoOptionsarea($id);
			$tot=count($data);
			for($i=0;$i<$tot;$i++){
				echo '<option value="'.$data[$i]['idarea'].'">'.$data[$i]['nombre'];
			}
		}
	}
?>