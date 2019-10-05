<?php
    include "include/top.php";
    
    $axios = new axios;
    
    $axios->postREFamilyData = $_SESSION["getPostREFamily"];
    $axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
    $axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
    $axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
    $axios->cookies = $_SESSION["cookies"];
    
    $result = $axios->getVote();
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
                    <br>
                    <div class="jumbotron">
                        <div class="row text-center" id="dettagliGiorno"></div>
                        <div class="row text-center">
                            <div class="col-2 text-center my-auto" onclick="changeDay(-1);"><i class="fas fa-angle-up fa-rotate-270"></i></div>
                            <div class="col-8" id="orario">
                                <div class="text-center"><br><h2>Caricamento</h2><i class="fas fa-circle-notch fa-spin fa-4x"></i></div>
                            </div>
                            <div class="col-2 text-center my-auto" onclick="changeDay(+1);"><i class="fas fa-angle-up fa-rotate-90"></i></div>
                        </div>
                    </div>
                    <h5>Info</h5>
                        <p><span style="color: red;">* </span>L'ultima ora termina alle 00:00 lamentatevi con la vicepresidenza non con me</p>
                </div>
                
                <?php include "include/footer.php";?>
                <script>
                    var plusday = 0;
                    function changeDay(day) { 
                        plusday += day;
                        if (plusday < 0) {
                            plusday++;
                        }
                        stat();
                    }
                    
                    var ajaxorario;
                    $.getJSON('api/ajax/orario', function(data) {
                        ajaxorario = data;
                        stat();
                    });
                    

                    function stat() {
                        $('#orario').html('<div class="text-center"><br><h2>Caricamento</h2><i class="fas fa-circle-notch fa-spin fa-4x"></i></div>');

                        var data = ajaxorario;
                            var day = new Date();
                            var array_giorni = ["Lunedi", "Lunedi", "Martedi", "Mercoledi", "Giovedi", "Venerdi", "Sabato"]
                            var print = "";
                            data.forEach(giorno => {
                                if (giorno.date.day == array_giorni[day.getDay()+plusday]) {
                                    $('#orario').html("<hr>");
                                    $('#dettagliGiorno').html(`<div class="col-12"><div class="alert alert-dark" role="alert"><h1>${giorno.date.day}</h1>${giorno.date.date}<br><h2></h2></div></div>`);
                                    var ora = 0;
                                    giorno.schedule.forEach(orario => {
                                        print = "";
                                        ora++;
                                        print += `<div data-toggle="collapse" data-target="#collapse${ora}" aria-expanded="true" aria-controls="collapse${ora}"><h6 id="numerora${ora}">${ora}ª Ora</h6><h3>${orario.schedule[0].subject}</h3><div id="collapse${ora}" class="collapse" aria-labelledby="heading${ora}"<br><p><small>`;
                                        var ninsegnanti = 0;
                                        orario.schedule.forEach(materie => {
                                            ninsegnanti++;
                                            print +=  `<b>Insegnante:</b> ${materie.teachers}<br>`;
                                        });
                                        print += `<b>Orario:</b> ${orario.info.start} - ${orario.info.end} <small></p></div></div><hr>`;
                                        
                                        $('#orario').append(print);

                                        if (ninsegnanti > 1) {
                                            $(`#numerora${ora}`).addClass("badge badge-secondary");
                                        }
                                    });

                                }
                            });
                            if (print == "") {
                                $('#orario').html("<h1>Giorno non presente</h1>");
                                $('#dettagliGiorno').html("");

                            }
                    }
                </script>
            </div><!-- Page Content -->
        </div><!-- Chiusura Wrapper -->
    </body>
</html>