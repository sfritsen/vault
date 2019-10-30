<?php 
include('partials/session.php');
include('partials/header.php');
include('partials/navbar.php');
include('lib/Cipher.php');
?>

<main role="app" id="pw_list">

    <div class="row no-gutters">
        <div class="col-sm-4">
            <input type="text" id="filter_name" class="form-control" value="" placeholder="Filter by application name" onkeyup="load_records()">
        </div>
        <div class="col ml-2">
            <button type="button" class="btn" id="btn_filter_clear">Clear</button>
        </div>
    </div>

    <table id="passwords_table" class="table table_data hover_table">
        <thead>
            <tr>
                <th scope="col">Application</th>
                <th scope="col">Username</th>
                <th scope="col">Password</th>
                <th scope="col">Added</th>
                <th scope="col">Edited</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</main>

<?php 
include('partials/modal_add_new.php');
include('partials/modal_edit.php');
include('partials/modal_delete.php');
include('partials/footer.php'); 
?>

<script>
function copyToClipboard(elementId) {
    var aux = document.createElement("input"); // Create a "hidden" input             
    aux.setAttribute("value", document.getElementById(elementId).innerHTML); // Assign it the value of the specified element
    document.body.appendChild(aux); // Append it to the body
    aux.select(); // Highlight its content
    document.execCommand("copy"); // Copy the highlighted text
    document.body.removeChild(aux); // Remove it from the body
}

function unmaskPassword(elementId) {
    var unmasked = $('#'+elementId).html();
    $('#display_password'+elementId).html(unmasked);
    $('#mask_icon'+elementId).hide();
}

function load_records() {
    var action = 'show';
    var filter_name = $('#filter_name').val();

    $.ajax({
        type: 'post',
        url: '<?php echo ROOT_DIR; ?>/scripts/passwords.php',
        data: {action: action, filter_name: filter_name},
        dataType: 'html',
        success: function(data){
            $('#passwords_table tbody').html(data);
        }
    });
}

$('#btn_filter_clear').click(function(){
    $('#filter_name').val('');
    load_records();
});

$('#link_add_new').click(function(){
    $('#modalAddNew').modal('show');
});

$('#passwords_table').on('click', '.icon_delete', function(){
    var id = $(this).attr('value');
    var action = 'get';

    $.ajax({
        type: 'post',
        url: '<?php echo ROOT_DIR; ?>/scripts/passwords.php',
        data: {action: action, id: id},
        dataType: 'json',
        success: function(data){
            $('#hid_delete_id').val(id);
            $('#delete_app_name').html('Application: '+data.app_name);
            $('#modalDelete').modal('show');
        }
    });
});

$('#btn_delete_record').click(function(){
    var action = 'delete';
    var id = $('#hid_delete_id').val();

    $.ajax({
        type: 'post',
        url: '<?php echo ROOT_DIR; ?>/scripts/passwords.php',
        data: {action: action, id: id},
        dataType: 'html',
        success: function(data){
            if (data = 'deleted') {
                $('#modalDelete').modal('hide');
                load_records();
            } else {
                console.log(data);
            }
        }
    });
});

$('#passwords_table').on('click', '.icon_edit', function(){
    var id = $(this).attr('value');
    var action = 'get';

    $.ajax({
        type: 'post',
        url: '<?php echo ROOT_DIR; ?>/scripts/passwords.php',
        data: {action: action, id: id},
        dataType: 'json',
        success: function(data){
            $('#edit_app_name').val(data.app_name);
            $('#edit_username').val(data.username);
            $('#edit_password').val(data.password);
            $('#edit_app_url').val(data.app_url);
            $('#edit_id').val(data.id);

            if (data.is_ldap === '1') {
                $('#edit_is_ldap').prop('checked', true);
            }
            if (data.req_pin === '1') {
                $('#edit_req_pin').prop('checked', true);
            }
            if (data.req_token === '1') {
                $('#edit_req_token').prop('checked', true);
            }

            $('#modalEditPassword').modal('show');
        },
        error: function(data){
            console.log(data);
        }
    });
});

load_records();
</script>