<?php
require_once('routers/bd.php');
require_once('userObject.php');
require_once('languages.php');

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


function calcDate($cDate, $lang = 'ru_ru') {
	 
	$firstDate = date("Y-m-d H:i:s");
	 
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
	
	if($lang === 'en_en') {
		$yearName = 'year'.($years>1 ? 's' : '');
		$montName = 'month'.($months>1 ? 's' : '');
		$dayName = 'day'.($days>1 ? 's' : '');
		$hourName = 'hour'.($hours>1 ? 's' : '');
		$minuName = 'minute'.($minutes>1 ? 's' : '');
		$secoName = 'second'.($seconds>1 ? 's' : '');
		$agoName = ' ago';
	} else {
		$yearName = declOfNum($years,['год','года','лет']);
		$montName = declOfNum($months,['месяц','месяца','месяцев']);
		$dayName = declOfNum($days,['день','дня','дней']);
		$hourName = declOfNum($hours,['час','часа','часов']);
		$minuName = declOfNum($minutes,['минуту','минуты','минут']);
		$secoName = declOfNum($seconds,['секунду','секунды','секунд']);
		$agoName = ' назад';
	}
	
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
		
	return $val1.' '.$val2.$agoName;
}

function calcDateSmall($cDate, $lang = 'ru_ru') {
	 
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
	
	if($lang === 'en_en') {
		$yearName = 'y';
		$montName = 'm';
		$dayName = 'd';
		$hourName = 'h';
		$minuName = 'min';
		$secoName = 's';
		$agoName = ' ago';
	} else {
		$yearName = 'г';
		$montName = 'м';
		$dayName = 'д';
		$hourName = 'ч';
		$minuName = 'мин';
		$secoName = 'с';
		$agoName = ' назад';
	}
	
	if($years > 0) {
		$val1 = $year.$yearName;
	}
	if ($months > 0) {
		if($val1 == '') {
			$val1 = $months.$montName;
		} elseif ($val2 == '') {
			$val2 = $months.$montName;
		}
	}
	if ($days > 0) {
		if($val1 == '') {
			$val1 = $days.$dayName;
		} elseif ($val2 == '') {
			$val2 = $days.$dayName;
		}
	}
	if ($hours > 0) {
		if($val1 == '') {
			$val1 = $hours.$hourName;
		} elseif ($val2 == '') {
			$val2 = $hours.$hourName;
		}
	}
	if ($minutes > 0) {
		if($val1 == '') {
			$val1 = $minutes.$minuName;
		} elseif ($val2 == '') {
			$val2 = $minutes.$minuName;
		}
	}
	if ($seconds > 0) {
		if($val1 == '') {
			$val1 = $seconds.$secoName;
		} elseif ($val2 == '') {
			$val2 = $seconds.$secoName;
		}
	}
		
	return $val1.' '.$val2.$agoName;
}

