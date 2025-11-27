<?php

declare(strict_types=1);

/**
 * Bootstrap file for PHP 8.5 pipe operator tests.
 * This file dynamically loads test files that contain PHP 8.5 syntax.
 */

// Only include PHP 8.5 test files if running on PHP 8.5+
if (PHP_VERSION_ID >= 80500) {
    // These files contain actual pipe operator syntax (|>)
    // and will cause parse errors on PHP < 8.5
    // They use .php85 extension to prevent auto-discovery by PHPUnit
    require_once __DIR__ . '/Option/Php85PipeOperatorTest.php85';
    require_once __DIR__ . '/Result/Php85PipeOperatorTest.php85';
}
