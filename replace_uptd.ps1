$paths = @("resources\views", "app", "config", "database")
foreach ($path in $paths) {
    $files = Get-ChildItem -Path $path -Recurse -File -Include *.php
    foreach ($file in $files) {
        $content = Get-Content $file.FullName -Raw
        $newContent = [regex]::Replace($content, '(?i)(?<!UPTD\s)SMKN 1 Majene', 'UPTD SMKN 1 Majene')
        if ($content -cne $newContent) {
            Set-Content -Path $file.FullName -Value $newContent -NoNewline
            Write-Host "Updated $($file.FullName)"
        }
    }
}
