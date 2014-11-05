<!-- List of debts of Coop members -->
<div class="section">
	<div class="title">
		<h3>חובות</h3>
		<p>רשימת כל החברים בעלי חוב (לחיוב או לזיכוי):</p>
	</div>
	<div class="content">
		{if $users != null}
		<div class="list">
		<table>
			<th>שם</th>
			<th>חוב</th>
			{foreach from=$users item=row}
			{if $row.user_comments != NULL}
			<tr>
				<td><a href="{$public_path}/manager/edit-user/id/{$row.user_id}">{$row.user_first_name|escape:"html"|stripslashes} {$row.user_last_name|escape:"html"|stripslashes}</a></td>
				<td>{$row.user_comments|escape:"html"|stripslashes}</td>
			</tr>
			{/if}
			{/foreach}
		</table>		
		</div>
		{else}
		<p>אין אף משתמש כרגע.</p>
		{/if}
	</div>
</div>

