<div class="modal fade" id="modalEditPassword" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewModalLabel">Edit Login Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div id="edit_status_msg"></div>

                <p>
                    <form id="form_edit">
                        <div class="form_block">
                            <label for="edit_app_name"><span class="required_field">*</span>Application Name</label>
                            <input type="text" id="edit_app_name" class="form-control" value="" placeholder="App Name" required>
                        </div>
                        <div class="form_block">
                            <label for="edit_username">Username</label>
                            <input type="text" id="edit_username" class="form-control" value="" placeholder="Username">
                        </div>
                        <div class="form_block">
                            <label for="edit_password">Password</label>
                            <input type="text" id="edit_password" class="form-control" value="" placeholder="Password">
                        </div>
                        <div class="form_block">
                            <label for="edit_app_url">URL</label>
                            <input type="text" id="edit_app_url" class="form-control" value="" placeholder="URL of application" aria-describedby="urlHelp">
                            <small id="urlHelp" class="form-text">Leave blank for installed application</small>
                        </div>
                        <div class="form_block">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="edit_is_ldap">
                                <label class="form-check-label" for="edit_is_ldap">LDAP Login</label>
                            </div>
                        </div>
                        <div class="form_block">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="edit_req_pin">
                                <label class="form-check-label" for="edit_req_pin">PIN Required</label>
                            </div>
                        </div>
                        <div class="form_block">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="edit_req_token">
                                <label class="form-check-label" for="edit_req_token">Token Required</label>
                            </div>
                        </div>
                    </form>
                </p>

            </div>
            <div class="modal-footer">
                <input type="hidden" id="edit_id" value="">
                <button type="button" class="btn" id="edit_btn_submit">Save</button>
                <button type="button" class="btn" id="edit_btn_close_add_new" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('#edit_btn_submit').click(function(){
    var action = 'edit';
    var app_name = $('#edit_app_name').val();
    var username = $('#edit_username').val();
    var password = $('#edit_password').val();
    var app_url = $('#edit_app_url').val();
    var is_ldap = $('#edit_is_ldap').prop('checked');
    var req_pin = $('#edit_req_pin').prop('checked');
    var req_token = $('#edit_req_token').prop('checked');
    var edit_id = $('#edit_id').val();

    if (app_name.length === 0) {
        $('#status_msg').html('<span class="error_text">Application name is required</span>');
    } else {
        $.ajax({
            type: 'post',
            url: '<?php echo ROOT_DIR; ?>/scripts/passwords.php',
            data: {action: action, app_name: app_name, username: username, password: password, app_url: app_url, is_ldap: is_ldap, req_pin: req_pin, req_token: req_token, edit_id: edit_id},
            dataType: 'html',
            success: function(data){
                $('#edit_status_msg').html(data).delay(2000).fadeOut();
                $('#modalEditPassword').modal('hide');
                $("#form_edit").trigger("reset");
                load_records();
            },
            error: function(data){
                console.log(data);
            }
        });
    }
});

$('#edit_btn_close_add_new').click(function(){
    $("#form_edit").trigger("reset");
});
</script>