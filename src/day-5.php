<?php

declare(strict_types=1);

use loophp\collection\Collection;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$input = file_get_contents(dirname(__DIR__). '/input/day-5.txt');
$exampleInput = <<<TEXT
seeds: 79 14 55 13

seed-to-soil map:
50 98 2
52 50 48

soil-to-fertilizer map:
0 15 37
37 52 2
39 0 15

fertilizer-to-water map:
49 53 8
0 11 42
42 0 7
57 7 4

water-to-light map:
88 18 7
18 25 70

light-to-temperature map:
45 77 23
81 45 19
68 64 13

temperature-to-humidity map:
0 69 1
1 0 69

humidity-to-location map:
60 56 37
56 93 4
TEXT;

[$seedInput, $mapInput] = explode(PHP_EOL.PHP_EOL, $input, 2);

$seeds = explode(' ', explode(': ', $seedInput)[1]);
$seeds = array_map('intval', $seeds);

$maps = explode(PHP_EOL.PHP_EOL, $mapInput);
/**
 * array<int, array<int, string[]>>
 */
$maps = Collection::fromIterable($maps)
    ->map(static fn (string $i) => array_slice(explode(PHP_EOL, $i), 1))
    ->map(static fn (array $lines) => array_map(static fn ($l) => explode(' ', $l), $lines))
    ->all();

$getMinOfRange = static function (array $seeds): int {
    global $maps;

    $seedResults = [];
    foreach ($seeds as $index => $seed) {
        $seedResults[$index] = $seed;
        foreach ($maps as $map) {
            foreach ($map as $range) {
                if ($seedResults[$index] >= $range[1]  && $seedResults[$index] <= $range[1] + $range[2] - 1) {
                    $seedResults[$index] = $range[0] + ($seedResults[$index] - $range[1]);
                    break;
                }
            }
        }
    }
    return min($seedResults);
};

var_dump($getMinOfRange($seeds));
