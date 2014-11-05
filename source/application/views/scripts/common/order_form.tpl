<script>
var allow_edit = {if $allow_edit}true{else}false{/if};
var duty_editing = {if $duty_editing}true{else}false{/if};
var change_view_type_url = "{$public_path}/user/change-view-type";
var view = '{$order_view_type}';
var public_path = '{$public_path}';
</script>
<link rel="stylesheet" href="{$js_path}/jquery/jquery-tooltip/jquery.tooltip.css" />
<link rel="stylesheet" href="{$js_path}/jquery/simplemodal/css/basic.css" />
<link rel="stylesheet" href="{$js_path}/jquery/simplemodal/css/basic_ie.css" />
<script src="{$js_path}/jquery/jquery.stickyscroll.js"></script>
<script src="{$js_path}/jquery/jquery-tooltip/jquery.tooltip.min.js"></script>
<script src="{$js_path}/jquery/simplemodal/jquery.simplemodal.1.4.1.min.js"></script>

{if $duty_editing}

<!-- autocomplete -->
<link rel="stylesheet" href="{$js_path}/jquery/jquery-ui/development-bundle/themes/base/jquery.ui.all.css">
<script src="{$js_path}/jquery/jquery-ui/development-bundle/ui/jquery.ui.core.js"></script>
<script src="{$js_path}/jquery/jquery-ui/development-bundle/ui/jquery.ui.widget.js"></script>
<script src="{$js_path}/jquery/jquery-ui/development-bundle/ui/jquery.ui.button.js"></script>
<script src="{$js_path}/jquery/jquery-ui/development-bundle/ui/jquery.ui.position.js"></script>
<script src="{$js_path}/jquery/jquery-ui/development-bundle/ui/jquery.ui.autocomplete.js"></script>
<script src="{$js_path}/jquery/jquery-hotkeys/jquery.hotkeys.js"></script>
<script src="{$js_path}/autocomplete_extensions.js"></script>

<script src="{$js_path}/jquery/ejs/ejs_production.js"></script>
<script>
var products = new Array();
{foreach from=$products item=product}
products[{$product.product_id}] = { 
    name: "{$product.product_name|escape:"html"|stripslashes}",
    price: "{$product.product_price|escape:"html"|stripslashes}",
    measure: "{$product.product_measure|escape:"html"|stripslashes}",
    description: "{$product.product_description|escape:"html"|stripslashes}",
    manufacturer: "{$product.product_manufacturer|escape:"html"|stripslashes}",
    categoryID: "{$product.category_id}"
};
{/foreach}

</script>

{/if}

<script src="{$js_path}/order.js"></script>

{if $allow_edit && !$is_open}
	האתר לא פתוח כרגע להזמנות
	{else}
	{if !$duty_editing}
<div id="order_view_type">
	<b>תצוגה:</b>&nbsp;
	<a class="{if $order_view_type == "list"}selected{else}change" href="#{/if}" id="list">רשימה</a>&nbsp;
	<a class="{if $order_view_type == "gallery"}selected{else}change" href="#{/if}" id="gallery">גלריה</a>&nbsp;
	<!--{$public_path}/user/change-view/type/gallery"-->
