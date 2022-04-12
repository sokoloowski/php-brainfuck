#!/usr/bin/php
<?php

// check arguments
if ($argc < 2) {
    throw new Exception("Missing argument", 1);
}
$path = $argv[1];

// get file
if (!file_exists($path)) {
    throw new Exception("File `$path` not found", 1);
}
$file = file_get_contents($path);

// cleanup file
$code = preg_replace('/[^\>\<\+\-\.\,\[\]]/', '', $file);

// create an empty array
$array = array_fill(0, 30000, 0);
$p = 0; // pointer

function parse($code)
{
    global $array, $p;

    for ($i = 0; $i < count($code); $i++) {
        $instruction = $code[$i];

        if ($p >= count($array)) {
            throw new Exception("Array index out of bounds", 1);
        }

        if ($instruction == '>') {
            ++$p;
            continue;
        }

        if ($instruction == '<') {
            --$p;
            continue;
        }

        if ($instruction == '+') {
            ++$array[$p];
            continue;
        }

        if ($instruction == '-') {
            --$array[$p];
            continue;
        }

        if ($instruction == '.') {
            echo chr($array[$p]);
            continue;
        }

        if ($instruction == ',') {
            $array[$p] = ord(readline());
            continue;
        }

        if ($instruction == '[') {
            $b = 1;
            $pos = $i + 1;

            while ($b > 0) {
                if ($code[$pos] == '[') ++$b;
                if ($code[$pos] == ']') --$b;
                ++$pos;
            }

            while ($array[$p] != 0) {
                parse(array_slice($code, $i + 1, $pos - $i - 2));
            }

            $i = $pos - 1;

            continue;
        }
    }
}

parse(str_split($code));
