<?php
require_once('routers/bd.php');
require_once('userObject.php');

function getUserForDraw() {
    if(isset($_COOKIE['auth'])) {
    	$bd = new bd();
    	return $bd->getUserByToken($_COOKIE['auth']);
    } else {
        return null;
    }
}

function declOfNum($n, $text_forms) {  
    $n = abs($n) % 100; 
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) { return $text_forms[2]; }
    if ($n1 > 1 && $n1 < 5) { return $text_forms[1]; }
    if ($n1 == 1) { return $text_forms[0]; }
    return $text_forms[2];
}


function calcDate($cDate) {
	 
	$firstDate = date("Y-m-d H:i:s");
	//$secondDate = $d; 
	 
	$firstDateTimeObject = DateTime::createFromFormat('Y-m-d H:i:s', $firstDate);
	$secondDateTimeObject = DateTime::createFromFormat('Y-m-d H:i:s', $cDate);
	 
	$delta = $secondDateTimeObject->diff($firstDateTimeObject);
	
	$years = $delta->format('%y');
	$months = $delta->format('%m');
	$days = $delta->format('%d');
	$hours = $delta->format('%h');
	$minutes = $delta->format('%i');
	$seconds = $delta->format('%s');
	
	$res = '';
	$val1 = '';
	$val2 = '';
	
	$yearName = declOfNum($years,array('год','года','лет'));
	$montName = declOfNum($months,array('месяц','месяца','месяцев'));
	$dayName = declOfNum($days,array('день','дня','дней'));
	$hourName = declOfNum($hours,array('час','часа','часов'));
	$minuName = declOfNum($minutes,array('минуту','минуты','минут'));
	$secoName = declOfNum($seconds,array('секунду','секунды','секунд'));
	
	if($years > 0) {
		$val1 = $year.' '.$yearName;
	}
	if ($months > 0) {
		if($val1 == '') {
			$val1 = $months.' '.$montName;
		} elseif ($val2 == '') {
			$val2 = $months.' '.$montName;
		}
	}
	if ($days > 0) {
		if($val1 == '') {
			$val1 = $days.' '.$dayName;
		} elseif ($val2 == '') {
			$val2 = $days.' '.$dayName;
		}
	}
	if ($hours > 0) {
		if($val1 == '') {
			$val1 = $hours.' '.$hourName;
		} elseif ($val2 == '') {
			$val2 = $hours.' '.$hourName;
		}
	}
	if ($minutes > 0) {
		if($val1 == '') {
			$val1 = $minutes.' '.$minuName;
		} elseif ($val2 == '') {
			$val2 = $minutes.' '.$minuName;
		}
	}
	if ($seconds > 0) {
		if($val1 == '') {
			$val1 = $seconds.' '.$secoName;
		} elseif ($val2 == '') {
			$val2 = $seconds.' '.$secoName;
		}
	}
		
	return $val1.' '.$val2.' назад';
}

function drawPost( $post, $showAnswerButton = false ) {
    $user = getUserForDraw();
    $yourVote = 0;
    $up = '';
    $down = '';
	$delbutton = '';
    if($user != null) {
        $bd = new bd();
    	$yourVote = $bd->getYourVote($post->uid,$user->ID);
    	if($yourVote == 1) {
    	    $up = ' upvote_pressed';
    	} elseif($yourVote == -1) {
    	    $down = ' downvote_pressed';
    	}
		if($post->author == $user->name) {
			$delbutton = '<div val="'.$post->uid.'" class="icons deletepost"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#trash"></use></svg></div>';
		}
    }
    
	$result = '<div class="row">';
	$result .= '<div class="col-lg-1"></div>';
	$result .= '<div class="col-sm-10 postRow">';
    $result .= '<div class="postContent">';
	$result .= '<div class="postLeft">';
	$result .= '<svg class="vote'.$up.'" val_target="'.$post->uid.'" val_data="1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-top"></use></svg>';
	$result .= '<span class="vote" val_target="'.$post->uid.'"  val_data="0">'.$post->rating.'</span>';
	$result .= '<svg class="vote'.$down.'" val_target="'.$post->uid.'"  val_data="-1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-bottom"></use></svg>';
	$result .= '</div>';
	$result .= '<div class="postRight">';
	$result .= '<h1>';
	if($post->blocked) {
		$title = '[Пост удален]';
	} else {
		$title = $post->title;
	}
	if( $post->link != '') {
		$result .= '<a class="postlink" href="'.$post->link.'">'.$title.'</a>';
	} else {
		$result .= $title;//$post->title;
	}
	if( $post->oc ) {
		$result .= ' <span class="OC">OC</span>';
	}
	$result .= '</h1>';
	
	$result .= '<div class="postframe"';
	if($showAnswerButton) {
	    $result .= ' style="max-height:100%;"';
	}
	$result .= '>';
	
	$result .= '<div val_type="post" val_target="'.$post->uid.'" class="post';
	if( $post->nsfw ) {
		$result .= ' blured">';
	} else {
		$result .= '">';
	}
	
	if(!$post->blocked) {
		$result .= updateContent( $post->content );
	} else {
	    $result .= updateContent( '~~пост~~' );
	}
	$result .= '</div>';
	
    $result .= '</div>';

	$commentText = declOfNum($post->allCommentsCount,array('комментарий','комментария','комментариев'));
	$result .= '<div class="postBottom">
	<a id="comment"></a>
	<a href="/user/'.$post->author.'">'.$post->author.'</a> '.calcDate($post->date).' <a href="'.$post->link.'#comment" title="'.$post->allCommentsCount.' '.$commentText.'">
	<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#comment-square"></use></svg></div> '.$post->allCommentsCount.'</a>'.$delbutton.'</div>';
	
	if( $showAnswerButton ) {
		$result .= '<div val_target="'.$post->uid.'"><button class="addComment" type="" class="btn btn-secondary">Ответить</button></div>';
	}

	
	$result .= drawComments( $post->childs, $user, true );
	
	$result .= '</div>';
	$result .= '</div>';
	$result .= '</div>';
	$result .= '<div class="col-lg-1"></div>';
	$result .= '</div>';
	
	return $result;
		
}

