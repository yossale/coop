$(document).ready(function(){
	$(".fckme").ckeditor({
		height: 800,
		contentsLangDirection: 'rtl',
		toolbar: [
		     ['Source'],['Templates'],
    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
    '/',
    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote','CreateDiv'],
    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
    ['BidiLtr', 'BidiRtl' ],
    ['Link','Unlink','Anchor'],
    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak','Iframe'],
    '/',
    ['Styles','Format','Font','FontSize'],
    ['TextColor','BGColor']
		],
		image_previewText: CKEDITOR.tools.repeat( ' ', 100 )
	}); 
	
	var last = "";
	
	function timeout()
	{
		var html = $(".fckme").val();
		if (html != last)
		{
			$.ajax({
				type: 'POST',
				url: public_path + '/duty/duty-reports',
				data: {
					report_content: html,
					ajax: "1"
				},
				success: function(res) {
					console.log(res);
				}
			});
			last = html;
		}
		$.doTimeout('someid', 1 * 1000, timeout);
	}
	timeout();

});