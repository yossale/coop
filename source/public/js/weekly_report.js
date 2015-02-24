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
	var saveTimer = null;
	var isDirty = false;

	$stockItemsForm.areYouSure();
	$stockItemsForm.bind('dirty.areYouSure', function() {
		isDirty = true;
		$messages.text("ישנם שינויים לא שמורים בטופס.");
		$messages.toggleClass('messages-warning', true);
	});
	$stockItemsForm.bind('clean.areYouSure', function() {
		isDirty = false;
		$messages.text("");
		$messages.toggleClass('messages-warning', false);
		if (saveTimer) {
			window.clearTimeout(saveTimer);
			saveTimer = null;
		}
	});
	$stockItemsForm.bind('activityDetected.areYouSure', function() {
		if (isDirty) {
			if (saveTimer) {
				window.clearTimeout(saveTimer);
				startAjaxSaveFormTimer($stockItemsForm);
			} else {
				startAjaxSaveFormTimer($stockItemsForm);
			} 
		}
	});
	
	function startAjaxSaveFormTimer($form) {
		saveTimer = window.setTimeout(function(){
			saveTimer = null;
			ajaxSaveForm($form);				
		}, 2000);
	};
	
	function ajaxSaveForm($form) {
		var formData = $form.serialize();
		formData = formData + '&isAjax=true';
		
		$.ajax({
			type: 'POST',
			url: public_path + '/duty/stock',
			data: formData,
			success: function(res) {
				$form.trigger('reinitialize.areYouSure');
			}
		});
	};
});
