<div class="section">
	<div class="title">
            <h3>מחירים</h3>                
			<a href="{$public_path}/farmer/excel"><h4>יצא לאקסל</h4></a>
	</div>
        <div>
            <form action="{$public_path}/farmer/add-product" method="post" class="validate_me">
                
                <b style="margin-left: 10px;">הוספת מוצר:</b>
                
                <label>שם:</label>
                <input type="text" name="product_name" size="20" class="required" style="margin-left: 10px;" />
                
                <label>קטגוריה:</label>
                <select name="category_id" class="required" style="margin-left: 10px;">
                    <option value="">בחר...</option>
                    {html_options options=$category_options}
                </select>
                
                <label>מחיר לקואופ:</label>
                <input type="text" name="product_coop_cost" size="7" dir="ltr" class="required number" />
                <a>₪ ל:</a>
                <input type="text" name="product_measure" class="required" size="15" style="margin-left: 10px;"  />
                
                <input type="submit" value="אישור" />
            </form>
        </div>
	{if $products == false}
            <!-- todo: no product -->
	{else}
	<div class="content list">
		<form action="" method="post">
		<table  id="prices">
			<th>קטגוריה</th>
			<th>מוצר</th>
			<th>במחסור?</th>
			<th>מחיר לקואופ</th>
			{foreach from=$products item=product}
			<tr>
				<td>{$product.category_name|escape:"html"|stripslashes}</td>
				<td>{$product.product_name|escape:"html"|stripslashes}</td>
				<td><input type="checkbox" name="shortage[{$product.product_id}]" {if $product.product_in_shortage == '1'}checked{/if} /></td>
				<td><input size="5" dir="ltr"class="required number" name="prices[{$product.product_id}]" value="{$product.product_coop_cost}" />₪ ל{$product.product_measure|escape:"html"|stripslashes}</td>				
			</tr>
			{/foreach}
		</table>		
		<input type="submit" value="שמור" />
	</form>
	</div>
	<div>
	</div>
	{/if}
</div>

