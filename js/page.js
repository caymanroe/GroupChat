$(document).ready(function() {
	//$('#groupbar li a:hover').css('background-color', 'black');
    $("#groupbar li a").click(function() {
    	$('.group-is-sticky').removeClass('group-is-sticky', 100);
    	$(this).addClass('group-is-sticky', 100);
    	//$('#groupbar li a').css('background-color', '#E6EEF3');
    	//$(this).css('background-color', '#5BA4D4');
	    	var groupid = $(this).attr("id");
				 
        $("#display").promise().done(function() {
				        
	        $.ajax({
	        	type: 'post',
				url: 'loadfeed.php',
				datatype: "html",
				data: {
					group:groupid
				},
				success: function(response) {
					$("#display").children().fadeOut({duration:60, complete: function(){
						$("#display").empty().append(response).hide().fadeIn(60);
					}});


				}
			});
		});
    	return false;
    });

    $("#feedbutton").click(function() {
    	$('.group-is-sticky').removeClass('group-is-sticky', 100);
    	$(this).addClass('group-is-sticky', 100);
				 
        $("#display").promise().done(function() {
				        
	        $.ajax({
	        	type: 'post',
				url: 'loadfeedall.php',
				datatype: "html",
				success: function(response) {
					$("#display").children().fadeOut({duration:60, complete: function(){
						$("#display").empty().append(response).hide().fadeIn(60);
					}});
				}
			});
		});
    	return false;
    });

    $('.icon, .group h2').click(function() {
    	var groupLoadId = $(this).closest('.group').attr('id');
		var url = 'index.php';
		var form = $('<form action="' + url + '" method="post">' + '<input type="text" name="groupId" value="' + groupLoadId + '" />' + '</form>');
		$('body').append(form).hide();
		form.submit();
    });

    //Auto load a group into view if ID was supplied to index.php
    var attr = $('#display').attr('data-h');
	if (typeof attr !== typeof undefined && attr !== false) {
	    $.ajax({
	      	type: 'post',
			url: 'loadfeed.php',
			datatype: "html",
			data: {
				group:attr
			},
			success: function(response) {
				$("#display").empty().append(response);
				var currentGroup = $('#groupbar li').find("[id='" + attr + "']");
				$(currentGroup).addClass('group-is-sticky', 100);
			}
		});
	} else {
		$.ajax({
		 	type: 'post',
			url: 'loadfeedall.php',
			datatype: "html",
			success: function(response) {
			$("#display").empty().append(response);
    			$('.group-is-sticky').removeClass('group-is-sticky', 100);
    			$("#feedbutton").addClass('group-is-sticky', 100);
			}
		});
	}


    $('.icon-search').click(function(){
        searchGroups();
    });
    
    $('#search input').keypress(function(e){
        if(e.which == 13){//Enter key pressed
            searchGroups();
        }
    });

    $('#addInvite').click(function() {
	    var currentId = $(this).prev().attr('name').substring(6);
	    var newId = +currentId + 1;
	    var newField = "<input class=\"userInvite\" type=\"text\" name=\"invite"+newId+"\" placeholder=\"Email Address\" />";
	    $(newField).insertBefore("#addInvite").hide().slideDown();
    });


});

$(document).on('focus.textarea', '.commentNewBox', function(){
	var savedValue = this.value;
	this.value = '';
	this.baseScrollHeight = this.scrollHeight;
	this.value = savedValue;
})
.on('input.textarea', '.commentNewBox', function(){
	var minRows = this.getAttribute('data-min-rows')|0,
		 rows;
	this.rows = minRows;
		    console.log(this.scrollHeight , this.baseScrollHeight);
	rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 17);
	this.rows = minRows + rows;
});

$(document).on('focus.textarea', '#postNewBox', function(){
	var savedValue = this.value;
	this.value = '';
	this.baseScrollHeight = this.scrollHeight;
	this.value = savedValue;
})
.on('input.textarea', '#postNewBox', function(){
	var minRows = this.getAttribute('data-min-rows')|0,
		 rows;
	this.rows = minRows;
		    console.log(this.scrollHeight , this.baseScrollHeight);
	rows = Math.ceil((this.scrollHeight - this.baseScrollHeight) / 17);
	this.rows = minRows + rows;
});

