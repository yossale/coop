<!-- products -->
<div class="section">
	<div class="title">
		<h3>מוצרים</h3>
	</div>
	<div class="content">
		<div class="navigate">
			<a class="add" href="{$public_path}/manager/add-product">הוספת מוצר</a>
		</div>
		{if $categories == null}
		<p>אין אף קטגוריה.</p>
		{else}
		<div class="list">
		<table>
			<th>שם</th>
			<th>מחיר</th>
			<th>מחיר לקואופ</th>
			<th>יצרן</th>
			<th>במלאי</th>
			<th>במחסור?</th>
			<th>אפשרויות</th>
			{foreach from=$categories item=category}
			<tr>
				<td colspan="8" class="colspan">{$category.category_name|escape:"html"|stripslashes}</td>
			</tr>
			{foreach from=$category.products item=product}
			<tr style="height: 1px;">
				<td>{$product.product_name|escape:"html"|stripslashes}</td>
				<td>₪{$product.product_price} ל{$product.product_measure|escape:"html"|stripslashes}</td>
				<td>₪{$product.product_coop_cost}</td>				
				<td>{$product.product_manufacturer|escape:"html"|stripslashes}</td>				
				<td>{if $product.product_items_left == "ללא הגבלה"}{$product.product_items_left}{else}{$product.product_items_left-$product.orders_count} מתוך {$product.product_items_left}{/if}</td>				
				<td>{if $product.product_in_shortage == '1'}כן{else}לא{/if}</td>				
				<td>
					<a href="{$public_path}/manager/edit-product/id/{$product.product_id}"><img src="{$img_path}/edit.png" /></a>
					<a href="#" confirm_msg="למחוק את המוצר {$product.product_name|escape:"html"|stripslashes}?" link="{$public_path}/manager/delete-product/id/{$product.product_id}" class="delete">&nbsp;</a></td>
			</tr> 
			{/foreach}
			{/foreach}
		</table>		
		</div>
		{/if}
	</div>
</div>

