Use app_zdliang;
Truncate table close_predict;
LOAD DATA INFILE '/home/stock/20150520_100_close_predict.csv'
INTO TABLE close_predict
FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '"'
LINES TERMINATED BY '\r\n'
IGNORE 1 LINES;
truncate top_ten_daily;
insert into top_ten_daily (symbol,date,predictRet,Ret_Cal,name)
SELECT  close_predict.symbol,
        close_predict.date,
        avg(close_predict.predictRet),
        (avg(close_predict.predictRet) * (1 - (avg(close_predict.dist) * 5))) AS Ret_Cal,
        stockinfo.name
FROM close_predict inner join stockinfo on close_predict.symbol=stockinfo.symbol
where sameIndex=1 and samePosition=1 and sameIndexPosition=1 and volumeDistRank in (0,1,2) and indexDistRank in (0) and weeklyDistRank in (0,1) and weeklyIndexDistRank in (0)
group by close_predict.symbol,close_predict.date,stockinfo.name   
having count(0)>4;