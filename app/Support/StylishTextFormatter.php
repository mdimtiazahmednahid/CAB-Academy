<?php

namespace App\Support;

class StylishTextFormatter
{
    /**
     * Convert ASCII letters and digits into Unicode Mathematical Bold Serif
     * Example: "PGD IN SUPPLY CHAIN MANAGEMENT" -> "𝐏𝐆𝐃 𝐈𝐍 𝐒𝐔𝐏𝐏𝐋𝐘 𝐂𝐇𝐀𝐈𝐍 𝐌𝐀𝐍𝐀𝐆𝐄𝐌𝐄𝐍𝐓"
     * Example: "Starting 30 October" -> "𝐒𝐭𝐚𝐫𝐭𝐢𝐧𝐠 𝟑𝟎 𝐎𝐜𝐭𝐨𝐛𝐞𝐫"
     * Preserves URLs (http/https), HTML entities, punctuation, emojis, and Bengali characters.
     */
    public static function toBoldSerif(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        // Split by URLs so we never mangle clickable links
        $parts = preg_split('/(https?:\/\/[^\s<>"\']+)/i', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if ($parts === false) {
            return self::convertSegmentToBoldSerif($text);
        }

        $result = '';
        foreach ($parts as $index => $part) {
            if ($index % 2 === 1 && preg_match('/^https?:\/\//i', $part)) {
                $result .= $part;
            } else {
                $result .= self::convertSegmentToBoldSerif($part);
            }
        }

        return $result;
    }

    /**
     * Convert a non-URL text segment to Unicode Mathematical Bold Serif (𝐀-𝐙, 𝐚-𝐳, 𝟎-𝟗).
     */
    protected static function convertSegmentToBoldSerif(string $segment): string
    {
        $chars = preg_split('//u', $segment, -1, PREG_SPLIT_NO_EMPTY);
        if (!$chars) {
            return $segment;
        }

        $out = '';
        foreach ($chars as $ch) {
            $code = mb_ord($ch, 'UTF-8');
            if ($code >= 0x41 && $code <= 0x5A) {
                // A-Z -> U+1D400 .. U+1D419
                $out .= mb_chr(0x1D400 + ($code - 0x41), 'UTF-8');
            } elseif ($code >= 0x61 && $code <= 0x7A) {
                // a-z -> U+1D41A .. U+1D433
                $out .= mb_chr(0x1D41A + ($code - 0x61), 'UTF-8');
            } elseif ($code >= 0x30 && $code <= 0x39) {
                // 0-9 -> U+1D7CE .. U+1D7D7
                $out .= mb_chr(0x1D7CE + ($code - 0x30), 'UTF-8');
            } else {
                $out .= $ch;
            }
        }

        return $out;
    }

    /**
     * Convert text to Uppercase Unicode Mathematical Bold Serif
     * Example: "PGD in Supply Chain Management" -> "𝐏𝐆𝐃 𝐈𝐍 𝐒𝐔𝐏𝐏𝐋𝐘 𝐂𝐇𝐀𝐈𝐍 𝐌𝐀𝐍𝐀𝐆𝐄𝐌𝐄𝐍𝐓"
     */
    public static function toUpperBoldSerif(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        return self::toBoldSerif(mb_strtoupper($text, 'UTF-8'));
    }

    /**
     * Convert ASCII letters and digits into Unicode Mathematical Sans-Serif Bold (𝗔-𝗭, 𝗮-𝘇, 𝟬-𝟵).
     */
    public static function toBoldSans(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (!$chars) {
            return $text;
        }

        $out = '';
        foreach ($chars as $ch) {
            $code = mb_ord($ch, 'UTF-8');
            if ($code >= 0x41 && $code <= 0x5A) {
                $out .= mb_chr(0x1D5D4 + ($code - 0x41), 'UTF-8');
            } elseif ($code >= 0x61 && $code <= 0x7A) {
                $out .= mb_chr(0x1D5EE + ($code - 0x61), 'UTF-8');
            } elseif ($code >= 0x30 && $code <= 0x39) {
                $out .= mb_chr(0x1D7EC + ($code - 0x30), 'UTF-8');
            } else {
                $out .= $ch;
            }
        }

        return $out;
    }

    /**
     * Normalize Unicode Mathematical Bold Serif/Sans characters back to standard ASCII.
     */
    public static function toPlainAscii(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if (!$chars) {
            return $text;
        }

        $out = '';
        foreach ($chars as $ch) {
            $code = mb_ord($ch, 'UTF-8');
            if ($code >= 0x1D400 && $code <= 0x1D419) {
                $out .= chr(0x41 + ($code - 0x1D400));
            } elseif ($code >= 0x1D41A && $code <= 0x1D433) {
                $out .= chr(0x61 + ($code - 0x1D41A));
            } elseif ($code >= 0x1D7CE && $code <= 0x1D7D7) {
                $out .= chr(0x30 + ($code - 0x1D7CE));
            } elseif ($code >= 0x1D5D4 && $code <= 0x1D5ED) {
                $out .= chr(0x41 + ($code - 0x1D5D4));
            } elseif ($code >= 0x1D5EE && $code <= 0x1D607) {
                $out .= chr(0x61 + ($code - 0x1D5EE));
            } elseif ($code >= 0x1D7EC && $code <= 0x1D7F5) {
                $out .= chr(0x30 + ($code - 0x1D7EC));
            } else {
                $out .= $ch;
            }
        }

        return $out;
    }
}

