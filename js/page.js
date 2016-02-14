$(document).ready(function() {
    $("#groupbar li a").click(function() {
	    	var groupid = $(this).attr("id");
				 
        $("#display").empty();
        $("#display").promise().done(function() {
				        
	        $.ajax({
	        	type: 'post',
				url: 'loadfeed.php',
				datatype: "html",
				data: {
					group:groupid
				},
				success: function(response) {
					$("#display").empty().append(response);
				}
			});
		});
    	return false;
    });

    $("#feedbutton").click(function() {
				 
        $("#display").empty();
        $("#display").promise().done(function() {
				        
	        $.ajax({
	        	type: 'post',
				url: 'loadfeedall.php',
				datatype: "html",
				success: function(response) {
					$("#display").empty().append(response);
				}
			});
		});
    	return false;
    });

	$.ajax({
	 	type: 'post',
		url: 'loadfeedall.php',
		datatype: "html",
		success: function(response) {
		$("#display").empty().append(response);
		}
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