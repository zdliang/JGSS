@IF "%_echo%" == "" ECHO off
setlocal enabledelayedexpansion

SET EXE_DATE=%1%
SET FILE_PATH=C:\Code\JGSS\Data\%EXE_DATE%\
SET ZIP_PATH=%FILE_PATH%%EXE_DATE%\
SET FTP_SERVER=waws-prod-hk1-001.ftp.azurewebsites.windows.net
SET FTP_USER=jgss-image\$jgss-image
SET FTP_PWD=Civ7NY8gNRzq0Ch6ixj7PDfbJLT50sjeJg1FWmyvfT92spE2rQ428GF0iAsg
SET FTP_PATH=/site/wwwroot
SET SQL_FILE=load_csv.sql

SET LOGDATE=%date%
SET LOGDATE=%LOGDATE:/=_%
SET LOGDATE=%LOGDATE::=_%
SET LOGDATE=%LOGDATE:,=_%
SET LOGDATE=%LOGDATE: =_%
SET LOG=Upload_Log_%EXE_DATE%.txt
SET LOG_DATEIL=Upload_Log_Detail_%EXE_DATE%.txt

echo ************************prepare zip file******************************* >> %LOG%

dir /B /AD /od %FILE_PATH%image > combine_file_list.txt
mkdir %ZIP_PATH%
SET IDX=100
SET /A IMG_IDX=%IDX%/100
SET /A PRE_IMG_IDX=%IMG_IDX%
echo %IMG_IDX%
FOR /F "eol=; tokens=1 delims= " %%i IN (combine_file_list.txt) do (
	SET /a IMG_IDX=!IDX!/100
	
	if !IMG_IDX! GTR !PRE_IMG_IDX! (		
		echo !date! !time! : Zip image!PRE_IMG_IDX!.zip>> %LOG%
		7z a -tzip %FILE_PATH%image!PRE_IMG_IDX!.zip "%ZIP_PATH%*" -r >> %LOG_DATEIL%
		SET /A PRE_IMG_IDX=!IMG_IDX!
		RD /Q/S /Q %ZIP_PATH%
		mkdir %ZIP_PATH%
	)

	xcopy %FILE_PATH%image\%%i %ZIP_PATH%%%i\ /Y /Q >> %LOG_DATEIL%
	set /a IDX+=1	
)
echo %date% %time% : Zip image%PRE_IMG_IDX%.zip>> %LOG%
7z a -tzip %FILE_PATH%image%PRE_IMG_IDX%.zip "%ZIP_PATH%*" -r >> %LOG_DATEIL%
RD /Q/S /Q %ZIP_PATH%

echo %date% %time% : prepare zip file complete >> %LOG%

echo ************************upload image file******************************* >> %LOG%

echo %date% %time% : Begin upload zip files>> %LOG%
:upload_img

ncftpput -u %FTP_USER% -p %FTP_PWD% -r 5 -R %FTP_SERVER% %FTP_PATH% %FILE_PATH%*.zip >> %LOG_DATEIL%

if %Errorlevel%==1 goto upload_img

echo %date% %time% : All Images are uploaded >> %LOG%

REM ************************upzip image file*******************************

SET IMAGE_NUMBER=1

:upzip_img
echo %IMAGE_NUMBER%

if not exist %FILE_PATH%image%IMAGE_NUMBER%.zip goto upzip_img_end

echo %date% %time% : Begin unzip image%IMAGE_NUMBER%.zip >> %LOG%

CmdHttpRequest.exe -u "http://jgss-image.azurewebsites.net/unzip.php?file=image%IMAGE_NUMBER%.zip&path=%EXE_DATE%" >> %LOG%

if %Errorlevel%==1 goto upzip_img

SET /a IMAGE_NUMBER+=1
goto upzip_img

:upzip_img_end
echo %date% %time% : All Images are upzipped >> %LOG%
DEL %FILE_PATH%*.zip

echo ************************upload CSV file******************************* >> %LOG%

SET CSV_FILENAME=%EXE_DATE%_100_close_predict.csv
SET STOCKINFO_FILENAME=stockinfo_%EXE_DATE%.csv

echo %date% %time% : Begin upload %CSV_FILENAME% >> %LOG%

echo open yuanbao.cloudapp.net>psftp_cmd.txt
echo cd /home/stock>>psftp_cmd.txt
echo put %FILE_PATH%%CSV_FILENAME%>>psftp_cmd.txt
echo put %FILE_PATH%%STOCKINFO_FILENAME%>>psftp_cmd.txt
echo bye>>psftp_cmd.txt

psftp -l zdliang -pw 1a2b3c4D  -b psftp_cmd.txt >> %LOG%

echo %date% %time% : End upload %CSV_FILENAME% >> %LOG%

echo ************************import CSV file******************************* >> %LOG%

echo %date% %time% : Begin import %CSV_FILENAME% >> %LOG%

echo Use app_zdliang; > %SQL_FILE%

if not exist %FILE_PATH%%STOCKINFO_FILENAME% goto import_predict

echo Truncate table stockinfo; >> %SQL_FILE%
echo LOAD DATA INFILE '/home/stock/stockinfo_%EXE_DATE%.csv' >> %SQL_FILE%
echo INTO TABLE stockinfo >> %SQL_FILE%
echo FIELDS TERMINATED BY ' ' OPTIONALLY ENCLOSED BY '^"' >> %SQL_FILE%
echo LINES TERMINATED BY '\r\n' >> %SQL_FILE%
echo IGNORE 1 LINES; >> %SQL_FILE%

:import_predict

echo Truncate table close_predict; >> %SQL_FILE%
echo LOAD DATA INFILE '/home/stock/%EXE_DATE%_100_close_predict.csv' >> %SQL_FILE%
echo INTO TABLE close_predict >> %SQL_FILE%
echo FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '^"' >> %SQL_FILE%
echo LINES TERMINATED BY '\r\n' >> %SQL_FILE%
echo IGNORE 1 LINES; >> %SQL_FILE%

echo truncate top_ten_daily; >> %SQL_FILE%
echo insert into top_ten_daily (symbol,date,predictRet,Ret_Cal,name) >> %SQL_FILE%
echo SELECT  close_predict.symbol,close_predict.date,avg(close_predict.predictRet),(avg(close_predict.predictRet) * (1 - (avg(close_predict.dist) * 5))) AS Ret_Cal,stockinfo.name >> %SQL_FILE%
echo FROM close_predict inner join stockinfo on close_predict.symbol=stockinfo.symbol >> %SQL_FILE%
echo where sameIndex=1 and samePosition=1 and sameIndexPosition=1 and volumeDistRank in (0,1,2) and indexDistRank in (0) and weeklyDistRank in (0,1) and weeklyIndexDistRank in (0) >> %SQL_FILE%
echo group by close_predict.symbol,close_predict.date,stockinfo.name having count(0)^>4;>> %SQL_FILE%

mysql -uyuanbao -p1a2b3c -hyuanbao.cloudapp.net < %SQL_FILE%

echo %date% %time% : End import %CSV_FILENAME% >> %LOG%

exit