<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="Stylesheet" type="text/css" href="login.css" />
</head>
<body>

<div class = "main">
    <form class="right" action="login.php" method="post">
        <input class ="input" type="text" name="user_email" placeholder="email@example.com">
        <input class ="input" type="password" name="user_password" placeholder="password">
<!--            <a href="home.php">-->
            <input class ="button" type="submit" name="login" value="Log in">
<!--            </a>-->
    </form>

    <div class="left" >
        <h1>Assign</h1>
        <h1>Prioritize</h1>
        <h1>Focus</h1>
    </div>

<div>
    <?php
        if(isset($_POST['login'])) {
            $user_email = filter_input(INPUT_POST, 'user_email', FILTER_VALIDATE_EMAIL);
            $user_password = $_POST['user_password'];
            if (!empty($user_email) && !empty($user_password)) {
                $_SESSION['user_email'] = $user_email;
                $_SESSION['user_password'] = $user_password;

                header("Location: home.php");
            } else {
                echo "Missing arguments";
            }
        }
    ?>
</div>
</body>
</html>
