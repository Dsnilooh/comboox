
<?php
ob_start();
require_once("../config/database.php");
ob_end_clean();

// RECIBIR ID DEL USUARIO
$id = $_POST['id'] ?? $_GET['id'] ?? '';

if (!ctype_digit((string)$id)) {
    die("ID de usuario no válido");
}

// RECIBIR ACCION
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// CONSULTAR USUARIO
$sql = "
    SELECT id, firstname, lastname
    FROM public.users
    WHERE id = $1
";

$result = pg_query_params($supa_conn, $sql, [$id]);

if (!$result) {
    die("Error al consultar usuario: " . pg_last_error($supa_conn));
}

$user = pg_fetch_assoc($result);

if (!$user) {
    die("Usuario no encontrado");
}

// ELIMINAR O INACTIVAR USUARIO
if ($action !== '') {

    if ($action === 'eliminar') {

        // ELIMINAR DEFINITIVAMENTE
        $sql = "
            DELETE FROM public.users
            WHERE id = $1
        ";

        $res = pg_query_params($supa_conn, $sql, [$id]);

    } elseif ($action === 'inactivar') {

        // INACTIVAR USUARIO
        $sql = "
            UPDATE public.users
            SET status = false
            WHERE id = $1
        ";

        $res = pg_query_params($supa_conn, $sql, [$id]);

    } else {
        die("Acción no válida");
    }

    if (!$res) {
        die("Error al procesar la acción: " . pg_last_error($supa_conn));
    }

    header("Location: list_users.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar usuario</title>
</head>

<body>

<center>

    <h2>¿Qué deseas hacer con este usuario?</h2>

    <p>
        Usuario:
        <?php
        echo htmlspecialchars(
            $user['firstname'] . ' ' . $user['lastname'],
            ENT_QUOTES,
            'UTF-8'
        );
        ?>
    </p>

    <br>

    <!-- ELIMINAR USUARIO -->
    <a href="./delete_users.php?id=<?php echo urlencode($id); ?>&action=eliminar"
       onclick="return confirm('¿Deseas eliminar definitivamente este usuario?')">
        Eliminar
    </a>

    <br><br>

    <!-- INACTIVAR USUARIO -->
    <a href="./delete_users.php?id=<?php echo urlencode($id); ?>&action=inactivar"
       onclick="return confirm('¿Deseas inactivar este usuario?')">
        Inactivar
    </a>

    <br><br>

    <!-- CANCELAR -->
    <a href="./list_users.php">Cancelar</a>

</center>

</body>
</html>
