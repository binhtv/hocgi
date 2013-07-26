if(typeof Documentary == 'undefined') {
	var Documentary = {};
};

Documentary.addViewCount = function(documentId) {
	$.ajax({
		url:'/cms/documentary/add-view',
		data:{document_id:documentId},
		dataType:'json',
		type:'GET',
		success:function(res) {
			console.log(res);
		},
		error:function(e) {
			console.log(e);
		},
	});
};

Documentary.addDownloadCount = function(documentId, downloadUrl) {
	$.ajax({
		url:'/cms/documentary/add-download',
		data:{document_id:documentId},
		dataType:'json',
		type:'GET',
		success:function(res) {
			document.location.href = downloadUrl;
			console.log(res);
		},
		error:function(e) {
			console.log(e);
		},
	});
};

$(document).ready(function(e){
	if(typeof documentaryId != 'undefined' && documentaryId) {//Track view count
		Documentary.addViewCount(documentaryId);
	}
});