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
                        echo '<tr><th>' . $value["info"]["date"] . '<br>' . $value["info"]["day"] . '</th>';

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
</div>