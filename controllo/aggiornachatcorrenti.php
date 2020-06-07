<?php
    session_start();

    $nomeutente = $_SESSION['nomeutente'];
    $idutente = $_SESSION['idutente'];

    $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");

    $query =   "SELECT ch.idchat, u1.nomeutente AS nomeutente1, u2.nomeutente AS nomeutente2
                FROM chat ch, utenti u1, utenti u2
                WHERE u1.idutente = ch.idutente1
                  AND u2.idutente = ch.idutente2
                  AND (u1.idutente = $idutente OR u2.idutente = $idutente)
                  AND ch.idchat IN (
                      SELECT c.idchat
                      FROM chat c, messaggi m
                      WHERE m.idchat = c.idchat
                      GROUP BY c.idchat
                      HAVING COUNT(*) > 0)
                ORDER BY nomeutente1, nomeutente2";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) == 0)
        echo "<div class='didascalia' id='startchat'><p>inizia subito una nuova chat</p></div>";

    while($chatt = mysqli_fetch_array($result)) {
        if($chatt['nomeutente1'] == $nomeutente)
            $utenteds = $chatt['nomeutente2'];
        else
            $utenteds = $chatt['nomeutente1'];
        $idchatds = $chatt['idchat'];
        echo '<div class="utenteCont">
                <p class="info">' . $utenteds . '</p>
                <input id="aprichat" name="aprichat" class="mex" type="submit" value="' . $idchatds . '">
                <label for="aprichat">apri la chat!</label>
                <span></span>';
        // SE CI SONO MESSAGGI NON LETTI, MOSTRA UNA SPIA
        $query2 = "SELECT COUNT(*) AS newmex
                    FROM chat ch, messaggi m
                    WHERE m.idchat = ch.idchat
                      AND (ch.idutente1 = $idutente OR ch.idutente2 = $idutente)
                      AND m.idmittente != $idutente AND ch.idchat = $idchatds
                      AND m.confermalettura = 0";
        $result2 = mysqli_query($con, $query2);
        $result2 = mysqli_fetch_array($result2);
        $nmex = $result2['newmex'];
        if($nmex != 0)
            echo '<div class="newmex"><p></p><h3>' . $nmex . '</h3></div>';
        echo "</div>";
    }
?>
