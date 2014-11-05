$(document).ready(function() {
	
	positionFooter(); 
	function positionFooter(){
		$("#order_bottom_sticky").css({position: "absolute",top:($(window).scrollTop()+$(window).height()-$("#order_bottom_sticky").height())+"px"})	
	}
 
	$(window)
		.scroll(positionFooter)
		.resize(positionFooter)
	
	calcTotalAmount();
		
	function calcTotalAmount()
	{
		var total = 0;
		$(".amount_txt").each(function() {
			total += parseFloat($(this).html());
		});
                
		$("#total_amount").html(total.toFixed(2));
	}
	
	$(".amount_input").keyup(function() {
		var amount = $(this).attr("value");
		if (amount == "") amount = 0;
		var items_left = $(this).attr("items_left");		
		var product_id = $(this).attr("product_id");
		var price = $(".price[product_id='" + product_id + "']").html();
		var original_amount = $(this).attr("original_amount");
		if (original_amount == "") original_amount = 0;
		
		
		if (isNaN(amount) || amount < 0)
		{
                        var length = $(this).val().length;
                        var result = $(this).val().substr(0, length-1);
			$(this).val(result);
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
	
	// display only line we ordered
	if (!allow_edit)
	{
		var canHide = new Array();
		$(".colspan").each(function() {
			canHide[$(this).attr("category_id")] = true;
		});
		
		$(".amount_input").each(function() {
			if ($(this).val() == "" || $(this).val() == "0")
			{
				if (view == "list")
				{
					$(this).parents("tr:first").remove();				
				}
				else if (view == "gallery")
				{
					$(this).parents("td:first").remove();									
				}
			}
		});
		
		$(".colspan").each(function() {
			var category_id = $(this).attr("category_id");
			if ($(".gallery_item[category_id=" + category_id + "], tr[category_id=" + category_id + "]").length == 0)
			{
				$(this).hide();
			}
		});
	}
        
        if (duty_editing)
        {
           var canHide = new Array();
            $(".colspan").each(function() {
                    canHide[$(this).attr("category_id")] = true;
            });

            $(".amount_input").each(function() {
                if ($(this).val() == "" || $(this).val() == "0")
                {
                    $(this).parents("tr:first").hide();
                }
            });

            $(".colspan").each(function() {
                var category_id = $(this).attr("category_id");
                var visibleItems = $("tr[category_id=" + category_id + "]").filter(function() { 
                    return $(this).css("display") != "none"; 
                });
                if (visibleItems.length == 0)
                {
                    $(this).hide();
                }
            });
            
            $(".rowalt").removeClass("rowalt").addClass("row");
            
            $("#products").combobox();

            $(".acinput").keyup(function(event) {
                if (event.which == 13)
                {
                    var id = $("#products").val();
                    if (id == "")
                        return;

                    $(".colspan[category_id=" + products[id].categoryID + "]").show();
                    $("tr[product_id="+ id + "]").show();                    
                    $("tr[product_id="+ id + "] td").removeClass("row").addClass("rowalt");                    
                    window.location = "#go_" + id;
                    $(".amount_input[product_id="+ id + "]").focus().select();
                    $(".acinput").val("");
                }
            });
            
            $(".acinput").css("width", "250");
            
            $(document).bind("keydown", "Ctrl+1", function() {
                $(".acinput").focus();
            });
            $("input, select").bind("keydown", "Ctrl+1", function() {
                $(".acinput").focus();
            });
            $(document).bind("keydown", "Ctrl+s", function() {
                $(".status").focus();
            });
            $("input").bind("keydown", "Ctrl+s", function() {
                $(".status").focus();
            });
            $(".acinput").focus();
        }

	$(".product_description").hide();

	$(".order_name").tooltip({
		bodyHandler: function() { 
	        var id = $(this).attr("id");
	       	var about = "";
	       	if ($(this).attr("hasImage") == "1")
	       	{
		       	about +=  "<img style='width: 270px;' src='/user/view-product-image/id/" + id + "' />";
	       	}
	       	about += $(".product_description[id=" + id + "]").html();
	       	return about;
	    },
	    showURL: false,
	    track: true,
	    extraClass: "floatingAboutExtra",
	    top: -10
	}).click(function() {
		var html = "<div id='basic-modal'><h3>" + $(this).html() + "</h3>";
       	if ($(this).attr("hasImage") == "1")
       	{			
			html += '<img src="/user/view-product-image/id/' + $(this).attr("id") + '" />';
		}
		html += "<p>"+ $(".product_description[id=" + $(this).attr("id") + "]").html() + "</p></div>";
		$.modal(html,
		{
			close: true,
			overlayClose: true,
			autoResize: false
		});
	});
	
	$("#order_view_type .change").click(function() {
		var answer = confirm("?אם כבר הזנת נתונים בהזמנה הם לא יישמרו. לשנות מצב תצוגה");
		if (answer)
		{
			$.post(change_view_type_url, { type: $(this).attr("id") }, function(data) {
				window.location.reload();
			});			
		}
	});
	

});