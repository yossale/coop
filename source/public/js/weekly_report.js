$(document).ready(function() {
	$(".orders").hide();
	
	var opened = new Array();
		
	$(".product").click(function() {
		var id = $(this).attr("id");
		var orders = $(".orders[id=" + id + "]");
		
		if (opened[id] == true)
		{
			orders.hide();
			opened[id] = false;
		}
		else
		{
			orders.show();
			opened[id] = true;
		}
		
	});
	
/*	
	$(".date").datePicker({
		startDate: '01/01/1990'
	});*/
});
