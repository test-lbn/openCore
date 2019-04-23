<?php
class [[programa]]Business extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new [[programa]]Model();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}