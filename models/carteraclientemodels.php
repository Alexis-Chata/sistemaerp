<?php

class CarteraCliente extends Applicationbase {

    private $tablas;
    private $idvend;
    private $condicion;
    private $catprin;
    private $regcobr;
    private $zona;
    private $fecini;
    private $fecfin;
    private $depa;
    private $prov;
    private $dist;
    private $ordenar;
    private $cond;

    function __construct($idvend, $condicion, $catprin, $regcobr, $zona, $fecini, $fecfin, $depa, $prov, $dist, $ordenar, $aprobados) {
        $this->tablas = "wc_cliente c
                        inner join wc_zona z on c.zona = z.idzona
                        inner join wc_categoria ct on ct.idcategoria = z.idcategoria
                        inner join wc_distrito d on c.iddistrito = d.iddistrito
                        inner join wc_provincia p on p.idprovincia = d.idprovincia
                        inner join wc_departamento t on p.iddepartamento = t.iddepartamento
                        inner join wc_ordenventa uov on uov.idordenventa = c.idultimaorden
                        inner join wc_ordenventa ov on ov.idcliente = c.idcliente";
        $this->idvend = $idvend;
        $this->condicion = $condicion;
        $this->catprin = $catprin;
        $this->regcobr = $regcobr;
        $this->zona = $zona;
        $this->fecini = $fecini;
        $this->fecfin = $fecfin;
        $this->depa = $depa;
        $this->prov = $prov;
        $this->dist = $dist;
        $this->ordenar = $ordenar;
        $this->cond = "c.estado = 1";
        switch ($aprobados) {
            case 1: $this->aprobados = "";
                break;
            case 2: $this->aprobados = "ov.vbcreditos=1";
                break;
            case 3: $this->aprobados = "ov.fechadespacho='0000-00-00'";
                break;
            case 4: $this->aprobados = "ov.desaprobado='1'";
                break;
        }
    }

