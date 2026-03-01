<?php
namespace Bucorel\F2\Utils;

class TextNormalizer
{
    private static ?\Transliterator $diacriticRemover = null;

    /**
     * Normalize text for semantic identity uniqueness.
     * Safe for multilingual systems.
     */
    public static function normalizeIdentity(
        string $text,
        bool $removeDiacritics = false
    ): string {

        // 1. Unicode normalization first (NFC)
        if (class_exists('\Normalizer')) {
            $normalized = \Normalizer::normalize($text, \Normalizer::FORM_C);
            if ($normalized !== false) {
                $text = $normalized;
            }
        }

        // 2. Trim
        $text = trim($text);

        // 3. Collapse all whitespace to single space
        $collapsed = preg_replace('/\s+/u', ' ', $text);
        if ($collapsed !== null) {
            $text = $collapsed;
        }

        // 4. Lowercase (multibyte safe)
        $text = mb_strtolower($text, 'UTF-8');

        // 5. Optional diacritic removal (NOT transliteration)
        if ($removeDiacritics) {
            $text = self::removeDiacritics($text);
        }

        return $text;
    }

    private static function removeDiacritics(string $text): string{
        if (!class_exists('\Transliterator')) {
            return $text;
        }

        if (self::$diacriticRemover === null) {
            self::$diacriticRemover = \Transliterator::create(
                'NFD; [:Nonspacing Mark:] Remove; NFC;'
            );
        }

        return self::$diacriticRemover
            ? self::$diacriticRemover->transliterate($text)
            : $text;
    }
}
