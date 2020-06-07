<?php
    session_start();
    
    header('Content-Type: text/html; charset=utf-8');

    include '../hillcipher/hill.php';

    if(!isset($_SESSION['nomeutente']) || !isset($_SESSION['idutente']) || !isset($_SESSION['idchat'])) {
        header("location: ../index.php?feedback=0");
    }
    else {
        $nomeutente = $_SESSION['nomeutente'];
        $idutente = $_SESSION['idutente'];
        $idchat = $_SESSION['idchat'];

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
                        AND ch.idchat = $idchat AND (idutente1 = $idutente OR idutente2 = $idutente)";
            $result = mysqli_query($con, $query);
            if(mysqli_num_rows($result) == 0)
                header("location: ../index.php?feedback=0");
            else {
                // PRENDO IL NOME DELL'ALTRO UTENTE
                $result = mysqli_fetch_array($result);

                if($result["idutente1"] == $idutente) {
                    $altroutente = $result["nomeutente2"];
                    $idaltroutente = $result["idutente2"];
                }
                else if($result["idutente2"] == $idutente) {
                    $altroutente = $result["nomeutente1"];
                    $idaltroutente = $result["idutente1"];
                }

                // CONTROLLO SE HA LA PREFERENZA SU DARKMODE O MENO
                $query = "SELECT darkmode FROM utenti WHERE idutente = $idutente";
                $result = mysqli_query($con, $query);
                $result = mysqli_fetch_array($result);
                $isDark = $result["darkmode"];
?>
<!DOCTYPE html>
<html lang="it" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
        <?php
            if($isDark)
                echo "<link rel='stylesheet' type='text/css' href='../css/styledarkmode.css' />";
            else if(!$isDark)
                echo "<link rel='stylesheet' type='text/css' href='../css/stylelightmode.css' />";
        ?>
        <link href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Josefin+Slab&display=swap" rel="stylesheet">
        <title>henemyx</title>
    </head>
    <body>
        <header>
            <h1><bold>henemyx</bold></h1>
        </header>

        <a id="goback" href="../home/"></a>

        <div id="changemode">
            <form action="../controllo/changemode.php" method="post">
                <input name='changemode' type='submit' value=
                    <?php
                        $link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        echo "'$link'";
                    ?>/>
            </form>
        </div>

        <div id="divoscuramento"></div>

        <div id="griglia">

            <div id="chatcorrenticont">
                <form id="chatcorrenti" action="../controllo/aprichat.php" method="post" onload="aggiornaChatCorrenti()">
                </form>
                <script>
                    var aggCC = setInterval(aggiornaChatCorrenti, 500);

                    function aggiornaChatCorrenti() {
                        var xhttp = new XMLHttpRequest();
                        var a = document.getElementById("chatcorrenti").innerHTML;
                        xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                            	var b = this.responseText.trim();
                            	if(a != b) {
                                	document.getElementById("chatcorrenti").innerHTML = b;
                                }
                            }
                        };
                        xhttp.open("POST", "../controllo/aggiornachatcorrenti.php", true);
                        xhttp.send();
                    }
                </script>
                <h1>chat correnti</h1>
            </div>

            <div id="chatcont">
                <div id="messaggi" onload="aggiornaMessaggi()">

                    <script>
                        var x = 0;
                        var i = 0;
                        var r = 0;
                        var aggMex = setInterval(aggiornaMessaggi, 500);
                        function aggiornaMessaggi() {
                            var xhttp = new XMLHttpRequest();
                            var objDiv = document.getElementById("messaggi");
                            if(objDiv.scrollTop + objDiv.offsetHeight == objDiv.scrollHeight)
                                r = 1;
                            xhttp.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    document.getElementById("messaggi").innerHTML = this.responseText;
                                    if(x == 0 || i == 1 || r == 1) {
                                        objDiv.scrollTop = objDiv.scrollHeight;
                                        x = 1;
                                        r = 0;
                                        i = 0;
                                    }
                                }
                            };
                            xhttp.open("POST", "../controllo/aggiornamessaggi.php", true);
                            xhttp.send();
                        }

                        function inviaMessaggio() {
                            var xhttp = new XMLHttpRequest();
                            xhttp.onreadystatechange = function() {
                                if (this.readyState == 4 && this.status == 200) {
                                    i = 1;
                                    aggiornaMessaggi();
                                }
                            };
                            xhttp.open("POST", "../controllo/inviamessaggio.php", true);
                            xhttp.send(document.getElementById("messaggioTA").value);
                            document.getElementById("messaggioTA").value = "";
                            document.getElementById("messaggioTA").focus();
                        }

                        window.onload=function () {     // SPOSTA IN BASSO LA BARRA DI SCORRIMENTO PER I MESSAGGI
                            var objDiv = document.getElementById("messaggi");
                            objDiv.scrollTop = objDiv.scrollHeight;
                        }
                    </script>
                </div>
                <div id="areainvio">
                    <textarea name="messaggio" id="messaggioTA" type="text" autofocus></textarea>
                    <button onclick="inviaMessaggio()"></button>
                </div>
                <h1><?php echo $altroutente?></h1>
            </div>

        </div>

        <div id="popupchiudi">
            <form action="../controllo/chiudisessione.php">
                <label>sei sicuro di voler terminare la sessione?</label><br />
                <button id="sis" type="submit">sì, termina qui</button><br /><br />
                <button id="non" type="button" onclick="closePopUp()">no, resta collegato</button><br /><br />
            </form>
        </div>

        <div id="chiudisessione" onclick="openPopUp()">logout</div>
        <script>
            function openPopUp() {
                document.getElementById("popupchiudi").style.top = "50%";
                document.getElementById("popupchiudi").style.transform = "translateX(-50%) translateY(-50%)";
                document.getElementById("divoscuramento").style.opacity = "0.7";
                document.getElementById("divoscuramento").style.zIndex = "250";
            }
            function closePopUp() {
                document.getElementById("popupchiudi").style.top = "-100%";
                document.getElementById("popupchiudi").style.transform = "translateX(-50%)";
                document.getElementById("divoscuramento").style.opacity = "0";
                document.getElementById("divoscuramento").style.zIndex = "-250";
            }
        </script>

        <footer>
            <?php echo "<p id='dati'>$nomeutente #$idutente</p>";?>
            <p id="credits">website developed by Daniele Piano</p>
        </footer>
    </body>
</html>
<?php } } }?>
