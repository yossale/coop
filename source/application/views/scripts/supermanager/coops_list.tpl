{literal}
<script>
$(document).ready(function() {
	$(".url").click(function() {
		$(this).select();
	});
});
</script>
{/literal}

<!-- coops -->
<div class="section">
	<div class="title">
		<h3>קואופרטיבים</h3>
	</div>
	<div class="content">
		<div class="navigate">
			<a class="add" href="{$public_path}/coops/add">הוספת קואופרטיב</a>
		</div>
		{if $list != null}
		<div class="list">
		<table>
			<th>שם</th>
			<th>אימייל</th>
			<th>מנהל</th>
			<th>כתובת אתר</th>
			<th>עמוד התחברות</th>
			<th>מעבר</th>
			<th>אפשרויות</th>
			{foreach from=$list item=row}
			<tr>
				<td>{$row.coop_name|escape:"html"|stripslashes}</td>
				<td>{$row.coop_email|escape:"html"|stripslashes}</td>
				<td>{$row.user_first_name|escape:"html"|stripslashes} {$row.user_last_name|escape:"html"|stripslashes}</td>
				<td><a href="{$row.coop_url|escape:"html"|stripslashes}" target="_blank">{$row.coop_url|escape:"html"|stripslashes}</a></td>
				<td><input class="url" type="text" value="/index/index/coop/{$row.coop_id}" dir="ltr" />
					&nbsp;<a href="/index/index/coop/{$row.coop_id}" target="_blank">(Link)</a>
				</td>
				<td>
					<form action="{$public_path}/index/index/coop/{$row.coop_id}" method="post">
						<input type="hidden" name="email" value="{$user.user_email|escape:"html"|stripslashes}" />
						<input type="hidden" name="password" value="{$user.user_password|escape:"html"|stripslashes}" />
						<input type="submit" value="עבור" />
					</form>
				</td>
				<td>
					<a href="{$public_path}/coops/edit/id/{$row.coop_id}" class="edit">עריכה</a>
					<a href="#" confirm_msg="למחוק את הקואופרטיב {$row.coop_name|escape:"html"|stripslashes}?" link="{$public_path}/coops/delete/id/{$row.coop_id}" class="delete">מחיקה</a>
				</td>
			</tr> 
			{/foreach}
		</table>	
		</div>	
		{else}
		<p>אין אף קואופרטיב.</p>
		{/if}
		<br />
	</div>
</div>

