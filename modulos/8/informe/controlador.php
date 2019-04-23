<?php
$plantilla="default/default.html";
class informeController extends mainController{
	private $Business;
	
	public function __construct () {
		$this->Business = new informeBusiness();
	}

	function getInformacion(){
		$modelo = new informeModel('sugar');
		$resultado = $modelo->getInformacion();
		$this->response($resultado);
	}
}
?>