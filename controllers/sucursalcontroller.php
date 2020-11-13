<?php

class SucursalController extends ApplicationGeneral {
    
    function habilitarDescarga() {
        $idordenventa = $_REQUEST['idordenventa'];
        $sucursal = $this->AutoLoadModel('sucursal');
        $resp['idsucursal'] = $sucursal->verificar($idordenventa);
        header('Content-type: application/json; charset=cp1252');
        echo json_encode($resp);
    }
    
}

?>