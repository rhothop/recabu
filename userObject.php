<?php
class userObject {
	public $ID;
	public $name;
	public $rating;
	
	public function __construct($cId, $cName, $cRating) {
		$this->ID = intval($cId);
		$this->name = $cName;
		if( $cRating == null ) {
			$this->rating = 0;
		} else {
			$this->rating = $cRating;
		}
	}
}