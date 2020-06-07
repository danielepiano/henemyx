<?php
    session_start();

    $nomeutente = $_SESSION['nomeutente'];
    $idutente = $_SESSION['idutente'];

    $ricerca = file_get_contents("php://input");

    $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");

    if($ricerca != '') {
        $query =   "SELECT ch.idchat, u1.nomeutente AS nomeutente1, u2.nomeutente AS nomeutente2
                    FROM chat ch, utenti u1, utenti u2
                    WHERE u1.idutente = ch.idutente1
                    AND u2.idutente = ch.idutente2
                    AND ((u1.idutente = $idutente AND u2.nomeutente LIKE '%$ricerca%')
                    OR (u2.idutente = $idutente AND u1.nomeutente LIKE '%$ricerca%'))";

        $result = mysqli_query($con, $query);

        if(mysqli_num_rows($result) == 0)
            echo "<div class='didascalia' id='attention'><p>ops... nessun risultato per '$ricerca'</p></div>";

        while($utenter = mysqli_fetch_array($result)) {
            if($utenter["nomeutente1"] == $nomeutente)
                $nomeutenter = $utenter["nomeutente2"];
            else
                $nomeutenter = $utenter["nomeutente1"]; // r -> ricercato
            $idchatr = $utenter['idchat'];
            echo "<div class='utenteCont'>
                    <p class='info'>$nomeutenter</p>
                    <input id='aprichat' name='aprichat' class='mex' type='submit' value='$idchatr'/>
                    <label for='aprichat'>apri la chat!</label>
                    <span></span>
                </div>";
        }
    }
    else {
        echo "<div class='didascalia' id='search'><p>cerca un contatto</p></div>";
    }
?>
