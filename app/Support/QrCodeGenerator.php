<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Zero-dependency ISO/IEC 18004 QR Code SVG generator (Byte mode, ECC Level L, Versions 1-6).
 * Safe for shared hosting without Composer QR packages or external APIs.
 */
class QrCodeGenerator
{
    /**
     * Version parameters for ECC Level L (Byte mode):
     * version => [size, dataCodewords, eccCodewords, blocks, alignmentCenters]
     */
    private const VERSIONS = [
        1 => [21, 19, 7, 1, []],
        2 => [25, 34, 10, 1, [6, 18]],
        3 => [29, 55, 15, 1, [6, 22]],
        4 => [33, 80, 20, 1, [6, 26]],
        5 => [37, 108, 26, 1, [6, 30]],
        6 => [41, 136, 36, 2, [6, 34]],
    ];

    public static function svg(string $text, string $fg = '#000000', string $bg = '#ffffff', int $margin = 2): string
    {
        $matrix = self::encode($text);
        $n = count($matrix);
        $total = $n + ($margin * 2);

        $paths = [];
        for ($r = 0; $r < $n; $r++) {
            $c = 0;
            while ($c < $n) {
                if ($matrix[$r][$c]) {
                    $start = $c;
                    while ($c < $n && $matrix[$r][$c]) {
                        $c++;
                    }
                    $w = $c - $start;
                    $x = $start + $margin;
                    $y = $r + $margin;
                    $paths[] = "M{$x} {$y}h{$w}v1h-{$w}z";
                } else {
                    $c++;
                }
            }
        }

        $d = implode('', $paths);

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $total . ' ' . $total . '" class="w-full h-full" shape-rendering="crispEdges" role="img" aria-label="QR Code">'
            . '<rect width="' . $total . '" height="' . $total . '" fill="' . htmlspecialchars($bg, ENT_QUOTES, 'UTF-8') . '"/>'
            . '<path d="' . $d . '" fill="' . htmlspecialchars($fg, ENT_QUOTES, 'UTF-8') . '"/>'
            . '</svg>';
    }

    /**
     * @return array<int, array<int, bool>>
     */
    public static function encode(string $text): array
    {
        $len = strlen($text);
        $version = self::selectVersion($len);
        [$size, $dataCapacity, $totalEcc, $numBlocks, $alignCenters] = self::VERSIONS[$version];

        // 1. Build bit stream: Byte mode (0100) + 8-bit length + bytes + terminator + padding
        $bits = [];
        self::appendBits($bits, 0b0100, 4);
        self::appendBits($bits, $len, 8);
        for ($i = 0; $i < $len; $i++) {
            self::appendBits($bits, ord($text[$i]), 8);
        }

        $capacityBits = $dataCapacity * 8;
        $terminatorLen = min(4, $capacityBits - count($bits));
        self::appendBits($bits, 0, $terminatorLen);

        while (count($bits) % 8 !== 0) {
            $bits[] = 0;
        }

        $dataBytes = [];
        for ($i = 0; $i < count($bits); $i += 8) {
            $byte = 0;
            for ($b = 0; $b < 8; $b++) {
                $byte = ($byte << 1) | $bits[$i + $b];
            }
            $dataBytes[] = $byte;
        }

        $padBytes = [0xEC, 0x11];
        $padIdx = 0;
        while (count($dataBytes) < $dataCapacity) {
            $dataBytes[] = $padBytes[$padIdx % 2];
            $padIdx++;
        }

        // 2. Split into blocks and compute Reed-Solomon ECC
        $eccPerBlock = intdiv($totalEcc, $numBlocks);
        $blockSize = intdiv($dataCapacity, $numBlocks);
        $dataBlocks = [];
        $eccBlocks = [];
        $offset = 0;

        for ($b = 0; $b < $numBlocks; $b++) {
            $block = array_slice($dataBytes, $offset, $blockSize);
            $offset += $blockSize;
            $dataBlocks[] = $block;
            $eccBlocks[] = self::reedSolomonCompute($block, $eccPerBlock);
        }

        // Interleave data + ECC codewords
        $codewords = [];
        for ($i = 0; $i < $blockSize; $i++) {
            for ($b = 0; $b < $numBlocks; $b++) {
                $codewords[] = $dataBlocks[$b][$i];
            }
        }
        for ($i = 0; $i < $eccPerBlock; $i++) {
            for ($b = 0; $b < $numBlocks; $b++) {
                $codewords[] = $eccBlocks[$b][$i];
            }
        }

        // 3. Build function modules grid
        $modules = array_fill(0, $size, array_fill(0, $size, false));
        $isFunction = array_fill(0, $size, array_fill(0, $size, false));

        self::drawFinderPattern($modules, $isFunction, 3, 3, $size);
        self::drawFinderPattern($modules, $isFunction, $size - 4, 3, $size);
        self::drawFinderPattern($modules, $isFunction, 3, $size - 4, $size);

        // Timing patterns
        for ($i = 8; $i < $size - 8; $i++) {
            $val = ($i % 2 === 0);
            self::setFunctionModule($modules, $isFunction, 6, $i, $val);
            self::setFunctionModule($modules, $isFunction, $i, 6, $val);
        }

        // Alignment patterns
        $numAlign = count($alignCenters);
        for ($i = 0; $i < $numAlign; $i++) {
            for ($j = 0; $j < $numAlign; $j++) {
                if (($i === 0 && $j === 0) || ($i === 0 && $j === $numAlign - 1) || ($i === $numAlign - 1 && $j === 0)) {
                    continue;
                }
                self::drawAlignmentPattern($modules, $isFunction, $alignCenters[$i], $alignCenters[$j]);
            }
        }

        // Reserve format info areas & dark module
        self::drawFormatBits($modules, $isFunction, 0, $size, true);

        // 4. Place data bits in zig-zag order
        $allBits = [];
        foreach ($codewords as $cw) {
            self::appendBits($allBits, $cw, 8);
        }

        $bitIdx = 0;
        $totalBits = count($allBits);
        for ($right = $size - 1; $right >= 1; $right -= 2) {
            if ($right === 6) {
                $right = 5;
            }
            for ($vert = 0; $vert < $size; $vert++) {
                for ($j = 0; $j < 2; $j++) {
                    $x = $right - $j;
                    $upward = ((($right + 1) & 2) === 0);
                    $y = $upward ? ($size - 1 - $vert) : $vert;
                    if (!$isFunction[$y][$x] && $bitIdx < $totalBits) {
                        $modules[$y][$x] = ($allBits[$bitIdx] === 1);
                        $bitIdx++;
                    }
                }
            }
        }

        // 5. Evaluate all 8 masks and pick the lowest penalty
        $bestMask = 0;
        $minPenalty = PHP_INT_MAX;
        for ($mask = 0; $mask < 8; $mask++) {
            $candidate = $modules;
            self::applyMask($candidate, $isFunction, $mask, $size);
            self::drawFormatBits($candidate, $isFunction, $mask, $size, false);
            $penalty = self::calculatePenalty($candidate, $size);
            if ($penalty < $minPenalty) {
                $minPenalty = $penalty;
                $bestMask = $mask;
            }
        }

        self::applyMask($modules, $isFunction, $bestMask, $size);
        self::drawFormatBits($modules, $isFunction, $bestMask, $size, false);

        return $modules;
    }

