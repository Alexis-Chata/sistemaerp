<?php

class diseniocontroller extends ApplicationGeneral{
    function listaDisenio(){
        if (count($_REQUEST)== 6) {
            $this->view->show('/disenio/productodisenio.phtml');
        } else {
            $idProducto = $_REQUEST['idProducto'];
            $repote = new Disenio();
            $data = $repote->listaProducto($idProducto);
            $data2 = array();
            $i = 0;
            for ($i = 0; $i < count($data); $i++) {
                $data2[$i]['codigo'] = $data[$i]['codigopa'];
                $data2[$i]['nompro'] = $data[$i]['nompro'];
                $data2[$i]['unidm'] = $data[$i]['unidm'];
                $data2[$i]['codigooc'] = (empty($data[$i]['codigooc'])?'-':$data[$i]['codigooc']);
                $data2[$i]['fordencompra'] = (empty($data[$i]['fordencompra'])?'-':$data[$i]['fordencompra']);
                $data2[$i]['stockactual'] = $data[$i]['stockactual'];
                $data2[$i]['stockdisponible'] = $data[$i]['stockdisponible'];
                $data2[$i]['responsable'] = (empty($data[$i]['responsable'])?'-':$data[$i]['responsable']);
                $data2[$i]['codempaque'] = (empty($data[$i]['codempaque'])?'-':$data[$i]['codempaque']);
            }
            $objeto = $this->formatearparakui($data2);
            header("Content-type: application/json");
            echo json_encode($objeto);
        }
    }
}
?>
