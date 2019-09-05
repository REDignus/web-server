<?php
    include "include/top.php";
    
    $axios = new axios;
    
    $axios->postREFamilyData = $_SESSION["getPostREFamily"];
    $axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
    $axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
    $axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
    $axios->cookies = $_SESSION["cookies"];
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Home | REDignus</title>
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
                <div class="container-fluid">
                    <div class="jumbotron">
                        <div class="text-center">
                            <a href="registro-ricerca" class="text-center badge badge-pill badge-primary">Usa le opzioni di ricerca</a>&nbsp;&nbsp;&nbsp;
                            <a href="opzioni" class="text-right badge badge-pill badge-secondary">Cambia la grafica</a>
                        </div>
                        <br>
                        <?php if ($_COOKIE["graficaregistro"] == "1"):
                            $result = $axios->getHomework($_GET["date"]);
                            include("dist/php/registro/1.php");
                            include "include/footer.php";
                        ?>
                        <?php elseif ($_COOKIE["graficaregistro"] == "2"):
                            $result = $axios->getHomework($_GET["date"]);
                            include("dist/php/registro/2.php");
                            include "include/footer.php";
                        ?>
                        <?php else: 
                            include("dist/php/registro/3.php");
                        ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div><!-- Page Content -->
        </div><!-- Chiusura Wrapper -->
    </body>
</html>