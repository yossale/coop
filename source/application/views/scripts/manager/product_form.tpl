<link rel="stylesheet" type="text/css" media="screen" href="{$js_path}/jquery/file_uploader/client/fileuploader.css" />
<link rel="stylesheet" type="text/css" media="screen" href="{$js_path}/jquery/myImageUploader/myImageUploader.css" />
<script src="{$js_path}/jquery/file_uploader/client/fileuploader.js"></script>
<script src="{$js_path}/jquery/myImageUploader/myImageUploader.js"></script>
<script src="{$js_path}/jquery/ckeditor/ckeditor.js"></script>
<script src="{$js_path}/jquery/ckeditor/adapters/jquery.js"></script>
<script>
$(document).ready(function() {	
	$("#file-uploader").myImageUploader({
		uploadPath:			"{$public_path}/manager/product-image-upload",
		previewPath:		"{$public_path}/manager/product-image-preview",
		previewLink:		"{$public_path}/manager/product-image-preview-container",
		deletePreviewPink:	"{$public_path}/manager/product-image-preview-delete",
		
		{if $product.product_image}
		id:					"{$product.product_id}",
		viewPath:			"{$public_path}/manager/product-image-view/id/?",
		viewLink:			"{$public_path}/manager/product-image-view-container/id/?",
		deleteLink:			"{$public_path}/manager/product-image-delete/id/?",
		{/if}
		
		showPreview:		true
	});
	
	$("#product_about").ckeditor({
		height: 200,
		contentsLangDirection: 'rtl',
		toolbar: [
	['Undo','Redo'],
    ['Bold','Italic','Underline','Strike'],
    ['NumberedList','BulletedList', 'RemoveFormat']
		],
		image_previewText: CKEDITOR.tools.repeat( ' ', 100 )
	}); 

});	
</script>
<!-- products from -->
<div class="section">
	<div class="title">
		<h3>{if $product == null}הוספת מוצר{else}עריכת מוצר{/if}</h3>
	</div>
	<div class="content">
		<form action="" method="POST" class="form validate_me">
			<p>
				<label>שם:</label>
				<input type="text" name="product_name" size="40" class="required" value="{$product.product_name|escape|stripslashes}" />
			</p>
			<p>
				<label>קטגוריה:</label>
				<select name="category_id" class="required">
					<option value="">בחר...</option>
					{html_options options=$category_options selected=$product.category_id}
				</select>
			</p>
			<p>
				<label>מחיר:</label>
				<input type="text" name="product_price" size="7" dir="ltr" class="required number" value="{$product.product_price}" />
				<a>₪ ל:</a>
				<input type="text" name="product_measure" class="required" size="15" value="{$product.product_measure|escape|stripslashes}" />
			</p>
			<p>
				<label>עלות לקואופ:</label>
				<input type="text" name="product_coop_cost" size="7" dir="ltr" class="number" value="{$product.product_coop_cost}" />
				<a>₪</a>
			</p>	
			<p>
				<label>כמות במלאי:</label>
				<input type="text" name="product_items_left" size="7" value="{if $product.product_items_left == null}ללא הגבלה{else}{$product.product_items_left}{/if}" />
			</p>	
			<p>
				<label>תיאור:</label>
				<input type="text" name="product_description" size="50" value="{$product.product_description|escape|stripslashes}" />
			</p>	
			<p>
				<label>יצרן:</label>
				<input type="text" name="product_manufacturer" size="30" value="{$product.product_manufacturer|escape|stripslashes}" />
			</p>
			<p>
				<label>במחסור?</label>
				<input type="checkbox" name="product_in_shortage" value="1" {if $product.product_in_shortage == '1'}checked{/if} />
			</p>	
			<p>
				<label>תמונה:</label>
				<div id="file-uploader" style="float: right;"></div>
			</p>
			<br style="clear: both;" />
			<p>
				<label style="width: 400px;">תיאור מורחב (כפי שיוצג לצג התמונה):</label>
				<br />
				<textarea name="product_about" id="product_about">{$product.product_about|escape|stripslashes}</textarea>
			</p>
			<p class="submit">
				<input type="submit" value="שמירת שינויים" />
			</p>			
		</form>
		<div class="navigate">
			<a href="{$public_path}/manager/products" class="back">ביטול וחזרה</a>
		</div>
	</div>
</div>