    public function listarCarteraPaginado($pagina) {
        $var_config = parse_ini_file("config.ini", true);
        $tamanio = $var_config['Parametros']['Paginacion'];
        if (!empty($pagina)) {
            $inicio = ($pagina - 1) * $tamanio;
            if ($inicio < 0) {
                $inicio = 0;
            }
            $limit = "limit " . $inicio . "," . $tamanio;
        } else {
            $limit = "limit 0," . $tamanio;
        }
        $condicion = $this->cond;
        $condicion .= !empty($this->idvend) ? " and ov.idvendedor='$this->idvend'" : "";

        if (!empty($this->condicion)) {
            switch ($this->condicion) {
                case '1': {
                        $condicion .= " and ov.es_contado = 1 and ov.es_credito = 0 and ov.es_letras = 0";
                        break;
                    }
                case '2': {
                        $condicion .= " and ov.es_credito = 1";
                        break;
                    }
                case '3': {
                        $condicion .= " and ov.es_letras = 1 and ov.tipo_letra = 1";
                        break;
                    }
                case '4': {
                        $condicion .= " and ov.es_letras = 1 and ov.tipo_letra = 2";
                        break;
                    }
                default: {
                        break;
                    }
            }
        }

        /* $condicion.=!empty($this->catprin) ? " and ct.idpadrec='$this->catprin' " : "";
          $condicion.=!empty($this->regcobr) ? " and ct.idcategoria='$this->regcobr' " : ""; */
        $condicion .= !empty($this->zona) ? " and c.zona='$this->zona' " : "";
        $condicion .= !empty($this->fecini) ? " and ov.fordenventa >= '$this->fecini'" : "";
        $condicion .= !empty($this->fecfin) ? " and ov.fordenventa <= '$this->fecfin'" : "";
        $condicion .= !empty($this->depa) ? " and t.iddepartamento='$this->depa'" : "";
        $condicion .= !empty($this->prov) ? " and p.idprovincia='$this->prov'" : "";
        $condicion .= !empty($this->dist) ? " and d.iddistrito='$this->dist'" : "";

        $data = $this->EjecutaConsulta("select distinct c.idcliente, z.nombrezona,
                c.razonsocial as cliente, c.ruc, c.telefono, c.celular,
                c.direccion, d.nombredistrito as dist, p.nombreprovincia as prov, t.nombredepartamento as depa,
                (sum(ov.importeov-ov.importedevolucion) / count(*)) as sumtotal,
                (sum(case when (ov.es_credito = 1 or ov.es_letras = 1) and datediff(ov.fechaCancelado,ov.fechavencimiento) > 0 then
                datediff(ov.fechaCancelado,ov.fechavencimiento) else 0 end)/
                sum(case when (ov.es_credito = 1 or ov.es_letras = 1) and datediff(ov.fechaCancelado,ov.fechavencimiento) > 0
                then 1 else 0 end)) as diasmora
                from " . $this->tablas . " where " . $condicion . " group by z.nombrezona, c.razonsocial,
                c.ruc, c.telefono, c.celular, c.direccion, d.nombredistrito, t.nombredepartamento, p.nombreprovincia
                order by z.nombrezona, " . ($this->ordenar == "m" ? "sumtotal desc" : ($this->ordenar == 'd' ? "c.direccion" : 'diasmora')) . ", c.razonsocial " . $limit);

        return $data;
    }

    public function paginadoCartera() {
        $condicion = $this->cond;
        $condicion .= !empty($this->idvend) ? " and ov.idvendedor='$this->idvend'" : "";

        if (!empty($this->condicion)) {
            switch ($this->condicion) {
                case '1': {
                        $condicion .= " and ov.es_contado = 1 and ov.es_credito = 0 and ov.es_letras = 0";
                        break;
                    }
                case '2': {
                        $condicion .= " and ov.es_credito = 1";
                        break;
                    }
                case '3': {
                        $condicion .= " and ov.es_letras = 1 and ov.tipo_letra = 1";
                        break;
                    }
                case '4': {
                        $condicion .= " and ov.es_letras = 1 and ov.tipo_letra = 2";
                        break;
                    }
                default: {
                        break;
                    }
            }
        }

        /* $condicion.=!empty($this->catprin) ? " and ct.idpadrec='$this->catprin' " : "";
          $condicion.=!empty($this->regcobr) ? " and ct.idcategoria='$this->regcobr' " : ""; */
        $condicion .= !empty($this->zona) ? " and c.zona='$this->zona' " : "";
        $condicion .= !empty($this->fecini) ? " and ov.fordenventa >= '$this->fecini'" : "";
        $condicion .= !empty($this->fecfin) ? " and ov.fordenventa <= '$this->fecfin'" : "";
        $condicion .= !empty($this->depa) ? " and t.iddepartamento='$this->depa'" : "";
        $condicion .= !empty($this->prov) ? " and p.idprovincia='$this->prov'" : "";
        $condicion .= !empty($this->dist) ? " and d.iddistrito='$this->dist'" : "";

        $data = $this->EjecutaConsulta("select count(*) as coun from (select distinct z.nombrezona,
            c.razonsocial as cliente, c.ruc, c.telefono, c.celular, c.direccion, d.nombredistrito as dist,
            p.nombreprovincia, t.nombredepartamento as depa from " . $this->tablas . " where " . $condicion . "
            ) as tabla");

        $var_config = parse_ini_file("config.ini", true);
        $tamanio = $var_config['Parametros']['Paginacion'];
        $paginas = ceil($data[0]['coun'] / $tamanio);
        return $paginas;
    }

    public function listarCartera($seleccion = "") {
        $condicion = $this->cond;
        $condicion .= !empty($this->idvend) ? " and uov.idvendedor='$this->idvend'" : "";

        if (!empty($this->condicion)) {
            switch ($this->condicion) {
                case '1': {
                        $condicion .= " and uov.es_contado = 1 and uov.es_credito = 0 and uov.es_letras = 0";
                        break;
                    }
                case '2': {
                        $condicion .= " and uov.es_credito = 1";
                        break;
                    }
                case '3': {
                        $condicion .= " and uov.es_letras = 1 and uov.tipo_letra = 1";
                        break;
                    }
                case '4': {
                        $condicion .= " and uov.es_letras = 1 and uov.tipo_letra = 2";
                        break;
                    }
                default: {
                        break;
                    }
            }
        }

        $condicion .= !empty($this->catprin) ? " and ct.idpadrec='$this->catprin' " : "";
        $condicion .= !empty($this->regcobr) ? " and ct.idcategoria='$this->regcobr' " : "";
        $condicion .= !empty($this->zona) ? " and c.zona='$this->zona' " : "";
        $condicion .= !empty($this->fecini) ? " and ov.fordenventa >= '$this->fecini'" : "";
        $condicion .= !empty($this->fecfin) ? " and ov.fordenventa <= '$this->fecfin'" : "";
        $condicion .= !empty($this->depa) ? " and t.iddepartamento='$this->depa' " : "";
        $condicion .= !empty($this->prov) ? " and p.idprovincia='$this->prov' " : "";
        $condicion .= !empty($this->dist) ? " and d.iddistrito='$this->dist' " : "";
        $condicion .= !empty($this->aprobados) ? " and " . $this->aprobados . " " : "";
        $condicion .= !empty($seleccion) ? $seleccion : "";
        $data = $this->EjecutaConsulta("select distinct c.idcliente, z.idzona, z.nombrezona,
                c.razonsocial as cliente, c.ruc, c.telefono, c.celular, c.email,
                c.direccion, d.nombredistrito as dist, p.nombreprovincia as prov, t.nombredepartamento as depa,
                uov.fordenventa, uov.codigov, uov.es_contado, uov.es_credito, uov.es_letras
                from " . $this->tablas . " where " . $condicion . " 
                order by z.nombrezona, " . ($this->ordenar == 'd' ? "c.direccion," : ($this->ordenar == 'u' ? 'uov.codigov desc,' : '')) . " c.razonsocial");

        return $data;
    }

    public function listarCarteraNuevoFormato($seleccion = "", $completo = '') {
        $condicion = $this->cond;
        $condicion .= !empty($this->idvend) ? " and uov.idvendedor='$this->idvend'" : "";

        if (!empty($this->condicion)) {
            switch ($this->condicion) {
                case '1': {
                        $condicion .= " and uov.es_contado = 1 and uov.es_credito = 0 and uov.es_letras = 0";
                        break;
                    }
                case '2': {
                        $condicion .= " and uov.es_credito = 1";
                        break;
                    }
                case '3': {
                        $condicion .= " and uov.es_letras = 1 and uov.tipo_letra = 1";
                        break;
                    }
                case '4': {
                        $condicion .= " and uov.es_letras = 1 and uov.tipo_letra = 2";
                        break;
                    }
                default: {
                        break;
                    }
            }
        }

        $condicion .= !empty($this->catprin) ? " and ct.idpadrec='$this->catprin' " : "";
        $condicion .= !empty($this->regcobr) ? " and ct.idcategoria='$this->regcobr' " : "";
        $condicion .= !empty($this->zona) ? " and c.zona='$this->zona' " : "";
        $condicion .= !empty($this->fecini) ? " and uov.fordenventa >= '$this->fecini'" : "";
        $condicion .= !empty($this->fecfin) ? " and uov.fordenventa <= '$this->fecfin'" : "";
        $condicion .= !empty($this->depa) ? " and t.iddepartamento='$this->depa' " : "";
        $condicion .= !empty($this->prov) ? " and p.idprovincia='$this->prov' " : "";
        $condicion .= !empty($this->dist) ? " and d.iddistrito='$this->dist' " : "";
        $condicion .= !empty($this->aprobados) ? " and " . $this->aprobados . " " : "";
        $condicion .= !empty($seleccion) ? $seleccion : "";
        $inner_join = '';
        $select = '';
        if ($completo == 1) {
            $select .= ', rvc.lineacreditototal, rvc.deudatotal, rvc.lineacreditodisponible';
            $inner_join .= ' inner join wc_resumenevaluacioncrediticia rvc on rvc.idcliente = c.idcliente ';
        }
        $data = $this->EjecutaConsulta("select distinct c.idcliente, z.idzona, z.nombrezona,
                c.razonsocial as cliente, c.ruc, c.telefono, c.celular, c.email,
                c.direccion, d.nombredistrito as dist, p.nombreprovincia as prov, t.nombredepartamento as depa,
                uov.fordenventa, uov.codigov" . $select . "
                from wc_cliente c
                        inner join wc_zona z on c.zona = z.idzona
                        inner join wc_categoria ct on ct.idcategoria = z.idcategoria
                        inner join wc_distrito d on c.iddistrito = d.iddistrito
                        inner join wc_provincia p on p.idprovincia = d.idprovincia
                        inner join wc_departamento t on p.iddepartamento = t.iddepartamento
                        inner join wc_ordenventa uov on uov.idordenventa = c.idultimaorden
                        " . $inner_join . "
                where " . $condicion . " 
                order by z.nombrezona, " . ($this->ordenar == 'd' ? "c.direccion," : ($this->ordenar == 'u' ? 'uov.codigov desc,' : '')) . " c.razonsocial");

        return $data;
    }

    public function ultimaCondicionCompraxCliente($idcliente) {
        return $this->EjecutaConsulta("select *
                                            from wc_clienteobservaciones
                                    where idcliente = '" . $idcliente . "' and estado = 1
                                    order by idclienteobservaciones desc 
                                    limit 1;");
    }

    public function getArrayCalificacion() {
        $data = $this->EjecutaConsulta("SELECT * FROM wc_calificacion where estado = 1;");
        $nuevoArray = array();
        for ($i = 0; $i < count($data); $i++) {
            $nuevoArray[$data[$i]['idcalificacion']] = $data[$i]['nombre'];
        }
        return $nuevoArray;
    }

    public function getArrayCondicionCompra() {
        $data = $this->EjecutaConsulta("SELECT * FROM wc_condicioncompra WHERE estado = 1;");
        $nuevoArray = array();
        for ($i = 0; $i < count($data); $i++) {
            $nuevoArray[$data[$i]['idcondicioncompra']] = $data[$i]['nombre'];
        }
        return $nuevoArray;
    }
    
    public function listarCarteraNuevoFormato2($deuda = '') {
        $condicion = $this->cond;
        $condicion.=!empty($this->idvend) ? " and uov.idvendedor='$this->idvend'" : "";

        if (!empty($this->condicion)) {
            switch($this->condicion) {
                case '1': {
                    $condicion.=" and uov.es_contado = 1 and uov.es_credito = 0 and uov.es_letras = 0";
                    break;
                }
                case '2': {
                    $condicion.=" and uov.es_credito = 1";
                    break;
                }
                case '3': {
                    $condicion.=" and uov.es_letras = 1 and uov.tipo_letra = 1";
                    break;
                }
                case '4': {
                    $condicion.=" and uov.es_letras = 1 and uov.tipo_letra = 2";
                    break;
                }
                default: {
                    break;
                }
            }
        }

        $condicion.=!empty($this->catprin) ? " and ct.idpadrec='$this->catprin' " : "";
        $condicion.=!empty($this->regcobr) ? " and ct.idcategoria='$this->regcobr' " : "";
        $condicion.=!empty($this->zona) ? " and c.zona='$this->zona' " : "";
        $condicion.=!empty($this->fecini) ? " and uov.fordenventa >= '$this->fecini'" : "";
        $condicion.=!empty($this->fecfin) ? " and uov.fordenventa <= '$this->fecfin'" : "";
        $condicion.=!empty($this->depa) ? " and t.iddepartamento='$this->depa' " : "";
        $condicion.=!empty($this->prov) ? " and p.idprovincia='$this->prov' " : "";
        $condicion.=!empty($this->dist) ? " and d.iddistrito='$this->dist' " : "";
        $condicion.=!empty($this->aprobados) ? " and " . $this->aprobados . " " : "";
        if (!empty($deuda)) {
            if ($deuda == 'D') {
                $condicion.=" and rvc.deudatotal >= 1";
            }
            if ($deuda == 'N') {
                $condicion.=" and rvc.deudatotal < 1";
            }
        }
        $data = $this->EjecutaConsulta("select distinct c.idcliente, z.idzona, z.nombrezona,
                c.razonsocial as cliente, c.ruc, c.telefono, c.celular, c.email,
                c.direccion, d.nombredistrito as dist, p.nombreprovincia as prov, t.nombredepartamento as depa,
                uov.idvendedor, uov.es_contado, uov.es_credito, uov.es_letras, rvc.lineacreditototal, rvc.deudatotal, rvc.lineacreditodisponible, lcredito.lineacredito
                from wc_cliente c
                        inner join wc_zona z on c.zona = z.idzona
                        inner join wc_categoria ct on ct.idcategoria = z.idcategoria
                        inner join wc_distrito d on c.iddistrito = d.iddistrito
                        inner join wc_provincia p on p.idprovincia = d.idprovincia
                        inner join wc_departamento t on p.iddepartamento = t.iddepartamento
                        inner join wc_ordenventa uov on uov.idordenventa = c.idultimaorden
                        left join wc_resumenevaluacioncrediticia rvc on rvc.idcliente = c.idcliente
                        left join (select idcliente, lineacredito from wc_lineacredito order by idlineacredito desc) as lcredito on lcredito.idcliente = c.idcliente
                where " . $condicion . " 
                order by z.nombrezona, ".($this->ordenar == 'd' ? "c.direccion," : ($this->ordenar == 'u' ? 'uov.codigov desc,' : ''))." c.razonsocial");

        return $data;
    }
    
    public function listarCarteraNuevoFormato3($deuda = '') {
        $condicion = $this->cond;
        $condicion.=!empty($this->idvend) ? " and ov.idvendedor='$this->idvend'" : "";

        if (!empty($this->condicion)) {
            switch($this->condicion) {
                case '1': {
                    $condicion.=" and uov.es_contado = 1 and uov.es_credito = 0 and uov.es_letras = 0";
                    break;
                }
                case '2': {
                    $condicion.=" and uov.es_credito = 1";
                    break;
                }
                case '3': {
                    $condicion.=" and uov.es_letras = 1 and uov.tipo_letra = 1";
                    break;
                }
                case '4': {
                    $condicion.=" and uov.es_letras = 1 and uov.tipo_letra = 2";
                    break;
                }
                default: {
                    break;
                }
            }
        }

        $condicion.=!empty($this->catprin) ? " and ct.idpadrec='$this->catprin' " : "";
        $condicion.=!empty($this->regcobr) ? " and ct.idcategoria='$this->regcobr' " : "";
        $condicion.=!empty($this->zona) ? " and c.zona='$this->zona' " : "";
        $condicion.=!empty($this->fecini) ? " and ov.fordenventa >= '$this->fecini'" : "";
        $condicion.=!empty($this->fecfin) ? " and ov.fordenventa <= '$this->fecfin'" : "";
        $condicion.=!empty($this->depa) ? " and t.iddepartamento='$this->depa' " : "";
        $condicion.=!empty($this->prov) ? " and p.idprovincia='$this->prov' " : "";
        $condicion.=!empty($this->dist) ? " and d.iddistrito='$this->dist' " : "";
        $condicion.=!empty($this->aprobados) ? " and " . $this->aprobados . " " : "";
        if (!empty($deuda)) {
            if ($deuda == 'D') {
                $condicion.=" and rvc.deudatotal >= 1";
            }
            if ($deuda == 'N') {
                $condicion.=" and rvc.deudatotal < 1";
            }
        }
        $condicion.= " and ov.estado=1 and ov.vbcreditos=1 and ov.desaprobado=''";
        $data = $this->EjecutaConsulta("select distinct c.idcliente, z.idzona, z.nombrezona,
                c.razonsocial as cliente, c.ruc, c.telefono, c.celular, c.email,
                c.direccion, d.nombredistrito as dist, p.nombreprovincia as prov, t.nombredepartamento as depa,
                uov.idvendedor, uov.es_contado, uov.es_credito, uov.es_letras, rvc.lineacreditototal, rvc.deudatotal, rvc.lineacreditodisponible, lcredito.lineacredito
                from wc_ordenventa ov
                        inner join wc_cliente c on c.idcliente = ov.idcliente
                        inner join wc_zona z on c.zona = z.idzona
                        inner join wc_categoria ct on ct.idcategoria = z.idcategoria
                        inner join wc_distrito d on c.iddistrito = d.iddistrito
                        inner join wc_provincia p on p.idprovincia = d.idprovincia
                        inner join wc_departamento t on p.iddepartamento = t.iddepartamento
                        inner join wc_ordenventa uov on uov.idordenventa = c.idultimaorden
                        inner join wc_resumenevaluacioncrediticia rvc on rvc.idcliente = c.idcliente
                        left join (select idcliente, lineacredito from wc_lineacredito order by idlineacredito desc) as lcredito on lcredito.idcliente = c.idcliente
                where " . $condicion . " 
                group by c.idcliente
                order by z.nombrezona, ".($this->ordenar == 'd' ? "c.direccion," : ($this->ordenar == 'u' ? 'uov.codigov desc,' : ''))." c.razonsocial");

        return $data;
    }
    public function listarRankingClientexVendedor($deuda = '') {
        $condicion = $this->cond;
        $condicion.=!empty($this->idvend) ? " and ov.idvendedor='$this->idvend'" : "";

        if (!empty($this->condicion)) {
            switch($this->condicion) {
                case '1': {
                    $condicion.=" and uov.es_contado = 1 and uov.es_credito = 0 and uov.es_letras = 0";
                    break;
                }
                case '2': {
                    $condicion.=" and uov.es_credito = 1";
                    break;
                }
                case '3': {
                    $condicion.=" and uov.es_letras = 1 and uov.tipo_letra = 1";
                    break;
                }
                case '4': {
                    $condicion.=" and uov.es_letras = 1 and uov.tipo_letra = 2";
                    break;
                }
                default: {
                    break;
                }
            }
        }

        $condicion.=!empty($this->catprin) ? " and ct.idpadrec='$this->catprin' " : "";
        $condicion.=!empty($this->regcobr) ? " and ct.idcategoria='$this->regcobr' " : "";
        $condicion.=!empty($this->zona) ? " and c.zona='$this->zona' " : "";
        $condicion.=!empty($this->fecini) ? " and ov.fordenventa >= '$this->fecini'" : "";
        $condicion.=!empty($this->fecfin) ? " and ov.fordenventa <= '$this->fecfin'" : "";
        $condicion.=!empty($this->depa) ? " and t.iddepartamento='$this->depa' " : "";
        $condicion.=!empty($this->prov) ? " and p.idprovincia='$this->prov' " : "";
        $condicion.=!empty($this->dist) ? " and d.iddistrito='$this->dist' " : "";
        $condicion.=!empty($this->aprobados) ? " and " . $this->aprobados . " " : "";
        if (!empty($deuda)) {
            if ($deuda == 'D') {
                $condicion.=" and rvc.deudatotal >= 1";
            }
            if ($deuda == 'N') {
                $condicion.=" and rvc.deudatotal < 1";
            }
        }
        $cosultaFechaOpc='';
        $cosultaFechaOpc.=!empty($this->fecini) ? " and ove.fordenventa >= '$this->fecini'" : "";
        $cosultaFechaOpc.=!empty($this->fecfin) ? " and ove.fordenventa <= '$this->fecfin'" : "";
        $condicion.= " and ov.estado=1 and ov.vbcreditos=1 and ov.desaprobado=''";
        $data = $this->EjecutaConsulta("select distinct c.idcliente,
                (select sum(
                        case 
                            when ove.IdMoneda=1 then ove.importeaprobado-ove.importedevolucion
                            when ove.IdMoneda=2 then (ove.importeaprobado-ove.importedevolucion)*tipc.compra
                        end)
                    from wc_ordenventa as ove inner join (SELECT * FROM wc_tipocambio GROUP BY fechatc ORDER BY idtipocambio DESC) as tipc on tipc.fechatc=ove.fordenventa
                    where ove.idvendedor='$this->idvend'". $cosultaFechaOpc." and ove.estado=1 and ove.vbcreditos=1 and ove.desaprobado='' and ove.idcliente=c.idcliente ) as importeTotalResuelto, 
                z.idzona, 
                z.nombrezona,
                c.razonsocial as cliente, 
                c.ruc, c.telefono, 
                c.celular, c.email,
                c.direccion, d.nombredistrito as dist, 
                p.nombreprovincia as prov, 
                t.nombredepartamento as depa,
                uov.idvendedor, uov.es_contado, 
                uov.es_credito, uov.es_letras, 
                rvc.lineacreditototal, 
                rvc.deudatotal, rvc.lineacreditodisponible, 
                lcredito.lineacredito
        from wc_ordenventa ov
                inner join wc_cliente c on c.idcliente = ov.idcliente
                inner join wc_zona z on c.zona = z.idzona
                inner join wc_categoria ct on ct.idcategoria = z.idcategoria
                inner join wc_distrito d on c.iddistrito = d.iddistrito
                inner join wc_provincia p on p.idprovincia = d.idprovincia
                inner join wc_departamento t on p.iddepartamento = t.iddepartamento
                inner join wc_ordenventa uov on uov.idordenventa = c.idultimaorden
                inner join wc_resumenevaluacioncrediticia rvc on rvc.idcliente = c.idcliente
                left join (select idcliente, lineacredito from wc_lineacredito order by idlineacredito desc) as lcredito on lcredito.idcliente = c.idcliente
                where " . $condicion . " 
                group by c.idcliente
                order by importeTotalResuelto desc");

        return $data;
    }

}

?>