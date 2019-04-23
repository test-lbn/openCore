<?php
class prueba_appBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new prueba_appModel();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}