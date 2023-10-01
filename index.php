<?php
session_start();

// statystyki stat.lers.it
$STATS_page_load_time_start = microtime(true);
$stat_lers_page_host = $_SERVER['HTTP_HOST'];
$stat_lers_page_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$stat_lers_page_referer = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "");$stat_lers_page_request_method = $_SERVER['REQUEST_METHOD'];
$stat_lers_client_ip = $_SERVER['REMOTE_ADDR'];$stat_lers_client_browser = $_SERVER['HTTP_USER_AGENT'];$stat_lers_client_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$stat_lers_curl_link_vars = base64_encode($stat_lers_page_host."||X||".$stat_lers_page_url."||X||".$stat_lers_page_referer."||X||".$stat_lers_page_request_method."||X||".
$stat_lers_client_ip."||X||".$stat_lers_client_browser."||X||".$stat_lers_client_language);
$stat_lers_curl_link = "https://stat.lers.it/tracker.php?vars=".$stat_lers_curl_link_vars;
$stat_lers_curl_conn = curl_init();curl_setopt($stat_lers_curl_conn, CURLOPT_URL, $stat_lers_curl_link);curl_exec($stat_lers_curl_conn);
/// statystyki


$conn = new mysqli('localhost', 'srv41169_gra', 'FVvLXcsGwcUv3vKi', 'srv41169_gra');

if(!isset($_SESSION['poziom']))
{
	if(isset($_GET['p']))
	{
		$_SESSION['poziom'] = $_GET['p'];
		header("Location: https://gra.lers.it");
	}
}
else
{

	if(!isset($_SESSION['identyfikator']))
	{
		$_SESSION['identyfikator'] = md5(getenv("REMOTE_ADDR").time().rand(5,100));	
		$_SESSION['energia'] = ($_SESSION['poziom'])*25;
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
			header("Location: https://gra.lers.it");
		}
	}
}
?>

<!doctype html>
<html lang="en" >
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
			.map-cel{border:1px solid #eee;height:8vw !important;vertical-align: baseline;display: flex;align-items: center;justify-content: center;vertical-align: baseline;}
			*{transition: all .5s ease-in-out;}
			.bi.d{font-size:4vw;cursor:pointer;}
			.map-container{border:5px solid black}
			@media only screen and (max-width: 700px) {.map-cel{height:14vw !important;}.bi.d{font-size:8vw;}.map-container .bi.d{font-size:6vw;}}
		</style>
	</head>
	
	<body>	
		<?php if(isset($_SESSION['poziom']))
		{
		?>
		<div class="container text-center">
			<div class="row py-3">
				<div class="col  mx-auto statystyki"></div>
			</div>
			<div class="row">
				<div class="col-2 mx-auto py-3"><span class="up"><i class="bi d bi-arrow-up-circle"></i></span></div>
			</div>
			<div class="row">			
				<div class="col-2 my-auto"><span class="left"><i class="bi d bi-arrow-left-circle"></i></span></div>
				<div class="col mapa">
					
				</div>
				<div class="col-2 my-auto"><span class="right"><i class="bi d bi-arrow-right-circle"></i></span></div>
			</div>
			<div class="row">	
				<div class="col-2 mx-auto py-3"><span class="down"><i class="bi d bi-arrow-down-circle"></i></span></div>
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
					<p>Gierka to prosta gra przeglądarkowa, w której chodzisz swoją postacią po planszy w poszukiwaniu domu.</p>					
				</div>
			</div>
			<div class="row">				
				<div class="col mt-1">
					<a class="btn btn-outline-dark" href="?p=1">Poziom łatwy</a>
				</div>
				<div class="col mt-1">
					<a class="btn btn-outline-dark" href="?p=3">Poziom średni</a>
				</div>
				<div class="col mt-1">
					<a class="btn btn-outline-dark" href="?p=5">Poziom trudny</a>
				</div>
			</div>
			<div class="row mt-5">				
				<div class="col-md-6">
				<h3>Instrukcja</h3>
				<p>
				Poruszasz się strzałkami - góra, dół, prawo i lewo. <i class="bi bi-arrow-up-circle"></i> <i class="bi bi-arrow-down-circle"></i> <i class="bi bi-arrow-right-circle"></i> <i class="bi bi-arrow-left-circle"></i> <br/>
				Każdy krok zabiera Ci 1punkt energii.<br/>
				Energię możesz uzupełnić wchodząc na pole oznaczone baterią <i class="bi bi-battery-charging text-success"></i>.</br/>
				Uważaj na pola z wodą <i class="bi bi-water text-primary"></i>, gdy na takie wejdziesz - przegrasz.</br/>
				Szukaj pola oznaczonego domkiem <i class="bi bi-house-heart-fill"></i>. Aby wygrać wejdź do niego.</br/>				
				</p>
				</div>				
				<div class="col-md-6">
					<img src="gra.png" class="img-fluid" />
				</div>
			</div>
			<div class="row">
				<div class="col">
				<?php
					$ilosc_giera = $conn->query("SELECT COUNT(id) AS ilosc FROM statystyki");
					$ilosc_gier = $ilosc_giera->fetch_assoc();
					$ilosc_wygranycha = $conn->query("SELECT COUNT(id) AS ilosc FROM statystyki WHERE wynik='1'");
					$ilosc_wygranych = $ilosc_wygranycha->fetch_assoc();
					$ilosc_przegranycha = $conn->query("SELECT COUNT(id) AS ilosc FROM statystyki WHERE wynik='2'");
					$ilosc_przegranych = $ilosc_przegranycha->fetch_assoc();
				?>
				<h3>Statystyki gry</h3>
				<p>
					W grę zagrano: <?=$ilosc_gier['ilosc'];?><br/>
					Wygranych: <?=$ilosc_wygranych['ilosc'];?><br/>
					Przegranych: <?=$ilosc_przegranych['ilosc'];?><br/>
				</p>
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
