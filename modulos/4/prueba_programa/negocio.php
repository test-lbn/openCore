<?php
class prueba_programaBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new prueba_programaModel();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}