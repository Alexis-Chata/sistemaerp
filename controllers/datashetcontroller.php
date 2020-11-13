<?php
//NO extraer mas las clases, se admeostrado que mientras mas se extraiga que el comportamiento del php es mas lento
require_once 'C:/wamp/www/sistema_erpx/libs/Config_.php';
$configjob=new configjob();
$configjob->begin1();
date_default_timezone_set('America/Lima');
Class Cliente extends configjob {
    public function ventaMayor($idcliente,$get_tcambio){
        //start como nacio la venta
            //$sql="SELECT ocCab.idordenventa,ocCab.importeordencobro,CASE ovCab.IdMoneda WHEN 2 THEN  ocCab.importeordencobro*".$get_tcambio." WHEN 1 THEN  ocCab.importeordencobro END AS total
            //FROM wc_ordenventa ovCab,wc_ordencobro ocCab
            //WHERE ovCab.idcliente=".$idcliente."
            //AND ovCab.idordenventa=ocCab.idordenventa
            //AND ovCab.vbcreditos=1
            //AND ovCab.vbventas=1
            //AND ovCab.vbcobranzas=1
            //AND ovCab.estado=1
            //ORDER BY ocCab.idordenventa,ocCab.idordencobro ASC";
                //            $array_ventaMayor = $this->scriptArrayCompleto($sql);
                //        $idordenventa=-1;
                //        for ($i = 0; $i < count($array_ventaMayor); $i++) {
                //            if($idordenventa!=$array_ventaMayor[$i]['idordenventa']){
                //            $cadena[]=$array_ventaMayor[$i]['total'];
                //            }
                //            $idordenventa=$array_ventaMayor[$i]['idordenventa'];
                //        }
                //        $totalmayor=max($cadena);
                //
        //  end como nacio la venta

        //start como esta actualmente esta la venta -- angel lo indico en el modulo vista global
            $sql="SELECT ovCab.idordenventa,CASE ovCab.IdMoneda WHEN 2 THEN  SUM(ogCab.importegasto)*".$get_tcambio." WHEN 1 THEN  SUM(ogCab.importegasto) END AS total
            FROM wc_ordenventa ovCab,wc_ordengasto ogCab
            WHERE ovCab.idcliente=".$idcliente."
            AND ovCab.idordenventa=ogCab.idordenventa
            AND ovCab.vbcreditos=1
            AND ovCab.vbventas=1
            AND ovCab.vbcobranzas=1
            AND ovCab.estado=1
            AND ogCab.estado=1
            GROUP BY ovCab.idordenventa ORDER BY total DESC;";
            $resultado = $this->filtro($sql);
            $lisAsos2 = $this->lisAsos2($resultado);
        //end como termino la venta
         return $array_ventaMayor[0]['total'];
        }
    public function calcularCreditoDisponible($idcliente,$tempDeudaTotal,$get_tcambio,$accion=''){
    //        $deudaactual_es_creditodisponible="0";
            $sqlx="select * from wc_clientelineacredito where idcliente='".$idcliente."' and estado=1 and anulado=0;";
            $resultado = $this->filtro($sqlx);
            $data = $this->lisAsos2($resultado);
            //start obtiene la deuda real
            if($accion=="calcular"){
                    $lista_deuda_contado=$this->listaDeudaTotalCliente($idcliente,"contado");
                    $lista_deuda_credito=$this-> listaDeudaTotalCliente($idcliente,"credito");
                    $lista_deuda_letrabanco=$this-> listaDeudaTotalCliente($idcliente,"letrabanco");
                    $lista_deuda_letracartera=$this-> listaDeudaTotalCliente($idcliente,"letracartera");
                    $lista_deuda_letraprotestada=$this-> listaDeudaTotalCliente($idcliente,"letraprotestada");

                    foreach ($lista_deuda_contado as $value) {
                       if($value['idmoneda']==1){
                       $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                       }
                       if($value['idmoneda']==2){
                       $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                       }
                    }
                    foreach ($lista_deuda_credito as $value) {
                       if($value['idmoneda']==1){
                        $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                       }
                       if($value['idmoneda']==2){
                        $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));

                       }
                   }
                    foreach ($lista_deuda_letrabanco as $value) {
                       if($value['idmoneda']==1){
                       $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                       }
                       if($value['idmoneda']==2){
                       $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                       }
                   }
                    foreach ($lista_deuda_letracartera as $value) {
                       if($value['idmoneda']==1){
                       $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                       }
                       if($value['idmoneda']==2){
                       $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                      }
                   }
                    foreach ($lista_deuda_letraprotestada as $value) {
                       if($value['idmoneda']==1){
                       $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                       }
                       if($value['idmoneda']==2){
                       $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
                      }
                   }
                   $tempDeudaTotal=$tempDeudaSoles+($tempDeudaDolares*$get_tcambio);
            }
            //end obtiene la deuda real
    //start obtiene la linea de credito actual
            //si tiene auditoria
            //  linea de credito es igual a linea de credito
            //fin si
            //
            //si no tiene auditoria
            // si  deuda>0
            //  linea de credito es igual a deuda
            // fin si
            //
            // si deuda <=0
            //  linea de credito es igual ultima venta mayor(a     la linea de credito)
            // fin si
            //fin si
            if(count($data)>0){
                if($data[0]['movimiento']==1){ //aumentaron linea de credito
                   $lineacreditoactual=(($data[0]['lcreditosoles']/$get_tcambio)+$data[0]['lcreditodolares'])+$data[0]['cantidad'];
                }
                if($data[0]['movimiento']==2){ //disminuyeron linea de credito
                   $lineacreditoactual=(($data[0]['lcreditosoles']/$get_tcambio)+$data[0]['lcreditodolares'])-$data[0]['cantidad'];

                }
                $lineacreditoactual=$lineacreditoactual*$get_tcambio;
            }
            if(count($data)==0){
                if($tempDeudaTotal>0){
                    $lineacreditoactual=$tempDeudaTotal;
                }
                if($tempDeudaTotal<=0){
                    $lineacreditoactual=$this->ventaMayor($idcliente,$get_tcambio);
                }
            }
           //end obtiene la linea de credito actual
            $arrayx[]=array("lineacreditoactual"=>$lineacreditoactual,'deudatotal'=>$tempDeudaTotal);
            return $arrayx;
        }
    public function listaCalificaciones(){
            $sql="select idcalificacion,nombre,estado from wc_calificacion where estado=1;";
            $resultado = $this->filtro($sql);
            $lisAsos2 = $this->lisAsos2($resultado);
            return $lisAsos2;
        }
    public function  listaClientesZonaparaCobranza($idzona,$idpadrec,$idcategoria,$idcliente,$orden1) {
    $sql="SELECT distinct(wc_cliente.`idcliente`),
           wc_cliente.`iddistrito`,
          (case when wc_cliente.razonsocial is null then concat(wc_cliente.nombrecli, ' ', wc_cliente.apellido1, ' ', wc_cliente.apellido2) else wc_cliente.razonsocial end) as razonsocial,
           wc_cliente.`direccion`,
           wc_categoria.`idcategoria`,
           wc_categoria.`idpadrec`,
           wc_categoria.`codigoc`,
           wc_categoria.`nombrec`,
           wc_zona.`idzona`,
           wc_zona.`nombrezona`,
           wc_cliente.fechacreacion
           FROM   `wc_cliente`
           INNER JOIN `wc_clientezona` wc_clientezona ON wc_cliente.`idcliente` = wc_clientezona.`idcliente`
           INNER JOIN `wc_zona` wc_zona  ON wc_clientezona.`idzona` = wc_zona.`idzona`
           INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
    WHERE
           wc_cliente.estado=1";
           if($idzona!=""){
             $sql.=" AND wc_zona.`idzona` = '".$idzona."'";
            }
            if($idpadrec!=""){
             $sql.=" AND wc_categoria.`idpadrec` = '".$idpadrec."'";
            }
            if($idcategoria!=""){
             $sql.=" AND wc_categoria.`idcategoria` = '".$idcategoria."'";
            }
            if($idcliente!=""){
             $sql.=" AND wc_cliente.idcliente='".$idcliente."'";
            }
            if($orden1==""){
            $sql.=" ORDER BY wc_zona.`nombrezona`,razonsocial ASC";
            }
            if($orden1=="antiguos"){
            $sql.=" ORDER  BY wc_zona.`nombrezona`,wc_cliente.fechacreacion ASC";
            }
            if($orden1=="recientes"){
            $sql.=" ORDER  BY wc_zona.`nombrezona`,wc_cliente.fechacreacion desc";
            }
            $resultado = $this->filtro($sql);
            $lisAsos2 = $this->lisAsos2($resultado);
            return $lisAsos2;
        }
    public function  listaDeudaTotalCliente($idcliente,$tipodeuda) {
            $sql="select wc_actor.`apellidomaterno`,wc_moneda.`idmoneda`,wc_moneda.`nombre` as nommoneda,
    wc_moneda.`simbolo`,categoriazona.`nombrec`,wc_categoria.`idpadrec`,sum(wc_detalleordencobro.`saldodoc`) as saldodoc,
    sum(wc_detalleordencobro.`importedoc`) as importedoc,sum(wc_detalleordencobro.`montoprotesto`) as montoprotesto
    from `wc_ordenventa` wc_ordenventa
    inner join `wc_moneda` wc_moneda on wc_ordenventa.idmoneda=wc_moneda.idmoneda
    inner join `wc_clientezona` wc_clientezona on wc_ordenventa.`idclientezona` = wc_clientezona.`idclientezona`
    inner join `wc_actor` wc_actor on wc_ordenventa.`idvendedor` = wc_actor.`idactor`
    inner join `wc_cliente` wc_cliente on wc_clientezona.`idcliente` = wc_cliente.`idcliente`
    inner join `wc_zona` wc_zona on wc_clientezona.`idzona` = wc_zona.`idzona`
    inner join `wc_categoria` wc_categoria on wc_zona.`idcategoria` = wc_categoria.`idcategoria`
    inner join `wc_categoria` categoriazona on categoriazona.`idcategoria` = wc_categoria.`idpadrec`
    inner join `wc_ordencobro` wc_ordencobro on wc_ordencobro.`idordenventa`=wc_ordenventa.`idordenventa`
    inner join `wc_detalleordencobro` wc_detalleordencobro on wc_detalleordencobro.`idordencobro`=wc_ordencobro.`idordencobro`
    where wc_detalleordencobro.`estado`=1
    and wc_ordenventa.`esguiado`=1
    and wc_ordenventa.`estado`=1
    and wc_ordencobro.`estado`=1
    and wc_detalleordencobro.`situacion`!='reprogramado'
    and wc_detalleordencobro.`situacion`!='anulado'
    and wc_detalleordencobro.`situacion`!='extornado'
    and wc_detalleordencobro.`situacion`!='refinanciado'
    and wc_detalleordencobro.`situacion`!='protestado'
    and wc_detalleordencobro.`situacion`!='renovado'
    and wc_cliente.`idcliente`='".$idcliente."'";
            if($tipodeuda=="contado"){
            $sql.=" and wc_detalleordencobro.`formacobro`='1'
                    group by wc_categoria.`idpadrec`, wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
            }
            if($tipodeuda=="credito"){
            $sql.=" and wc_detalleordencobro.`formacobro`='2'
                    and wc_detalleordencobro.referencia=''
                    group by wc_categoria.`idpadrec`,wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
            }
            if($tipodeuda=="letrabanco"){
            $sql.=" and wc_detalleordencobro.`formacobro`='3'
                    and wc_ordencobro.`tipoletra`=1
                    group by wc_categoria.`idpadrec`, wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
            }
            if($tipodeuda=="letracartera"){
            $sql.=" and wc_detalleordencobro.`formacobro`='3'
                    and wc_ordencobro.`tipoletra`=2
                    group by wc_categoria.`idpadrec`, wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
            }
            if($tipodeuda=="letraprotestada"){
            $sql.=" and wc_detalleordencobro.`formacobro`='2'
                    and (substring( wc_detalleordencobro.referencia,9,1)='p' or substring(wc_detalleordencobro.referencia,11,1)='p')
                    and wc_zona.`nombrezona` not like '%incobrab%'
                    group by wc_categoria.`idpadrec`,wc_moneda.`idmoneda` order by wc_categoria.`idpadrec` asc;";
            }
          $resultado = $this->filtro($sql);
          $lisAsos2 = $this->lisAsos2($resultado);
          return $lisAsos2;

        }
    public function ultimoPagoCliente($idcliente){
            $sql="select ing.fcobro,ing.idOrdenVenta,ov.codigov,ing.montoasignado,ov.idmoneda
                  from wc_ingresos ing,wc_ordenventa ov
                  where ing.idcliente='".$idcliente."' and ing.montoasignado!=0  and ing.estado=1 and ing.idordenventa=ov.idordenventa order by ing.idingresos desc limit 0,1";
         $resultado = $this->filtro($sql);
          $lisAsos2 = $this->lisAsos2($resultado);
          return $lisAsos2;
        }
    public function listaCalificacionActual($idcliente){
            $sql="select cal.idcalificacion,cal.nombre as 'calificacion'
                from wc_clientelineacredito clicre,wc_calificacion cal
                where clicre.idcliente='".$idcliente."'
                and clicre.estado=1
                and clicre.anulado=0
                and clicre.idcalificacion=cal.idcalificacion
                order by clicre.idclientelineacredito desc limit 0,1;";
            $resultado = $this->filtro($sql);
            $lisAsos2 = $this->lisAsos2($resultado);
          return $lisAsos2;
        }
    public function listaCondicionCompraActual($idcliente){
            $sql="select clicre.idcondicioncompra,codCom.nombre as 'condicioncompra'
                from wc_clientelineacredito clicre,wc_condicioncompra codCom
                where clicre.idcliente='".$idcliente."'
                and clicre.estado=1
                and clicre.anulado=0
                and clicre.idcondicioncompra=codCom.idcondicioncompra
                order by clicre.idclientelineacredito desc limit 0,1;";
          $resultado = $this->filtro($sql);
          $lisAsos2 = $this->lisAsos2($resultado);
          return $lisAsos2;
        }
    public function listaCondicionCompra(){
            $sql="select idcondicioncompra,nombre from wc_condicioncompra WHERE estado=1 order by idcondicioncompra asc;";
            $resultado = $this->filtro($sql);
            $lisAsos2 = $this->lisAsos2($resultado);
            return $lisAsos2;
        }
    public function ultimaCompraCliente($idcliente){
            $sql0="select idordenventa,codigov,fordenventa,idmoneda from wc_ordenventa where idcliente='".$idcliente."' and estado=1 and esguiado=1 and vbcreditos=1 and faprobado!='' order by idordenventa desc limit 0,1;";
            $resultado0= $this->filtro($sql0);
            $scriptArrayCompleto0 = $this->lisAsos2($resultado0);
            $get_idordenventa=$scriptArrayCompleto0[0]['idordenventa'];
            $get_codigov=$scriptArrayCompleto0[0]['codigov'];
            $get_fordenventa=$scriptArrayCompleto0[0]['fordenventa'];
            $get_idmoneda=$scriptArrayCompleto0[0]['idmoneda'];

            $sql1="select (select sum(importegasto) from wc_ordengasto where idordenventa='".$get_idordenventa."' and estado=1 and idtipogasto in(7,9)) as 'importeordenventa',(select sum(importegasto) from wc_ordengasto where idordenventa='".$get_idordenventa."' and estado=1 and idtipogasto in(6)) as 'percepcion',(select sum(importegasto) from wc_ordengasto where idordenventa='".$get_idordenventa."' and estado=1 and idtipogasto not in(6,7,9)) as 'gastosadicionales';";
            $resultado1= $this->filtro($sql1);
            $scriptArrayCompleto1 = $this->lisAsos2($resultado1);

            foreach($scriptArrayCompleto1 as $val){
                $array[]=array("idordenventa"=>$get_idordenventa,
                                "codigov"=>$get_codigov,
                                "fordenventa"=>$get_fordenventa,
                                "importeordenventa"=>$val['importeordenventa'],
                                "percepcion"=>$val['percepcion'],
                                "gastosadicionales"=>$val['gastosadicionales'],
                                "idmoneda"=>$get_idmoneda);
            }
            return $array;
        }
    public function  listaClientesZonaparaCobranzaVendedor($idvendedor) {
            $in='';
            $sql1="select distinct(ovCab.idcliente)
    from wc_ordenventa ovCab,wc_cliente cliente
    where ovCab.idvendedor='".$idvendedor."'
    and ovCab.idcliente=cliente.idcliente
    and ovCab.estado=1
    and ovCab.esguiado=1
    and ovCab.vbcreditos=1
    and ovCab.faprobado!=''
    order by cliente.idcliente asc;";
            $resultado1 = $this->filtro($sql1);
            $scriptArrayCompleto1 = $this->lisAsos2($resultado1);


            foreach ($scriptArrayCompleto1 as $val) {
                $sql2="select idvendedor from wc_ordenventa where idcliente='".$val['idcliente']."' and estado=1 and esguiado=1 and vbcreditos=1 and faprobado!='' order by idordenventa desc limit 0,1;";
                $resultado2 = $this->filtro($sql2);
                $scriptArrayCompleto2 = $this->lisAsos2($resultado2);
                    if($scriptArrayCompleto2[0]['idvendedor']==$idvendedor){
                        $in.=$val['idcliente'].',';
                    }
            }
            $in= substr($in, 0, -1);

    $sqlx="SELECT distinct(wc_cliente.`idcliente`),
           wc_cliente.`iddistrito`,
          (case when wc_cliente.razonsocial is null then concat(wc_cliente.nombrecli, ' ', wc_cliente.apellido1, ' ', wc_cliente.apellido2) else wc_cliente.razonsocial end) as razonsocial,
           wc_cliente.`direccion`,
           wc_categoria.`idcategoria`,
           wc_categoria.`idpadrec`,
           wc_categoria.`codigoc`,
           wc_categoria.`nombrec`,
           wc_zona.`idzona`,
           wc_zona.`nombrezona`,
           wc_cliente.fechacreacion
           FROM   `wc_cliente`
           INNER JOIN `wc_clientezona` wc_clientezona ON wc_cliente.`idcliente` = wc_clientezona.`idcliente`
           INNER JOIN `wc_zona` wc_zona  ON wc_clientezona.`idzona` = wc_zona.`idzona`
           INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
           WHERE
           wc_cliente.estado=1 and wc_cliente.idcliente in (".$in.")";

           $resultado3 = $this->filtro($sqlx);
           $scriptArrayCompleto3 = $this->lisAsos2($resultado3);
           return $scriptArrayCompleto3;
        }
    public function  listadoclientes_evaluacioncrediticia($in) {
        $sqlx="SELECT distinct(wc_cliente.`idcliente`),
           wc_cliente.`iddistrito`,
          (case when wc_cliente.razonsocial is null then concat(wc_cliente.nombrecli, ' ', wc_cliente.apellido1, ' ', wc_cliente.apellido2) else wc_cliente.razonsocial end) as razonsocial,
           wc_cliente.`direccion`,
           wc_categoria.`idcategoria`,
           wc_categoria.`idpadrec`,
           wc_categoria.`codigoc`,
           wc_categoria.`nombrec`,
           wc_zona.`idzona`,
           wc_zona.`nombrezona`,
           wc_cliente.fechacreacion
           FROM   `wc_cliente`
           INNER JOIN `wc_clientezona` wc_clientezona ON wc_cliente.`idcliente` = wc_clientezona.`idcliente`
           INNER JOIN `wc_zona` wc_zona  ON wc_clientezona.`idzona` = wc_zona.`idzona`
           INNER JOIN `wc_categoria` wc_categoria ON wc_zona.`idcategoria` = wc_categoria.`idcategoria`
           WHERE
           wc_cliente.estado=1 and wc_cliente.idcliente in (".$in.")";
           $resultadox = $this->filtro($sqlx);
           $scriptArrayCompleto1 = $this->lisAsos2($resultadox);
           return $scriptArrayCompleto1;
    }
}
class Creditos extends configjob {
    public function resumenevaluacioncrediticia($idcliente){
            $sql="select * from wc_resumenevaluacioncrediticia where idcliente=".$idcliente." and estado=1;";
            $resultado = $this->filtro($sql);
            $scriptArrayCompleto = $this->lisAsos2($resultado);
            return $scriptArrayCompleto;
        }
    public function historialcredito($idcliente,$condicion){
            $sql="SELECT wc_calificacion.nombre as 'calificacion',wc_condicioncompra.nombre as 'condicioncompra',wc_clientelineacredito.*
    FROM wc_clientelineacredito
    LEFT JOIN wc_condicioncompra ON wc_clientelineacredito.idcondicioncompra = wc_condicioncompra.idcondicioncompra
    inner join wc_calificacion on wc_clientelineacredito.idcalificacion=wc_calificacion.idcalificacion
    where wc_clientelineacredito.idcliente='".$idcliente."' and wc_clientelineacredito.anulado=0 order by wc_clientelineacredito.idclientelineacredito desc";
             if($condicion=='filaultima'){
                  $sql.=" limit 0,1";
             }
            $resultado = $this->filtro($sql);
            $scriptArrayCompleto = $this->lisAsos2($resultado);
            return $scriptArrayCompleto;
        }
    public function clienteAuditado($idcliente){
            $sql="select count(*) as 'auditado'
                from wc_clientelineacredito
                where idcliente='".$idcliente."'
                and anulado=0
                and estado=1
                order by idclientelineacredito desc limit 0,1;";
            $resultado = $this->filtro($sql);
            $scriptArrayCompleto = $this->lisAsos2($resultado);
            return $scriptArrayCompleto;
        }
    public function desactivarCreditoDisponibleVigente($idcliente){
            $sql="update wc_clientelineacredito set estado='0' where idcliente='".$idcliente."';";
            $EjecutaConsultaBoolean = $this->filtro($sql);
            return $EjecutaConsultaBoolean;
        }
    public function agregarLineaCredito($idcliente,$lcreditosoles,$lcreditodolares,$deudasoles,$deudadolares,$movimiento,$cantidad,$idcalificacion,$condcompra,$observaciones,$anulado,$estado,$dcontado_s,$dcontado_d,$dcredito_s,$dcredito_d,$dletrabanco_s,$dletrabanco_d,$dletracartera_s,$dletracartera_d,$dletraprotestada_s,$dletraprotestada_d,$tcambio,$usuariocreacion,$fechacreacion,$condicioncompra){
        $sql="INSERT INTO `wc_clientelineacredito`
            (`idcliente`,
            `lcreditosoles`,
            `lcreditodolares`,
            `deudasoles`,
            `deudadolares`,
            `movimiento`,
            `cantidad`,
            `idcalificacion`,
            `condcompra`,
            `observaciones`,
            `anulado`,
            `estado`,
            `dcontado_s`,
            `dcontado_d`,
            `dcredito_s`,
            `dcredito_d`,
            `dletrabanco_s`,
            `dletrabanco_d`,
            `dletracartera_s`,
            `dletracartera_d`,
            `dletraprotestada_s`,
            `dletraprotestada_d`,
            `tcambio`,
            `usuariocreacion`,
            `fechacreacion`,
            `idcondicioncompra`)
            VALUES ('".$idcliente."',
            '".$lcreditosoles."',
            '".$lcreditodolares."',
            '".$deudasoles."',
            '".$deudadolares."',
            '".$movimiento."',
            '".$cantidad."',
            '".$idcalificacion."',
            '".$condcompra."',
            '".$observaciones."',
            '".$anulado."',
            '".$estado."',
            '".$dcontado_s."',
            '".$dcontado_d."',
            '".$dcredito_s."',
            '".$dcredito_d."',
            '".$dletrabanco_s."',
            '".$dletrabanco_d."',
            '".$dletracartera_s."',
            '".$dletracartera_d."',
            '".$dletraprotestada_s."',
            '".$dletraprotestada_d."',
            '".$tcambio."',
            '".$usuariocreacion."',
            '".$fechacreacion."',
            '".$condicioncompra."');";
            $EjecutaConsultaBoolean = $this->filtro($sql);
            return $EjecutaConsultaBoolean;
        }
    public function insert_update_resumenevaluacioncrediticia($idcliente,$deudacontadosoles,$deudacontadodolares,$deudacreditosoles,$deudacreditodolares,$deudaletrabancosoles,$deudaletrabancodolares,$deudaletraprotestadasoles,$deudaletraprotestadadolares,$lineacreditototal,$deudatotal,$lineacreditodisponible,$fechaultimacompra,$ovultimacompra,$importeultimacompra,$fechaultimopago,$ovultimopago,$importeultimopago,$condicioncompra,$calificacioncompra,$estado){

            $sql="INSERT INTO `wc_resumenevaluacioncrediticia`
            (`idcliente`,
            `deudacontadosoles`,
            `deudacontadodolares`,
            `deudacreditosoles`,
            `deudacreditodolares`,
            `deudaletrabancosoles`,
            `deudaletrabancodolares`,
            `deudaletraprotestadasoles`,
            `deudaletraprotestadadolares`,
            `lineacreditototal`,
            `deudatotal`,
            `lineacreditodisponible`,
            `fechaultimacompra`,
            `ovultimacompra`,
            `importeultimacompra`,
            `fechaultimopago`,
            `ovultimopago`,
            `importeultimopago`,
            `condicioncompra`,
            `calificacioncompra`,
            `estado`)values
            ('".$idcliente."',
            '".$deudacontadosoles."',
            '".$deudacontadodolares."',
            '".$deudacreditosoles."',
            '".$deudacreditodolares."',
            '".$deudaletrabancosoles."',
            '".$deudaletrabancodolares."',
            '".$deudaletraprotestadasoles."',
            '".$deudaletraprotestadadolares."',
            '".$lineacreditototal."',
            '".$deudatotal."',
            '".$lineacreditodisponible."',
            '".$fechaultimacompra."',
            '".$ovultimacompra."',
            '".$importeultimacompra."',
            '".$fechaultimopago."',
            '".$ovultimopago."',
            '".$importeultimopago."',
            '".$condicioncompra."',
            '".$calificacioncompra."',
            '".$estado."')";


           $EjecutaConsultaBoolean = $this->filtro($sql);
           return $EjecutaConsultaBoolean;
        }
}
Class Tipocambio extends configjob{
	private $_name="wc_tipocambio";
	private $_moneda="wc_moneda";
	function consultavigentehoy(){
		$sql="
		Select m.simbolo,m.nombre,tc.compra,tc.venta From ".$this->_name." tc
		Inner Join ".$this->_moneda." m On m.idmoneda=tc.idmoneda
		Where tc.estado=1 and tc.fechatc=date_format(Now(),'%y/%m/%d')
		Order By tc.fechatc desc,tc.estado desc,tc.idmoneda";
		 $resultado = $this->filtro($sql);
            $scriptArrayCompleto = $this->lisAsos2($resultado);
            return $scriptArrayCompleto;
	}
}
Class logicaljob extends configjob{
	function listarCliente($ini,$fin){
        $sql="select idcliente from wc_cliente where estado=1 order by idcliente asc limit ".$ini.",".$fin;
		$resultado = $this->filtro($sql);
        $lisAsos2 = $this->lisAsos2($resultado);
        return $lisAsos2;
	}
    function truncate_wc_resumenevaluacioncrediticia(){
        $sql="truncate  wc_resumenevaluacioncrediticia;";
		$resultado = $this->filtro($sql);
        return $resultado;
	}

}

