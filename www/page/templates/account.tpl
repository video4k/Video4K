<div id="page-wrapper" class="content-box account-wrap">
	<div class="header">{if $PROFILE.name == $USER.name}My Profile{else}{$PROFILE.name}'s Profile{/if}</div>
	<div class="wrapper">
		<div class="account">
			<div class="left">
				<h1>General</h1>
				<h2>Display name</h2>
				<h3>{$PROFILE.name}</h3>
				<h2>Account type</h2>
				{if $PROFILE.rights == 0}
					<h4 class="inactive">Inactive Member</h3>
				{elseif $PROFILE.rights == 1}
					<h4 class="member">Active Member</h3>
				{elseif $PROFILE.rights == 2}
					<h4 class="active">Trusted Member</h3>
				{elseif $PROFILE.rights == 3}
					<h4 class="trusted">Moderator</h3>
				{elseif $PROFILE.rights == 4}
					<h4 class="admin">Administrator</h3>
				{/if}
			</div>
			<div class="right">
				<h1>Account activity</h1>
				<div class="left">
					<h2>Register date</h2>
					<h3>{$SIGNUP} (DD.MM.YYYY)</h3>
					<h2>Last login</h2>
					<h3>{$ACCESS} (DD.MM.YYYY)</h3>
				</div>
				<div class="right">
					<h2>Active Link(s)</h2>
					<h3>{$LINKS_COUNT}</h3>
				</div>
			</div>
		</div>
		{if $PROFILE.name == $USER.name}
		<form method="POST">
			<fieldset>
				<legend>Change password</legend>
				<input type="password" name="cpassword" placeholder="Current password" required />
				<input type="password" name="apassword" placeholder="New password" required />
				<input type="password" name="bpassword" placeholder="Retype new password" required />
				<span class="help-block">Type your current and the desired new password in the fields above.</span>
				<button type="submit" class="btn">Change password</button>
				<input type="hidden" name="action" value="password" />
			</fieldset>
		</form>
		{/if}
	</div>
</div>