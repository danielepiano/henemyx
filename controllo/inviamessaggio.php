<?php
    session_start();

    include '../hillcipher/hill.php';
    header('Content-Type: text/html; charset=utf-8');

    $nomeutente = $_SESSION['nomeutente'];
    $idutente = $_SESSION['idutente'];
    $messaggio = file_get_contents("php://input");
    $idchat = $_SESSION['idchat'];
    $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");
    //$result = mysqli_query($con, "SET NAMES utf8");
    //mysqli_set_charset($con, "utf8");

    // CONTROLLO SE IL nomeutente E' REALMENTE ASSOCIATO ALL'idutente
    $query = "SELECT idutente FROM utenti WHERE nomeutente = '$nomeutente'";
    $result = mysqli_query($con, $query);
    $result = mysqli_fetch_array($result);
    $idut = $result["idutente"];

    if($idut != $idutente)
        header("location: ../index.php?feedback=0");
    else {
        if($messaggio != "") {
            // SELEZIONO LA CHIAVE ASSOCIATA ALLA CHAT idchat
            $query = "SELECT chiavecifratura FROM chat WHERE idchat = $idchat";
            $result = mysqli_query($con, $query);
            $result = mysqli_fetch_array($result);
            $chiaveStringa = $result["chiavecifratura"];
            // CIFRATURA MESSAGGIO SECONDO CIFRARIO DI HILL
            $cifrato = mysqli_escape_string($con, cifrarioHill($messaggio, $chiaveStringa, 0));
            // SALVATAGGIO MESSAGGIO NEL DATABASE
            $query = "INSERT INTO messaggi (idchat, idmittente, corpo, dataora, confermalettura)
                        VALUES ($idchat, $idutente, '$cifrato', (select @now := now()), 0)";
            $result = mysqli_query($con, $query);
        }
        $_SESSION['nomeutente'] = $nomeutente;
        $_SESSION['idutente'] = $idutente;
        $_POST['aprichat'] = $idchat;
    }
?>
