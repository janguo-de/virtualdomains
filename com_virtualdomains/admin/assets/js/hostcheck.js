jQuery.noConflict();
(function($) {
	jQuery(document).ready(function() {
		console.log('fertig');
		$('.hostcheck').each(function() {
			var curelem = $(this);
			var host = $(curelem).attr('data-host');
			var url = 'http://' + host + '/index.php?option=com_virtualdomains';
			 $.getJSON( url).done(function( response ) {
				 var data = jQuery.parseJSON(response);
				 if(host == data.hostname) {
					 $(curelem).html('OK')
					 $(curelem).css('color','green');
				 } else {
					 $(curelem).html('Redirection won\'t work!!');
					 $(curelem).css('color','red');
				 }
				 console.log(host + ' = ' + data.hostname);
			}).fail(function() { 
				$(curelem).html('Foreign Host or redirection!');
				$(curelem).css('color','red');
				});
		})
	});
	
})(jQuery);
