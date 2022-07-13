<?php

$minimumCoverage = 80;
$coverageFile = 'var/tests/coverage/index.xml';

if (!is_file($coverageFile)) {
    echo 'Coverage file does not exist at '.$coverageFile;
    exit(1);
}

$changedFiles = [];

// TODO needs to be diff against target branch, which might not be main
exec('git diff origin/main --name-only', $files, $resultCode);

if ($resultCode !== 0) {
    echo implode("\n", $files);
    exit($resultCode);
}

$stats = [];
$parser = new \DOMDocument();
$parser->loadXML(file_get_contents($coverageFile));
foreach ($parser->getElementsByTagName('file') as $file) {
    $filename = substr($file->getAttribute('href'), 0, -4);
    if (in_array('src/'.$filename, $files, true)) {
        $lines = $file->getElementsByTagName('lines')[0];
        if ($lines->getAttribute('executable') > 0) {
            $stats[$filename] = round($lines->getAttribute('percent'), 2);
        }
    }
}

$anyInsufficientlyCovered = false;
foreach ($stats as $filename => $coverage) {
    if ($coverage < $minimumCoverage) {
        $anyInsufficientlyCovered = true;
        $emoji = 'x';
    } else {
        $emoji = 'heavy_check_mark';
    }

    echo sprintf(
        '%s | %s | :%s:'.PHP_EOL,
        $filename,
        $coverage,
        $emoji
    );
}

if ($anyInsufficientlyCovered) {
    echo "\e[0;37;41mFAILURE: Insufficient code coverage\e[0m\n";
    exit(1);
}

echo "\e[0;30;42mSUCCESS: Sufficient code coverage\e[0m\n";
exit(0);
