		<table  id="prices">
			<th>קטגוריה</th>
			<th>מוצר</th>
			<th>במחסור?</th>
			<th>מחיר לקואופ</th>
			<th>יחידות</th>
			{foreach from=$products item=product}
			<tr>
							<td>{$product.category_name|escape:"html"|stripslashes}</td>
				<td>{$product.product_name|escape:"html"|stripslashes}</td>
				<td>{if $product.product_in_shortage == '1'}True{else}False{/if}</td>
				<td>{$product.product_coop_cost}</td>
				<td>{$product.product_measure|escape:"html"|stripslashes}</td>				
			</tr>
			{/foreach}
		</table>		
