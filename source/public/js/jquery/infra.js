$(document).ready(function(){

	// delete
	$(".delete").click(function() {
		var confirm_msg = $(this).attr("confirm_msg");
		var link = $(this).attr("link");
		var answer = confirm(confirm_msg);
		if (answer)
		{
			$.ajax({
				url: link,
				dataType: 'json',
				success: function(data)
				{
					if (data.success == '1')
					{
						location.reload();
					}
					else
					{
						alert('נמצאה שגיאה בעת ביצוע המחיקה');
					}
				}
			});
		}
	});
	
	// validate form
	$(".validate_me").validate();
	
});