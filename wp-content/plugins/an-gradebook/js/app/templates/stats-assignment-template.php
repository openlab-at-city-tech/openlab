<script id="stats-assignment-template" type="text/template">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Assignment Statistics: <%= assignment.get('assign_name') %></h4>
			</div>
			<div class="modal-body">
				<div class="labeled-chart-container">
					<canvas id = "myChart"></canvas>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</script>