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

	var $stockItemsForm = $("#stockItemsForm");
	var $messages = $("#messages");

	$stockItemsForm.areYouSure();
	$stockItemsForm.bind('dirty.areYouSure', function() {
		$messages.text("ישנם שינויים לא שמורים בטופס.");
		$messages.toggleClass('messages-warning', true);
	});
	$stockItemsForm.bind('clean.areYouSure', function() {
		$messages.text("");
		$messages.toggleClass('messages-warning', false);
	});
});
