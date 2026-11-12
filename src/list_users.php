
<?php
require("../config/database.php");

$sql = "
    SELECT
        id,
        firstname,
        lastname,
        mobile_phone,
        email,
        CASE
            WHEN users.status = true THEN 'Active'
            ELSE 'Inactive'
        END AS user_status,
        photo_url
    FROM
        public.users
    ORDER BY
        lastname ASC
";

// RESULTADO DE ESA CONSULTA
$res = pg_query($supa_conn, $sql);

if (!$res) {
    die("Error en la consulta: " . pg_last_error($supa_conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>comboox - List Users</title>
</head>
<body>

    <center>
        <h3>List all users</h3>
    </center>

    <table border="1" align="center">
        <tr>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Mobile Phone</th>
            <th>Email</th>
            <th>User Status</th>
            <th>Photo</th>
            <th>...</th>
        </tr>

        <?php
        while ($row = pg_fetch_assoc($res)) {

            echo "<tr>";

            echo "<td>" . htmlspecialchars($row['firstname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['lastname']) . "</td>";
            echo "<td>" . htmlspecialchars($row['mobile_phone']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['user_status']) . "</td>";

            // FOTO DEL USUARIO
            echo "<td>";

            $foto = trim($row['photo_url'] ?? '');

            if (empty($foto)) {
                $foto = 'photos/user_default.png';
            }

            $foto = ltrim($foto, '/');

            echo "<img src='" . htmlspecialchars($foto) . "' width='60' height='60' alt='User photo'>";

            echo "</td>";

            // ICONOS DE ACCIONES
            echo "<td>";

            echo "<img src='icons/search.png' width='20' height='20' alt='Search'>&nbsp;";

            echo "<a href='edit_user.php?id=" . urlencode($row['id']) . "'>";
            echo "<img src='icons/edit.png' width='20' height='20' alt='Edit'>";
            echo "</a>&nbsp;";

            echo "<img src='icons/delete.png' width='20' height='20' alt='Delete'>";

            echo "</td>";

            echo "</tr>";
        }
        ?>
    </table>

</body>
</html>
