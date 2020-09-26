<?php
class postObject {
	public $uid;
	public $link;
	public $date;
	public $rating;
	public $title;
	public $content;
	public $nsfw;
	public $oc;
	public $author;
	public $childs = array();
	public $blocked;
	public $allCommentsCount;
	public $yourVote;
	public $parent;
	
	public function __construct($cId, $cTitle, $cContent, $cAuthor, $cDate, $cName = '', $cNSFW = false, $cOc = false, $cRating = 0, $cBlocked = false, $parent = 0) {
		$this->uid = $cId;
		$this->title = $cTitle;
		$this->content = $cContent;
		$this->author = $cAuthor;
		$this->nsfw = boolval($cNSFW);
		$this->oc = boolval($cOc);
		$this->blocked = boolval($cBlocked);
		$this->allCommentsCount = 0;
		if( $cRating == null ) {
			$this->rating = 0;
		} else {
			$this->rating = $cRating;
		}
		$this->date = $cDate;
		if( $cName != '' ) {
		    if($parent == 0) {
			    $this->link = '/posts/'.$cName;
		    } else {
		        $this->link = '/posts/'.$cName.'#comment'.$cId;
		    }
		} else {
			$this->link = '';
		}
		$this->yourVote = 0;
	}
	
}
?>