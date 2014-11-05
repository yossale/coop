(function($) {
	$.fn.myImageUploader = function(options)
	{
		var settings = $.extend({
			uploadPath:		"",
			previewPath:	"",
			previewLink:	"",
			viewPath:		"",
			viewLink:		"",
			id:				"",
			show_preview:	false
		}, options);
		
		var uploadedPic = false;
		
		$(".myupload-view").live("click", function() {
			openImageWindow(settings.viewLink);
		});
		

		$(".myupload-preview").live("click", function() {
			openImageWindow(settings.previewLink);
		});
		
		$(".myupload-switch").live("click", function() {
			displayUpload($(this).parent());
		});
		
		$(".myupload-remove-view").live("click", function() {
			displayUpload($(this).parent());					
		});
		
		$(".myupload-remove-preview").live("click", function() {
			displayUpload($(this).parent());					
		});

		return this.each(function() {
			
			var id = settings.id;
			if (id != "")
			{
				settings.viewPath = settings.viewPath.replace("?", id);
				settings.viewLink = settings.viewLink.replace("?", id);
				settings.deleteLink = settings.deleteLink.replace("?", id);
				displayInfo($(this));
			}			
			else
			{
				displayUpload($(this));	
			}
		});
		
		function displayUpload(container)
		{
			
			if ($(container).next().attr("class") == "myupload-cancel")
			{
				$(container).next().remove();
			}
			
			if (uploadedPic)
			{
			//	$(container).after('<a href="#" class="myupload-cancel">ביטול ההחלפה</a>');							
			}
	
			uploader = new qq.FileUploader({
				element: document.getElementById($(container).attr("id")),
				action: settings.uploadPath,
				allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
				sizeLimit: (350 * 1024),
				debug: true,
				onComplete: function(id, fileName, responseJSON){
					uploadedPic = true;
					displayInfo(container);
				}
			});				
		}
		
		function displayInfo(container)
		{
			var actions = '<span class="myupload-ok"></span>&nbsp;&nbsp;';
			
			if (!settings.showPreview)
			{
				if (uploadedPic)
				{
					actions += '<a href="#" class="myupload-preview">צפה</a>&nbsp;&nbsp;';										
				}			
				else
				{
					actions += '<a href="#" class="myupload-view">צפה</a>&nbsp;&nbsp;';
				}
				
			}
			
			actions += '<a href="#" class="myupload-switch">החלף</a>&nbsp;&nbsp;';
			
			/*if (uploadedPic)
			{
				actions += '<a href="#" class="myupload-remove-preview">הסר</a>&nbsp;&nbsp;';					
			}
			else
			{
				actions += '<a href="#" class="myupload-remove-view">הסר</a>&nbsp;&nbsp;';					
			}*/

			if (settings.showPreview)
			{
				if (uploadedPic)
				{				
					actions += '<div id="myupload-preview-container"></div>';	
				}
				else
				{					
					actions += '<div id="myupload-view-container"></div>';	
				}
			}

			$(container).empty().html(actions);
			$("#myupload-preview-container").load(settings.previewLink);
			$("#myupload-view-container").load(settings.viewLink);
		}
			
		
		function openImageWindow(url)
		{
			window.open(url,  Math.floor(Math.random() * 1111), 'width=1000, height=800, scrollbars=yes');
		}
	}
	
})(jQuery);

