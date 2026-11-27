# À exécuter UNE FOIS en administrateur : crée une tâche qui lance le serveur au démarrage de Windows, en arrière-plan.
param([string]$BindHost = '127.0.0.1', [int]$Port = 8000)

$name   = 'ZK-Attendance-Server'
$script = Join-Path $PSScriptRoot 'start-service.ps1'

$action  = New-ScheduledTaskAction -Execute 'powershell.exe' `
    -Argument "-NoProfile -ExecutionPolicy Bypass -WindowStyle Hidden -File `"$script`" -BindHost $BindHost -Port $Port"
$trigger = New-ScheduledTaskTrigger -AtStartup
$set     = New-ScheduledTaskSettingsSet -AllowStartIfOnBatteries -DontStopIfGoingOnBatteries `
    -StartWhenAvailable -RestartCount 999 -RestartInterval (New-TimeSpan -Minutes 1) -ExecutionTimeLimit ([TimeSpan]::Zero)

Unregister-ScheduledTask -TaskName $name -Confirm:$false -ErrorAction SilentlyContinue
Register-ScheduledTask -TaskName $name -Action $action -Trigger $trigger -Settings $set `
    -User 'SYSTEM' -RunLevel Highest | Out-Null
Start-ScheduledTask -TaskName $name

Write-Host "OK : tâche '$name' installée et démarrée -> http://$BindHost`:$Port"