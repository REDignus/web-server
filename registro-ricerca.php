<?php
    include "include/top.php";
    
    $axios = new axios;
    
    $axios->postREFamilyData = $_SESSION["getPostREFamily"];
    $axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
    $axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
    $axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
    $axios->cookies = $_SESSION["cookies"];
    
    $result = $axios->getHomeworkDateRange($_GET["startdate"], $_GET["enddate"]);
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Registro | REDignus</title>
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
                        <h1>Opzioni di ricerca</h1>
                        <br>
                        <form method="get">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="inputEmail4">Data inizio della ricerca</label>
                                    <input type="date" class="form-control" name="startdate" value="<?php echo $_GET["startdate"]; ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="inputPassword4">Data fine della ricerca</label>
                                    <input type="date" class="form-control" name="enddate" value="<?php echo $_GET["enddate"]; ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail4">Parola chiave (o più parole chiave)</label>
                                <input type="text" class="form-control" placeholder="Es. (ITAL, Illuminismo, ING...)" name="keyworld" value="<?php echo $_GET["keyworld"]; ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Cerca</button>
                            <input type="hidden" name="type" value="<?php echo $_GET["type"]; ?>">
                        </form>
                    </div>
                    <div class="jumbotron" <?php if (empty($_GET["startdate"]) || empty($_GET["enddate"])) {echo 'style="display: none;"';} ?>>
                        <?php if ($_GET["type"] == "mix" || empty($_GET["type"])): ?>
                        <table class="table table-bordered table-responsive-sm table-responsive-md">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Data</th>
                                    <th scope="col">Argomenti</th>
                                    <th scope="col">Compiti</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    foreach ($result as $key => $value) {
                                        if ((!empty($value["homework"][0]["text"])) || (!empty($value["arguments"][0]["text"]))) {
                                            //Data
                                            echo '<tr style="display: none;">
                                                                            <th>' . $value["info"]["date"] . '<br>' . $value["info"]["day"] . '</th>
                                                                        ';
                                    
                                            //Argomenti
                                            echo '<td>';
                                            if (!empty($value["arguments"][0]["text"])) {
                                                foreach ($value["arguments"] as $key => $argument) {
                                                    if ($key != 0) {
                                                        echo '<br>';
                                                    }
                                                    echo '<b>' . $argument["name"] . ':</b> ' . $argument["text"];
                                                }
                                            }
                                            echo '</td>';
                                    
                                            //Compiti
                                            echo '<td>';
                                            if (!empty($value["homework"][0]["text"])) {
                                                foreach ($value["homework"] as $key => $homework) {
                                                    if ($key != 0) {
                                                        echo '<br>';
                                                    }
                                                    echo '<b>' . $homework["name"] . ':</b> ' . $homework["text"];
                                                }
                                            }
                                            echo '</td>';
                                        }
                                    }
                                    ?>
                            </tbody>
                        </table>
                        <?php elseif ($_GET["type"] == "normal"): ?>
                        <form method="get">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Data</div>
                                </div>
                                <input type="hidden" name="type" value="<?php echo $_GET["type"]; ?>">
                                <input name="date" type="date" class="form-control" onchange="this.form.submit()">
                            </div>
                        </form>
                        <br>
                        <?php
                            foreach ($result as $key => $value) {
                                $date = explode('-', $_GET["date"]);
                                $date = $date[2] . "/" . $date[1] . "/" . $date[0];
                                if ($value["info"]["date"] == $date) {
                                    $print = true;
                                    echo '
                                                         <table class="table table-bordered table-responsive-sm table-responsive-md">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">Argomenti</th>
                                                                    <th scope="col">Compiti</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                        ';
                                    if ((!empty($value["homework"][0]["text"])) || (!empty($value["arguments"][0]["text"]))) {
                                        //Argomenti
                                        echo '<td>';
                                        if (!empty($value["arguments"][0]["text"])) {
                                            foreach ($value["arguments"] as $key => $argument) {
                                                if ($key != 0) {
                                                    echo '<br>';
                                                }
                                                echo '<b>' . $argument["name"] . ':</b> ' . $argument["text"];
                                            }
                                        }
                                        echo '</td>';
                            
                                        //Compiti
                                        echo '<td>';
                                        if (!empty($value["homework"][0]["text"])) {
                                            foreach ($value["homework"] as $key => $homework) {
                                                if ($key != 0) {
                                                    echo '<br>';
                                                }
                                                echo '<b>' . $homework["name"] . ':</b> ' . $homework["text"];
                                            }
                                        }
                                        echo '</td>';
                                    }
                                    echo '
                                                            </tbody>
                                                        </table>
                                                        ';
                                    break;
                                }
                            }
                            if (!$print) {
                                echo '
                                                    <h2 class="text-center">Non sono presenti compiti o argomenti in questo giorno.</h2>
                                                    ';
                            }
                            ?>
                        <?php endif;?>
                    </div>
                </div>
                <?php include "include/footer.php";?>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/mark.js/8.11.1/mark.min.js" integrity="sha256-IdYuEFP3WJ/mNlzM18Y20Xgav3h5pgXYzl8fW4GnuPo=" crossorigin="anonymous"></script>
                <script>
                    // Ottieni i parametri dell' url
                    var url = new URL(window.location.href);
                    var getUrlParameter = function getUrlParameter(sParam) {
                        var sPageURL = window.location.search.substring(1),
                            sURLVariables = sPageURL.split('&'),
                            sParameterName,
                            i;
                    
                        for (i = 0; i < sURLVariables.length; i++) {
                            sParameterName = sURLVariables[i].split('=');
                    
                            if (sParameterName[0] === sParam) {
                                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
                            }
                        }
                    };
                    
                    // Evidenzia i risultati 
                    var instance = new Mark(document.querySelector("*"));
                    instance.mark(url.searchParams.get("keyworld"), {
                        "className": "bg-warning"
                    });
                    
                    // Mostra solo le righe con i risultati
                    $("tbody tr:has(.bg-warning)").show();
                    
                    // Se non è inserita nessuna parola chiave mostra tutto
                    if (url.searchParams.get("keyworld") == ""){
                        $("tbody tr").show();
                    }
                    
                </script>
            </div>
            <!-- Page Content -->
        </div>
        <!-- Chiusura Wrapper -->
    </body>
</html>