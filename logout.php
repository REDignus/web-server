<?php
    include "include/top.php";
    
    session_destroy();
    
    // unset cookies
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            if ($name != "cfscuola") {
                setcookie($name, '', time()-1000);
                setcookie($name, '', time()-1000, '/');
            }
        }
    }
    
    header("location: /login");
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>LogOut | REDignus</title>
        <?php include "include/head.php";?>
    </head>
    <body>
        <div class="wrapper">
            <!-- Apertura Wrapper -->
            <!-- Sidebar -->
            <?php include "include/sidebar.php";?>
            <!-- Page Content  -->
            <div id="content">
                <?php include "include/navbar.php"; ?>
                <div class="container">
                    <br>
                    <div class="jumbotron">
                        <p>EXIT</p>
                    </div>
                </div>
                <?php include "include/footer.php";?>
            </div><!-- Page Content -->
        </div><!-- Chiusura Wrapper -->
    </body>
</html>