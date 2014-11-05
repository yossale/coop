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
	<h1>הזמנה לחקלאי - {$date}</h1>

{if $report == null}
	אין אף מוצר להזמין השבוע.
	{else}
	<table border="1" width="200">
		<thead>
			<th>מוצר</th>
			<th>כמות</th>
		</thead>
		<tbody>
			{foreach from=$report item=row}
			<tr>
				<td>{$row.product_name|escape:"html"|stripslashes}</td>
				<td>{$row.amount} {$row.product_measure|escape:"html"|stripslashes}</td>
			</tr> 
			{/foreach}	
		</tbody>
	</table>	
	{/if}

</body>
</html>
