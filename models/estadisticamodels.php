<?php
    class estadistica extends Applicationbase{

            public function vendedorxCantidadDeIngreso(){
            $sql="select concat(a.nombres,' ',a.apellidopaterno,' ',a.apellidomaterno) as Vendedor,sum(ov.importeov) as importe
                    from wc_ordenventa as ov 
                    inner join wc_actor a on ov.idvendedor=a.idactor 
                    group by (ov.idvendedor) order by importe desc limit 15;";
            $data=$this->EjecutaConsulta($sql);
            return $data;
        }
    }
?>
