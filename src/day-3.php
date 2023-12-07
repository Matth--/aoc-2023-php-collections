<?php

declare(strict_types=1);

use loophp\collection\Collection;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$input = file_get_contents(dirname(__DIR__). '/input/day-3.txt');
$exampleInput = <<<TEXT
467..114..
...*......
..35..633.
......#...
617*......
.....+.58.
..592.....
......755.
...$.*....
.664.598..
TEXT;

final readonly class PartNumber {
    /** @param array{start: int, end: int} $lookup */
    private function __construct(
        public int $partNumber,
        public array $lookup,
        public int $lineNo,
    ) {}

    /** @param array{0: string, 1: int} $partsInfo */
    public static function fromData(array $partsInfo, int $lineNo): self
    {
        return new self(
            (int)$partsInfo[0],
            [
                'start' => max(0, ($partsInfo[1] - 1)),
                'end' => min(($partsInfo[1] + strlen($partsInfo[0])), 139),
            ],
            $lineNo,
        );
    }
}

final readonly class Gear
{
    public function __construct(
        public int $lineNo,
        public int $position,
    ) {
    }
}

$checkLineRangeForSymbol = static function (PartNumber $p, ?string $line): bool
{
    if (!$line) {
        return false;
    }

    $chars = substr($line, $p->lookup['start'], $p->lookup['end'] + 1 - $p->lookup['start']);
    return preg_match('/^.*[^.0-9].*$/', $chars) >= 1;
};

$getParts = static function (string $line, int $lineNo) {
    preg_match_all('/\d+/', $line, $matches, PREG_OFFSET_CAPTURE);

    return Collection::fromIterable($matches[0])
        ->map(static fn(array $p) => PartNumber::fromData($p, $lineNo))
        ->all();
};

$getGearSymbols = static function (string $line, int $lineNo) {
    preg_match_all('/\*/', $line, $matches, PREG_OFFSET_CAPTURE);
    return Collection::fromIterable($matches[0])
        ->map(static fn(array $g) => new Gear($lineNo, $g[1]))
        ->all();
};

$partOne = static function (string $input) use ($checkLineRangeForSymbol, $getParts): int {
    $lines = explode(PHP_EOL, $input);
    return Collection::fromIterable($lines)
        ->flatMap($getParts)
        ->filter(static fn(PartNumber $p) =>
            $checkLineRangeForSymbol($p, $lines[$p->lineNo - 1] ?? null) ||
            $checkLineRangeForSymbol($p, $lines[$p->lineNo]) ||
            $checkLineRangeForSymbol($p, $lines[$p->lineNo + 1] ?? null)
        )
        ->reduce(static fn(int $carry, PartNumber $p) => $carry + $p->partNumber, 0);
};

$partTwo = static function (string $input) use ($getParts, $getGearSymbols): int {
    $lines = explode(PHP_EOL, $input);
    $parts = Collection::fromIterable($lines)
        ->flatMap($getParts);

    return Collection::fromIterable($lines)
        ->flatMap($getGearSymbols)
        ->map(static function (Gear $g) use ($parts) {
            $linkedParts = $parts
                ->filter(static fn(PartNumber $p) =>
                    in_array($g->lineNo, range($p->lineNo -1, $p->lineNo + 1)) &&
                    in_array($g->position, range($p->lookup['start'], $p->lookup['end'])))
                ->all();

            if (count($linkedParts) === 2) {
                return $linkedParts[0]->partNumber * $linkedParts[1]->partNumber;
            }

            return 0;
        })
        ->reduce(static fn (int $carry, int $gearParts) => $carry + $gearParts, 0);
};

var_dump($partOne($exampleInput)); // 4361
var_dump($partOne($input));

var_dump($partTwo($exampleInput)); // 467835
var_dump($partTwo($input));
