<?php

class detalleOrdenCobro extends Applicationbase {

    private $tabla = "wc_detalleordencobro";

    function grabaDetalleOrdenVentaCobro($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }

    function listadoxidOrdenCobro($idOrdenCobro) {
        return $this->leeregistro($this->tabla, "", "idOrdenCobro=" . $idOrdenCobro . " and estado=1", "", "");
    }

    function totalLetrasxidOrdenCobro($idOrdenCobro) {
        $data = $this->leeRegistro($this->tabla, "count(*)", "idOrdenCobro=" . $idOrdenCobro . " and estado=1 and formacobro=3", "", "");
        return $data[0]['count(*)'];
    }

    function listadodeletrasXcobro($idOrdenCobro) {
        return $this->leeregistro($this->tabla, "", "idOrdenCobro=" . $idOrdenCobro . " and formacobro=3 and recepcionLetras='PA'", "", "");
    }

    function buscaRenovadoOrdenCobro($idOrdenCobro) {
        return $this->leeregistro($this->tabla, "", "estado='1' and gastosrenovacion=1 and idOrdenCobro=" . $idOrdenCobro, "", "");
    }

    function actualizar_cargado2($data, $nroletra) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "formacobro=3 and recepcionLetras='PA' and estacargada=0 and numeroletra=" . $nroletra);
        return $exito;
    }

    function actualizar_cargado($data, $iddetalle) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "iddetalleordencobro=$iddetalle");
        return $exito;
    }

    function actualizaDetalleOrdenCompraxFiltro($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }

    function eliminaxIdOrdenCobro($idOrdenCobro) {
        $exito = $this->cambiaEstado($this->tabla, "iddetalleordencobro=" . $idOrdenCobro);
    }

    function listadoxidOrdenCobroxrenovado($idOrdenCobro) {
        return $this->leeregistro($this->tabla, "", "idOrdenCobro='$idOrdenCobro' and renovado!=0 and situacion!='cancelado' and situacion!='anulado'", "", "");
    }

    function listadoxidOrdenCobrosinletras($idOrdenCobro) {
        return $this->leeregistro($this->tabla, "", "idOrdenCobro=" . $idOrdenCobro . " and formacobro!=3", "", "");
    }

    function listadoxidOrdenCobro2($idOrdenCobro) {
        return $this->leeregistro($this->tabla, "", "idOrdenCobro=" . $idOrdenCobro . " and formacobro=3", "", "");
    }

    function listadoxidOrdenCobroPendiente($idOrdenCobro) {
        return $this->leeregistro($this->tabla, "", "idOrdenCobro=" . $idOrdenCobro, "situacion=''", "");
    }

    function actualizaDetalleOrdencobro($data, $iddetalleordencobro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, "iddetalleordencobro=$iddetalleordencobro");
        return $exito;
    }

    function buscaDetalleOrdencobro($iddetalleordencobro) {
        $data = $this->leeRegistro($this->tabla, "", "iddetalleordencobro=$iddetalleordencobro", "");
        return $data;
    }

    function sacarIDOVxOrdenCobro($iddetalleordencobro) {
        $data = $this->leeRegistro($this->tabla . " doc inner join wc_ordencobro oc on oc.idordencobro=doc.idordencobro inner join wc_ordenventa ov.idordenventa=ov.idordenventa", "ov.idordenventa, ov.percepcion", "dociddetalleordencobro=$iddetalleordencobro", "");
        return $data;
    }

    function buscaDetalleOrdencobroxNumeroletra($numeroletra, $monto = "") {
        $data = $this->leeRegistro($this->tabla, "", "numeroletra='$numeroletra' and formacobro=3" . (!empty($monto) ? " and importedoc = '" . $monto . "'" : ""), "");
        return $data;
    }
    
    function verificarnumerounico($iddetalleordencobro) {
        $data = $this->leeRegistro($this->tabla . " doc 
                                   inner join wc_actor actor on actor.nombrecompleto = doc.numerounico and actor.estado = 1", "actor.idactor", "doc.iddetalleordencobro='$iddetalleordencobro' and doc.estado = 1 and doc.numerounico!=''", "", "limit 1");
        return (count($data) > 0 ? $data[0]['idactor'] : 0);
    }
    
    function buscaDetalleOrdencobro_pendiente($iddetalleordencobro) {
        $data = $this->leeRegistro($this->tabla, "*", "iddetalleordencobro='$iddetalleordencobro' and situacion='' and estado=1", "");
        return $data;
    }

    function buscaDetalleOrdencobro2($iddetalleordencobro) {
        $data = $this->leeRegistro(
                "`wc_detalleordencobro` doc inner join `wc_ordencobro` oc on doc.`idordencobro`=oc.`idordencobro`", "doc.`idordencobro`,
				doc.`renovado`,
				doc.`numeroletra`,
				doc.`saldodoc`,
                                doc.`numerounico`,
				doc.`fvencimiento`,
				doc.`referencia`,
				doc.`fechagiro`,
				doc.`importedoc`,
                                doc.`recepcionLetras` as rl,
				oc.`saldoordencobro`,
				oc.`tipoletra`", "doc.`iddetalleordencobro`=$iddetalleordencobro", "");
        return $data;
    }

    function listaConClientes($iddocumento) {
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa 
                                        INNER JOIN `wc_clientezona` wc_clientezona ON wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
                                        INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.`idmoneda`=wc_moneda.`idmoneda`
                                        INNER JOIN `wc_movimiento` wc_movimiento on wc_ordenventa.`idordenventa`=wc_movimiento.`idordenventa` 
                                        INNER JOIN `wc_cliente` wc_cliente ON wc_clientezona.`idcliente` = wc_cliente.`idcliente`
                                        INNER JOIN `wc_distrito` wc_distrito ON wc_cliente.`iddistrito` = wc_distrito.`iddistrito`
                                        INNER JOIN `wc_provincia` wc_provincia ON wc_distrito.`idprovincia` = wc_provincia.`idprovincia`
                                        INNER JOIN `wc_departamento` wc_departamento ON wc_provincia.`iddepartamento` = wc_departamento.`iddepartamento`
                                        INNER JOIN `wc_documento` wc_documento  ON  wc_documento.`idordenventa`=wc_ordenventa.`idordenventa`", "wc_cliente.`razonsocial`,
					wc_cliente.`direccion`,
					wc_cliente.`ruc`,
					wc_cliente.`dni`,
					wc_cliente.`telefono`,
					wc_cliente.`tipocliente`,
					wc_provincia.`nombreprovincia`,
					wc_departamento.`nombredepartamento`,
					wc_distrito.`nombredistrito`,
					wc_documento.`iddocumento`,
					wc_documento.`numdoc`,
					wc_documento.`nombredoc`,
					wc_documento.`esImpreso`,
					wc_ordenventa.`direccion_envio`,
					wc_documento.`esAnulado`,
                                        wc_documento.`montofacturado`,
					wc_moneda.`simbolo`,
					wc_moneda.`nombre`,
                                        wc_movimiento.`ndocumento`
					", "wc_documento.`iddocumento`='$iddocumento' AND wc_movimiento.iddevolucion =0", ""
        );
        return $data;
    }

    function GeneraNumeroLetra() {
        $data = $this->leeRegistro($this->tabla, "CONCAT( DATE_FORMAT( NOW( ) ,  '%y' ) , LPAD(  (MAX(SUBSTRING(`numeroletra`,3,6))+1) , 6,  '0' ) )  as maxletra", "`formacobro`=3 and year(`fechacreacion`)=year(now())", "", "");

        if ($data[0]['maxletra'] != "") {
            return $data[0]['maxletra'];
        } else {
            return date('y') . str_pad(1, 6, '0', STR_PAD_LEFT);
        }
    }

    function letranotadebito($codigov, $nroletra) {
        $cliente = $this->leeRegistro($this->tabla . " wdoc inner join wc_ordencobro woc on woc.idordencobro = wdoc.idordencobro and woc.estado=1
        inner join wc_ordenventa wov on wov.idordenventa = woc.idordenventa and wov.estado=1 and wov.esfacturado=1 and wov.codigov='$codigov'", "wdoc.numeroletra", "wdoc.numeroletra!='' and wdoc.estado=1 and (wdoc.formacobro=2 or wdoc.formacobro=3) and wdoc.numeroletra LIKE '%$nroletra%'", "", "limit 0,10");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => $valor['numeroletra'],
                "label" => $valor['numeroletra'],
            );
        }
        return $dato;
    }

    function buscaLetrasinCargar($nroletra) {
        $cliente = $this->leeRegistro($this->tabla, "iddetalleordencobro, numeroletra", "formacobro=3 and recepcionLetras='PA' and estacargada=0 and numeroletra LIKE '%$nroletra%'", "", "limit 0,10");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => $valor['numeroletra'],
                "label" => $valor['numeroletra'],
                "id" => $valor['iddetalleordencobro'],
            );
        }
        return $dato;
    }

    function buscaletrasPendientes($nroletra) {
        $cliente = $this->leeRegistro($this->tabla, "iddetalleordencobro, numeroletra", "formacobro=3 and recepcionLetras='' and situacion='' and numeroletra LIKE '%$nroletra%'", "", "limit 0,10");
        foreach ($cliente as $valor) {
            $dato[] = array("value" => $valor['numeroletra'],
                "label" => $valor['numeroletra'],
                "id" => $valor['iddetalleordencobro'],
            );
        }
        return $dato;
    }

    function fechagironrodias($idordencobro) {
        /* $sql="SELECT c.idPADREC 
          FROM wc_categoria c
          INNER JOIN wc_zona z ON z.idcategoria = c.idcategoria
          INNER JOIN wc_clientezona cz ON cz.idzona = z.idzona
          INNER JOIN wc_ordenventa ov ON cz.idclientezona = ov.idclientezona
          INNER JOIN wc_ordencobro oc ON oc.idordenventa = ov.idordenventa
          WHERE oc.idordencobro =31"; */
        $tabla = "wc_categoria c
				INNER JOIN wc_zona z ON z.idcategoria = c.idcategoria
				INNER JOIN wc_clientezona cz ON cz.idzona = z.idzona
				INNER JOIN wc_ordenventa ov ON cz.idclientezona = ov.idclientezona
				INNER JOIN wc_ordencobro oc ON oc.idordenventa = ov.idordenventa";
        $data = $this->leeRegistro($tabla, "c.idPADREC as zonacat", "oc.idordencobro =" . $idordencobro, "", "");
        return $data[0]['zonacat'];
    }

    function buscaLetra($letra) {
        $filtro = "numeroletra=$letra and protesto=1";
        $data = $this->leeRegistro($this->tabla, "importedoc", $filtro, "");
        return $data[0]['importedoc'];
    }

    function lista_info_cobro($idordenventa) {
        $sql1 = "select oCab.idordencobro,oCab.idordenventa,oCab.importeordencobro,oCab.situacion,oCab.estado,
        oDet.iddetalleordencobro,oDet.formacobro,oDet.importedoc,oDet.idpadre,oDet.numeroletra,oDet.situacion,oCab.escredito,oCab.escontado,oCab.esletras,oDet.formacobro
        from wc_ordencobro oCab,wc_detalleordencobro oDet
        where oCab.idordenventa=" . $idordenventa . "
        and oCab.idordencobro=oDet.idordencobro
        and oCab.estado=1
        and oDet.estado=1
        and oDet.idpadre=0
        order by oCab.idordencobro asc;";
        $data = $this->EjecutaConsulta($sql1);
        return $data;
    }

    function buscaDetalleOrdencobroespecial($iddetalleordencobro) {
        $data = $this->leeRegistro("wc_detalleordencobro doc 
                                   inner join wc_ordencobro oc on oc.idordencobro = doc.idordencobro and oc.estado = 1
                                   inner join wc_ordengasto og on og.idordenventa = oc.idordenventa and og.estado = 1 and og.idtipogasto = 2 and og.estado = 1 and og.importegasto > 0", 
                                    "doc.idordencobro, doc.iddetalleordencobro, doc.importedoc, doc.saldodoc, doc.situacion, doc.montoprotesto,
                                    oc.importeordencobro, oc.saldoordencobro, oc.situacion as situacionoc,
                                    og.idordengasto, og.importegasto", "doc.montoprotesto > 0 and doc.estado = 1 and doc.iddetalleordencobro=$iddetalleordencobro", "");
        return $data;
    }

}

?>