    private static function selectVersion(int $byteLength): int
    {
        foreach (self::VERSIONS as $ver => [, $dataCapacity]) {
            // 4-bit mode + 8-bit length = 2 bytes overhead (rounded up)
            if ($byteLength + 2 <= $dataCapacity) {
                return $ver;
            }
        }

        throw new InvalidArgumentException('QR payload exceeds maximum supported length (134 bytes).');
    }

    private static function appendBits(array &$bits, int $val, int $len): void
    {
        for ($i = $len - 1; $i >= 0; $i--) {
            $bits[] = ($val >> $i) & 1;
        }
    }

    /**
     * @param array<int, int> $data
     * @return array<int, int>
     */
    private static function reedSolomonCompute(array $data, int $degree): array
    {
        // Generator polynomial coefficients
        $gen = [1];
        $root = 1;
        for ($i = 0; $i < $degree; $i++) {
            $next = array_fill(0, count($gen) + 1, 0);
            for ($j = 0; $j < count($gen); $j++) {
                $next[$j] ^= $gen[$j];
                $next[$j + 1] ^= self::gfMultiply($gen[$j], $root);
            }
            $gen = $next;
            $root = self::gfMultiply($root, 0x02);
        }

        $result = array_fill(0, $degree, 0);
        foreach ($data as $b) {
            $factor = $b ^ $result[0];
            array_shift($result);
            $result[] = 0;
            for ($i = 0; $i < $degree; $i++) {
                $result[$i] ^= self::gfMultiply($gen[$i + 1], $factor);
            }
        }

        return $result;
    }

    private static function gfMultiply(int $x, int $y): int
    {
        $z = 0;
        for ($i = 7; $i >= 0; $i--) {
            $z = ($z << 1) ^ (($z >> 7) * 0x11D);
            $z ^= (($y >> $i) & 1) * $x;
        }

        return $z;
    }

    private static function setFunctionModule(array &$modules, array &$isFunction, int $x, int $y, bool $val): void
    {
        $modules[$y][$x] = $val;
        $isFunction[$y][$x] = true;
    }

    private static function drawFinderPattern(array &$modules, array &$isFunction, int $cx, int $cy, int $size): void
    {
        for ($dy = -4; $dy <= 4; $dy++) {
            for ($dx = -4; $dx <= 4; $dx++) {
                $xx = $cx + $dx;
                $yy = $cy + $dy;
                if ($xx >= 0 && $xx < $size && $yy >= 0 && $yy < $size) {
                    $dist = max(abs($dx), abs($dy));
                    $isDark = ($dist !== 2 && $dist !== 4);
                    self::setFunctionModule($modules, $isFunction, $xx, $yy, $isDark);
                }
            }
        }
    }

