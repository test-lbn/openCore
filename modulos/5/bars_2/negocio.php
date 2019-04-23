<?php
class bars_2Business extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new bars_2Model();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}
}