function drawPost( $post, $showAnswerButton = false, $lang = 'ru_ru' ) {
	
	$langcl = new languages($lang);
	
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
			$delbutton = '<div val="'.$post->uid.'" class="icons deletepost clickable"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#trash"></use></svg></div>';
		}
    }
	
	$result = '<div class="row">';
	$result .= '<div class="col-lg-1"></div>';
	
	$result .= '<div class="col-lg-1 d-none d-lg-block text-center">';
	
	$result .= '<div style="display:inline-block;">';
		
	$result .= '<svg class="vote'.$up.'" val_target="'.$post->uid.'" val_data="1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-top"></use></svg>';
	$result .= '<span class="vote" val_target="'.$post->uid.'"  val_data="0">'.$post->rating.'</span>';
	$result .= '<svg class="vote'.$down.'" val_target="'.$post->uid.'"  val_data="-1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-bottom"></use></svg>';
	
	$result .= '</div>';

	$result .= '</div>';
	
	$result .= '<div class="col-12 col-lg-8 postframe">';
		
	$result .= '<h1>';
	if($post->blocked) {
		$title = '['.$langcl->dic['deleted'].']';
	} else {
		$title = $post->title;
	}
	if( $post->link != '') {
		$result .= '<a class="postlink" href="'.$post->link.'">'.$title.'</a>';
	} else {
		$result .= $title;
	}
	if( $post->oc ) {
		$result .= ' <span class="OC">OC</span>';
	}
	$result .= '</h1>';
		
	if(!$post->blocked) {
		if($post->nsfw && $user->ID == 0) {
			$result .= '<div>'.$langcl->dic['hidden'].'</div>';
		} elseif($post->nsfw) {
			$result .= '<div class="blured">'.updateContent( $post->content ).'</div>';
		} else {
			$result .= '<div>'.updateContent( $post->content ).'</div>';
		}
	} else {
	    $result .= updateContent( '~~'.$langcl->dic['post'].'~~' );
	}
	
	$result .= '</div>';
	$result .= '<div class="w-100"></div>';
	$result .= '<div class="col-lg-2"></div>';
	
	$result .= '<div class="col-12 col-lg-8 postBottom px-0">';
	
	$result .= '<div class="d-inline-flex d-lg-none ml-3 ml-lg-0 text-center">';
	$result .= '<svg class="vote'.$up.'" val_target="'.$post->uid.'" val_data="1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-top"></use></svg>';
	$result .= '<span class="vote" val_target="'.$post->uid.'"  val_data="0">'.$post->rating.'</span>';
	$result .= '<svg class="vote'.$down.'" val_target="'.$post->uid.'"  val_data="-1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-bottom"></use></svg>';
	$result .= '</div>';
	
	$result .= '<a id="comment"></a>';
	$result .= '<a href="/user/'.$post->author.'">'.$post->author.'</a> ';
	$result .= '<div class="d-inline-flex d-lg-none">'.calcDateSmall($post->date,$lang).'</div>';
	$result .= '<div class="d-none d-lg-inline-flex">'.calcDate($post->date,$lang).'</div>';
	$result .= ' <a href="'.$post->link.'#comment" title="'.$post->allCommentsCount.' '.$commentText.'">';
	$result .= '<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#comment-square"></use></svg></div> '.$post->allCommentsCount.'</a>'.$delbutton;
	
	$result .= '</div>';
	
	$result .= '<div class="w-100"></div>';
	
	$result .= '<div class="col-lg-2"></div>';
	
	$result .= '<div class="col-12 col-lg-8">';
	if( $showAnswerButton ) {
		$result .= '<div val_target="'.$post->uid.'"><button class="addComment" type="" class="btn btn-secondary">'.$langcl->dic['reply'].'</button></div>';
	}
	
	$result .= drawComments( $post->childs, $user, true, $lang );
	
	$result .= '</div>';
	
	$result .= '</div>';
	
	return $result;
		
}