$hora_minuto= date("H:i");
if($hora_minuto>="19:00" and $hora_minuto<="19:01"){
    $fp = fopen("C:/wamp/www/sistema_erpx/libs/log.txt", "w"); fputs($fp, 0); fclose($fp);
}

$intervalo=100;
//entra y obtiene el inicio
$file = fopen("C:/wamp/www/sistema_erpx/libs/log.txt", "r");
while(!feof($file)){ $ini= fgets($file); } $ini=intval($ini);
$fin=$ini+$intervalo;
$fp = fopen("C:/wamp/www/sistema_erpx/libs/log.txt", "w"); fputs($fp, $fin); fclose($fp);

$logicaljob=new logicaljob();
$listarCliente = $logicaljob->listarCliente($ini,$intervalo);
if($ini==0){
    $logicaljob->truncate_wc_resumenevaluacioncrediticia();
    $configjob->commit1();
}
$in='';

for ($i = 0; $i < count($listarCliente); $i++) {
    $idcliente = $listarCliente[$i]['idcliente'];
    $in.=$idcliente.',';
}
$in=substr ($in, 0, -1);


set_time_limit(10000);
$cliente=new Cliente();
$tipocambio=new Tipocambio();
$creditos = new Creditos();
$idpadrec = $_REQUEST['lstCategoriaPrincipal'];
$idcategoria = $_REQUEST['lstZonaCobranza'];
$idzona = $_REQUEST['lstZona'];
$idcliente = $_REQUEST['idCliente'];
$idvendedor=$_REQUEST['txtVendedor'];
$orden1= $_REQUEST['orden1'];
$filtro1= $_REQUEST['filtro1'];
$filtro2= $_REQUEST['filtro2'];
$filtro3= $_REQUEST['filtro3'];



