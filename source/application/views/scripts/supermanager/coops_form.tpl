<!-- category from -->
<div class="section">
	<div class="title">
		<h3>{if $data == null}הוספת קואופרטיב{else}עריכת קואופרטיב{/if}</h3>
	</div>
	<div class="content">
		<form action="" method="POST" class="form validate_me">
			<p>
				<label>שם:</label>
				<input type="text" name="coop_name" value="{$data.coop_name|escape:"html"|stripslashes}" class="required" />
			</p>
			<p>
				<label>אימייל:</label>
				<input type="text" name="coop_email" dir="ltr" value="{$data.coop_email|escape:"html"|stripslashes}" class="required">
			</p>
			<p>
				<label>כתובת:</label>
				<input type="text" name="coop_url" dir="ltr" value="{$data.coop_url|escape:"html"|stripslashes}" class="required">
			</p>
			{if $data == null}
			<b>פרטי מנהל הקואופרטיב</b>
			<p>
				<label>שם פרטי:</label>
				<input type="text" name="user[user_first_name]" class="required" />
			</p>
			<p>
				<label>שם משפחה:</label>
				<input type="text" name="user[user_last_name]" class="required" />
			</p>
			<p>
				<label>אימייל:</label>
				<input type="text" name="user[user_email]" dir="ltr" class="required" />
			</p>
			<p>
				<label>סיסמא:</label>
				<input type="text" name="user[user_password]" dir="ltr" class="required" />
			</p>

			{/if}
			<p class="submit">
				<input type="submit" value="שמירת שינויים" />
			</p>			
		</form>
		<div class="navigate">
			<a href="{$public_path}/coops" class="back">ביטול וחזרה</a>
		</div>
	</div>
</div>

