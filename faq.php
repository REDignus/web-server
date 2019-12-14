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
    <?php include "include/head.php"; ?>
</head>

<body>
    <div class="wrapper">
        <!-- Apertura Wrapper -->
        <!-- Sidebar -->
        <?php include "include/sidebar.php"; ?>
        <!-- Page Content  -->
        <div id="content">
            <?php include "include/navbar.php"; ?>
            <div class="container">
                <br>
                <div class="p-3 mb-2 bg-warning text-white text-center">Ricorda sempre che questa è una beta, per cui potrebbero
                    esserci dei
                    <i class="fas fa-bug"></i>, se li trovi
                    <a href="https://t.me/REDignusHelpBot">segnalaceli</a>.
                </div>
                <!--<div class="jumbotron"><iframe src="https://docs.google.com/forms/d/e/1FAIpQLSfAjA_zIVLr9eoV5XYV0-wc8d_-lHHFtyjjFuGAFhp7hEyobg/viewform?embedded=true" width="100%" height="797px" frameborder="0" marginheight="0" marginwidth="0">Caricamento in corso...</iframe></div>-->
                <div class="jumbotron text-center">
                    <h1>Benvenuto in
                        <b>REDignus</b>
                    </h1>
                    <div class="d-md-none">
                        <p>
                            <hr>
                            <b>Trascina da sinistra verso destra per aprire il menù.
                                <br>Forza prova non è così
                                difficile.
                                <br>
                                <i class="fas fa-arrow-left"></i>
                            </b>
                            <hr>
                        </p>
                    </div>
                    <p>Puoi contattarci su
                        <a href="https://t.me/REDignusHelpBot">Telegram @REDignusHelpBot</a> oppure
                        se sei un po' più vecchio stile a
                        <a href="mailto:info@redignus.it">info@redignus.it</a>
                    </p>
                    <p>Made with
                        <i class="fas fa-heart" style="color: #ff6b6b;"></i> by
                        <a href="https://giuseppini.me/">Marco Giuseppini</a> of Federico Caffe.
                    </p>
                </div>
                <div class="jumbotron">
                <div class="text-center">
                    <h3>FAQ:</h3>
                    <h6>Frequently Asked Question</h6>
                    <br>
                    </div>
                    <div id="accordion">
                    <hr>
                    <!-- q1 -->
                        <div>
                            <div id="1">
                                <h6 class="mb-0">
                                    <b data-toggle="collapse" data-target="#collapseOne" aria-expanded="true"
                                        aria-controls="collapseOne">
                                        Cosè redignus? 
                                    </b><i class="fas fa-chevron-down"></i>
                                    </h5>
                            </div>
                            <div id="collapseOne" class="collapse fade show" aria-labelledby="1" data-parent="#accordion">
                                <div class="card-body">
                                    Redignus è semplicemente una "
                                    <a href="#" data-toggle="tooltip" data-placement="top"
                                        title="Una skin, nella terminologia informatica e di Wikipedia, è l'aspetto grafico e di impaginazione con cui può apparire il sito web di Wikipedia.">skin</a>"
                                    con notevoli migliorie.

                                </div>
                            </div>
                        </div>
                        <div>
                            <hr>
                            <!-- q2 -->
                            <div>
                                <div id="2">
                                    <h6 class="mb-0">
                                        <b data-toggle="collapse" data-target="#collapse2" aria-expanded="true"
                                            aria-controls="collapse2">
                                            Come posso aiutare?
                                        </b><i class="fas fa-chevron-down"></i>
                                        </h5>
                                </div>
                                <div id="collapse2" class="collapse" aria-labelledby="2" data-parent="#accordion">
                                    <div class="card-body">
                                        Redignus è un sistema open-source disponibile per chiunque, basta
                                        andare su
                                        <a href="https://github.com/REDignus">github.com</a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <hr>
                                <!-- q3 -->
                                <div>
                                    <div id="3">
                                        <h6 class="mb-0">
                                            <b data-toggle="collapse" data-target="#collapse3" aria-expanded="true"
                                                aria-controls="collapse3">
                                                Perché alcuni dati sono errati?
                                            </b><i class="fas fa-chevron-down"></i>
                                            </h5>
                                    </div>
                                    <div id="collapse3" class="collapse" aria-labelledby="3" data-parent="#accordion">
                                        <div class="card-body">
                                            Questo non è determinato da noi, ma dai professori e
                                            la presidenza (o axios, la cosa non ci stupirebbe).
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <hr>
                                    <!-- q4 -->
                                    <div>
                                        <div id="4">
                                            <h6 class="mb-0">
                                                <b data-toggle="collapse" data-target="#collapse4" aria-expanded="true"
                                                    aria-controls="collapse4">
                                                    É sicuro?
                                                </b><i class="fas fa-chevron-down"></i>
                                                </h5>
                                        </div>
                                        <div id="collapse4" class="collapse" aria-labelledby="4"
                                            data-parent="#accordion">
                                            <div class="card-body">
                                                Questo è da chiedere al gestore del registro (axios..cof..le
                                                password..cof.. non in chiaro c: ).
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <hr>
                                        <!-- q5 -->
                                        <div>
                                            <div id="5">
                                                <h6 class="mb-0">
                                                    <b data-toggle="collapse" data-target="#collapse5"
                                                        aria-expanded="true" aria-controls="collapse5">
                                                        Rubate le password o dati?
                                                    </b><i class="fas fa-chevron-down"></i>
                                                    </h5>
                                            </div>
                                            <div id="collapse5" class="collapse" aria-labelledby="5"
                                                data-parent="#accordion">
                                                <div class="card-body">
                                                    Assolutamente no, non li salviamo per nessuno scopo (non
                                                    ci interessano molto \_(ツ)_/ ).
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- Q6 -->
                                        <div>
                                            <div id="6">
                                                <h6 class="mb-0">
                                                    <b data-toggle="collapse" data-target="#collapse6"
                                                        aria-expanded="true" aria-controls="collapse6">
                                                        Vuoi offrirci un caffé? (spoiler questa non è una domanda)
                                                    </b><i class="fas fa-chevron-down"></i>
                                                    </h5>
                                            </div>
                                            <div id="collapse6" class="collapse" aria-labelledby="6"
                                                data-parent="#accordion">
                                                <div class="card-body">
                                                    Puoi <a href="#URLPP">donare</a> anche solo <a href="#URLPP1">1€</a> e ci farai felici.
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                         <!-- q7 -->
                                        <div>
                                            <div id="7">
                                                <h6 class="mb-0">
                                                    <b data-toggle="collapse" data-target="#collapse7"
                                                        aria-expanded="true" aria-controls="collapse7">
                                                        Funziona con altri registri?
                                                    </b><i class="fas fa-chevron-down"></i>
                                                    </h5>
                                            </div>
                                            <div id="collapse7" class="collapse" aria-labelledby="7"
                                                data-parent="#accordion">
                                                <div class="card-body">
                                                   No, per ora...
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <!-- Q8 -->
                                        <div>
                                            <div id="8">
                                                <h6 class="mb-0">
                                                    <b data-toggle="collapse" data-target="#collapse8"
                                                        aria-expanded="true" aria-controls="collapse8">
                                                        Perché non posso giustificare?
                                                    </b><i class="fas fa-chevron-down"></i>
                                                    </h5>
                                            </div>
                                            <div id="collapse8" class="collapse" aria-labelledby="8"
                                                data-parent="#accordion">
                                                <div class="card-body">
                                                    ci stiamo lavorando.. (non è vero, ma fa figo scriverlo)
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--
                    <p><b>Cosè redignus?</b><br> Redignus è semplicemente una "<a href="#" data-toggle="tooltip"
                            data-placement="top"
                            title="Una skin, nella terminologia informatica e di Wikipedia, è l'aspetto grafico e di impaginazione con cui può apparire il sito web di Wikipedia.">skin</a>"
                        con notevoli migliorie.
                        <hr><b>Come posso aiutare?</b><br> Redignus è un sistema open-source disponibile per chiunque, basta
                        andare su <a href="https://github.com/REDignus">github.com</a>.
                        <hr><b>Perché alcuni dati sono errati?</b><br> Questo non è determinato da noi, ma dai professori e
                        la presidenza (o axios, la cosa non ci stupirebbe).
                        <hr><b>É sicuro?</b><br> Questo è da chiedere al gestore del registro (axios..cof..le
                        password..cof.. non in chiaro c: ).
                        <hr><b>Rubate le password o dati?</b><br> Assolutamente no, non li salviamo per nessuno scopo (non
                        ci interessano molto \_(ツ)_/ ).
                        <hr><b>Vuoi offrirci un caffé? (spoiler questa non è una domanda)</b><br> Puoi <a
                            href="#URLPP">donare</a> anche solo <a href="#URLPP1">1€</a> e ci farai felici.
                        <hr><b>Funziona con altri registri?</b><br> No, per ora...
                        <hr><b>Perché non posso giustificare?</b><br> ci stiamo lavorando.. (non è vero, ma fa figo
                        scriverlo)
                        <hr></p>-->
                <!-- Mi spiace Mario ma la principessa è in un altro castello -->
                <div class="jumbotron  text-center">
                    <h3>Problemi noti:</h3>
                    <br>
                    <p>
                        - Alcuni tasti della barra laterale non funzionano (Opzioni, Github, Contatti,
                        FAQ...)
                        <br>
                    </p>
                </div>
            </div>
            <?php include "include/footer.php"; ?>
        </div>
        <!-- Page Content -->
    </div>
    <!-- Chiusura Wrapper -->
</body>

</html>