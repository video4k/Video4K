<div id="page-wrapper" class="content-box">
	<div class="header">Release Tags</div>
	<div class="wrapper">
		<form method="POST" style="margin-bottom: -5px;">
			<table id="table-pending" class="table table-bordered">
				<thead>
					<tr>
						<th style="width: 28px; padding: 8px 0 8px 12px;" class="no-sort"><div class="checkbox"><input type="checkbox" /></div></th>
						<th style="width: 600px;">Tag</th>
						<th style="width: 140px;">Added</th>
						<th style="width: 90px;" class="no-sort">Set IMDB</th>
					</tr>
				</thead>
				<tbody>
					<tr><td colspan="4"><center><b>Loading entries ...</b></center></td></tr>
				</tbody>
			</table><hr style="margin: 10px 0;" />
			<button type="submit" class="btn btn-block btn-info">Save Changes</button>
		</form>
	</div>
</div>
<script>$(document).ready (function () { Manage.LoadTable ('#table-pending', '/releases'); });</script>