<!DOCTYPE html>
<html>
    <head>
        <title>Forma za prikaz i upravljanje vlastitim igrama</title>
        <style>
            body {
                text-align: center;
                background-color: aquamarine;
                font-size: medium;
            }
            [type="submit"] {
                background-color: blanchedalmond;
                border: none;
                padding: 10px;
                text-align: center;
                cursor: pointer;
            }
        </style>
    </head>
    <body>
        <h2>Forma za prikaz i upravljanje vlastitim igrama</h2>

        <?php
        
            $veza = mysqli_connect("localhost","root","", "mydb");
            if (!$veza)
            {
                die("Greška kod povezivanja na bazu podataka: " . mysql_error());
            }    

            class Igra
            {
                public $id_igre;
                public $naslov;
                public $developer;
                public $datum_izlaska;

                public function __construct($id_igre, $naslov, $developer, $datum_izlaska) 
                {
                    $this->id_igre = $id_igre;
                    $this->naslov = $naslov;
                    $this->developer = $developer;
                    $this->datum_izlaska = $datum_izlaska;
                }
            }

            class Stavka_liste
            {
                public $naslov;
                public $korime;
                public $vrijeme_dodavanja;
                public $kupljena;

                public function __construct($naslov, $korime, $vrijeme_dodavanja, $kupljena) 
                {
                    $this->naslov = $naslov;
                    $this->korime = $korime;
                    $this->vrijeme_dodavanja = $vrijeme_dodavanja;
                    $this->kupljena = $kupljena;
                }
            }

            class Stavka_kataloga
            {
                public $naslov;
                public $korime;
                public $vrijeme_kupnje;

                public function __construct($naslov, $korime, $vrijeme_kupnje) 
                {
                    $this->naslov = $naslov;
                    $this->korime = $korime;
                    $this->vrijeme_kupnje = $vrijeme_kupnje;
                }
            }

            $igre = array();

            $upit = "select * from igre";
            $rezultat = mysqli_execute_query($veza, $upit);
            foreach ($rezultat as $red) 
            {
                //printf("<p>%s (%s)</p>", $red["naslov"], $red["developer"]);
                //echo "<p>".$red[1]." ".$red[2]."</p>";
                array_push($igre, new Igra($red["id_igre"], $red["naslov"], $red["developer"], $red["datum_izlaska"]));
            }

            /*foreach ($igre as $igra) 
            {
                echo 'Naslov: ' . $igra->naslov . ' ' . $igra->developer . ' ' . $igra->datum_izlaska . "<br>";
            }*/

            $lista_zelja = array();

            $upit = "select * from lista_zelja lz JOIN korisnici k ON lz.id_korisnici = k.id_korisnici
                JOIN igre i ON lz.id_igre = i.id_igre";
            $rezultat = mysqli_execute_query($veza, $upit);
            foreach ($rezultat as $red) 
            {
                //printf("<p>%s (%s)</p>", $red["naslov"], $red["developer"]);
                //echo "<p>".$red[1]." ".$red[2]."</p>";
                array_push($lista_zelja, new Stavka_liste($red["naslov"], $red["korime"], $red["vrijeme_dodavanja"], $red["kupljena"]));
            }

            $katalog = array();

            $upit = "select * from katalog_igara ki JOIN korisnici k ON ki.id_korisnici = k.id_korisnici
                JOIN igre i ON ki.id_igre = i.id_igre";
            $rezultat = mysqli_execute_query($veza, $upit);
            foreach ($rezultat as $red) 
            {
                //printf("<p>%s (%s)</p>", $red["naslov"], $red["developer"]);
                //echo "<p>".$red[1]." ".$red[2]."</p>";
                array_push($katalog, new Stavka_kataloga($red["naslov"], $red["korime"], $red["vrijeme_kupnje"]));
            }

            mysqli_close($veza);
        ?>

        <p><strong>Sve igre:</strong></p>
        <ul>
            <?php
                foreach ($igre as $igra)
                {
                    printf('<li>%s</li>', $igra->naslov);
                }
            ?>
        </ul><br><br>

        <p><strong>Moja lista želja:</strong></p>
            <?php
                printf('<ul name="lista_zelja" id="lista_zelja">');
                printf('<p>Korisnik: %s </p>', $lista_zelja[0]->korime);

                foreach ($lista_zelja as $l)
                {
                    if (!$l->kupljena)
                        printf('<li>Naslov: %s, vrijeme dodavanja: %s</li>', $l->naslov, $l->vrijeme_dodavanja);
                }

                printf('</ul>');
            ?>
        <br><br>

        <p><strong>Moj katalog igara:</strong></p>
            <?php
                printf('<ul name="katalog_igara" id="katalog_igara">');
                printf('<p>Korisnik: %s </p>', $katalog[0]->korime);

                foreach ($katalog as $k)
                {
                    printf('<li>Naslov: %s, vrijeme kupnje: %s</li>', $k->naslov, $k->vrijeme_kupnje);
                }

                printf('</ul>');
            ?>
        </ul><br><br>

        <form action="/izmjena_lista_zelja.php" method="POST">

            <!--ako je igra koju dodajemo u listu zelja izasla, triggeraj
            njezinu kupnju odnosno stavljanje u game library-->
                
            <input type="radio" id="dodaj" name="operacija" value="dodaj">
            <label for="dodaj"> 
                <strong>Dodaj u listu želja / predbilježi kupnju:</strong>
            </label><br>
            <input type="radio" id="brisi" name="operacija" value="brisi">
            <label for="brisi">
                <strong>Obriši iz liste želja:</strong>
            </label><br>
            <select multiple name="dodavanje_lista_zelja[]" id="dodavanje_lista_zelja">
                <?php
                    foreach ($igre as $igra)
                    {
                        printf('<option value="%s:%s">%s</option>', $igra->naslov, $igra->id_igre, $igra->naslov);
                    }
                ?>
            </select><br><br>

            <input type="submit" value="Prenesi">
        </form> 
    </body>
</html>