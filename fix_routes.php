<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
foreach($ite as $file) {
    if($file->isFile() && $file->getExtension() === 'php') {
        $c = file_get_contents($file->getPathname());
        $newC = str_replace(["route('e-Book.", "routeIs('e-Book."], ["route('ebook.", "routeIs('ebook."], $c);
        if($c !== $newC) {
            file_put_contents($file->getPathname(), $newC);
            echo "Fixed: " . $file->getPathname() . "\n";
        }
    }
}
echo "Done fixing e-Book routes.\n";
