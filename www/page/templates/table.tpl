<div id="table-wrapper" {if !$TABLE}style="display: none;"{/if}>
	<div class="table-filter">
		<div class="btn-toolbar" id="alphabet">
			<div class="btn-group">
				<span class="btn">All</span>
				<span class="btn">#</span>
			</div>
			<div class="btn-group">
				<span class="btn">A</span>
				<span class="btn">B</span>
				<span class="btn">C</span>
				<span class="btn">D</span>
				<span class="btn">E</span>
				<span class="btn">F</span>
				<span class="btn">G</span>
				<span class="btn">H</span>
				<span class="btn">I</span>
				<span class="btn">J</span>
				<span class="btn">K</span>
				<span class="btn">L</span>
				<span class="btn">M</span>
				<span class="btn">N</span>
				<span class="btn">O</span>
				<span class="btn">P</span>
				<span class="btn">Q</span>
				<span class="btn">R</span>
				<span class="btn">S</span>
				<span class="btn">T</span>
				<span class="btn">U</span>
				<span class="btn">V</span>
				<span class="btn">W</span>
				<span class="btn">X</span>
				<span class="btn">Y</span>
				<span class="btn">Z</span>
			</div>
		</div>
		<div class="well well-small" id="date"><b>{($smarty.now - ((1 - $CONFIG.CURUPDATES) * 0x15180))|date_format:"%d.%m.%G"}</b> - Showing the latest Updates for this date.</div>
	</div>
	<div id="table-setting"></div>
	<table id="linktable">
		<thead>
			<tr>
				<th>Kind</th>
				<th>Title</th>
				<th>Language</th>
				<th>Genre</th>
				<th>IMDB Rating</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>
<div id="result-wrapper" class="content-box table-box">
	<div class="header">
		<div class="left"></div>
		<div class="right"><div id="language-switcher"></div></div>
	</div>
	<div class="wrapper">
		<img class="thumbnail" />
		<div class="stars"><div><span></span></div></div>
		<span></span>
		<hr />
		<table>
			<tbody>
				<tr>
					<td><i data-toggle="tooltip" title="Directors" class="icon-user"></i></td>
					<td id="directors"></td>
				</tr>
				<tr>
					<td><i data-toggle="tooltip" title="Duration" class="icon-time"></i></td>
					<td id="duration"></td>
				</tr>
				<tr>
					<td><i data-toggle="tooltip" title="Genres" class="icon-tags"></i></td>
					<td id="genres"></td>
				</tr>
				<tr>
					<td><i data-toggle="tooltip" title="Actors" class="icon-star"></i></td>
					<td id="actors"></td>
				</tr>
				<tr>
					<td><i data-toggle="tooltip" title="Release Date" class="icon-calendar"></i></td>
					<td id="released"></td>
				</tr>
				<tr>
					<td><i data-toggle="tooltip" title="Stream Quality" class="icon-eye-open"></i></td>
					<td><div class="quality-box"></div></td>
				</tr>
			</tbody>
		</table>
		<hr />
		<ul class="nav nav-tabs nav-top"></ul>
		<div class="nav-player" alt="Click here to watch the movie" title="Click here to watch the movie">
			<h3>Click <u>here</u> to play</h3>
			<div class="loader-64"></div>
			<h2>Link down? There are mirrors available, click <u>here</u> again to open a different link.</h2>
		</div>
		<ul class="nav nav-tabs nav-bottom"></ul>
	</div>
</div>