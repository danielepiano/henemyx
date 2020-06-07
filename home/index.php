<?php
    session_start();
    if(!isset($_SESSION['nomeutente']) || !isset($_SESSION['idutente']))
        header("location: ../index.php?feedback=0");
    else {
        $nomeutente = $_SESSION['nomeutente'];
        $idutente = $_SESSION['idutente'];
        $con = mysqli_connect("127.0.0.1", "root", "", "my_henemyx") or die("Errore nella connessione.");

        // CONTROLLO SE IL nomeutente E' REALMENTE ASSOCIATO ALL'idutente
        /*$query = "SELECT idutente FROM utenti WHERE nomeutente = '$nomeutente'";
        $result = mysqli_query($con, $query);
        $result = mysqli_fetch_array($result);
        $idut = $result["idutente"];
        if($idut != $idutente)
            header("location: ../index.php?feedback=0");
        else {*/
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

            <div id="cercachatcont">
                <form action="#" method="get" id="headcerca">
                    <input name="ricerca" id="barraricerca" type="text" autocomplete="off" onload="cercaChat()"
                    onkeyup="cercaChat()" oncut="cercaChat()" onpaste="cercaChat()" value="" required/>
                    <label for="ricerca"><h1>cerca. . .</h1></label>
                    <span></span>
                </form>

                <form id="cercachat" action="../controllo/aprichat.php" method="post">
                    <div class='didascalia' id='search'><p>cerca un contatto</p></div>
                </form>

                <script>
                    var post = 0;
                    function cercaChat() {
                        var xhttp = new XMLHttpRequest();
                        xhttp.onreadystatechange = function() {
                            if (this.readyState == 4 && this.status == 200) {
                                document.getElementById("cercachat").innerHTML = this.responseText;
                            }
                        };
                        xhttp.open("POST", "../controllo/cercachat.php", true);
                        xhttp.send(document.getElementById("barraricerca").value);
                    }
                </script>

            </div>

            <div id="scopricont">
                <form id="scopri" action="../controllo/aprichat.php" method="post">
                    <?php
                        $query =   "SELECT ch.idchat, u1.nomeutente AS nomeutente1, u2.nomeutente AS nomeutente2
                                    FROM chat ch, utenti u1, utenti u2
                                    WHERE u1.idutente = ch.idutente1
                                      AND u2.idutente = ch.idutente2
                                      AND (u1.idutente = $idutente OR u2.idutente = $idutente)
                                      AND ch.idchat NOT IN (
                                          SELECT c.idchat
                                          FROM chat c, messaggi m
                                          WHERE m.idchat = c.idchat
                                          GROUP BY c.idchat
                                          HAVING COUNT(*) > 0)
                                    ORDER BY nomeutente1, nomeutente2";
                        $result = mysqli_query($con, $query);

                        if(mysqli_num_rows($result) == 0)
                            echo "<div class='didascalia' id='newchat'><p>nessun nuovo utente</p></div>";

                        while($chatt = mysqli_fetch_array($result)) {
                            if($chatt['nomeutente1'] == $nomeutente)
                                $utenteds = $chatt['nomeutente2'];
                            else
                                $utenteds = $chatt['nomeutente1']; // ds -> da scoprire
                            $idchatds = $chatt['idchat'];
                            echo "<div class='utenteCont'>
                                    <p class='info'>$utenteds</p>
                                    <input id='aprichat' name='aprichat' class='mex' type='submit' value='$idchatds'/>
                                    <label for='aprichat'>inizia a messaggiare!</label>
                                    <span></span>
                                </div>";
                        }
                    ?>
                </form>
                <h1>scopri anche. . .</h1>
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
<?php } }?>
