<?php
    session_start();
    if(isset($_SESSION['nomeutente']) && isset($_SESSION['idutente']))
        header("location: ./home/");
    else {
        session_unset();
        session_destroy();
    }
?>
<!DOCTYPE html>
<html lang="it" dir="ltr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1" />
        <link rel="stylesheet" type="text/css" href="./css/styledarkmode.css" />
        <link href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css?family=Josefin+Slab&display=swap" rel="stylesheet" />
        <title>henemyx</title>
    </head>
    <body>
        <header>
            <h1><bold>henemyx</bold></h1>
        </header>

        <form id="formaccesso" action="./controllo/accesso.php" method="post">
            <div class="inpCont">
                <input name="nomeutente" class="inp" type="text" autocomplete="off"
                       value="<?php if(isset($_GET["nut"])) echo $_GET["nut"];?>"
                       maxlength="15" required/>
                <label for="nomeutente">inserisci il tuo nome utente</label>
                <span></span>
            </div>
            <br /><br />
            <div class="inpCont">
                <input name="pwd" class="inp" type="password" autocomplete="off" required/>
                <label for="pwd">inserisci la tua password</label>
                <span></span>
            </div>
            <input name="azione" id="log" type="submit" value="login"/>
            <input name="azione" id="reg" type="submit" value="registrazione"/>
        </form>

        <!-- Gestione messaggi di errore o note -->
        <?php
            if(isset($_GET["feedback"])) {
                if($_GET["feedback"] == 0)
                    echo "<div id='feedback'>hai fatto qualcosa che non dovevi?</div>";
                else if($_GET["feedback"] == 1)
                    echo "<div id='feedback'>nome utente già utilizzato!</div>";
                else if($_GET["feedback"] == 2)
                    echo "<div id='feedback'>nome utente inesistente!</div>";
                else if($_GET["feedback"] == 3)
                    echo "<div id='feedback'>password errata!</div>";
                else if($_GET["feedback"] == 4)
                    echo "<div id='feedback'>a presto!</div>";
                else if($_GET["feedback"] == 5)
                    echo "<div id='feedback'>registrazione avvenuta con successo! ora accedi su <i>henemyx</i> !</div>";
            }
        ?>

        <footer>
            <p id="credits">website developed by Daniele Piano</p>
        </footer>
    </body>
</html>
