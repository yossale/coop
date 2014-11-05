<html>
<head>
	<title>טבלת סיכום</title>
	<link rel="stylesheet" href="{$css_path}/print.css" />
	<script>
		//window.print();
	</script>
        <style>
        *
        {
            font-size: 20px;
            }
            td, th
            {
                padding: 5px;
                
                }
        </style>
</head>
<body>

<div id="print">
	<table border="1" width="700">
		<thead>	
			<th>מוצר</th>
			<th>הזמנה שבועית</th>
		</thead>
		<tbody>
		{foreach from=$report item=row}
		{if $current != $row.category_id}
		<tr>
		<td colspan="9">
			<table>
				<tr>
					<td>{$row.category_name|escape:"html"|stripslashes}</td>
					<td align="left"><b>סה"כ תשלום לספק: </b>₪{$sum[$row.category_id]}</td>
				</tr>
			</table>
		</td>
		</tr>
		{assign var='current' value=$row.category_id}
		{/if}
		<tr>
			<!-- product name -->
			<td style="padding-left: 20px;">
                            {$row.product_name|escape:"html"|stripslashes}
                        </td>
			
			<!-- weekly order amount -->
			<td>{if $row.weekly_order != ""}
				{$row.weekly_order|string_format:"%.1f"} {$row.product_measure|escape:"html"|stripslashes}{else}ללא{/if}</td>
			
		</tr> 
		{/foreach}
		</tbody>
	</table>
</div>

</body>
</html>
