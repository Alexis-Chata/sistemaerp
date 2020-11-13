<?php  

Class ClientePosicion extends ApplicationBase{
	private $_name="wc_clienteposicion";

	public function datosPosicion($idCliente)
	{
		return $this->leeRegistro($this->_name,"","idcliente=".$idCliente,"","");
	}

	public function grabaPosicion($data)
	{
		return $this->grabaRegistro($this->_name,$data);
	}

	public function actualizaPosicion($idcliente)
	{
		$data['estado']=0;
		return $this->actualizaRegistro($this->_name,$data,"idcliente=".$idcliente);
	}

	public function actualizasaldoPosicion($saldo,$idcliente)
	{
		$sql="Update ".$this->_name." set saldo=saldo-".$saldo." Where idcliente=".$idcliente." and estado=1";
		return $this->ejecutaConsulta($sql);
	}
        
	public function updateClientePosicion($data,$idcliente)
	{
		
                $sql=$this->actualizaRegistro($this->_name,$data,"idcliente=$idcliente");
            
	}
        function buscaxfiltro($filtro){
			$data=$this->leeRegistro($this->_name,"",$filtro,"");
			return $data;
        }
        

}

?>