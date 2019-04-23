<?php
class bars_4Business extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new bars_4Model();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}