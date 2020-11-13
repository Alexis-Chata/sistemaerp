<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            $link = mysql_connect("localhost","root","");
            $flag= mysql_select_db("fotos_catalogo",$link);
            
            $SQL = "select * from linea";
            $QUERY =  mysql_query($SQL);
        
        ?>
        
        
        <div style="margin: auto;width: 500px;">
            <h2>Power Acustik :catalogo</h2>
            <label style="margin-left: 250px"><a href="listar.php"> Listar Fotos:</a> </label><br><br>
            <form style="border: solid 1px #0066FF;padding: 7px"  method="POST" action="uploadcatalogo.php" enctype="multipart/form-data" >
            
                <label >Linea: </label>
                <select name="linea">
                    
             <?php

             while ( $resultado = mysql_fetch_array($QUERY)){

                 echo "<option  value='".$resultado['id']."'> ". $resultado['nombre']."</option>";

                  }

                ?>
                  
<!--                    <option value="radio">Radio, sonido y musica</option>
                    <option value="tv">tv</option>-->
                </select>
            
            <br><br>    
                
            <label>Titulo: </label><input type="text" name="titulo" placeholder="Ingrese ttulo"><br><br>
            <label>Alt: </label><input  style="margin-left: 20px;" type="text" name="alt" placeholder="Ingrese descripcion"><br><br>
            <input name="fotos" type="file" /><br><br>
            <input type="submit" value="Subir archivo" />
        </form>
        </div>
    </body>
</html>