$listaCalificaciones=$cliente->listaCalificaciones();
$listaCondicionCompra=$cliente->listaCondicionCompra();

$consultavigentehoy=$tipocambio->consultavigentehoy();
$get_tcambio=$consultavigentehoy[0]['compra'];


$data1=$cliente->listadoclientes_evaluacioncrediticia($in);
if($idpadrec=="" and $idcategoria=="" and $idzona=="" and $idcliente!=""){
    if(count($data1)>1){ $data1[0]=null; }
}


for($i=0;$i<count($data1);$i++){
    if($data1[$i]!=null){
        $lista_deuda_contado='';
        $lista_deuda_credito='';
        $lista_deuda_letrabanco='';
        $lista_deuda_letracartera='';
        $lista_deuda_letraprotestada='';

        $tempVendidoSoles=0.00;
        $tempPagadoSoles=0.00;
        $tempDeudaSoles=0.00;
        $tempDeudaContadoSoles=0.00;
        $tempDeudaCreditoSoles=0.00;
        $tempDeudaletrabancoSoles=0.00;
        $tempDeudaletracarteraSoles=0.00;
        $tempDeudaletraprotestadaSoles=0.00;

        $tempVendidoDolares=0.00;
        $tempPagadoDolares=0.00;
        $tempDeudaDolares=0.00;
        $tempDeudaContadoDolares=0.00;
        $tempDeudaCreditoDolares=0.00;
        $tempDeudaletrabancoDolares=0.00;
        $tempDeudaletracarteraDolares=0.00;
        $tempDeudaletraprotestadaDolares=0.00;

        $tempDeudaTotal=0.00;

       $lista_deuda_contado=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'contado');
       $lista_deuda_credito=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'credito');
       $lista_deuda_letrabanco=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'letrabanco');
       $lista_deuda_letracartera=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'letracartera');
       $lista_deuda_letraprotestada=$cliente->listaDeudaTotalCliente($data1[$i]['idcliente'],'letraprotestada');
       $ultimoPagoCliente=$cliente->ultimoPagoCliente($data1[$i]['idcliente']);
       $listaCalificacionActual=$cliente->listaCalificacionActual($data1[$i]['idcliente']);
       $listaCondicionCompraActual=$cliente->listaCondicionCompraActual($data1[$i]['idcliente']);
       $ultimaCompraCliente=$cliente->ultimaCompraCliente($data1[$i]['idcliente']);

       foreach ($lista_deuda_contado as $value) {
           if($value['idmoneda']==1){
            $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
            $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
            $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaContadoSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
           if($value['idmoneda']==2){
            $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
            $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
            $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaContadoDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
       }

       foreach ($lista_deuda_credito as $value) {
           if($value['idmoneda']==1){
            $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
            $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
            $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaCreditoSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
           if($value['idmoneda']==2){
            $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
            $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
            $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaCreditoDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
       }

       foreach ($lista_deuda_letrabanco as $value) {
           if($value['idmoneda']==1){
            $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
            $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
            $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaletrabancoSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));

           }
           if($value['idmoneda']==2){
            $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
            $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
            $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaletrabancoDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
       }

       foreach ($lista_deuda_letracartera as $value) {
           if($value['idmoneda']==1){
            $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
            $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
            $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaletracarteraSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
           if($value['idmoneda']==2){
            $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
            $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
            $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaletracarteraDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
       }

       foreach ($lista_deuda_letraprotestada as $value) {
           if($value['idmoneda']==1){
            $tempVendidoSoles=$tempVendidoSoles+($value['importedoc']);
            $tempPagadoSoles=$tempPagadoSoles+($value['importedoc']-$value['saldodoc']);
            $tempDeudaSoles=$tempDeudaSoles+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaletraprotestadaSoles=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
           if($value['idmoneda']==2){
            $tempVendidoDolares=$tempVendidoDolares+($value['importedoc']);
            $tempPagadoDolares=$tempPagadoDolares+($value['importedoc']-$value['saldodoc']);
            $tempDeudaDolares=$tempDeudaDolares+($value['importedoc']-($value['importedoc']-$value['saldodoc']));
            $tempDeudaletraprotestadaDolares=($value['importedoc']-($value['importedoc']-$value['saldodoc']));
           }
       }
        $tempDeudaTotal=$tempDeudaSoles+($tempDeudaDolares*$get_tcambio);
        $calcularCreditoDisponible=$cliente->calcularCreditoDisponible($data1[$i]['idcliente'],$tempDeudaTotal,$get_tcambio,'');
        $lineaCreditoDisponible=$calcularCreditoDisponible[0]['lineacreditoactual']-$calcularCreditoDisponible[0]['deudatotal'];
        $clienteAuditado=$creditos->clienteAuditado($data1[$i]['idcliente']);

        $dataFinal[]=array("d1_idcliente"=>$data1[$i]['idcliente'],
                         "totalvendidosoles"=>$tempVendidoSoles,
                         "totalvendidodolares"=>$tempVendidoDolares,
                         "totalpagadosoles"=>$tempPagadoSoles,
                         "totalpagadodolares"=>$tempPagadoDolares,
                         "totaldeudasoles"=>$tempDeudaSoles,
                         "totaldeudadolares"=>$tempDeudaDolares,
                         "deudacontadosoles"=>$tempDeudaContadoSoles,
                         "deudacreditosoles"=>$tempDeudaCreditoSoles,
                         "deudaletrabancosoles"=>$tempDeudaletrabancoSoles,
                         "deudaletracarterasoles"=>$tempDeudaletracarteraSoles,
                         "deudaletraprotestadasoles"=>$tempDeudaletraprotestadaSoles,
                         "deudacontadodolares"=>$tempDeudaContadoDolares,
                         "deudacreditodolares"=>$tempDeudaCreditoDolares,
                         "deudaletrabancodolares"=>$tempDeudaletrabancoDolares,
                         "deudaletracarteradolares"=>$tempDeudaletracarteraDolares,
                         "deudaletraprotestadadolares"=>$tempDeudaletraprotestadaDolares,
                         "fcobro"=>$ultimoPagoCliente[0]['fcobro'],
                         "idordenventa"=>$ultimoPagoCliente[0]['idordenventa'],
                         "codigov"=>$ultimoPagoCliente[0]['codigov'],
                         "montoasignado"=>$ultimoPagoCliente[0]['montoasignado'],
                         "idmoneda"=>$ultimoPagoCliente[0]['idmoneda'],
                         "lineacreditoactual"=>$calcularCreditoDisponible[0]['lineacreditoactual'],
                         "deudatotal"=>$calcularCreditoDisponible[0]['deudatotal'],
                         "lineacreditodisponible"=>$lineaCreditoDisponible,
                         "prueba"=>$tempDeudaTotal,
                         "idcalificacion"=>$listaCalificacionActual[0]['idcalificacion'],
                         "calificacion"=>$listaCalificacionActual[0]['calificacion'],
                         "auditado"=>$clienteAuditado[0]['auditado'],
                         "idcondicioncompra"=>$listaCondicionCompraActual[0]['idcondicioncompra'],
                         "condicioncompra"=>$listaCondicionCompraActual[0]['condicioncompra'],
                         "ov_codigov"=>$ultimaCompraCliente[0]['codigov'],
                         "ov_fordenventa"=>$ultimaCompraCliente[0]['fordenventa'],
                         "ov_importeordenventa"=>$ultimaCompraCliente[0]['importeordenventa'],
                         "ov_percepcion"=>$ultimaCompraCliente[0]['percepcion'],
                         "ov_gastosadicionales"=>$ultimaCompraCliente[0]['gastosadicionales'],
                         "ov_idmoneda"=>$ultimaCompraCliente[0]['idmoneda'],
                         );
    }
}


//echo json_encode($dataFinal);
$temp_idcliente=-1;
for($i=0;$i<count($dataFinal);$i++){
    $name_condicioncompra='';
    $name_calificacioncompra='';
    if($dataFinal[$i]!=null){
        if($dataFinal[$i]['d1_idcliente']!=$temp_idcliente){
            $moneda='';
            $ultimacompra_moneda='';
            if($dataFinal[$i]['ov_idmoneda']==1){ $ultimacompra_moneda='S/.&nbsp;'; }
            if($dataFinal[$i]['ov_idmoneda']==2){ $ultimacompra_moneda='US $.'; }
            if($dataFinal[$i]['montoasignado']!=''){
                if($dataFinal[$i]['idmoneda']==1){ $moneda='S/.&nbsp;'; }
                if($dataFinal[$i]['idmoneda']==2){ $moneda='US $.'; }
            }

            if($dataFinal[$i]['idcalificacion']!=''){
                    $name_calificacioncompra=$dataFinal[$i]['calificacion'];
            }
            if($dataFinal[$i]['idcondicioncompra']!=''){
                    $name_condicioncompra=$dataFinal[$i]['condicioncompra'];
            }

                $insert_update_resumenevaluacioncrediticia=$creditos->insert_update_resumenevaluacioncrediticia(
                $dataFinal[$i]['d1_idcliente'],
                number_format(round($dataFinal[$i]['deudacontadosoles'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['deudacontadodolares'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['deudacreditosoles'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['deudacreditodolares'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['deudaletrabancosoles'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['deudaletrabancodolares'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['deudaletraprotestadasoles'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['deudaletraprotestadadolares'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['lineacreditoactual'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['deudatotal'],2), 2, ".", ""),
                number_format(round($dataFinal[$i]['lineacreditodisponible'],2), 2, ".", ""),
                $dataFinal[$i]['ov_fordenventa'],
                $dataFinal[$i]['ov_codigov'],
                str_replace ('&nbsp;' , '' ,$ultimacompra_moneda) .$dataFinal[$i]['ov_importeordenventa'],
                $dataFinal[$i]['fcobro'],
                $dataFinal[$i]['codigov'],
                str_replace ('&nbsp;' , '' ,$moneda) .number_format(round($dataFinal[$i]['montoasignado'],2), 2, ".", ""),
                $name_condicioncompra,
                $name_calificacioncompra,
                1);
                $configjob->commit1();

        }
        $temp_idcliente=$dataFinal[$i]['d1_idcliente'];
    }
}
?>

<script type="text/javascript">
window.close();
</script>