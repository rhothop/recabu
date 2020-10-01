<?php
require_once('./postObject.php');
require_once('./userObject.php');

class bd extends mysqli {
	private $server = 'localhost';
	private $base = 'u1096751_pikabu';
	private $login = 'u1096751_pikabu';
	private $psw = 'pikabu2020';
	
	public function __construct() {
        parent::__construct($this->server, $this->login, $this->psw, $this->base);
		if (mysqli_connect_error()) {
            die('Ошибка подключения (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }
        $this->set_charset("utf8");
	}

    function getYourVote($postid,$userid) {
        $queryText = 'SELECT `val` FROM `rating` WHERE `post`='.$postid.' AND `author` = '.$userid;
        if($query = $this->query($queryText)) {
            while($cur = $query->fetch_object()) {
                return $cur->val;
            }
        }
    }
    
    function getMainPostByCommentID($id) {
        if($query = $this->query('SELECT * FROM `posts` WHERE `_id` = '.$id)) {
            while($cur = $query->fetch_object()) {
                if($cur->parent == 0) {
                    return new postObject($cur->_id, '', '', '', '', $cur->name, false, false, 0);
                } else {
                    return $this->getMainPostByCommentID($cur->parent);
                }
            }
        }
    }

	function regNewUser( $login, $psw, $invite = '') {
	    $refEnabled = false;
	    if(!isset($login) || !isset($psw)) {
	        return 'Bad bad boy';
	    }
	    if($refEnabled) {
	        if($query = $this->query('SELECT * FROM `users` WHERE `invite` = "'.$invite.'"')) {
	            if($query->num_rows == 0) {
	                return 'Нет такого кода приглашения';
	            } else {
	                if($query = $this->query('SELECT * FROM `users` WHERE `ref` = "'.$invite.'"')) {
	                    if($query->num_rows > 0) {
	                        return 'Этот кода приглашения уже использован';
	                    }
	                } else {
	                    return 'Непредвиденная ошибка';
	                }
	            }
	        } else {
	            return 'Непредвиденная ошибка';
	        }
	    }
	    $hash = password_hash($psw,PASSWORD_DEFAULT);
	    $queryText = 'SELECT * FROM `users` WHERE `name` = "'.$login.'"';
	    if($query = $this->query($queryText)) {
	        while($cur = $query->fetch_object()) {
	            return 'Пользователь с таким логином уже зарегистрирован';
	        }
	    } else {
	        return $this->error;
	    }
	    $queryText = 'INSERT INTO `users`(`name`, `psw`, `invite`) VALUES ("'.$login.'","'.$hash.'", "'.$invite.'")';
	    if($this->query($queryText)) {
	        return 'good';
	    } else {
	        return $this->error;
	    }
	}
	
	function login( $login, $psw ) {
	    $result = '';
	    $hash = password_hash($psw,PASSWORD_DEFAULT);
		$queryText = 'SELECT * FROM `users` WHERE `name` = "'.$login.'"';// AND `psw` = "'.$hash.'"';
		if($query = $this->query( $queryText ) ) {
			while($cur = $query->fetch_object()) {
			    if(password_verify($psw,$cur->psw)) {
			        $token = md5($login.time());
    				$this->query('INSERT INTO `sessions`( `user`, `token`) VALUES ('.$cur->_id.',"'.$token.'")');
    				//setcookie ( 'auth', $token, 0, '/', '', true, true, array('SameSite'=>'strict') );
    				setcookie('auth', $token, [
                        'path' => '/',
                        'domain' => 'recabu.cf',
                        'secure' => true,
                        'httponly' => true,
                        'samesite' => 'lax',
                    ]);
    				return $token;
			    }
			}
		}
		
		return $result;
	}
	
	function getUsersCount() {
	    $queryText = 'SELECT
	        SUM(1) AS count
	    FROM
	        `users`';
	   if($query = $this->query($queryText)) {
	       while($cur = $query->fetch_object()) {
	           return $cur->count;
	       }
	   }
	}
	
	function getUnreadCount($user) {
		$count = 0;
	    $queryText = 'SELECT IFNULL(SUM(1),0) AS countnum FROM `unreaded` WHERE `unread` AND `target` = '.$user->ID;
	    if($query = $this->query($queryText)) {
	        while($cur = $query->fetch_object()){
	            $count = $cur->countnum;
	        }
	    } else {
	        $count = 0;
	    }
	    return $count;
	}
	
	function getUnreadPage($token, $str = 1) {
	    $arr = array();
	    $user = $this->getUserByToken( $token );
		if($user->ID == 0) {
			return $arr;
		}
        $queryText = 'SELECT
            `posts`.`_id` AS postID,
            `posts`.`name` AS link,
			`posts`.`title` AS postTitle, 
			`posts`.`content` AS postContent, 
			`posts`.`nsfw` AS NSFW,
			`posts`.`oc` AS OC,
			`posts`.`date` AS postDate,
			`posts`.`blocked` AS ban,
			`users`.`name` AS author,
			IFNULL(SUM(`rating`.`val`),0) AS rating,
			`unreaded`.`unread` AS new
        FROM
            `unreaded`
        LEFT JOIN
            `posts`
        ON
            `unreaded`.`postID` = `posts`.`_id`
        LEFT JOIN
            `users`
        ON
            `posts`.`author`=`users`.`_id`
        LEFT JOIN
	        `rating`
	    ON
	        `posts`.`_id` = `rating`.`post`
        WHERE
            `unreaded`.`target` = '.$user->ID.'
		GROUP BY
			`posts`.`_id`
        ORDER BY
            `postID` DESC
		LIMIT 10
		OFFSET '.($str-1)*10;
        if($query = $this->query($queryText)) {
            while($cur = $query->fetch_object()) {
                if($cur->link === '') {
                    $parent = $this->getMainPostByCommentID($cur->postID);
                    $link = str_replace('/posts/','',$parent->link).'#comment'.$cur->postID;
                } else {
                    $link = $cur->link;
                }
                $post = new postObject($cur->postID, $cur->postTitle, $cur->postContent, $cur->author, $cur->postDate, $link, $cur->NSFW, $cur->OC, $cur->rating, $cur->ban);
                $arr[] = $post;
            }
        }
        return $arr;
	}
	function getAnswersCount($token) {
	    $user = $this->getUserByToken( $token );
		if($user->ID == 0) {
			return 1;
		}
        $queryText = 'SELECT
            IFNULL(SUM(1),0) AS count
        FROM
            `unreaded`
        WHERE
            `target` = '.$user->ID;
        if($query = $this->query($queryText)) {
            while($cur = $query->fetch_object()) {
                return ceil($cur->count/10);
            }
        }
        return 1;
	}
	
	
	function getNewpostPageCount() {
		$queryText = 'SELECT
			SUM(1) AS Count
		FROM
			`posts`
		WHERE
			`parent` = 0';
		$count = 0;
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$count = $cur->Count;
			}
		}
		if($count == null) {
			return 1;
		} else {
			return ceil($count/10);
		}
	}
	function getToppostPageCount($date) {
		$queryText = 'SELECT
			SUM(1) AS Count
		FROM
			`posts`
		WHERE
			`parent` = 0
		AND
		    `date` >= "'.date('Y-m-d 00:00:00',strtotime($date)).'"
		AND
		    `date` <= "'.date('Y-m-d 23:59:59',strtotime($date)).'"';
		$count = 0;
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$count = $cur->Count;
			}
		}
		if($count == null) {
			return 1;
		} else {
			return ceil($count/10);
		}
	}
	function getUserPageCount($user) {
	    $queryText = 'SELECT
	        SUM(1) AS Count
		FROM 
			`posts` 
		LEFT JOIN 
			`users` 
		ON 
			`posts`.`author` = `users`.`_id` 
		WHERE 
			`posts`.`parent` = 0
		AND
		    `users`.`name` = "'.$user.'"';
		$count = 0;
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$count = $cur->Count;
			}
		}
		if($count == null) {
			return 1;
		} else {
			return ceil($count/10);
		}
	}
	
	function getUserByToken( $token ) {
		$cUser = new userObject(0,'',0);
		$queryText = 'SELECT
			`users`.*, 
			IFNULL(SUM(`rating`.`val`),0) AS val 
		FROM 
			`sessions`
		LEFT JOIN
			`users`
		ON
			`sessions`.`user` = `users`.`_id`
        LEFT JOIN
        	`posts`
        ON
        	`users`.`_id` = `posts`.`author`
		LEFT JOIN
			`rating`
		ON
			`posts`.`_id` = `rating`.`post`
		WHERE
			`sessions`.`token` = "'.$token.'"
		GROUP BY
			`users`.`_id`';
		$query = $this->query($queryText);
		while($cur = $query->fetch_object()) {
			$cUser = new userObject($cur->_id, $cur->name, $cur->val);
			break;
		}
		return $cUser;
	}
	
	function votePost($target, $value) {
		$valForVote = 0;
		if($value > 0) {
			$valForVote = 1;
		} elseif ($value < 0) {
			$valForVote = -1;
		}
		$user = $this->getUserByToken( $_COOKIE['auth'] );
		if($user->ID == 0) {
			return '{"result":false,"content":"","msg":"Необходимо авторизоваться"}';
		}
		if($query = $this->query('SELECT * FROM `rating` WHERE `post` = '.$target.' AND `author` = '.$user->ID)) {
			if($query->num_rows > 0) {
				while($cur = $query->fetch_object()) {
					$this->query('UPDATE `rating` SET `val`='.$valForVote.' WHERE `_id` = '.$cur->_id);
				}
			} else {
				$this->query('INSERT INTO `rating`(`post`, `author`, `val`) VALUES ('.$target.','.$user->ID.','.$valForVote.')');
			}
			return '{"result":true,"content":"","msg":""}';
		} else {
			return '{"result":false,"content":"","msg":"'.$this->error.'"}';
		}
		return '{"result":true,"content":"","msg":"Необходимо авторизоваться"}';
	}

	function getPost( $name ) {
		$posts = array();
		$queryText = 'SELECT 
			`posts`.`_id` AS postID,
			`posts`.`name` AS link,
			`posts`.`title` AS postTitle, 
			`posts`.`content` AS postContent, 
			`posts`.`nsfw` AS NSFW,
			`posts`.`oc` AS OC,
			`posts`.`date` AS postDate,
			`posts`.`blocked` AS ban,
			`users`.`name` AS author,
			IFNULL(SUM(`rating`.`val`),0) AS rating
		FROM 
			`posts` 
		LEFT JOIN 
			`users` 
		ON 
			`posts`.`author` = `users`.`_id`
	    LEFT JOIN
	        `rating`
	    ON
	        `posts`.`_id` = `rating`.`post`
		WHERE 
			`posts`.`name` = "'.$name.'"
		GROUP BY
			`posts`.`_id`';
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$post = new postObject($cur->postID, $cur->postTitle, $cur->postContent, $cur->author, $cur->postDate, $name, $cur->NSFW, $cur->OC, $cur->rating, $cur->ban);
				$post->allCommentsCount = $this->getChildsCount($post->uid);
				$childs = $this->getChilds($post->uid,$name);
				if(isset($_COOKIE['auth'])) {
			        $user = $this->getUserByToken($_COOKIE['auth']);
			        if($user->ID > 0) {
			            $this->query('UPDATE `unreaded` SET `unread`= 0 WHERE `postID` = '.$cur->postID.' AND `target` = '.$user->ID);
			        }
			    }
				foreach($childs as $child) {
					$post->childs[] = $child;
				}
				$posts[] = $post;
				break;
			}
		}
		return $posts;
	}
	
	function getChilds($id,$parentName = '') {
		$childs = array();
		$queryText = 'SELECT 
			`posts`.`_id` AS postID, 
			`posts`.`title` AS postTitle, 
			`posts`.`content` AS postContent, 
			`posts`.`nsfw` AS NSFW, 
			`posts`.`date` AS postDate,
			`posts`.`blocked` AS ban,
			`users`.`name` AS author,
			IFNULL(SUM(`rating`.`val`),0) AS rating
		FROM 
			`posts` 
		LEFT JOIN 
			`users` 
		ON 
			`posts`.`author` = `users`.`_id`
		LEFT JOIN
		    `rating`
		ON
		    `posts`.`_id` = `rating`.`post`
		WHERE 
			`posts`.`parent` = '.$id.'
		GROUP BY
			`posts`.`_id`
		ORDER BY
			rating DESC';
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
			    if(isset($_COOKIE['auth'])) {
			        $user = $this->getUserByToken($_COOKIE['auth']);
			        if($user->ID > 0) {
			            $this->query('UPDATE `unreaded` SET `unread`= 0 WHERE `postID` = '.$cur->postID.' AND `target` = '.$user->ID);
			        }
			    }
				$post = new postObject($cur->postID, $cur->postTitle, $cur->postContent, $cur->author, $cur->postDate, $parentName, $cur->NSFW, false, $cur->rating, $cur->ban, $id);
				$childs1 = $this->getChilds($post->uid, $parentName);
				foreach($childs1 as $child) {
					$post->childs[] = $child;
				}
				$childs[] = $post;
			}
		}
		
		return $childs;
	}
	
	function getNewPosts( $str = 1 ) {
		$posts = array();
		$queryText = 'SELECT
			`posts`.`name` AS link,
			`posts`.`_id` AS postID, 
			`posts`.`title` AS postTitle, 
			`posts`.`content` AS postContent, 
			`posts`.`nsfw` AS NSFW,
			`posts`.`oc` AS OC,
			`posts`.`date` AS postDate,
			`posts`.`blocked` AS ban,
			`users`.`name` AS author,
			IFNULL(SUM(`rating`.`val`),0) AS rating
		FROM 
			`posts` 
		LEFT JOIN 
			`users` 
		ON 
			`posts`.`author` = `users`.`_id`
	    LEFT JOIN
	        `rating`
	    ON
	        `posts`.`_id` = `rating`.`post`
		WHERE 
			`posts`.`parent` = 0
		GROUP BY
			`posts`.`_id`
		ORDER BY
			`posts`.`date` DESC
		LIMIT 10
		OFFSET '.($str-1)*10;
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$post = new postObject($cur->postID, $cur->postTitle, $cur->postContent, $cur->author, $cur->postDate, $cur->link, $cur->NSFW, $cur->OC, $cur->rating, $cur->ban);
				//$post->rating = $this->getRating($post->uid);
				$post->allCommentsCount = $this->getChildsCount($post->uid);
				$posts[] = $post;
			}
		}
		return $posts;
	}
	
	function getTopPosts( $str = 1, $date = null ) {
		if( $date === null) {
			$startDate = date('Y-m-d 00:00:00');
			$endDate = date('Y-m-d 23:59:59');
		} else {
			$startDate = date('Y-m-d 00:00:00',strtotime($date));
			$endDate = date('Y-m-d 23:59:59',strtotime($date));
		}
		$posts = array();
		$queryText = 'SELECT
			`posts`.`name` AS link,
			`posts`.`_id` AS postID, 
			`posts`.`title` AS postTitle, 
			`posts`.`content` AS postContent, 
			`posts`.`nsfw` AS NSFW,
			`posts`.`oc` AS OC,
			`posts`.`date` AS postDate,
			`posts`.`blocked` AS ban,
			`users`.`name` AS author,
            IFNULL(SUM(`rating`.`val`),0) AS rating
		FROM 
			`posts`
		LEFT JOIN 
			`users` 
		ON 
			`posts`.`author` = `users`.`_id`
        LEFT JOIN
        	`rating`
        ON
        	`posts`.`_id` = `rating`.`post`
		WHERE 
			`posts`.`parent` = 0
		AND
			`posts`.`date` >= "'.$startDate.'"
		AND
			`posts`.`date` <= "'.$endDate.'"
		GROUP BY
			`posts`.`_id`
        ORDER BY
        	rating DESC
		LIMIT 10
		OFFSET '.($str-1)*10;
//SUM(`rating`.`val`) DESC
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$post = new postObject($cur->postID, $cur->postTitle, $cur->postContent, $cur->author, $cur->postDate, $cur->link, $cur->NSFW, $cur->OC, $cur->rating, $cur->ban);
				$post->allCommentsCount = $this->getChildsCount($post->uid);
				$posts[] = $post;
			}
		}
		return $posts;
	}
	
	function getTagPosts( $hashtag, $str = 1) {
		$posts = array();
		$queryText = 'SELECT
			`posts`.`name` AS link,
			`posts`.`_id` AS postID, 
			`posts`.`title` AS postTitle, 
			`posts`.`content` AS postContent, 
			`posts`.`nsfw` AS NSFW,
			`posts`.`oc` AS OC,
			`posts`.`date` AS postDate,
			`posts`.`blocked` AS ban,
			`users`.`name` AS author,
			IFNULL(SUM(`rating`.`val`),0) AS rating
		FROM 
			`posts` 
		LEFT JOIN 
			`users` 
		ON 
			`posts`.`author` = `users`.`_id`
		LEFT JOIN
		    `rating`
		ON
		    `posts`.`_id` = `rating`.`post`
		WHERE 
			`posts`.`parent` = 0
		AND
		    (`posts`.`title` LIKE "%#'.$hashtag.'%" OR `posts`.`content` LIKE "%#'.$hashtag.'%")
		GROUP BY
			`posts`.`_id`
		ORDER BY
			`posts`.`date` DESC
		LIMIT 10
		OFFSET '.($str-1)*10;
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$post = new postObject($cur->postID, $cur->postTitle, $cur->postContent, $cur->author, $cur->postDate, $cur->link, $cur->NSFW, $cur->OC, $cur->rating, $cur->ban);
				//$post->rating = $this->getRating($post->uid);
				$post->allCommentsCount = $this->getChildsCount($post->uid);
				$posts[] = $post;
			}
		}
		return $posts;	}
		
	function getUserPosts( $username, $str = 1) {
		$posts = array();
		$queryText = 'SELECT
			`posts`.`name` AS link,
			`posts`.`_id` AS postID, 
			`posts`.`title` AS postTitle, 
			`posts`.`content` AS postContent, 
			`posts`.`nsfw` AS NSFW,
			`posts`.`oc` AS OC,
			`posts`.`date` AS postDate,
			`posts`.`blocked` AS ban,
			`users`.`name` AS author,
			IFNULL(SUM(`rating`.`val`),0) AS rating
		FROM 
			`posts` 
		LEFT JOIN 
			`users` 
		ON 
			`posts`.`author` = `users`.`_id`
		LEFT JOIN
		    `rating`
		ON
		    `posts`.`_id` = `rating`.`post`
		WHERE 
			`posts`.`parent` = 0
		AND
		    `users`.`name` = "'.$username.'"
		GROUP BY
			`posts`.`_id`
		ORDER BY
			`posts`.`date` DESC
		LIMIT 10
		OFFSET '.($str-1)*10;
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$post = new postObject($cur->postID, $cur->postTitle, $cur->postContent, $cur->author, $cur->postDate, $cur->link, $cur->NSFW, $cur->OC, $cur->rating, $cur->ban);
				//$post->rating = $this->getRating($post->uid);
				$post->allCommentsCount = $this->getChildsCount($post->uid);
				$posts[] = $post;
			}
		}
		return $posts;
	}	
	
	function getHotPosts($str = 1) {
		$posts = array();
		$queryText = 'SELECT
			`posts`.`name` AS link,
			`posts`.`_id` AS postID, 
			`posts`.`title` AS postTitle, 
			`posts`.`content` AS postContent, 
			`posts`.`nsfw` AS NSFW,
			`posts`.`oc` AS OC,
			`posts`.`date` AS postDate,
			`posts`.`blocked` AS ban,
			`users`.`name` AS author,
            IFNULL(SUM(`rating`.`val`),0) AS rating,
			IFNULL(SUM(`rating`.`val`),0) / TIME_TO_SEC(TIMEDIFF(NOW(),`posts`.`date`)) AS hot
		FROM 
			`posts`
		LEFT JOIN 
			`users` 
		ON 
			`posts`.`author` = `users`.`_id`
        LEFT JOIN
        	`rating`
        ON
        	`posts`.`_id` = `rating`.`post`
		WHERE 
			`posts`.`parent` = 0
		GROUP BY
			`posts`.`_id`
        ORDER BY
        	hot DESC,
			postDate DESC
		LIMIT 10
		OFFSET '.($str-1)*10;

		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$post = new postObject($cur->postID, $cur->postTitle, $cur->postContent, $cur->author, $cur->postDate, $cur->link, $cur->NSFW, $cur->OC, $cur->rating, $cur->ban);
				//$post->rating = $this->getRating($post->uid);
				$post->allCommentsCount = $this->getChildsCount($post->uid);
				$posts[] = $post;
			}
		}
		return $posts;
	}
		
	function addPost( $cTitle, $cContent, $cNsfw, $cOc, $cParent ) {
		
		$user = $this->getUserByToken( $_COOKIE['auth'] );
		if($user->ID == 0) {
			return '{"result":false,"content":"","msg":"Только зарегистрированные пользователи могут это делать"}';
		}
		if($user->rating < -50 && $cParent == 0) {
			return '{"result":false,"content":"","msg":"Ваш рейтинг слишком мал для постов!"}';;
		}
		if($user->rating < -100 && $cParent != 0) {
			return '{"result":false,"content":"","msg":"Пользователи забанили Вас по рейтингу"}';;
		}
		
		if( $cParent == 0 ) {
			$name = $this->translit_sef( $cTitle );
		} else {
			$name = '';
		}
		$title = $this->adopt_title( $cTitle );
		
    	$html = '/<.+>/';
    	$matches = array();
    	preg_match_all($html, $cContent, $matches, PREG_PATTERN_ORDER );
    	foreach( $matches[0] as $match ) {
    		$cContent = str_replace( $match, '', $cContent);
    	}
		
		if($cContent == '') {
			return '{"result":false,"content":"","msg":"Нельзя писать пустые посты"}';
		}
		if($cParent == 0 && $cTitle == '') {
			return '{"result":false,"content":"","msg":"У поста обязательно должен быть заголовок"}';
		}
		
		$cContent = $this->real_escape_string($cContent);
		$queryText = 'INSERT INTO 
			`posts`
			(`name`, `title`, `content`, `parent`, `author`, `nsfw`, `oc`)
		VALUES 
			("'.$name.'","'.$title.'","'.$cContent.'",'.$cParent.','.$user->ID.','.$cNsfw.','.$cOc.')';
		if( $query = $this->query( $queryText ) ) {
		    $msgID = $this->insert_id;
		    if($cParent != 0) {
		        if($query1 = $this->query('SELECT `author` FROM `posts` WHERE `_id` = '.$cParent)) {
		            while($cur = $query1->fetch_object()) {
		                $this->query('INSERT INTO `unreaded`(`postID`, `target`, `unread`) VALUES ('.$msgID.','.$cur->author.',1)');
		            }
		        } else {
		            //var_dump($this->error);
		        }
		    }
            $this->query('INSERT INTO `rating`(`post`, `author`, `val`) VALUES ('.$msgID.','.$user->ID.',1)');
            
        	$user_link = '/(^|\s)@\S+(\s|$)/';
        	$matches = array();
        	preg_match_all($user_link, $cContent, $matches, PREG_PATTERN_ORDER );
        	//var_dump($matches);
        	foreach( $matches[0] as $match ) {
        	    $username = str_replace('@','',$match);
        	    $username = str_replace(',','',$username);
        	    $username = str_replace('.','',$username);

        	    $username = trim($username);
        		if($query = $this->query('SELECT * FROM `users` WHERE `name` = "'.$username.'"')) {
        		    while($cur = $query->fetch_object()) {
        		        $this->query('INSERT INTO `unreaded`(`postID`, `target`, `unread`) VALUES ('.$msgID.','.$cur->_id.',1)');
        		    }
        		}
        	}
            
			return '{"result":true,"content":"/posts/'.$name.'","msg":""}';
		} else {
		    return '{"result":false,"content":"","msg":"'.$this->error.'"}';
			//return $this->error.'<br />'.$queryText;
		}
	}
	
	function delPost($id, $token) {
		$user = $this->getUserByToken( $token );
		if($user != null) {
			if($this->query('UPDATE `posts` SET `blocked` = 1 WHERE `_id` = '.$id.' AND `author` = '.$user->ID)) {
				return '{"result":true,"content":"","msg":""}';
			} else {
				return '{"result":false,"content":"","msg":"'.$this->error.'"}';
			}
		} else {
			return '{"result":false,"content":"","msg":"Ублюдок, мать твою, а ну иди сюда, говно собачье, решил ко мне лезть? Ты, засранец вонючий, мать твою, а? Ну иди сюда, попробуй меня трахнуть, я тебя сам трахну"}';
		}
	}
	
	function adopt_title( $value ) {
		$result = str_replace('"','\"',$value);
		
		return $result;
	}
	
	function getChildsCount($postID) {
		$count = 0;
		$queryText = 'SELECT
			`_id` AS ID 
		FROM 
			`posts`
		WHERE
			`posts`.`parent` = '.$postID;
		if($query = $this->query($queryText)) {
			while($cur = $query->fetch_object()) {
				$count ++;
				$count += $this->getChildsCount($cur->ID);
			}
		}
		return $count;
	}
	
	function getRating($postID) {
		$rating = 0;
	    $queryText = 'SELECT SUM(`val`) AS ratingVal FROM `rating` WHERE `post` = '.$postID;
	    if($query = $this->query($queryText)) {
	        while($cur = $query->fetch_object()) {
	            $rating = $cur->ratingVal;
	        }
	    }
		if($rating == null) {
			return 0;
		} else {
			return $rating;
		}
	}
	
	function translit_sef($value) {
	$converter = array(
		'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
		'е' => 'e',    'ё' => 'e',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
		'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
		'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
		'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
		'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
		'э' => 'e',    'ю' => 'yu',   'я' => 'ya'
	);
 
	$value = mb_strtolower($value);
	$value = strtr($value, $converter);
	$value = mb_ereg_replace('[^-0-9a-zA-Zа-яА-Я]', '-', $value);
	$value = mb_ereg_replace('[-]+', '-', $value);
	$value = trim($value, '-');	
	
	$value .= '_'.time();
 
	return $value;
	}
}