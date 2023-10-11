<?php

require_once __DIR__ . '/lib/sql.php';
require_once __DIR__ . '/lib/validate.php';

/**
 * 解析を開始する
 *
 */
function startAnalyze(): void
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
 * @return string $validatedStdin
 */
function selectTask(): string
{
    echo  PHP_EOL .
        '★Wikipediaのアクセスログを分析します。' . PHP_EOL . PHP_EOL .
        '1: 最もビュー数の多い記事を表示' . PHP_EOL .
        '2: 人気記事の合計ビュー数を表示' . PHP_EOL .
        '9: 終了する' . PHP_EOL . PHP_EOL .
        '上記から選択してください(1, 2, 9を選択): ';
    $stdin = trim(fgets(STDIN));

    return validateStdin($stdin);
}

/**
 * 選択したタスクを実行する
 *
 * @param string $validatedStdin
 * @param PDO $dbh
 */
function executeTask($validatedStdin, $dbh): void
{
    if ($validatedStdin === '1') {
        getTopArticles($dbh);
    } elseif ($validatedStdin === '2') {
        getDomainViews($dbh);
    }

    if ($validatedStdin === '9') {
        echo PHP_EOL . 'プログラムを終了します';
        exit();
    }
}

startAnalyze();
