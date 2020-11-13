<?php

Class seguridadcontroller extends ApplicationGeneral {

    /**
     * Function	: listado
     * Author	: Fernando Garcia Atuncar
     */
    function listado() {
        $data = New Actor();
        $actor = $data->listadoActor();
        $total = count($actor);
        for ($i = 0; $i < $total; $i++) {
            $actor[$i]['contrasena'] = $this->Desencripta($actor[$i]['contrasena']);
        }
        $datos['listaactor'] = $actor;
        $this->view->show("/seguridad/listado.phtml", $datos);
    }

    /**
     * 	Funcion :AsignarOpciones
     * 	Author	:Fernando Garcia Atuncar
     */
    function AsignarOpciones() {
        $Opciones = New Opciones();
        $data['Listado'] = $Opciones->ListadoOpciones();
        $rol = New Rol();
        $dataRol = $rol->RolCombo();
        $data['Rol'] = $dataRol;
        $this->view->show("/seguridad/AsignarOpcionRol.phtml", $data);
    }

    function AsignarxRol() {
        if (!isset($_REQUEST['idRol'])) {
            $idRol = 0;
        } else {
            $idRol = $_REQUEST['idRol'];
        }
        $data['idRol'] = $idRol;
        $Opciones = New Opciones();
        $data['Listado'] = $Opciones->ListadoOpciones();
        $rol = New Rol();
        $dataRol = $rol->RolCombo();
        $data['Rol'] = $dataRol;
        $OpcRol = New OpcionesRol();
        $data['dataRol'] = $OpcRol->OpcionesListaxId($idRol);
        $this->view->show("/seguridad/AsignarOpcionRol.phtml", $data);
    }

    function asignaropcionxrol() {
        $data['idrol'] = $_REQUEST['Rol'];
        $data['idopciones'] = $_REQUEST['Opcion'];
        $data['estado'] = "1";
        $OpcionRol = New OpcionesRol();
        $exito = $OpcionRol->GrabaOpcionesRol($data);
        if ($exito) {
            echo "Permiso asignado con &eacute;xito";
        }
    }

    function desasignaropcionxrol() {
        $data['idrol'] = $_REQUEST['Rol'];
        $data['idopciones'] = $_REQUEST['Opcion'];
        $data['estado'] = "1";
        $OpcionRol = New OpcionesRol();
        $exito = $OpcionRol->EliminaOpcionesRol($data);
        if ($exito) {
            echo "Permiso quitado con &eacute;xito";
        }
    }

    //ASIGNADO ROLES A USUARIOS.
    function AsignarRoles() {
        if (!empty($_REQUEST['id'])) {
            $idActor = $_REQUEST['id'];
            $Actor = $this->AutoloadModel("actor");
            $data['Actor'] = $Actor->buscarxid($idActor);
            $data['ActorRol'] = $Actor->listaRolesxIdActor($idActor);
            $Rol = $this->AutoloadModel("rol");
            $data['Roles'] = $Rol->RolCombo();
            $this->view->show("/seguridad/AsignarRolActor.phtml", $data);
        } else {
            $this->view->show("/seguridad/BuscarActor.phtml", $data);
        }
    }

    function asignarrolxactor() {
        $data['idrol'] = $_REQUEST['Rol'];
        $data['idactor'] = $_REQUEST['Actor'];
        $data['estado'] = "1";
        $ActorRol = New ActorRol();
        $exito = $ActorRol->grabaActorRol($data);
        if ($exito) {
            echo "Rol grabado correctamente";
        }
    }

    function desasignarrolxactor() {
        $idrol = $_REQUEST['Rol'];
        $idactor = $_REQUEST['Actor'];
        $ActorRol = New ActorRol();
        $exito = $ActorRol->eliminaActorRol("idactor='" . $idactor . "' and idrol='" . $idrol . "'");
        if ($exito) {
            echo "Rol borrado correctamente";
        }
    }
    
function cambioPrecio07072020() {
        // Abriendo el archivo
        //$archivo = fopen("archivo.txt", "r");
        // Recorremos todas las lineas del archivo
        $producto = new Producto();
        //$producto->buscaxcodigo();
        
        $contenido = "";
        
        $ArrayContenido = explode('///', $contenido);
        $tam = count($ArrayContenido);
        for ($i = 0; $i < $tam; $i++) {
            $linea = explode('*****', $ArrayContenido[$i]);
            $codigopa = trim($linea[0]);
            $dataProducto = $producto->buscaxcodigo($codigopa);
            if (count($dataProducto) > 0) {
                $preciolista = trim($linea[1])*1;
                $dataAct['preciolista'] = trim($linea[1]);
                $dataAct['preciolistadolares'] = $preciolista/3.60;
                $producto->actualizaProducto($dataAct, $dataProducto[0]['idproducto']);
                echo $dataProducto[0]['idproducto'] .': ' . trim($linea[1]);
            } else {
                echo 'problemas en el codigo ' . $linea[0];
            }
            echo '<br>';
        }
        echo '<br>';/*
        while (!feof($archivo)) {
            $linea = fgets($archivo);
            $traer = iconv(mb_detect_encoding($linea), "UTF-8", $linea);
            $arregloProducto = explode('*****', $traer);
            $codigopa = trim($arregloProducto[0]);
            $dataProducto = $producto->buscaxcodigo($codigopa);
            
            //echo trim($arregloProducto[0]) . trim($arregloProducto[1]);
            
        }*/
// Cerrando el archivo
        fclose($archivo);
        //$this->view->show("/seguridad/reactivaperu.phtml", $data);
    }
    
}

?>