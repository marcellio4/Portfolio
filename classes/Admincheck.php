<?php
class Admincheck{

	private $clean;
	private $title_field;
	private $trimmed;
	private $html;
	private $descr_field;


	public function title_check($title_field){
		if(!empty($title_field)){
			return true;
		}else{
			return false;
		}
	}

	public function description_check($descr_field){
		if(!empty($descr_field)){
			return true;
		}else{
			return false;
		}
	}

	public function set_title($title){
		if (isset($title)){
			$trimmed = trim($title);
			$html = htmlentities($trimmed);
			return $clean = $html;
		}
	}

	public function set_description($description){
		if(isset($description)){
			$trimmed = trim($description);
			$html = htmlentities($trimmed);
			return $clean = $html;
		}
	}

}
?>
