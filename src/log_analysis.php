<?php

require_once __DIR__ . '/lib/mysqli.php';

$dbh = dbConnect();
$validatedStdin = selectTask();
startAnalyze($validatedStdin, $dbh);

/**
 * ここを修正
 * バリデーション処理を追加
 *
 */
/**
 * タスクを選択する
 *
 * @return string $stdin
 */
function selectTask(): string
{
    echo  PHP_EOL .
        'Wikipediaのアクセスログを分析します。' . PHP_EOL . PHP_EOL .
        '1: 最もビュー数の多い記事を表示' . PHP_EOL .
        '2: 人気記事の合計ビュー数を表示' . PHP_EOL .
        '9: 終了する' . PHP_EOL . PHP_EOL .
        '上記から選択してください(1, 2, 9を選択): ';
    $stdin = trim(fgets(STDIN));

    return validateStdin($stdin);
}

/**
 * ここを修正
 *
 * 1, 2を選択している間はループするようにする
 */
function startAnalyze($validatedStdin, $dbh)
{
    while ($validatedStdin !== '9') {
        if ($validatedStdin === '1') {
            $validatedStdin = getTopArticles($dbh);
        } elseif ($validatedStdin === '2') {
            $validatedStdin = getDomainViews($dbh);
        }
    }

    if ($validatedStdin === '9') {
        echo PHP_EOL . 'プログラムを終了します';
        exit();
    }
}

/**
 * ここを追加
 */
function validateStdin($stdin)
{
    $allowedNumbers  = ['1', '2', '9'];

    // 入力値のバリデーション処理
    while (!(in_array($stdin, $allowedNumbers))) {
        echo '【1, 2, 9】のどれかを入力してください: ';
        $stdin = trim(fgets(STDIN));
    }
    return $stdin;
}

/**
 * ここを追加
 */
function validateLimit($stdin)
{
    // バリデーション処理を追加する
    while (!(preg_match('/\A[1-9][0-9]*\z/', $stdin))) {
        echo '記事数を１以上の整数で指定してください: ';
        $stdin = trim(fgets(STDIN));
    }

    return $stdin;
}