function drawComments( $comments, $user = null, $drawChilds = true, $lang = 'ru_ru' ) {
	if( count( $comments ) == 0 ) {
		return '';
	}
	
	$langcl = new languages( $lang );

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
				$delbutton = '<div val="'.$comment->uid.'" class="icons deletepost clickable"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#trash"></use></svg></div>';
			}
        }

		//$result .= '<li class="comment">';
		$result .= '<li class="row">';
		
		//$result .= '<div class="commentVote">';
		$result .= '<div class="col-1 d-none d-lg-block text-center">';
		
		$result .= '<div style="display:inline-block;">';
		
	    $result .= '<svg class="vote'.$up.'" val_target="'.$comment->uid.'" val_data="1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-top"></use></svg>';
	    $result .= '<span class="vote" val_target="'.$comment->uid.'"  val_data="0">'.$comment->rating.'</span>';
	    $result .= '<svg class="vote'.$down.'" val_target="'.$comment->uid.'"  val_data="-1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-bottom"></use></svg>';
		
		$result .= '</div>';
		
	    $result .= '</div>';
		
		//$result .= '<div class="commentInfo">';
		$result .= '<div class="col-12 col-lg-'.($drawChilds ? '11' : '9').'">';
		$result .= '<div class="postBottom">';

		$result .= '<div class="d-inline-flex d-lg-none ml-3 ml-lg-0 text-center">';
			
		$result .= '<svg class="vote'.$up.'" val_target="'.$comment->uid.'" val_data="1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-top"></use></svg>';
		$result .= '<span class="vote" val_target="'.$comment->uid.'"  val_data="0">'.$comment->rating.'</span>';
		$result .= '<svg class="vote'.$down.'" val_target="'.$comment->uid.'"  val_data="-1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-bottom"></use></svg>';
		
		$result .= '</div>';
		
		$result .= '
		<a id="comment'.$comment->uid.'"></a><a href="'.$comment->link.'">
		<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#link-intact"></use></svg></div>
		</a>'.$langcl->dic['answer'].' <a href="/user/'.$comment->author.'">'.$comment->author.'</a>
		<div class="d-inline-flex d-lg-none">'.calcDateSmall($comment->date,$lang).'</div>
		<div class="d-none d-lg-inline-flex">'.calcDate($comment->date,$lang).'</div> '.$delbutton.'</div>';
		
		$result .= '<div class="postframe';
		if(!$comment->blocked) {
			if($comment->nsfw) {
				$result .= ' blured">';
			} else {
				$result .= '">';
			}
			if($comment->nsfw) {
				if($user == null) {
					$result .= $langcl->dic['hidden'].'</div>';
				} elseif($user->ID == 0) {
					$result .= $langcl->dic['hidden'].'</div>';
				} else {
					$result .= updateContent($comment->content).'</div>';
				}
			} else {
				$result .= updateContent($comment->content).'</div>';
			}
		} else {
		    $result .= '">'.updateContent( '~~'.$langcl->dic['comment'].'~~' ).'</div>';
		}
		
		$result .= '<div val_target="'.$comment->uid.'"><button class="addComment" type="" class="btn btn-secondary">'.$langcl->dic['reply'].'</button></div>';
		$result .= '</div>';
			
		$result .= '</li>';

		if($drawChilds) {
		    $result .= drawComments( $comment->childs, $user, $drawChilds, $lang );
		}
	}
	$result .= '</ul>';
	
	return $result;
}

function drawTopList($lang = 'ru_ru') {
	$langcl = new languages($lang);
    $bd = new bd();
	$result = '<h1 class="postlink" style="text-align:center;">'.$langcl->dic['top'].' 10</h1><ol>';
	$users = $bd->getTopTenUsers();
	for($i = 0; $i < count($users); $i++) {
		$curname = $users[$i]->name;
		$result .= '<li><a href="/user/'.$curname.'">'.$curname.'</a></li>';
	}
	$result .= '</ol>';
	
	return $result;
}

