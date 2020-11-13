<?php  
Class TipoCambioController extends applicationgeneral{

	function consulta(){
		$id=$_REQUEST['id'];
		$tipocambio=New TipoCambio();
		$data=$tipocambio->consultavigente($id);
		echo json_encode($data[0]);
		/*echo "{'idtipocambio':".$data[0]['idtipocambio'].",";
		echo "compra:".$data[0]['compra'].",";
		echo "venta:".$data[0]['venta']."}";*/
	}


	function consultaSimboloVigente(){
		$id=$_REQUEST['id'];
		$tipocambio=$this->AutoLoadModel("TipoCambio");
		$data=$tipocambio->consultaDatosTCVigente($id);
		echo json_encode($data[0]);
	}
//
	function consultahoy(){
		$tipocambio=New TipoCambio();
		$data=$tipocambio->consultavigentehoy();
		$numtc=count($data);
		if($numtc==0){
			echo "0";
		}else{
			for ($i=0; $i < $numtc; $i++) { 
				echo "<h2>".$data[$i]['simbolo']."</h2>";
				echo "<h4>Compra: ".$data[$i]['compra']."</h4>";
				echo "<h4>Venta: ".$data[$i]['venta']."</h4>";
				/*echo "<tr>";
					//echo "<td>".$data[$i]['nombre']."(".$data[$i]['simbolo'].")</td>";
					echo "<td>Moneda:<br>".$data[$i]['simbolo']."</td>";
					echo "<td>Compra: ".$data[$i]['compra']."</td>";
					echo "<td>Venta: ".$data[$i]['venta']."</td>";
				echo "</tr>";*/
			}
		}

		
	}
	function gestionar(){
		$tipocambio=New TipoCambio();
		$data['tipocambio']=$tipocambio->consultalista();
		$data['todos']=count($data['tipocambio']);
		$this->view->show("/tipocambio/verlistado.phtml",$data);
	}

	function nuevo(){
		$this->view->show("/tipocambio/nuevo.phtml",$data);
	}

	function grabarVigente(){
		$data=$_REQUEST['TipoCambio'];
		$tipocambio=New TipoCambio();
		$exito=$tipocambio->grabavigente($data);
		$ruta['ruta']="/tipocambio/gestionar";
		$this->view->show("ruteador.phtml",$ruta);
	}
         function obtenerTipoCambio(){
                $tipocambio=New TipoCambio();
                $consultavigentehoy=$tipocambio->consultavigentehoy();
                $get_tcambio=$consultavigentehoy[0]['compra'];
                echo json_encode(array("resultado"=>$get_tcambio));
        }
}


?>