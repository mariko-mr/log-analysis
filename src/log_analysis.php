<?php

require_once __DIR__ . '/lib/sql.php';
require_once __DIR__ . '/lib/validate.php';

/**
 * ここを追加
 *
 */
function startAnalyze()
{
    //DBに接続
    $dbh = dbConnect();

    $validatedStdin = '';

    while ($validatedStdin !== '9') {
        executeTask(selectTask(), $dbh);
    }
}

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
 * 関数名を変更
 */
/**
 * 選択したタスクを実行する
 */
function executeTask($validatedStdin, $dbh)
{
    if ($validatedStdin === '1') {
        $validatedStdin = getTopArticles($dbh);
    } elseif ($validatedStdin === '2') {
        $validatedStdin = getDomainViews($dbh);
    }

    if ($validatedStdin === '9') {
        echo PHP_EOL . 'プログラムを終了します';
        exit();
    }
}

startAnalyze();
