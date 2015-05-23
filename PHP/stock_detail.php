<html>
	<header>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	</header>
	<body>
		<?php

			include 'stock.php';
			define("IMG_HOME","http://jgss-image.azurewebsites.net/");
			
			$symbol = $_GET["symbol"];
			$date = $_GET["date"];
			$matchedSymbol = $_GET["matchedSymbol"];			
			$matchedWinEndDate = $_GET["matchedWinEndDate"];
			$matchedWinStartDate = $_GET["matchedWinStartDate"];
			//$stock = new StockTest();
			if (empty($matchedSymbol))
			{
				$pic_url = IMG_HOME.$date."/".$symbol."/".$symbol.".png";
			}			
			else
			{
				$pic_url = IMG_HOME.$date."/".$symbol."/".$matchedSymbol."_".$matchedWinStartDate."_".$matchedWinEndDate.".png";
			}
			//echo "<img src=\"".$pic_url."\" />";
			//$stockList = $stock->GetSimiliarStocks($stockNum);
			?>					
			<img src="<?php echo $pic_url?>" />		
	</body>
</html>