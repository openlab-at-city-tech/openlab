<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                aria-label="Close<?php esc_html_e('Close', 'openlab-gradebook')?>"><span
                    aria-hidden="true">&times;</span></button>

            <h4 class="modal-title" id="myModalLabel"><?php esc_html_e('Grade Comment', 'openlab-gradebook')?></h4>

        </div>
        <div class="modal-body">
            <form id="edit-comment-form" class="form-horizontal">
                <div class="form-group">
                    <label for="comment">
                        <ul class="list-unstyled">
                            <li><%= username %></li>
                            <li><%= name %></li>
                        </ul>
                    </label>
                    <% if (gradebook.role === 'instructor') { %>
                        <textarea class="form-control" id="comment" name="comment" rows="3"><% if (comments) { %><%= comments %><% } %></textarea>
                    <% } else  {%>
                        <p><% if (comments) { %><%= comments %><% } %></p>
                    <% } %>                    
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php esc_html_e('Close', 'openlab-gradebook')?></button>
            <% if (gradebook.role === 'instructor') { %>
                <button type="button" id="edit-comment" class="btn btn-primary"><span class="hidden dashicons dashicons-image-rotate dashicons-spinning"></span><span class="button-text"><?php esc_html_e('Save', 'openlab-gradebook')?></span></button>
            <% } %> 

        </div>
    </div>
</div>