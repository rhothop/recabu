<?php
class languages {
	public $dic;
	
	public function __construct( $lang ) {
		if ($lang === 'en_en') {
			$this->dic = [
				'rules' => 'Rules',
				'addpost' => 'Create',
				'newposts' => 'New',
				'topposts' => 'Top',
				'hotposts' => 'Hot',
				'rating' => 'Rating',
				'messages' => 'Answers',
				'signout' => 'Signout',
				'sigin' => 'Signin',
				'registr' => 'Registration',
				'users' => 'Users',
				'top' => 'TOP',
				'reflink' => 'Refference link',
				'hidden' => 'Hiden',
				'deleted' => 'Deleted',
				'comment' => 'comment',
				'reply' => 'Reply',
				'post' => 'post',
				'answer' => 'Answer by',
				'nofound' => 'Nothing'
			];
		} else {
			$this->dic = [
				'rules' => 'Правила',
				'addpost' => 'Добавить',
				'newposts' => 'Свежее',
				'topposts' => 'Лучшее',
				'hotposts' => 'Горячее',
				'rating' => 'Рейтинг',
				'messages' => 'Ответы',
				'signout' => 'Выйти',
				'sigin' => 'Войти',
				'registr' => 'Зарегистрироваться',
				'users' => 'Пользователей',
				'top' => 'ТОП',
				'reflink' => 'Ссылка для приглашения',
				'hidden' => 'Скрыто',
				'deleted' => 'Удалено',
				'comment' => 'коммент',
				'reply' => 'Ответить',
				'post' => 'пост',
				'answer' => 'Ответ',
				'nofound' => 'Упс, ничего нет'
			];
		}
	}
	
}