$(document).on("click","#send",function() {
	var post = $("#postNewBox").val();
	$("#postNewBox").val("");
	$.ajax({
		type: 'post',
		url: 'submit.php',
		data: {
			action:'newpost',
			text:post
		},
		success: function (response) {
			//$("#newPost").after(response);
			$("#postlist").prepend(response);
			$("#postlist").find(".post:first").hide().slideDown();
		}
	});
});

$(document).keypress(function(e) {
    if(e.which == 13 && $(".commentNewBox").is(":focus") && !e.shiftKey) {
    	e.preventDefault();
        var comment = $(':focus').val();
        var postId = $(':focus').closest(".post").attr("id");
        var post = $(':focus').closest(".post");
        $.ajax({
        	type: 'post',
        	url: 'submit.php',
        	data: {
        		action:'newcomment',
        		text:comment,
        		postId:postId
        	},
        	success: function (response) {
        		$(post).find('.commentList').append(response);
        		$(post).find('.comment:last').hide().slideDown();
        		$(':focus').val("");
        	}
        });

    }
});

$(document).on("click",".deletePost",function() {
	var postId = $(this).closest(".post").attr("id");
	var post = $(this).closest(".post");
	$("#postNewBox").val("");
	$.ajax({
		type: 'post',
		url: 'submit.php',
		data: {
			action:'removepost',
			postId:postId
		},
		success: function (response) {
			$(post).slideUp();		
		}
	});
});

$(document).on("click",".deleteComment",function() {
	var commentId = $(this).closest(".comment").attr("id");
	var comment = $(this).closest(".comment");
	$("#commentNewBox").val("");
	$.ajax({
		type: 'post',
		url: 'submit.php',
		data: {
			action:'removecomment',
			commentId:commentId
		},
		success: function (response) {
			$(comment).slideUp();		
		}
	});
});

$(document).on("click",".canjoin",function() {
	var loading = $(this).next();
	var loaded = $(loading).next();
    $(this).hide('slide',{direction:'left'},200);
    $(loading).show('slide',{direction:'right'},200);
    var groupId = $(loading).closest('.group').attr('id');
    $.ajax({
    	type:'post',
    	url: 'submit.php',
    	data: {
    		action:'joingroup',
    		groupId:groupId
    	},
    	success: function (response) {
    		if (response=="1") {
    			$(loading).delay(1000).hide('slide',{direction:'left'},200);
    			$(loaded).delay(1200).show('slide',{direction:'right'},200);
    		} else {
    			alert("An error has occured. Please ensure you are connected to the internet. Otherwise, GroupChat may be experiencing difficulties.")
    		}

    	}
    })


});

function searchGroups() {
	if ($("#search input").val()!="") {
		var searchTerm = $("#search input").val();
		var url = 'grouplist.php';
		var form = $('<form action="' + url + '" method="post">' + '<input type="text" name="groupSearch" value="' + searchTerm + '" />' + '</form>');
		$('body').append(form).hide();
		form.submit();
		
	} else {
		alert("Search box is empty.")
	}
}

$(document).on("click","#notifications",function() {
	if ($('#notify_dropdown').css('display') == 'none') {

    	$.ajax({
    		type:'post',
    		url: 'submit.php',
    		data: {
    			action:'checknotifications'
    		},
    		success: function (response) {
    			$('#notifications').css('background-color', '#3498DB');
    			$('#notify_dropdown').css('display', 'block').prepend(response).hide().slideDown(170);
    		}
    	})

		//$('#notify_dropdown').css('display', 'block');
	} else {
		$('#notifications').css('background-color', 'transparent');
		$('#notify_dropdown').css('display', 'none');
		$('.notify').remove();
		$('#NoNotifications').remove();
		$('#clearNotifications').remove();
	}
});

$(document).on("click",".notify",function() {
	var relid = $(this).attr('id');

    $.ajax({
    	type:'post',
    	url: 'submit.php',
    	data: {
    		action:'changeseen',
    		relid:relid
    	},
    	success: function (response) {
    	}
    })
});

$(document).on("click","#clearNotifications",function() {
	$('#notify_dropdown').css('display', 'none');
	$('.notify').remove();
	$('#clearNotifications').remove();
	$('#NoNotifications').remove();
    $.ajax({
    	type:'post',
    	url: 'submit.php',
    	data: {
    		action:'clearnotif'
    	},
    	success: function (response) {
    	}
    })
});