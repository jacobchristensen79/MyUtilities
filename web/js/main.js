//Responsive Menu

$(document).ready(function(){
	$("#nav").addClass("js").before('<div id="rm-icon"></div>');
	$("#rm-icon").click(function(){
		$("#rm-nav").toggle();
	});
});

// Home URL Form Validate

$(document).ready(function(){
	
	
    $("#home-form").validate({
        rules: {
        	"url": {
        		required: true,
        		url: true
        		}
		},
		messages: {
		    "url": "Please, enter a Link"
		},
		submitHandler: function(form) {
			var resultbox = $('#server_response'),
				sbtn = $(form).find('input[type="submit"]'),
				sLoader = $(form).find('.btn_loader');
			
			resultbox.hide();
			
			$.ajax({
	            type : 'POST',
	            url : '/make-short-url',
	            async : false,
	            data : $(form).serialize(),
	            beforeSend: function(){
	            	sbtn.hide();
	            	sLoader.show();
	            	resultbox.find('.result').html('');
	            	resultbox.find('.resultlink').val('');
	            },
	            success : function (returnData) {
	            	$(form).find('input[name="url"]').val('');
	            	resultbox.find('.result').html(returnData.message);
	            	resultbox.find('.resultlink').val(returnData.link);
	            	resultbox.slideDown('fast');
	            },
	            error : function (xhr, textStatus, errorThrown) {
	            	resultbox.html(xhr);
	            },
	            complete : function (){
	            	sbtn.show();
	            	sLoader.hide();
	            }
		     });
		}
	});
});

// Access Form Validate

$(document).ready(function(){
    $("#access-form").validate({
        rules: {
        	"access_password": "required",
		    "access_username": "required",
		},
		messages: {
		    "access_password": "Please, enter password",
		    "access_username": "Please, enter your username",
		}
	});
});


// Focus
$(".resultlink").on("click", function () {
	   $(this).select();
});