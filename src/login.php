
<?php
// Get database connection
require("../config/database.php");

session_start();

if (isset($_SESSION['userId'])) {
    header("Location: home.php");
    exit();
}

// Get data from HTML form
$e_mail = $_POST['email'] ?? '';
$p_assword = $_POST['psw'] ?? '';

$pass_enc = md5($p_assword);

// Search user in database
$sql = "SELECT
            id,
            firstname,
            lastname,
            photo_url,
            password
        FROM public.users
        WHERE email = $1
        AND password = $2";

$res = pg_query_params($supa_conn, $sql, [$e_mail, $pass_enc]);

if ($res && pg_num_rows($res) > 0) {

    $row = pg_fetch_assoc($res);

    $_SESSION['userId'] = $row['id'];
    $_SESSION['fname'] = $row['firstname'];
    $_SESSION['lname'] = $row['lastname'];
    $_SESSION['photo_url'] = $row['photo_url'] ?? '';

    header("Location: home.php");
    exit();

} else {
    echo "<script>
        alert('Invalid email or password');
        window.location.href = 'login.html';
    </script>";
    exit();
}
?>
