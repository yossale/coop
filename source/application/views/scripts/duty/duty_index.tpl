<script src="{$js_path}/jquery/datepicker/date.js" /></script>
<!--[if IE]><script src="{$js_path}/jquery/datepicker/jquery.bgiframe.js" /></script><![endif]-->
<script src="{$js_path}/jquery/datepicker/jquery.datePicker.js" /></script>
<link rel="stylesheet" href="{$js_path}/jquery/datepicker/datePicker.css" />
<script>
var public_path = "{$public_path}";
{literal}
$(document).ready(function() {
	$("#new_order").change(function() {
		var id = $(this).val();
		var url = public_path  + "/duty/new-order/id/" + id;
		window.location = url;
	});
	
	$(".date").datePicker({
		startDate: '01/01/1990'
	});
});
{/literal}
</script>
<!-- debt  -->
<div class="section">
	<div class="title">
		<h3>חובות</h3>
	</div>
	{if $debts == null}
	אין אף חוב כרגע.
	{else}
	<div class="content list">
		<table>
			<th>שם</th>
			<th>טלפון</th>
			<th>חוב</th>
			<th>תאריך</th>
			<th>הערות</th>
			<th>אפשרויות</th>
			{foreach from=$debts item=debt}
			<tr>
				<td>{$debt.user_first_name|escape:"html"|stripslashes} {$debt.user_last_name|escape:"html"|stripslashes}</td>
				<td>{$debt.user_phone|escape:"html"|stripslashes}</td>
				<td>₪{$debt.debt_amount}</td> 
				<td>{if $debt.debt_date != '0000-00-00'}{$debt.debt_date|date_format:"%d/%m/%y"}{/if}</td>
				<td>{$debt.debt_comments|escape:"html"|stripslashes}</td>
				<td><a href="{$public_path}/duty/remove-debt/user/{$debt.user_id}" class="delete" confirm_msg="להסיר את החוב על סך ₪{$debt.debt_amount} ל{$debt.user_first_name|escape:"html"|stripslashes} {$debt.user_last_name|escape:"html"|stripslashes}?">הסרת חוב</a></td>
			</tr>
			{/foreach}
		</table>		
	</div>
	{/if}
	<form action="{$public_path}/duty/add-debt" method="POST">
		<b>הוספת חוב:</b><br />
		<a>משתמש:</a>&nbsp;
		<select name="user_id">
			<option value="">בחר...</option>
			{foreach from=$users item=user}
			<option value="{$user.user_id}">{$user.user_first_name|escape:"html"|stripslashes} {$user.user_last_name|escape:"html"|stripslashes}</option>
			{/foreach}
		</select>
		&nbsp;&nbsp;
		<a>כמות:</a>
		<input type="text" dir="ltr" name="amount" size="4" />
		&nbsp;&nbsp;
		<a>תאריך:</a>
		<input type="text" dir="ltr" name="date" size="10" class="date" />
		&nbsp;&nbsp;
		<br /><a>הערות:</a>
		<input type="text" dir="rtl" name="comments" size="50" />
		<br />
		<input type="submit" value="הוסף" />
	</form>
</div>

<!-- waiting orders -->

<script>var status_url = '{$public_path}/duty/change-status/id/%orderid%/status/%status%';</script>
<script src="{$js_path}/status.js"></script>

<div class="section">
	<div class="title">
		<h3>הזמנות מחכות</h3>
	</div>
	<div class="content list">
		<div class="navigate">
			<a href="{$public_path}/duty/print" class="print" target="_blank">גרסה להדפסה</a>
		</div>
		<form action="javascript:void();">
			<b>עבור לדף ההזמנה של:</label>&nbsp;&nbsp;
			<select id="new_order">
				<option value="">בחר...</option>
				{foreach from=$users_without_order item=user}
				<option value="{$user.user_id}">{$user.user_first_name|escape:"html"|stripslashes} {$user.user_last_name|escape:"html"|stripslashes}</option>
				{/foreach}
			</select>
		</form><br /><br />
		{if !$orders}
		<a>אין הזמנות להשבוע.</a>
		{else}
		<table>
			<th>שם</th>
			<th>טלפון</th>
			<th>תאריך עדכון אחרון</th>
			<th>סכום משוער</th>
			<th>דף הזמנה</th>
			<th>סטטוס</th>
			{foreach from=$orders item=order}
			<tr>
				<td>{$order.user_first_name|escape:"html"|stripslashes} {$order.user_last_name|escape:"html"|stripslashes}</td>
				<td>{$order.user_phone|escape:"html"|stripslashes}</td>
				<td>{$order.order_last_edit|date_format:"%d/%m/%y %H:%M"}</td>
				<td>₪{$order.order_amount|string_format:"%.2f"}</td>
				<td><a href="{$public_path}/duty/view-order/id/{$order.order_id}" class="go">עבור להזמנה</a></td>
				<td>{include file='duty/change_status.tpl'}</td>
			</tr>
			{/foreach}
		</table>		
		{/if}
	</div>
</div>

