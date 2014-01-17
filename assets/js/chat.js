$('document').ready(function() {
		$('#chat_toggle').click(function() {
			$('#chatbox').slideToggle();
		});

		var refreshChat = function() 
		{ 
			setTimeout( 
				function() 
		  		{
		  			var moveDown = false;

		  			if($('#messages').scrollTop() == ($('#messages')[0].scrollHeight - $('#messages')[0].offsetHeight)) {
		  				moveDown = true;
		  			}

		   			$('#messages').load('system/ajaxLOAD.php', 
						function() 
						{
							if(moveDown) {
								$('#messages').animate({scrollTop: $('#messages')[0].scrollHeight}, 1000);
							}
						}
		   			);
		   			refreshChat();
		  		}, 
		  5000);
		};
		refreshChat();
		
		$('#ajaxPOST').submit(function() {
			$.post('ajaxPOST.php', $('#ajaxPOST').serialize(), function(data){
						
                            $('#message').val('');
                            $('#messages').load('system/ajaxLOAD.php', function() {
                                $('#messages').animate({
									
                                    scrollTop: $('#messages')[0].scrollHeight
                                  }, 1000);
                            }); 
			});
			return false; 
		});
});

	$('#message').keypress(function(event){
    var char = String.fromCharCode(event.which)
    var txt = $(this).val()
    if (! txt.match(/^[^A-Za-z0-9+#\-\.]+$/)){
        $(this).val(txt.replace(char, ''));
    }
	});