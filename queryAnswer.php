<?php
class queryAnswer {
	public $result;
	public $content;
	public $msg;
	public $draw;
	public $status;
	
	public function __construct($cResult = false, $cContent = '', $cMsg = '', $cDraw = false, $cStatus = 200 ) {
		$this->result = $cResult;
		$this->content = $cContent;
		$this->msg = $cMsg;
		$this->draw = $cDraw;
		$this->status = $cStatus;
	}
	
}
?>