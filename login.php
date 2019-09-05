<!doctype html>
<?php 
include("include/top.php");
$axios = new axios;

?>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Login | REDignus</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <!-- Custom styles for this template -->
    <link href="dist/css/login.css" rel="stylesheet">

    <?php
      $_POST["cfscuola"] = $_GET["cfscuola"];

      if ((!empty($_POST["cfscuola"])) && (!empty($_POST["axiosuser"])) && (!empty($_POST["axiospassword"]))) { //Controlla accesso normale

        $logindata = $axios->login($_POST["cfscuola"], $_POST["axiosuser"], $_POST["axiospassword"], $_POST["key"]);
        if ($logindata["error"] != true) {
          if ($_POST["remember-me"] == true) { //Salvare le credenziale suoi cookie
            setcookie("cfscuola", $_POST["cfscuola"], time() + 31536000);
            setcookie("axiosuser", $_POST["axiosuser"], time() + 31536000);
            setcookie("axiospassword", $_POST["axiospassword"], time() + 31536000);
          } else {
            setcookie("cfscuola", $_COOKIE["cfscuola"], time() + 31536000);
          }
          
          // foreach ($logindata as $key => $value) {
            //   setcookie($key, $value, time() + 7200);
            // }
            
            $_SESSION["expire"] = time() + 1800;
            $_SESSION["cookies"] = $logindata;
            $_SESSION["getPostREFamily"] = $axios->getPostREFamily();
            $_SESSION["getStudentId"] = $axios->getStudentId();
            $_SESSION["QuadrimestreFTAll"] = $axios->getPeriodYear();

            if (empty($_COOKIE['studentNumber'])){ //imposta lo studente predefinito se non impostato
              setcookie("studentNumber", 0, time() + 31536000);
            }
            if (empty($_COOKIE['QuadrimestreFT'])){ //imposta il periodo dell'anno predefinito se non impostato
              setcookie("QuadrimestreFT", $_SESSION["QuadrimestreFTAll"]["selected"], time() + 31536000);
            }

          if (!empty($_GET["page"])){
            header("location: ".$_GET["page"]);
          } else {
            header("location: /home");
          }

        }


      } elseif ((!empty($_COOKIE["cfscuola"])) && (!empty($_COOKIE["axiosuser"])) && (!empty($_COOKIE["axiospassword"]))) { //Controlla accesso automatico

        $logindata = $axios->login($_COOKIE["cfscuola"], $_COOKIE["axiosuser"], $_COOKIE["axiospassword"], $_COOKIE["key"]);
        if ($logindata["error"] != true) {
          //Salva le credenziali
          setcookie("cfscuola", $_COOKIE["cfscuola"], time() + 31536000);
          setcookie("axiosuser", $_COOKIE["axiosuser"], time() + 31536000);
          setcookie("axiospassword", $_COOKIE["axiospassword"], time() + 31536000);

          // foreach ($logindata as $key => $value) {
          //   setcookie($key, $value, time() + 7200);
          // }
          
          $_SESSION["expire"] = time() + 1800;
          $_SESSION["cookies"] = $logindata;
          $_SESSION["getPostREFamily"] = $axios->getPostREFamily();
          $_SESSION["getStudentId"] = $axios->getStudentId();
          $_SESSION["QuadrimestreFTAll"] = $axios->getPeriodYear();

          if (empty($_COOKIE['studentNumber'])){ //imposta lo studente predefinito se non impostato
            setcookie("studentNumber", 0, time() + 31536000);
          }
          if (empty($_COOKIE['QuadrimestreFT'])){ //imposta il periodo dell'anno predefinito se non impostato
              setcookie("QuadrimestreFT", $_SESSION["QuadrimestreFTAll"]["selected"], time() + 31536000);
          }

          if (!empty($_GET["page"])){
            header("location: ".$_GET["page"]);
          } else {
            header("location: /home");
          }
        }
      }
    ?>
  </head>
  <body class="text-center">
    <?php if (empty($_GET['cfscuola'])): ?>
    <?php
    if ((!empty($_COOKIE["cfscuola"])) && $_GET['changecf'] != true) {
      header("location: ?cfscuola=".$_COOKIE["cfscuola"]."&page=".$_GET["page"]);
    }
    ?>
    <form class="form-signin" method="get">
      <!-- <img class="mb-4" src="dist/img/Logo-REDignus-1-6464.png" alt="" width="74"> -->
      <h1 class="h3 mb-3 font-weight-normal">Accedi</h1>
      <h4 class="h5 mb-3 font-weight-normal">(Credenziali Axios)</h4>
      <br>
      
      <label for="cf" class="sr-only">Codice fiscale scuola</label>
      <input type="text" value="<?php echo $_POST["cfscuola"]; ?>" id="cf" class="form-control" name="cfscuola" placeholder="Codice fiscale scuola" required>
      <small><a target="_blank" href="http://axiositalia.it/accesso-registro-elettronico/">Clicca qui per trovare quello della tua scuola</a> altrimenti lo puoi cercare su google</small>
      <br>
      <br>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Accedi</button>
      <p class="mt-5 mb-3 text-muted">&copy; 2018</p>
    </form>
    <?php else: ?>
    <form class="form-signin" method="post">
      <!-- <img class="mb-4" src="dist/img/Logo-REDignus-1-6464.png" alt="" width="74"> -->
      <h1 class="h3 mb-3 font-weight-normal">Accedi</h1>
      <h4 class="h5 mb-3 font-weight-normal">(Credenziali Axios)</h4>
      <br>

      <label for="inputEmail">Codice fiscale scuola | <a href="?changecf=true">Cambialo...</a></label>
      <input type="number" value="<?php echo $_POST["cfscuola"]; ?>" id="inputEmail" class="form-control"disabled>
      <br>
      <label for="inputEmail" class="sr-only">Nome utente</label>
      <input type="text" value="<?php echo $_POST["axiosuser"]; ?>" id="inputEmail" class="form-control" name="axiosuser" placeholder="Nome utente" required>
      <label for="inputPassword" class="sr-only">Password</label>
      <input type="password" value="<?php echo $_POST["axiospassword"]; ?>" id="inputPassword" class="form-control" name="axiospassword" placeholder="Password" required>
      <div class="checkbox mb-3">
        <label>
          <input type="checkbox" name="remember-me" value="true"> Ricordami (NON SICURO)
        </label>
      </div>
      <?php
        if ($logindata["error"] == true) {
          if ($logindata["errorcode"] == 11 || $logindata["errorcode"] == 10) {
            echo '
              <div class="alert alert-danger" role="alert">
                '.$logindata["msg"].'
              </div>
              <label for="key" class="sr-only">Key di attivazione</label>
               <input type="text" id="key" class="form-control" name="key" placeholder="Key di attivazione" required><br>
            ';
          } else {
            echo '
              <div class="alert alert-danger" role="alert">
                '.$logindata["msg"].'
              </div>';
          }
        }
      ?>
      <button class="btn btn-lg btn-primary btn-block" type="submit">Accedi</button>
      <p class="mt-5 mb-3 text-muted">&copy; 2018</p>
    </form>
    <?php endif; ?>
    
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
  </body>
</html>
