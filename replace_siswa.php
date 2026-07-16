<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/app/Http/Controllers');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    
    // Replace "Siswa" with "Murid"
    $newContent = preg_replace('/\bSiswa\b/', 'Murid', $content);
    
    // Replace "siswa" with "murid", BUT NOT if it starts with $
    // and NOT if it's part of a route name inside route('...') unless we want to change it.
    // Actually we ALREADY changed route('siswa.*') to route('murid.*') in app.blade.php.
    // What about route('admin.absensi-siswa')? The user said don't change what's not visible.
    // So let's only replace lowercase "siswa" if it's NOT a variable (not preceded by $)
    // AND NOT part of a URL or route like `absensi-siswa` (not preceded or followed by hyphen)
    // AND NOT part of an object property like `->siswa` (not preceded by >)
    // AND NOT part of an array key like `['siswa']` or `"siswa"` (we'll avoid quotes entirely if possible, but UI text might not be quoted).
    
    $newContent = preg_replace('/(?<!\$)(?<!>)(?<!-)(?<!\.)\bsiswa\b(?!-)(?!\.)/', 'murid', $newContent);

    // Also replace role == 'siswa' just in case it was missed, although we did some manually.
    // Actually, the regex `(?<!\$)(?<!>)(?<!-)(?<!\.)\bsiswa\b(?!-)(?!\.)` will match `role === 'siswa'` and change it to `'murid'` which is EXACTLY what we want!

    if ($content !== $newContent) {
        file_put_contents($path, $newContent);
        $count++;
    }
}

echo "Updated $count files.\n";
