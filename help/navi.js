$(document).ready(function() {
    $('.chapter').hide();
    $('.popup').hide();    

    
    if(window.location.hash && $(window.location.hash).attr('id')) {
    		var par = $(window.location.hash).parent('div');
    		par.show();
    		location.href=window.location.hash;
    		 $('#contentmenu li a').each(function(idx,elem) {         	
    	        if($(this).attr('href') == window.location.hash) {
    	        	$(this).addClass('active');     
    	        }  else {
    	        	$(this).removeClass('active');
    	        }
    		 });
    		 
    } else {
    	$('#00-Wiki').show();	
    }

    $('#contentmenu>li>a').click(function () { 
       $('.chapter').hide();
       $('#contentmenu li a').removeClass('active');      
       $('.submenu li a').removeClass('active');
       var myid = $(this).attr('href');
       $(this).addClass('active');
        $(myid).show();
    });    
    $('.submenu li a').click(function () { 
       $('.chapter').hide();      
       $('#contentmenu li a').removeClass('active');
       $('.submenu li a').removeClass('active');
       var myid = $(this).attr('href');
       $(this).addClass('active');
       var anchor = $(this).parent().parent().parent().children(':first-child').attr("href");
       $(anchor).show();
    });   

	$("a#a-Whats-all-this-in-aid-of").easyTooltip({
			useElement: "Using-Parameters"				   
	});

 	$("a#a-ServerAliases").easyTooltip({
			useElement: "Server-Aliases"				   
	});
});
