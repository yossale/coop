$(document).ready(function() {
	
	$(".status").change(function() {
		var order_id = $(this).attr("order_id");
		var status = $(this).val();
		var url = status_url;
		url = url.replace("%orderid%", order_id);
		url = url.replace("%status%", status);
		
		$.get(url);
		
		$(".saved[order_id='" + order_id + "']").show();
	});
	
	$(".saved").each(function() {
		$(this).hide();
	});
	
});