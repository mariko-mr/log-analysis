<?php

require_once __DIR__ . '/../lib/sql.php';

/**
 * ここを修正
 * createDatabase($dbh)を削除
 * changeDatabase($dbh)を削除
 */
$dbh = dbConnect();
createTable($dbh);
loadWikiLog($dbh);

function createTable($dbh)
{
    $sql = <<< EOT
    CREATE TABLE IF NOT EXISTS page_views
        (domain_code        VARCHAR(20)  NOT NULL,
        page_title          VARCHAR(300) NOT NULL,
        count_views         INTEGER      NOT NULL,
        total_response_size INTEGER      NOT NULL,
        PRIMARY KEY (domain_code, page_title)
        )COLLATE utf8mb4_0900_bin;
    EOT;

    try {
        $dbh->exec($sql);
        echo "テーブル 'page_views' の作成に成功しました。" . PHP_EOL;
    } catch (PDOException $e) {
        echo "テーブル 'page_views' の作成に失敗しました。: " . $e->getMessage() . PHP_EOL;
        exit();
    }
}

function loadWikiLog($dbh)
{
    $sql = <<< EOT
    LOAD DATA INFILE '/var/lib/mysql-files/page_views' IGNORE
    INTO TABLE page_views
        FIELDS TERMINATED BY ' '
        LINES  TERMINATED BY '\n'
        (@var1, @var2, @var3, @var4)
    SET domain_code = @var1,
        page_title  = @var2,
        count_views = @var3,
        total_response_size = @var4;
EOT;

    try {
        $dbh->exec($sql);
        echo 'データの読み込みに成功しました。' . PHP_EOL;
    } catch (PDOException $e) {
        echo 'データの読み込みに失敗しました。: ' . $e->getMessage() . PHP_EOL;
        exit();
    }
}
