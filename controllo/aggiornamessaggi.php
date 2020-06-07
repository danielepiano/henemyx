<?php
    session_start();
	
    include '../hillcipher/hill.php';
    header('Content-Type: text/html; charset=utf-8');

    $idutente = $_SESSION['idutente'];
    $idchat = $_SESSION['idchat'];

    $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");
    //mysqli_set_charset($con, "utf8");
    $query = "SELECT * FROM messaggi WHERE idchat = $idchat ORDER BY dataora ASC";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) == 0)
        echo "<div class='didascalia' id='startmessaggio'><p>invia un messaggio</p></div>";

    while($messaggio = mysqli_fetch_array($result)) {
        $corpo = $messaggio['corpo'];
        $dataora_ = $messaggio['dataora'];
        	$do = explode(" ", $dataora_);
            $data = date("d/m/Y", strtotime($do[0]));
            $ora = $do[1];
        $dataora = $data . " " . $ora;
        $idmit = $messaggio['idmittente'];
        $cl = $messaggio['confermalettura'];

        // SELEZIONO LA CHIAVE ASSOCIATA ALLA CHAT idchat
        $query2 = "SELECT chiavecifratura FROM chat WHERE idchat = $idchat";
        $result2 = mysqli_query($con, $query2);
        $result2 = mysqli_fetch_array($result2);
        $chiaveStringa = $result2["chiavecifratura"];
        // CIFRATURA MESSAGGIO SECONDO CIFRARIO DI HILL
        $chiaro = cifrarioHill($corpo, $chiaveStringa, 1) /*. mysqli_character_set_name($con)*/;
        //$chiaro = iconv('ASCII', 'UTF-8//IGNORE', $chiaro);
        //$chiaro = mb_convert_encoding($chiaro, "UTF-8");

        echo "<div class=";
            if($idmit == $idutente)         // DIVERSE CLASSI PER IMPOSTARE MESSAGGI MITTENTE
                echo "'miomessaggio'>";
            else                            // E DESTINATARIO
                echo "'altromessaggio'>";
        echo "<div class='datamessaggio'>$dataora</div>
                <div class='corpomessaggio'>$chiaro</div>";
        if($cl && $idutente == $idmit)
            echo "<div class='cl'></div>";     // VISUALIZZA CONFERMA DI LETTURA
        echo "</div>";
    }

    // AGGIORNO COME VISUALIZZATI I MESSAGGI DEL DESTINATARIO CHE HANNO CONFERMA DI LETTURA = 0
    $query = "UPDATE messaggi SET confermalettura = b'1'
    		  WHERE idchat = $idchat
                AND confermalettura = 0
                AND idmittente <> $idutente";
    $result = mysqli_query($con, $query);
?>
