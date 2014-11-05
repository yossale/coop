<!-- category from -->
<div class="section">
	<div class="title">
		<h3>{if $category == null}הוספת קטגוריה{else}עריכת קטגוריה{/if}</h3>
	</div>
	<div class="content">
		<form action="" method="POST" class="form validate_me">
			<p>
				<label>שם:</label>
				<input type="text" name="category_name" value="{$category.category_name|escape:"html"|stripslashes}" class="required" size="40" />
			</p>
			<p>
				<label>מיקום:</label>
				<input type="text" name="category_list_position" size="5" dir="ltr" value="{$category.category_list_position}" / class="required number">
			</p>
			<p class="submit">
				<input type="submit" value="שמירת שינויים" />
			</p>			
		</form>
		<div class="navigate">
			<a href="{$public_path}/manager/categories" class="back">ביטול וחזרה</a>
		</div>
	</div>
</div>

