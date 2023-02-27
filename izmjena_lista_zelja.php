<!doctype html>
<html>
	<head>
		<title>Prikaz igara</title>
		<meta charset="utf-8" />
	</head>
	<body>
		<main>
			<header>
				<h1>Dodavanje/brisanje igre u listu želja</h1>
			</header>
			<section>
				<?php
					$veza = mysqli_connect("localhost","root","", "mydb");
                    if (!$veza)
                    {
                        die("Greška kod povezivanja na bazu podataka: " . mysql_error());
                    }

                    $poruka = "";
                    $ids_igre = array();

                    if(empty($_POST['dodavanje_lista_zelja']))
                        $poruka .= "Nijedna igra nije odabrana.<br>";
                    
                    if (empty($_POST['operacija']))
                        $poruka .= "Odaberite operaciju!<br>";
                    else
                        $operacija = $_POST['operacija'];
					
					if (empty($poruka))
					{
                        foreach ($_POST['dodavanje_lista_zelja'] as $opcija)
                        {
                            //echo $opcija."\n";
                            $polje = explode(':', $opcija);
                            //echo $polje[0]." ".$polje[1]."\n";
                            array_push($ids_igre, strip_tags($polje[1]));

                        } 

                        if ($operacija === 'dodaj')
                        {
                            $unos = "insert into lista_zelja values ";
                            foreach ($ids_igre as $indeks => $val)
                            {
                                $unos .= "('$val', 1, now(), 0)";
                                if ($indeks !== array_key_last($ids_igre))
                                    $unos .= ", ";
                            }
                        }
                        else if ($operacija === 'brisi')
                        {
                            $unos = "delete from lista_zelja where id_korisnici = 1 and (";
                            foreach ($ids_igre as $indeks => $val)
                            {
                                $unos .= "id_igre = $val";
                                if ($indeks !== array_key_last($ids_igre))
                                    $unos .= " or ";
                                else 
                                    $unos .= ");";
                            }
                        }
                        
                        try 
                        {
                            $rezultat_unos = mysqli_execute_query($veza, $unos);
                            if ($rezultat_unos)
                                switch ($operacija)
                                {
                                    case "dodaj":
                                        $poruka .= "Uspješno dodavanje na listu želja.";
                                        break;
                                    case "brisi":
                                        $poruka .= "Uspješno brisanje s liste želja.";
                                        break;
                                }
                        }
                        catch (Exception $e)
                        {
                            if (mysqli_errno($veza) == 1062)
                                $poruka .= "Ova igra je već u listi igara ili je već kupljena!";
                            else 
                                $poruka .= $veza->error;
                        }
						
			
					}

                    mysqli_close($veza);

				?>
				<!-- ispis vrijednosti varijable $poruka -->
				<p><?php echo $poruka; ?></p>
				<input type="button" value="Natrag" onclick="location.href='forma_lista_zelja.php'" />
			</section>
		</main>
	</body>
</html>