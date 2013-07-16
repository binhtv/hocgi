function showConFirmBox()
{
	var r = confirm('Do you want to delete this item?');
	if (r){
		return true;
	}else
		return false;
}

// open popup
function openPop(url)
{
	window.open(url,"_blank","toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=400, height=300, left=350, top=250");	
}

/**
 * get module by type(view|box) in block management
 */
function ajaxChangeModule(getUrl, selObj)
{
	$.ajax( {
		// Define AJAX properties.
		method :"get",
		url :getUrl + '/type/' + selObj.value,
		dataType :"html",
		// Define the succss method.
		success : function(data) {
			$("#deltaBox").html(data);
		},
		// Define the error method.
		error : function(objAJAXRequest, strError) {
			$("#deltaBox").text("Error! Type: " + strError);
		}
	});
}

/**
 * get section by layout in block management
 */
function ajaxChangeLayout(getUrl, selObj)
{
	$.ajax( {
		// Define AJAX properties.
		method :"get",
		url :getUrl + '/layout/' + selObj.value,
		dataType :"html",
		// Define the succss method.
		success : function(data) {
			$("#sectionBox").html(data);
		},
		// Define the error method.
		error : function(objAJAXRequest, strError) {
			$("#sectionBox").text("Error! Type: " + strError);
		}
	});
}

/**
 * add more parasm for view object
 */
function addParam()
{
	var counter = parseInt($('#counter').val()); 
	if(counter < 5 && counter != 0)
	{
		var item = $('#block_param_' + counter);	
		var clone = item.clone().insertAfter(item);	
		var time = new Date().getTime();	
		$(clone).attr('id', 'block_param_' + (counter + 1));
		$(clone.children().children()[2]).attr("id", "remove_" + (counter + 1));
		$(clone.children().children()[2]).css("display", "block");
		$('#counter').val(counter + 1);
	}
}

function removeParam(button) 
{
	var counter = parseInt($('#counter').val());
	if(counter > 1)
	{	
		$(button).parent().parent().remove();
		$('#counter').val(counter - 1);
	}
}

function checkAll() 
{console.log($("#chkAll").attr('id'));

	if($("#chkAll").is(':checked')){
		$('input[id|=item]').each(function(index) {
			$(this).attr('checked','checked');
		});
	} else{
		$('input[id|=item]').each(function(index) {
			$(this).removeAttr('checked');
		});
	}
}

$(document).ready(function() {
	$(".actionCheckbox, .resourceCheckbox").click(function(){
		var id = $(this).attr('id');  
		if($(this).is(":checked")){ 
			$("."+id).attr('checked','checked');
		}else{ 
			$("."+id).attr('checked','');
		} 
	});
});