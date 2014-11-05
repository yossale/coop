<html>
<head>
	<title>הזמנות {$date}</title>
	<link rel="stylesheet" href="{$css_path}/print.css" />
	<script>
		window.print();
	</script>
</head>
<body>

<div id="print">

{foreach from=$orders item=order}
	<h1>{$order.user_first_name|escape:"html"|stripslashes} {$order.user_last_name|escape:"html"|stripslashes}</h1>
	<p>
		<b>כתובת:</b>&nbsp;<a>{$order.user_address|escape:"html"|stripslashes}</a>
		<b>טלפון:</b>&nbsp;<a>{$order.user_phone|escape:"html"|stripslashes}</a>
		<b>טלפון נוסף:</b>&nbsp;<a>{$order.user_phone2|escape:"html"|stripslashes}</a>
	</p>
	<table border="1" width="700">
		<thead>
			<th>מלא</th>
			<th>חלקי</th>
			<th>חסר</th>
			<th>יצרן</th>
			<th>שם מוצר</th>
			<th>כמות</th>
			<th>מחיר</th>
			<th>עלות</th>
		</thead>
		<tbody>
		{foreach from=$order.items item=item}
			{if $current_category != $item.category_id}
			<tr>
				<td colspan="8">{$item.category_name|escape:"html"|stripslashes}</td>
			</tr>
			{assign var=current_category value=$item.category_id}
			{/if}
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>{$item.product_manufacturer|escape:"html"|stripslashes}</td>
				<td>{$item.product_name|escape:"html"|stripslashes}</td>
				<td><b style="font-size: 200%;">{$item.item_amount|escape:"html"|stripslashes}</b></td>
				<td>₪{$item.product_price|escape:"html"|stripslashes} ל{$item.product_measure|escape:"html"|stripslashes}</td>
				<td>₪{$item.cost}</td>				
			</tr>
			{/foreach}
		</tbody>
	</table>
	<hr>
	<p><b>סה"כ לתשלום:</b>&nbsp;<a>₪{$order.total}</a></p>
	{/foreach}
</div>

</body>
</html>
