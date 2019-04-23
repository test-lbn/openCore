<?php
$plantilla="default/default.html";
class bars_1Controller extends mainController{
	private $Business;
	
	public function __construct () {
		$this->Business = new bars_1Business();
	}
	function funcionRest($parametros){
		$resultado['campo1']=$this->Business->funcionNegocio();
		$this->response($resultado);
	}
}	

?>