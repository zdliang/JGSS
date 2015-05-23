@if "%_echo%" == "" echo off
SET EXE_DATE=%1%

SET IMAGE_NUMBER=1

:upzip_img
echo %IMAGE_NUMBER%

echo %date% %time% : Begin unzip image%IMAGE_NUMBER%.zip

CmdHttpRequest.exe -u "http://jgss-image.azurewebsites.net/unzip.php?file=image%IMAGE_NUMBER%.zip&path=%EXE_DATE%" 

if %Errorlevel%==1 goto upzip_img

SET /a IMAGE_NUMBER+=1
goto upzip_img

echo %date% %time% : All Images are upzipped