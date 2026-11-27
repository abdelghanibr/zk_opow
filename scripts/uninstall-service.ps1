# À exécuter en administrateur : arrête et supprime la tâche + le serveur.
$name = 'ZK-Attendance-Server'
Stop-ScheduledTask -TaskName $name -ErrorAction SilentlyContinue
Unregister-ScheduledTask -TaskName $name -Confirm:$false -ErrorAction SilentlyContinue
Get-CimInstance Win32_Process -Filter "Name='php.exe'" | Where-Object { $_.CommandLine -match 'artisan\s+serve' } |
    ForEach-Object { Stop-Process -Id $_.ProcessId -Force }
Write-Host "Service '$name' supprimé."