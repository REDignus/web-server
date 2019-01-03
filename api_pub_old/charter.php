<?php
require "../vendor/autoload.php";
require "../api/class.php";

use DiDom\Query;
use DiDom\Document;
use DiDom\Element;
use Screen\Capture;
use Screen\Exceptions;

if (empty($_GET["htmlcharter"]) || $_GET["htmlcharter"] == false):

$axios = new axios;

if (!empty($_GET)) {
    $axios->cookies = [
        'ASP.NET_SessionId' => $_GET["ASP_NET_SessionId"],
        '__AntiXsrfToken' => $_GET['__AntiXsrfToken'],
    ];
    $axios->students[0] = [
        'num' => $_GET["num"],
        'qualcosa' => $_GET["qualcosa"],
        'id' => $_GET['id'],
    ];
    $axios->getPostREFamily(); //Ottieni gli input messi a caso da axios
    $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&htmlcharter=true";
} elseif (!empty($_POST)) {
    $axios->cookies = [
        'ASP.NET_SessionId' => $_POST["ASP_NET_SessionId"],
        '__AntiXsrfToken' => $_POST['__AntiXsrfToken'],
    ];
    $axios->students[0] = [
        'num' => $_POST["num"],
        'qualcosa' => $_POST["qualcosa"],
        'id' => $_POST['id'],
    ];
    $axios->getPostREFamily(); //Ottieni gli input messi a caso da axios
}

$screenCapture = new Capture();
$screenCapture->setUrl($url);
$screenCapture->setBackgroundColor('#ffffff');
$screenCapture->setImageType('png');

$screenCapture->save("/temp/charter");

echo base64_encode(file_get_contents("/temp/charter.png"));

?>

<?php elseif ($_GET["htmlcharter"]): ?>

<!doctype html>
<?php 
$axios = new axios;
if (!empty($_GET)) {
    $axios->cookies = [
        'ASP.NET_SessionId' => $_GET["ASP_NET_SessionId"],
        '__AntiXsrfToken' => $_GET['__AntiXsrfToken'],
    ];
    $axios->students[0] = [
        'num' => $_GET["num"],
        'qualcosa' => $_GET["qualcosa"],
        'id' => $_GET['id'],
    ];
    $axios->getPostREFamily(); //Ottieni gli input messi a caso da axios
    $output = $axios->getVote();
} elseif (!empty($_POST)) {
    $axios->cookies = [
        'ASP.NET_SessionId' => $_POST["ASP_NET_SessionId"],
        '__AntiXsrfToken' => $_POST['__AntiXsrfToken'],
    ];
    $axios->students[0] = [
        'num' => $_POST["num"],
        'qualcosa' => $_POST["qualcosa"],
        'id' => $_POST['id'],
    ];
    $axios->getPostREFamily(); //Ottieni gli input messi a caso da axios
    $output = $axios->getVote();
}  
  $result = $axios->getVote();
  ?>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
  </body>
</html>
<?php endif; ?>