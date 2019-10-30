<div class="modal fade" id="modalAddNew" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewModalLabel">Add New</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            <p>
                To add a new password, fill out the form below as required.  Your password will be stored with AES 128 bit encryption 
                requiring a private security key to unlock which is unique to your account.
            </p>

                <div id="status_msg"></div>

                <p>
                    <form id="form_add_new">
                        <div class="form_block">
                            <label for="app_name"><span class="required_field">*</span>Application Name</label>
                            <input type="text" id="app_name" class="form-control" value="" placeholder="App Name" required>
                        </div>
                        <div class="form_block">
                            <label for="username">Username</label>
                            <input type="text" id="username" class="form-control" value="" placeholder="Username">
                        </div>
                        <div class="form_block">
                            <label for="password">Password</label>
                            <input type="text" id="password" class="form-control" value="" placeholder="Password">
                        </div>
                        <div class="form_block">
                            <label for="app_url">URL</label>
                            <input type="text" id="app_url" class="form-control" value="" placeholder="URL of application" aria-describedby="urlHelp">
                            <small id="urlHelp" class="form-text">Leave blank for installed application</small>
                        </div>
                        <div class="form_block">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="is_ldap">
                                <label class="form-check-label" for="is_ldap">LDAP Login</label>
                            </div>
                        </div>
                        <div class="form_block">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="req_pin">
                                <label class="form-check-label" for="req_pin">PIN Required</label>
                            </div>
                        </div>
                        <div class="form_block">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="req_token">
                                <label class="form-check-label" for="req_token">Token Required</label>
                            </div>
                        </div>
                    </form>
                </p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn" id="btn_submit">Submit</button>
                <button type="button" class="btn" id="btn_close_add_new" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('#btn_submit').click(function(){
    var action = 'new';
    var app_name = $('#app_name').val();
    var username = $('#username').val();
    var password = $('#password').val();
    var app_url = $('#app_url').val();
    var is_ldap = $('#is_ldap').prop('checked');
    var req_pin = $('#req_pin').prop('checked');
    var req_token = $('#req_token').prop('checked');

    if (app_name.length === 0) {
        $('#status_msg').html('<span class="error_text">Application name is required</span>');
    } else {
        $.ajax({
            type: 'post',
            url: '<?php echo ROOT_DIR; ?>/scripts/passwords.php',
            data: {action: action, app_name: app_name, username: username, password: password, app_url: app_url, is_ldap: is_ldap, req_pin: req_pin, req_token: req_token},
            dataType: 'html',
            success: function(data){
                $('#status_msg').html(data).delay(5000).fadeOut();
                $("#form_add_new").trigger("reset");
                load_records();
            },
            error: function(data){
                console.log(data);
            }
        });
    }
});

$('#btn_close_add_new').click(function(){
    $("#form_add_new").trigger("reset");
});
</script>