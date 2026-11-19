<?php

session_start();

require("../config/database.php");

if (isset($_SESSION['userId'])) {
    header("Location: home.php");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['psw'] ?? '';

if ($email === '' || $password === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
        alert('Invalid email or password');
        window.location.href = 'login.html';
    </script>";
    exit();
}

$sql = "SELECT id, firstname, lastname, photo_url, password
        FROM public.users
        WHERE LOWER(email) = LOWER($1)
        LIMIT 1";

$res = pg_query_params($supa_conn, $sql, [$email]);

if (!$res) {
    error_log("Login error: " . pg_last_error($supa_conn));
    exit("No se pudo consultar la base de datos.");
}

$row = pg_fetch_assoc($res);

if (!$row) {
    echo "<script>
        alert('Invalid email or password');
        window.location.href = 'login.html';
    </script>";
    exit();
}

$storedPassword = $row['password'];
$validPassword = password_verify($password, $storedPassword);

// Compatibilidad con las contraseñas antiguas MD5.
if (
    !$validPassword &&
    preg_match('/^[a-f0-9]{32}$/i', $storedPassword)
) {
    $validPassword = hash_equals(
        strtolower($storedPassword),
        md5($password)
    );

    // Convertir la contraseña antigua a un hash seguro.
    if ($validPassword) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);

        $update = pg_query_params(
            $supa_conn,
            "UPDATE public.users SET password = $1 WHERE id = $2",
            [$newHash, $row['id']]
        );

        if (!$update) {
            error_log("No se pudo actualizar el hash de la contraseña.");
        }
    }
}

if (!$validPassword) {
    echo "<script>
        alert('Invalid email or password');
        window.location.href = 'login.html';
    </script>";
    exit();
}

session_regenerate_id(true);

$_SESSION['userId'] = $row['id'];
$_SESSION['fname'] = $row['firstname'];
$_SESSION['lname'] = $row['lastname'];
$_SESSION['photo_url'] = $row['photo_url'] ?? '';

header("Location: home.php");
exit();

?>
