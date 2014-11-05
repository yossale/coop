$(document).ready(function() {
	
	$(".colspan").each(function() {
		var category_id = $(this).attr("category_id");
		if ($("tr[category_id=" + category_id + "]").length == 0)
		{
			$(this).hide();
		}
	});
	
	calcTotalAmount();
	
	
	function calcTotalAmount()
	{
		var total = 0;
		$(".amount_txt").each(function() {
			total += parseFloat($(this).html());
		});
		$("#total_amount").html(total);
	}
	
	$(".amount_input").keyup(function() {
		
		var amount = $(this).attr("value");
		if (amount == "") amount = 0;
		
		var product_id = $(this).attr("product_id");
		var price = $(".price[product_id='" + product_id + "']").html();
		
		if (isNaN(amount) || amount < 0)
		{
			alert("נא לכתוב מספר גבוה או שווה לאפס");
			$(this).val("");
			return;
		}

		var cost = 0;
		if (!isNaN(amount) || amount > 0 || amount.length > 4)
		{
			cost = price * amount;
		}
		$(".amount_txt[product_id='" + product_id + "']").html(cost);
		
		calcTotalAmount();
	});
});