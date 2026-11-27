# Lance le serveur Laravel en continu (redémarre s'il s'arrête). Aucun besoin de Laragon ouvert.
param(
    [string]$BindHost = '127.0.0.1',   # '0.0.0.0' pour l'ouvrir aux autres PC du réseau
    [int]$Port = 8000
)

$project = Split-Path -Parent $PSScriptRoot
$php = (Get-ChildItem 'C:\laragon\bin\php\*\php.exe' -ErrorAction SilentlyContinue | Sort-Object FullName -Descending | Select-Object -First 1).FullName
if (-not $php) { $php = (Get-Command php.exe -ErrorAction SilentlyContinue).Source }
if (-not $php) { Write-Error 'php.exe introuvable'; exit 1 }

$log = Join-Path $project 'storage\logs\service.log'
Set-Location $project

while ($true) {
    "[{0}] démarrage http://{1}:{2}" -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'), $BindHost, $Port | Add-Content $log
    & $php artisan serve --host=$BindHost --port=$Port *>> $log
    "[{0}] arrêt (code {1}), redémarrage dans 5 s" -f (Get-Date -Format 'yyyy-MM-dd HH:mm:ss'), $LASTEXITCODE | Add-Content $log
    Start-Sleep -Seconds 5
}