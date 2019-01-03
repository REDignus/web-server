<?php
include "include/top.php";

// $axios = new axios;

// $axios->postREFamilyData = $_SESSION["getPostREFamily"];
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
                                    <option value="scheda">Scheda (BETA)</option>
                                    <option value="tabella">Tabella</option>
                                </select>
                            </div>
                            <div class="col-md-9 col-sm-8">
                                <p class="mx-auto">Grafica voti </p>
                            </div>
                            <div class="col-md-3 col-sm-4">
                                <select name="grafica-voti" id="grafica-voti">
                                    <option value="tabella">Tabella</option>
                                    <option value="scheda">Scheda (BETA)</option>
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
                        </div>
                        <hr>
                        <div class="row justify-content-end">
                            <div class="col text-center">
                                <button type="button" class="btn btn-primary" onclick="saveVarie();">Salva</button>&nbsp;&nbsp;
                                <button type="button" class="btn btn-danger" onclick="resetVarie();">Cancella</button>
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
                        $.cookie("graficaregistro", "scheda", { expires: 365 })
                    }
                    if (typeof $.cookie('graficavoti') === 'undefined'){
                        $.cookie("graficavoti", "tabella", { expires: 365 })
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
                    }
                    resetVarie();
                    
                    
                    function saveVarie() {
                        $.cookie("studentNumber", $('#studentNumber').find(":selected").val(), { expires: 365 })
                        alert("Ottimo! \nLe modifiche sono state salvate...");
                    }
                </script>
            </div><!-- Page Content -->
        </div><!-- Chiusura Wrapper -->
    </body>
</html>