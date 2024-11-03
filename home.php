<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="Stylesheet" type="text/css" href="start.css" />
</head>
<body>
<div>
    <form action="home.php" method="post">
        <input type="submit" name="logout" value="Log out">
    </form>
    <?php

//        var_dump(
//            pg_connect("
//                host=localhost
//                port=5432
//                dbname=postgres
//                user=postgres
//                password=wdpai
//            ")
//        );
        echo $_SESSION['user_email'] . "<br>";
        echo $_SESSION['user_password'] . "<br>";
        if(isset($_POST['logout']))
        {
            session_destroy();
            header("Location: login.php");
        }
    ?>
</div>

</body>
</html>
