$path = "c:\laragon\www\SMKN 1 Majene\E-Presensi\resources\views\admin"
Get-ChildItem -Path $path -File -Recurse | ForEach-Object {
    $content = Get-Content -Path $_.FullName -Raw
    $newContent = $content -creplace '(?<![\$\-\>\.])\bSiswa\b', 'Murid' -creplace '(?<![\$\-\>\.])\bsiswa\b', 'murid' -creplace '(?<![\$\-\>\.])\bSISWA\b', 'MURID'
    if ($content -cne $newContent) {
        Set-Content -Path $_.FullName -Value $newContent -NoNewline
        Write-Output "Updated: $($_.FullName)"
    }
}
