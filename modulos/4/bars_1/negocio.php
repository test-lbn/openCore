<?php
class bars_1Business extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new bars_1Model();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}