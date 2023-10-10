<?php

function validateStdin($stdin)
{
    $allowedNumbers  = ['1', '2', '9'];

    while (!(in_array($stdin, $allowedNumbers))) {
        echo '【1, 2, 9】のどれかを入力してください: ';
        $stdin = trim(fgets(STDIN));
    }
    return $stdin;
}

function validateLimit($stdin)
{
    while (!(preg_match('/\A[1-9][0-9]*\z/', $stdin))) {
        echo '記事数を１以上の整数で指定してください: ';
        $stdin = trim(fgets(STDIN));
    }

    return $stdin;
}
