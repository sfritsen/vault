<?php
session_start();

// Security check for direct browser request
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    die("This page must have gotten lost in the Matrix");
}

include dirname(__DIR__).'/lib/Cipher.php';
include dirname(__DIR__).'/lib/Mask.php';
include dirname(__DIR__).'/scripts/db.php';

try {

    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    if ($conn->connect_error) {
        die("Bunk connection: ".$conn->connect_error);
    }

    $action = $_POST['action'];
    $cur_date = date("U");
    $account_id = $_SESSION['account_id'];
    $security_key = $_SESSION['security_key'];

    if ($action === 'show') {

        $filter_name = $_POST['filter_name'];

        $sql = "
            SELECT *
            FROM data_vault
            WHERE account_id = '$account_id'
            AND security_key = '$security_key'
            AND deleted = '0'
            AND app_name LIKE '%$filter_name%'
            ORDER BY app_name ASC, username ASC
        ";
        $results = mysqli_query($conn, $sql);

        if ($results->num_rows === 0) {
            echo '<tr><td colspan="4">No Passwords Found</td></tr>';
        }

        foreach ($results as $row) {

            // If no url exists, it means it's an actual installed app so just show the name without link
            if ($row['app_url'] === 'installed') {
                $show_name = $row['app_name'];
            } else {
                $show_name = '<a href="'.$row['app_url'].'" target="__blank">'.$row['app_name'].'</a>';
            }

            // If ldap or pin / token is found, don't decrypt password
            if ($row['is_ldap'] === '1') {
                $show_hidden_field = null;
                $show_password = $row['password'];
            } elseif ($row['req_token'] === '1') {
                $show_hidden_field = null;
                $show_password = $row['password'];
            } else {

                // Apply mask to string
                $unmasked_password = Cipher::decrypt($_SESSION['security_key'], $row['password']);
                $masked_password = mask($unmasked_password, null, strlen($unmasked_password));

                $show_hidden_field = '<div id="'.$row['id'].'" style="display: none;">'.Cipher::decrypt($_SESSION['security_key'], $row['password']).'</div>';
                $show_password = '<a href="javascript:;" id="display_password'.$row['id'].'" onclick="copyToClipboard('.$row['id'].')" title="Click to copy">'.$masked_password.'</a><i class="fas fa-paste" onclick="copyToClipboard('.$row['id'].')" title="Click to copy"></i> <i class="far fa-eye" id="mask_icon'.$row['id'].'" onclick="unmaskPassword('.$row['id'].')" title="Show"></i>';
            }

            if ($row['edited_date'] === '0') {
                $edited_date = '--';
            } else {
                $edited_date = date("M jS Y h:i a", $row['edited_date']);
            }

            echo '<tr>';
            echo '<td>'.$show_name.'</td>';
            echo '<td>'.$row['username'].'</td>';
            echo '<td>'.$show_hidden_field.$show_password.'</td>';
            echo '<td>'.date("M jS Y", $row['added_date']).'</td>';
            echo '<td>'.$edited_date.'</td>';
            echo '<td align="right">';
            echo '<i class="icon_edit fas fa-edit fa-fw" value="'.$row['id'].'" title="Edit"></i>';
            echo '<i class="icon_delete fas fa-trash-alt fa-fw" value="'.$row['id'].'" title="Delete"></i>';
            echo '</td>';
            echo '</tr>';
        }
    }

    if ($action === 'new') {
        $app_name = $_POST['app_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $app_url = $_POST['app_url'];
        $is_ldap = $_POST['is_ldap'];
        $req_pin = $_POST['req_pin'];
        $req_token = $_POST['req_token'];

        // Trim whitespaces from user inputs
        $app_name = trim($app_name);
        $username = trim($username);
        $password = trim($password);
        $app_url = trim($app_url);

        // Encrypt password
        $password = Cipher::encrypt($_SESSION['security_key'], $password);

        // Correct checkbox post data
        if ($is_ldap === 'true') {
            $is_ldap = '1';
            $username = 'LDAP Username';
            $password = 'LDAP Password';
        } else {
            $is_ldap = '0';
        }

        if ($req_token === 'true') {
            $req_token = '1';

            if ($req_pin === 'true') {
                $req_pin = '1';
                $password = 'PIN + Token';
            } else {
                $req_pin = '0';
                $password = 'Token';
            }
            
        } else {
            $req_pin = '0';
            $req_token = '0';
        }

        // If no URL was supplied, assume it's installed
        if ($app_url === '') {
            $app_url = 'installed';
        } elseif (preg_match("/(:\/\/)/", $app_url)) {
            $app_url = $app_url;
        } else {
            $app_url = 'https://'.$app_url;
        }

        $sql = "
            INSERT INTO data_vault (
                account_id,
                security_key,
                app_url,
                app_name,
                username,
                password,
                added_date,
                edited_date,
                is_ldap,
                req_pin,
                req_token
            ) VALUES (
                '$account_id',
                '$security_key',
                '$app_url',
                '$app_name',
                '$username',
                '$password',
                '$cur_date',
                '0',
                '$is_ldap',
                '$req_pin',
                '$req_token'
            )
        ";
        $query = mysqli_query($conn, $sql);

        if (!$query) {
            echo mysqli_error($conn);
        } else {
            echo $app_name." saved successfully";
        }
    }

    if ($action === 'delete') {
        $id = $_POST['id'];

        $sql = "
            UPDATE data_vault
            SET deleted = '$cur_date'
            WHERE id = '$id'
        ";

        $query = mysqli_query($conn, $sql);

        if (!$query) {
            echo mysqli_error($conn);
        } else {
            echo 'deleted';
        }
    }

    if ($action === 'get') {
        $id = $_POST['id'];

        $sql = "
            SELECT *
            FROM data_vault
            WHERE id = '$id'
        ";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($row['password'] === 'LDAP Password') {
            $password = $row['password'];
        } else {
            $password = Cipher::decrypt($_SESSION['security_key'], $row['password']);
        }

        if ($row['req_token'] === '1') {
            $password = $row['password'];
        } else {
            $password = Cipher::decrypt($_SESSION['security_key'], $row['password']);
        }

        if ($row['app_url'] === 'installed') {
            $app_url = '';
        } else {
            $app_url = $row['app_url'];
        }

        $return_data = array(
            'app_name' => $row['app_name'],
            'username' => $row['username'],
            'password' => $password,
            'app_url' => $app_url,
            'is_ldap' => $row['is_ldap'],
            'req_pin' => $row['req_pin'],
            'req_token' => $row['req_token'],
            'id' => $row['id']
        );
    
        print json_encode($return_data);
    }

    if ($action === 'edit') {
        $app_name = $_POST['app_name'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $app_url = $_POST['app_url'];
        $is_ldap = $_POST['is_ldap'];
        $req_pin = $_POST['req_pin'];
        $req_token = $_POST['req_token'];
        $id = $_POST['edit_id'];

        // Trim whitespaces from user inputs
        $app_name = trim($app_name);
        $username = trim($username);
        $password = trim($password);
        $app_url = trim($app_url);

        // Encrypt password
        $password = Cipher::encrypt($_SESSION['security_key'], $password);

        // Correct checkbox post data
        if ($is_ldap === 'true') {
            $is_ldap = '1';
            $username = 'LDAP Username';
            $password = 'LDAP Password';
        } else {
            $is_ldap = '0';
        }
        
        if ($req_token === 'true') {
            $req_token = '1';

            if ($req_pin === 'true') {
                $req_pin = '1';
                $password = 'PIN + Token';
            } else {
                $req_pin = '0';
                $password = 'Token';
            }
            
        } else {
            $req_pin = '0';
            $req_token = '0';
        }

        // If no URL was supplied, assume it's installed
        if ($app_url === '') {
            $app_url = 'installed';
        } elseif (preg_match("/(:\/\/)/", $app_url)) {
            $app_url = $app_url;
        } else {
            $app_url = 'https://'.$app_url;
        }

        $sql = "
            UPDATE data_vault
            SET 
                app_name = '$app_name',
                username = '$username',
                password = '$password',
                app_url = '$app_url',
                is_ldap = '$is_ldap',
                req_pin = '$req_pin',
                req_token = '$req_token',
                edited_date = '$cur_date'
            WHERE id = '$id'
            AND account_id = '$account_id'
            AND security_key = '$security_key'
        ";

        $query = mysqli_query($conn, $sql);

        if (!$query) {
            echo mysqli_error($conn);
        } else {
            echo $app_name." saved successfully";
        }
    }

    if ($action === 'trash') {

        $sql = "
            SELECT *
            FROM data_vault
            WHERE account_id = '$account_id'
            AND security_key = '$security_key'
            AND deleted NOT LIKE '0%'
            ORDER BY app_name ASC, username ASC
        ";
        $results = mysqli_query($conn, $sql);

        if ($results->num_rows === 0) {
            echo '<tr><td colspan="4">No Trash Found</td></tr>';
        }

        foreach ($results as $row) {

            // If no url exists, it means it's an actual installed app so just show the name without link
            if ($row['app_url'] === 'installed') {
                $show_name = $row['app_name'];
            } else {
                $show_name = '<a href="'.$row['app_url'].'" target="__blank">'.$row['app_name'].'</a>';
            }

            // If ldap or pin / token is found, don't decrypt password
            if ($row['is_ldap'] === '1') {
                $show_hidden_field = null;
                $show_password = $row['password'];
            } elseif ($row['req_token'] === '1') {
                $show_hidden_field = null;
                $show_password = $row['password'];
            } else {
                $show_hidden_field = '<div id="'.$row['id'].'" style="display: none;">'.Cipher::decrypt($_SESSION['security_key'], $row['password']).'</div>';
                $show_password = '<a href="javascript:;" onclick="copyToClipboard('.$row['id'].')">'.Cipher::decrypt($_SESSION['security_key'], $row['password']).'</a><i class="fas fa-paste" onclick="copyToClipboard('.$row['id'].')" title="Click to copy"></i>';
            }

            echo '<tr>';
            echo '<td>'.$show_name.'</td>';
            echo '<td>'.$row['username'].'</td>';
            echo '<td>'.$show_hidden_field.$show_password.'</td>';
            echo '<td>'.date("M jS Y", $row['added_date']).'</td>';
            echo '<td>'.date("M jS Y h:i:s a", $row['deleted']).'</td>';
            echo '<td align="right">';
            echo '<i class="icon_restore fas fa-undo fa-fw" value="'.$row['id'].'" title="Restore"></i>';
            echo '</td>';
            echo '</tr>';
        }
    }

    if ($action === 'restore') {
        $id = $_POST['id'];

        $sql = "
            UPDATE data_vault
            SET deleted = '0'
            WHERE id = '$id'
            AND account_id = '$account_id'
            AND security_key = '$security_key'
        ";

        $query = mysqli_query($conn, $sql);

        if (!$query) {
            echo mysqli_error($conn);
        } else {
            echo $app_name." restored successfully";
        }
    }

    // Refresh session activity
    $_SESSION['last_activity'] = time();

    $conn->close();

} catch (mysqli_sql_exception $e) {
    echo "<pre>".$e."</pre>";
}