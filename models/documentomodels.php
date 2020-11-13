<?php

class Documento extends Applicationbase {

    private $tabla = "wc_documento";

    function grabaDocumento($data) {
        $exito = $this->grabaRegistro($this->tabla, $data);
        return $exito;
    }
    
    function autocompletefacturaelectronica($text) {
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro($this->tabla, "numdoc,serie,iddocumento,idordenventa", "nombredoc=1 and estado=1 and electronico=1 and esAnulado!=1 and concat(serie,' ',numdoc) like '%" . $text . "%'", "");
        $dato = array();
        foreach ($datos as $valor) {
            $dato[] = array("value" => (str_pad($valor['serie'], 3, '0', STR_PAD_LEFT) . '-' . str_pad($valor['numdoc'], 8, '0', STR_PAD_LEFT)),
                "label" => 'F' . str_pad($valor['serie'], 3, '0', STR_PAD_LEFT) . '-' . str_pad($valor['numdoc'], 8, '0', STR_PAD_LEFT),
                "id" => $valor['iddocumento'],
                "idorden" => $valor['idordenventa']
            );
        }
        return $dato;
    }

    function autocompletefactura($text) {
        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
        $datos = $this->leeRegistro($this->tabla, "numdoc,serie,iddocumento,idordenventa", "nombredoc=1 and estado=1 and esAnulado!=1 and concat(serie,' ',numdoc) like '%" . $text . "%'", "");
        $dato = array();
        foreach ($datos as $valor) {
            $dato[] = array("value" => ($valor['serie'] . '-' . $valor['numdoc']),
                "label" => $valor['serie'] . '-' . $valor['numdoc'],
                "id" => $valor['iddocumento'],
                "idorden" => $valor['idordenventa']
            );
        }
        return $dato;
    }

    function buscaNotaCredito($iddocumento) {
        $condicion = "estado=1";
        if (!empty($iddocumento)) {
            $condicion = "nombredoc=5 and concepto=1 and iddocumento='$iddocumento' and estado=1 and esAnulado!=1 ";
        }
        $data = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        return $data;
    }

    function actualizarDocumento($data, $filtro) {
        $exito = $this->actualizaRegistro($this->tabla, $data, $filtro);
        return $exito;
    }
    
    function actualizarPercepcion($data, $filtro) {
        $exito = $this->actualizaRegistro("wc_ordenventa", $data, $filtro);
        return $exito;
    }

    function listaDocumentos($idordenventa, $nombredoc) {
        $condicion = "estado=1";
        if (!empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "idordenventa='$idordenventa' and estado=1 and nombredoc='$nombredoc'";
        } elseif (!empty($idordenventa) && empty($nombredoc)) {
            $condicion = "idordenventa='$idordenventa' and estado=1";
        } elseif (empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "nombredoc='$nombredoc' and estado=1";
        }
        $data = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        return $data;
    }

    function listaDocumentosSinAnulados($idordenventa, $nombredoc) {
        $condicion = "estado=1 nd esAnulado!=1 ";
        if (!empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "idordenventa='$idordenventa' and esAnulado!=1 and estado=1 and nombredoc='$nombredoc'";
        } elseif (!empty($idordenventa) && empty($nombredoc)) {
            $condicion = "idordenventa='$idordenventa' and estado=1 nd esAnulado!=1 ";
        } elseif (empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "nombredoc='$nombredoc' and estado=1 nd esAnulado!=1 ";
        }
        $data = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        return $data;
    }

    function listaDocumentos2($idordenventa, $nombredoc, $paraBusqueda = "") {
        $condicion2 = "";
        if (!empty($paraBusqueda)) {
            $condicion2 = " or wc_ordenventa.`codigov`='$paraBusqueda' or wc_documento.`numdoc`='$paraBusqueda' ";
        }

        $condicion = "wc_documento.`estado`=1  and wc_ordenventa.`estado`=1";
        if (!empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "wc_documento.`idordenventa`='$idordenventa' and wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 and wc_ordenventa.`nombredoc`='$nombredoc'";
        } elseif (!empty($idordenventa) && empty($nombredoc)) {
            $condicion = "wc_documento.`idordenventa`='$idordenventa' and wc_documento.`estado`=1  and wc_ordenventa.`estado`=1";
        } elseif (empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 and wc_ordenventa.`nombredoc`='$nombredoc'";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa 
     			INNER JOIN `wc_documento` wc_documento ON wc_ordenventa.`idordenventa` = wc_documento.`idordenventa`", "wc_documento.`serie`,
			    wc_documento.`numdoc`,
			    wc_documento.`iddocumento`,
			    wc_documento.`idordenVenta`,
			    wc_documento.`nombredoc`,
			    wc_documento.`fechadoc`,
			    wc_documento.`porcentajefactura`,
			    wc_documento.`montofacturado`,
			    wc_documento.`montoigv`,
			    wc_documento.`modofactura`,
			    wc_documento.`esImpreso`,
			    wc_ordenventa.`importeov`,
			    wc_ordenventa.`esfacturado`,
			    wc_ordenventa.`codigov`,
			    wc_ordenventa.`fordenventa`", $condicion + $condicion2, "", ""
        );
        return $data;
    }

