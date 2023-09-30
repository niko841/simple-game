<?php
session_start();

$conn = new mysqli('localhost', 'root', 'root', 'gra');

if(!isset($_SESSION['poziom']))
{
	if(isset($_GET['p']))
	{
		$_SESSION['poziom'] = $_GET['p'];
		header("Location: index.php");
	}
}
else
{

	if(!isset($_SESSION['identyfikator']))
	{
		$_SESSION['identyfikator'] = bin2hex(random_bytes(20));	
		$_SESSION['energia'] = ($_SESSION['poziom'])*50;
		$conn->query("INSERT IGNORE INTO gracze(id, identyfikator, data) VALUES (NULL, '".$_SESSION['identyfikator']."', '".time()."')");
		$conn->query("INSERT INTO statystyki(id, identyfikator, poziom, wynik, czas_start, czas_stop) VALUES (NULL, '".$_SESSION['identyfikator']."', '".$_SESSION['poziom']."', '0', '".time()."', '0')");
		require_once("map_generator.php");
	}
	else
	{
		$spr_graczaa = $conn->query("SELECT id FROM gracze WHERE identyfikator = '".$_SESSION['identyfikator']."'");
		$spr_gracza = $spr_graczaa->fetch_assoc();
		if($spr_gracza['id'] < 1)
		{
			session_destroy();
			header("Location: index.php");
		}
	}
}
?>

<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Gierka</title>
		<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
		<style>
			.h-100{min-height:100vh;}
			.h-90{min-height:90vh;}
			.h-75{min-height:75vh;}
			.h-50{min-height:50vh;}
			.h-25{min-height:25vh;}
			.map-cel{height:10vh;width:10vh;border:1px solid #eee;}
			*{transition: all .5s ease-in-out;}
			.bi{font-size:52px;cursor:pointer;}
			.map-container{border:5px solid black}
		</style>
	</head>
	
	<body>	
		<?php if(isset($_SESSION['poziom']))
		{
		?>
		<div class="container text-center">
			<div class="row py-3 mb-3">
				<div class="col  mx-auto statystyki"></div>
			</div>
			<div class="row">
				<div class="col-2 mx-auto"><span class="up"><i class="bi bi-arrow-up-circle"></i></span></div>
			</div>
			<div class="row">			
				<div class="col-2 my-auto"><span class="left"><i class="bi bi-arrow-left-circle"></i></span></div>
				<div class="col mapa">
					
				</div>
				<div class="col-2 my-auto"><span class="right"><i class="bi bi-arrow-right-circle"></i></span></div>
			</div>
			<div class="row">	
				<div class="col-2 mx-auto"><span class="down"><i class="bi bi-arrow-down-circle"></i></span></div>
			</div>
		</div>
		<?php
		}
		else
		{
		?>
		<div class="container">
			<div class="row">				
				<div class="col mt-5">
				<h1>Gierka</h1>
				<p>Gierka to prosta gierka, w której chodzisz swoją postacią po planszy. Celem gry jest znalezienie domu.</p>
				<p><a class="btn btn-outline-dark" href="?p=1">Poziom łatwy</a> <a class="btn btn-outline-dark" href="?p=5">Poziom średni</a> <a class="btn btn-outline-dark" href="?p=10">Poziom trudny</a></p>
				</div>
			</div>
		</div>
		<?php
		}
		?>
		<script type="text/javascript">
			function laduj_mape()
			{
				$(".mapa").load("core.php?f=laduj_mape");
			}
			
			function laduj_statystyki()
			{
				$(".statystyki").load("core.php?f=laduj_statystyki");
			}
			
			$(document).ready(function()
			{
				laduj_mape();
				laduj_statystyki();
				
				$('.up').on('click', function(){				
					$.ajax({
						url: "core.php?f=up",
						success:function(){laduj_mape();laduj_statystyki();}
					});
				});
				$('.down').on('click', function(){				
					$.ajax({
						url: "core.php?f=down",
						success:function(){laduj_mape();laduj_statystyki();}
					});
				});
				$('.left').on('click', function(){				
					$.ajax({
						url: "core.php?f=left",
						success:function(){laduj_mape();laduj_statystyki();}
					});
				});
				$('.right').on('click', function(){				
					$.ajax({
						url: "core.php?f=right",
						success:function(){laduj_mape();laduj_statystyki();}
					});
				});
			});	
		</script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
	</body>
</html>