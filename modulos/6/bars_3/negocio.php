<?php
class bars_3Business extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new bars_3Model();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}