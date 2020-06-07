<?php
    session_start();
    if(!isset($_SESSION['nomeutente']) || !isset($_SESSION['idutente']) || !isset($_POST["changemode"]))
        header("location: ../index.php?feedback=0");
    else {
        $nomeutente = $_SESSION['nomeutente'];
        $idutente = $_SESSION['idutente'];
        $change = $_POST["changemode"];
        $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");

        // CONTROLLO SE IL nomeutente E' REALMENTE ASSOCIATO ALL'idutente
        $query = "SELECT idutente, darkmode FROM utenti WHERE nomeutente = '$nomeutente'";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $idut = $result["idutente"];
        $dm = $result["darkmode"];
        if($idut != $idutente)
            header("location: ../index.php?feedback=0");
        else {
            if($dm)
                $query = "UPDATE utenti SET darkmode = b'0' WHERE idutente = $idutente";
            else if (!$dm)
                $query = "UPDATE utenti SET darkmode = b'1' WHERE idutente = $idutente";
            $result = mysqli_query($con, $query);
                header("location: " . $_SERVER['HTTP_REFERER']);
        }
    }
?>
