
<?php
require("../config/database.php");

$sql = "
    SELECT
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
        users
    ORDER BY
        lastname ASC
";

// RESULTADO DE ESA CONSULTA
$res = pg_query($local_conn, $sql);

if (!$res) {
    die("Error en la consulta: " . pg_last_error($local_conn));
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

    if (!empty($row['photo_url'])) {
        echo "<td><img src='../src/" . htmlspecialchars($row['photo_url']) . "' width='60' height='60' alt='User photo'></td>";
   
      
}
    echo "<td>
        <img src ='icons/search.png' width= '20' height='20'>&nbsp;
        <img src ='icons/edit.png' width= '20' height='20'>&nbsp;
        <img src ='icons/delete.png' width= '20'>
        </td>
        ";
        echo "</tr>";
    }
?>