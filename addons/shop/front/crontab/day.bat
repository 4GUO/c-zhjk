@echo off 
start http://yt.hndouya.cc/crontab.php/day/index/i/1/ps/125d0d502244655321fd3c3daf0dc440
ping -n 5 127.1 >nul 5>nul 
taskkill /f /im IEXPLORE.exe 
exit