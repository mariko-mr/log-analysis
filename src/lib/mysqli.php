<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../log_analysis.php';

function dbConnect()
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
 * ここを修正
 *
 * バリデーション処理を追加
 */
function getTopArticles($dbh)
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

  echo  PHP_EOL .
    '最もビュー数の多い記事を表示します' . PHP_EOL .
    '記事数を１以上の整数で指定してください: ';
  $validatedLimit = validateLimit(trim(fgets(STDIN)));

  // クエリの実行
  try {
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':limit', $validatedLimit, PDO::PARAM_INT);
    $sth->execute();
  } catch (PDOException $e) {
    echo '取得失敗: ' . $e->getMessage() . PHP_EOL;
    exit();
  }

  // 最もビュー数の多い記事を表示する
  while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
    echo '”' . $row['domain_code'] . '”, ' .
      '”' .  $row['page_title'] . '”, ' .
      $row['count_views'] . PHP_EOL;
  }

  return selectTask();
}

/**
 * ここを修正
 *
 * バリデーション処理を追加
 */
function getDomainViews($dbh)
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

  echo  PHP_EOL .
    '指定したドメインコードに対して、人気順にソートし、ドメインコード名と合計ビュー数を提示します' . PHP_EOL;

  // ドメインごとの合計ビュー数を格納する連想配列('en' => 10001)
  $domainViews = [];

  while (empty($domainViews)) {
    echo 'ドメインコードを指定してください(例: en de): ';

    // ドメインコードを分割し、配列に格納
    $domainCodes = explode(' ', trim(fgets(STDIN)));

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

  // ドメインコードと合計ビューを表示
  foreach ($domainViews as $code => $views) {
    echo '”' . $code . '”, ' . $views . PHP_EOL;
  }

  return selectTask();
}
