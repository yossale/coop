<!-- js -->
<script>
var open_for_orders_url = '{$public_path}/manager/is-open-for-orders';
var open_for_orders_true = 'האתר מעכשיו פתוח להזמנות';
var open_for_orders_false = 'האתר מעכשיו סגור להזמנות';
var saved = {if $saved}true{else}false{/if};
{literal}
$(document).ready(function(){
	if (saved)
	{
		alert("השינויים נשמרו בהצלחה");
	}
	
	$("#open_for_orders_checkbox").click(function(){
		var is_checked = 0;
		if ($(this).attr("checked"))
		{
			is_checked = 1;
		}
		$.ajax({
			url: open_for_orders_url + '/open/' + is_checked,
			dataType: 'json',
			success: function(data)
			{
				if (data.success == '1')
				{
					if (is_checked == 1)
					{
						alert(open_for_orders_true);
					}
					else
					{
						alert(open_for_orders_false);
					}
				}
				else
				{
					alert('נמצאה שגיאה בעת ביצוע הפעולה');
				}
			}
		});
	});

});
{/literal}
</script>

<!-- site open? -->
<div id="open_for_orders">
	<p>
		<input type="checkbox" {if $open_for_orders == '1'}checked{/if} id="open_for_orders_checkbox">
		<label>האתר פתוח להזמנות?</label>
	</p>
</div>
<!-- duty report locked? (NEED TO ADD JS)-->
{if $is_locked == '1'}
	<div id="open_for_report">
		<p>
			<input type="checkbox" {if $is_locked == '1'}checked{/if} id="open_for_report_checkbox">
			<label>דוח תורן נעול?</label>
		</p>
	</div>
{/if}

<!-- openning times -->
<div id="openning_times">
	<form action="" method="post">
		<h4>זמני פתיחת הקואופ</h4>
		<p>
			<b>פתיחה:</b>
			<label>יום</label>
			<select name="coop_open_day">{html_options options=$days selected=$coop.coop_open_day}</select>
			<label>שעה</label>
			<input name="coop_open_time" type="text" size="4" dir="ltr" value="{$coop.coop_open_time|truncate:5:""}" />
			<br />

			<b>סגירה:</b>
			<label>יום</label>
			<select name="coop_close_day">{html_options options=$days selected=$coop.coop_close_day}</select>
			<label>שעה</label>
			<input name="coop_close_time" type="text" size="4" dir="ltr" value="{$coop.coop_close_time|truncate:5:""}" />
			<br />			
		</p>	
		<p>
			<input type="submit" value="שמירה" />
		</p>
	</form>
</div>

<!-- reset -->
<div id="openning_times">
	<form action="" method="post">
		<p>
			<label>יום אתחול המערכת</label>
			<select name="coop_reset_day">{html_options options=$days selected=$coop.coop_reset_day}</select>
			<input type="submit" value="שמירה" />
		</p>	
	</form>
</div>