function drawComments( $comments, $user = null, $drawChilds = true ) {
	if( count( $comments ) == 0 ) {
		return '';
	}

	$result = '<ul>';
	foreach( $comments as $comment ) {

        $bd = new bd();

        $yourVote = 0;
        $up = '';
        $down = '';
		$delbutton = '';
		if($user != null) {
        	$yourVote = $bd->getYourVote($comment->uid,$user->ID);
        	if($yourVote == 1) {
        	    $up = ' upvote_pressed';
        	} elseif($yourVote == -1) {
        	    $down = ' downvote_pressed';
        	}
			if($comment->author == $user->name) {
				$delbutton = '<div val="'.$comment->uid.'" class="icons deletepost"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#trash"></use></svg></div>';
			}
        }

		$result .= '<li class="comment">';
		
		$result .= '<div class="commentVote">';
	    $result .= '<svg class="vote'.$up.'" val_target="'.$comment->uid.'" val_data="1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-top"></use></svg>';
	    $result .= '<span class="vote" val_target="'.$comment->uid.'"  val_data="0">'.$comment->rating.'</span>';
	    $result .= '<svg class="vote'.$down.'" val_target="'.$comment->uid.'"  val_data="-1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-bottom"></use></svg>';
	    $result .= '</div>';
		
		$result .= '<div class="commentInfo">';
		$result .= '<div class="postBottom">
		<a id="comment'.$comment->uid.'"></a><a href="'.$comment->link.'">
		<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#link-intact"></use></svg></div>
		</a>Ответ <a href="/user/'.$comment->author.'">'.$comment->author.'</a> '.calcDate($comment->date).' '.$delbutton.'</div>';
		
		if(!$comment->blocked) {
			$result .= '<div class="postframe';
			if($comment->nsfw) {
				$result .= ' blured">';
			} else {
				$result .= '">';
			}
			$result .= updateContent($comment->content).'</div>';
		} else {
		    $result .= '<div>'.updateContent( '~~коммент~~' ).'</div>';
		}
		
		$result .= '<div val_target="'.$comment->uid.'"><button class="addComment" type="" class="btn btn-secondary">Ответить</button></div>';
		$result .= '</div>';
			
		$result .= '</li>';

		if($drawChilds) {
		    $result .= drawComments( $comment->childs, $user, $drawChilds );
		}
	}
	$result .= '</ul>';
	
	return $result;
}

