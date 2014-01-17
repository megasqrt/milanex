$(document).ready(function() {
			
        $("#contentloader").slideDown(500, function() {
            $('.spinner').fadeOut();
        });
		
		
        $("a").click(function(event){

            event.preventDefault();

            linkLocation = this.href;
            
            $("#contentloader").slideUp(500, function() {
                $('.spinner').fadeIn(0, redirectPage);
            });    
        });
         
        function redirectPage() {
            window.location = linkLocation;
        }
	});