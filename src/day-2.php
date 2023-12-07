<?php

declare(strict_types=1);

use loophp\collection\Collection;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$input = file_get_contents(dirname(__DIR__). '/input/day-2.txt');

final readonly class CubeAmount
{
    private function __construct(
        public string $color,
        public int $amount,
    ) {
    }

    public static function fromData(array $data): self
    {
        return new self($data[1], (int)$data[0]);
    }
}

$parse_game = static function (string $game): array
{
    preg_match('/Game (\d+): (.+)/', $game, $matches);
    $max = [
        'green' => 0,
        'blue' => 0,
        'red' => 0,
    ];

    $result = Collection::fromIterable(explode(';', $matches[2]))
        ->map(static fn (string $set) => Collection::fromIterable(explode(',', $set))
            ->map(static fn (string $cube_amount) => CubeAmount::fromData(explode(' ', trim($cube_amount))))
            ->reduce(static function (array $acc, CubeAmount $c) {
                if ($acc[$c->color] < $c->amount) {
                    $acc[$c->color] = $c->amount;
                }

                return $acc;
            }, $max)
        )->reduce(static fn($acc, $set) => [
            'green' => max([$acc['green'], $set['green']]),
            'blue' => max([$acc['blue'], $set['blue']]),
            'red' => max([$acc['red'], $set['red']]),
        ], $max);


    return ['game' => (int)$matches[1], 'result' => $result];
};

$targetCubes = ['red' => 12, 'green' => 13, 'blue' => 14];

$result = Collection::fromIterable(explode(PHP_EOL, $input))
    ->map($parse_game)
    ->filter(static fn (array $g) =>
        $g['result']['green'] <= $targetCubes['green'] &&
        $g['result']['blue'] <= $targetCubes['blue'] &&
        $g['result']['red'] <= $targetCubes['red']
    )
    ->reduce(static fn ($acc, array $g) => $acc + $g['game'], 0);

$result2 = Collection::fromIterable(explode(PHP_EOL, $input))
    ->map($parse_game)
    ->map(static fn (array $g): int => $g['result']['green'] * $g['result']['red'] * $g['result']['blue'])
    ->reduce(static fn ($acc, int $r) => $acc + $r, 0);

var_dump($result);
var_dump($result2);
