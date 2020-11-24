<?php

class Proveedornacionalevaltecnica extends Applicationbase {

    private $tabla = "wc_proveedornacionalevaltecnica";

    function listado() {
        return $this->leeRegistro($this->tabla, "", "estado=1", "", "");
    }
    
    function listadoxproveedornacional($idproveedornacional) {
        return $this->leeRegistro($this->tabla . " proveedornacionalevaltecnica
                                  left join wc_evaluador evaluador ON evaluador.idevaluador = proveedornacionalevaltecnica.idevaluador and evaluador.estado = 1", 
                                "proveedornacionalevaltecnica.*, evaluador.nombre as nombreevaluador", "proveedornacionalevaltecnica.estado=1 and proveedornacionalevaltecnica.idproveedornacional='$idproveedornacional'", "", "");
    }

    function verificar($id, $productoservicio, $idevaluador, $condicion, $fecha, $idproveedornacionalevaltecnica) {
        $filtro = "estado=1 and idproveedornacional='$id' and productoservicio='$productoservicio' and idevaluador='$idevaluador' and condicion='$condicion' and fecha='$fecha'";
        if (!empty($idproveedornacionalevaltecnica)) {
            $filtro .= " and idproveedornacionaevaltecnica!='$idproveedornacionalevaltecnica'";
        }
        return $this->leeRegistro($this->tabla, "*", $filtro, "", "");
    }
    
    function actualiza($data, $idproveedornacionalevaltecnica) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "idproveedornacionaevaltecnica=$idproveedornacionalevaltecnica");
        return $exito;
    }

    function grabar($data) {
        return $this->grabaRegistro($this->tabla, $data);
    }
    
}

?>