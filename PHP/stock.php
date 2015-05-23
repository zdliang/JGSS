<?php
/**
*  class to get stock information
*/
class Stock
{    	
    function GetSimiliarStocks($stockNum)
    {
        $similarStocks = array();
        if (is_numeric($stockNum) && strlen($stockNum)==6){
            if ($_SERVER['REMOTE_ADDR']=="127.0.0.1"){
                $link = mysql_connect('127.0.0.1', 'admin', '1a2b3c') or die('Could not connect: ' . mysql_error());    
                mysql_select_db("app_zdliang") or die('Could not select database');                
                
            } else{                
            	$link = mysql_connect('yuanbao.cloudapp.net', 'yuanbao', '1a2b3c') or die('Could not connect: ' . mysql_error());    
                mysql_select_db("app_zdliang") or die('Could not select database');                

                // $link = mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS) or die('Could not connect: ' . mysql_error()); 
                // mysql_select_db(SAE_MYSQL_DB) or die('Could not select database');
            }            

            $query = "SELECT p.*,s1.name symbolName,s2.name matchedSymbolName FROM close_predict p 
						inner join stockinfo s1 on p.symbol=s1.symbol 
						inner join stockinfo s2 on p.matchedSymbol=s2.symbol 
						where sameIndex=1 and samePosition=1 and sameIndexPosition=1 and volumeDistRank in (0,1,2) and indexDistRank in (0) and weeklyDistRank in (0,1) and weeklyIndexDistRank in (0) 
                        and p.symbol='".$stockNum."' order by abs(dist) asc limit 0,9";

            $result = mysql_query($query) or die('Query failed: ' . mysql_error());

            // Printing results in HTML            
            while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {                                
                $similarStocks[] = array("matchedSymbol" => $line["matchedSymbol"], 
                	"date" => $line["date"],
                	"symbolName" => $line["symbolName"],
                	"matchedSymbolName" => $line["matchedSymbolName"],
                	"matchedWinStartDate"=>$line["matchedWinStartDate"],
                	"matchedWinEndDate"=>$line["matchedWinEndDate"],
                	"meanPredictRet"=>$line["meanPredictRet"],
                	"meanPredictExcessRet"=>$line["meanPredictExcessRet"],
                	"dist"=>$line["dist"]);
            }            

            // Free resultset
            mysql_free_result($result);

            // Closing connection
            mysql_close($link);
            return $similarStocks;
        }
    }

    function GetStockInfo($keyword)
    {
    	$stockinfo = array();
    	if ($_SERVER['REMOTE_ADDR']=="127.0.0.1"){
            $link = mysql_connect('127.0.0.1', 'admin', '1a2b3c') or die('Could not connect: ' . mysql_error());    
            mysql_select_db("app_zdliang") or die('Could not select database');                
            
        } else{
    		$link = mysql_connect('yuanbao.cloudapp.net', 'yuanbao', '1a2b3c') or die('Could not connect: ' . mysql_error());    
            mysql_select_db("app_zdliang") or die('Could not select database');                                

            // $link = mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS) or die('Could not connect: ' . mysql_error()); 
            // mysql_select_db(SAE_MYSQL_DB) or die('Could not select database');                
        }

        $query = "SELECT * from stockinfo where name like '%".$keyword."%' or py like '%".$keyword."%' limit 0,9";

        $result = mysql_query($query) or die('Query failed: ' . mysql_error());

        // Printing results in HTML            
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {                                
            $similarStocks[] = array("symbol" => $line["symbol"], 
            	"name" => $line["name"],
            	"py" => $line["py"]);
        }

        // Free resultset
        mysql_free_result($result);

        // Closing connection
        mysql_close($link);
        return $similarStocks;

    }

    function GetTopTenDaily()
    {
    	$stockinfo = array();
    	if ($_SERVER['REMOTE_ADDR']=="127.0.0.1"){
            $link = mysql_connect('127.0.0.1', 'admin', '1a2b3c') or die('Could not connect: ' . mysql_error());    
            mysql_select_db("app_zdliang") or die('Could not select database');                
            
        } else{
    		$link = mysql_connect('yuanbao.cloudapp.net', 'yuanbao', '1a2b3c') or die('Could not connect: ' . mysql_error());    
            mysql_select_db("app_zdliang") or die('Could not select database');                                

            // $link = mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS) or die('Could not connect: ' . mysql_error()); 
            // mysql_select_db(SAE_MYSQL_DB) or die('Could not select database');                
        }

        $query = "select * from top_ten_daily order by Ret_cal desc limit 0,10";

        $result = mysql_query($query) or die('Query failed: ' . mysql_error());

        // Printing results in HTML            
        while ($line = mysql_fetch_array($result, MYSQL_ASSOC)) {                                
            $similarStocks[] = array("symbol" => $line["symbol"], 
            	"name" => $line["name"],
            	"predictRet" => $line["predictRet"],
            	"Ret_Cal" => $line["Ret_Cal"],
            	"date" => $line["date"]);
        }

        // Free resultset
        mysql_free_result($result);

        // Closing connection
        mysql_close($link);
        return $similarStocks;
    }

