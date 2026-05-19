<?php
$f = fopen('eduforum', 'rb');
$header = fread($f, 16);
fclose($f);
echo $header . PHP_EOL;
if (strpos($header, 'SQLite format 3') === 0) {
    echo "It IS a SQLite file" . PHP_EOL;
} else {
    echo "It IS NOT a SQLite file" . PHP_EOL;
}
