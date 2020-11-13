<?php
class estadisticacontroller extends ApplicationGeneral{
    function cuadroestadistico(){
        $producto=$this->AutoLoadModel("Producto");
        $dataProducto=$producto->ValorizadoxLinea();
        
        $this->AutoLoadLib(array('GoogChart','GoogChart.class'));
        $data['grafico']=new GoogChart();
        
        $data['datos']=$dataProducto;
        $this->view->show("/producto/valorizadoxlinea.phtml",$data);
        
    }
    function pruebaEstadistico(){
        
        $estadistica=$this->AutoLoadModel("estadistica");
        $datos=$estadistica->vendedorxCantidadDeIngreso();
        
        $this->AutoLoadLib(array('GoogChart','GoogChart.class'));
        $data['grafico']=new GoogChart();
        
        $data['resultado']=$datos;
        $this->view->show("/estadistica/estadistica.phtml",$data);      
    }
}
