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
    <div class="col">
        <div id="registroelementi">
        </div>
    </div>
</div>
<?php include "include/footer.php"; ?>
<script>
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
    
    function convertDate(inputFormat) {
        function pad(s) { return (s < 10) ? '0' + s : s; }
        var d = new Date(inputFormat);
        return [pad(d.getDate()), pad(d.getMonth()+1), d.getFullYear()].join('/');
    }

    function registro() {
        if (getUrlParameter("date") === undefined) {
            var getdate = "";
        } else {
            var getdate = getUrlParameter("date");
        }

        // Ajax Stringa voti
        $('#registroelementi').html('<div class="text-center"><br><h2>Caricamento</h2><i class="fas fa-circle-notch fa-spin fa-4x"></i></div>');
        $.getJSON('api/ajax/registro?date='+getdate, function(data) {
            if (getdate === "") {
                var nextworkday = new Date();

                var nowdate = new Date();
                nowdate.setHours(0,0,0,0);

            } else {
                var nextworkday = new Date(getdate);
                
                var nowdate = new Date(getdate);
                nowdate.setHours(0,0,0,0);
            }
            if(nextworkday.getDay() == 6 || nextworkday.getDay() == 0 || nextworkday.getDay() == 5) { //SE è venerdì sabato o domentica
                nextworkday.setDate(nextworkday.getDate() + (1 + 7 - nextworkday.getDay()) % 7);

            } else {
                nextworkday.setDate(nextworkday.getDate()+1);
            }
            nextworkday.setHours(0,0,0,0);
            var nextworkdaystring = convertDate(nextworkday);
            
            console.log(nextworkdaystring);
            
            $('#registroelementi').html('<br>');
            var numcollaps = 0;
            
            // Per ogni voto
            data.forEach(element => {
                numcollaps++;
                var output = ""; //Resetta l' output
                var collapseclass = "";
                var bgclass = "";
                console.log(element);

                var elementdate = new Date(element.info.date.split("/").reverse().join("-"));
                elementdate.setHours(0,0,0,0);

                
                if (nowdate.getTime() == elementdate.getTime()) { //The ==, !=, ===, and !== operators require you to use date.getTime()
                    console.log("oggi");
                    bgclass = "alert-secondary";
                } else if (elementdate < nextworkday) {
                    console.log("Prima");
                    return; // stop processing this iteration
                } else if (element.info.date == nextworkdaystring) {
                    console.log("il prossimo giorno di lavoro");
                    collapseclass = "show";
                    // bgclass = "alert-primary";
                }
                
                if (element.arguments[0].name !== "") {
                    output += '<hr style="border-top: 1px solid rgba(0,0,0,.8);"><h3>Argomenti</h3>';

                    element.arguments.forEach(argumentday => {
                        output += '<hr><b>'+argumentday.name+':</b> '+argumentday.text;
                    });
                }
                if (element.homework[0].name !== "") {
                    output += '<hr style="border-top: 1px solid rgba(0,0,0,.8);"><h3>Compiti</h3>';
                    element.homework.forEach(homeworkday => {
                        output += '<hr><b>'+homeworkday.name+':</b> '+homeworkday.text;
                    });
                }
                if (element.profnotes !== undefined) {                                    
                    output += '<hr style="border-top: 1px solid rgba(0,0,0,.8);"><h3>Note Dirigente</h3>';
                    element.profnotes.forEach(profnotesday => {
                        output += '<hr><b>'+profnotesday.prof+':</b> '+profnotesday.text;
                    });
                }
                if (element.disciplinary !== undefined) {                                    
                    output += '<hr style="border-top: 1px solid rgba(0,0,0,.8);"><h3>Note Disciplinari</h3>';
                    element.disciplinary.forEach(disciplinaryday => {
                        output += '<hr><b>'+disciplinaryday.prof+':</b> '+disciplinaryday.text;
                    });
                }
                // Salva le informazioni precedenti
                var precedente = $('#registroelementi').html();
                // Stampa il voto più le informazioni precedenti
                $('#registroelementi').html(precedente + '<div class="card '+bgclass+'" data-toggle="collapse" data-target="#collapse'+numcollaps+'" aria-expanded="true" aria-controls="collapse'+numcollaps+'">'+
                    '<div class="card-body">'+
                        '<div class="row">'+
                            '<div class="col-10 col-md-11">'+
                                '<h3>'+element.info.date+'</h3>'+
                                '<h4>'+element.info.day+'</h4>'+
                            '</div>'+
                            '<div class="col-2 col-md-1 text-right">'+
                                '<i style="height: 100%; weight 100%; display: flex; justify-content: center; align-items: center;" class="fas fa-angle-down fa-2x"></i>'+
                            '</div>'+
                        '</div>'+
                        '<div class="text-center collapse '+collapseclass+'" id="collapse'+numcollaps+'">'+
                            output+
                        '</div>'+
                    '</div>'+
                '</div>'+
                '<br>');
            });
        });
    }
    registro();
</script>