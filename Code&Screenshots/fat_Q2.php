<?php

function normalizeText($fileName, $mode) {
    $corrections = 0;
    $punctuationOnlyLines = [];

    if (!file_exists($fileName)) {
        throw new Exception("File not found!");
    }
    $lines = file($fileName, FILE_IGNORE_NEW_LINES);

    $normalized = [];

    foreach ($lines as $index => $line) {

        $original = $line;
        $newLine = preg_replace('/[ \t]+/', ' ', $line);
        if ($newLine !== $line) {
            $corrections++;
        }
        $trimmed = trim($newLine);
        if ($trimmed !== $newLine) {
            $corrections++;
        }
        if ($trimmed !== "" && preg_match('/^[[:punct:]]+$/', $trimmed)) {
            $punctuationOnlyLines[] = $index + 1;
        }

        $normalized[] = $trimmed;
    }

    $finalLines = [];
    if ($mode === "compress") {
        $blankFlag = false;

        foreach ($normalized as $line) {
            if ($line === "") {
                if (!$blankFlag) {
                    $finalLines[] = "";
                    $blankFlag = true;
                } else {
                    $corrections++; 
                }
            } else {
                $finalLines[] = $line;
                $blankFlag = false;
            }
        }
    }

    else if ($mode === "expand") {
        foreach ($normalized as $line) {
            $finalLines[] = $line;
            $finalLines[] = "";
            $corrections++; 
        }
    }

    else {
        throw new Exception("Invalid mode: use 'compress' or 'expand'");
    }

    file_put_contents($fileName, implode("\n", $finalLines) . "\n");


    if (!empty($punctuationOnlyLines)) {
        echo "Lines containing only punctuation: " . implode(", ", $punctuationOnlyLines) . "\n";
    } else {
        echo "No punctuation-only lines found.\n";
    }

    return $corrections;
}
$filename="file.txt";
$mode="compress";
?>
