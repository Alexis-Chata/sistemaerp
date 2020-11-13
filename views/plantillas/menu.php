		<script type="text/javascript" charset="iso-8986">
				$(function(){
				
					$('#menuizq li a').click(function(event){
						var elem = $(this).next();
						if(elem.is('ul')){
							event.preventDefault();
							$('#menuizq ul:visible').not(elem).slideUp();
							elem.slideToggle();
						}
					});
				});
		</script>

<ul id="menuizq"><h4>Módulos del Sistema</h4>qqqqqq
<form action="" method="POST" id="frmParametros">
	<input type="hidden" name="opcion" id="opcion">
	<input type="hidden" name="modulo" id="modulo">
</form>

<?php
//llamando a un modelo
$datarol=New ActorRol();
$rol=$datarol->RolesxId($_SESSION['idactor']);			
$OpRol=New OpcionesRol();
$Opciones=$OpRol->OpcionesxRoles($rol);
$TotalOpciones=Count($Opciones);

for($i=0;$i<$TotalOpciones;$i++){
	$a=$Opciones[$i];
	session_start();
	$_SESSION['rrrr']="IUIUiu";
	$ta=count($a);
	echo "<li><img src=\"/imagenes/".$a[0]['icono']."\"  width=\"32px\"><a href=\"#\">".$a[0]['nombre']."</a>";
	echo "<ul>";
	for($j=1;$j<$ta;$j++){
		$_SESSION[$a[j]['url']]=$a[j]['url'];
		echo "<li><img src=\"/imagenes/iconos/arrow.png\"  width=\"12px\"><a href=\"".$a[$j]['url']."\" class=\"opciones\">".$a[$j]['nombre']."</a></li>";
	}
	echo "</ul></li>";
}
?>
</ul>