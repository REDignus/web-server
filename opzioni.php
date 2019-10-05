<?php
include "include/top.php";

// $axios = new axios;

// $axios->postREFamilyData = $_SESSION["getPostREFamily"];
// $axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
// $axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
// $axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
// $axios->cookies = $_SESSION["cookies"];

// $result = $axios->getAbsences();
?>
<!DOCTYPE html>
<html>
    <head>
        <!-- Test code notifiche -->
        <link rel="manifest" href="/manifest.json" />
        <title>Opzioni | REDignus</title>
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
                <div class="container">
                    <br>
                    <h1 class="text-center">Opzioni</h1>
                    <br>
                    <div class="jumbotron">
                        <h2>Grafica</h2>
                        <p>Qui puoi cambiare tutte le opzioni relative all' aspetto grafico dell' interfaccia.</p>
                        <p>*<small>Se viene selezionata l'opzione Default allora verrà sempre visualizzata la versione più recente</small></p>
                        <hr>
                        <div class="row">
                            <div class="col-md-10 col-sm-8">
                                <p class="mx-auto">Tasto menù</p>
                            </div>
                            <div class="col-md-2 col-sm-4">
                                <span class="switch">
                                <input type="checkbox" name="tastomenu" class="switch" id="Tastomenu">
                                <label for="Tastomenu"></label>
                                </span>
                            </div>
                            <div class="col-md-9 col-sm-8">
                                <p class="mx-auto">Grafica registro </p>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <select name="grafica-registro" id="grafica-registro">
                                    <option value="default">Default (Scheda [v3.0])</option>
                                    <option value="3">Scheda [v3.0]</option>
                                    <option value="2">Scheda [v2.1]</option>
                                    <option value="1">Tabella [v1.0]</option>
                                </select>
                            </div>
                            <div class="col-md-9 col-sm-8">
                                <p class="mx-auto">Grafica voti </p>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <select name="grafica-voti" id="grafica-voti">
                                    <option value="default">Default (Scheda [v2.2])</option>
                                    <option value="2">Scheda [v2.2]</option>
                                    <option value="1">Tabella [v1.3]</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row justify-content-end">
                            <div class="col text-center">
                                <button type="button" class="btn btn-primary" onclick="saveGrafica();">Salva</button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-danger" onclick="resetGrafica();">Cancella</button>
                            </div>
                        </div>
                    </div>
                    <div class="jumbotron">
                        <h2>Varie</h2>
                        <p>Qui puoi cambiare tutte le opzioni.</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-9 col-sm-8">
                                <p class="mx-auto">Alunno </p>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <select name="studentNumber" id="studentNumber">
                                    <?php
                                    foreach ($_SESSION['getStudentId'] as $key => $value) {
                                        echo '<option value="' . $value['num'] . '">' . $value["name"] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-9 col-sm-8">
                                <p class="mx-auto">Periodo dell'anno </p>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <select name="studentNumber" id="QuadrimestreFT">
                                    <?php
                                    foreach ($_SESSION['QuadrimestreFTAll'] as $key => $value) {
                                        echo '<option value="' . $value . '">' . $value . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <div class="row justify-content-end">
                            <div class="col text-center">
                                <button type="button" class="btn btn-primary" onclick="saveVarie();">Salva</button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-danger" onclick="resetVarie();">Cancella</button>
                            </div>
                        </div>
                    </div>
                    <div class="jumbotron">
                        <h2>Info</h2>
                        <p>Qui puoi vedere i dettagli del tuo account e del tuo dispositivo.</p>
                        <hr>
                        <div class="row">
                            <div class="col-md-9 col-sm-8">
                                <p class="mx-auto">Alunno: </p>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <p id="alunnodebug"></p>
                            </div>
                            <div class="col-md-9 col-sm-8">
                                <p class="mx-auto">Password: </p>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <p class="btn btn-primary" id="passowrddebug" onclick="showPass();">Clicca<br> per mostrare</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include "include/footer.php";?>
                <script>
                    if (typeof $.cookie('tastomenu') === 'undefined'){
                        $.cookie("tastomenu", true, { expires: 365 })
                    }
                    if (typeof $.cookie('graficaregistro') === 'undefined'){
                        $.cookie("graficaregistro", "default", { expires: 365 })
                    }
                    if (typeof $.cookie('graficavoti') === 'undefined'){
                        $.cookie("graficavoti", "default", { expires: 365 })
                    }
                    if (typeof $.cookie('studentNumber') === 'undefined'){
                        $.cookie("studentNumber", "0", { expires: 365 })
                    }
                    
                    
                    
                    // ---- Opzioni Grafica ----
                    function resetGrafica() {
                        $('#Tastomenu').prop('checked', $.parseJSON($.cookie("tastomenu")));
                        $("#grafica-registro").val($.cookie("graficaregistro"));
                        $("#grafica-voti").val($.cookie("graficavoti"));
                    }
                    resetGrafica();
                    
                    
                    function saveGrafica() {
                        $.cookie("tastomenu", $('#Tastomenu').is(':checked'), { expires: 365 })
                        $.cookie("graficaregistro", $('#grafica-registro').find(":selected").val(), { expires: 365 })
                        $.cookie("graficavoti", $('#grafica-voti').find(":selected").val(), { expires: 365 })
                        alert("Ottimo! \nLe modifiche sono state salvate...");
                    }
                    // ---- Opzioni Varie ----
                    function resetVarie() {                        
                        $("#studentNumber").val($.cookie("studentNumber"));
                        $("#QuadrimestreFT").val($.cookie("QuadrimestreFT"));
                    }
                    resetVarie();
                    
                    
                    function saveVarie() {
                        $.cookie("studentNumber", $('#studentNumber').find(":selected").val(), { expires: 365 })
                        $.cookie("QuadrimestreFT", $('#QuadrimestreFT').find(":selected").val(), { expires: 7 })
								window.location.href = "login";
                        alert("Ottimo! \nLe modifiche sono state salvate...\nAspetta un attimo");
                        
                    }


                    // ---- Debug Info ----
                    function showPass() {
                        if ($.cookie("axiospassword") === undefined) {
                            $("#passowrddebug").text("Non è presente nessuna password");
                            $("#passowrddebug").removeClass();
                        } else {
                            $("#passowrddebug").text($.cookie("axiospassword"));
                            $("#passowrddebug").removeClass();
                        }
                    }

                    function infoDebug() {
                        if ($.cookie("axiosuser") === undefined) {
                            $("#alunnodebug").text("Non è presente nessun username");
                        } else {
                            $("#alunnodebug").text($.cookie("axiosuser"));
                        }
                    }
                    infoDebug();
                </script>
            </div><!-- Page Content -->
        </div><!-- Chiusura Wrapper -->
    </body>
</html>