    private static function drawAlignmentPattern(array &$modules, array &$isFunction, int $cx, int $cy): void
    {
        for ($dy = -2; $dy <= 2; $dy++) {
            for ($dx = -2; $dx <= 2; $dx++) {
                $dist = max(abs($dx), abs($dy));
                self::setFunctionModule($modules, $isFunction, $cx + $dx, $cy + $dy, $dist !== 1);
            }
        }
    }

    private static function drawFormatBits(array &$modules, array &$isFunction, int $mask, int $size, bool $reserveOnly): void
    {
        // ECC Level L = 0b01
        $data = (0b01 << 3) | $mask;
        $rem = $data;
        for ($i = 0; $i < 10; $i++) {
            $rem = ($rem << 1) ^ (($rem >> 9) * 0x537);
        }
        $bits = (($data << 10) | $rem) ^ 0x5412;

        for ($i = 0; $i <= 5; $i++) {
            $val = !$reserveOnly && ((($bits >> $i) & 1) !== 0);
            self::setFunctionModule($modules, $isFunction, 8, $i, $val);
        }
        self::setFunctionModule($modules, $isFunction, 8, 7, !$reserveOnly && ((($bits >> 6) & 1) !== 0));
        self::setFunctionModule($modules, $isFunction, 8, 8, !$reserveOnly && ((($bits >> 7) & 1) !== 0));
        self::setFunctionModule($modules, $isFunction, 7, 8, !$reserveOnly && ((($bits >> 8) & 1) !== 0));
        for ($i = 9; $i < 15; $i++) {
            $val = !$reserveOnly && ((($bits >> $i) & 1) !== 0);
            self::setFunctionModule($modules, $isFunction, 14 - $i, 8, $val);
        }

        for ($i = 0; $i < 8; $i++) {
            $val = !$reserveOnly && ((($bits >> $i) & 1) !== 0);
            self::setFunctionModule($modules, $isFunction, $size - 1 - $i, 8, $val);
        }
        for ($i = 8; $i < 15; $i++) {
            $val = !$reserveOnly && ((($bits >> $i) & 1) !== 0);
            self::setFunctionModule($modules, $isFunction, 8, $size - 15 + $i, $val);
        }
        self::setFunctionModule($modules, $isFunction, 8, $size - 8, true);
    }

    private static function applyMask(array &$modules, array $isFunction, int $mask, int $size): void
    {
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                if ($isFunction[$y][$x]) {
                    continue;
                }
                $invert = match ($mask) {
                    0 => ($x + $y) % 2 === 0,
                    1 => $y % 2 === 0,
                    2 => $x % 3 === 0,
                    3 => ($x + $y) % 3 === 0,
                    4 => (intdiv($x, 3) + intdiv($y, 2)) % 2 === 0,
                    5 => (($x * $y) % 2) + (($x * $y) % 3) === 0,
                    6 => ((($x * $y) % 2) + (($x * $y) % 3)) % 2 === 0,
                    7 => ((($x + $y) % 2) + (($x * $y) % 3)) % 2 === 0,
                    default => false,
                };
                if ($invert) {
                    $modules[$y][$x] = !$modules[$y][$x];
                }
            }
        }
    }

    private static function calculatePenalty(array $modules, int $size): int
    {
        $penalty = 0;

        // Horizontal & vertical runs
        for ($y = 0; $y < $size; $y++) {
            $runColor = $modules[$y][0];
            $runLen = 1;
            for ($x = 1; $x < $size; $x++) {
                if ($modules[$y][$x] === $runColor) {
                    $runLen++;
                } else {
                    if ($runLen >= 5) {
                        $penalty += 3 + ($runLen - 5);
                    }
                    $runColor = $modules[$y][$x];
                    $runLen = 1;
                }
            }
            if ($runLen >= 5) {
                $penalty += 3 + ($runLen - 5);
            }
        }

        for ($x = 0; $x < $size; $x++) {
            $runColor = $modules[0][$x];
            $runLen = 1;
            for ($y = 1; $y < $size; $y++) {
                if ($modules[$y][$x] === $runColor) {
                    $runLen++;
                } else {
                    if ($runLen >= 5) {
                        $penalty += 3 + ($runLen - 5);
                    }
                    $runColor = $modules[$y][$x];
                    $runLen = 1;
                }
            }
            if ($runLen >= 5) {
                $penalty += 3 + ($runLen - 5);
            }
        }

        // 2x2 blocks
        for ($y = 0; $y < $size - 1; $y++) {
            for ($x = 0; $x < $size - 1; $x++) {
                $c = $modules[$y][$x];
                if ($c === $modules[$y][$x + 1] && $c === $modules[$y + 1][$x] && $c === $modules[$y + 1][$x + 1]) {
                    $penalty += 3;
                }
            }
        }

        return $penalty;
    }
}
