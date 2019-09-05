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