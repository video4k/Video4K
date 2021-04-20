<div id="page-wrapper" class="content-box">
	<div class="header">Database Management</div>
	<div class="wrapper entries">
		<table id="table-entries" class="table table-bordered">
			<thead>
				<tr>
					<th style="width: 70px;" class="no-sort">IMDB</th>
					<th style="width: 50px;">Kind</th>
					<th style="width: 300px;">German Title</th>
					<th style="width: 250px;">English Title</th>
					<th style="width: 45px;">Year</th>
					<th style="width: 100px;">Active Links</th>
					<th style="width: 34px;" class="no-sort">&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="7"><center><b>Loading entries ...</b></center></td></tr>
			</tbody>
		</table>
	</div>
	<div class="wrapper links" style="display: none;">
		<table id="table-links" class="table table-bordered">
			<thead>
				<tr>
					<th style="width: 5px;" class="no-sort">&nbsp;</th>
					<th style="width: 100px;">Owner</th>
					<th style="width: 410px;" class="no-sort">Link</th>
					<th style="width: 80px;">Language</th>
					<th style="width: 60px;">Season</th>
					<th style="width: 65px;">Episode</th>
					<th style="width: 55px;">Status</th>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="7"><center><b>Loading entries ...</b></center></td></tr>
			</tbody>
		</table><hr />
		<button class="btn btn-primary" id="back">&laquo;&nbsp;Back</button>
		<button class="btn btn-danger" id="remove">Delete selected</button>
	</div>
</div>
<script>$(document).ready (function () { Manage.LoadTable ('#table-entries', '/database'); });</script>