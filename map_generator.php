<?php
$poziom = $_SESSION['poziom']*10;

//generowanie pustej mapy
for($x=1;$x<($poziom+1);$x++) 
{
	for($y=1;$y<($poziom+1);$y++) 
	{
        $conn->query("INSERT INTO mapa (identyfikator, x, y) VALUES ('".$_SESSION['identyfikator']."', '".$x."', '".$y."')");
    }
}

//generowanie wody na mapie
for($i=1;$i<($poziom+1);$i++)
{
	$x = rand(3,$poziom);
	$y = rand(3,$poziom);
	$conn->query("UPDATE mapa SET type='1' WHERE x='".$x."' AND y='".$y."' AND identyfikator='".$_SESSION['identyfikator']."'");
} 

//generowanie energii na mapie
for($i=1;$i<($poziom+1);$i++)
{
	$x = rand(3,$poziom);
	$y = rand(3,$poziom);
	$conn->query("UPDATE mapa SET type='2' WHERE x='".$x."' AND y='".$y."' AND identyfikator='".$_SESSION['identyfikator']."'");
}

//generowanie domu
$x = rand(5,$poziom);
$y = rand(5,$poziom);
$conn->query("UPDATE mapa SET type='3' WHERE x='".$x."' AND y='".$y."' AND identyfikator='".$_SESSION['identyfikator']."'");

?>

