
<?php
ob_start();
require_once("../config/database.php");
ob_end_clean();

// VERIFICAR ENVIO DEL FORMULARIO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list_users.php");
    exit;
}

// DATOS DEL FORMULARIO
$id = trim($_POST['id'] ?? '');
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile_phone = trim($_POST['mobile_phone'] ?? '');

if ($id === '' || $firstname === '' || $lastname === '' || $email === '') {
    die("Faltan datos obligatorios");
}

// VERIFICAR QUE EL CELULAR NO PERTENEZCA A OTRO USUARIO
$sql_check = "
    SELECT id
    FROM public.users
    WHERE mobile_phone = $1
    AND id <> $2
    LIMIT 1
";

$check = pg_query_params(
    $supa_conn,
    $sql_check,
    [$mobile_phone, $id]
);

if (!$check) {
    die("Error al verificar celular: " . pg_last_error($supa_conn));
}

if (pg_num_rows($check) > 0) {
    echo "<script>
        alert('Este número de celular ya está registrado por otro usuario.');
        window.history.back();
    </script>";
    exit;
}

// CONSULTAR USUARIO Y FOTO ACTUAL
$sql = "SELECT photo_url FROM public.users WHERE id = $1";

$result = pg_query_params($supa_conn, $sql, [$id]);

if (!$result) {
    die("Error al consultar usuario: " . pg_last_error($supa_conn));
}

$user = pg_fetch_assoc($result);

if (!$user) {
    die("Usuario no encontrado");
}

$photo_url = $user['photo_url'] ?? '';

// SUBIR FOTOGRAFIA NUEVA
if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {

    if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        die("Error al cargar la fotografía");
    }

    if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
        die("La fotografía supera el límite de 5 MB");
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($_FILES['photo']['tmp_name']);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];

    if (!isset($allowed[$mime])) {
        die("Solo se permiten imágenes JPG o PNG");
    }

    $upload_dir = __DIR__ . '/photos/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $filename = 'user_' . $id . '_' . bin2hex(random_bytes(8))
                . '.' . $allowed[$mime];

    if (!move_uploaded_file(
        $_FILES['photo']['tmp_name'],
        $upload_dir . $filename
    )) {
        die("No se pudo guardar la fotografía");
    }

    $photo_url = 'photos/' . $filename;
}

// ACTUALIZAR DATOS EN SUPABASE
$sql_update = "
    UPDATE public.users
    SET
        firstname = $1,
        lastname = $2,
        email = $3,
        mobile_phone = $4,
        photo_url = $5
    WHERE id = $6
";

$update = pg_query_params(
    $supa_conn,
    $sql_update,
    [$firstname, $lastname, $email, $mobile_phone, $photo_url, $id]
);

if (!$update) {
    die("Error al actualizar: " . pg_last_error($supa_conn));
}

// REGRESAR AL LISTADO
header("Location: list_users.php");
exit;
?>
