<?php
class mainBusiness {
	public $Model;

	public function __construct(){
	}
	
	function __destruct(){
		$this->Model = null;
		unset($this->Model); 
	}
}
