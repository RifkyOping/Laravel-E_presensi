<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/app/Http/Controllers');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    
    // Replace 'siswa' with 'murid' ONLY when it's surrounded by quotes
    // and NOT as part of route names like 'siswa.profil'
    $newContent = preg_replace("/(['\"])siswa(['\"])/", "$1murid$2", $content);

    // Revert some cases that should not have been replaced by the previous line:
    // For example: compact('siswa') -> compact('murid') (we want to keep it 'siswa')
    $newContent = preg_replace("/compact\(['\"]murid['\"]\)/", "compact('siswa')", $newContent);
    // return view('siswa.profil') -> 'murid.profil' is wrong because we didn't rename the folders.
    $newContent = str_replace("view('murid.", "view('siswa.", $newContent);
    $newContent = str_replace("view(\"murid.", "view(\"siswa.", $newContent);

    if ($content !== $newContent) {
        file_put_contents($path, $newContent);
        $count++;
    }
}

echo "Updated $count files.\n";
