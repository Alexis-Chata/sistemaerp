<?php

class saldosIniciales extends Applicationbase {

    private $tabla = 'wc_saldosiniciales';

    function verSaldonicial($idsaldo) {
        $data = $this->leeRegistro("wc_saldosiniciales si
                                    inner join wc_producto pro on si.idproducto = pro.idproducto
                                    inner join wc_unidadmedida um on um.idunidadmedida = pro.unidadmedida
                                    inner join wc_unidadmedidasunat ums on ums.idunimedsunat = um.idunimedsunat", "si.*, pro.nompro, pro.codigopa, ums.codigosunat", "si.idsaldo='" . $idsaldo . "' and si.estado='1'  and si.simulacro=0", "", "");
        return $data;
    }
    
    function verSaldonicialgeneral($idsaldo) {
        $data = $this->leeRegistro("wc_saldosiniciales si
                                    inner join wc_producto pro on si.idproducto = pro.idproducto
                                    LEFT join wc_unidadmedida um on um.idunidadmedida = pro.unidadmedida
                                    LEFT join wc_unidadmedidasunat ums on ums.idunimedsunat = um.idunimedsunat", "si.*, pro.nompro, pro.codigopa, ums.codigosunat", "si.idsaldo='" . $idsaldo . "' and si.estado='1'", "", "");
        return $data;
    }


    function listaSaldosInicialesPaginado($pagina, $filtro) {
        $data = $this->leeRegistroPaginado(
                "wc_saldosiniciales si inner join wc_producto pro on si.idproducto=pro.idproducto inner join wc_actor ac on si.usuariocreacion=ac.idactor
				", "si.idproducto,si.idsaldo,si.cantidad1,si.costounitario,si.tcambio,si.estado,si.fechacreacion as fecha_creacion_saldoinicial,si.fechasaldo,si.idmoneda,ac.nombrecompleto,ac.nombres,ac.apellidopaterno,ac.apellidomaterno,pro.*", "si.estado=1 and si.simulacro='0' " . $filtro, "si.fechacreacion desc", $pagina);
        return $data;
    }

    function paginadoSaldosIniciales($filtro) {
        return $this->paginado(
                        "wc_saldosiniciales si inner join wc_producto pro on si.idproducto=pro.idproducto inner join wc_actor ac on si.usuariocreacion=ac.idactor", "si.estado=1 and si.simulacro='0' " . $filtro);
    }

    function insertarSaldoInicial($idproducto, $cantidad1, $costounitario, $tcambio, $estado, $idmoneda, $fechasaldo, $usuariocreacion, $fechacreacion, $simulacro = 0) {
        $sql = "INSERT INTO wc_saldosiniciales
            (`idproducto`,
            `cantidad1`,
            `costounitario`,
            `tcambio`,
            `simulacro`,
            `estado`,
            `idmoneda`,
            `fechasaldo`,
            `usuariocreacion`,
            `fechacreacion`)
            VALUES
            ('" . $idproducto . "',
            '" . $cantidad1 . "',
            '" . $costounitario . "',
            '" . $tcambio . "',
            '" . $simulacro . "',       
            '" . $estado . "',
            '" . $idmoneda . "',
            '" . $fechasaldo . "',
            '" . $usuariocreacion . "',
            '" . $fechacreacion . "')";
        $data = $this->EjecutaConsulta($sql);
        if (count($data == 1)) {
            return true;
        } else {
            return false;
        }
    }

    function evaluarDuplicididad($idproducto, $fecha, $simulacro = 0) {
        $sql = "select * from wc_saldosiniciales where idproducto='" . $idproducto . "' and fechasaldo like '%" . $fecha . "%' and simulacro='" . $simulacro . "'  and estado=1;";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function listarSaldonicial($idproducto) {
        $data = $this->leeRegistroPaginado("wc_saldosiniciales si
                    inner join wc_producto pro on si.idproducto=pro.idproducto
                    inner join wc_actor ac on si.usuariocreacion=ac.idactor", "si.idsaldo,si.cantidad1,si.costounitario,si.tcambio,
                    si.estado,si.fechacreacion as fecha_creacion_saldoinicial,
                    si.fechasaldo,si.idmoneda,ac.nombrecompleto,ac.nombres,ac.apellidopaterno,ac.apellidomaterno,pro.*", "si.idproducto='$idproducto' and si.estado='1' and si.simulacro='0'", "si.fechasaldo asc", "");
        return $data;
    }

    function actualizarSaldoInicial($idsaldo, $cantidad1, $costounitario, $tcambio, $usuariomodificacion, $fechamodificacion) {
        $sql = "update wc_saldosiniciales set
            `cantidad1`='" . $cantidad1 . "',
            `costounitario`='" . $costounitario . "',
            `tcambio`='" . $tcambio . "',
            `usuariomodificacion`='" . $usuariomodificacion . "',
            `fechamodificacion`='" . $fechamodificacion . "' where idsaldo='" . $idsaldo . "'";
        $data = $this->EjecutaConsultaBoolean($sql);
        return $data;
    }

    function listaSaldosInicialesxProducto($idproducto, $simulacro = 0) {
        $data = $this->leeRegistro("wc_saldosiniciales", "*", "estado=1 and simulacro='$simulacro' and idproducto='$idproducto'", "", "");
        return $data;
    }

    function listaSaldosInicialesxProductoinicial($idproducto) {
        $data = $this->leeRegistro("wc_saldosiniciales", "*", "estado=1 and simulacro='0' and idproducto='$idproducto'", "fechasaldo asc", "limit 2");
        return $data;
    }

}

?>