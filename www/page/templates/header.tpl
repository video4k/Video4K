<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "//www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="//www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="Content-Language" content="en-US" />
		<meta http-equiv="language" content="en-US" />
		<meta name="description" content="{$CONFIG.DOMAIN} - The finest in Cinema, Movie and Series Streams. Watch the highest quality content for free!" />
		<meta name="keywords" content="{$CONFIG.DOMAIN}, Movies, Cinemas, Series, Shows, Serien, Filme, Download, Filme kostenlos, Stream, Online Stream, Kino, Filme kostenlos schauen" />
		<meta name="robots" content="index, follow, noarchive, nosnippet" />
		<meta name="revisit-after" content="3 days" />
		<meta name="fragment" content="!" />
		<base href="//{$CONFIG.DOMAIN}/" />
		<link rel="shortcut icon" type="image/x-icon" href="//{$CONFIG.STATIC_URL}/images/favicon.ico" />
		<link rel="stylesheet" type="text/css" href="//{$CONFIG.STATIC_URL}/css/style.css" />
		<script type="text/javascript" src="//{$CONFIG.STATIC_URL}/scripts/module.base.js"></script>
		<script type="text/javascript" src="//{$CONFIG.STATIC_URL}/scripts/module.page.js"></script>
		{if $USER.rights > 2}<script type="text/javascript" src="//{$CONFIG.STATIC_URL}/scripts/module.manage.js"></script>{/if}
		<script type="text/javascript" src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
		<script type="text/javascript">
			var ReCaptchaKey = '{$CONFIG.CAPTCHA_KEY_PUBLIC}';
			$(document).ready (function () { Page.Initialize (); });
			{if $NOTIFICATION}
				$(document).ready (function () {
					$('#notification > .modal-body').html ('{$NOTIFICATION}');
					$('#notification').modal ();
				});
			{/if}
		</script>
		<title>{$CONFIG.TITLE} - streams for you</title>
	</head>
	<body>
		<div id="header">
			<div class="wrapper">
				<ul class="navigation">
					<a href="/"><div class="logo"></div></a>
					<li><a href="#cinema">Cinema</a></li>
					<li><a href="#movies">Movies</a></li>
					<li><a href="#series">Series</a></li>
					<li class="dropdown">
						<a href="#updates" data-toggle="dropdown">Latest Updates</a>
						<ul class="dropdown-menu" id="date-selector">
							{section name=a loop=7}
								<li data-date="{($smarty.now - ($smarty.section.a.index * 0x15180))|date_format:"%d.%m.%G"}"><a href="#updates">{($smarty.now - ($smarty.section.a.index * 0x15180))|date_format:"%d.%m.%G"}</a></li>
							{/section}
						</ul>
					</li>
				</ul>
				<iframe class="fb-like" src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fvideo4k.to&width&layout=button_count&action=like&show_faces=true&share=false&height=21" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
				<div class="action-button">
					{if !$USER.VALID}
						<div class="btn-group">
							<button id="signin-show" class="btn btn-info dropdown-toggle" data-toggle="dropdown">Sign In&nbsp;<span class="caret"></span></button>
							<ul class="dropdown-menu">
								<form class="signin-box">
									<div class="input-prepend input-block">
										<span class="add-on"><i class="icon-user"></i></span>
										<input id="username" class="span2" id="prependedInput" type="text" placeholder="Username" />
									</div>
									<div class="input-prepend input-block">
										<span class="add-on"><i class="icon-lock"></i></span>
										<input id="password" class="span2" id="prependedInput" type="password" placeholder="Password" />
									</div>
									<div class="btn-group input-block">
										<button class="sign-in btn btn-warning" id="signin" type="submit">Sign In</button>
										<button class="btn" id="register">Create Account</button>
									</div>
								</form>
							</ul>
						</div>
					{else}
						<div class="btn-group">
							<button class="btn btn-info dropdown-toggle" data-toggle="dropdown"><i class="icon-user"></i>&nbsp;{$USER.name}&nbsp;<span class="caret"></span></button>
							<ul class="dropdown-menu">
								<li><a href="/account"><i class="icon-th-large"></i>&nbsp;My Account</a></li>
								{if $USER.rights > 1}<li><a href="/streams"><i class="icon-facetime-video"></i>&nbsp;Add Streams</a></li>{/if}
								{if $USER.rights > 2}
									<li class="divider"></li>
									<li><a href="/database"><i class="icon-book"></i>&nbsp;Database</a></li>
									<li><a href="/releases"><i class="icon-tags"></i>&nbsp;Release Tags</a></li>
									<li><a href="/users"><i class="icon-tasks"></i>&nbsp;Users</a></li>
								{/if}
								<li class="divider"></li>
								<li><a href="/logout"><i class="icon-arrow-left"></i>&nbsp;Logout</a></li>
							</ul>
						</div>
					{/if}
				</div>
			</div>
			<div id="slider-wrapper">
				<div id="movie-slider" class="touchcarousel grey-blue">
					<ul class="touchcarousel-container">
						{foreach item=ENTRY from=$SLIDER}
							<li class="touchcarousel-item">
								<a class="item-block" href="#tt{$ENTRY.MID}">
									<img src="//{$CONFIG.STATIC_URL}/covers/{$ENTRY.cover}" alt="{$ENTRY.name}" width="170" height="230" />
									<div class="info">{$ENTRY.name}</div>
								</a>
							</li>
						{/foreach}
					</ul>
				</div>
			</div>
			<div class="search_holder">
				<input type="text" />
			</div>
			<div class="fancy_placeholder">Searching for something specific?</div>
		</div>
		<div id="content">
			<div id="notification" class="modal hide fade" role="dialog" aria-hidden="true">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3>Notification</h3>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer">
					<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				</div>
			</div>
			<div id="register-form" class="modal hide fade">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<h3>Create Account</h3>
				</div>
				<div class="modal-body">
					<form class="form-horizontal signup">
						<div class="control-group">
							<label class="control-label" for="name"><strong>Your Username</strong></label>
							<div class="controls">
								<input type="text" name="name" id="name" maxlength="12" placeholder="Your Username" value="" required />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="secret"><strong>Password</strong></label>
							<div class="controls">
								<input type="password" name="secret" id="secret" maxlength="20" placeholder="Choose your password" value="" required />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="confirm"><strong>Confirm Password</strong></label>
							<div class="controls">
								<input type="password" name="confirm" id="confirm" maxlength="20" placeholder="Repeat your password" value="" required />
							</div>
						</div>
						<div class="control-group" id="register-captcha"></div>
						<div class="alert alert-info">Fill out all the necessary fields and click on the "Sign Up" button.</div>
					</form>
				</div>
				<div class="modal-footer">
					<button class="btn left" data-dismiss="modal" aria-hidden="true">Close</button>
					<button class="btn btn-success right" id="signup">Sign Up</button>
				</div>
			</div>