<?php
$plantilla="default/default.html";
class bars_2Controller extends mainController{
	private $Business;
	
	public function __construct () {
		$this->Business = new bars_2Business();
	}
	function funcionRest($parametros){
		$resultado['campo1']=$this->Business->funcionNegocio();
		$this->response($resultado);
	}
}	

?>