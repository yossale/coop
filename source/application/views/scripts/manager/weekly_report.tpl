<script src="{$js_path}/jquery/datepicker/date.js" /></script>
<!--[if IE]><script src="{$js_path}/jquery/datepicker/jquery.bgiframe.js" /></script><![endif]-->
<script src="{$js_path}/jquery/datepicker/jquery.datePicker.js" /></script>
<link rel="stylesheet" href="{$js_path}/jquery/datepicker/datePicker.css" />
<script src="{$js_path}/weekly_report.js"></script>


<!-- weekly report -->
<div class="section">
	<div class="title">
		<h3>טבלת סיכום שבועית</h3>
	</div>
	<div>
		<form action="" method="post">
			<label>עבור לשבוע שהתאפס בתאריך:</label>
			&nbsp;<select name="reset_day">
				<option value="">בחר...</option>
				{foreach from=$reset_days item=foo}
				<option value="{$foo}" {if $current == $foo}selected{/if}>{$foo|date_format:"%d/%m/%y"}</option>
				{/foreach}
			</select>
			&nbsp;<input type="submit" value="שנה" />
		</form>	
	</div>
	{if $report != null}
	
	{if $isFarmer}
		<form action="{$public_path}/farmer/print" method="post" target="_blank"">
			<input type="hidden" name="reset_day" value="{$current}" />
			<input type="submit" value="גרסה להדפסה" />
		</form>
	{/if}

	{assign var='current' value='0'}
	<div class="content list">
		<table>
			<th>מוצר</th>
			{if !$isFarmer}<th>מחיר לחבר</th>{/if}
			<th>עלות לקואופ</th>
			<th>הזמנה שבועית</th>
			<th>כמה סופק</th>
			<th>תשלום לספק</th>
			{if !$isFarmer}<th>הכנסות</th>{/if}
			{if !$isFarmer}<th>רווח</th>{/if}
			<th>הערות</th>
			{foreach from=$report item=row}
			{if $current != $row.category_id}
			<tr>
			<td colspan="{if $isFarmer}6{else}9{/if}" class="colspan" style="padding: 0;">
				<table width="100%" style="border: 0;">
					<tr>
						<td style="background-color: black; background-image: none; color: white; border: 0;" >
							{$row.category_name|escape:"html"|stripslashes}				
						</td>
						<td align="left" style="background-color: black; background-image: none; color: white; border 0; " >
							<b>סה"כ תשלום לספק: </b>₪{$sum[$row.category_id]}
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <b>תשלום שהגיע מהחברים: </b>₪{$user_payments[$row.category_id]}
						</td>
					</tr>
				</table>
			</td>
			</tr>
			{assign var='current' value=$row.category_id}
			{/if}
			<tr>
				<!-- product name -->
				<td><a class="product" id="{$row.product_id}"
					{if !$isFarmer} href="javascript:void(0);" style="color: blue;"{/if}>
					{$row.product_name|escape:"html"|stripslashes}</a></td>
					
				<!-- price for members -->
				{if !$isFarmer}
				<td><a href="javascript:void(0);" title="מחיר מ-{$prices[$row.product_id]['price_date']|date_format:"%d/%m/%y"}">₪{$prices[$row.product_id]['price_amount']|string_format:"%.1f"}</a> ל{$row.product_measure|escape:"html"|stripslashes}</td>
				{/if}
				
				<!-- cost for coop -->
				<td>₪{$row.product_coop_cost} ל{$row.product_measure|escape:"html"|stripslashes}</td>
				
				<!-- weekly order amount -->
				<td>{if $row.weekly_order != ""}
					{$row.weekly_order|string_format:"%.1f"} {$row.product_measure|escape:"html"|stripslashes}{else}ללא{/if}</td>
				
				<!-- stock -->
				<td>{if $stock[$row.product_id] != null}{$stock[$row.product_id]} {$row.product_measure|escape:"html"|stripslashes}{else}ללא{/if}</td>
				
				<!-- pay for supplier (stock * cost for coop) -->
				<td>₪{$row.product_coop_cost * $stock[$row.product_id]|string_format:"%.1f"}</td>
				
				<!-- pay for member (stock * price for members) -->
				{if !$isFarmer}
				<td>₪{$row.product_price * $stock[$row.product_id]|string_format:"%.1f"}</td>
				{/if}
				
				<!-- profit (stock * price for members) -->
				{if !$isFarmer}
				<td>₪{($prices[$row.product_id]['price_amount'] - $row.product_coop_cost) * $stock[$row.product_id]|string_format:"%.1f"}</td>
				{/if}
				
				<!-- comments -->
				<td>{$comments[$row.product_id]|escape:"html"|stripslashes}</td>
				
			</tr> 
			{if !$isFarmer}
			<tr>
				<td colspan="9" class="orders" id="{$row.product_id}">
					<table>
						<th>שם</th>
						<th>טלפון</th>
						<th>תאריך עדכון אחרון</th>
						<th>כמות</th>
						<th>עלות</th>
						<th>דף הזמנה</th>
						<th>סטטוס</th>
						{foreach from=$row.orders item=order}
						<tr>
							<td>{$order.user_first_name|escape:"html"|stripslashes} {$order.user_last_name|escape:"html"|stripslashes}</td>
							<td>{$order.user_phone|escape:"html"|stripslashes}</td>
							<td>{$order.order_last_edit|date_format:"%d/%m/%y %H:%M"}</td>
							<td>{$order.item_amount} {$row.product_measure|escape:"html"|stripslashes}</td>
							<td>₪{$order.order_amount|string_format:"%.1f"}</td>
							<td><a target="_blank" href="{$public_path}/duty/view-order/id/{$order.order_id}" class="go">עבור להזמנה</a></td>
							<td>{if $order.order_status == "unpayed"}לא שולם{else}שולם{/if}</td>
						</tr>
						{/foreach}
					</table>	
				</td>
			</tr>
			{/if}
			{/foreach}
		</table>		
	</div>
	{/if}
</div>

