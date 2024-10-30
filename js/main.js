jQuery(document).ready(function($){
	
	
	$(".convr input[name='where']").on('change', function(){
		var val = $(".convr input[name='where']:checked").val();
		
		if(val == 'some'){
			$('.convr .some').fadeIn(200);
		} else {
			$('.convr .some').fadeOut(200);
		}
		
	});
	
	
});