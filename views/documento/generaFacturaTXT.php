<script src="/javascript/impresion.js"></script>
<?php
$max_item_xfactura=$maximoItem;
$cantidad=count($DetalleFactura);
$pages=$hojas;
?>

<?php

for($z=0;$z<$pages;$z++){
	$minimo=($z)*$max_item_xfactura;
	$maximo=(($minimo+$max_item_xfactura)>$cantidad)?($cantidad):($minimo+$max_item_xfactura);
?>
<div style="display: inline-block">
<div id="imprimirdiv">
<input type="hidden" value="35" id="cantidadMaxima">
<input type="hidden" value="35" id="numerodoc">

<!--<a href="#" id="print" ><img src="/imagenes/imprimir.gif"></a>-->
<button onclick="window.print();">Imrpimir</button>
</div>
    
<div id="muestra">
    <br><br>


	<table id="encabezado">
		<tr>
			<td class="td1" > </td>
			<td class="td2" ><?php echo $Factura[0]['razonsocial']; ?></td>
			<td class="td3" ></td>
			<td class="td4" ></td>
		</tr>
		<tr>
			<td class="td1"></td>
			<td class="td2"><?php echo $Factura[0]['direccion_envio']; ?></td>
			<td class="td3"></td>
			<td class="td4"><?php echo $Factura[0]['fecha'];  ?></td>
		</tr>
		<tr>
			<td class="td1"> </td>
			<td class="td2"></td>
			<td class="td3"> </td>
			<td class="td4"><?php echo $Factura[0]['numeroRelacionado'];  ?></td>
		</tr>
		<tr>
			<td> </td>
			<td><?php echo $Factura[0]['condicion']; ?></td>
			<td></td>
			<td><?php echo $Factura[0]['nombredepartamento'].' - '.$Factura[0]['nombreprovincia'].' - '.$Factura[0]['nombredistrito'];  ?></td>
		</tr>
		<tr>
			<td> .</td>
			<td> </td>
			<td> </td>
			<td> </td>
		</tr>
		<tr>
			<td> </td>
			<td><?php echo $Factura[0]['contacto']; ?></td>
			<td></td>
			<td><?php echo $Factura[0]['referencia']; ?></td>
		</tr>
		<tr>
			<td> </td>
			<td class="td5"><?php echo $Factura[0]['ruc']; ?></td>
			<td></td>
			<td><?php echo $Factura[0]['telefono'].'/'.$Factura[0]['celular']; ?></td>
		</tr>
	</table >
        <br><br>
	<table id="cuerpoFactura">
		<thead>
			<tr>
				<th class="tc1"></th>
				<th class="tc2"></th>
				<th class="tc3"></th>
				<th class="tc4"></th>
				<th class="tc5"></th>
				<th class="tc6"></th>
			</tr>
		</thead>
		<tbody>
				<?php if ($maximo>$max_item_xfactura ) { ?>
					
					<tr>
						<td>viene..</td>
					</tr>
				<?php } ?>
			<?php 
			$importetotal=0;
			/*$maximo=25;
			$minimo=0;*/
			//$cantidad=count($DetalleFactura);
			for ($i=$minimo; $i <$maximo ; $i++) { 
				///if (!empty($DetalleFactura[$i]['codigopa']) ) { ?>
					
				
			<tr>
				
				<td class="tc1"><?php echo ($i+1); ?></td>
				<td class="tc2"><?php echo substr($DetalleFactura[$i]['codigopa'],0,17); ?></td>
				<?php 
				$DetalleFactura[$i]['nompro']=html_entity_decode($DetalleFactura[$i]['nompro'],ENT_QUOTES,'UTF-8');
				$tamaño=strlen($DetalleFactura[$i]['nompro']);
				$texto=substr($DetalleFactura[$i]['nompro'],0,48);
				if ($tamaño>48) {
					$texto.="...";
				}

				?>
				<td class="tc3"><?php echo $texto; ?></td>
				<td class="tc4"><?php echo $DetalleFactura[$i]['cantdespacho']; ?></td>
				<td class="tc5"><?php echo number_format($DetalleFactura[$i]['preciofinal'],2); ?></td>
				<td class="tc6"><?php echo ' '.number_format((($DetalleFactura[$i]['preciofinal'])*($DetalleFactura[$i]['cantdespacho'])),2); ?></td>
				<?php $importetotal+=($DetalleFactura[$i]['preciofinal'])*($DetalleFactura[$i]['cantdespacho']); ?>

			</tr>
			<?php //} 
			}?>
				<?php if ($cantidad>$maximo && $maximo!=$cantidad) { ?>
					<tr>
						<td colspan="6"><?php echo str_pad('-',225,'-',STR_PAD_LEFT); ?></td>
					</tr>
					<tr>
						<td>va..</td>
					</tr>
				<?php } ?>
				<?php if ($maximo==$cantidad){ ?>
					<tr>
						<td colspan="6"><?php echo str_pad('-',225,'-',STR_PAD_LEFT); ?></td>
					</tr>
				<?php  } ?>
		</tbody>

	</table>

	<table id="piepagina">
		<?php $conversion=$letras->ValorEnLetras(round($importetotal,2),$Factura[0]['nombremoneda']); ?>
		<tr>
			<td width="500" colspan="6" ><?php echo $conversion; ?></td>
			<td width="50" style="text-align:right;"><?php echo $Factura[0]['simbolomoneda'].' '.number_format(($importetotal/1.18),2); ?></td>
		</tr>
		<tr>
			<td style="width:692px;font-size: 5px;" colspan="6" style="padding-bottom:3;"><?php echo '.'; ?></td>

		</tr>
		<tr>
			<td width="635" colspan="6" style="padding-bottom:0;text-align: right;">18%</td>
			<td width="70" style="text-align:right;"><?php echo $Factura[0]['simbolomoneda'].' '.number_format(($importetotal-($importetotal/1.18)),2); ?></td>
		</tr>
		<tr>
			<td width="60"><?php  ?></td>
			<td width="80"><?php echo date('d'); ?></td>
			<td width="110"><?php echo $mes; ?></td>
			<td width="35"><?php echo date('Y'); ?></td>
			<td width="300"></td>
			<td style="width:20px;text-align: right;"></td>
			<td style="width:100px;text-align: right;"><?php echo $Factura[0]['simbolomoneda'].' '.number_format($importetotal,2); ?></td>
		</tr>
		<tr>
			<td width="60"><?php  ?></td>
			<td width="80"></td>
			<td width="110"></td>
			<td width="35"></td>
			
			<td colspan="2" style="width:320px;text-align: center;">Total + Percepcion(2%)</td>
			<td style="width:100px;text-align: right;"><?php echo $Factura[0]['simbolomoneda'].' '.number_format(($importetotal*1.02),2); ?></td>
		</tr>
		
	</table>
</div>
 
<?php
if(($pages-$z)!=1){
?>
<br style="page-break-before: always;" />
<?php
}
?>
</div>    
<?php
}
?>



