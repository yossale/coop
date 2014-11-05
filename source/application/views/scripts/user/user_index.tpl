<!-- current order -->
{if $is_open}
<div class="section">
	<div class="title">
		<h3>ההזמנה השבועית</h3>
	</div>
	<div class="content">
		<a>שים לב - ההזמנה נסגרת ב{$closing_day} ב{$closing_time}</a><br />
		<div class="navigate">
			<a href="{$public_path}/user/current" class="go">להזמנה השבועית</a>
		</div>
	</div>
</div>
{else}
<div class="section">
האתר כרגע סגור להזמנות ויפתח ביום {$openning_day} ב{$openning_time}.
</div>
{/if}

<div class="section">
	<div class="title">
		<h3>עדכונים</h3>
	</div>
	<div class="content">{$updates}</div>
</div>

<!-- previous orders -->
<div class="section">
	<div class="title">
		<h3>הזמנות קודמות</h3>
	</div>
	{if $previous == false}
	אין הזמנות קודמות.
	{else}
	<div class="content list">
		<table>
			<th>תאריך</th>
			<th>תאריך עדכון אחרון</th>
			<th>נאספה?</th>
			<th>פירוט</th>
			{foreach from=$previous item=order}
			<tr>
				<td>{$order.order_date|date_format:"%d/%m/%y"}</td>
				<td>{$order.order_date|date_format:"%d/%m/%y"}</td>
				<td>{if $order.order_status == "payed"}<a class="payed_txt">כן</a>{else}<a class="unpayed_txt">לא</a>{/if}</td>
				<td><a href="{$public_path}/user/prev-order/id/{$order.order_id}" class="go">לחץ לפירוט</a></td>				
			</tr>
			{/foreach}
		</table>		
	</div>
	{/if}
</div>

