<?php
if($_SESSION['Autenticado']==true){
	$datarol=New ActorRol();
	$rol=$datarol->RolesxId($_SESSION['idactor']);			
	$OpRol=New OpcionesRol();
	$Opciones=$OpRol->OpcionesxRoles($rol);
	$TotalOpciones=Count($Opciones);
	//print_r($Opciones);
}

?>
<?php include_once("partials/celestium/_headerc.phtml");?>
<div>
    <br>
    <?php include($path);?>
</div>

<?php include_once("partials/celestium/_footerc.phtml");?>
	
