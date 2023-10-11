<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/validate.php';

/**
 * DBに接続
 *
 * @return PDO $dbh
 */
function dbConnect(): PDO
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    $dbDatabase = $_ENV['DB_DATABASE'];
    $dbUserName = $_ENV['DB_USERNAME'];
    $dbPassword = $_ENV['DB_PASSWORD'];

    try {
        $dbh = new PDO($dbDatabase, $dbUserName, $dbPassword);
    } catch (PDOException $e) {
        echo '接続失敗: ' . $e->getMessage() . PHP_EOL;
        exit();
    }
    return $dbh;
}

/**
 * 最もビュー数の多い記事情報を取得し提示
 *
 * @param PDO $dbh
 */
function getTopArticles($dbh): void
{
    $sql = <<< EOT
        SELECT
            domain_code, page_title, count_views
        FROM
            page_views
        ORDER BY
            count_views DESC
        LIMIT
            :limit;
EOT;

    // 標準入力よりLIMITの取得
    $validatedLimit = getValidatedLimit();

    // クエリの実行
    try {
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':limit', $validatedLimit, PDO::PARAM_INT);
        $sth->execute();
    } catch (PDOException $e) {
        echo '取得失敗: ' . $e->getMessage() . PHP_EOL;
        exit();
    }

    showTopArticlesMsg($sth);
}

/**
 * ドメインコードと合計ビュー数を取得し提示
 *
 * @param PDO $dbh
 */
function getDomainViews($dbh): void
{
    $sql = <<< EOT
        SELECT
            domain_code, count_views
        FROM
            page_views
        WHERE
            domain_code = :domain_code
        ORDER BY
            count_views DESC
        LIMIT
            1;
EOT;

    echo    PHP_EOL .
        '★指定したドメインコードに対して、人気順にソートし、ドメインコード名と合計ビュー数を提示します' . PHP_EOL;

    // ドメインごとの合計ビュー数を格納する連想配列('en' => 10001)
    $domainViews = [];

    while (empty($domainViews)) {
        // 標準入力よりドメインコードの取得
        $domainCodes = createDomainCodesArray();

        // クエリの実行
        try {
            // 各ドメインコードごとにデータを取得
            foreach ($domainCodes as $domainCode) {
                $sth = $dbh->prepare($sql);
                $sth->bindParam(':domain_code', $domainCode, PDO::PARAM_STR);
                $sth->execute();

                // クエリの結果を取得
                $result = $sth->fetch(PDO::FETCH_ASSOC);

                // ドメインコードが存在する場合、連想配列に格納
                if ($result) {
                    $domainViews[$result['domain_code']] = $result['count_views'];
                }
            }
        } catch (PDOException $e) {
            echo '取得失敗: ' . $e->getMessage() . PHP_EOL;
            exit();
        }
    }

    showDomainViewsMsg($domainViews);
}

/**
 * バリデーション処理したLIMITを取得
 *
 * @return string $stdin
 */
function getValidatedLimit(): string
{
    echo    PHP_EOL .
        '★最もビュー数の多い記事を表示します' . PHP_EOL .
        '記事数を１以上の整数で指定してください: ';

    return validateLimit(trim(fgets(STDIN)));
}

/**
 * 最もビュー数の多い記事情報を提示
 *
 * @param PDOStatement $sth
 */
function showTopArticlesMsg($sth): void
{
    echo '----------------------------------------' . PHP_EOL;
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        echo '”' . $row['domain_code'] . '”, ' .
            '”' .   $row['page_title'] . '”, ' .
            $row['count_views'] . PHP_EOL;
    }
    echo '----------------------------------------' . PHP_EOL;
}


/**
 * 指定されたドメインコードを分割し、配列に格納
 *
 * @return array<int, string>
 */
function createDomainCodesArray(): array
{
    echo 'ドメインコードを指定してください(例: en de): ';

    return explode(' ', trim(fgets(STDIN)));
}

/**
 * ドメインコードと合計ビューを表示
 *
 * @param array<string, int> $domainViews
 */
function showDomainViewsMsg($domainViews): void
{
    echo '----------------------------------------' . PHP_EOL;
    foreach ($domainViews as $code => $views) {
        echo '”' . $code . '”, ' . $views . PHP_EOL;
    }
    echo '----------------------------------------' . PHP_EOL;
}
