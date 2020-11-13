<?php
if (!isset($_SESSION)) {
    session_start();
} ?>
<?php
    $evaluarCredenciales=0;
    for ($j = 0; $j < count($_SESSION["credenciales"]); $j++) {
        if ($_SESSION["credenciales"][$j]["idmodulo"] == $idmodulo) {
                $c1_datashet =  $_SESSION["credenciales"][$j]["c1"];
                $c2_datashet = $_SESSION["credenciales"][$j]["c2"];
                $c3_datashet = $_SESSION["credenciales"][$j]["c3"];
                $c4_datashet = $_SESSION["credenciales"][$j]["c4"];
                $c5_datashet = $_SESSION["credenciales"][$j]["c5"];
                $c6_datashet = $_SESSION["credenciales"][$j]["c6"];
                $c7_datashet =  $_SESSION["credenciales"][$j]["c7"];
                $c8_datashet =$_SESSION["credenciales"][$j]["c8"];
                $c9_datashet =$_SESSION["credenciales"][$j]["c9"];
                $c10_datashet = $_SESSION["credenciales"][$j]["c10"];
                $c11_datashet = $_SESSION["credenciales"][$j]["c11"];
                $c12_datashet = $_SESSION["credenciales"][$j]["c12"];
                $c13_datashet =  $_SESSION["credenciales"][$j]["c13"];
                $c14_datashet =$_SESSION["credenciales"][$j]["c14"];
                $c15_datashet =$_SESSION["credenciales"][$j]["c15"];
                $evaluarCredenciales=1;
        }
    }
?>
<?php if($evaluarCredenciales==1 and $c15_datashet==0){ ?>
    <?php if($c1_datashet==0){ ?>
    <style type="text/css"> .c1_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c2_datashet==0){ ?>
    <style type="text/css"> .c2_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c3_datashet==0){ ?>
    <style type="text/css"> .c3_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c4_datashet==0){ ?>
    <style type="text/css"> .c4_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c5_datashet==0){ ?>
    <style type="text/css"> .c5_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c6_datashet==0){ ?>
    <style type="text/css"> .c6_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c7_datashet==0){ ?>
    <style type="text/css"> .c7_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c8_datashet==0){ ?>
    <style type="text/css"> .c8_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c9_datashet==0){ ?>
    <style type="text/css"> .c9_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c10_datashet==0){ ?>
    <style type="text/css"> .c10_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c11_datashet==0){ ?>
    <style type="text/css"> .c11_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c12_datashet==0){ ?>
    <style type="text/css"> .c12_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c13_datashet==0){ ?>
    <style type="text/css"> .c13_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c14_datashet==0){ ?>
    <style type="text/css"> .c14_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>
    <?php if($c15_datashet==0){ ?>
    <style type="text/css"> .c15_datashet{ pointer-events: none !important;cursor: default !important;opacity: 0.6 !important;  } </style>
    <?php } ?>

<?php } ?>










