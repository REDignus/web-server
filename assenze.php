<?php
include "include/top.php";

$axios = new axios;

$axios->postREFamilyData = $_SESSION["getPostREFamily"];
$axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
$axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
$axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
$axios->cookies = $_SESSION["cookies"];

$result = $axios->getAbsences();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Assenze | REDignus</title>
        <?php include "include/head.php";?>
    </head>
    <body>
        <div class="wrapper">
            <!-- Apertura Wrapper -->
            <!-- Sidebar -->
            <?php include "include/sidebar.php";?>
            <!-- Page Content  -->
            <div id="content">
                <?php include "include/navbar.php";?>
                <div class="container-fluid">
                    <br>
                    <div class="jumbotron">
                        <div class="text-center">
                            <h3>Ore di assenza totali:</h3>
                            <div id="oreassenza">
                                <button type="button" class="btn btn-primary" onclick="calcolaAssenza()">Clicca qui per calcolarle...</button>
                            </div>
                        </div>
                        <br><hr><br>
                        <!-- GIUSTIFICATE start -->
                        <div class="card mb-5">
                            <div class="card-header bg-danger text-white">
                                Non giustificate
                            </div>
                            <div class="card-body" id="tojustify">
                            </div>
                        </div>
                        <!-- GIUSTIFICATE end -->
                        <!-- NON_GIUSTIFICATE start -->
                        <div class="card">
                            <div class="card-header bg-success text-white ">
                                Giustificate
                            </div>
                            <div class="card-body" id="justified">
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
                <?php include "include/footer.php";?>
                <script>
                    function calcolaAssenza() {
                        $('#oreassenza').html("<h3><i class='fas fa-circle-notch fa-spin'></i></h3>");
                        $.get('api/ajax/oreAssenza', function(data) {
                            $('#oreassenza').html("<h3>"+data+" Ore</h3>")
                        });
                    }


                    function assenza() {
                        // $('#registroelementi').html('<div class="text-center"><br><h2>Caricamento</h2><i class="fas fa-circle-notch fa-spin fa-4x"></i></div>');
                        $.getJSON('https://redignus.it/api/ajax/assenze', function(data) {
                            if (data.tojustify != null) {
                                var output_tojustify = "";
                                data.tojustify.forEach(element => {
                                    if (element.type == "Assenza") {
                                        output_tojustify += `<hr style="border-top: 1px solid rgba(0,0,0,.8);"><div class="row">
                                            <div class="col-8 col-lg-10">
                                                <div class="row" style="height: 100%;">
                                                    <div class="col text-center">
                                                        <h5>${element.date}</h5>
                                                        <hr>
                                                        <h5>${element.type}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4 col-lg-2">
                                                <div class="p-2 bg-danger text-white" style="height: 100%; display: flex; justify-content: center; align-items: center;">
                                                    <div class="text-center">
                                                        <h5 class="text-white">${element.type.substring(0, 3)}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;
                                    } else {
                                        output_tojustify += `<hr style="border-top: 1px solid rgba(0,0,0,.8);"><div class="row">
                                            <div class="col-8 col-lg-10">
                                                <div class="row" style="height: 100%;">
                                                    <div class="col text-center">
                                                        <h5>${element.date}</h5>
                                                        <hr>
                                                        <h5>${element.type}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4 col-lg-2">
                                                <div class="p-2 bg-warning text-dark" style="height: 100%; display: flex; justify-content: center; align-items: center;">
                                                    <div class="text-center">
                                                        <h5 class="text-dark">${element.type.substring(0, 3)}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>`;

                                    }
                                });
                            }
                            if (data.justified != null) {
                                var output_justified = "";
                                data.justified.forEach(element => {
                                    output_justified += `<hr style="border-top: 1px solid rgba(0,0,0,.8);"><div class="row">
                                        <div class="col-8 col-lg-10">
                                            <div class="row" style="height: 100%;">
                                                <div class="col text-center">
                                                    <h5>${element.date}</h5>
                                                    <hr>
                                                    <h5>${element.type}</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 col-lg-2">
                                            <div class="p-2 bg-success text-white" style="height: 100%; display: flex; justify-content: center; align-items: center;">
                                                <div class="text-center">
                                                    <h5 class="text-white">${element.type.substring(0, 3)}</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                });
                            }
                            $("#tojustify").html(output_tojustify+`<hr style="border-top: 1px solid rgba(0,0,0,.8);">`);
                            $("#justified").html(output_justified+`<hr style="border-top: 1px solid rgba(0,0,0,.8);">`);

                        });
                    }

                    assenza();
                </script>
            </div><!-- Page Content -->
        </div><!-- Chiusura Wrapper -->
    </body>
</html>