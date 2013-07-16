if(typeof Poll == 'undefined') {
	var Poll = {};
};

Poll.pollResult = '';

Poll.registerEvent = function() {
	$('#btnVote').unbind('click');
	$('#btnVote').bind('click', Poll.handleVoteClick);
	$('#btnViewResult').unbind('click');
	$('#btnViewResult').bind('click', Poll.handleViewResultClick);
	$(document).keydown(function(e) {
		if(e.keyCode == 27) {
			Poll.hideResult();
		}
	});
};

Poll.showResult = function(html) {
	var width = 350;
	var height = 200;
	var top = (window.innerHeight - height) / 2;
	var left = (window.innerWidth - width)/2
	var div = '<div id="pollResult" style="position: fixed;z-index: 10003;top: '+top+'px;left: ' + left + 'px;background: white;width: 350px;height: 200px;">';
	var closeDiv = '</div>';
	$('body').append(div + html +closeDiv);
}

Poll.hideResult = function() {
	$('#pollResult').remove();
}

Poll.vote = function() {
	var choices = [];
	var options = $('.check_poll');
	options.each(function() {
		if($(this).is(':checked')) {
			choices.push($(this).attr('data-id'));
		}
	});
	
	return choices;
}

Poll.handleVoteClick = function(e) {
	if(Utils.getCookie('already_vote')) {
		$('#btnViewResult').trigger('click');
	} else {
		var ids = Poll.vote();
		if(ids.length == 0) {
			alert('Bạn phải chọn ít nhất một phương án!');
			return;
		}
		$.ajax({
			url: '/cms/poll/vote',
			type:'GET',
			data: 'choices=' + JSON.stringify(ids),
			success: function(response) {
				Utils.setCookie('already_vote', 1, 1);//Expire within a day
				$('#btnViewResult').trigger('click');
			},
			error: function(error) {
				$('#article_content').html('Có lỗi xảy ra, vui lòng tải lại trang!');
				console.log(error);
			}
		});
	}
};

Poll.handleViewResultClick = function(e) {
	var pollId = $(this).attr('data-poll-id');
	if($('#popup_bieuquyet').length > 0) {
		$('#popup_bieuquyet').modal();	
	} else {
		$.ajax({
			url: '/cms/poll/poll-result',
			type:'GET',
			data: 'id=' + pollId,
			success: function(response) {
				$('body').append(response);
				$('#popup_bieuquyet').modal();
			},
			error: function(error) {
				alert('Có lỗi xảy ra, vui lòng tải lại trang!');
				console.log(error);
			}
		});
	}
};

$(document).ready(function(e) {
	Poll.registerEvent();
});