function updateContent( $content ) {
	$result = $content;

	$youtube = '/(^|\s)https:\/\/youtu\.be\/(\S+)(\s|$)/';
	$matches = array();
	preg_match_all($youtube, $content, $matches, PREG_PATTERN_ORDER );
	foreach( $matches[0] as $match ) {
	    $mass = array();
	    preg_match($youtube, $match, $mass);
	    $result = str_replace( $match, '<div class="youtubevideo"><iframe src="https://www.youtube.com/embed/'.$mass[2].'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>', $result);
	}
	
	$vidos = '/(^|\s)\[video\]\((\S+\.mp4)\)($|\s)/';
	$matches = array();
	preg_match_all($vidos, $content, $matches, PREG_PATTERN_ORDER );
	foreach( $matches[0] as $match ) {
	    $mass = array();
	    preg_match($vidos, $match, $mass);
	    $result = str_replace( $match, '<video controls style="max-width:100%;"><source src="'.$mass[2].'" type="video/mp4"></video>', $result);
	}
	
	$markdown1 = '/!\[(.+)\]\((\S+)( ".+"|)\)/';
	$matches = array();
	preg_match_all($markdown1, $content, $matches, PREG_PATTERN_ORDER );
	foreach( $matches[0] as $match ) {
	    $mass = array();
	    preg_match($markdown1, $match, $mass);
	    $result = str_replace( $match, '<img style="max-width:100%;" src="'.$mass[2].'" alt="'.$mass[1].'" title="'.str_replace('"', '', $mass[3]).'" />', $result);
	}
	
	$markdown2 = '/\[(.+)\]\((\S+)\)/';
	$matches = array();
	preg_match_all($markdown2, $content, $matches, PREG_PATTERN_ORDER );
	foreach( $matches[0] as $match ) {
	    $mass = array();
	    preg_match($markdown2, $match, $mass);
	    $result = str_replace( $match, '<a href="'.$mass[2].'">'.$mass[1].'</a>', $result);
	}
	
	$markdown3 = '/~~(.+)~~/';
	$matches = array();
	preg_match_all($markdown3, $content, $matches, PREG_PATTERN_ORDER );
	foreach( $matches[0] as $match ) {
	    $mass = array();
	    preg_match($markdown3, $match, $mass);
	    $result = str_replace( $match, '<s>'.$mass[1].'</s>', $result);
	}
	
	$markdown4 = '/\*\*(.+)\*\*/';
	$matches = array();
	preg_match_all($markdown4, $content, $matches, PREG_PATTERN_ORDER );
	foreach( $matches[0] as $match ) {
	    $mass = array();
	    preg_match($markdown4, $match, $mass);
	    $result = str_replace( $match, '<strong>'.$mass[1].'</strong>', $result);
	}

	$tag = '/#\S+(\s|$)/';
	$matches = array();
	preg_match_all($tag, $content, $matches, PREG_PATTERN_ORDER );
	foreach( $matches[0] as $match ) {
		$result = str_replace( $match, '<a href="/hashtag/'.str_replace('#','',$match).'" />'.$match.'</a>', $result);
	}
	
	$user = '/(^|\s)@\S+(\s|$)/';
	$matches = array();
	preg_match_all($user, $content, $matches, PREG_PATTERN_ORDER );
	foreach( $matches[0] as $match ) {
	    $userlink = str_replace('@','',$match);
	    $userlink = str_replace(',','',$userlink);
	    $userlink = str_replace('.','',$userlink);
	    $userlink = trim($userlink);
		$result = str_replace( $match, '<a href="/user/'.$userlink.'" />'.$match.'</a>', $result);
	}
	
	$result = nl2br($result);

	return $result;
}

function drawTopLine() {
    $user = getUserForDraw();
    $bd = new bd();
    $answer = '';
	if($user != null) {
	    $unread = $bd->getUnreadCount($user);
		$answer .= '<div class="logoutForm">
			<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#account-logout"></use></svg></div>
		</div> 
		Привет, '.$user->name.' 
		<div style="align-self: flex-start;"><div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#star"></use></svg></div>'.$user->rating.'</div>
		<a href="/unread/" title="'.$unread.' '.declOfNum($unread,array('непрочитанное','непрочитанных','непрочитанных')).'"><div style="align-self: center;"><div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#chat"></use></svg></div>'.$unread.'</div></a>';
   	} else {
		$answer .= '
		<div class="loginForm">
		    <input type="text" name="login" />
		    <input type="password" name="password" />
			<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#account-login"></use></svg></div>
		    <a href="/registration/">Зарегистрироваться</a>
		</div>';
   	}
    $usersCount = $bd->getUsersCount();
    if($usersCount != null) {
        $answer .= '<div style="align-self: flex-start;"><div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#people"></use></svg></div>'.$usersCount.'</div>';
    }
    return $answer;
}

function drawPageButton($pageCount, $current) {
    $uri = $_SERVER['REQUEST_URI'];
    $uri = str_replace('&str='.$current,'',$uri);
    $uri = str_replace('str='.$current,'',$uri);
    $pagelink = '?';
    if(strpos($uri,'?')) {
        $pagelink = stristr($uri,'?',false);
    }
    
    $prev = $current-1;
    $next = $current+1;
    
	$str = '<nav aria-label="Pages">';
	$str .= '<ul class="pagination">';
	$str .= '<li class="page-item"><a class="page-link myPageItem" href="'.$pagelink.'">Начало</a></li>';
	if($prev < 1) {
	    //$str .= '<li class="page-item"><a class="page-link myPageItem disabled" >Предыдущая</a></li>';
	} else {
	    $str .= '<li class="page-item"><a class="page-link myPageItem" href="'.$pagelink.'&str='.$prev.'"><<</a></li>';
	}
	if($pageCount > 10) {
	    $start = $current-4;
	    if($start < 1) {
	        $start = 1;
	    }
	    $end = $current+4;
	    if($end > $pageCount) {
	        $end = $pageCount;
	    }
	} else {
	    $start = 1;
	    $end = $pageCount;
	}
	for ($i = $start; $i <= $end; $i++) {
		$curpage = '';
		if($i == $current) {
			$curpage = ' style="filter: invert(2);"';
		}
		$str .= '<li'.$curpage.' class="page-item"><a class="page-link myPageItem" href="'.$pagelink.'&str='.$i.'">'.$i.'</a></li>';
    }
    if($next > $pageCount) {
        //$str .= '<li class="page-item"><a class="page-link myPageItem disabled" >Следующая</a></li>';
    } else {
        $str .= '<li class="page-item"><a class="page-link myPageItem" href="'.$pagelink.'&str='.($current+1).'">>></a></li>';
    }
	$str .= '</ul>';
	$str .= '</nav>';
	
	return $str;
}
?>