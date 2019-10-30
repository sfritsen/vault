<?php include('partials/header.php'); ?>

    <main role="centered">
        <div class="app_logo"><i class="fas fa-fingerprint"></i></div>
        <div class="app_title">Vault</div>

        <p>
            Password Management / 128 bit AES encrpytion
            <div id="login_msg">Please login to continue</div>
        </p>

        <form id="login_form" autocomplete="off">

            <p>
                <input type="text" id="username" class="form-control text-center" value="" placeholder="Username" autocomplete="vault_username">
                <input type="password" id="password" class="form-control text-center" value="" placeholder="Password" autocomplete="vault_password">
            </p>

            <button type="button" id="btn_login" class="btn">Login</button>
        </form>

        <p>
            <a href="create_account.php">Create account</a><br>
            <a href="mailto:donotreply@mydomain.com?subject=Vault Support">Support</a>
        </p>
    </main>

<?php include('partials/footer.php'); ?>

<script>
$('#btn_login').click(function(){
    var action = 'login';
    var username = $('#username').val();
    var password = $('#password').val();

    $.ajax({
        type: 'post',
        url: '<?php echo ROOT_DIR; ?>/scripts/login.php',
        data: {action: action, username: username, password: password},
        dataType: 'html',
        success: function(data){
            if (data === 'good') {
                window.location.href = 'list.php';
            } else {
                $('#login_msg').html(data);
            }
        }
    });
});
</script>