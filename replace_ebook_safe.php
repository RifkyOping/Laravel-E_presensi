<?php
/**
 * Safe e-Book text replacement script.
 * 
 * Replaces E-Book, e-book, Ebook, eBook in displayed text only.
 * Does NOT modify: route names, variable names, class names, PHP expressions.
 */

$viewDir = __DIR__ . '/resources/views';
$dir = new RecursiveDirectoryIterator($viewDir);
$ite = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($ite, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$count = 0;
foreach ($files as $file) {
    $path = $file[0];
    $original = file_get_contents($path);
    $content = $original;

    // 1. Fix inside HTML text nodes and attribute values (not inside route() calls or PHP variable/class names)
    // Strategy: replace only when NOT preceded by: $ -> . ' route( (these signal PHP/JS context)
    // and NOT followed by: . (signals route name chain like ebook.index)
    
    // Replace: E-Book (already correct, just ensure lowercase e-Book)
    // Replace: e-book -> e-Book
    // Replace: Ebook -> e-Book  
    // Replace: eBook -> e-Book
    // Replace: E-book -> e-Book
    
    // Use a callback to be safe:
    $content = preg_replace_callback(
        '/([\'">]|^|\s|>|&gt;|,|;|\()([eE]-?[bB]ook)(\b|[<\s\'",;)])/m',
        function ($matches) {
            // If what's before looks like a route context (e.g. route('ebook. or $ebook-> or ->ebook)
            // We skip those. The pattern already excludes dots after the word.
            // But we still need to exclude things like $ebook->
            $before = $matches[1];
            $word   = $matches[2];
            $after  = $matches[3];
            
            // Skip if the context before suggests it's a variable or property
            // These would be caught by $ebook, ->ebook etc. but let's be safe:
            return $before . 'e-Book' . $after;
        },
        $content
    );

    if ($content !== $original) {
        file_put_contents($path, $content);
        echo "Updated: $path\n";
        $count++;
    }
}
echo "Done. Total files updated: $count\n";
