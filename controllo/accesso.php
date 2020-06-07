<?php
    include '../hillcipher/hill.php';
	header('Content-Type: text/html; charset=utf-8');
    
    $nomeutente = $_POST["nomeutente"];
    $pwd = $_POST["pwd"];
    $azione = $_POST["azione"];

    if(empty($azione) || empty($nomeutente) || empty($pwd))
        header("location: ./index.php");
    else {
        $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");
        $pwd = md5($nomeutente . $pwd);

        if($azione == "login") {
            // CONTROLLA SE E' ESISTE QUEL NOME UTENTE
            $query = "SELECT * FROM utenti WHERE nomeutente = '$nomeutente'";
            $result = mysqli_query($con, $query);

            // IN QUEL CASO AVVERTI CHE NON ESISTE ALCUN UTENTE CON QUEL NOME
            if(mysqli_num_rows($result) == 0) {
                mysqli_close($con);
                header("location: ../index.php?feedback=2");
            }
            else { // MENTRE SE ESISTE...
                // ... CONTROLLA LA PASSWORD
                $query = "SELECT pwd FROM utenti WHERE nomeutente = '$nomeutente'";
                $result = mysqli_query($con, $query);
                $result = mysqli_fetch_array($result);
                $pwdDB = $result["pwd"];
                // SE E' SBAGLIATA AVVERTI CHE LA PASSWORD E' SBAGLIATA
                if($pwd != $pwdDB) {
                    echo $pwd . " _ " . $pwdDB;
                    mysqli_close($con);
                    header("location: ../index.php?feedback=3&nut=$nomeutente");
                }
                else { // ALTRIMENTI ACCEDI ALLE CHAT
                    session_start();
                    // OTTIENI L'ID ASSOCIATO AL NOME UTENTE E SALVALO IN SESSIONE INSIEME AL NOME UTENTE
                    $query = "SELECT idutente FROM utenti WHERE nomeutente = '$nomeutente'";
                    $result = mysqli_query($con, $query);
                    $result = mysqli_fetch_array($result);
                    $_SESSION["idutente"] = $result["idutente"];
                    $_SESSION["nomeutente"] = $nomeutente;

                    mysqli_close($con);
                    header("location: ../home/");
                }
            }
        }
        else if($azione == "registrazione") {
            // CONTROLLA SE E' GIA' USATO QUEL NOME UTENTE
            $query = "SELECT * FROM utenti WHERE nomeutente = '$nomeutente'";
            $result = mysqli_query($con, $query);

            // IN CASO CONTRARIO...
            if(mysqli_num_rows($result) == 0) {
                // CREA L'ACCOUNT
                $query = "INSERT INTO utenti (nomeutente, pwd, darkmode) VALUES ('$nomeutente', '$pwd', 1)";
                $ins = mysqli_query($con, $query);

                // OTTIENI L'ID ASSOCIATO AL NOME UTENTE
                $query = "SELECT idutente FROM utenti WHERE nomeutente = '$nomeutente'";
                $result = mysqli_query($con, $query);
                $result = mysqli_fetch_array($result);
                $idutente = $result["idutente"];

                // SELEZIONA OGNI UTENTE REGISTRATO SU NYX E...
                $query = "SELECT idutente FROM utenti WHERE nomeutente != '$nomeutente'";
                $result = mysqli_query($con, $query);
                while($ut = mysqli_fetch_array($result)) {
                    $idut = $ut["idutente"];
                    // ... GENERA UNA CHIAVE PER CIFRARE I MESSAGGI CON QUESTO UTENTE
                    $chiave = mysqli_escape_string($con, getChiave());
                    echo "<br /> >'$chiave' <br />";
                    // ... CREA UNA CHAT TRA QUESTO E IL NUOVO UTENTE
                    $query = "INSERT INTO chat (idutente1, idutente2, chiavecifratura) VALUES ('$idutente', '$idut', '$chiave')";
                    $ins = mysqli_query($con, $query);
                }

                mysqli_close($con);
                header("location: ../index.php?feedback=5"); // E ACCEDI ALLE CHAT
            }
            else { // ALTRIMENTI AVVERTI CHE IL NOME UTENTE E' GIA' UTILIZZATO
                mysqli_close($con);
                header("location: ../index.php?feedback=1");
            }
        }
    }
?>
