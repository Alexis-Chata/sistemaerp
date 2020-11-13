
        <?php
     
        $link = mysql_connect("localhost","root","");
        $flag= mysql_select_db("fotos_catalogo",$link);
        
        //echo $flag;
        $var_local="http://localhost/catalogo/lineas/pages/";
        
        $filtro=$_POST['linea'];
        
        if(isset($filtro)){
        
        $query = 'SELECT * FROM foto1 where idlinea='.$filtro;
        }else{
        $query = 'SELECT * FROM foto1';       
        }
        
        $ide=$_GET['ide'];
        
        if(isset($ide)){
            $sqle="delete from foto1 where id=".$ide;
            $resute=mysql_query($sqle) or die('Consulta fallida: ' . mysql_error());
        }
        
        
        $result = mysql_query($query) or die('Consulta fallida: ' . mysql_error());

        //lineas
        $SQLL = "select * from linea";
        $QUERYL =  mysql_query($SQLL);
        ?>
        <form style="border: solid 1px #0066FF;padding: 7px"  method="POST" action="" enctype="multipart/form-data" >
            
                <label >Linea: </label>
                <select name="linea">
        
        <?php

             while ( $resultado = mysql_fetch_array($QUERYL)){

                 echo "<option  value='".$resultado['id']."'> ". $resultado['nombre']."</option>";

                  }

          ?>
                  
<!--                    <option value="radio">Radio, sonido y musica</option>
                    <option value="tv">tv</option>-->
                </select>
                
             <input type="submit" value="Buscar" />
             <a href="index.php">volver</a>
        
            <br><br>    
                    
        <?php
            // Imprimir los resultados en HTML
            echo "<table>\n";
             echo "\t<tr style='color:red'>\n";
                            echo "\t\t<td>Nombre</td>\n";
                            echo "\t\t<td>Descripcion</td>\n";
                            echo "\t\t<td>Foto</td>\n";
                            echo "\t\t<td>Operacion</td>\n";
                echo "\t</tr>\n";
            
            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
                echo "\t<tr>\n";
                            echo "\t\t<td>".$line["titulo"]."</td>\n";
                            echo "\t\t<td>".$line["alternativo"]."</td>\n";
                            echo "\t\t<td><img src='".$var_local.$line["img"]."'"
                                    . "width='70' height='50'></td>\n";
                            echo "\t\t<td><a href='listar.php?ide=".$line["id"]."'>"
                                    . "<img src='".$var_local."tachito.jpg'"
                                    . "width='50' height='50' alt='Eliminar'></td>\n </a></td>\n";
                echo "\t</tr>\n";
            }
            echo "</table>\n";
        
        
        
        ?>
    
</form>

<style type="text/css">
    a{text-decoration: none;color: #000;font-size: 16px;}
    a:hover{color: red;text-decoration: underline;}
</style>