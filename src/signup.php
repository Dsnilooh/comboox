
<?php

// Get database connection
require("../config/database.php");

// Get data from HTML form
$f_name = $_POST['fname'];
$l_name = $_POST['lname'];
$m_phone = $_POST['mphone'];
$e_mail = $_POST['email'];
$p_assword = $_POST['psw'];

// Encrypt password
$pass_enc = md5($p_assword);

// SQL TO VALIDATION IF USER ALREADY EXISTS
$sql_validation = "
    SELECT
        u.email,
        u.mobile_phone
    FROM
        users u
    WHERE
        u.email = '$e_mail'
        OR u.mobile_phone = '$m_phone'
";

// Execute query in Supabase
$supa_res = pg_query($supa_conn, $sql_validation);

if ($supa_res) {

    if (pg_num_rows($supa_res) > 0) {

        echo "<script>
            alert('User email or mobile phone already exists :::');
            window.location.href='signup.html';
        </script>";

    } else {

        // Insert user into Supabase
        $supa_sql = "
            INSERT INTO public.users (
                firstname,
                lastname,
                mobile_phone,
                email,
                password
            )
            VALUES (
                '$f_name',
                '$l_name',
                '$m_phone',
                '$e_mail',
                '$pass_enc'
            )
        ";

        // Execute query in Supabase
        $supa_res = pg_query($supa_conn, $supa_sql);

        if ($supa_res) {

            echo "<script>
                alert('User has been created successfully :::');
                window.location.href='login.html';
            </script>";

        } else {

            echo "<script>
                alert('User has not been created into Supabase !!!');
                window.location.href='signup.html';
            </script>";

        }
    }

} else {

    echo "<script>
        alert('Error connecting to or querying Supabase !!!');
        window.location.href='signup.html';
    </script>";

}

?>
