
        <?php
        
        $titulo=$_POST["titulo"];
        $alternativo=$_POST["alt"];
        $linea= $_POST["linea"];
        
        
         //$target_path = "uploads/";
		$target_path = "samples/basic/pages/";
        $target_path = $target_path . basename( $_FILES['fotos']['name']); 
        //move_uploaded_file asegura archivo valido y mueve ruta destino
        //verificacio,ruta_destino
        //uploads/nameimagen.jpg
        if($_FILES['fotos']['tmp_name']!="") {
			//tmp_name donde esta guardado temporalmente el archivo en la pc wamp,y 
			//otro parametro es el destino
			copy($_FILES['fotos']['tmp_name'],$target_path);
            echo "El archivo ". basename( $_FILES['fotos']['tmp_name']). " ha sido subido";
			//'imprime..'.$_FILES['fotos']['name'];
        } else{
        echo "Ha ocurrido un error, trate de nuevo!";
        }
        //var_dump($_FILES);
		
        $fotos=$_FILES['fotos']['name'];	
        $link = mysql_connect("localhost","root","");
        $flag= mysql_select_db("fotos_catalogo",$link);
        
        $sql = "INSERT INTO foto1 (titulo, alternativo, img, idlinea ) VALUES ('$titulo', '$alternativo', '$fotos','$linea')";
        $result = mysql_query($sql);
        
        //echo $sql;
        if($result!=0 && $result==1){ 
            echo "Los datos se subieron on exito"."<br>";
           
        }
        header('Location: http://localhost/catalogo/');
 
        ?>

