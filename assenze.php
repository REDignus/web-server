<?php
    include "include/top.php";
    
    $axios = new axios;
    
    $axios->postREFamilyData = $_SESSION["getPostREFamily"];
    $axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
    $axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
    $axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
    $axios->cookies = $_SESSION["cookies"];
    
    $result = $axios->getAbsences();
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Assenze | REDignus</title>
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
                        <?php if ($result["empty"] == true): ?>
                        <h2 class="text-center">Complimenti neanche un' assenza, sei un mito!</h2>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
																				<th scope="col">N</th>
                                        <th scope="col">Data</th>
                                        <th scope="col">Tipo</th>
                                        <th scope="col">Giustificata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($result as $key => $value) {
                                            echo '<tr>
                                                                            <th>' . ($key+1) . '</th>
																																						<td>' . $value["date"] . '</td>
                                                                            <td>' . $value["type"] . '</td>
                                                                            <td>' . $value["justified"] . '</td>
                                                                        </tr>';
                                        }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                        <hr>
                        <br>
                        <div class="text-center">
                            <h3>Ore di assenza totali:</h3>
                            <div id="oreassenza">
                                <button type="button" class="btn btn-primary" onclick="calcolaAssenza()">Clicca qui per calcolarle...</button>
                            </div>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
                <?php include "include/footer.php"; ?> 
                <script>
                    function calcolaAssenza() {
                        $('#oreassenza').html("<h3><i class='fas fa-circle-notch fa-spin'></i></h3>")
                        $.get('api/ajax/oreAssenza', function(data) {
                            $('#oreassenza').html("<h3>"+data+" Ore</h3>")
                        })
                    }
                </script>
            </div><!-- Page Content -->           
        </div><!-- Chiusura Wrapper -->
    </body>
</html>