<?php include('partials/header.php'); ?>

    <main role="centered" class="container">
        <div class="app_logo"><i class="fas fa-fingerprint"></i></div>
        <div class="app_title">Vault</div>

        <h2>Create Vault Account</h2>
        <form id="new_account_form" autocomplete="off">
        <div id="create_account_msg"></div>
            <div class="row d-flex justify-content-center">
                <div class="col-sm-4 text-center">
                <div class="form_block">
                <input type="text" id="username" value="" class="form-control" placeholder="Username" autocomplete="vault_new_username">
            </div>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-sm-4">
                <div class="form_block">
                <input type="password" id="password" value="" class="form-control" placeholder="Password" autocomplete="vault_new_password">
            </div>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-sm-4">
                <div class="form_block">
                <input type="password" id="password2" value="" class="form-control" placeholder="Confirm Password" autocomplete="vault_new_password2">
            </div>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-sm-4">
                    <button type="button" id="btn_create_account" class="btn">Create Account</button>
                </div>
            </div>
        </form>

        <p>
            <a href="index.php">Back to login</a>
        </p>

</main>

<?php include('partials/footer.php'); ?>

<script>
$('#btn_create_account').click(function(){
    var username = $('#username').val();
    var password = $('#password').val();
    var password2 = $('#password2').val();
    var action = 'create';

    if (!username) {
        $('#create_account_msg').html("Username is required");
    } else if (!password) {
        $('#create_account_msg').html("Password is required");
    } else if (!password2) {
        $('#create_account_msg').html("Confirm password please");
    } else if (password !== password2) {
        $('#create_account_msg').html("Passwords do not match");
    } else {
        $.ajax({
            type: 'post',
            url: '<?php echo ROOT_DIR; ?>/scripts/login.php',
            data: {action: action, username: username, password: password},
            dataType: 'html',
            success: function(data){
                $('#create_account_msg').html(data);
            }
        });
    }

    e.preventDefault();
});
</script>
