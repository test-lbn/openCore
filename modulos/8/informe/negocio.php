<?php
class informeBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new informeModel();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}