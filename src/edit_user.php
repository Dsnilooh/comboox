
<?php
ob_start();
require_once("../config/database.php");
ob_end_clean();

$id = $_GET['id'] ?? '';

if (!ctype_digit((string)$id)) {
    die("ID de usuario no válido");
}

// CONSULTAR USUARIO
$sql = "
    SELECT
        id,
        firstname,
        lastname,
        email,
        mobile_phone,
        photo_url
    FROM public.users
    WHERE id = $1
";

$result = pg_query_params($supa_conn, $sql, array($id));

if (!$result) {
    die("Error en la consulta: " . pg_last_error($supa_conn));
}

$user = pg_fetch_assoc($result);

if (!$user) {
    die("Usuario no encontrado");
}

$photoUrl = !empty($user['photo_url'])
    ? $user['photo_url']
    : 'photos/user_default.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>COMBOOX - Editar usuario</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f3ef;
            margin: 0;
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #E3A852;
            margin-bottom: 25px;
        }

        .container {
            background-color: white;
            width: 90%;
            max-width: 550px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.12);
        }

        .photo {
            text-align: center;
            margin-bottom: 25px;
        }

        .photo img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #E3A852;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #444;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        input[type="file"] {
            width: 100%;
        }

        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        button {
            background-color: #E3A852;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            flex: 1;
            font-size: 15px;
        }

        button:hover {
            background-color: #cc913c;
        }

        .cancel {
            background-color: #555;
            color: white;
            text-decoration: none;
            text-align: center;
            padding: 12px;
            border-radius: 6px;
            flex: 1;
        }

        .cancel:hover {
            background-color: #333;
        }
    </style>
</head>

<body>

    <h1>Editar usuario</h1>

    <div class="container">

        <form action="update_user.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id"
                   value="<?php echo htmlspecialchars($user['id']); ?>">

            <div class="photo">
                <img
                    src="<?php echo htmlspecialchars($photoUrl, ENT_QUOTES, 'UTF-8'); ?>"
                    alt="Foto del usuario"
                    onerror="this.onerror=null;this.src='photos/user_default.png';"
                >
                <p>Fotografía del usuario</p>
            </div>

            <div class="form-group">
                <label>Nombre:</label>
                <input type="text" name="firstname"
                       value="<?php echo htmlspecialchars($user['firstname']); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Apellido:</label>
                <input type="text" name="lastname"
                       value="<?php echo htmlspecialchars($user['lastname']); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Correo electrónico:</label>
                <input type="email" name="email"
                       value="<?php echo htmlspecialchars($user['email']); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Celular:</label>
                <input type="text" name="mobile_phone"
                       value="<?php echo htmlspecialchars($user['mobile_phone']); ?>"
                       required>
            </div>

            <div class="form-group">
                <label>Cambiar fotografía:</label>
                <input type="file" name="photo" accept=".jpg,.jpeg,.png">
            </div>

            <div class="buttons">
                <button type="submit">Guardar cambios</button>

                <a href="list_users.php" class="cancel">Cancelar</a>
            </div>

        </form>

    </div>

</body>
</html>
