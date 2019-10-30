<nav class="navbar navbar-expand fixed-top align-middle">
    <a class="navbar-brand" href="list.php"><i class="fas fa-fingerprint"></i> Vault</a>

    <div class="navbar-nav mr-auto">
        <a class="nav_link" href="list.php">List</a>
        <a class="nav_link" href="javascript:;" id="link_add_new">Add New</a>
        <a class="nav_link" href="trash.php" id="link_add_new">Trash</a>
    </div>

    <div class="dropdown dropleft">
        <a class="nav-link dropdown-toggle" href="#" id="userAccountDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <?php echo $_SESSION['username']; ?>
        </a>
        <div class="dropdown-menu" aria-labelledby="userAccountDropdown">
            <?php if ($_SESSION['username'] === 't815138') { ?>
                <a class="dropdown-item" href="session_debug.php" target="__blank">DEBUG Session</a>
            <?php } ?>
            <a class="dropdown-item" href="logout.php">Logout</a>
        </div>
    </div>
    
</nav>