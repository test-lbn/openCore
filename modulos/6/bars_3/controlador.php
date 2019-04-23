<?php
$plantilla="default/default.html";
class bars_3Controller extends mainController{
	private $Business;
	
	public function __construct () {
		$this->Business = new bars_3Business();
	}
	function funcionRest($parametros){
		$resultado['campo1']=$this->Business->funcionNegocio();
		$this->response($resultado);
	}
}	

?>