<?php

class menuBusiness extends mainBusiness
{
	public function __construct () {
    	$this->Model = new menuModel();
        $this->Model->wait();
        die("OK");
    }
	
}