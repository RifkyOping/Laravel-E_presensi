<?php
$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    
    // Pattern: 
    // (?<![\$\.\/a-zA-Z_\-\'"]) means DO NOT match if preceded by any of those.
    // (?![a-zA-Z_\-\.\/]) means DO NOT match if followed by any of those.
    $pattern = '/(?<![\$\.\/a-zA-Z_\-\'"])[eE]-?[bB]ook(s)?(?![a-zA-Z_\-\.\/])/i';
    
    $replaced = preg_replace($pattern, 'e-Book', $content);
    
    if ($content !== $replaced) {
        file_put_contents($path, $replaced);
        echo "Updated: $path\n";
        $count++;
    }
}
echo "Total updated: $count\n";
