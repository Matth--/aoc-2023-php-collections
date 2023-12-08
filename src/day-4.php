<?php

declare(strict_types=1);

use loophp\collection\Collection;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$input = file_get_contents(dirname(__DIR__). '/input/day-4.txt');
$exampleInput = <<<TEXT
Card 1: 41 48 83 86 17 | 83 86  6 31 17  9 48 53
Card 2: 13 32 20 16 61 | 61 30 68 82 17 32 24 19
Card 3:  1 21 53 59 44 | 69 82 63 72 16 21 14  1
Card 4: 41 92 73 84 69 | 59 84 76 51 58  5 54 83
Card 5: 87 83 26 28 32 | 88 30 70 12 93 22 82 36
Card 6: 31 18 13 56 72 | 74 77 10 23 35 67 36 11
TEXT;

final readonly class ScratchCard {
    public int $winningNumbers;
    public function __construct (
        public int $game,
        public array $numbers,
        public array $results,
    ) {
        $this->winningNumbers = count(array_intersect($this->numbers, $this->results));
    }
}

$parseScratchCard = static function (string $line): ScratchCard {
    $split_l = preg_split('/[:|]/', $line);

    preg_match('/\d+/', $split_l[0], $game_match);
    preg_match_all('/\d+/', $split_l[1], $numberMatches);
    preg_match_all('/\d+/', $split_l[2], $resultMatches);

    return new ScratchCard((int) $game_match[0], $numberMatches[0], $resultMatches[0]);
};

$partOne = static fn(string $input): int => Collection::fromIterable(explode(PHP_EOL, $input))
    ->map($parseScratchCard)
    ->map(static function (ScratchCard $card): int {
        if ($card->winningNumbers === 0) {
            return 0;
        }

        return pow(2, $card->winningNumbers - 1);
    })
    ->reduce(static fn ($carry, int $r) => $carry + $r, 0);

$partTwo = static function (string $input) use ($parseScratchCard): int  {
    $winningCounts = Collection::fromIterable(explode(PHP_EOL, $input))
        ->map($parseScratchCard)
        ->reduce(static function (array $counts, ScratchCard $card) {
            $counts[$card->game] = ($counts[$card->game] ?? 0) + 1;

            if ($card->winningNumbers)
            {
                for ($i = $card->game + 1; $i <= $card->game + $card->winningNumbers; $i++) {
                    $counts[$i] = ($counts[$i] ?? 0) + $counts[$card->game];
                }
            }

            return $counts;
        }, []);

    return array_sum($winningCounts);
};


var_dump($partOne($exampleInput)); // 13
var_dump($partOne($input)); // 26443

var_dump($partTwo($exampleInput)); // 30
var_dump($partTwo($input)); // 6284877
