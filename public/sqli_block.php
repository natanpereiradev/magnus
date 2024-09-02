<?php

function isSafeInput($input) {
    $sqlPatterns = [
        "/\bSELECT\b/i",
        "/\bINSERT\b/i",
        "/\bUPDATE\b/i",
        "/\bDELETE\b/i",
        "/\bDROP\b/i",
        "/\bALTER\b/i",
        "/\bTRUNCATE\b/i",
        "/\bUNION\b/i",
        "/\bOR\b/i",
        "/\bAND\b/i",
        "/\bWHERE\b/i",
        "/\bFROM\b/i",
        "/\bJOIN\b/i",
        "/\bLIKE\b/i",
        "/\bHAVING\b/i",
        "/\bIN\b/i"
    ];
    
    $decodedInput = urldecode($input);

    foreach ($sqlPatterns as $pattern) {
        if (preg_match($pattern, $decodedInput)) {
            return false;
        }
    }

    return true;
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isNotEmpty($value) {
    return !empty(trim($value));
}

function ensureUtf8($string) {
    return mb_detect_encoding($string, 'UTF-8', true) ? $string : mb_convert_encoding($string, 'UTF-8', 'auto');
}
