<div class="wizard" id="wizard-message">
	<h1>Contact Us</h1>
	<div class="wizard-card" data-cardname="personal">
		<h3>Name &amp; E-Mail address</h3>
		<div class="wizard-input-section">
			<p>To begin, please enter your full name below.</p>
			<div class="control-group">
				<input name="name" type="text" placeholder="Your name" />
			</div>
		</div>
		<div class="wizard-input-section">
			<p>Optionally, give us your email address if you want to get a response.</p>
			<div class="control-group">
				<input name="mail" type="text" placeholder="E-Mail address (optional)" />
			</div>
		</div>
	</div>
	<div class="wizard-card" data-cardname="group">
		<h3>Your request</h3>
		<div class="wizard-input-section">
			<p>Why are you contacting us? Which department do you want to contact?</p>
			<div class="control-group">
				<label id="advertising"><input type="radio" value="advertising" name="subject" checked>Advertising</label>
				<label id="business"><input type="radio" value="business" name="subject">My concern is business related</label>
				<label id="suggestions"><input type="radio" value="suggestions" name="subject">I want to give improvement suggestions</label>
				<label id="technicalproblem"><input type="radio" value="technicalproblem" name="subject">Other technical problem</label>
			</div>
		</div>
	</div>
	<div class="wizard-card" data-cardname="services">
		<h3>Leave us a message</h3>
		<div class="wizard-input-section">
			<p>Type your message to the selected department below.</p>
			<div class="control-group">
				<textarea name="message" placeholder="Write your message here ..." required></textarea>
			</div>
			<div id="contact-captcha"></div>
		</div>
	</div>
	<div class="wizard-error">
		<div class="alert alert-error">
			There was a <strong>problem</strong> with your submission.<br />
			Either your message is too short or to big or the Captcha is wrong.<br />
			Please correct the errors and re-submit.
		</div>
		<a class="btn send-new-mail pull-left">Try again</a>
		<a class="btn im-done pull-right">Abort</a>
	</div>
	<div class="wizard-failure">
		<div class="alert alert-error">
			There was a <strong>problem</strong> submitting your message.<br />
			Please try again in a minute.
		</div>
		<a class="btn send-new-mail pull-left">Try again</a>
		<a class="btn im-done pull-right">Abort</a>
	</div>
	<div class="wizard-success">
		<div class="alert alert-success">
			Your message has been <strong>successfully</strong> sent to us.
		</div>
		<a class="btn send-new-mail pull-left">Send another message</a>
		<a class="btn im-done pull-right">Done</a>
	</div>
</div>