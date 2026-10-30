$ErrorActionPreference = 'Stop'
$port = 8123
$ip = Get-NetIPAddress -AddressFamily IPv4 |
    Where-Object { $_.IPAddress -notlike '127.*' -and $_.PrefixOrigin -ne 'WellKnown' } |
    Sort-Object InterfaceMetric |
    Select-Object -First 1 -ExpandProperty IPAddress

if (-not $ip) {
    Write-Error 'IP jaringan lokal tidak ditemukan. Pastikan komputer terhubung ke WiFi/LAN.'
}

Write-Host ''
Write-Host 'Absensi Kampus siap untuk jaringan lokal:' -ForegroundColor Green
Write-Host ('  Komputer : http://127.0.0.1:' + $port)
Write-Host ('  HP       : http://' + $ip + ':' + $port) -ForegroundColor Cyan
Write-Host ''
Write-Host 'Pastikan HP dan komputer berada pada jaringan yang sama.'
Write-Host 'Jika Windows Firewall bertanya, izinkan akses Private network.'
Write-Host 'Tekan Ctrl+C untuk menghentikan server.'
Write-Host ''

php artisan serve --host=0.0.0.0 --port=$port
