<!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
<meta name="viewport" content="width = 1050, user-scalable = no" />
<script type="text/javascript" src="../../extras/jquery.min.1.7.js"></script>
<script type="text/javascript" src="../../extras/modernizr.2.5.3.min.js"></script>
</head>
<body>

<?php 
       $link = mysql_connect("localhost","root","");
       $flag= mysql_select_db("fotos_catalogo",$link);
	   //$sql="select * from foto1 order by id desc";
           $sql="SELECT foto1.*,linea.nombre from foto1 inner join linea "
                   . "on foto1.idlinea=linea.id where foto1.idlinea=1";
	   $result= mysql_query($sql);
	   $url="localhost/catalogo/";

?>

<div class="flipbook-viewport">
	<div class="container">
		<div class="flipbook">
        <!--498 x 646-->
        <?php
        while ($line = mysql_fetch_array($result)) {?>
<!--			<div style="background-image:url(pages/EXT-10325.jpg)"></div>-->
            <div style="background-image:url(pages/<?php echo $line['img'];?>)"></div>
            
        <?php }?> 
			<!--<div style="background-image:url(pages/2.jpg)"></div>
			<div style="background-image:url(pages/3.jpg)"></div>
			<div style="background-image:url(pages/4.jpg)"></div>
			<div style="background-image:url(pages/5.jpg)"></div>
			<div style="background-image:url(pages/6.jpg)"></div>
			<div style="background-image:url(pages/7.jpg)"></div>
			<div style="background-image:url(pages/8.jpg)"></div>
			<div style="background-image:url(pages/9.jpg)"></div>
			<div style="background-image:url(pages/10.jpg)"></div>
			<div style="background-image:url(pages/11.jpg)"></div>
			<div style="background-image:url(pages/12.jpg)"></div>-->
		</div>
	</div>
</div>


<script type="text/javascript">

function loadApp() {

	// Create the flipbook

	$('.flipbook').turn({
			// Width

			width:922,
			
			// Height

			height:600,

			// Elevation

			elevation: 50,
			
			// Enable gradients

			gradients: true,
			
			// Auto center this flipbook

			autoCenter: true

	});
}

// Load the HTML4 version if there's not CSS transform

yepnope({
	test : Modernizr.csstransforms,
	yep: ['../../lib/turn.js'],
	nope: ['../../lib/turn.html4.min.js'],
	both: ['css/basic.css'],
	complete: loadApp
});

</script>

</body>
</html>