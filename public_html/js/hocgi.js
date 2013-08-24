if(typeof Utils == 'undefined') {
	var Utils = {};
};

/**
 * Set cookie
 * @param a name
 * @param b value
 * @param c day, duration to expire
 * @param d path
 * @param f domain
 * @param j secure
 * */
Utils.setCookie = function(a,b,c,d,f,j) {
	var e=new Date;e.setTime(e.getTime());c&&(c*=864E5);e=new Date(e.getTime()+c);document.cookie=a+"="+escape(b)+(c?";expires="+e.toGMTString():"")+(d?";path="+d:"")+(f?";domain="+f:"")+(j?";secure":"")
};

Utils.getCookie = function(c_name) {
	if (document.cookie.length > 0) {
		c_start = document.cookie.indexOf(c_name + "=");
		if (c_start != -1) {
			c_start = c_start + c_name.length + 1;
			c_end = document.cookie.indexOf(";", c_start);
			if (c_end == -1)
				c_end = document.cookie.length;
			return unescape(document.cookie.substring(c_start, c_end));
		}
	}
	return "";
};

Utils.showMask = function() {
	var width = document.width;
	var height = document.height;
	var html = '<div id="BoxOverlay" style="text-align: center; padding-top: 50%; position: absolute; top: 0px; left: 0px; opacity: 0.7; background-color: rgb(0, 0, 0); z-index: 1001; height: '+height + 'px; width: ' + width + 'px; display: block;">'+
					'<img src="http://hocgi.net/images/zme_loading.gif">'+
				'</div>';
	$('body').append(html);
};
Utils.hideMask = function() {
	$('#BoxOverlay').remove();
};

Utils.checkValidEmail = function(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}


if(typeof Article == 'undefined') {
	var Article = {};
};

Article.load = function (category, page) {
	$.ajax({
		url: '/cms/category/list-article',
		type:'GET',
		data: 'id=' + category + '&page=' + page,
		success: function(response) {
			$('#article_content').html(response);
		},
		error: function(error) {
			$('#article_content').html('Có lỗi xảy ra, vui lòng tải lại trang!');
			console.log(error);
		}
	});
};
Article.addViewCount = function(articleId) {
	$.ajax({
		url:'/cms/index/add-view',
		data:{article_id:articleId},
		dataType:'json',
		type:'GET',
		success:function(res) {
			console.log(res);
		},
		error:function(e) {
			console.log(e);
		},
	});
}


if(typeof Course == 'undefined') {
	var Course = {};
};
if(typeof Course.HandleFunction == 'undefined') {
	Course.HandleFunction = {};
};
Course.tab = '';
Course.load = function(page, name, tuition_from, tuition_to, city, tab, category, keySearch) {
	var url = '/cms/course/list-course';
	var data = 'id=' + category +'&page=' + page + '&name=' + name + '&from=' + tuition_from + '&to=' + tuition_to + '&city=' + city + '&tab=' + tab;
	if(typeof keySearch != 'undefined' && keySearch != '') {
		url = '/cms/search/index';
		data = {page:page, keySearch:keySearch};
	}
	
	$.ajax({
		url: url,
		type:'GET',
		data: data,
		success: function(response) {
			$('#course_list').html(response);
		},
		error: function(error) {
			$('#course_list').html('Có lỗi xảy ra, vui lòng tải lại trang!');
			console.log(error);
		}
	});
};

Course.loadCourseInPageDetail = function(page, name, tuition_from, tuition_to, city) {
	var category = $('input#category').val();
	var courseId = $('input#course_id').val();
	$.ajax({
		url: '/cms/course/course-comparison',
		type:'GET',
		data: 'id=' + courseId +'&category=' + category +'&page=' + page + '&name=' + name + '&from=' + tuition_from + '&to=' + tuition_to + '&city=' + city,
		success: function(response) {
			$('#course_comparison_content').html(response);
		},
		error: function(error) {
			$('#course_comparison_content').html('Có lỗi xảy ra, vui lòng tải lại trang!');
			console.log(error);
		}
	});
};

Course.registerEvent = function() {
	$('#filter_course').unbind('click');
	$('#filter_course').bind('click', Course.HandleFunction.handleClickFilterCourse);
	
	$('#filter_chitiet_course').unbind('click');
	$('#filter_chitiet_course').bind('click', Course.HandleFunction.handleClickFilterDetailCourse);
	
	$('div#content').off('click', 'a.quick_view');
	$('div#content').on('click', 'a.quick_view', Course.HandleFunction.handleClickViewQuickDetailCourse);
	
	$('div#content').off('click', 'a.btn_compare');
	$('div#content').on('click', 'a.btn_compare', Course.HandleFunction.handleClickViewCompareDetailCourse);
	$('a#course_all_tab').unbind('click');
	$('a#course_all_tab').bind('click', Course.HandleFunction.handleClickTabCourse);
	$('a#course_new_tab').unbind('click');
	$('a#course_new_tab').bind('click', Course.HandleFunction.handleClickTabCourse);
	$('a#course_requested_tab').unbind('click');
	$('a#course_requested_tab').bind('click', Course.HandleFunction.handleClickTabCourse);
};

