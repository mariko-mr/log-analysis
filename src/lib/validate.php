<?php

/**
 *
 *
 * @param string $stdin
 * @return string $validatedStdin
 */
function validateStdin($stdin): string
{
    $allowedNumbers  = ['1', '2', '9'];

    while (!(in_array($stdin, $allowedNumbers))) {
        echo '【1, 2, 9】のどれかを入力してください: ';
        $stdin = trim(fgets(STDIN));
    }

    $validatedStdin = $stdin;
    return $validatedStdin;
}

/**
 *
 *
 * @param string $stdin
 * @return string $validatedStdin
 */
function validateLimit($stdin): string
{
    while (!(preg_match('/\A[1-9][0-9]*\z/', $stdin))) {
        echo '記事数を１以上の整数で指定してください: ';
        $stdin = trim(fgets(STDIN));
    }

    $validatedStdin = $stdin;
    return $validatedStdin;
}
