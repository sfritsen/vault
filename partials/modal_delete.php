<div class="modal fade" id="modalDelete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Login Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>
                    Please confirm
                </p>
                <p class="error_text">
                    Are you sure you want to delete this login?
                </p>
                <p>
                    <div id="delete_app_name"></div>
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="btn_delete_record">Delete</button>
                <button type="button" class="btn" data-dismiss="modal">Close</button>
                <input type="hidden" id="hid_delete_id" value="">
            </div>
        </div>
    </div>
</div>