Course.HandleFunction.handleClickTabCourse = function(e) {
	var tab = $(this).attr('data-tab');
	$('li.tabs-selected').removeClass('tabs-selected');
	$(this).parent().addClass('tabs-selected');
	Course.tab = tab;
	Course.load(1, '', '', '', '', tab, category);
};
Course.HandleFunction.handleClickFilterCourse = function(e) {
	var name = $('#f_name').val();
	var from = $('#f_tuition_from').val();
	var to = $('#f_tuition_to').val();
	var city = $('#f_city').val();
	Course.load(1, name, from, to, city, Course.tab, category);
};

Course.HandleFunction.handleClickFilterDetailCourse = function(e) {
	var name = $('#f_name').val();
	var from = $('#f_tuition_from').val();
	var to = $('#f_tuition_to').val();
	var city = $('#f_city').val();
	Course.loadCourseInPageDetail(1, name, from, to, city);
};
Course.HandleFunction.handleClickViewQuickDetailCourse = function(e) {
	var courseId = $(this).attr('data-course-id');
	var courseDetail = $('#course_detail_' + courseId); 
	if(courseDetail.length > 0) {
		courseDetail.dialog({
			draggable:false,
			height:(8*window.innerHeight)/9,
			width:(8*window.innerWidth)/9,
			show : {effect:'fadein', duration:1000},
			modal:true,
		});
	} else {//Call ajax
		Utils.showMask();
		$.ajax({
			url:'/cms/course/detail-course',
			data:{id:courseId},
			type:'GET',
			success:function(response) {
				Utils.hideMask();
				$('body').append(response);
				$('#course_detail_' + courseId).hide();
				$('#course_detail_' + courseId).dialog({
					draggable:false,
					height:(8*window.innerHeight)/9,
					width:(8*window.innerWidth)/9,
					show : {effect:'fadein', duration:1000},
					modal:true,
				});
			},
			error:function(error) {
				Utils.hideMask();
				alert('Có lỗi xảy ra, vui lòng thử lại!');
				console.log(error);
			},
		});
	}
};

Course.HandleFunction.handleClickViewCompareDetailCourse = function(e) {
	var courseId = $(this).attr('data-course-id');
	var courseDetail = $('#box_compare_course_' + current_course_id + '_' + courseId); 
	if(courseDetail.length > 0) {
		courseDetail.dialog({
			draggable:false,
			height:(9*window.innerHeight)/10,
			width:(9*window.innerWidth)/10,
			show : {effect:'fadein', duration:1000},
			modal:true,
		});
	} else {//Call ajax
		Utils.showMask();
		$.ajax({
			url:'/cms/course/two-course',
			data:{id:current_course_id, id2:courseId},
			type:'GET',
			success:function(response) {
				Utils.hideMask();
				$('body').append(response);
				$('#box_compare_course_' + current_course_id + '_' + courseId).hide();
				$('#box_compare_course_' + current_course_id + '_' + courseId).dialog({
					draggable:false,
					height:(9*window.innerHeight)/10,
					width:(9*window.innerWidth)/10,
					show : {effect:'fadein', duration:1000},
					modal:true,
				});
			},
			error:function(error) {
				Utils.hideMask();
				alert('Có lỗi xảy ra, vui lòng thử lại!');
				console.log(error);
			},
		});
	}
};
/**
 * Library Type
 */
if(typeof Library == 'undefined') {
	var Library = {};
};

Library.load = function(page, type) {
	var elementId = '';
	if(type == 1) {
		elementId = 'box-latest-documentary'; 
	} else if(type == 2) {
		elementId = 'box-most-download-documentary';
	}
	
	$.ajax({
		url: '/cms/documentary/index',
		type:'GET',
		data: 'type=' + type + '&page=' + page,
		success: function(response) {
			$('#' + elementId).html(response);
		},
		error: function(error) {
			$('#' + elementId).html('Có lỗi xảy ra, vui lòng tải lại trang!');
			console.log(error);
		}
	});
}

$(document).ready(function(e){
	Course.registerEvent();
	if(articleId) {//Track view count
		Article.addViewCount(articleId);
	}
});