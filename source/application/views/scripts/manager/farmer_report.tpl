<!-- weekly report -->
<div class="section">
	<div class="title">
		<h3>הזמנה לחקלאי</h3>
	</div>
		<div class="navigate">
			<a href="{$public_path}/manager/print-farmer-order" class="print" target="_blank">גרסה להדפסה</a>
		</div>
	{if $report == null}
	אין אף מוצר להזמין השבוע.
	{else}
	<div class="content list">
		<table>
			<th>מוצר</th>
			<th>כמות</th>
			{assign var="current" value=""}

			{foreach from=$report item=row}
			{if $current != $row.category_id}
			<tr>
				<td colspan="2" class="colspan">{$row.category_name|escape:"html"|stripslashes}</td>
			</tr>
			{assign var="current" value=$row.category_id}
			{/if}
			<tr>
				<td>{$row.product_name|escape:"html"|stripslashes}</td>
				<td>{$row.amount} {$row.product_measure|escape:"html"|stripslashes}</td>
			</tr> 
			{/foreach}
		</table>		
	</div>
	{/if}
</div>

