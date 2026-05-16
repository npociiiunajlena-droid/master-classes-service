<?php

declare(strict_types=1);

$minCoverage = isset($argv[1]) ? (float) $argv[1] : 50.0;
$reportPath = $argv[2] ?? 'coverage.xml';

if (! is_file($reportPath)) {
    fwrite(STDERR, "Coverage report not found: {$reportPath}\n");
    exit(1);
}

$xml = simplexml_load_file($reportPath);

if ($xml === false) {
    fwrite(STDERR, "Cannot parse coverage report: {$reportPath}\n");
    exit(1);
}

$lineRate = 0.0;

if (isset($xml['line-rate'])) {
    $lineRate = (float) $xml['line-rate'];
}

if ($lineRate <= 0.0 && isset($xml->project->metrics)) {
    $metrics = $xml->project->metrics;
    $statements = (int) ($metrics['statements'] ?? 0);
    $coveredStatements = (int) ($metrics['coveredstatements'] ?? 0);

    if ($statements > 0) {
        $lineRate = $coveredStatements / $statements;
    }
}

$coverage = round($lineRate * 100, 2);
printf("Line coverage: %.2f%% (minimum: %.2f%%)\n", $coverage, $minCoverage);

if ($coverage < $minCoverage) {
    fwrite(STDERR, "Coverage gate failed: {$coverage}% < {$minCoverage}%\n");
    exit(1);
}

exit(0);
