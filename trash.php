<?php 
include('partials/session.php');
include('partials/header.php');
include('partials/navbar.php');
include('lib/Cipher.php');
?>

<main role="app" id="pw_list">

    <table id="trash_passwords_table" class="table table_data hover_table">
        <thead>
            <tr>
                <th scope="col">Application</th>
                <th scope="col">Username</th>
                <th scope="col">Password</th>
                <th scope="col">Added</th>
                <th scope="col">Deleted</th>
                <th scope="col"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

</main>

<?php include('partials/footer.php'); ?>

<script>
function load_trash() {
    var action = 'trash';

    $.ajax({
        type: 'post',
        url: '<?php echo ROOT_DIR; ?>/scripts/passwords.php',
        data: {action: action},
        dataType: 'html',
        success: function(data){
            $('#trash_passwords_table tbody').html(data);
        }
    });
}

function copyToClipboard(elementId) {
    var aux = document.createElement("input"); // Create a "hidden" input             
    aux.setAttribute("value", document.getElementById(elementId).innerHTML); // Assign it the value of the specified element
    document.body.appendChild(aux); // Append it to the body
    aux.select(); // Highlight its content
    document.execCommand("copy"); // Copy the highlighted text
    document.body.removeChild(aux); // Remove it from the body
}

$('#trash_passwords_table').on('click', '.icon_restore', function(){
    var id = $(this).attr('value');
    var action = 'restore';

    $.ajax({
        type: 'post',
        url: '<?php echo ROOT_DIR; ?>/scripts/passwords.php',
        data: {action: action, id: id},
        dataType: 'html',
        success: function(data){
            load_trash();
        }
    });
});

load_trash();
</script>