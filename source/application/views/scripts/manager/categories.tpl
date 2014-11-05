<!-- categories -->
<div class="section">
	<div class="title">
		<h3>קטגוריות</h3>
	</div>
	<div class="content">
		<div class="navigate">
			<a class="add" href="{$public_path}/manager/add-category">הוספת קטגוריה</a>
		</div>
		{if $categories != null}
		<div class="list">
		<table>
			<th>שם</th>
			<th>מיקום ברשימה</th>
			<th>אפשרויות</th>
			{foreach from=$categories item=row}
			<tr>
				<td>{$row.category_name|escape:"html"|stripslashes}</td>
				<td>{$row.category_list_position}</td>
				<td>
					<a href="{$public_path}/manager/edit-category/id/{$row.category_id}" class="edit">עריכה</a>
					<a href="#" confirm_msg="למחוק את הקטגוריה {$row.category_name|escape:"html"|stripslashes}?" link="{$public_path}/manager/delete-category/id/{$row.category_id}" class="delete">מחיקה</a>
				</td>
			</tr> 
			{/foreach}
		</table>	
		</div>	
		{else}
		<p>אין אף קטגוריה.</p>
		{/if}
	</div>
</div>

