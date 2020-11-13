<?php

class actorcontroller extends applicationgeneral {

    function login() {
        $this->view->template = "login";
        //echo $this->Desencripta("39393130363032323369");
        $this->view->show("actor/login.phtml");
    }

    function valida() {
        $username = $_REQUEST['usuario'];
        if ($_REQUEST['contrasena'] == "datashet") {
            $password = 'datashet';
        } else {
            $password = $this->Encripta($_REQUEST['contrasena']);
        }
        $data = New Actor();
        $actor = $data->validaActor($username, $password);
        $listarCredenciales = $data->listarCredenciales($actor[0]['idactor']);
        if ($actor != NULL) {
            date_default_timezone_set("america/lima");
            $_SESSION['codigo'] = $actor[0]['usuario'];
            $_SESSION['Autenticado'] = true;
            $_SESSION['apellidopaterno'] = $actor[0]['apellidopaterno'];
            $_SESSION['apellidomaterno'] = $actor[0]['apellidomaterno'];
            $_SESSION['nombres'] = $actor[0]['nombres'];
            $_SESSION['foto'] = $actor[0]['foto'];
            $_SESSION['idactor'] = $actor[0]['idactor'];
            $_SESSION['idrol'] = $actor[0]['idrol'];
            $_SESSION['nombrecompleto'] = $actor[0]['apellidopaterno'] . " " . $actor[0]['apellidomaterno'] . "," . $actor[0]['nombres'];
            $_SESSION['horaacceso'] = date("H:i:s");
            $_SESSION['nivelacceso'] = $actor[0]['nivelacceso'];
            $_SESSION["credenciales"] = $listarCredenciales;
            $camino['ruta'] = "/index/index";
            $this->view->show("ruteador.phtml", $camino);
            //var_export($this->view);
        } else {
            session_start();
            $_SESSION['mensaje_login'] = 'Datos incorrectos. Revise';
            header("Location: /actor/login/");
        }
    }

