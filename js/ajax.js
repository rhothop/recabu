function readJson( str ) {
    var result;
    try {
        result = JSON.parse( str );
    } catch ( exc ) {
        result = {status: false, content: '', msg : exc };
    }
    return result;
}

function getUserList( mask ) {
	var request = $.ajax({
		url: '/db_point/userlist/',
		method: "POST",
		data: {mask:mask},
		dataType: "html",
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
	    var answer = readJson( msg );
		showUserList( answer );
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}

function sendFile( file ) {
	var request = $.ajax({
		url: '/upload/',
		method: "POST",
		data: file,
		processData: false,
        contentType: false,
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
	    var answer = readJson( msg );
	    if( answer.result ) {
			var cont = $( '#content' ).val();
			if( cont === '' ) {
				$( '#content' ).val( '![picture]('+answer.content+')' );
			} else {
				$( '#content' ).val( cont + ' ![picture]('+answer.content+')' );
			}
			$( '#addfile' ).val( '' );
	    } else {
	        alert(answer.msg);
	    }
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}

function postPost(title, content, nsfw = false, oc = false, parent = 0) {
	var request = $.ajax({
		url: '/add/',
		method: "POST",
		data: { title : title, content : content, nsfw : nsfw, oc : oc, parent : parent},
		dataType: "html",
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
	    var answer = readJson( msg );
	    if( answer.result ) {
	        if(answer.content === '/posts/') {
			    commentForm.remove();
    			commentForm = undefined;
    			location.reload();
	        } else {
	            location.href = answer.content;
	        }
	    } else {
	        alert(answer.msg);
	    }
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}

function deletePost( id ) {
	var request = $.ajax({
		url: '/add/',
		method: "DELETE",
		data: { post : id },
		dataType: "html",
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
	    var answer = readJson( msg );
	    if( answer.result ) {
			location.reload();
		} else {
			alert(answer.msg);
	    }
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}

function registration(login, psw, ref) {
	var request = $.ajax({
		url: '/registration/',
		method: "POST",
		data: { login : login, pass : psw, ref : ref},
		dataType: "html",
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
		if(msg === 'good') {
			alert('Поздравлем с регистрацией. Теперь можно авторизоваться');
			location.href = '/hot';
		} else {
		    alert(msg);
			//location.reload();
		}
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}

function login( login, psw ) {
	var request = $.ajax({
		url: '/login/',
		method: "POST",
		data: { login : login, psw : psw },
		dataType: "html",
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
	    var answer = readJson( msg );
	    if( answer.status ) {
	        location.reload();
	    } else {
	        alert( answer.msg );
	    }
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}

function logout() {
	var request = $.ajax({
		url: '/login/',
		method: "DELETE",
		//data: {},
		dataType: "html",
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
		if(msg) {
			location.reload();
		}
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}

function votePost( target, val ) {
	var request = $.ajax({
		url: '/vote/',
		method: "POST",
		data: { target : target, value : val},
		dataType: "html",
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
		var answer = readJson( msg );
		if( answer.result ) {
			location.reload();
		} else {
			alert( answer.msg );
		}
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}

function sendForBoyan( str ) {
	var request = $.ajax({
		url: '/add/boyan/',
		method: "POST",
		data: { incoming : str},
		dataType: "html",
		beforeSend: function() {
		}
	});
	
	request.done(function( msg ) {
		var answer = readJson( msg );
		if( answer.perc > 70 ) {
			$( '#msgbox' ).html( 'Скорее всего Ваш пост - баян' );
		} else {
			$( '#msgbox' ).html( '' );
		}
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}