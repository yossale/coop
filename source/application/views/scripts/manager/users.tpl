<!-- products -->
<div class="section">
	<div class="title">
		<h3>ניהול משתמשים</h3>
	</div>
	<div class="content">
		<div class="navigate">
			<a href="{$public_path}/manager/add-user" class="add">הוספת משתמש</a>
		</div>
		{if $users != null}
		<div class="list">
		<table>
			<th>שם</th>
			<th>תפקיד</th>
			<th>סוג גישה</th>
			<th>כתובת</th>
			<th>טלפון</th>
			<th>E-mail</th>
			<th>אפשרויות</th>
			{foreach from=$users item=row}
			<tr>
				<td>{$row.user_first_name|escape:"html"|stripslashes} {$row.user_last_name|escape:"html"|stripslashes}</td>
				<td>{$row.user_job|escape:"html"|stripslashes}</td>
				<td>{if $row.user_access == 'USER'}משתמש{elseif $row.user_access == 'ONDUTY'}תורן{elseif $row.user_access == 'MANAGER'}מנהל{elseif $row.user_access == 'FARMER'}חקלאי{elseif $row.user_access == 'SUPER'}מנהל-על{/if}</td>
				<td>{$row.user_address|escape:"html"|stripslashes}</td>
				<td align="left">{$row.user_phone|escape:"html"|stripslashes}</td>
<!--				<td align="left">{$row.user_phone2|escape:"html"|stripslashes}</td>-->
				<td align="left">{$row.user_email|escape:"html"|stripslashes}</td>
				<td>
					<a href="{$public_path}/manager/edit-user/id/{$row.user_id}" class="edit">&nbsp;</a>
					<a href="#" confirm_msg="למחוק את {$row.user_first_name|escape:"html"|stripslashes} {$row.user_last_name|escape:"html"|stripslashes}?" link="{$public_path}/manager/delete-user/id/{$row.user_id}" class="delete">&nbsp;</a></td>
			</tr> 
			{/foreach}
		</table>		
		</div>
		{else}
		<p>אין אף משתמש כרגע.</p>
		{/if}
	</div>
</div>

