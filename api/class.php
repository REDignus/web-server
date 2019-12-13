<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

use DiDom\Document;
use DiDom\Query;
use DiDom\Element;

class setting
{
    /* *--- Database Private Key---*
    Le credenziale per il database dove verranno salvate le key.
    Questa è la struttura della tabella: 
    Maggiori informazioni sulla wiki (link)
    */
    public $mysql_ip_private_key = "localhost";
    public $mysql_user_private_key = "registro_elettronico";
    public $mysql_password_private_key = "password";
    public $mysql_database_private_key = "registro_elettronico";   

    /* *--- Debug ---*
    In lavorazione (Altamente buggato)
    */
    public $debug = false;

    /* *--- Private Key ---*
    Se questa opzione è impostata su true allora verrà eseguita una richiesta al database nella quale controllerà se la chiave è valida.
    Una chiave per essere valida non deve essere già stata usata precedentemente.
    La chiave verrà chiesta solamente al primo accesso dopodiché verrà associata con l' account.
     */
    public $private_key_enable = false;
}


class axios extends setting
{
    public $cfScuola; //Codice fiscale scuola
    public $username; //Username axios
    private $password; //Password axios
    private $activation_key; //Key di attivazione

    public $cookies;
    public $student; //Studente selezionato [passare solo un array con un solo studente]

    public $postREFamilyData; //Vari input inseriti a caso nella pagina (perché axios non sa fare niente)

    public $QuadrimestreFT; //Indica il periodo dell' anno, se è invalido esplode (es. FT01, FT02...)
    public $QuadrimestreFTAll; //Indica tutti i periodi dell' anno [array]

    public function checkKey()
    {
        // Create connection
        $conn = new mysqli($this->mysql_ip_private_key, $this->mysql_user_private_key, $this->mysql_password_private_key, $this->mysql_database_private_key);

        // Check connection
        if ($conn->connect_error && $this->debug) {
            die("Connection failed: " . $conn->connect_error);
        }

        // prepare and bind
        $stmt = $conn->prepare("SELECT * FROM activation_key WHERE re_cf=? AND re_user=? AND key_enable=1");
        $stmt->bind_param("ss", $this->cfScuola, $this->username);
        $stmt->execute();

        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        if ($result->num_rows == 0)
            return false;
        else
            return true;
    }

    public function activateKey()
    {
        // Create connection
        $conn = new mysqli($this->mysql_ip_private_key, $this->mysql_user_private_key, $this->mysql_password_private_key, $this->mysql_database_private_key);

        // Check connection
        if ($conn->connect_error && $this->debug) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT * FROM activation_key WHERE activation_key=? AND re_cf is NULL AND re_user is NULL AND key_enable=1");
        $stmt->bind_param("s", $this->activation_key);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows >= 1){
            // prepare and bind
            $stmt = $conn->prepare("UPDATE activation_key SET re_cf=?, re_user=? WHERE activation_key=?");
            $stmt->bind_param("sss", $this->cfScuola, $this->username, $this->activation_key);
            $stmt->execute();

            $stmt->close(); 
            $conn->close(); 
            return true;
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    }

    public function clearKey()
    {
        // Create connection
        $conn = new mysqli($this->mysql_ip_private_key, $this->mysql_user_private_key, $this->mysql_password_private_key, $this->mysql_database_private_key);

        // Check connection
        if ($conn->connect_error && $this->debug) {
            die("Connection failed: " . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT * FROM activation_key WHERE activation_key=?");
        $stmt->bind_param("s", $this->activation_key);
        $stmt->execute();
        
        $result = $stmt->get_result();
        if ($result->num_rows >= 1){
            // prepare and bind
            $stmt = $conn->prepare("UPDATE activation_key SET re_cf=NULL, re_user=NULL WHERE activation_key=?");
            $stmt->bind_param("s", $this->activation_key);
            $stmt->execute();

            $stmt->close(); 
            $conn->close(); 
            return true;
        } else {
            $stmt->close();
            $conn->close();
            return false;
        }
    }
    
    public function login($cfScuola = null, $username = null, $password = null, $activation_key = null)
    {
        if (empty($cfScuola) && empty($username) && empty($password)) {
            $cfScuola = $this->cfScuola;
            $username = $this->username;
            $password = $this->password;
            $activation_key = $this->activation_key;
        } else {
            $this->cfScuola = $cfScuola;
            $this->username = $username;
            $this->password = $password;
            $this->activation_key = $activation_key;
        }

        /*
        ERROR CODE:
        
        1 = Utente non presente o password errata
        2 = Codice fiscale della scuola errato
        ----
        10 = La key inserita non è valida
        11 = Il tuo account non è stato attivato, inserisci la key per attivarlo
        ----
        99 = Uno o più campi non compilati
        */

        if ($private_key_enable) { //Se il controllo della chiave di login è abilitato
            if ((!is_null($activation_key)) && (!$this->activateKey()) && (!$this->checkKey())){
                $error['error'] = true;
                $error['errorcode'] = 11;
                $error['msg'] = "La key inserita non è valida";
                return $error; 
            } elseif(!$this->checkKey()){
                $error['error'] = true;
                $error['errorcode'] = 10;
                $error['msg'] = "Il tuo account non è stato attivato, inserisci la key per attivarlo";
                return $error;
            }  
        } //Continua se la chiave è valida
        

        $paginaIniziale = 'https://family.axioscloud.it/Secret/REStart.aspx?Customer_ID=' . $cfScuola;

        //Ottieni i cookie dalla prima pagina (serviranno per identificare la scuola nel login)
        $ch = curl_init($paginaIniziale);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        // get cookie
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
        $cookies = array();
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }
        //get pagina di value post
        preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*value=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $postREStartData['value']); //Ottieni i value dagli input
        preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*name=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $postREStartData['name']); //Ottieni i name dagli input
        $ASP_NET_SessionId = $cookies["ASP_NET_SessionId"];

