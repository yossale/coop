<!-- users from -->
<div class="section">
	<div class="title">
		<h3>עריכת הפרטים שלי</h3>
	</div>
	<div class="content">
		<form action="" method="POST" class="form validate_me">
			<p>
				<label>שם פרטי:</label>
				<input type="text" name="user_first_name" value="{$user.user_first_name|escape:"html"|stripslashes}" class="required" />
			</p>
			<p>
				<label>שם משפחה:</label>
				<input type="text" name="user_last_name" value="{$user.user_last_name|escape:"html"|stripslashes}" class="required" />
			</p>
			<p>
				<label>סיסמא:</label>
				<input type="text" name="user_password" dir="ltr" class="required" value="{if $zf_action == 'add-user'}{$default_password}{else}{$user.user_password|escape:"html"|stripslashes}{/if}" />
			</p>
			<p>
				<label>טלפון:</label>
				<input type="text" name="user_phone" value="{$user.user_phone|escape:"html"|stripslashes}" dir="ltr" class="required" />
			</p>
			<p>
				<label>טלפון נוסף:</label>
				<input type="text" name="user_phone2" value="{$user.user_phone2|escape:"html"|stripslashes}" dir="ltr" />
			</p>
			<p>
				<label>E-mail:</label>
				<input type="text" name="user_email" value="{$user.user_email|escape:"html"|stripslashes}" size="40" dir="ltr" class="required email" />
			</p>
			<p>
				<label>E-mail #2:</label>
				<input type="text" name="user_email2" value="{$user.user_email2|escape:"html"|stripslashes}" size="40" dir="ltr" />
			</p>
			<p>
				<label>E-mail #3:</label>
				<input type="text" name="user_email3" value="{$user.user_email3|escape:"html"|stripslashes}" size="40" dir="ltr" />
			</p>
			<p>
				<label>E-mail #4:</label>
				<input type="text" name="user_email4" value="{$user.user_email4|escape:"html"|stripslashes}" size="40" dir="ltr" />
			</p>
			<p>
				<label>כתובת:</label>
				<input type="text" name="user_address" value="{$user.user_address|escape:"html"|stripslashes}" />
			</p>
			<p class="submit">
				<input type="submit" value="אישור" />			
			</p>
		</form>
	</div>
</div>

