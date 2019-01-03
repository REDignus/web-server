<!doctype html>
<?php 

// server should keep session data for AT LEAST 1 hour
ini_set('session.gc_maxlifetime', 7200);

// each client should remember their session id for EXACTLY 1 hour
session_set_cookie_params(7200);

session_start(); // ready to go!

//Controllo login
if (strpos($_SERVER['PHP_SELF'], 'login.php') === false) {
    if (empty($_SESSION["cookies"]["__AntiXsrfToken"]) || empty($_SESSION["cookies"]["ASP.NET_SessionId"]) || $_SESSION["expire"] < time()){
        header("location: /login.php?page=".$_SERVER['PHP_SELF']);
        exit;
    }
}

include "../vendor/autoload.php";
include "../api/class.php";

use DiDom\Document;
use DiDom\Query;
use DiDom\Element;
  
  $axios = new axios;
  
  $axios->postREFamilyData = $_SESSION["getPostREFamily"];
  $axios->students = $_SESSION["getStudentId"];
  $axios->cookies = $_SESSION["cookies"];
  
  $result = $axios->getVote();
  ?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login</title>
    <!-- Bootstrap core CSS -->
    <!-- <link href="vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet"> -->
  </head>
  <body>
          <?php
            $i["numerovotitotali"] = 0;
            $totale = 0;
            foreach ($result as $key => $value) {
              if ($_GET["materia"] == $value["topic"] || empty($_GET["materia"]) || $_GET["materia"] == "tutte"){
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
              
                if (is_numeric($value["vote"])){
                  //Media
                  $totale += $value["vote"];
                  $i["numerovotitotali"] += 1;
                  //Ordinare i voti per materia
                  if (empty($i[$value["topic"]]))
                    $i[$value["topic"]] = 0;
                  $voti_x_materia[$value["topic"]][$i[$value["topic"]]] = $value;
                  $i[$value["topic"]] += 1;
                }
              }
            }
            $media = $totale / $i["numerovotitotali"];
            ?>          
      <canvas id="myChart" width="400" height="400"></canvas>
      <script src="https://momentjs.com/downloads/moment.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
      <script>
        function hexToRgbA(hex){
            var c;
            if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
                c= hex.substring(1).split('');
                if(c.length== 3){
                    c= [c[0], c[0], c[1], c[1], c[2], c[2]];
                }
                c= '0x'+c.join('');
                return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+',0.3)';
            }
            throw new Error('Bad Hex');
        }

        var ctx = document.getElementById("myChart").getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [
                  {
                    label: "MEDIA",
                    backgroundColor: '#ff0000',
                    borderColor: '#ff0000',
                    fill: false,
                    data: [
                      {
                        y: <?php echo $media; ?>,
                        x: <?php
          $data = explode("/", $result[0]["date"]);
          echo 'new Date('.$data[2].', '.$data[1].', '.$data[0].')'; 
          ?>,
                      },
                      {
                        y: <?php echo $media; ?>,
                        x: <?php
          $data = explode("/", end($result)["date"]);
          echo 'new Date('.$data[2].', '.$data[1].', '.$data[0].')'; 
          ?>,
                      },
                    ],
                  },
                  <?php
          foreach ($voti_x_materia as $key => $value) {
            $color = substr(md5($key), 0, 6);
           echo '
           {
             label: "'.$key.'",
             backgroundColor: "#'.$color.'",
             borderColor: hexToRgbA("#'.$color.'"),
             fill: false,
             data: [';
              foreach ($value as $voto) {
                $data = explode("/", $voto["date"]);
                echo '
                {
                  x: new Date('.$data[2].', '.$data[1].', '.$data[0].'),
                  y: '.$voto["vote"].',
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
                    labelString: 'Data'
                  }
                }],
                yAxes: [{
                  scaleLabel: {
                    display: true,
                    labelString: 'Voto'
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
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>