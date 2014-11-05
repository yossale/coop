<html>
<head>
	
<link rel="stylesheet" href="/css/admin.css" />
<script src="/js/jquery/jquery_latest.js"></script>
<script src="/js/jquery/validation/jquery.validate.min.js"></script>
<script src="/js/jquery/infra.js"></script>
<script src="/js/lightbox_order.js"></script>

</head> 
<body style="margin: 0;">

<div id="canvas">
	<div id="main_without_left_panel" style="margin: 0;">
				
		<!-- previous orders -->
		<div class="section" style="width: 800px;">
			<div class="title" style="float: right;">
				<h3>ההזמנה של {$order.user_first_name|escape:"html"|stripslashes} {$order.user_last_name|escape:"html"|stripslashes}</h3>
			</div>
			<div class="add-product" style="float: left;">
				<a>הוספת מוצר:</a>
				<input type="text" name="product-ac" size="30" />
				<a>(או Ctrl+A)</a>
			</div>
			<br class="clear_both" />
			<a>לדפדוף בין מוצרים השתמש בחיצים למעלה ולמטה. כשאתה מסמן מוצר לחץ Backspace להסרתו או Space לשינוי הכמות ואז Enter.</a>
			
			<form action="{$public_path}/duty/save-order" method="post">
			<input type="hidden" name="order_id" value="{$order.order_id}" />
			<input type="hidden" name="backto" value="duty-order/index/id/{$order.order_id}" />
			
			<div class="content list">
				<table style="width: 800px;">
					<th>מוצר</th>
					<th>כמות</th>
					<th>מחיר יח.</th>
					<th>סכום*</th>
					<th>תאור</th>			
					<th>יצרן</th>
				</tr>
				{foreach from=$cats item=cat}
				<tr>
					<td colspan="6" class="colspan" category_id="{$cat.category_id}">
						{$cat.category_name|escape:"html"|stripslashes}</td>
				</tr>
				{foreach from=$cat.products key=key item=product}	
				{if !empty($items[$product.product_id].item_amount)}
				<!--cycle values='row,rowalt' assign=cellClass -->
				<tr category_id="{$cat.category_id}">
					<td class="{$cellClass}">{$product.product_name|escape:"html"|stripslashes}</td>
					<td class="{$cellClass}">
						<input type="text" size="4" dir="ltr" maxlength="10" class="amount_input" 
							name="items[{$product.product_id}]" 
							product_id="{$product.product_id}" 
							value="{$items[$product.product_id].item_amount}"/>
							<!--<img src="/coopadmin/images/delete.png" />-->
 						{$product.product_measure|escape:"html"|stripslashes}</td>
					<td class="{$cellClass}">
						₪<a class="price" product_id="{$product.product_id}">
							{$product.product_price|escape:"html"|stripslashes}
						</a></td>
					<td class="{$cellClass}">
						₪<a class="amount_txt" product_id="{$product.product_id}">
							{$product.product_price * $items[$product.product_id].item_amount}</a>
					</td>
					<td class="{$cellClass}">{$product.product_description|escape:"html"|stripslashes}</td>
					<td class="{$cellClass}">{$product.product_manufacturer|escape:"html"|stripslashes}</td>
				</tr>
				{/if}
				{/foreach}
				{/foreach}
				</table>		
				<br class="clear_both" />
				
					<div id="total" style="float: left; margin-left: 10px; margin-top: 4px;">
						<label>סכום משוער:</label>
						<b id="total_amount">0</b>&nbsp;<b>₪</b>
					</div>
					<br />
					<br style="clear: both;" />
					<div style="float: left;">
						<input type="submit" value="שמור וסגור" />
						<a>Ctrl+S</a>
					</div>
			</div>
			
			</form>
			
		</div>
	</div>
</div>