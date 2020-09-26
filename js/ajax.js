function readJson( str ) {
    var result;
    try {
        result = JSON.parse( str );
    } catch ( exc ) {
        result = {status: false, content: '', msg : exc };
    }
    return result;
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
		//if(msg) {
		//	location.reload();
		//}
		//alert( msg );
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
		location.reload();
	});
	
	request.fail(function( jqXHR, textStatus ) {
		console.log( jqXHR.responseText );
	});
	request.always(function() {
	});
}