<?php
    include "include/top.php";
    
    // $axios = new axios;
    
    // $axios->postREFamilyData = $_SESSION["getPostREFamily"];
    // $axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
    // $axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
    // $axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
    // $axios->cookies = $_SESSION["cookies"];
    
    // $result = $axios->getVote();
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Voti | REDignus</title>
        <?php include "include/head.php"; ?>
        <style>
            .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
                border-color: rgb(0, 0, 0, 0.5) rgb(0, 0, 0, 0.5) #fff;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <!-- Apertura Wrapper -->
            <!-- Sidebar -->
            <?php include "include/sidebar.php"; ?>
            <!-- Page Content  -->
            <div id="content">
                <?php include "include/navbar.php"; ?>
                <div class="container-fluid">
                    <br>
                    <div class="jumbotron">
                        <div class="text-center">
                            <a href="opzioni" class="text-right badge badge-pill badge-secondary">Torna alla vecchia versione</a>
                        </div>
                        <br>
                        <div class="card-header" style="background-color: transparent; border-bottom: 1px solid rgb(0, 0, 0, 0.5);">
                            <ul class="nav nav-tabs card-header-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" id="votimenu" onclick="voti();">Voti</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="mediamenu" onclick="media();">Media</a>
                                </li>
                            </ul>
                        </div>
                        <div id="votilista">
                        <br>
                            <div class="text-center">
                                <br>
                                <h2>Caricamento</h2>
                                <i class="fas fa-circle-notch fa-spin fa-4x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include "include/footer.php"; ?>    
                <script>
                    function voti() {
                        // Ajax Stringa voti
                        $('#votilista').html('<div class="text-center"><br><h2>Caricamento</h2><i class="fas fa-circle-notch fa-spin fa-4x"></i></div>');
                        $( "#votimenu" ).addClass( "active" );
                        $( "#mediamenu" ).removeClass( "active" );
                        $.getJSON('api/ajax/voti', function(data) {
                            // Per ogni voto
                            var numcollaps = 0;
                            $('#votilista').html('<br>');
                            data.forEach(element => {
                                numcollaps++;
                                // Imposta il colore del voto
                                if (null === element.realVote) {
                                    var colorevoto = "bg-primary";
                                } else if (element.realVote.replace(',', '.') >= 6) {
                                    var colorevoto = "bg-success";
                                } else {
                                var colorevoto = "bg-danger"; 
                                }

                                if ("" === element.comment) {
                                    asterisco = "";
                                } else {
                                    asterisco = '<span style="color: red;">*</span>';
                                }

                                // Salva le informazioni precedenti
                                var precedente = $('#votilista').html();
                                // Stampa il voto più le informazioni precedenti
                                $('#votilista').html(precedente + '<div class="row" data-toggle="collapse" data-target="#collapse'+numcollaps+'" aria-expanded="true" aria-controls="collapse'+numcollaps+'">'+
                                    '<div class="col-8 col-lg-10">'+
                                        '<div class="row" style="height: 100%;">'+
                                            '<div class="col text-center">'+
                                                '<h5>'+asterisco+' '+element.date+' </h5>'+
                                                '<hr>'+
                                                '<h5> '+element.topic+' </h5>'+
                                                '<div id="collapse'+numcollaps+'" class="collapse">'+
                                                    '<p> '+element.comment+' </p>'+
                                                    '<p><small class="text-body"> '+element.teacher+' </small></p>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-4 col-lg-2">'+
                                        '<div class="p-2 '+colorevoto+' text-white" style="height: 100%; display: flex; justify-content: center; align-items: center;">'+
                                            '<div class="text-center">'+
                                                '<p class="text-white"> '+element.type+' </p>'+
                                            '<h5> '+element.vote+' </h5>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                                '<hr style="border-top: 1px solid rgb(0, 0, 0, 0.5)">');
                            });
                        });
                    }
                    voti();
                </script>
                <script>
                    // Ajax Stringa voti
                    function media() {
                        $('#votilista').html('<div class="text-center"><br><h2>Caricamento</h2><i class="fas fa-circle-notch fa-spin fa-4x"></i></div>');
                        $("#mediamenu").addClass("active");
                        $("#votimenu").removeClass("active");
                        $.getJSON('api/ajax/mediaMateria', function(data) {
                            // Per ogni voto
                            var numcollaps = 0;
                            $('#votilista').html('<br>');
                            data.forEach(element => {
                                numcollaps++;
                                // Imposta il colore del voto
                                if (null === element.average) {
                                    var colorevoto = "bg-primary";
                                } else if (element.average.replace(',', '.') >= 6) {
                                    var colorevoto = "bg-success";
                                } else {
                                var colorevoto = "bg-danger"; 
                                }

                                // Salva le informazioni precedenti
                                var precedente = $('#votilista').html();

                                // Stampa il voto più le informazioni precedenti
                                $('#votilista').html(precedente + '<div class="row" data-toggle="collapse" data-target="#collapse'+numcollaps+'" aria-expanded="true" aria-controls="collapse'+numcollaps+'">'+
                                    '<div class="col-8 col-lg-10">'+
                                        '<div class="row" style="height: 100%;" >'+
                                            '<div class="col text-center" style="height: 100%; display: flex; justify-content: center; align-items: center;">'+
                                                '<h5> '+element.name+' </h5>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-4 col-lg-2">'+
                                        '<div class="p-2 '+colorevoto+' text-white" style="height: 100%; display: flex; justify-content: center; align-items: center;">'+
                                            '<div class="text-center">'+
                                            '<h5> '+element.average+' </h5>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                                '<hr style="border-top: 1px solid rgb(0, 0, 0, 0.5)">');
                            });
                        });
                    }
                </script>
            </div><!-- Page Content -->    
        </div><!-- Chiusura Wrapper -->
    </body>
</html>