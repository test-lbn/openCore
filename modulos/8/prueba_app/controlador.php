<?php
$plantilla="default/default.html";
class prueba_appController extends mainController{
	private $Business;
	
	public function __construct () {
		$this->Business = new prueba_appBusiness();
	}
	function funcionRest($parametros){
		$resultado['campo1']=$this->Business->funcionNegocio();
		$this->response($resultado);
	}
}	

?>