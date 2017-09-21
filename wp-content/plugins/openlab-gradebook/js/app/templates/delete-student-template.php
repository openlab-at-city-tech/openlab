<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Delete Student From This Gradebook</h4>
        </div>
        <div class="modal-body">
            <form id="delete-student-form" class="form-horizontal">      
                <input type="hidden" name="action" value="delete_student"/>
                <input type="hidden" name="id" value="<%= student ? student.get('id') : '' %>"/> 			        
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" id="delete-student-delete" data-dismiss="modal" class="btn btn-danger">Delete</button>
        </div>
    </div>
</div>