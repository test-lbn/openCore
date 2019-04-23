<?php
$plantilla="default/default.html";
class myprogController extends mainController{
	private $Business;
	
	public function __construct () {
		$this->Business = new myprogBusiness();
	}
	function funcionRest($parametros){
		$resultado=$this->Business->funcionNegocio();
		$this->response($resultado);
	}
	function funcionRest2($parametros){
		try {
           $resultado=$this->Business->funcionNegocio2();
           $this->response($resultado);
        } catch (Exception $e) {
            $this->catchError($e);
        }
	}
}	