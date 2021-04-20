<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "//www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="//www.w3.org/1999/xhtml" class="edit">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="//{$CONFIG.STATIC_URL}/css/style.css" />
		<script type="text/javascript" src="//{$CONFIG.STATIC_URL}/scripts/module.base.js"></script>
		<title>{$CONFIG.TITLE} - Edit Entry</title>
		<script>
			{if $SUCCESS}
				opener.location.reload ();
				window.close ();
			{/if}
			$(document).ready (function () {
				$('select[name="type"] > option[value="{$ENTRY.type}"]').attr ('selected', true);
				
				{foreach item=GENRE from=$ENTRY.genres}
					$('select[name="genres[]"] > option[value="{$GENRE}"]').attr ('selected', true);
				{/foreach}
			});
		</script>
	</head>
	<body>
		<form method="post" class="form-horizontal">
			{if $smarty.post.imdb && !$SUCCESS}
				<div class="alert alert-error">
					<a href="#" class="close" data-dismiss="alert">&times;</a>
					An error occured during the process.
				</div>
			{/if}
			<div class="left">
				<div class="control-group">
					<label class="control-label" for="imdb"><strong>IMDB ID</strong><br /><small>Identifier</small></label>
					<div class="controls">
						<input type="text" name="imdb" id="imdb" maxlength="8" placeholder="tt0000000" value="tt{$ENTRY.MID}" readonly required />
						<label class="checkbox retail"><input type="checkbox" name="retail" id="retail" />Retail (for 24h)</label>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="name_de"><strong>German Title</strong><br /><small>Name of entry</small></label>
					<div class="controls">
						<input type="text" name="name_de" id="name_de" maxlength="255" placeholder="Unknown" value="{$ENTRY.name_de}" required />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="trailer_de"><strong>German Trailer</strong><br /><small>YouTube Trailer URL</small></label>
					<div class="controls">
						<input type="text" name="trailer_de" id="trailer_de" maxlength="255" placeholder="Unknown" value="{$ENTRY.trailer_de}" />
					</div>
				</div>
				<div class="control-group" style="height: 73px;">
					<label class="control-label" for="type"><strong>Type</strong><br /><small>Kind of media</small></label>
					<div class="controls">
						<select name="type" required>
							<option value="0">Cinema</option>
							<option value="1">Movie</option>
							<option value="2">Series</option>
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="year"><strong>Year</strong><br /><small>Year of production</small></label>
					<div class="controls">
						<input type="text" name="year" id="year" maxlength="4" placeholder="0" value="{$ENTRY.year}" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="plot_de"><strong>German Plot</strong><br /><small>Content description</small></label>
					<div class="controls">
						<textarea name="plot_de" id="plot_de">{$ENTRY.plot_de}</textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="duration"><strong>Duration</strong><br /><small>Given in minutes</small></label>
					<div class="controls">
						<input type="text" name="duration" id="duration" maxlength="3" placeholder="0" value="{$ENTRY.duration}" />
					</div>
				</div>
			</div>
			<div class="right">
				<div class="control-group">
					<label class="control-label" for="cover"><strong>Cover</strong><br /><small>Replacement URL for cover</small></label>
					<div class="controls">
						<input type="text" name="cover" id="cover" maxlength="255" placeholder="http://.../cover.jpg" value="{$smarty.post.cover}" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="name_en"><strong>English Title</strong><br /><small>Name of entry</small></label>
					<div class="controls">
						<input type="text" name="name_en" id="name_en" maxlength="255" placeholder="Unknown" value="{$ENTRY.name_en}" required />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="trailer_en"><strong>English Trailer</strong><br /><small>YouTube Trailer URL</small></label>
					<div class="controls">
						<input type="text" name="trailer_en" id="trailer_en" maxlength="255" placeholder="Unknown" value="{$ENTRY.trailer_en}" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="type"><strong>Genres</strong><br /><small>Multi select items through 'ctrl' button</small></label>
					<div class="controls">
						<select name="genres[]" multiple="multiple" size="3" required>
							{foreach item=GENRE from=$GENRES}
								<option value="{$GENRE.ID}">{$GENRE.name}</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="released"><strong>Release Date</strong><br /><small>Format like dd.mm.yyyy</small></label>
					<div class="controls">
						<input type="text" name="released" id="released" maxlength="10" placeholder="00.00.0000" value="{$ENTRY.released}" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="plot_en"><strong>English Plot</strong><br /><small>Content description</small></label>
					<div class="controls">
						<textarea name="plot_en" id="plot_en">{$ENTRY.plot_en}</textarea>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="refetch"><strong>IMDB Refetch</strong><br /><small>Refetches all content from IMDB</small></label>
					<div class="controls">
						<input type="checkbox" name="refetch" id="refetch" />
					</div>
				</div>
			</div>
			<div class="control-group submit">
				<div class="controls">
					<button type="submit" class="btn btn-success">Save</button>
					<button onClick="window.close (); return false;" class="btn btn">Cancel</button>
				</div>
			</div>
			<input type="hidden" name="SID" value="{$ENTRY.ID}" />
		</form>
	</body>
</html>