</div>{/if} 
<div id="order_form" class="list">
    <form action="{$public_path}/{$controller}/save-order" method="post">
	{if $duty_editing}
                <label style="position: relative; bottom: 3px;">מוצר:</label>&nbsp;&nbsp;
               <select id="products">
               <option value=""></option>
               {foreach from=$products item=product}
               <option value="{$product.product_id}">{$product.product_name|escape:"html"|stripslashes}</option>
               {/foreach}
                </select>  
                
                   <label style="position: relative; bottom: 3px; padding-right: 10px;">הערות למשתמש:</label>&nbsp;&nbsp;
            <input style="position: relative; bottom: 3px;" size="60" name="user_comments" value="{$order.user_comments|escape:"html"|stripslashes}" />
         <br /><b>טיפ:</b> תורנ/ית יקר/ה, השתמש/י בקיצורי המקלדת Ctrl+1 לחיפוש/הוספת מוצר ו-Ctrl+s לשינוי סטטוס (ומשם אפשר לעשות Tab ואנטר)<br /><br />
          
        {/if}
	<table style="width: 930px;">
	{if $allow_edit}
		<input type="hidden" name="order_id" value="{$order.order_id}" />
		{if $order.order_id == null}
			<input type="hidden" name="user_id" value="{$user_id}" />
		{/if}
		<input type="hidden" name="backto" value="{$backto}" />
	{/if}
       
	{if $order_view_type == "list"}
		<tr id="table-header">
			<th>מוצר</th>
			<th>כמות</th>
			<th>מחיר יח.</th>
			<th>סכום*</th>
			<th>תאור</th>			
			<th>יצרן</th>
		</tr>
	{/if}
		{foreach from=$cats item=cat}
		<tr>
			<td colspan="{if $order_view_type == "list"}7{else}4{/if}" class="colspan" category_id="{$cat.category_id}">{$cat.category_name|escape:"html"|stripslashes}</td>
		</tr>
		{foreach from=$cat.products key=key item=product}		
		{if !$allow_edit && empty($items[$product.product_id].item_amount)}
		
		{else}
		{if $order_view_type == "list"}
		{cycle values='row,rowalt' assign=cellClass}                
		<tr category_id="{$cat.category_id}" product_id="{$product.product_id}">
			<td class="{$cellClass}"><a name="go_{$product.product_id}" {if $product.product_image || $product.product_about != null}class="order_name" hasImage="{$product.product_image}" href="javascript:void(0);" {/if} id="{$product.product_id}">{$product.product_name|escape:"html"|stripslashes}</a></td>
			<td class="{$cellClass}"><input type="text" size="4" dir="ltr" maxlength="10" class="amount_input" name="items[{$product.product_id}]" product_id="{$product.product_id}" value="{$items[$product.product_id].item_amount}" original_amount="{$items[$product.product_id].item_amount}" items_left="{if $product.product_items_left == "ללא הגבלה"}unlimited{else}{$product.product_items_left-$product.orders_count}{/if}" {if !$allow_edit}disabled{/if} /> {$product.product_measure|escape:"html"|stripslashes}</td>
			<td class="{$cellClass}">₪<a class="price" product_id="{$product.product_id}">{$product.product_price|escape:"html"|stripslashes}</a></td>
			<td class="{$cellClass}">₪<a class="amount_txt" product_id="{$product.product_id}">{$product.product_price * $items[$product.product_id].item_amount}</a></td>
			<td class="{$cellClass}">{$product.product_description|escape:"html"|stripslashes}</td>
			<td class="{$cellClass}">{$product.product_manufacturer|escape:"html"|stripslashes}</td>
			
		</tr>
		{else}
		{capture name="column"}{math equation="x % 4" x=$key}{/capture}
		{if $smarty.capture.column == 0}<tr>{/if}
			<td class="gallery_item" category_id="{$cat.category_id}" style="width: 215px; padding: 0; margin: 0; border: 1px solid black; vertical-align: text-top; text-align: center;">
				<p class="title">
					<a class="title{if $product.product_image || $product.product_about != null} order_name" hasImage="{$product.product_image}" href="javascript:void(0);{/if}" id="{$product.product_id}">{$product.product_name|escape:"html"|stripslashes}</a><br />
					{if $product.product_description != null}<a class="description">{$product.product_description|escape:"html"|stripslashes}</a><br />{/if}
					<span class="show_manufacturer"><b>יצרן:</b> {$product.product_manufacturer|escape:"html"|stripslashes}</span><br />					
					{if $product.product_image}<img src="{$public_path}/user/view-product-image/id/{$product.product_id}" />{/if}<br />
					<span class="show_items_left"><b>במלאי:</b> {if $product.product_items_left == "ללא הגבלה"}{$product.product_items_left}{else}<a class="items_left" id="{$product.product_id}">{$product.product_items_left-$product.orders_count}</a> מתוך {$product.product_items_left}{/if}</span><br />
					<span class="show_price"><b>מחיר:</b> ₪<a class="price" product_id="{$product.product_id}">{$product.product_price|escape:"html"|stripslashes}</a> ל{$product.product_measure|escape:"html"|stripslashes}</span><br />
					<span class="show_amount"><b>כמות:</b> <input type="text" size="2" dir="ltr" maxlength="10" class="amount_input" name="items[{$product.product_id}]" original_amount="{$items[$product.product_id].item_amount}" items_left="{if $product.product_items_left == "ללא הגבלה"}unlimited{else}{$product.product_items_left-$product.orders_count}{/if}" product_id="{$product.product_id}" value="{$items[$product.product_id].item_amount}" {if !$allow_edit}disabled{/if} /></span><br />
					<span class="show_total"><b>עלות:</b> ₪<a class="amount_txt" product_id="{$product.product_id}">{$product.product_price * $items[$product.product_id].item_amount}</a></span>					
				</p>
				
			</td>
		{/if}
		{if $product.product_image || $product.product_about != null}
		<div class="product_description" id="{$product.product_id}">
			{if $product.product_about != null}{$product.product_about}{/if}
		</div>{/if}
		{/if}
		{/foreach}
		
		{/foreach}
	</table>
	
	{if $allow_edit}
	<div id="order_bottom_sticky">
		<div id="submit_form_container" class="sticky">
                    <input type="submit" id="submit_form" value="שמור" tabindex="3"/>	
		</div>
		<div id="sticky_total">
                    {if $duty_editing}
                    <label>סטטוס:</label>
                    {include file='duty/change_status.tpl'}
                    {/if}
                    &nbsp;&nbsp;&nbsp;
			<label>סכום:</label>
			<b id="total_amount">0</b>&nbsp;<b>₪</b>
		</div>
            <div id="sticky_right">
               <a href="{$public_path}/duty" tabindex="1"><< חזרה לרשימת ההזמנות</a>
            </div>
	</div>
	</form>
	{else}
	<div id="total" style="width: 930px;">
		<label>סכום משוער:</label>
		<b id="total_amount">0</b>&nbsp;<b>₪</b>
	</div>
	{/if}
</div>

	{/if}