    function RefreshData()
    {
    	if ($_SERVER['REMOTE_ADDR']=="127.0.0.1"){
            $link = mysql_connect('127.0.0.1', 'admin', '1a2b3c') or die('Could not connect: ' . mysql_error());    
            mysql_select_db("app_zdliang") or die('Could not select database');                            
        } else{                
        	$link = mysql_connect('yuanbao.cloudapp.net', 'yuanbao', '1a2b3c') or die('Could not connect: ' . mysql_error());
            mysql_select_db("app_zdliang") or die('Could not select database');                
            // $link = mysql_connect(SAE_MYSQL_HOST_M.":".SAE_MYSQL_PORT, SAE_MYSQL_USER, SAE_MYSQL_PASS) or die('Could not connect: ' . mysql_error()); 
            // mysql_select_db(SAE_MYSQL_DB) or die('Could not select database');            
        }

        $drop_srcipt="DROP table close_predict;";
        $rename_srcipt ="ALTER TABLE `close_predict_new` RENAME TO  `close_predict` ;";
        $create_script ="CREATE TABLE `close_predict_new` (
		  `symbol` text,
		  `date` datetime DEFAULT NULL,
		  `winStartDate` datetime DEFAULT NULL,
		  `winEndDate` datetime DEFAULT NULL,
		  `windowRet` double DEFAULT NULL,
		  `position` int(11) DEFAULT NULL,
		  `indexposition` int(11) DEFAULT NULL,
		  `indexcode` text,
		  `matchedSymbol` text,
		  `matchedWinStartDate` datetime DEFAULT NULL,
		  `matchedWinEndDate` datetime DEFAULT NULL,
		  `matchedWindowRet` double DEFAULT NULL,
		  `matchedPosition` int(11) DEFAULT NULL,
		  `matchedIndexPosition` int(11) DEFAULT NULL,
		  `matchedIndexcode` int(11) DEFAULT NULL,
		  `sameIndustry` int(11) DEFAULT NULL,
		  `sameIndex` int(11) DEFAULT NULL,
		  `samePosition` int(11) DEFAULT NULL,
		  `sameIndexPosition` int(11) DEFAULT NULL,
		  `cux` double DEFAULT NULL,
		  `cvx` double DEFAULT NULL,
		  `cuy` double DEFAULT NULL,
		  `cvy` double DEFAULT NULL,
		  `alpha` double DEFAULT NULL,
		  `beta` double DEFAULT NULL,
		  `gamma` double DEFAULT NULL,
		  `dist` double DEFAULT NULL,
		  `volumeDist` double DEFAULT NULL,
		  `volumeDistRank` int(11) DEFAULT NULL,
		  `indexDist` double DEFAULT NULL,
		  `indexDistRank` int(11) DEFAULT NULL,
		  `longDist` double DEFAULT NULL,
		  `longDistRank` int(11) DEFAULT NULL,
		  `longIndexDist` double DEFAULT NULL,
		  `longIndexDistRank` int(11) DEFAULT NULL,
		  `predictDist` double DEFAULT NULL,
		  `actualRet` double DEFAULT NULL,
		  `actualExcessRet` double DEFAULT NULL,
		  `predictRet` double DEFAULT NULL,
		  `meanPredictRet` double DEFAULT NULL,
		  `predictExcessRet` double DEFAULT NULL,
		  `meanPredictExcessRet` double DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $query = "SELECT date from app_zdliang.close_predict limit 0,1";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        while($line = mysql_fetch_array($result, MYSQL_ASSOC)){
        	$old_date = $line["date"];        	
        }
        mysql_free_result($result);

        $query = "SELECT date from app_zdliang.close_predict_new limit 0,1";
        $result = mysql_query($query) or die('Query failed: ' . mysql_error());
        while($line = mysql_fetch_array($result, MYSQL_ASSOC)){
        	$new_date = $line["date"];        	
        }
        mysql_free_result($result);

        if(empty($old_date) || (!empty($new_date) && $new_date>$old_date)){
        	mysql_query($drop_srcipt) or die('Query failed: ' . mysql_error());
        	mysql_query($rename_srcipt) or die('Query failed: ' . mysql_error());
        	mysql_query($create_script) or die('Query failed: ' . mysql_error());
        }

        mysql_close($link);
    }
}
?>