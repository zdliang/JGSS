<html>
	<header>
		<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	</header>
	<body>
		<?php
			include 'stock.php';			
			$keyword = $_GET["keyword"];
			$stock = new Stock();
			// // $date = $stock->RefreshData();
			// // foreach ($date as $key => $value) {
			// // 	echo $key."<BR/>";
			// // 	echo $value."<BR/>";
			// // }		
			$topStocks = $stock->GetTopTenDaily();
			foreach ($topStocks as $topstock) {
				echo $topstock["symbol"]." ".$topstock["name"];
				echo "<BR />";
			}

			echo "<BR />";

			$stockinfo = $stock->GetStockInfo($keyword);
			if (!is_array($stockinfo) or count($stockinfo)==0) {
			 	echo "empty stock array";	
			}
			else {
				foreach ($stockinfo as $onestock) {
					echo $onestock["symbol"]." ".$onestock["name"];
					echo "<BR />";
				}
			}			

			echo "<BR />";

			$stockNum = $stockinfo[0]["symbol"];

			$stockList = $stock->GetSimiliarStocks($stockNum);
			if (!is_array($stockList) or count($stockList)==0) {
			 	echo "empty stock array";	
			}
			else{		
				//echo sprintf("%.2f", $stockList[0]["meanPredictExcessRet"]*100)."%<BR />";
				//echo sprintf("%.2f", $stockList[0]["meanPredictExcessRet"]*100)."%<BR />";
				echo "未来20日预期绝对回报 ".sprintf("%.2f", $stockList[0]["meanPredictRet"]*100)."%  预期相对回报".sprintf("%.2f", $stockList[0]["meanPredictExcessRet"]*100)."%\n本数据由历史数据模拟而成，不作为投资依据，\n投资者据此操作，我公司不负任何责任";
				echo "<BR />";
				$content = array();
				$content[] = array("Title"=>"股票名称：".$stockList[0]["symbolName"]."\n股票代码：".$stockNum, 
			                                   "Description"=>"未来20日预期绝对回报 ".sprintf("%.2f", $stockList[0]["meanPredictRet"]*100)."%  预期相对回报".sprintf("%.2f", $stockList[0]["meanPredictExcessRet"]*100)."%\n本数据由历史数据模拟而成，不作为投资依据，\n投资者据此操作，我公司不负任何责任", 
			                                   "PicUrl"=>$stockNum."/".$stockNum.".png", 
			                                   "Url" =>"");
				$index = 1;
				foreach ($stockList as $stock) {		
					//echo $index."<BR />";
					//echo "历史最像走势第".$index."名 ".$stock["matchedSymbol"]." ".date("Y/m/d",strtotime($stock["matchedWinStartDate"]))."-".date("Y/m/d",strtotime($stock["matchedWinEndDate"]))."<BR />";
					echo "历史最像走势第".$index."名 ".$stock["matchedSymbol"]." ".date("Y/m/d",strtotime($stock["matchedWinStartDate"]))."-".date("Y/m/d",strtotime($stock["matchedWinEndDate"]));
					echo "<BR />";
					$content[] = array(//"Title"=>"历史最像走势第".$index."名 ".$stock["matchedSymbol"]." ".date("Y/m/d",strtotime($stock["matchedWinStartDate"]))."-".date("Y/m/d",strtotime($stock["matchedWinEndDate"])), 
			                        "Title"=>$index." ".date("Ymd",strtotime($stock["date"]))." ".$stock["matchedSymbol"]." ".$stock["matchedSymbolName"]." ".date("Y/m/d",strtotime($stock["matchedWinStartDate"]))."-".date("Y/m/d",strtotime($stock["matchedWinEndDate"])), 
			                        "Description"=>"", 
			                        "PicUrl"=>$stockNum."/".$stock["matchedSymbol"]."_".date("Y-m-d",strtotime($stock["matchedWinEndDate"]))."_".rtrim(sprintf("%.4f", $stock["dist"]),"0").".png", 
			                        "Url" =>"");
					//echo sprintf("%.2f", $stock["meanPredictExcessRet"]*100)."%<BR />";
					$index++;		
				}	
				foreach ($content as $item) {
					foreach ($item as $key => $value) {
						echo $key."<BR/>";
						echo $value."<BR/>";
					}				
				}				
			}
			?>			
		<form>			
		</form>
	</body>
</html>