        //-------------------------------------------------------------------

        //Ottieni il link urlREDefault
        $ch = curl_init($paginaIniziale);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($postREStartData['name'][1][0], "\"") => trim($postREStartData['value'][1][0], "\""),
            trim($postREStartData['name'][1][2], "\"") => trim($postREStartData['value'][1][2], "\""),
            trim($postREStartData['name'][1][1], "\"") => trim($postREStartData['value'][1][1], "\""),
            'ibtnRE.x' => rand(0, 160),
            'ibtnRE.y' => rand(0, 116),
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        curl_setopt($ch, CURLOPT_COOKIE, "ASP.NET_SessionId=$ASP_NET_SessionId");

        $result = curl_exec($ch);
        curl_close($ch);

        //get pagina di redirect
        preg_match_all('/<a\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*href=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $urlredirect);
        $urlREDefault = trim($urlredirect[1][0], "\""); //Url d'intermezzo tra la prima pagina e il login

        //-------------------------------------------------------------------

        $urlREDefault = explode("?", $urlREDefault);
        $urlREDefault[1] = explode("&", $urlREDefault[1]);
        foreach ($urlREDefault[1] as $key => $value) {
            $urlREDefault[1][$key] = ltrim($value, "amp;");
            if (strpos($value, 'Customer_WebSite') !== false) {
                $urlREDefault[1][$key] = "Customer_WebSite=" . $paginaIniziale;
            }
            $urlREDefault[2] = $urlREDefault[2] . $urlREDefault[1][$key] . "&";
        }
        $allUrlREDefault = $urlREDefault[0] . "?" . str_replace(' ', '%20', $urlREDefault[2]);

        //Ottieni il link login
        $ch = curl_init($allUrlREDefault);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // get headers too with this line
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_COOKIE, "ASP.NET_SessionId=$ASP_NET_SessionId");

        $result = curl_exec($ch);
        curl_close($ch);

        // get cookie
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
        $cookies = array();
        foreach ($matches[1] as $item) {
            parse_str($item, $cookie);
            $cookies = array_merge($cookies, $cookie);
        }

        $__AntiXsrfToken = $cookies['__AntiXsrfToken']; //Cookie variabile

        preg_match_all('/<a\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*href=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $urlredirect);
        $urlRELogin = trim($urlredirect[1][0], "\""); //Url redirect al login

        //-------------------------------------------------------------------

        //Vai alla pagina login
        $ch = curl_init("https://family.axioscloud.it" . $urlRELogin);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // get headers too with this line
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, $paginaIniziale); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=$__AntiXsrfToken; ASP.NET_SessionId=$ASP_NET_SessionId");

        $result = curl_exec($ch);
        curl_close($ch);

