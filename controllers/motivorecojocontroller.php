<?php

class MotivorecojoController extends ApplicationGeneral {
    
    
    public function listarmotivoshtml() {
        $mrecojo = new Motivorecojo();
        $listado = $mrecojo->listado();
        $tam = count($listado);
        echo '<option value="">--- Elegir Motivo ---</option>';
        for ($i = 0; $i < $tam; $i++) {
            echo '<option value="' . $listado[$i]['idmotivorecojo'] . '">' . $listado[$i]['nombre'] . '</option>';
        }
    }
    
    public function grabamotivo() {
        $mrecojo = new Motivorecojo();
        $data['nombre'] = $_REQUEST['nombremotivo'];        
        $resp['nuevoid'] = $mrecojo->graba($data);;
        header('Content-type: application/json; charset=utf-8');
        echo json_encode($resp);
    }    
    
}

?>