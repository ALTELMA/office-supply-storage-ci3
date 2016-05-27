<?php if(!defined('BASEPATH'))exit('No direct Script access allowed!!');

require_once(APPPATH.'libraries/PHPExcel/trunk/Classes/PHPExcel.php');

class MyExcel extends PHPExcel{
	
	public function __construct(){ 
		parent::__construct(); 
	} 
}
?>
