<?php
    session_start();
    if(!isset($_SESSION['nomeutente']) || !isset($_SESSION['idutente']) || !isset($_POST["aprichat"]))
        header("location: ../index.php?feedback=0");
    else {
        $nomeutente = $_SESSION['nomeutente'];
        $idutente = $_SESSION['idutente'];
        $idchat = $_POST["aprichat"];
        $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");

        // CONTROLLO SE IL nomeutente E' REALMENTE ASSOCIATO ALL'idutente
        $query = "SELECT idutente FROM utenti WHERE nomeutente = '$nomeutente'";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $idut = $result["idutente"];
        if($idut != $idutente)
            header("location: ../index.php?feedback=0");
        else {
            // CONTROLLO SE L'idchat E' REALMENTE ASSOCIATO ALL'idutente
            $query =    "SELECT ch.idutente1, u1.nomeutente AS nomeutente1, ch.idutente2, u2.nomeutente AS nomeutente2
                        FROM chat ch, utenti u1, utenti u2
                        WHERE u1.idutente = ch.idutente1 AND u2.idutente = ch.idutente2
                        AND (idutente1 = $idutente AND idchat = $idchat)
                        OR (idutente2 = $idutente AND idchat = $idchat)";
            $result = mysqli_query($con, $query);
            if(mysqli_num_rows($result) == 0)
                header("location: ../index.php?feedback=0");
            else {
                $_SESSION['idchat'] = $idchat;
                header("location: ../chat/");
            }
        }
    }
?>
