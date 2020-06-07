<?php
    session_start();
    if(!isset($_SESSION['nomeutente']) || !isset($_SESSION['idutente']))
        header("location: ../index.php?feedback=0");
    else {
        $nomeutente = $_SESSION['nomeutente'];
        $idutente = $_SESSION['idutente'];
        $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");

        // CONTROLLO SE IL nomeutente E' REALMENTE ASSOCIATO ALL'idutente
        $query = "SELECT idutente FROM utenti WHERE nomeutente = '$nomeutente'";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $idut = $result["idutente"];
        if($idut != $idutente)
            header("location: ../index.php?feedback=0");
        else {
            session_unset();
            session_destroy();
            mysqli_close($con);
            header("location: ../index.php?feedback=4");
        }
    }
?>
