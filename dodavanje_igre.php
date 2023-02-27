<!doctype html>
<html>
	<head>
		<title>Dodavanje igre</title>
		<meta charset="utf-8" />
	</head>
	<body>
		<main>
			<header>
				<h1>Dodavanje nove igre u sustav</h1>
			</header>
			<section>
    <?php
    $veza = mysqli_connect("localhost","root","", "mydb");
    if (!$veza)
    {
        die("Greška kod povezivanja na bazu podataka: " . mysql_error());
    }

    $poruka = "";
    

    if (empty($_POST['naslov']) || empty($_POST['platforma']) || 
        empty($_POST['datum_izlaska']) || empty($_POST['developer']) || 
        empty($_POST['fransiza']) || empty($_POST['zanrovi']))
            
        $poruka .= "Nisu unijete/odabrane sve opcije!<br>";

    
    if (empty($poruka))
    {
        $naslov = strip_tags($_POST['naslov']);
        $datum_izlaska = strip_tags($_POST['datum_izlaska']);
        $developer = strip_tags($_POST['developer']);
        $fransiza = strip_tags($_POST['fransiza']);
        $zanrovi = array();
        $platforma = array();

        if ($fransiza === '--') $fransiza = 'null';
        echo "<br>ID odabrane franšize: ".$fransiza."<br>";

        foreach($_POST['platforma'] as $pl)
        {
            array_push($platforma, strip_tags($pl));
        }


        $unos = "insert into igre values (default, '$naslov', '$datum_izlaska',
            '$developer', $fransiza);";

        $rezultat_unos = mysqli_execute_query($veza, $unos);

        foreach ($_POST['zanrovi'] as $opcija)
        {
            $polje = explode(':', $opcija);
            array_push($zanrovi, strip_tags($polje[1]));

        } 

        $id_igre = "";
        $rezultat = mysqli_execute_query($veza, 'select * from igre order by id_igre desc limit 1'); 
        foreach ($rezultat as $red) 
            $id_igre = $red["id_igre"];

        echo "<br>ID igre: ".$id_igre."<br>";

        $unos = "insert into zanrovi_igre values ";
        foreach ($zanrovi as $indeks => $val)
        {
            $unos .= "('$val', $id_igre)";
            if ($indeks !== array_key_last($zanrovi))
                $unos .= ", ";
        }
        echo "<br>SQL za tablicu zanrovi_igre: ".$unos."<br>";
        $rezultat_unos = mysqli_execute_query($veza, $unos);

        $unos = "insert into platforme_igre values ";
        foreach ($platforma as $indeks => $val)
        {
            $unos .= "('$val', $id_igre, NULL)";
            if ($indeks !== array_key_last($platforma))
                $unos .= ", ";
        }
        echo "<br>SQL za tablicu platforme_igre: ".$unos."<br>";
        $rezultat_unos = mysqli_execute_query($veza, $unos); 

    }


    mysqli_close($veza);

?>

<p><?php echo $poruka; ?></p>
				<input type="button" value="Natrag" onclick="location.href='forma_dodavanje_igre.php'" />
			</section>
		</main>
	</body>
</html>