<!DOCTYPE html>
<html>
    <head>
        <title>Forma za dodavanje nove igre u sustav</title>
        <style>
            body {
                text-align: center;
                background-color: cornflowerblue;
                font-size: medium;
            }
            [type="submit"] {
                background-color: blanchedalmond;
                border: none;
                padding: 10px;
                text-align: center;
                cursor: pointer;
            }
            table {
                width: fit-content; 
                margin: auto;
            }
        </style>
    </head>
    <body>
        <h2>Forma za dodavanje nove igre u sustav</h2>
        <?php
            $veza = mysqli_connect("localhost","root","", "mydb");
            if (!$veza)
            {
                die("Greška kod povezivanja na bazu podataka: " . mysql_error());
            }    

            class Zanr
            {
                public $id_zanrovi;
                public $naziv;

                public function __construct($id_zanrovi, $naziv) 
                {
                    $this->id_zanrovi = $id_zanrovi;
                    $this->naziv = $naziv;
                }
            }

            class Igra
            {
                public $id_igre;
                public $naslov;
                public $developer;
                public $datum_izlaska;
                public $fransiza;

                public function __construct($id_igre, $naslov, $developer, $datum_izlaska, $fransiza) 
                {
                    $this->id_igre = $id_igre;
                    $this->naslov = $naslov;
                    $this->developer = $developer;
                    $this->datum_izlaska = $datum_izlaska;
                    $this->fransiza = $fransiza;
                }
            }

            $zanrovi = array();

            $upit = "select * from zanrovi";
            $rezultat = mysqli_execute_query($veza, $upit);
            foreach ($rezultat as $red) 
            {
                array_push($zanrovi, new Zanr($red["id_zanrovi"], $red["naziv"]));
            }

            $igre = array();

            $upit = "select * from igre";
            $rezultat = mysqli_execute_query($veza, $upit);
            foreach ($rezultat as $red) 
            {
                array_push($igre, new Igra($red["id_igre"], $red["naslov"], $red["developer"], $red["datum_izlaska"], $red['fransiza']));
            }
            
            //složeni upiti
            $upit = 'SELECT ig2.naslov AS "Igra", ig1.naslov AS "Direktni nastavak" FROM igre ig1 JOIN prethodnice p ON ig1.id_igre = p.id_igre JOIN igre ig2 ON p.id_prethodnice = ig2.id_igre';

            $rezultat = mysqli_execute_query($veza, $upit);
            $prikaz = "<table border='1'>";
            $prikaz .= "<tr><th>Igra</th><th>Direktni nastavak</th></tr>";
            foreach ($rezultat as $red)
            {
                $prikaz .= "<tr>";
                $prikaz .= "<td>".$red["Igra"]."</td>";
                $prikaz .= "<td>".$red["Direktni nastavak"]."</td>";
                $prikaz .= "</tr>";
            }
            $prikaz .= "</table><br>";


            $upit = 'SELECT z.naziv AS "Žanr s trenutno najviše igara", COUNT(*) AS "Broj igara u žanru" 
            FROM zanrovi z JOIN zanrovi_igre zi ON z.id_zanrovi = zi.id_zanrovi
            JOIN igre i ON zi.id_igre = i.id_igre GROUP BY z.naziv ORDER BY COUNT(*) DESC LIMIT 1;';

            $rezultat = mysqli_execute_query($veza, $upit);
            $prikaz2 = "<table border='1'>";
            $prikaz2 .= "<tr><th>Žanr s trenutno najviše igara</th></tr>";
            foreach ($rezultat as $red)
            {
                $prikaz2 .= "<tr>";
                $prikaz2 .= "<td>".$red["Žanr s trenutno najviše igara"]."</td>";
                $prikaz2 .= "</tr>";
            }
            $prikaz2 .= "</table><br>";



            $upit = 'SELECT p.naziv AS "Najpopularnija platforma" 
            FROM platforme p JOIN platforme_igre pi ON p.id_platforme = pi.id_platforme
            JOIN igre i ON pi.id_igre = i.id_igre GROUP BY p.naziv ORDER BY COUNT(*) DESC LIMIT 1;';

            $rezultat = mysqli_execute_query($veza, $upit);
            $prikaz3 = "<table border='1'>";
            $prikaz3 .= "<tr><th>Najpopularnija platforma</th></tr>";
            foreach ($rezultat as $red)
            {
                $prikaz3 .= "<tr>";
                $prikaz3 .= "<td>".$red["Najpopularnija platforma"]."</td>";
                $prikaz3 .= "</tr>";
            }
            $prikaz3 .= "</table><br>";



            $upit = 'DROP VIEW IF EXISTS Ekskluzive;';
            $rezultat = mysqli_execute_query($veza, $upit);
            $upit = 'CREATE VIEW Ekskluzive AS
                SELECT pi.id_igre, COUNT(*) AS "broj" 
                FROM platforme p JOIN platforme_igre pi ON p.id_platforme = pi.id_platforme
                JOIN igre i ON pi.id_igre = i.id_igre 
                GROUP BY pi.id_igre HAVING broj = 1;';
            $rezultat = mysqli_execute_query($veza, $upit);
            $upit = 'SELECT i.naslov, p.naziv FROM Ekskluzive e 
                JOIN igre i ON i.id_igre = e.id_igre 
                JOIN platforme_igre pi ON pi.id_igre = i.id_igre 
                JOIN platforme p ON pi.id_platforme = p.id_platforme;';
            $rezultat = mysqli_execute_query($veza, $upit);

            $prikaz4 = "<table border='1'>";
            $prikaz4 .= "<tr><th>Ekskluzive</th><th>Platforma</th></tr>";
            foreach ($rezultat as $red)
            {
                $prikaz4 .= "<tr>";
                $prikaz4 .= "<td>".$red["naslov"]."</td>";
                $prikaz4 .= "<td>".$red["naziv"]."</td>";
                $prikaz4 .= "</tr>";
            }
            $prikaz4 .= "</table><br>";

            mysqli_close($veza);

        ?>
        <form action="/dodavanje_igre.php" method="POST">
            <label for="naslov"><strong>Naslov igre:</strong></label><br>
            <input type="text" id="naslov" name="naslov"><br><br>

            <label for="datum_izlaska"><strong>Datum izlaska:</strong></label><br>
            <input type="date" id="datum_izlaska" name="datum_izlaska"><br><br>

            <label for="developer"><strong>Developer:</strong></label><br>
            <input type="text" id="developer" name="developer"><br><br>

            <label><strong>Platforma:</strong></label><br>
            <input type="checkbox" id="platforma_pc" name="platforma[]" value="1">
            <label for="platforma_pc">PC</label><br>
            <input type="checkbox" id="platforma_playstation" name="platforma[]" value="2">
            <label for="platforma_playstation">Playstation</label><br>
            <input type="checkbox" id="platforma_xbox" name="platforma[]" value="3">
            <label for="platforma_xbox">Xbox</label><br>
            <input type="checkbox" id="platforma_nintendo" name="platforma[]" value="4">
            <label for="platforma_nintendo">Nintendo</label><br>
            <input type="checkbox" id="platforma_mobilna" name="platforma[]" value="5">
            <label for="platforma_mobilna">Mobilna</label><br><br>

            <label for="zanrovi"><strong>Žanrovi:</strong></label>
            <select multiple name="zanrovi[]" id="zanrovi">
                <?php
                    foreach ($zanrovi as $z)
                    {
                        printf('<option value="%s:%s">%s</option>', $z->naziv, $z->id_zanrovi, $z->naziv);
                    }
                ?>
            </select><br><br>

            <label for="fransiza"><strong>Franšiza:</strong></label>
            <select name="fransiza" id="fransiza">
                <?php
                    printf('<option value="--">--</option>');
                    foreach ($igre as $i)
                    {
                        if (is_null($i->fransiza))
                            printf('<option value="%s">%s</option>', $i->id_igre, $i->naslov);
                    }
                ?>            
            </select><br><br>

            <input type="submit" value="Submit">
        </form> 
        <div>
            <h3>Složeni upiti</h3>
            <?php
                echo $prikaz;
            ?>
            <?php
                echo $prikaz2;
            ?>
            <?php
                echo $prikaz3;
            ?>
            <?php
                echo $prikaz4;
            ?>
        </div>
    </body>
</html>
