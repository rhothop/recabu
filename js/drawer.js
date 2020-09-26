var commentForm;

$( document ).ready( function() {
	//alert( showData );
	bind();
});

function bind() {
    $( window ).on( 'scroll', function() {
        scrollFunction();
    });
    $( '#ontop' ).on( 'click', function() {
          document.body.scrollTop = 0; // For Safari
          document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    });
    $( '#registration' ).on( 'click', function() {
        event.preventDefault();
        var login = $( '#login' ).val();
        var psw1 = $( '#psw1' ).val();
        var psw2 = $( '#psw2' ).val();
        var agree = $( '#agree' ).prop( 'checked' );
        var ref = $( '#ref' ).val();
        
        if(login.match(/^[A-Za-zА-Яа-яЁё0-9]+$/g) === null) {
            alert('Используйте в логине только буквы и цифры. Пробел тоже запрещен');
            return;
        }
        if( login === '') {
            alert('Ну и как мы тебя узнаем без логина?');
            return;
        }

        if( psw1 === '') {
            alert('Не будет пароля, не будет учетки!');
            return;
        }
        
        if(psw1 != psw2) {
            alert('Накосячил в паролях, они не совпадают');
            return;
        }
        if( !agree ) {
            alert('Ты уже большой и должен взять ответственность за свои действия на себя. Ставь галочку или проваливай!');
            return;
        }
        registration( login, psw1, ref );
    });
	$( '.themeChange' ).on( 'click', function() {
		if( $( this ).attr( 'param' ) === 'dark' ) {
			document.cookie = 'theme=dark; path=/; secure; domain=recabu.cf; samesite=lax';
		} else {
			document.cookie = 'theme=light; path=/; secure; domain=recabu.cf; samesite=lax';
		}
		location.reload();
	});		
	$( '.blured' ).on( 'click', function() {
		$( this ).removeClass( 'blured' );
	});
	$( '.postframe' ).on( 'click', function() {
	    if( $( this ).css( 'max-height' ) == '100%' ) {
	        $( this ).css( 'max-height', '1000px' );
	    } else {
	        $( this ).css( 'max-height', '100%' );
	    }
	});
	$( '#createPost' ).on( 'click', function() {
		event.preventDefault();
		postPost( $( '#title' ).val(), $( '#content' ).val(), $( '#nsfw' ).prop( 'checked' ), $( '#oc' ).prop( 'checked'));
	});
	$( '.loginForm input[type="button"]' ).on( 'click', function() {
		login( $( '.loginForm input[type="text"]' ).val(), $( '.loginForm input[type="password"]' ).val() );
	});
	$( '.loginForm .icons' ).on( 'click', function() {
		login( $( '.loginForm input[type="text"]' ).val(), $( '.loginForm input[type="password"]' ).val() );
	});
	$( '.logoutForm input[type="button"]' ).on( 'click', function() {
		logout();
	});
	$( '.logoutForm .icons' ).on( 'click', function() {
		logout();
	});
	$( '.vote' ).on( 'click', function() {
		votePost( $( this ).attr( 'val_target' ), $( this ).attr( 'val_data') );
	});
	$( '.addComment' ).on( 'click', function() {
		if( commentForm === undefined ) {
			commentForm = $( '<div>', {
				class: 'answer',
				append: $( '<div>', {
					class: 'form-group',
					append: $( '<input>', {
						type: 'text',
						id: 'target',
						value: $( this ).parent().attr( 'val_target' ),
						style: 'display:none;'
					}).add( $ ( '<textarea>', {
						class: 'form-control',
						name: 'content',
						id: 'content',
						placeholder: 'Комментарий'
					})).add( $ ( '<div>', {
						class: 'form-check',
						append: $( '<input>', {
							class: 'form-check-input',
							name: 'nsfw',
							id: 'nsfw',
							type: 'checkbox'
						}).add( $( '<label>', {
							class: 'form-check-label',
							for: 'nsfw',
							append: 'NSFW'
						}))
					})).add( $( '<button>', {
						type: 'submit',
						id: 'sendComment',
						class: 'btn btn-primary',
						append: 'Ответить'
					}))
				})
			}).appendTo( $( this ).parent() );
			$( '<div>', {
				class: 'icons',
				append: '<svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#video"></use></svg>'
			}).prependTo( commentForm ).on( 'click', function() {
				var input = prompt( 'Вставьте ссылку на mp4 видео. Адрес должен заканчиваться на .mp4', '');
				if( input != null ) {
					var cont = $( '#content' ).val();
					if( cont === '' ) {
						$( '#content' ).val( '[video]('+input+')' );
					} else {
						$( '#content' ).val( cont + ' [video]('+input+')' );
					}
				}
			});
			$( '<div>', {
				class: 'icons',
				append: '<img style="max-width: 100%;vertical-align: text-top;" src="https://upload.wikimedia.org/wikipedia/commons/0/09/YouTube_full-color_icon_%282017%29.svg" />'
			}).prependTo( commentForm ).on( 'click', function() {
				var input = prompt( 'Вставьте ссылку на youtube-видео', '');
				if( input != null ) {
					var cont = $( '#content' ).val();
					if( cont === '' ) {
						$( '#content' ).val( '[youtube]('+input+')' );
					} else {
						$( '#content' ).val( cont + ' [youtube]('+input+')' );
					}
				}
			});
			$( '<div>', {
				class: 'icons',
				append: '<svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#image"></use></svg>'
			}).prependTo( commentForm ).on( 'click', function() {
				var input = prompt( 'Вставьте ссылку на изображение', '');
				if( input != null ) {
					var cont = $( '#content' ).val();
					if( cont === '' ) {
						$( '#content' ).val( '!['+input+']('+input+')' );
					} else {
						$( '#content' ).val( cont + ' !['+input+']('+input+')' );
					}
				}
			});
			$( '<div>', {
				class: 'icons',
				append: '<svg viewBox="0 0 8 8"><use xlink:href="/images/sprite.svg#link-intact"></use></svg>'
			}).prependTo( commentForm ).on( 'click', function() {
				var input = prompt( 'Вставьте адрес ссылки', '');
				if( input != null ) {
					var input2 = prompt( 'Введите текст ссылки', '');
					var cont = $( '#content' ).val();
					if( input2 != null ) {
						if( cont === '' ) {
							$( '#content' ).val( '!['+input2+']('+input+')' );
						} else {
							$( '#content' ).val( cont + ' ['+input2+']('+input+')' );
						}
					} else {
						if( cont === '' ) {
							$( '#content' ).val( '!['+input+']('+input+')' );
						} else {
							$( '#content' ).val( cont + ' ['+input+']('+input+')' );
						}
					}
				}
			});
			$( '#sendComment' ).on( 'click', function() {
				postPost( '', $( '#content' ).val(), $( '#nsfw' ).prop( 'checked' ), false, $( '#target' ).val() );
			});
		} else {
			commentForm.remove();
			commentForm = undefined;
		}
	});
}

//Get the button:
mybutton = document.getElementById("myBtn");

function scrollFunction() {
  if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
    $('#ontop').css('display','block');
  } else {
    $('#ontop').css('display','none');
  }
}
