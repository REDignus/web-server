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
                    <?php if ($_COOKIE['graficavoti'] == "1"): 
                        $result = $axios->getVote();
                    ?>
                    <br>
                    <div class="jumbotron">
                        <div class="text-center">
                            <a href="opzioni" class="text-right badge badge-pill badge-secondary">Prova la nuova versione (BETA)</a>
                            <a href="opzioni#QuadrimestreFT" class="text-right badge badge-pill badge-primary">Cambia periodo dell'anno</a>
                        </div>
                        <br>
                        <form method="get">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">Materia</div>
                                </div>
                                <select name="materia" class="form-control" onchange="this.form.submit()">
                                    <option value="tutte" selected>Tutte</option>
                                    <?php
                                        foreach ($result as $key => $value) {
                                            $materie[$value["topic"]] = 1;
                                        }
                                        foreach ($materie as $key => $value) {
                                            if ($_GET["materia"] == $key) {
                                                echo '<option selected value="' . $key . '">' . $key . '</option>';
                                            } else {
                                                echo '<option value="' . $key . '">' . $key . '</option>';
                                            }
                                        
                                        }
                                        ?>
                                </select>
                            </div>
                        </form>
                        <br>
                        <div class="row">
                            <div class="col-md">
                                <div class="table-responsive">
                                    <table id="ignore" class="table table-bordered">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th scope="col">Data</th>
                                                <th>Materia</th>
                                                <th>Tipo</th>
                                                <th>Voto</th>
                                                <th>Commento</th>
                                                <th>Insegnante</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i["numerovotitotali"] = 0;
                                                $totale = 0;
                                                foreach ($result as $key => $value) {
                                                    if ($_GET["materia"] == $value["topic"] || empty($_GET["materia"]) || $_GET["materia"] == "tutte") {
                                                        $value["vote"] = str_replace(",", ".", $value["vote"]);
                                                        $color = "alert-primary";
                                                        if (is_numeric($value["vote"][0])) {
                                                            if (strpos($value["vote"], '+') !== false) {
                                                                $value["vote"] = str_replace("+", "", $value["vote"]);
                                                                $value["vote"] = str_replace("++", "", $value["vote"]);
                                                                $value["vote"] += 0.25;
                                                            } elseif (strpos($value["vote"], '-') !== false) {
                                                                $value["vote"] = str_replace("-", "", $value["vote"]);
                                                                $value["vote"] = str_replace("--", "", $value["vote"]);
                                                                $value["vote"] -= 0.25;
                                                            }
                                                            if ($value["vote"] >= 6) {
                                                                $color = "alert-success";
                                                            } else {
                                                                $color = "alert-danger";
                                                            }
                                                        }
                                                
                                                        echo '<tr><th scope="row">' . $value["date"] . '</th>';
                                                        echo '<td>' . $value["topic"] . '</td>';
                                                        echo '<td>' . $value["type"] . '</td>';
                                                        echo '<td class="' . $color . '">' . $value["vote"] . '</td>';
                                                        echo '<td>' . $value["comment"] . '</td>';
                                                        echo '<td>' . $value["teacher"] . '</td></tr>';
                                                
                                                        if (is_numeric($value["vote"])) {
                                                            //Media
                                                            $totale += $value["vote"];
                                                            $i["numerovotitotali"] += 1;
                                                            //Ordinare i voti per materia
                                                            if (empty($i[$value["topic"]])) {
                                                                $i[$value["topic"]] = 0;
                                                            }
                                                
                                                            $voti_x_materia[$value["topic"]][$i[$value["topic"]]] = $value;
                                                            $i[$value["topic"]] += 1;
                                                        }
                                                    }
                                                }
                                                $media = $totale / $i["numerovotitotali"];
                                                ?>
                                        </tbody>
                                        <tfoot class="thead-dark">
                                            <tr>
                                                <th class="text-center" colspan="3">Media</th>
                                                <th colspan="5"><?php echo round($media, 2); ?></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md">
                                <canvas id="myChart" width="400" height="400"></canvas>
                            </div>
                        </div>
                        <script src="https://momentjs.com/downloads/moment.js"></script>
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
                        <script>
                            //Converire da hex ad RGBA
                            function hexToRgbA(hex){
                                var c;
                                if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
                                    c= hex.substring(1).split('');
                                    if(c.length== 3){
                                        c= [c[0], c[0], c[1], c[1], c[2], c[2]];
                                    }
                                    c= '0x'+c.join('');
                                    return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+',0.2)';
                                }
                                throw new Error('Bad Hex');
                            }
                            
                            var ctx = document.getElementById("myChart").getContext('2d');
                            var myChart = new Chart(ctx, {
                                labels: [ // Date Objects
                                  <?php
                                foreach ($result as $key => $value) {
                                    $data = explode("/", $value["date"]);
                                    echo "new Date('.$data[2].', '.$data[1].', '.$data[0].'),";
                                }
                                ?>
                                ],
                                type: 'line',
                                data: {
                                    datasets: [
                                      {
                                        backgroundColor: '#ff0000',
                                        borderColor: '#ff0000',
                                        fill: false,
                                        label: "MEDIA",
                                        data: [
                                          {
                                            y: <?php echo $media; ?>,
                                            x: <?php
                                $data = explode("/", $result[0]["date"]);
                                echo 'new Date(' . $data[2] . ', ' . $data[1] . ', ' . $data[0] . ')';
                                ?>,
                                          },
                                          {
                                            y: <?php echo $media; ?>,
                                            x: <?php
                                $data = explode("/", end($result)["date"]);
                                echo 'new Date(' . $data[2] . ', ' . $data[1] . ', ' . $data[0] . ')';
                                ?>,
                                          },
                                        ],
                                      },
                                      <?php
                                foreach ($voti_x_materia as $key => $value) {
                                    $color = substr(md5($key), 0, 6);
                                    echo '
                                {
                                 backgroundColor: "#' . $color . '",
                                 borderColor: hexToRgbA("#' . $color . '"),
                                 fill: false,
                                 label: "' . $key . '",
                                 data: [';
                                    foreach ($value as $voto) {
                                        $data = explode("/", $voto["date"]);
                                        echo '
                                    {
                                      x: new Date(' . $data[2] . ', ' . $data[1] . ', ' . $data[0] . '),
                                      y: ' . $voto["vote"] . ',
                                    },';
                                    }
                                    echo ']
                                },
                                ';
                                }
                                ?>
                                    ]
                                },
                                options: {
                                  tooltips: {
                                    mode: 'nearest',
                                  },
                                  scales: {
                                    xAxes: [{
                                      type: 'time',
                                      time: {
                                        format: "DD/MM/YYYY",
                                        round: 'day',
                                        tooltipFormat: 'DD/MM/YYYY'
                                      },
                                      scaleLabel: {
                                        display: true,
                                        labelString: 'Date'
                                      }
                                    }],
                                    yAxes: [{
                                      scaleLabel: {
                                        display: true,
                                        labelString: 'value'
                                      },
                                      ticks: {
                                        suggestedMin: 0,
                                        suggestedMax: 10,
                                      }
                                    }]
                                  },
                                }
                            });
                        </script>
                    </div>
                </div>
                <?php include "include/footer.php"; ?>    
                <?php else: ?>
                <div class="container-fluid">
                    <br>
                    <div class="jumbotron">
                        
                        <div class="text-center">
                            <a href="opzioni#QuadrimestreFT" class="text-right badge badge-pill badge-primary">Cambia periodo dell'anno</a>
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
                    <h5>Legenda</h5>
                        <p>- Se si clicca su un voto si andranno a vedere i dettagli di esso.<br>
                        - Se si clicca su una materia nella sezione "Media" si vedranno i voti di essa.<br>
                        - Se presente un "<span style="color: red;">*</span>" vorrà dire che sono disponibili maggiori dettagli.</p>
                </div>
                <?php include "include/footer.php"; ?>    
                <script>
                    function voti(materia) {
                        if (materia == null) {
                            materia = "";
                        }
                        // Ajax Stringa voti
                        $('#votilista').html('<div class="text-center"><br><h2>Caricamento</h2><i class="fas fa-circle-notch fa-spin fa-4x"></i></div>');
                        $("#votimenu").addClass("active");
                        $("#mediamenu").removeClass("active");
                        $.getJSON('api/ajax/voti?materia='+materia, function(data) {
                            // Per ogni voto
                            var numcollaps = 0;
                            $('#votilista').html('<br>');
                            data.forEach(element => {
                                numcollaps++;
                                // Imposta il colore del voto
                                if (null === element.realVote ) {
                                    var colorevoto = "bg-primary";
                                } else if (element.realVote.replace(',', '.') == 10 && (null == $.cookie("votifunmode") || $.parseJSON($.cookie("votifunmode")))) {
                                    console.log(null == $.cookie("votifunmode") || $.parseJSON($.cookie("votifunmode")));
                                    
                                    var colorevoto = "bg-voto10";
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
                                                    '<p><small class="text-body">Voto: '+element.realVote+' <br> '+element.teacher+' </small></p>'+
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

                        var numcollaps = 0;
                        var numtot = 0;
                        var tot = 0;
                        $.getJSON('api/ajax/mediaMateria', function(data) {
                            // Per ogni voto
                            $('#votilista').html('<br>');
                            data.forEach(element => {
                                numcollaps++;
                                numtot++;

                                // Imposta il colore del voto
                                if ("" === element.average) {
                                    var colorevoto = "bg-primary";
                                    element.average = "N/A"
                                    numtot--;
                                } else if (element.average.replace(',', '.') == 10 && (null == $.cookie("votifunmode") || $.parseJSON($.cookie("votifunmode")))) {
                                    var colorevoto = "bg-voto10";
                                    tot += parseFloat(element.average.replace(',', '.'));
                                } else if (element.average.replace(',', '.') >= 6) {
                                    var colorevoto = "bg-success";
                                    tot += parseFloat(element.average.replace(',', '.'));
                                } else {
                                    var colorevoto = "bg-danger"; 
                                    tot += parseFloat(element.average.replace(',', '.'));
                                }

                                // Salva le informazioni precedenti
                                var precedente = $('#votilista').html();

                                // Stampa il voto più le informazioni precedenti
                                $('#votilista').html(precedente + '<div class="row" onclick="voti(\''+element.name+'\');" data-toggle="collapse" data-target="#collapse'+numcollaps+'" aria-expanded="true" aria-controls="collapse'+numcollaps+'">'+
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
                            var media = Math.round(tot/numtot * 100) / 100; //Calcolo media
                            if ("" === media || isNaN(media) || null === media) {
                                var colorevoto = "bg-primary";
                                media = "N/A";
                            } else if (media >= 6) {
                                var colorevoto = "bg-success";
                            } else {
                                var colorevoto = "bg-danger"; 
                            }
                            
                            console.log("tot: " + tot + " numcollaps: "+numcollaps+" media:"+media);
                            
                            numcollaps++;

                            // Salva le informazioni precedenti
                            var precedente = $('#votilista').html();

                            // Stampa il voto più le informazioni precedenti
                            $('#votilista').html(precedente + '<div class="row" onclick="voti();" data-toggle="collapse" data-target="#collapse'+numcollaps+'" aria-expanded="true" aria-controls="collapse'+numcollaps+'">'+
                                '<div class="col-8 col-lg-10">'+
                                    '<div class="row" style="height: 100%;" >'+
                                        '<div class="col text-center" style="height: 100%; display: flex; justify-content: center; align-items: center;">'+
                                            '<h4><u> MEDIA GENERALE </u></h4>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                                '<div class="col-4 col-lg-2">'+
                                    '<div class="p-2 '+colorevoto+' text-white" style="height: 100%; display: flex; justify-content: center; align-items: center;">'+
                                        '<div class="text-center">'+
                                        '<h5> '+media+' </h5>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>'+
                            '</div>'+
                            '<hr style="border-top: 1px solid rgb(0, 0, 0, 0.5)">');
                        });
                       
                    }
                </script>
                </script>
                <?php endif; ?>
            </div><!-- Page Content -->        
        </div><!-- Chiusura Wrapper -->
    </body>
</html>