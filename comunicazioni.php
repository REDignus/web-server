<?php
    include "include/top.php";
    
    $axios = new axios;
    
    $axios->postREFamilyData = $_SESSION["getPostREFamily"];
    $axios->QuadrimestreFT = $_COOKIE["QuadrimestreFT"];
    $axios->QuadrimestreFTAll = $_SESSION["QuadrimestreFTAll"];
    $axios->student = $_SESSION["getStudentId"][$_COOKIE['studentNumber']];
    $axios->cookies = $_SESSION["cookies"];
    
    $result = $axios->getCommunication();
    ?>
<!DOCTYPE html>
<html>
    <head>
        <title>Circolari | REDignus</title>
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
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Data</th>
                                        <th scope="col">Inviata da</th>
                                        <th scope="col">Testo</th>
                                        <th scope="col">Allegato</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        foreach ($result as $key => $value) {
																					//Da aggingere nelle opzioni
																					//$value["text"] = str_replace("_", " ", $value["text"]);
                                            echo '<tr>
                                                                            <th>' . $value["date"] . '</th>
                                                                            <td>' . $value["sender"] . '</td>
                                                                            <td>' . $value["text"] . '</td>
                                                                            <td><!-- <a data-toggle="modal" data-target="#anteprima" data-url="' . $value["attached"] . '">Anteprima</a> | --><a target="_blank" href="' . $value["attached"] . '">Link</a></td>
                                                                        </tr>';
                                        }
                                        ?>
                                </tbody>
                            </table>
                        </div>
                        <br>
                    </div>
                    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#anteprima" data-url="https://redignus.it/comunicazioni">Open modal for @mdo</button>
                        <div class="modal fade" id="anteprima" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <iframe style="border: 0; width: 100%; height: 100%">
                                    <h1>L'anteprima non è disponibile</h1></iframe>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                                </div>
                            </div>
                        </div> -->
                </div>
                <?php include "include/footer.php";?>
                <!-- <script>
                    // $('#anteprima').on('show.bs.modal', function (event) {
                    //     var button = $(event.relatedTarget) // Button that triggered the modal
                    //     var url = button.data('url') // Extract info from data-* attributes
                    //     // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
                    //     // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
                    //     var modal = $(this)
                    //     modal.find('.modal-body iframe').attr("src", url)
                    //     console.log(url);
                        
                    // })
                    </script> -->
            </div><!-- Page Content -->
        </div><!-- Chiusura Wrapper -->
    </body>
</html>