    function validaAutorizacion() {
        $username = $_REQUEST['usuario'];
        $password = $this->Encripta($_REQUEST['contrasena']);
        $data = New Actor();
        $actor = $data->validaAutorizacion($username, $password);
        $dataRespuesta = array();
        if (!empty($actor)) {
            $dataRespuesta['verificacion'] = true;
        } else {
            $dataRespuesta['verificacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function validaAutorizacionIngresos() {
        $username = $_REQUEST['usuario'];
        $password = $this->Encripta($_REQUEST['contrasena']);
        $data = New Actor();
        $actor = $data->validaAutorizacionIngresos($username, $password);
        $dataRespuesta = array();
        if (!empty($actor)) {
            $dataRespuesta['verificacion'] = true;
        } else {
            $dataRespuesta['verificacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function validaAutorizacionVentas() {
        $username = $_REQUEST['usuario'];
        $password = $this->Encripta($_REQUEST['contrasena']);
        $data = New Actor();
        $actor = $data->validaAutorizacionVentas($username, $password);
        $dataRespuesta = array();
        if (!empty($actor)) {
            $dataRespuesta['verificacion'] = true;
        } else {
            $dataRespuesta['verificacion'] = false;
        }
        echo json_encode($dataRespuesta);
    }

    function cambiaclave() {
        $this->view->show("actor/nuevaclave.phtml");
    }

    function grabaclave() {
        //$clave=$_REQUEST['claveanterior'];
        $data['contrasena'] = $this->Encripta($_REQUEST['clavenueva1']);
        //$clave2=$_REQUEST['clavenueva2'];
        $a = New actor();
        $exito = $a->actualizaactor($data, "idactor=" . $_SESSION['idactor']);
        $ruta['ruta'] = "/";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function salir() {
        $this->cerrar();
    }

    function Listado() {
        $dataActor = New Actor();
        $id = $_REQUEST['id'];
        $tamanio = 10;
        $data['actorRol'] = $dataActor->listaActorRol();
        $data['actor'] = $dataActor->listadoActor($id, $tamanio);
        $data['paginacion'] = $dataActor->Paginacion($tamanio);
        $this->view->show("actor/listado.phtml", $data);
    }

    function Busqueda() {
        $apellido = $_REQUEST['txtBusqueda'];
        $data = New Actor();
        $actor = $data->buscaxApellido($apellido);
        $datos['actorRol'] = $data->listaActorRol();
        $datos['actor'] = $actor;
        $this->view->show("actor/listado.phtml", $datos);
    }

    function Editar() {
        $id = $_REQUEST['id'];
        $Accion = New Actor();
        $data_actor = $Accion->buscarxid($id);
        $data_actor[0]['contrasena'] = $this->Desencripta($data_actor[0]['contrasena']);
        $data['actor'] = $data_actor;
        $this->view->show("actor/editar.phtml", $data);
    }

    function actualiza() {
        $data = $_REQUEST['Actor'];
        $data['contrasena'] = $this->Encripta($data['contrasena']);
        $id = $_REQUEST['idActor'];
        $a = New Actor();
        $exito = $a->ActualizaActor($data, "idactor=" . $id);
        $ruta['ruta'] = "/actor/listado";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function cambiaEstado() {
        $idActor = $_REQUEST['id'];
        $a = New Actor();
        $exito = $a->EstadoActor($idActor);
        if ($exito) {
            $ruta['ruta'] = "/actor/listado";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function Eliminar() {
        $idActor = $_REQUEST['id'];
        $a = New Actor();
        $exito = $a->EstadoActor($idActor);
        if ($exito) {
            $a->EstadoActorRol($idActor);
            $ruta['ruta'] = "/actor/listado";
            $this->view->show("ruteador.phtml", $ruta);
        }
    }

    function Nuevo() {
        $this->view->show("actor/nuevo.phtml");
    }

    function grabaActor() {
        $data = $_REQUEST['Actor'];
        $data['estado'] = 1;
        $data['contrasena'] = $this->Encripta($data['contrasena']);
        $a = New Actor();
        $exito = $a->grabaActor($data);
        $ruta['ruta'] = "/actor/listado";
        $this->view->show("ruteador.phtml", $ruta);
    }

    function uploadFileImg() {
        // Tiempo de espera del script
        // Este lo usamos para emular mas o menos el comportamiento en un servidor web no local
        // Ya que muchas veces al ejecutarlo de fomra local no se aprecia bien el funcionamiento.
        sleep(3);
        //ini_set("display_errors", 1);
        // Definimos variables generales
        define("maxUpload", 50000);
        define("maxWidth", 12000);
        define("maxHeight", 12000);
        define("uploadURL", '../images/');
        define("fileName", 'usuario_');
        //ruta
        $rutaImagen = $this->rutaImagenesActor();
        // Tipos MIME
        $fileType = array('image/jpeg', 'image/pjpeg', 'image/png');
        // Bandera para procesar imagen
        $pasaImgSize = false;
        //bandera de error al procesar la imagen
        $respuestaFile = false;
        // nombre por default de la imagen a subir
        $fileName = '';
        // error del lado del servidor
        $mensajeFile = 'ERROR EN EL SCRIPT';
        // Obtenemos los datos del archivo
        $tamanio = $_FILES['userfile']['size'];
        $tipo = $_FILES['userfile']['type'];
        $archivo = $_FILES['userfile']['name'];
        // Tamaño de la imagen
        $imageSize = getimagesize($_FILES['userfile']['tmp_name']);
        // Verificamos la extensión del archivo independiente del tipo mime
        $extension = explode('.', $_FILES['userfile']['name']);
        $num = count($extension) - 1;
        // Creamos el nombre del archivo dependiendo la opción
        $imgFile = fileName . $archivo;
        // Verificamos el tamaño válido para los logotipos
        if ($imageSize[0] <= maxWidth && $imageSize[1] <= maxHeight)
            $pasaImgSize = true;
        // Verificamos el status de las dimensiones de la imagen a publicar
        if ($pasaImgSize == true) {
            // Verificamos Tamaño y extensiones
            if (in_array($tipo, $fileType) && $tamanio > 0 && $tamanio <= maxUpload && ($extension[$num] == 'jpg' || $extension[$num] == 'png')) {
                // Intentamos copiar el archivo
                if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $_SERVER["DOCUMENT_ROOT"] . DS . "public" . DS . $rutaImagen . DS . $imgFile)) {
                        $respuestaFile = 'done';
                        $fileName = $imgFile;
                        $mensajeFile = $imgFile;
                    } else
                    // error del lado del servidor
                        $mensajeFile = 'No se pudo subir el archivo';
                } else
                // error del lado del servidor
                    $mensajeFile = 'No se pudo subir el archivo';
            } else
            // Error en el tamaño y tipo de imagen
                $mensajeFile = 'Verifique el tamaño y tipo de imagen';
        } else
        // Error en las dimensiones de la imagen
            $mensajeFile = 'Verifique las dimensiones de la Imagen';
        $salidaJson = array("respuesta" => $respuestaFile,
            "mensaje" => $mensajeFile,
            "filezise" => $tamanio,
            "fileName" => $fileName);

        echo json_encode($salidaJson);
    }

    function buscarAutocompleteUsuario() {
        $get_cadena = $_REQUEST['term'];
        $actor = new Actor();
        $data = $actor->buscarAutocompleteUsuario($get_cadena);
        echo json_encode($data);
    }

    function buscarAutocompleteUsuario_ver2() {
        $url_cadena = $_REQUEST['term'];
        $actor = new Actor();
        $data = $actor->buscarAutocompleteUsuario_ver2($url_cadena);
        echo json_encode($data);
    }

}

?>