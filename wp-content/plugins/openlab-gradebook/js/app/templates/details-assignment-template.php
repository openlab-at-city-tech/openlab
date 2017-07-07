<script id="details-assignment-template" type="text/template">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Assignment</h4>
			</div>
			<div class="modal-body">  
				<dl class="dl-horizontal">
				    <dt>Title:</dt> <dd><%= assignment.get('assign_name') %></dt>
				    <dt>Date Assigned:</dt> <dd><%= assignment.get('assign_date') %></dt>
				    <dt>Date Due:</dt> <dd><%= assignment.get('assign_due') %></dt>
				    <dt>Assignment Category:</dt> <dd><%= assignment.get('assign_category') %></dt>
				</dl>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</script>     