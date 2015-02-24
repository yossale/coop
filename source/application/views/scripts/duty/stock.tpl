<script>var public_path = '{$public_path}';</script>
<script src="{$js_path}/weekly_report.js"></script>
<script src="{$js_path}/jquery.are-you-sure.js"></script>

<div class="section">
	<div class="title">
		<h3>מלאי</h3>
	</div>
	<div id="messages" class="messages">
	</div>
	<div>
		<form action="{$public_path}/duty/stock" method="post">
			<label>עבור לשבוע שהתאפס בתאריך:</label>
			&nbsp;<select name="reset_day">
				<option value="">בחר...</option>
				{foreach from=$reset_days item=foo}
				<option value="{$foo}" {if $current_date == $foo}selected{/if}>{$foo|date_format:"%d/%m/%y"}</option>
				{/foreach}
			</select>
			&nbsp;<input type="submit" value="שנה" />
		</form>
	</div>
	{assign var='current' value='0'}
	<div class="content list">
		<form id="stockItemsForm" name="stockItemsForm" action="" method="post" class="validate_me">
                    <input type="hidden" name="reset_day" value="{$current_date}" />
		<table>
			<th>מוצר</th>
			<th>הזמנה שבועית</th>
			<th>כמה הגיע</th>
			<th>הערות</th>
			{foreach from=$products item=row}
			{if $current != $row.category_id}
			<tr>
			<td colspan="8" class="colspan">{$row.category_name|escape:"html"|stripslashes}</td>
			</tr>
			{assign var='current' value=$row.category_id}
			{/if}
			<tr>
				<td><a class="product" id="{$row.product_id}" href="javascript:void(0);"
					style="color: blue;">{$row.product_name|escape:"html"|stripslashes}</a></td>

				<td>{$row.weekly_order|string_format:"%.1f"}
					{$row.product_measure|escape:"html"|stripslashes}</td>

				<td><input size="5" dir="ltr"class="required number"
					name="stock[{$row.product_id}]"
					value="{if $stock[$row.product_id] == null}{$row.weekly_order|string_format:"%.1f"}{else}{$stock[$row.product_id]}{/if}" />
					{$row.product_measure|escape:"html"|stripslashes}</td>

				<td><input size="40" name="comments[{$row.product_id}]"
					value="{$comments[$row.product_id]|escape:"html"|stripslashes}" /></td>
			</tr>
			<tr>
				<td colspan="8" class="orders" id="{$row.product_id}">
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
			{/foreach}
		</table>
		<input type="submit" value="שמור" />
	</form>
	</div>
</div>
