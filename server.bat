@echo off
rem Lance le serveur Laravel (redemarre automatiquement s'il s'arrete)
cd /d "%~dp0"
powershell.exe -NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File "%~dp0scripts\start-service.ps1"
