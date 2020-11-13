<?php

class Db{
	
	function conectar(){
	
            $link = mysql_connect("localhost","grupocel_susana","power2015");
            $flag= mysql_select_db("grupocel_catalogo",$link);
		}
	function leer_fotos($id){
		$sql="SELECT foto1.*,linea.nombre from foto1 inner join linea "
                   . "on foto1.idlinea=linea.id where foto1.idlinea=".$id;
	    $result= mysql_query($sql);
		return $result;
		}
	}

?>