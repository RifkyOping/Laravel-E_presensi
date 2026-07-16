<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    // Replace E-Book, Ebook, e-book, eBook with e-Book
    // But ONLY if it's not preceded by a dot (e.g. route('ebook.'))
    // and not preceded by a slash (e.g. /ebook/)
    // and not a variable $ebook
    
    // We can do this simply by replacing exactly what's visible.
    // e.g. E-Book, e-book, Ebook, eBook
    
    // Using preg_replace with negative lookbehind
    $replaced = preg_replace('/(?<![\$\.\/a-zA-Z_-])[eE]-?[bB]ook(?![a-zA-Z_-])/', 'e-Book', $content);
    
    if ($content !== $replaced) {
        file_put_contents($path, $replaced);
        echo "Updated: $path\n";
        $count++;
    }
}
echo "Total updated: $count\n";
