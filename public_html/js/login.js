if(typeof Login == 'undefined') {
	var Login = {};
};

Login.submit = function() {
	var u = $.trim($('#username').val());
	var password = $.trim($('#password').val());
	if(u == '' || password == '') {
		alert('Vui lòng nhập username và password');
		return;
	}
	$.ajax({
		url:'/cms/auth/login',
		type:'POST',
		dataType:'json',
		data:{username:u, password:password},
		success:function(response) {
			if(response.code == 1) {
				username = response.data.username;
				var fullname = response.data.fullname;
				Login.updateLoginInformation(fullname);
			} else if(response.code == -1) {
				alert('Tài khoản của bạn chưa kích hoạt!');
			} else{
				alert('Username hoặc mật khẩu không đúng!');
			}
		},
		error: function(e) {
			alert('Có lỗi xảy ra vui lòng thử lại!');
		},
	});
};

Login.updateLoginInformation = function(fullname) {
	var htmlSuccessLogin = "<li>Xin chào <a href='javascript:void(0)'>"+fullname+"</a></li>";
	htmlSuccessLogin += "<li>| <a href='javascript:void(0);' class='Logout'>Thoát</a></li>";
	$('#loginBox').html(htmlSuccessLogin);
}

Login.logout = function() {
	$.ajax({
		url:'/cms/auth/logout',
		type:'POST',
		complete:function() {
			window.location.reload(false);
		},
	});
}

Login.checkLogin = function() {
	$.ajax({
		url:'/cms/auth/check-login',
		type:'POST',
		success:function(response) {
			if(response.code==1 && response.data.username) {
				Login.updateLoginInformation(response.data.fullname);
			}
		},
		error:function(e) {
			console.log(e);
		}
	});
}

Login.register = function() {
	//Check validation
	var username = $.trim($('div#form_register #username').val());
	if(!username) {
		$('div#form_register #err_username').show();
		return;
	} else {
		$('div#form_register #err_username').hide();
	}
	
	var password = $.trim($('div#form_register #password').val());
	if(!password) {
		$('div#form_register #err_password').show();
		return;
	} else {
		$('div#form_register #err_password').hide();
	}
	var rpassword = $.trim($('div#form_register #r_password').val());
	if(!rpassword || rpassword != password) {
		$('div#form_register #err_r_password').show();
		return;
	} else {
		$('div#form_register #err_r_password').hide();
	}
	var email = $.trim($('div#form_register #email').val());
	if(!email) {
		$('div#form_register #err_email').show();
		return;
	} else {
		$('div#form_register #err_email').hide();
	}
	if(!Utils.checkValidEmail(email)) {
		$('div#form_register #err_email').show();
		return;
	} else {
		$('div#form_register #err_email').hide();
	}
	var remail = $.trim($('div#form_register #r_email').val());
	if(!remail || remail != email) {
		$('div#form_register #err_r_email').show();
		return;
	} else {
		$('div#form_register #err_r_email').hide();
	}
	var fullname = $.trim($('div#form_register #fullname').val());
	if(!fullname) {
		$('div#form_register #err_fullname').show();
		return;
	} else {
		$('div#form_register #err_fullname').hide();
	}
	var gender = $('input[name=id_gender]:checked', 'div#form_register').val();
	if(!gender) {
		$('div#form_register #err_gender').show();
		return;
	} else {
		$('div#form_register #err_gender').hide();
	}
	var day = $('#day option:selected', 'div#form_register').val();
	var month = $('#month option:selected', 'div#form_register').val();
	var year = $('#year option:selected', 'div#form_register').val();
	
	var data = {username : username,
				email:email, password:password,
				fullname:fullname, gender:gender};
	if(day&&month&&year) {
		data.day = day;
		data.month = month;
		data.year = year;
	}
	$.ajax({
		url : '/cms/auth/register',
		type:'POST',
		data:data,
		success:function(response) {
			if(response.code == 1 && response.data.username) {
				alert('Bạn đã đăng ký tài khoản mới thành công!');
				username = response.data.username;
				Login.updateLoginInformation(response.data.fullname);
				$.modal.close();
			} else if(response.code == -1) {
				alert('Username hoặc email đã được sử dụng');
			} else {
				alert('Có lỗi xảy ra, vui lòng thử lại!');
			}
		},
		error:function(e) {
			console.log(e);
			alert('Có lỗi xảy ra, vui lòng thử lại!');
		}
	});
}

Login.showRegister = function() {
	if($('#popup_formregister').length > 0) {
		$('#popup_formregister').modal();
	}
}

$(document).ready(function() {
	$(document).on('click', '.Login', Login.submit);
	$(document).on('click', '.Logout', Login.logout);
	$(document).on('click', '.Register', Login.showRegister);
	$('#registerAccountButton').bind('click', Login.register);
	Login.checkLogin();
});