        //get pagina di value post
        preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*name=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $postRELoginData['name']); //Ottieni i name dagli input
        preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*value=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $postRELoginData['value']); //Ottieni i value dagli input

        //-------------------------------------------------------------------

        //Login
        $ch = curl_init("https://family.axioscloud.it" . $urlRELogin);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it" . $urlRELogin); //link da cui provieni
        $post = [
            trim($postRELoginData['name'][1][0], "\"") => trim($postRELoginData['value'][1][0], "\""),
            trim($postRELoginData['name'][1][1], "\"") => trim($postRELoginData['value'][1][1], "\""),
            trim($postRELoginData['name'][1][2], "\"") => trim($postRELoginData['value'][1][2], "\""),
            'txtUser' => $username,
            'txtPassword' => $password,
            'btnLogin' => "Accedi",
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=$__AntiXsrfToken; ASP.NET_SessionId=$ASP_NET_SessionId");

        $result = curl_exec($ch);
        curl_close($ch);

        preg_match_all('/<a\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*href=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $urlredirect);
        $urlREFamily = trim($urlredirect[1][0], "\""); //Url redirect al login

        //-------------------------------------------------------------------
        // echo "<pre>";
        // var_dump(htmlspecialchars($result));

        
        if (empty($cfScuola) || empty($username) || empty($password)) {
            $error['error'] = true;
            $error['errorcode'] = 99;
            $error['msg'] = "Uno o più campi non compilati";
            if (!is_null($activation_key))
                $this->clearKey();
            return $error;
        } elseif (empty($__AntiXsrfToken)) {
            $error['error'] = true;
            $error['errorcode'] = 2;
            $error['msg'] = "Codice fiscale della scuola errato";
            if (!is_null($activation_key))
                $this->clearKey();
            return $error;
        } elseif (strpos($result, "Utente non presente o password errata")) {
            $error['error'] = true;
            $error['errorcode'] = 1;
            $error['msg'] = "Utente non presente o password errata";
            if (!is_null($activation_key))
                $this->clearKey();
            return $error;
        }

        $cookie = [
            "__AntiXsrfToken" => $__AntiXsrfToken,
            "ASP.NET_SessionId" => $ASP_NET_SessionId,
        ];
        $this->cookies = $cookie;
        return $cookie;
    }

    public function getPostREFamily()
    {
        //Ottieni gli input messi a caso da axios
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        $result = curl_exec($ch);
        curl_close($ch);

        preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*name=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $this->postREFamilyData['name']); //Ottieni i name dagli input
        preg_match_all('/<input\s+(?:[^"\'>]+|"[^"]*"|\'[^\']*\')*value=("[^"]+"|\'[^\']+\'|[^<>\s]+)/i', $result, $this->postREFamilyData['value']); //Ottieni i value dagli input

        return $this->postREFamilyData;
    }

    public function getStudentId()
    {
        //Ottieni il numero dello studente
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        $result = curl_exec($ch);
        curl_close($ch);

        $document = new Document($result);

        $table = $document->find('.TableMasterFamily')[0]; //Trova la tabella con la classe TableMasterFamily
        foreach ($table->children() as $key => $value) { //ogni figlio
            $aluninfo = $value->children()[0]->getAttribute('onclick'); //del figlio seleziona il primo campo (img Genere) e ottieni l'attributo onclick
            $aluName = str_replace(chr( 194 ).chr( 160 ), '', $value->children()[1]->text()); //Il nome contiene un carattere unicode (\u00a0 -> spazio) che va convertito prima di rimpiazzarlo nella stringa del nome
            $aluninfoExplode = explode("\"", $aluninfo); //Esplodi la stringa su " [stringa tipo: AluSelectedInFamily("0","1","00006200")] 
            $output[] = array('num' => $aluninfoExplode[1], 'qualcosa' => $aluninfoExplode[3], 'id' => $aluninfoExplode[5], 'name' => $aluName); //Salva nell'array i dati dell'alunno
        }
        return $output;
    }

    public function getPeriodYear()
    {
        //Ottieni il numero dello studente
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        $result = curl_exec($ch);
        curl_close($ch);

        $document = new Document($result);

        $periodi = $document->find('#ContentPlaceHolderMenu_ddlFT')[0]; //Trova La lista dei periodi dell' anno
        foreach ($periodi->find('option') as $key => $value) { //Per ogni opzione nel select
            if (!empty($value->getAttribute('selected'))) {
                $output["selected"] = $value->getAttribute('value'); //ottieni il value (es: FT01, FT02...)
            } else {
                $output[] = $value->getAttribute('value'); //ottieni il value (es: FT01, FT02...)
            }      
        }
        return $output;
    }

    public function getHomework($date = null)
    {
        if ($date == null)
            $date = date('d/m/Y', time());
        
        //Abilitazione del calendario
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => "REC",
            '__EVENTTARGET' => "FAMILY",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);

        //Vai alla pagina dei compiti e invia il numero dello studente
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => $date,
            '__EVENTTARGET' => "CAL",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);

        //Trova la tabella
        $document = new Document($result);
        $posts = $document->find('table');
        // var_dump($posts[1]->children()[1]->children()[0]->children()[0]->html());
        //                                  ^^^giorno       ^^^0 per la data/1 per gli argomenti/2 per i compiti/3 Assenze/4 Note Dirigente/5 Note Disciplinari
        
        if ($posts[1]->text() === "Registro di Classe - Alunno") {
            $output["empty"] = $posts[1];
            return $output;
        } else { 
            //Generazione output
            $counter = 0;
            foreach ($posts[1]->children()[1]->children() as $key1 => $value1) { //Ogni Giorno (lunedi, martedi...)
                foreach ($value1->children() as $key => $value) { //Ogni colonna (argomenti, data, compiti...)
                    switch ($key) {
                        case '0': //Data
                            $data = explode("</i>", explode("<br>", $value->html())[0])[1]; // data (24/09/2018)
                            $giorno = explode("</small>", explode("<small>", $value->html())[1])[0]; // giorno della settimana (Lunedi)
                            $output[$counter]['info']['date'] = $data;
                            $output[$counter]['info']['day'] = $giorno;
                            break;
                        case '1': //Argomenti
                            $n_materie = 0;
                            $materie = explode("<br>", $value->html());
                            foreach ($materie as $stringa_materia) {
                                $nome_materia = rtrim(explode("</b>", explode("<b>", $stringa_materia)[1])[0], ":"); //Nome materia
                                $argomenti_materia = rtrim(explode("<b>", explode("</b>", $stringa_materia)[1])[0], "</td>"); //Argomenti materia
                                $output[$counter]['arguments'][$n_materie]['name'] = trim($nome_materia, " ");
                                $output[$counter]['arguments'][$n_materie]['text'] = trim($argomenti_materia, " ");
                                $n_materie++;
                            }
                            break;
                        case '2': //Compiti
                            $n_materie = 0;
                            $materie = explode("<br>", $value->html());
                            foreach ($materie as $stringa_materia) {
                                $nome_materia = rtrim(explode("</b>", explode("<b>", $stringa_materia)[1])[0], ":"); //Nome materia
                                $compiti_materia = rtrim(explode("<b>", explode("</b>", $stringa_materia)[1])[0], "</td>"); //Argomenti materia
                                $output[$counter]['homework'][$n_materie]['name'] = trim($nome_materia, " ");
                                $output[$counter]['homework'][$n_materie]['text'] = trim($compiti_materia, " ");
                                $n_materie++;
                            }
                            break;
                    }
                }
                $counter++;
            }
        }
        if (empty($output))
            $output["empty"] = true;
            
        return $output;
    }


    public function getHomeworkFull($date = null)
    {
        if ($date == null)
            $date = date('d/m/Y', time());

        //Abilitazione del calendario
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => "REC",
            '__EVENTTARGET' => "FAMILY",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);
        
        //Vai alla pagina dei compiti e invia il numero dello studente
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => $date,
            '__EVENTTARGET' => "CAL",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);

        //Trova la tabella
        $document = new Document($result);
        $posts = $document->find('table');
        // var_dump($posts[1]->children()[1]->children()[0]->children()[0]->html());
        //                                  ^^^giorno       ^^^0 per la data/1 per gli argomenti/2 per i compiti/3 Assenze/4 Note Dirigente/5 Note Disciplinari

        //Generazione output
        if ($posts[1]->text() == "Registro di Classe - Alunno") {
            $output["empty"] = true;
            return $output;
        } else { 
            $counter = 0;
            foreach ($posts[1]->children()[1]->children() as $key1 => $value1) { //Ogni Giorno (lunedi, martedi...)
                foreach ($value1->children() as $key => $value) { //Ogni colonna (argomenti, data, compiti...)
                    switch ($key) {
                        case '0': //Data
                            $data = explode("</i>", explode("<br>", $value->html())[0])[1]; // data (24/09/2018)
                            $giorno = explode("</small>", explode("<small>", $value->html())[1])[0]; // giorno della settimana (Lunedi)
                            $output[$counter]['info']['date'] = $data;
                            $output[$counter]['info']['day'] = $giorno;
                            break;
                        case '1': //Argomenti
                            $n_materie = 0;
                            $materie = explode("<br>", $value->html());
                            foreach ($materie as $stringa_materia) {
                                $nome_materia = rtrim(explode("</b>", explode("<b>", $stringa_materia)[1])[0], ":"); //Nome materia
                                $argomenti_materia = rtrim(explode("<b>", explode("</b>", $stringa_materia)[1])[0], "</td>"); //Argomenti materia
                                $output[$counter]['arguments'][$n_materie]['name'] = trim($nome_materia, " ");
                                $output[$counter]['arguments'][$n_materie]['text'] = trim($argomenti_materia, " ");
                                $n_materie++;
                            }
                            break;
                        case '2': //Compiti
                            $n_materie = 0;
                            $materie = explode("<br>", $value->html());
                            foreach ($materie as $stringa_materia) {
                                $nome_materia = rtrim(explode("</b>", explode("<b>", $stringa_materia)[1])[0], ":"); //Nome materia
                                $compiti_materia = rtrim(explode("<b>", explode("</b>", $stringa_materia)[1])[0], "</td>"); //Argomenti materia
                                $output[$counter]['homework'][$n_materie]['name'] = trim($nome_materia, " ");
                                $output[$counter]['homework'][$n_materie]['text'] = trim($compiti_materia, " ");
                                $n_materie++;
                            }
                            break;
                        case '4': //Note Dirigente
                            if (!empty($value->text())) {
                                $n_materie = 0;
                                $materie = explode("<br>", $value->html());
                                foreach ($materie as $stringa_materia) {
                                    $prof = explode("</b>", explode("<b>", $value->html())[1])[0]; // Nome del prof
                                    $prof  = trim($prof, ":");
                                    $testo = explode("</b>", $value->html())[1]; // Testo
                                    $testo = trim($testo, " ");
                                    $output[$counter]['profnotes'][$n_materie]['prof'] = $prof;
                                    $output[$counter]['profnotes'][$n_materie]['text'] = trim($testo, "</td>");
                                }
                            }
                            break;
                        case '5': //Note Disciplinari
                            if (!empty($value->text())) {
                                $n_materie = 0;
                                $materie = explode("<br>", $value->html());
                                foreach ($materie as $stringa_materia) {
                                    $prof = explode("</b>", explode("<b>", $stringa_materia)[1])[0]; // Nome del prof
                                    $prof  = trim($prof, ":");
                                    $testo = explode("</b>", $stringa_materia)[1]; // Testo
                                    $testo = trim($testo, " ");
                                    $output[$counter]['disciplinary'][$n_materie]['prof'] = $prof;
                                    $output[$counter]['disciplinary'][$n_materie]['text'] = trim($testo, "</td>");
                                }
                            }
                            break;
                    }
                }
                $counter++;
            }
        }
        if (empty($output))
            $output["empty"] = true;
            
        return $output;
    }


    public function getNote($date = null)
    {
        //Vai alla pagina dei compiti e invia il numero dello studente
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => "REC",
            '__EVENTTARGET' => "FAMILY",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);

        //Trova la tabella
        $document = new Document($result);
        $posts = $document->find('table');
        // var_dump($posts[1]->children()[1]->children()[0]->children()[0]->html());
        //                                  ^^^giorno       ^^^0 per la data/1 per gli argomenti/2 per i compiti/3 Assenze/4 Note Dirigente/5 Note Disciplinari

        //Generazione output
        $counter = 0;
        foreach ($posts[1]->children()[1]->children() as $key1 => $value1) { //Ogni Giorno (lunedi, martedi...)
            if ((!empty($value1->children()[5]->text())) || (!empty($value1->children()[4]->text()))) {
                foreach ($value1->children() as $key => $value) { //Ogni colonna (argomenti, data, compiti...)
                    switch ($key) {
                        case '0': //Data
                            $data = explode("</i>", explode("<br>", $value->html())[0])[1]; // data (24/09/2018)
                            $giorno = explode("</small>", explode("<small>", $value->html())[1])[0]; // giorno della settimana (Lunedi)
                            $output[$counter]['info']['date'] = $data;
                            $output[$counter]['info']['day'] = $giorno;
                            break;
                        case '4': //Note Dirigente
                            if (!empty($value->text())) {
                                $prof = explode("</b>", explode("<b>", $value->html())[1])[0]; // Nome del prof
                                $prof  = trim($prof, ":");
                                $testo = explode("</b>", $value->html())[1]; // Testo
                                $testo = trim($testo, " ");
                                $output[$counter]['profnotes']['prof'] = $prof;
                                $output[$counter]['profnotes']['text'] = trim($testo, "</td>");
                            }
                            break;
                        case '5': //Note Disciplinari
                            if (!empty($value->text())) {
                                $prof = explode("</b>", explode("<b>", $value->html())[1])[0]; // Nome del prof
                                $prof  = trim($prof, ":");
                                $testo = explode("</b>", $value->html())[1]; // Testo
                                $testo = trim($testo, " ");
                                $output[$counter]['disciplinary']['prof'] = $prof;
                                $output[$counter]['disciplinary']['text'] = trim($testo, "</td>");
                            }
                            break;
                    }
                }
                $counter++;
            }
        }
        if (empty($output))
            $output["empty"] = true;

        return $output;
    }


    public function getVote()
    {
        //Vai alla pagina dei voti e invia il numero dello studente
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => "RED",
            '__EVENTTARGET' => "FAMILY",
            // Quadrimestre
            'ctl00$ContentPlaceHolderMenu$ddlFT' => $this->QuadrimestreFT,
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);


        //Trova la tabella
        $document = new Document($result);
        $posts = $document->find('table');
        // var_dump($posts[1]->children()[1]->children()[0]->children()[5]->html());
        //                                  ^^^voto       ^^^0 per la data/1 materia/2 tipo/3 voto/4 commento/5 professore

        //Generazione output
        $counter = 0;
        foreach ($posts[1]->children()[1]->children() as $key1 => $value1) {//Ogni singolo voto
            foreach ($value1->children() as $key => $value) {//Ogni singolo campo (data, materia, voto...)
                switch ($key) {
                    case '0'://Data
                        $output[$counter]["date"] = $value->text();//Ottieni la data
                        break;
                    case '1'://Materia
                        $output[$counter]["topic"] = $value->text();//Ottieni il nome della materia
                        break;
                    case '2'://Tipo
                        $output[$counter]["type"] = $value->text();//Ottieni il tipo (orale, scritto...)
                        break;
                    case '3'://Voto
                        $output[$counter]["realVote"] = explode("Valore: ", $value->title)[1];//Ottieni il voto reale
                        $output[$counter]["vote"] = $value->text();//Ottieni il voto
                        break;
                    case '4'://Obiettivi
                        $output[$counter]["target"] = $value->text();//Ottieni gli Obiettivi
                        break;
                    case '5'://Commento
                        $output[$counter]["comment"] = $value->text();//Ottieni il commento
                        break;
                    case '6'://Professore
                        $output[$counter]["teacher"] = $value->text();//Ottieni il nome del professore
                        break;
                }
            }
            $counter++;
        }
        if (empty($output))
            $output["empty"] = true;
        return $output;
    }

    public function getAverageVote()
    {
        //Vai alla pagina delle della tabella assenza e invia il numero dello studente
        //Abilitazione delle comunicazioni
        $base64id = base64_encode($this->student['id']."|".$this->QuadrimestreFT); // base64 da inviare con l' id dello studente seguito da "|FT01"
        $ch = curl_init("https://family.axioscloud.it/Secret/APP_Ajax_Get.aspx?Action=FAMILY_REGISTRO_DOCENTI_GRIGLIA&Others=".$base64id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            // Quadrimestre
            'ctl00$ContentPlaceHolderMenu$ddlFT' => $this->QuadrimestreFT,
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);


        $document = new Document($result);
        $tr = $document->find('#elencoMaterie')[0]->first('tbody')->find('tr'); //ottieni ogni riga della tabella con la materia e la media

        foreach ($tr as $key => $value) {
            $output[] = array("name" => $value->find('td')[0]->text(),//imposta il nome della materia
             "average" => $value->find('td')[1]->text());//imposta la media
        }        
        
        return $output;
    }

    
    public function getAbsences()
    {
        //Vai alla pagina delle assenze e invia il numero dello studente
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => "Assenze",
            '__EVENTTARGET' => "FAMILY",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);

        //Trova la tabella
        $document = new Document($result);
        $posts = $document->find('table');
        


        
        foreach ($posts as $key => $tabella) {
            if ($key == 0) {
                continue;
            }

            if ("Elenco assenze da giustificare" == $tabella->parent()->parent()->child(0)->text()) {
                //Assenze da giustificare

                foreach ($tabella->child(1)->children() as $key => $giorno) {
                    try {
                        $temp["date"] = str_replace(chr( 194 ).chr( 160 ), '', $giorno->child(1)->text()); //Il nome contiene un carattere unicode (\u00a0 -> spazio) che va convertito prima di rimpiazzarlo nella stringa del nome
                        $temp["type"] = str_replace(chr( 194 ).chr( 160 ), '', $giorno->child(2)->text()); //Il nome contiene un carattere unicode (\u00a0 -> spazio) che va convertito prima di rimpiazzarlo nella stringa del nome
                    } catch (Error $e) {
                        $temp["date"] = str_replace(chr( 194 ).chr( 160 ), '', $giorno->child(0)->text()); //Il nome contiene un carattere unicode (\u00a0 -> spazio) che va convertito prima di rimpiazzarlo nella stringa del nome
                        $temp["type"] = str_replace(chr( 194 ).chr( 160 ), '', $giorno->child(1)->text()); //Il nome contiene un carattere unicode (\u00a0 -> spazio) che va convertito prima di rimpiazzarlo nella stringa del nome
                    }

                    $output["tojustify"][] = $temp;
                }

            } elseif ("Elenco assenze giustificate" == $tabella->parent()->parent()->child(0)->text()) {
                //Assenze giustificate
                
                foreach ($tabella->child(1)->children() as $key => $giorno) {
                    $temp["date"] = str_replace(chr( 194 ).chr( 160 ), '', $giorno->child(0)->text()); //Il nome contiene un carattere unicode (\u00a0 -> spazio) che va convertito prima di rimpiazzarlo nella stringa del nome
                    $temp["type"] = str_replace(chr( 194 ).chr( 160 ), '', $giorno->child(1)->text()); //Il nome contiene un carattere unicode (\u00a0 -> spazio) che va convertito prima di rimpiazzarlo nella stringa del nome

                    $output["justified"][] = $temp;
                }
            }

        }
        if (empty($output))
            $output["empty"] = true;

        return $output;
    }



    public function getHomeworkDateRange($fromdate, $todate)
    {
        
        // (Passare solo date con il trattino)
        function getStartAndEndDateOf2Week($date_input) {
            if (is_int($date_input))
                $timestamp = $date_input;
            else
                $timestamp = strtotime(str_replace('/', '-', $date_input));
            $date = date('Y-m-d', $timestamp);

            $ddate = new DateTime($date);
            $week = $ddate->format("W");
            $year = $ddate->format("Y");
            
            $dto = new DateTime();
            $dto->setISODate($year, $week);
            $ret['week_start'] = $dto->format('Y-m-d');
            $dto->modify('+13 days');
            $ret['week_end'] = $dto->format('Y-m-d');
            return $ret;
        }

        function dateIsGreater($old_date_input, $new_date_input) {
            if (is_int($old_date_input))
                $timestamp = $old_date_input;
            else
                $timestamp = strtotime(str_replace('/', '-', $old_date_input));
            $old_date = date('Y-m-d', $timestamp);

            if (is_int($new_date_input))
                $timestamp = $new_date_input;
            else
                $timestamp = strtotime(str_replace('/', '-', $new_date_input));
            $new_date = date('Y-m-d', $timestamp);

            if ($old_date > $new_date) {
                return true; //more
            }else{
                return false; //less
            }
        }


        //Abilitazione del calendario
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => "REC",
            '__EVENTTARGET' => "FAMILY",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);
        
        $lastdate = $fromdate;
        while (!dateIsGreater($lastdate, $todate)) {
            $date = getStartAndEndDateOf2Week($lastdate);
            $date_to_check[] = $date["week_start"];
            $lastdate = strtotime(str_replace('/', '-', $date["week_end"])." +1 day");
        }

				
				if (count($date_to_check) > 15){
					$output["empty"] = true;
					$output["toodate"] = true;
					$output["ndate"] = count($date_to_check);
					return $output;
				}
					
        $counter = 0;
        foreach ($date_to_check as $key => $value) {
            //Vai alla pagina dei compiti e invia il numero dello studente
            $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
    
            curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni
    
            curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);
    
            curl_setopt($ch, CURLOPT_POST, 1);
            $post = [
                trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
                trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
                trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
                '__EVENTARGUMENT' => $value,
                '__EVENTTARGET' => "CAL",
                //Dati dell'alunno
                'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
                'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
            ];
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    
            $result = curl_exec($ch);
            curl_close($ch);
    
            //Trova la tabella
            $document = new Document($result);
            $posts = $document->find('table');
            // var_dump($posts[1]->children()[1]->children()[0]->children()[0]->html());
            //                                  ^^^giorno       ^^^0 per la data/1 per gli argomenti/2 per i compiti/3 Assenze/4 Note Dirigente/5 Note Disciplinari
            
            if ($posts[1]->text() === "Registro di Classe - Alunno") {
                // $output["empty"] = $posts[1];
            } else { 
                //Generazione output
                foreach ($posts[1]->children()[1]->children() as $key1 => $value1) { //Ogni Giorno (lunedi, martedi...)
                    if ($lastprintday != $value1->children()[0]->text()) {
                        $lastprintday = $value1->children()[0]->text();
                        foreach ($value1->children() as $key => $value) { //Ogni colonna (argomenti, data, compiti...)
                            switch ($key) {
                                case '0': //Data
                                    $data = explode("</i>", explode("<br>", $value->html())[0])[1]; // data (24/09/2018)
                                    $giorno = explode("</small>", explode("<small>", $value->html())[1])[0]; // giorno della settimana (Lunedi)
                                    $output[$counter]['info']['date'] = $data;
                                    $output[$counter]['info']['day'] = $giorno;
                                    break;
                                case '1': //Argomenti
                                    $n_materie = 0;
                                    $materie = explode("<br>", $value->html());
                                    foreach ($materie as $stringa_materia) {
                                        $nome_materia = rtrim(explode("</b>", explode("<b>", $stringa_materia)[1])[0], ":"); //Nome materia
                                        $argomenti_materia = rtrim(explode("<b>", explode("</b>", $stringa_materia)[1])[0], "</td>"); //Argomenti materia
                                        $output[$counter]['arguments'][$n_materie]['name'] = trim($nome_materia, " ");
                                        $output[$counter]['arguments'][$n_materie]['text'] = trim($argomenti_materia, " ");
                                        $n_materie++;
                                    }
                                    break;
                                case '2': //Compiti
                                    $n_materie = 0;
                                    $materie = explode("<br>", $value->html());
                                    foreach ($materie as $stringa_materia) {
                                        $nome_materia = rtrim(explode("</b>", explode("<b>", $stringa_materia)[1])[0], ":"); //Nome materia
                                        $compiti_materia = rtrim(explode("<b>", explode("</b>", $stringa_materia)[1])[0], "</td>"); //Argomenti materia
                                        $output[$counter]['homework'][$n_materie]['name'] = trim($nome_materia, " ");
                                        $output[$counter]['homework'][$n_materie]['text'] = trim($compiti_materia, " ");
                                        $n_materie++;
                                    }
                                    break;
                            }
                        }
                        $counter++;
                    }
                }
            }
            if (empty($output))
                $output["empty"] = true;
        }
            
        return $output;
    }


    public function getCommunication()
    {
        //Vai alla pagina delle circolari e invia il numero dello studente
        //Abilitazione delle comunicazioni
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => "REC",
            '__EVENTTARGET' => "FAMILY",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);

        //Ottenimento delle comunicazioni
        $ch = curl_init("https://family.axioscloud.it/Secret/APP_Ajax_Get.aspx?Action=READ_COMUNICAZIONI_FAMILY&Others=0"); //Url Comunicazioni (Sperando che axios non lo cambi una volta al giorno)
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        $result = curl_exec($ch);
        curl_close($ch);


        //Trova la tabella
        $document = new Document($result);
        $posts = $document->find('table');
        // var_dump($posts[0]->children()[1]->children()[0]->children()[4]->html());
        //                  ^^^tbody(1)      ^^^riga        ^^^0 per la data/1 inviata da/2 text/3 allegati/4 letto

        //Generazione output
        $counter = 0;
        foreach ($posts[0]->children()[1]->children() as $key1 => $value1) { //Ogni singolo assenza
            foreach ($value1->children() as $key => $value) {
                switch ($key) {
                    case '0': //Data
                        $output[$counter]["date"] = $value->text(); //Ottieni la data
                        break;
                    case '1': //Inviata da
                        $output[$counter]["sender"] = $value->text(); //Ottieni chi l'ha creata
                        break;
                    case '2': //Testo
                        $output[$counter]["text"] = $value->text(); //Ottieni il testo
                        break;
                    case '3': //Allegati
                        if ($value->children()[0] != null)
                            $output[$counter]["attached"] = $value->children()[0]->getAttribute('href'); //Ottieni l'allegato
                        else
                            $output[$counter]["attached"] = ""; //Se non presente non ottenere nulla
                        break;
                    // case '4': //Stato
                    //     $output[$counter]["state"] = $value->text(); //Ottieni lo stato (letto/permante/non letto)
                    //     break;
                }
            }
            $counter++;
        }
        if (empty($output))
            $output["empty"] = true;

        return $output;
    }


    public function getAbsencesTotalHours()
    {
        //Vai alla pagina delle della tabella assenza e invia il numero dello studente
        //Abilitazione delle comunicazioni
        $assenze = 0; //Imposta le ore di assenza a 0
        foreach ($this->QuadrimestreFTAll as $key => $value) { //Per ogni periodo dell' anno aggingi le ore di assenza
            $base64id = base64_encode($this->student['id']."|".$value); // base64 da inviare con l' id dello studente seguito da "|FTXX"
            $ch = curl_init("https://family.axioscloud.it/Secret/APP_Ajax_Get.aspx?Action=FAMILY_REGISTRO_DOCENTI_GRIGLIA&Others=".$base64id);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);

            curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

            curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

            curl_setopt($ch, CURLOPT_POST, 1);
            $post = [
                // Quadrimestre
                'ctl00$ContentPlaceHolderMenu$ddlFT' => $value,
            ];

            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

            $result = curl_exec($ch);
            curl_close($ch);


            $document = new Document($result);
            $posts = $document->find('.assenza'); //trva tutto cio` che ha la casse assenza

            foreach ($posts as $key => $value) { //ogni elemento trovato ottieni il testo
                $ora_assenza = str_replace("A", "", $value->text()); //Rimpiazza tutte le A nelle ore per avere un intero
                $assenze += $ora_assenza; //Somma alle ore di assenza
            }
        }

        return $assenze;
    }

    public function getSchedule()
    {
        //Vai alla pagina delle assenze e invia il numero dello studente
        $ch = curl_init("https://family.axioscloud.it/Secret/REFamily.aspx");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_REFERER, "https://family.axioscloud.it/Secret/RELogin.aspx"); //link da cui provieni

        curl_setopt($ch, CURLOPT_COOKIE, "__AntiXsrfToken=" . $this->cookies['__AntiXsrfToken'] . "; ASP.NET_SessionId=" . $this->cookies['ASP.NET_SessionId']);

        curl_setopt($ch, CURLOPT_POST, 1);
        $post = [
            trim($this->postREFamilyData['name'][1][0], "\"") => trim($this->postREFamilyData['value'][1][0], "\""),
            trim($this->postREFamilyData['name'][1][1], "\"") => trim($this->postREFamilyData['value'][1][1], "\""),
            trim($this->postREFamilyData['name'][1][2], "\"") => trim($this->postREFamilyData['value'][1][2], "\""),
            '__EVENTARGUMENT' => "Orario",
            '__EVENTTARGET' => "FAMILY",
            //Dati dell'alunno
            'ctl00$ContentPlaceHolderBody$txtIDAluSelected' => $this->student['num'],
            'ctl00$ContentPlaceHolderBody$txtAluSelected' => $this->student['id'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        $result = curl_exec($ch);
        curl_close($ch);


        //Trova la tabella
        $document = new Document($result);
        $tabella = $document->find('table')[1];

        // $tabella->child(0)->                      child(0)->                      child(1)->
        //               ^^^0 lista giorni e date        ^^^Table row (obbligatorio)     ^^^1 lunedi 2 martedi...
        // child(0)              
        //      ^^^ 0 Nome girno della settimana / 2 data

        // $tabella->child(1)->                                         child(0)->                                                                                                                   
        //                ^^^ 1 Dati Tabella                                  ^^^Ora(pari) e materia(dispari)         
        // child(0)->
        //      ^^^0 (se il precedente pari)numero ora / (se il precedente dispari)  giorni della settimana 0 lunedi 1 martedi... 
        // child(0)
        //      ^^^ (se il precedinte è giorni della settimana) 0 materia / 2 insegnante ... 4 materia / 6 insegnante ...

        $orario = [];

        //Ottieni i girni e le date della settimana
        foreach ($tabella->child(0)->child(0)->children() as $key => $value) {
            //ignora l'elemento data
            if ($key == 0)
                continue; 

            //ottieni giorno e data
            $temp["day"] = $value->child(0)->text();
            $temp["date"] = $value->child(2)->text();

            //inseriscili nell'array
            array_push($orario, $temp);
        }

        $subject = [];
        $time = [];
        // return $tabella->child(1)->children()[5]->children()[4]->children();
        foreach ($tabella->child(1)->children() as $key => $value) {
            # code...
            if ($key%2) {
                // Dispari Materia

                $day = 0;
                foreach ($value->children() as $key_giorni => $value_giorni) {
                    // giorni della settimana 0 lunedi 1 martedi
                    if (null === $subject[$day]) {
                        $subject[$day] = [];
                    }

                    unset($temp_array);
                    

                    unset($temp);
                    $temp = $value_giorni->child(2);
                    if (!empty($temp)) {
                        $temp_array["schedule"][0]["teachers"] = trim($temp->text(), "/ ");

                        unset($temp);
                        $temp = $value_giorni->child(0);
                        $temp_array["schedule"][0]["subject"] = trim($temp->text(), "/ ");

                    }

                    unset($temp);
                    $temp = $value_giorni->child(4);
                    if (!empty($temp)) {
                        $temp_array["schedule"][1]["subject"] = trim($temp->text(), "/ ");
                    }
                    
                    unset($temp);
                    $temp = $value_giorni->child(6);
                    if (!empty($temp)) {
                        $temp_array["schedule"][1]["teachers"] = trim($temp->text(), "/ ");
                    }
                    
                    if (!empty($temp_array))
                        array_push($subject[$day], $temp_array);


                    if (empty($subject[$day]))
                        unset($subject[$day]);

                    $day++;
                }

            } else {
                //Pari orario     

                $day = 0;
                foreach ($value->children() as $key_giorni => $value_giorni) {
                    if ($key_giorni == 0) {
                        continue;
                    }
                    // giorni della settimana 0 lunedi 1 martedi
                    if (null === $time[$day]) {
                        $time[$day] = [];
                    }

                    unset($temp_array);

                    unset($temp);
                    $temp = $value_giorni->child(0)->text();
                    if (!empty($temp)) {
                        $temp_array["start"] = explode("-", $temp)[0];
                        $temp_array["end"] = explode("-", $temp)[1];
                    }
                    
                    
                    if (!empty($temp_array))
                        array_push($time[$day], $temp_array);


                    if (empty($time[$day]))
                        unset($time[$day]);

                    $day++;
                }
                
                
            }
            // $temp["schedule"][]
        }

        foreach ($subject as $key => $value) {
            
            $output[$key]["schedule"] = $value;
            $output[$key]["date"] = $orario[$key];
            foreach ($value as $key1 => $value1) {
                $output[$key]["schedule"][$key1]["info"] = $time[$key][$key1];
            }
        }

        return $output;
    }

}