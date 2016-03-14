/*
Скрипты для оформления блоков дополнительных полей в админке
*/
(function($, undefined){
	$(document).ready(function(){
		$('.more_input').click(function(){
			var $this = $(this);
			var clone = $this.parent().prev().find('input').last().clone().val('');
			var canvas = $this.parent().prev();
			var div = $('<div/>').addClass('input_div');
			var span = $('<span/>').addClass('delete_input');
			
			span.click(function(){
				deleteInput( div );
			});
			
			div.append(clone);
			div.append(span);
			canvas.append(div);
			clone.focus();
			return false;
		});

		$('.extra_field_text_clone .input_div .delete_input').click(function(){
			deleteInput( $(this).parent() )
		});
		
		function deleteInput($this){
			console.log($this);
			$this.remove();
			return false;
		};
	
	});
})(jQuery);