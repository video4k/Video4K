<div id="page-wrapper" class="content-box streams-box">
	<div class="header">Add Streams</div>
	<div class="wrapper">
		<div class="well left">
			<ul class="nav nav-list">
				<li class="nav-header">Add Options</li>
				<li {if !$BULK}class="active"{/if}><a href="#" id="nav-manual">Single Entry</a></li>
				<li {if $BULK}class="active"{/if}><a href="#" id="nav-bulk">Bulk Upload</a></li>
				<li><a href="#" id="nav-api">API Upload</a></li>
			</ul>
		</div>
		<div class="well right">
			<div id="manual" {if $smarty.post.imdb || $BULK}style="display: none;"{/if}>
					<form method="POST">
						<div class="search-div input-append">
							<input name="search" id="appendedInputButton" type="text" placeholder="Title of the entry or IMDB ID" value="{$smarty.post.search}" />
							<button class="btn" type="submit">Search</button>
						</div>
					</form>
					<hr />
					{if $SEARCH}
						{if $SUCCESS_RESULT}<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>{$SUCCESS_RESULT}</div>{/if}
						{if $FAILED_RESULT}<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>{$FAILED_RESULT}</div>{/if}
						<form method="POST">
							<input type="hidden" name="search" value="{$smarty.post.search}" />							
							<select name="entry" id="entry" size="4" class="left">
							{foreach from=$SEARCH item=ENTRY}
								<option data-type="{$ENTRY.TYPE}" value="{$ENTRY.ID}">{$ENTRY.name}</option>
							{/foreach}
							</select>
							<div class="right form-horizontal">
								<div class="control-group">
									<label class="control-label">Stream Quality:</label>
									<div class="controls">
										<select name="quality" required>
											<option value="good">Good (DVD / BD)</option>
											<option value="medium">Medium (TS)</option>
											<option value="bad">Bad (CAM)</option>
										</select>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Language:</label>
									<div class="controls">
										<select name="language" required>
										{foreach from=$CONFIG.LANG item=ENTRY}
											<option value="{$ENTRY.ID}">{$ENTRY.text}</option>
										{/foreach}
										</select>
									</div>
								</div>
								<div id="series-data">
									<div class="control-group left">
										<label class="control-label">Season:</label>
										<div class="controls">
											<input type="text" pattern="[0-9.]+" name="season" value="{if $smarty.post.season}{$smarty.post.season}{else}1{/if}" maxlength="4"/>
										</div>
									</div>
									<div class="control-group right">
										<label class="control-label" style="width: 50px;">Episode:</label>
										<div class="controls" style="margin-left: 70px;">
											<input type="text" pattern="[0-9.]+" name="episode" value="{if $smarty.post.episode}{$smarty.post.episode}{else}0{/if}" maxlength="4"/>
										</div>
									</div>
								</div>
							</div>
							<textarea name="links" required>{$smarty.post.links}</textarea>
							<div class="left">Please insert only <b>one</b> link per line.</div>
							<button class="btn btn-success right" type="submit">Record Links</button>
						</form>
					{elseif $smarty.post.search && !$SEARCH}
						<center>
							<h4 class="no-results">Your search did not return any results.</h4>
							<h5 class="new-entry">Click <a href="#" id="nav-new"><u>here</u></a> to add a new entry.</h5>
						</center>
					{else}
						<center><h4 class="stream-info">Enter the media title or IMDB ID into the search for adding a stream.</h4></center>
					{/if}
			</div>
			<div id="new" {if !$smarty.post.imdb}style="display: none;"{/if}>
				{if $RESULT}
					<div class="alert {if $SUCCESS}alert-success{else}alert-error{/if}">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						{$RESULT}
					</div>
				{/if}
				<form method="post" class="new-form">
					<div class="form-horizontal">
						<div class="control-group">
							<label class="control-label">Type:</label>
							<div class="controls">
								<select name="type" required>
									<option value="c">Cinema</option>
									<option value="m">Movie</option>
									<option value="s">Series</option>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">IMDB ID:</label>
							<div class="controls">
								<input type="text" name="imdb" placeholder="tt0000000" value="{$smarty.post.imdb}" required />
								<button class="btn btn-warning" id="check-id">Check ID</button>
								<div class="loading-wrapper">
									<div class="loader-20"></div>
								</div>
							</div>
						</div>
						<hr />
						<div class="control-group">
							<label class="control-label">German Title:</label>
							<div class="controls">
								<input type="text" name="name_de" placeholder="Unknown" value="{$smarty.post.name_de}" style="width: 295px;" required />
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">English Title:</label>
							<div class="controls">
								<input type="text" name="name_en" placeholder="Unknown" value="{$smarty.post.name_en}" style="width: 295px;" required />
							</div>
						</div>
					</div>
					<hr />
					<button class="btn btn-success right" type="submit">Add Entry</button>
				</form>
			</div>
			<div id="bulk" {if !$BULK}style="display: none;"{/if}>
				{if $RESULT && !$smarty.post.imdb}
					<div class="alert {if $SUCCESS}alert-success{else}alert-error{/if}">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						{$RESULT}
					</div>
				{/if}
				<blockquote>
					<legend>Bulk Documentation</legend>
					<h5>With the help of the Bulk-System you can add streams a lot easier since all the needed information can be provided with a simple text file. All you have to do is to upload the prepared text file to the server.<br /><br />
					The text document has to look the following way:<br /></h5>
					<ul>
						<li>One line in the text file is one entry for the page. All Links to one entry are put into one line.</li>
						<li>Lines starting with "&#35;" are excluded from entering. They are reserved for comments.</li>
						<li>
							The information in one line is always separated by the symbol ( | ). In order to determine what information is entered there are a couple of identifiers which tell the system what information is entered. The value set to this information is separated by a ( : ).
							<ul>
								<li>
									<b>Type</b> (necessary) – Sets the type of the entry:
									<ul>
										<li><b>c</b> - Cinema</li>
										<li><b>m</b> - Movie</li>
										<li><b>s</b> - Series</li>
									</ul>
								</li>
								<li><b>IMDb</b> (necessary) - <a target="_blank" href="http://imdb.com/">IMDb-ID</a> (e.g. tt0418279) of the entry</li>
								<li><b>Lang</b> (necessary) - Audio language:
									<ul>
										{foreach from=$CONFIG.LANG item=ENTRY}
											<li><b>{$ENTRY.symbol}</b> - {$ENTRY.text}</li>
										{/foreach}
									</ul>
								</li>
								<li><b>Subtitle</b> (optional) – Language of the subtitle. Same values as <b>Lang</b> must be used.</li>
								<li><b>Link</b> (necessary) - URL of the mirror; this tag can be used a soften as needed in one line.</li>
								<li>
									<b>Quality</b> (necessary) - Quality of the streams, this information can only be added once per line:
									<ul>
										<li><b>good</b></li>
										<li><b>medium</b></li>
										<li><b>bad</b></li>
									</ul>
								</li>
								<li><b>Season</b> (only for series) - Number of the season (as a number)</li>
								<li><b>Episode</b> (only for series) - Number of the episode (as a number)</li>
							</ul>
						</li>
					</ul>
					<h5>Some examples:</h5>
					<div class="well">
						Type:m|IMDb:tt0418279|Lang:de|Link:http://streamhoster.com/?v=12345678|Rated:medium
						Type:s|IMDb:tt0460649|Lang:en|Subtitle:de|Link:http://streamhoster.com/?v=12387678|Rated:good|Season:1|Episode:5
					</div>
				</blockquote>
				<hr />
				<form class="bulk-upload" method="post" enctype="multipart/form-data">
					<input type="file" name="file-bulk" />
					<div class="input-append">
						<input class="span2 filename" name="filename" value="{$smarty.post.filename}" id="appendedInputButton" type="text" readonly />
						<button class="btn" type="button" id="select-bulk">Select Bulk</button>
						<button class="btn btn-warning" type="submit" id="upload-bulk">Upload</button>
					</div>
				</form>
			</div>
			<div id="api" style="display: none; min-height: 311px;">
				<blockquote>
					<legend>API Documentation</legend>
					<h5>With the help of our API Key System you are able to automate the link addition by using <i>uTool</i>.<br />
					The {$CONFIG.TITLE} Plugin has to be enabled and set up with your personal API Key (<i>see below</i>).<br /><br /></h5>
					<div class="well" style="margin-top: 85px;">
						<center><h3>{$USER.api}</h3></center>
					</div>
				</blockquote>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
</div>
<script>$(document).ready (function () { Page.InitStreamPage (); });</script>