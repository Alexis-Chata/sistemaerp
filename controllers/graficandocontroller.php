<?php
    class GraficandoController extends ApplicationGeneral{
        function probando(){
            $this->AutoLoadLib(array('GoogChart','GoogChart.class'));

            $chart = new GoogChart(); //objeto
            $color1 = array( '#000000', '#FF0000', '#FFFF00','#00FF00','#00FFFF','#FF00FF');
            $color = array('#CE3636','#CEC036','#40CE36','#3662CE'); //colores
            $datos = array(enero=>50, febrero=>35, marzo=>89, abril=>45);//datos simples
            
            $chart->setChartAttrs( array(
            'type' => 'bar-vertical',
            'title' => 'Ventas 2012',
            'data' => $datos,
            'size' => array(1000, 200),
            'color' => $color1,
            'labelsXY' => true
            ));

            echo $chart;
            
             $chart2 = new GoogChart();
            
            $datosMultiple = array(
            'Año 2011' => array(
            enero => 30,
            febrero => 20,
            marzo => 45,
            abril => 75
            ),
            'Año 2012' => array(
            enero => 50,
            febrero => 35,
            marzo => 89,
            abril => 65
            ),
            'Año 2013' => array(
            enero => 30,
            febrero => 75,
            marzo => 11,
            abril => 95
            ),
            );
            
            $chart2->setChartAttrs( array(
            'type' => 'bar-vertical',
            'title' => 'Ventas 2012',
            'data' => $datosMultiple,
            'size' => array( 1000, 300 ),
            'color' => $color,
            'labelsXY' => true
            ));
            
            echo $chart2;
    }
}
?>