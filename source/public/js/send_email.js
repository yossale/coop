$(document).ready(function(){
	$(".fckme").ckeditor({
		height: 300,
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
	
	var editor = $(".fckme").ckeditorGet();
	CKFinder.setupCKEditor( editor, js_path + '/jquery/ckfinder/' );
	
	$("#list_users").hide();
	
	$("input[value='specific_users']").click(function() {
		$("#list_users").show();
	});
	
	$("input[value='everyone']").click(function() {
		$("#list_users").hide();
	});
});