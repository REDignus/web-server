<?php
include "include/top.php";

$axios = new axios;

$axios->postREFamilyData = $_SESSION["getPostREFamily"];
$axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
$axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
$axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
$axios->cookies = $_SESSION["cookies"];

// $result = $axios->getAbsences();
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
                <?php include "include/navbar.php";?>
                <div class="container text-center">
                    <br>
                    <div class="p-3 mb-2 bg-warning text-white">Ricorda sempre che questa è una beta, per cui potrebbero esserci dei <i class="fas fa-bug"></i>, se li trovi <a href="https://t.me/REDignusHelpBot">segnalaceli</a>.</div>
                    <!--<div class="jumbotron">
                        <iframe src="https://docs.google.com/forms/d/e/1FAIpQLSfAjA_zIVLr9eoV5XYV0-wc8d_-lHHFtyjjFuGAFhp7hEyobg/viewform?embedded=true" width="100%" height="797px" frameborder="0" marginheight="0" marginwidth="0">Caricamento in corso...</iframe>
                    </div>-->
                    <div class="jumbotron">
                        <h1>Benvenuto in <b>REDignus</b></h1>
                        <hr>
                        <p>Puoi contattarci su <a href="https://t.me/REDignusHelpBot">Telegram @REDignusHelpBot</a> oppure se sei un po' più vecchio stile a <a href="mailto:info@redignus.it">info@redignus.it</a></p>
                        <p>Made with <i class="fas fa-heart" style="color: #ff6b6b;"></i> by <a href="https://giuseppini.me/">Marco Giuseppini</a> of Federico Caffe.</p>
                    </div>
                    <div class="jumbotron">
                        <h3>Changelog:</h3>
                        <br>
                        <p>
                            --------------(V 0.3.5)--------------<br>
                            <b>Sistemate</b> le assenze<br>
                            <b>Sistemato</b> l'orario<br>
                            <small>- Aggiunti nuovi bug per sistemarli in futuro</small><br>
                            --------------(V 0.3.0)--------------<br>
                            <b>Aggiunta</b> la visualizzazione dell'orario<br>
                            <b>Aggiunti</b> gli obiettivi nei voti<br>
                            <b>Cambiata</b> la barra per la navigazione da mobile<br>
                            --------------(V 0.2.1)--------------<br>
                            <b>Aggiunta</b> la possibilità di avere più componenti nello stesso account<br>
                            <b>Aggiunta</b> una nuova visualizzazione dei voti [beta]<br>
                            <b>Aggiunta</b> una nuova visualizzazione del registro [beta]<br>
                            --------------(V 0.1.2)--------------<br>
                            <b>Cambiato</b> il funzionamento "tecnico" della sidebar<br>
                            <b>Attivate</b> alcune opzioni<br>
                            --------------(V 0.1.1)--------------<br>
                            <b>Aggiunte</b> le circolari<br>
                            <b>Aggiunto</b> il tasto menù per gli utenti IOS (rimovibile dalle opzioni)
                        </p>
                        <!-- Mi spiace Mario ma la principessa è in un altro castello -->
                    </div>
                    <div class="jumbotron">
                        <h3>Problemi noti:</h3>
                        <br>
                        <p>
                            - Alcuni tasti della barra laterale non funzionano (Opzioni, Github, Contatti, FAQ...)<br>
                            - Non c'è un tasto logout (evidentemente questi programmatori non sanno implementare un bottone..)<br>
                            - La pagina "assenze" non funziona
                            - La pagina circolari fa schifo (lo sappiamo, ora ci lavoriamo)
                        </p>
                    </div>
                </div>
                <?php include "include/footer.php";?>
            </div><!-- Page Content -->
        </div><!-- Chiusura Wrapper -->
    </body>
</html>