    function cuentaDocumentos($idordenventa, $nombredoc, $paraBusqueda = "") {
        $condicion2 = "";
        if (!empty($paraBusqueda)) {
            $condicion2 = "and wc_ordenventa.`codigov`='$paraBusqueda' or wc_documento.`numdoc`='$paraBusqueda' ";
        }

        $condicion = "wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 ";
        if (!empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "wc_documento.`idordenventa`='$idordenventa' and wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 and wc_ordenventa.`nombredoc`='$nombredoc' ";
        } elseif (!empty($idordenventa) && empty($nombredoc)) {
            $condicion = "wc_documento.`idordenventa`='$idordenventa' and wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 ";
        } elseif (empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 and wc_ordenventa.`nombredoc`='$nombredoc' ";
        }
        $data = $this->leeRegistro(
                "`wc_ordenventa` wc_ordenventa 
     			INNER JOIN `wc_documento` wc_documento ON wc_ordenventa.`idordenventa` = wc_documento.`idordenventa` ", "count(*)", $condicion . $condicion2, "", ""
        );
        return $data[0]['count(*)'];
    }

    function listaDocumentosPaginado($idordenventa, $nombredoc, $pagina, $paraBusqueda = "") {
        $condicion2 = "";
        if (!empty($paraBusqueda)) {
            $condicion2 = " and wc_ordenventa.`codigov`='$paraBusqueda' or wc_documento.`numdoc`='$paraBusqueda' ";
        }

        $condicion = "wc_documento.`estado`=1  and wc_ordenventa.`estado`=1";
        if (!empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "wc_documento.`idordenventa`='$idordenventa' and wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 and wc_ordenventa.`nombredoc`='$nombredoc' ";
        } elseif (!empty($idordenventa) && empty($nombredoc)) {
            $condicion = "wc_documento.`idordenventa`='$idordenventa' and wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 ";
        } elseif (empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 and wc_ordenventa.`nombredoc`='$nombredoc' ";
        }
        $data = $this->leeRegistroPaginado(
                "`wc_ordenventa` wc_ordenventa 
     			INNER JOIN `wc_documento` wc_documento ON wc_ordenventa.`idordenventa` = wc_documento.`idordenventa`
     			INNER JOIN `wc_moneda` wc_moneda ON wc_ordenventa.`idmoneda`=wc_moneda.idmoneda", "wc_documento.`serie`,
			    wc_documento.`numdoc`,
			    wc_documento.`iddocumento`,
			    wc_documento.`idordenVenta`,
			    wc_documento.`nombredoc`,
			    wc_documento.`fechadoc`,
			    wc_documento.`porcentajefactura`,
			    wc_documento.`montofacturado`,
			    wc_documento.`montoigv`,
			    wc_documento.`modofactura`,
			    wc_documento.`esImpreso`,
			    wc_ordenventa.`importeov`,
			    wc_ordenventa.`esfacturado`,
			    wc_ordenventa.`codigov`,
			    wc_moneda.`simbolo`,			    
			    wc_ordenventa.`fordenventa`", $condicion . $condicion2, "wc_documento.`iddocumento` desc", $pagina);
        return $data;
    }

    function paginadoDocumentos($idordenventa, $nombredoc, $paraBusqueda = "") {
        $condicion2 = "";
        if (!empty($paraBusqueda)) {
            $condicion2 = " and wc_ordenventa.`codigov`='$paraBusqueda' or wc_documento.`numdoc`='$paraBusqueda' ";
        }

        $condicion = "wc_documento.`estado`=1  and wc_ordenventa.`estado`=1";
        if (!empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "wc_documento.`idordenventa`='$idordenventa' and wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 and wc_ordenventa.`nombredoc`='$nombredoc' ";
        } elseif (!empty($idordenventa) && empty($nombredoc)) {
            $condicion = "wc_documento.`idordenventa`='$idordenventa' and wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 ";
        } elseif (empty($idordenventa) && !empty($nombredoc)) {
            $condicion = "wc_documento.`estado`=1  and wc_ordenventa.`estado`=1 and wc_ordenventa.`nombredoc`='$nombredoc' ";
        }

        return $this->paginado(
                        "`wc_ordenventa` wc_ordenventa 
     			INNER JOIN `wc_documento` wc_documento ON wc_ordenventa.`idordenventa` = wc_documento.`idordenventa` ",
                        /* "wc_documento.`serie`,
                          wc_documento.`numdoc`,
                          wc_documento.`iddocumento`,
                          wc_documento.`idordenVenta`,
                          wc_documento.`nombredoc`,
                          wc_documento.`fechadoc`,
                          wc_documento.`porcentajefactura`,
                          wc_documento.`montofacturado`,
                          wc_documento.`montoigv`,
                          wc_documento.`modofactura`,
                          wc_documento.`esImpreso`,
                          wc_ordenventa.`importeov`,
                          wc_ordenventa.`esfacturado`,
                          wc_ordenventa.`codigov`,
                          wc_ordenventa.`fordenventa`", */ $condicion . $condicion2);
    }
    
    function buscaDocumento($iddocumento, $filtro) {
        $condicion = "estado=1";
        if (!empty($iddocumento) && !empty($filtro)) {
            $condicion = "$filtro and iddocumento='$iddocumento' and estado=1  ";
        } elseif (!empty($iddocumento) && empty($filtro)) {
            $condicion = "iddocumento='$iddocumento' and estado=1";
        } elseif (empty($iddocumento) && !empty($filtro)) {
            $condicion = "$filtro  and estado=1  ";
        }
        $data = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        return $data;
    }
    
    function buscaDocumentoXId($iddoc, $filtro, $noanulado = false) {
        $condicion = "doc.estado=1";
        if (!empty($filtro)) {
            $condicion = "doc.estado=1 and " . $filtro;
        } 
        $sql = "Select doc.*,mn.simbolo From " . $this->tabla . " doc
			Inner Join wc_ordenventa ov ON ov.idordenventa=doc.idordenventa
			Inner Join wc_moneda mn ON ov.IdMoneda=mn.idmoneda 
			Where " . $condicion .($noanulado ? " and doc.esanulado = 0" : "") . " and doc.iddocumento=" . $iddoc;
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function buscadocumentoxordenventa($idordenventa, $filtro, $noanulado = false) {
        $condicion = "doc.estado=1";
        if (!empty($idordenventa) && !empty($filtro)) {
            $condicion = "doc.idordenventa='$idordenventa' and doc.estado=1 and " . $filtro;
        } elseif (!empty($idordenventa) && empty($filtro)) {
            $condicion = "doc.idordenventa='$idordenventa' and doc.estado=1";
        } elseif (empty($idordenventa) && !empty($filtro)) {
            $condicion = " doc.estado=1 and " . $filtro;
        }
        $sql = "Select doc.*,mn.simbolo From " . $this->tabla . " doc
			Inner Join wc_ordenventa ov ON ov.idordenventa=doc.idordenventa
			Inner Join wc_moneda mn ON ov.IdMoneda=mn.idmoneda 
			Where " . $condicion .($noanulado ? " and doc.esanulado = 0" : "") . " ";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }

    function buscaletrasxordenventa($idordenventa, $filtro) {
        $condicion = "doc.estado=1";
        if (!empty($idordenventa) && !empty($filtro)) {
            $condicion = "doc.idordenventa='$idordenventa' and doc.estado=1 and " . $filtro;
        } elseif (!empty($idordenventa) && empty($filtro)) {
            $condicion = "doc.idordenventa='$idordenventa' and doc.estado=1";
        } elseif (empty($idordenventa) && !empty($filtro)) {
            $condicion = " estado=1 and " . $filtro;
        }
        $sql = "Select doc.*,mn.simbolo,det.fvencimiento,det.numerounico,det.recepcionLetras,
			CASE det.situacion  When '' Then 'Pendiente' else det.situacion END as situacion From " . $this->tabla . " doc
			Inner Join wc_ordenventa ov ON ov.idordenventa=doc.idordenventa
			Inner Join wc_moneda mn ON ov.IdMoneda=mn.idmoneda 
			Left Join wc_ordencobro oc ON ov.idordenventa=oc.idordenventa
			Inner Join wc_detalleordencobro det ON oc.idordencobro=det.idordencobro 
			and doc.numdoc=det.numeroletra and doc.nombredoc=7 and det.formacobro=3
			Where " . $condicion . " ";
        // $data=$this->leeRegistro($this->tabla." doc
        // 	Inner Join wc_ordenventa ov ON ov.idordenventa=doc.idordenventa
        // 	Inner Join wc_moneda mn ON ov.IdMoneda=mn.idmoneda","",$condicion,"iddocumento ","limit 0,1 ");
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
        
    function buscadocumentoxRelacionado($idordenventa, $idrelacionado, $filtro) {

        $condicion = "doc.estado=1 and doc.esAnulado!=1 and ";
        if (!empty($idordenventa) && !empty($filtro)) {
            $condicion = "doc.idordenventa='$idordenventa' and doc.estado=1 and doc.esAnulado!=1 and " . $filtro;
        } elseif (!empty($idordenventa) && empty($filtro)) {
            $condicion = "doc.idordenventa='$idordenventa' and doc.estado=1 and doc.esAnulado!=1 and ";
        } elseif (empty($idordenventa) && !empty($filtro)) {
            $condicion = " doc.estado=1 and  and doc.esAnulado!=1 and " . $filtro;
        }
        $data = $this->leeRegistro($this->tabla . " doc
				Inner Join wc_ordenventa OV ON ov.idordenventa=doc.idordenVenta
				Inner Join wc_moneda MN ON ov.IdMoneda=mn.idmoneda", "doc.*,mn.simbolo,mn.nombre", "doc.iddocumento='$idrelacionado' and " . $condicion, "iddocumento ", "limit 0,1 ");
        return $data;
    }

    function buscadocumentoxordenventaPrimero($idordenventa, $filtro) {
        $condicion = "doc.estado=1 and doc.esAnulado!=1";
        if (!empty($idordenventa) && !empty($filtro)) {
            $condicion = "doc.idordenventa='$idordenventa' and doc.estado=1 and doc.esAnulado!=1 and " . $filtro;
        } elseif (!empty($idordenventa) && empty($filtro)) {
            $condicion = "doc.idordenventa='$idordenventa' and doc.estado=1 and doc.esAnulado!=1 and ";
        } elseif (empty($idordenventa) && !empty($filtro)) {
            $condicion = " doc.estado=1 and doc.esAnulado!=1 and " . $filtro;
        }
        $data = $this->leeRegistro($this->tabla . " doc
				Inner Join wc_ordenventa OV ON ov.idordenventa=doc.idordenVenta
				Inner Join wc_moneda MN ON ov.IdMoneda=mn.idmoneda", "doc.*,mn.simbolo,mn.nombre", $condicion, "iddocumento ", "");
        return $data;
    }
    
    function sumaNotasCreditoXFactura($electronico, $serie, $nroFactura, $idordenventa, $iddoc="") {
        if ($electronico == 0) {
            $data = $this->leeRegistro($this->tabla, " sum(montofacturado) ", "numeroRelacionado=" . $nroFactura . " and idordenventa=" . $idordenventa . " and electronico=" . $electronico . " and serie=" . $serie . " and nombredoc='5' and estado=1 and esAnulado=0", "", "");
        } else {
            $data = $this->leeRegistro($this->tabla, " sum(montofacturado) ", "idRelacionado=" . $iddoc . " and idordenventa=" . $idordenventa . " and nombredoc='5' and estado=1 and esAnulado=0", "", "");
        }

        return $data[0]['sum(montofacturado)'];
    }


    function sumaNotasCredito($idordenventa) {

        $data = $this->leeRegistro($this->tabla, " sum(montofacturado) ", " idordenventa=" . $idordenventa . " and nombredoc='5' ", "", "");

        return $data[0]['sum(montofacturado)'];
    }

    function listaGuiasSinDocumentos($pagina, $paraBusqueda = "", $fechaini = "", $fechafin = "") {
        $condicion = "d.estado = 1 and d.nombredoc = 4 and (select count(*) from wc_documento dx where dx.idordenventa = d.idordenventa and (dx.nombredoc = 1 or dx.nombredoc = 2) and dx.esAnulado!=1) = 0";
        if (!empty($paraBusqueda)) {
            $condicion .= " and ov.codigov = '".$paraBusqueda."'";
        }
        if (!empty($fechaini) && !empty($fechafin)) {
            $condicion .= " and d.fechacreacion between '".$fechaini."' and '".$fechafin."'";
        } else if (!empty ($fechaini)) {
            $condicion .= " and d.fechacreacion >= '".$fechaini."'";
        } else if (!empty ($fechafin)) {
            $condicion .= " and d.fechacreacion <= '".$fechafin."'";
        }
        
        $data = $this->leeRegistroPaginado("wc_documento d
                inner join wc_ordenventa ov on ov.idordenventa = d.idordenventa
                left join wc_cliente cl on ov.idcliente = cl.idcliente",
                "distinct ov.idordenventa, ov.codigov, cl.razonsocial, substring(d.fechacreacion, 1, 10) as fechadoc, d.serie, d.numdoc",
                $condicion,
                "substring(d.fechacreacion, 1, 10), ov.codigov",
                $pagina);
        return $data;
    }

    function paginadoGuiasSinDocumentos($paraBusqueda = "", $fechaini = "", $fecfin = "") {
        $condicion = "d.estado = 1 and d.nombredoc = 4 and (select count(*) from wc_documento dx where dx.idordenventa = d.idordenventa and (dx.nombredoc = 1 or dx.nombredoc = 2)) = 0";
        if (!empty($paraBusqueda)) {
            $condicion .= " and ov.codigov = '".$paraBusqueda."'";
        }
        if (!empty($fechaini) && !empty($fechafin)) {
            $condicion .= " and d.fechacreacion between '".$fechaini."' and '".$fechafin."'";
        } else if (!empty ($fechaini)) {
            $condicion .= " and d.fechacreacion >= '".$fechaini."'";
        } else if (!empty ($fechafin)) {
            $condicion .= " and d.fechacreacion <= '".$fechafin."'";
        }

        return $this->paginado("wc_documento d
                inner join wc_ordenventa ov on ov.idordenventa = d.idordenventa
                left join wc_cliente cl on ov.idcliente = cl.idcliente", $condicion, "ov.idordenventa, ov.codigov, cl.razonsocial, substring(d.fechacreacion, 1, 10), d.serie, d.numdoc");
    }
    
    function getMonto ($fecha, $tipodocumento, $moneda) {
        $data = $this->leeRegistro($this->tabla . " doc
				Inner Join wc_ordenventa OV ON ov.idordenventa=doc.idordenVenta", 
                "SUM(doc.montofacturado) as montototal", "doc.estado=1 and doc.fechadoc='$fecha' and ov.idMoneda='$moneda' and ov.estado=1 and doc.esAnulado=0 and doc.nombredoc IN ($tipodocumento)", "", "");

	return $data[0]['montototal'];
    } 
    
    function getDetallePercepcion($idOrdenVenta, $filtro = "") {
        /*
        $sql = "select d.nombredoc, d.serie, d.numdoc, d.fechadoc, m.simbolo, ov.importeov, og.importegasto, sum(ov.importeov+og.importegasto) as subtotal
		from wc_ordenventa ov
                inner join wc_ordengasto og on og.idordenventa = ov.idordenventa
                inner join wc_documento d on d.idordenventa = ov.idordenventa
                inner join wc_moneda m on m.idmoneda = ov.idmoneda
                where ov.estado = 1 and og.idtipogasto = 6 and d.nombredoc=1 and d.esAnulado != 1 and og.estado = 1 and d.estado = 1 and og.importegasto>0 and ov.idOrdenVenta=" . $idOrdenVenta;-*/
        $sql = "select d2.serie as seriep, d2.numdoc as numdocp, d.iddocumento, d.idRelacionado, d.nombredoc, d.serie, d.numdoc, d.fechadoc, d.montofacturado, d.porcentajefactura , d.esCargado, d.esAnulado, m.simbolo, ov.percepcion
		from wc_ordenventa ov
                inner join wc_documento d on d.idordenventa = ov.idordenventa
                left join wc_documento d2 on d.idRelacionado = d2.iddocumento
                inner join wc_moneda m on m.idmoneda = ov.idmoneda
                where ov.estado = 1 and d.nombredoc=1 and d.estado = 1 and ov.idOrdenVenta=" . $idOrdenVenta . $filtro;
        $data = $this->EjecutaConsulta($sql);
        return $data;
    }
    
    public function ultimoCorrelativoElectronico($serie, $nombredoc) {                
        $condicion = "serie='$serie' and nombredoc='$nombredoc' and electronico=1 and estado=1";
        $data = $this->leeRegistro($this->tabla, "(numdoc*1) as numdoc", $condicion, "numdoc desc", "limit 1");
        if (count($data) == 0) return 1;         
        return $data[0]['numdoc']+1;
    }
    
    public function getIdDebitoXDetalle($idordenventa, $serie, $numdoc, $nombredoc, $fechadoc) {
        $condicion = "idordenventa='$idordenventa' and serie='$serie' and numdoc='$numdoc' and nombredoc='$nombredoc' and fechadoc='$fechadoc' and electronico=1 and estado=1";
        $data = $this->leeRegistro($this->tabla, "iddocumento", $condicion, "iddocumento desc", "limit 1");     
        return $data[0]['iddocumento'];
    }
    
    public function listaFacturasElectronicas($idordenventaVenta = "") {
        $condicion = "esAnulado!=1 and estado=1 and electronico=1 and nombredoc=1";
        if (!empty($idordenventaVenta)) $condicion .= " and idordenventa='$idordenventaVenta'";
        $data = $this->leeRegistro($this->tabla, "", $condicion, "", "");
        return $data;
    }    
    
    public function verificarPercepcion($idordenventaVenta) {
        $condicion = "idordenventa='$idordenventaVenta' and electronico=1 and nombredoc=10 and esAnulado!=1";
        $data = $this->leeRegistro($this->tabla, "iddocumento", $condicion, "", "");
        if (count($data) == 0) return 0;         
        return $data[0]['iddocumento'];
    }
    public function verificasidevoluciontienefactura($idordenventa) {
        $sql = "select * from wc_documento where idordenventa='".$idordenventa."' and nombredoc='1' and estado=1 and (esCargado=1 or esImpreso=1); ";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
    public function verificasidevoluciontieneboleta($idordenventa) {
        $sql = "select * from wc_documento where idordenventa='".$idordenventa."' and nombredoc='2' and estado=1 and (esCargado=1 or esImpreso=1); ";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    public function verificasidevoluciontienenotacredito($idordenventa,$iddevolucion) {
        $sql = "select * from wc_documento where idordenventa='".$idordenventa."' and nombredoc='5' and iddevolucion='".$iddevolucion."'  and estado=1 and (esCargado=1 or esImpreso=1); ";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
    
    public function listar_guia_remision($idordenventa) {
        $sql = "select * from wc_documento where idordenventa='".$idordenventa."' and nombredoc='4' and (esCargado=1 or esImpreso=1) and esAnulado=0";
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }
    public function listar_comprobantes($idordenventa,$esAnulado) {
        $sql = "SELECT * FROM wc_documento where idordenventa='".$idordenventa."' ";
        $sql.= "and nombredoc in('1','2') and estado=1 and (esCargado=1 or esImpreso=1) and CHARACTER_LENGTH(numdoc)<6";
        if($esAnulado=='0'){
            $sql.=" and esAnulado='".$esAnulado."'";
        }
        $scriptArrayCompleto = $this->scriptArrayCompleto($sql);
        return $scriptArrayCompleto;
    }

    function sumarTotalxDocumento($fechainicio = "", $fechafin = "", $tipodocumento = "", $electronico = "", $fisico = "", $moneda = "") {
        $filtro = '';
        if (!empty($fechainicio)) {
            $filtro .= " and documento.fechadoc >= '" . $fechainicio . "'";
        }
        if (!empty($fechafin)) {
            $filtro .= " and documento.fechadoc <= '" . $fechafin . "'";
        }
        if ($electronico == 1 && $fisico == 1) {
            $filtro .= " and (documento.esCargado = 1 or documento.esImpreso = 1)";
        } else if ($electronico == 0 && $fisico == 0) {
            $filtro .= " and documento.electronico = 1 and documento.esCargado = 1";
            $filtro .= " and documento.electronico = 0 and documento.esImpreso = 1";
        } else {
            if ($electronico == 1) {
                $filtro .= " and documento.electronico = 1 and documento.esCargado = 1";
            }
            if ($fisico == 1) {
                $filtro .= " and documento.electronico = 0 and documento.esImpreso = 1";
            }
        }
        $sql = "select sum(documento.montofacturado) as total
                    from wc_documento documento 
                    inner join wc_ordenventa ordenventa on ordenventa.idordenventa = documento.idordenventa and ordenventa.IdMoneda=" . $moneda . "
                    where documento.nombredoc='" . $tipodocumento . "' and                          
                          documento.esAnulado=0" . $filtro .
                    " order by documento.iddocumento desc";
        $data = $this->EjecutaConsulta($sql);
        if (!empty($data[0]['total']))
            return $data[0]['total'];
        return 0;
    }

    function listaDocumentoElectronico($txtFechaInicio, $txtFechaFin, $filtroSerie, $folioDesde, $folioHasta, $filtroComprobante) {
        $condicion = "wc_documento.electronico = 1 and wc_documento.estado = 1 and wc_documento.esCargado = 1 and wc_documento.numdoc*1 >= '$folioDesde' and wc_documento.numdoc*1 <= '$folioHasta' and wc_documento.fechadoc>='2019-05-27'";
        if (!empty($txtFechaInicio)) {
            $condicion .= " and wc_documento.fechadoc>='$txtFechaInicio'";
        }
        if (!empty($txtFechaFin)) {
            $condicion .= " and wc_documento.fechadoc<='$txtFechaFin'";
        }
        if (!empty($filtroSerie)) {
            if ($filtroSerie == 'F001') {
                $condicion .= " and wc_documento.nombredoc in (1, 5, 6)";
            } else if ($filtroSerie == 'B001') {
                $condicion .= " and wc_documento.nombredoc = 2";
            } else if ($filtroSerie == 'P001') {
                $condicion .= " and wc_documento.nombredoc = 10";
            }
            $condicion .= " and wc_documento.serie='1'";
        }
        if (!empty($filtroComprobante)) {
            $condicion .= " and wc_documento.nombredoc='$filtroComprobante'";
        }
        $data=$this->leeRegistro(
                "`wc_documento` wc_documento
                 INNER JOIN `wc_ordenventa` wc_ordenventa ON wc_ordenventa.`idordenventa` = wc_documento.`idordenventa`
                 INNER JOIN `wc_cliente` wc_cliente ON wc_cliente.`idcliente` = wc_ordenventa.`idcliente`
                 INNER JOIN `wc_moneda`  wc_moneda ON wc_ordenventa.Idmoneda=wc_moneda.IdMoneda",
                "
                wc_documento.*,    
                wc_cliente.`razonsocial`,
                wc_ordenventa.`codigov`,                    
                wc_moneda.`simbolo` as simbolomoneda",
                $condicion,
                "wc_documento.nombredoc, wc_documento.numdoc*1 asc",
                ""
                );
        return $data;
    }
        function documentosRegistrados($fechaDocumentos,$idDocumento) {
        $parametros='';
        switch ($idDocumento) {
            case 0:
                $parametros='1,2,5,6,10';
                break;
            case 1:
                $parametros='1';
                break;
            case 2:
                $parametros='2';
                break;
            case 3:
                $parametros='5';
                break;
            case 4:
                $parametros='6';
                break;
            case 5:
                $parametros='10';
                break;
        }
        $sql = "select d.iddocumento, d.serie, d.nombredoc, dt.nombre, d.numdoc, c.dni, c.ruc, ov.codigov, c.razonsocial,  d.montofacturado, d.montoigv, d.fechadoc, d.electronico, d.esCargado, d.esAnulado, d.esImpreso, d.estado from wc_documento as d
                    inner join wc_documentotipo as dt on d.nombredoc=dt.iddocumentotipo
                    inner join wc_ordenventa as ov on ov.idordenventa=d.idordenventa
                    inner join wc_cliente as c on c.idcliente=ov.idcliente
                    where (d.electronico=1 and d.esCargado=0 and d.esAnulado=0 and d.esImpreso=0 and d.estado=1) 
                            and d.nombredoc in (".$parametros.")
                            and d.fechadoc='".$fechaDocumentos."';";
        $data = $this->EjecutaConsulta($sql);
        return $data;
    } 
}

?>