function updateContent( $content ) {
	$result = $content;

	$youtube = '/\[youtube\]\(https:\/\/youtu\.be\/(\S+)\)/mU';
	$youtube = '/\[youtube\]\((\S+)\)/mU';
	$matches = [];
	preg_match_all($youtube, $content, $matches, PREG_SET_ORDER );
	foreach($matches as $yvid) {
		$videolink = $yvid[1];
		$droper = [];
		$vidid = '';
		preg_match('/https:\/\/\S+v=(\S+)(&|$)/mU',$videolink,$droper);
		if(count($droper) != 0) {
			$vidid = $droper[1];
		} else {
			preg_match('/https:\/\/\S+be\/(\S+)(&|$)/mU',$videolink,$droper);
			if(count($droper) != 0) {
				$vidid = $droper[1];
			}
		}
		$marker = '<div class="yumarker" style="width:100%;height: 0;opacity: 0;">123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890</div>';
		$result = str_replace($yvid[0],$marker.'<div class="youtubevideo"><iframe src="https://www.youtube.com/embed/'.$vidid.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>', $result);
	}
	
	
	$vidos = '/\[video\]\((\S+\.mp4)\)/mU';
	$matches = [];
	preg_match_all($vidos, $content, $matches, PREG_SET_ORDER );
	foreach($matches as $video) {
	    $result = str_replace( $video[0], '<video controls style="max-width:100%;"><source src="'.$video[1].'" type="video/mp4"></video>', $result);
	}
	
	$markdown1 = '/!\[(.+)\]\((\S+)\)/mU';
	$matches = [];
	preg_match_all($markdown1, $content, $matches, PREG_SET_ORDER );
	foreach ($matches as $pic) {
	    $result = str_replace( $pic[0], '<div><img style="max-width:100%;" src="'.$pic[2].'" alt="'.$pic[1].'" /></div>', $result);
	}
	
	$markdown2 = '/\[(.+)\]\((\S+)\)/mU';
	$matches = [];
	preg_match_all($markdown2, $content, $matches, PREG_SET_ORDER );
	foreach($matches as $link) {
	    $result = str_replace( $link[0], '<a target="blank_" href="'.$link[2].'">'.$link[1].'</a>', $result);
	}
	
	$markdown3 = '/~~(.+)~~/mU';
	$matches = [];
	preg_match_all($markdown3, $content, $matches, PREG_SET_ORDER );
	foreach($matches as $cross) {
	    $result = str_replace( $cross[0], '<s>'.$cross[1].'</s>', $result);
	}
	
	$markdown4 = '/\*\*(.+)\*\*/mU';
	$matches = [];
	preg_match_all($markdown4, $content, $matches, PREG_SET_ORDER );
	foreach($matches as $bold) {
	    $result = str_replace( $bold[0], '<strong>'.$bold[1].'</strong>', $result);
	}

	$tag = '/(^|\s)#(\S+)[\s$\.,!?]/mU';
	$matches = [];
	preg_match_all($tag, $content, $matches, PREG_SET_ORDER );
	foreach($matches as $tag) {
		$result = str_replace( $tag[0], '<a href="/hashtag/'.$tag[2].'" />'.$tag[0].'</a>', $result);
	}
	
	$user = '/(^|\s)@(\S+)[\s$\.,!?]/mU';
	$matches = [];
	preg_match_all($user, $content, $matches, PREG_SET_ORDER );
	foreach($matches as $userlink) {
		$result = str_replace( $userlink[0], '<a href="/user/'.$userlink[2].'" />'.$userlink[0].'</a>', $result);
	}
	
	$result = nl2br($result);

	return $result;
}

function drawTopLine($lang = 'ru_ru') {
	$langcl = new languages($lang);
    $user = getUserForDraw();
    $bd = new bd();
    $answer = '';
	if($user != null) {
	    $unread = $bd->getUnreadCount($user);
		$answer .= '
		<div class="d-lg-none">
			<div class="logoutForm nav-item">
				<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#account-logout"></use></svg></div>
			</div>
			<div class="nav-item">
				Привет, <a href="/user/'.$user->name.'">'.$user->name.'</a>
			</div>
			<div class="nav-item">
				<div style="align-self: flex-start;">
					<div class="icons">
						<svg viewBox="0 0 8 8">
							<use xlink:href="/images/sprite.svg#star"></use>
						</svg>
					</div>
					'.$user->rating.'
				</div>
			</div>
			<div class="nav-item">
				<a href="/unread/" title="'.$unread.' '.declOfNum($unread,array('непрочитанное','непрочитанных','непрочитанных')).'">
					<div style="align-self: center;">
						<div class="icons">
							<svg viewBox="0 0 8 8">
								<use xlink:href="/images/sprite.svg#chat"></use>
							</svg>
						</div>
						'.$unread.'
					</div>
				</a>
			</div>
			<div class="nav-item">
				<a href="/registration/?ref='.$bd->getRefCode($user).'">'.$langcl->dic['reflink'].'</a>
			</div>';
	} else {
		$answer .= '
		<div class="d-lg-none">
		<div class="loginForm nav-item">
		    <input type="text" name="login" />
		    <input type="password" name="password" />
			<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#account-login"></use></svg></div>
		    <a href="/registration/">'.$langcl->dic['registr'].'</a>
		</div>';
   	}
    $usersCount = $bd->getUsersCount();
    if($usersCount != null) {
        $answer .= '<div class="nav-item"><div style="align-self: flex-start;"><div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#people"></use></svg></div>'.$usersCount.'</div></div>';
    }
    return '</div>'.$answer;
}

function drawUserMenu($lang = 'ru_ru') {

	$langcl = new languages($lang);

    $user = getUserForDraw();
    $bd = new bd();
    $answer = '';
	if($user != null) {
	    $unread = $bd->getUnreadCount($user);
		$answer .= '<div><a href="/user/'.$user->name.'/">'.$user->name.'</a></div>';
		$answer .= '<div>'.$langcl->dic['rating'].' '.$user->rating.'</div>';
		$answer .= '<div><a href="/unread/">'.$langcl->dic['messages'];
		if($unread > 0 ) {
			$answer .= '<span class="unread_count">'.$unread.'</span>';
		}
		$answer .= '</a></div>';
		$answer .= '<div class="logoutForm"><div class="formAction">'.$langcl->dic['signout'].'</div></div>';
		
	} else {
		$answer .= '
		<div class="loginForm">
			<div>
				<input type="text" name="login" />
			</div>
			<div>
				<input type="password" name="password" />
			</div>
			<div class="formAction">
				'.$langcl->dic['signin'].'
			</div>
			<div>
				<a href="/registration/">'.$langcl->dic['registr'].'</a>
			</div>
		</div>';
   	}
	
	$usersCount = $bd->getUsersCount();
    if($usersCount != null) {
        $answer .= '<div>'.$langcl->dic['users'].' '.$usersCount.'</div>';
    }

    return $answer;
}

function drawNavBar($lang = 'ru_ru') {
	
	$langcl = new languages($lang);
		
	$result .= '';
	$result .= '<a class="nav-item nav-link" href="/rules/">'.$langcl->dic['rules'].'</a>';
	$result .= '<a class="nav-item nav-link" href="/add/">'.$langcl->dic['addpost'].'</a>';
	$result .= '<a class="nav-item nav-link" href="/new/">'.$langcl->dic['newposts'].'</a>';
	$result .= '<a class="nav-item nav-link" href="/top/">'.$langcl->dic['topposts'].'</a>';
	$result .= '<a class="nav-item nav-link" href="/hot/">'.$langcl->dic['hotposts'].'</a>';

	return $result;
}

function drawPageButton($pageCount, $current) {
	$maxPageCount = 7;
	
    $uri = $_SERVER['REQUEST_URI'];
    $uri = str_replace('&str='.$current,'',$uri);
    $uri = str_replace('str='.$current,'',$uri);
    $pagelink = '?';
    if(strpos($uri,'?')) {
        $pagelink = stristr($uri,'?',false);
    }
    
    $prev = $current-1;
    $next = $current+1;
    
	$str = '<nav aria-label="Pages" style="justify-content: center;">';
	$str .= '<ul class="pagination">';
	$str .= '<li class="page-item"><a class="page-link myPageItem" href="'.$pagelink.'">1<<</a></li>';
	if($prev < 1) {
	    //$str .= '<li class="page-item"><a class="page-link myPageItem disabled" >Предыдущая</a></li>';
	} else {
	    $str .= '<li class="page-item"><a class="page-link myPageItem" href="'.$pagelink.'&str='.$prev.'"><<</a></li>';
	}
	if($pageCount > $maxPageCount) {
	    $start = $current-4;
	    if($start < 1) {
	        $start = 1;
	    }
	    $end = $start+$maxPageCount-1;
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