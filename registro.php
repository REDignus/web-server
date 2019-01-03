<?php
    include "include/top.php";
    
    $axios = new axios;
    
    $axios->postREFamilyData = $_SESSION["getPostREFamily"];
    $axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
    $axios->cookies = $_SESSION["cookies"];
    
    $result = $axios->getHomework($_GET["date"]);
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Registro | REDignus</title>
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
                <div class="container-fluid">
                    <br>
                    <div class="jumbotron">
                        <div class="text-center">
                            <a href="registro-ricerca" class="text-center badge badge-pill badge-primary">Usa le opzioni di ricerca</a>&nbsp;&nbsp;&nbsp;
                            <a href="opzioni" class="text-right badge badge-pill badge-secondary">Torna alla vecchia versione</a>
                        </div>
                        <br>
                        <!-- <hr> -->
                        <?php if ($_COOKIE["graficaregistro"] == "scheda" || empty($_COOKIE["graficaregistro"])): ?>

                        <?php
                            if (empty($_GET["date"])) {
                                $_GET["date"] = date("Y-m-d");
                            }
                            $dateexplode = explode("-", $_GET["date"]);
                            $date = $dateexplode[2]."/".$dateexplode[1]."/".$dateexplode[0];
                            $key = array_search($date, array_column(array_column($result, "info"), "date"));
                            // var_dump($key);
                            if ($key === false)
                                $key = 999;
                        ?>
                        <form method="get">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Data</div>
                                </div>
                                <input name="date" type="date" value="<?php echo $_GET["date"]; ?>" class="form-control" onchange="this.form.submit()">
                            </div>
                        </form>
                        <br>
                        <div class="row">
                            <div class="col-xl-3 col-lg-4 col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                    <?php if (!empty($result[$key]["info"]["date"])) : ?>
                                        <h3 class="card-title text-center"><?php echo($result[$key]["info"]["date"]); ?></h3>
                                        <h5 class="card-title text-center mb-0"><?php echo($result[$key]["info"]["day"]); ?></h5>
                                    <?php else: ?>
                                        <h3 class="card-title text-center mb-0">Il giorno non è disponibile</h3>
                                    <?php endif; ?>
                                    </div>
                                </div>
                                <br>
                                <?php if (!empty($result[$key]["arguments"][0]["name"])) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Argomenti</h3>
                                        <?php
                                            foreach ($result[$key]["arguments"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["name"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <?php endif; ?>
                                <?php if (!empty($result[$key]["homework"][0]["name"])) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Compiti</h3>
                                        <?php
                                            foreach ($result[$key]["homework"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["name"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <?php endif; ?>
                                <?php if ((!empty($result[$key]["profnotes"][0]["prof"])) || (!empty($result[$key]["disciplinary"][0]["prof"]))) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Note Dirigente & Note Disciplinari</h3>
                                        <?php
                                            foreach ($result[$key]["profnotes"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["prof"].":</b> ".$value["text"];
                                            }
                                            foreach ($result[$key]["disciplinary"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["prof"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-xl-3 col-lg-4 col-md-6 d-none d-xl-block d-lg-block d-md-block">
                                <?php $key++; ?>
                                <div class="card">
                                    <div class="card-body">
                                    <?php if (!empty($result[$key]["info"]["date"])) : ?>
                                        <h3 class="card-title text-center"><?php echo($result[$key]["info"]["date"]); ?></h3>
                                        <h5 class="card-title text-center mb-0"><?php echo($result[$key]["info"]["day"]); ?></h5>
                                    <?php else: ?>
                                        <h3 class="card-title text-center mb-0">Il giorno non è disponibile</h3>
                                    <?php endif; ?>
                                    </div>
                                </div>
                                <br>
                                <?php if (!empty($result[$key]["arguments"][0]["name"])) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Argomenti</h3>
                                        <?php
                                            foreach ($result[$key]["arguments"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["name"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <?php endif; ?>
                                <?php if (!empty($result[$key]["homework"][0]["name"])) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Compiti</h3>
                                        <?php
                                            foreach ($result[$key]["homework"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["name"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <?php endif; ?>
                                <?php if ((!empty($result[$key]["profnotes"][0]["prof"])) || (!empty($result[$key]["disciplinary"][0]["prof"]))) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Note Dirigente & Note Disciplinari</h3>
                                        <?php
                                            foreach ($result[$key]["profnotes"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["prof"].":</b> ".$value["text"];
                                            }
                                            foreach ($result[$key]["disciplinary"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["prof"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-xl-3 col-lg-4 d-none d-xl-block d-lg-block">
                                <?php $key++; ?>
                                <div class="card">
                                    <div class="card-body">
                                    <?php if (!empty($result[$key]["info"]["date"])) : ?>
                                        <h3 class="card-title text-center"><?php echo($result[$key]["info"]["date"]); ?></h3>
                                        <h5 class="card-title text-center mb-0"><?php echo($result[$key]["info"]["day"]); ?></h5>
                                    <?php else: ?>
                                        <h3 class="card-title text-center mb-0">Il giorno non è disponibile</h3>
                                    <?php endif; ?>
                                    </div>
                                </div>
                                <br>
                                <?php if (!empty($result[$key]["arguments"][0]["name"])) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Argomenti</h3>
                                        <?php
                                            foreach ($result[$key]["arguments"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["name"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <?php endif; ?>
                                <?php if (!empty($result[$key]["homework"][0]["name"])) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Compiti</h3>
                                        <?php
                                            foreach ($result[$key]["homework"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["name"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <?php endif; ?>
                                <?php if ((!empty($result[$key]["profnotes"][0]["prof"])) || (!empty($result[$key]["disciplinary"][0]["prof"]))) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Note Dirigente & Note Disciplinari</h3>
                                        <?php
                                            foreach ($result[$key]["profnotes"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["prof"].":</b> ".$value["text"];
                                            }
                                            foreach ($result[$key]["disciplinary"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["prof"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-xl-3 d-none d-xl-block">
                                <?php $key++; ?>
                                <div class="card">
                                    <div class="card-body">
                                    <?php if (!empty($result[$key]["info"]["date"])) : ?>
                                        <h3 class="card-title text-center"><?php echo($result[$key]["info"]["date"]); ?></h3>
                                        <h5 class="card-title text-center mb-0"><?php echo($result[$key]["info"]["day"]); ?></h5>
                                    <?php else: ?>
                                        <h3 class="card-title text-center mb-0">Il giorno non è disponibile</h3>
                                    <?php endif; ?>
                                    </div>
                                </div>
                                <br>
                                <?php if (!empty($result[$key]["arguments"][0]["name"])) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Argomenti</h3>
                                        <?php
                                            foreach ($result[$key]["arguments"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["name"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <?php endif; ?>
                                <?php if (!empty($result[$key]["homework"][0]["name"])) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Compiti</h3>
                                        <?php
                                            foreach ($result[$key]["homework"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["name"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <br>
                                <?php endif; ?>
                                <?php if ((!empty($result[$key]["profnotes"][0]["prof"])) || (!empty($result[$key]["disciplinary"][0]["prof"]))) : ?>
                                <div class="card">
                                    <div class="card-body">
                                    <h3 class="card-title text-center">Note Dirigente & Note Disciplinari</h3>
                                        <?php
                                            foreach ($result[$key]["profnotes"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["prof"].":</b> ".$value["text"];
                                            }
                                            foreach ($result[$key]["disciplinary"] as $value) {
                                                echo "<hr>";
                                                echo "<b>".$value["prof"].":</b> ".$value["text"];
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php elseif ($_COOKIE["graficaregistro"] == "tabella"): ?>
                        <form method="get">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Data</div>
                                </div>
                                <input name="date" type="date" value="<?php echo $_GET["date"]; ?>" class="form-control" onchange="this.form.submit()">
                            </div>
                        </form>
                        <br>
                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                                                echo '<tr>
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
                </div>
                <?php include "include/footer.php"; ?>
            </div><!-- Page Content -->          
        </div><!-- Chiusura Wrapper -->
    </body>
</html>