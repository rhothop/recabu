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

function calcDateSmall($cDate) {
	 
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
	
	$yearName = 'г';
	$montName = 'м';
	$dayName = 'д';
	$hourName = 'ч';
	$minuName = 'мин';
	$secoName = 'с';
	
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
		$title = '[Пост удален]';
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
			$result .= '<div>Пост скрыт</div>';
		} elseif($post->nsfw) {
			$result .= '<div class="blured">'.updateContent( $post->content ).'</div>';
		} else {
			$result .= '<div>'.updateContent( $post->content ).'</div>';
		}
	} else {
	    $result .= updateContent( '~~пост~~' );
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
	$result .= '<div class="d-inline-flex d-lg-none">'.calcDateSmall($post->date).'</div>';
	$result .= '<div class="d-none d-lg-inline-flex">'.calcDate($post->date).'</div>';
	$result .= ' <a href="'.$post->link.'#comment" title="'.$post->allCommentsCount.' '.$commentText.'">';
	$result .= '<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#comment-square"></use></svg></div> '.$post->allCommentsCount.'</a>'.$delbutton;
	
	$result .= '</div>';
	
	$result .= '<div class="w-100"></div>';
	
	$result .= '<div class="col-lg-2"></div>';
	
	$result .= '<div class="col-12 col-lg-8">';
	if( $showAnswerButton ) {
		$result .= '<div val_target="'.$post->uid.'"><button class="addComment" type="" class="btn btn-secondary">Ответить</button></div>';
	}
	
	$result .= drawComments( $post->childs, $user, true );
	
	$result .= '</div>';
	
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
		$result .= '<div class="col-12 col-lg-11">';
		$result .= '<div class="postBottom">';

		$result .= '<div class="d-inline-flex d-lg-none ml-3 ml-lg-0 text-center">';
			
		$result .= '<svg class="vote'.$up.'" val_target="'.$comment->uid.'" val_data="1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-top"></use></svg>';
		$result .= '<span class="vote" val_target="'.$comment->uid.'"  val_data="0">'.$comment->rating.'</span>';
		$result .= '<svg class="vote'.$down.'" val_target="'.$comment->uid.'"  val_data="-1" viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#caret-bottom"></use></svg>';
		
		$result .= '</div>';
		
		$result .= '
		<a id="comment'.$comment->uid.'"></a><a href="'.$comment->link.'">
		<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#link-intact"></use></svg></div>
		</a>Ответ <a href="/user/'.$comment->author.'">'.$comment->author.'</a>
		<div class="d-inline-flex d-lg-none">'.calcDateSmall($comment->date).'</div>
		<div class="d-none d-lg-inline-flex">'.calcDate($comment->date).'</div> '.$delbutton.'</div>';
		
		$result .= '<div class="postframe';
		if(!$comment->blocked) {
			if($comment->nsfw) {
				$result .= ' blured">';
			} else {
				$result .= '">';
			}
			if($comment->nsfw) {
				if($user == null) {
					$result .= 'Пост скрыт</div>';
				} elseif($user->ID == 0) {
					$result .= 'Пост скрыт</div>';
				} else {
					$result .= updateContent($comment->content).'</div>';
				}
			} else {
				$result .= updateContent($comment->content).'</div>';
			}
		} else {
		    $result .= '">'.updateContent( '~~коммент~~' ).'</div>';
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

function drawTopList() {
    $bd = new bd();
	$result = '<h1 class="postlink" style="text-align:center;">ТОП 10</h1><ol>';
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
		$result = str_replace($yvid[0],'<div class="youtubevideo"><iframe src="https://www.youtube.com/embed/'.$vidid.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>', $result);
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

function drawTopLine() {
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
				<a href="/registration/?ref='.$bd->getRefCode($user).'">Ссылка для приглашения</a>
			</div>';
	} else {
		$answer .= '
		<div class="d-lg-none">
		<div class="loginForm nav-item">
		    <input type="text" name="login" />
		    <input type="password" name="password" />
			<div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#account-login"></use></svg></div>
		    <a href="/registration/">Зарегистрироваться</a>
		</div>';
   	}
    $usersCount = $bd->getUsersCount();
    if($usersCount != null) {
        $answer .= '<div class="nav-item"><div style="align-self: flex-start;"><div class="icons"><svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#people"></use></svg></div>'.$usersCount.'</div></div>';
    }
    return '</div>'.$answer;
}

function drawUserMenu() {
    $user = getUserForDraw();
    $bd = new bd();
    $answer = '';
	if($user != null) {
	    $unread = $bd->getUnreadCount($user);
		$answer .= '<div><a href="/user/'.$user->name.'/">'.$user->name.'</a></div>';
		$answer .= '<div>РЕЙТИНГ '.$user->rating.'</div>';
		$answer .= '<div><a href="/unread/">ОТВЕТЫ';
		if($unread > 0 ) {
			$answer .= '<span class="unread_count">'.$unread.'</span>';
		}
		$answer .= '</a></div>';
		$answer .= '<div class="logoutForm"><div class="formAction">ВЫЙТИ</div></div>';
		
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
				ВОЙТИ
			</div>
			<div>
				<a href="/registration/">Зарегистрироваться</a>
			</div>
		</div>';
   	}
	
	$usersCount = $bd->getUsersCount();
    if($usersCount != null) {
        $answer .= '<div>Пользователей '.$usersCount.'</div>';
    }

    return $answer;
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