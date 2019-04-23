<?php
class myprogBusiness extends mainBusiness{ 
    public function __construct () {
    	$this->Model = new myprogModel();
        $this->Model->wait();
    }
    public function funcionNegocio(){
		return $this->Model->funcionModelo("dato en negocio");
	}

    public function funcionNegocio2(){
        try {
           return $this->Model->funcionModelo2("1");
        } catch (Exception $e) {
            throw $e;
            
        }
    }

}