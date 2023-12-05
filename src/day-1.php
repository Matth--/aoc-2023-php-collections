<?php

declare(strict_types=1);

use loophp\collection\Collection;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$input = file_get_contents(dirname(__DIR__). '/input/day-1.txt');

$wordToNumber = [
    'one' => '1e',
    'two' => 't2o',
    'three' => 't3e',
    'four' => '4r',
    'five' => '5e',
    'six' => '6',
    'seven' => '7n',
    'eight' => 'e8',
    'nine' => 'n9e',
];

$replace_words_to_numbers = static fn (string $input) =>
    str_replace(array_keys($wordToNumber), array_values($wordToNumber), $input);

$get_calibration_value = static function (string $line): int {
    $integers = Collection::fromIterable(str_split($line))
        ->filter(static fn(string $c): bool => is_numeric($c));

    return (int)($integers->first() . $integers->last());
};

$sum_reduce = static fn (int $acc, int $calibration_value) => $acc + $calibration_value;

$calibration_result = Collection::fromIterable(explode(PHP_EOL, $input))
    ->map($replace_words_to_numbers)
    ->map($get_calibration_value)
    ->reduce($sum_reduce, 0)
;

echo $calibration_result;