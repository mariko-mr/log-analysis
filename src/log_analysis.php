<?php

require_once __DIR__ . '/lib/mysqli.php';

// データベースに接続する
$dbh = dbConnect();

/**
 * ここを修正
 * 「9: 終了する」を追加
 */
// タスクを選択
echo 'Wikipediaのアクセスログを分析します。' . PHP_EOL . PHP_EOL .
    '1: 最もビュー数の多い記事を表示' . PHP_EOL .
    '2: 人気記事の合計ビュー数を表示' . PHP_EOL .
    '9: 終了する' . PHP_EOL . PHP_EOL .
    '上記から選択してください(1, 2, 9を選択): ';
$stdin = trim(fgets(STDIN));

// TODO: 1, 2を選択している間はループするようにする
// TODO: バリデーション処理をする
if ($stdin === '1') {
    // 最もビュー数の多い記事を表示
    getTopArticles($dbh);
} elseif ($stdin === '2') {
    // 人気記事の合計ビュー数を表示
    getDomainViews($dbh);
}
exit();
