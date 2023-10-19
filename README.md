# Wikipedia ログ解析システム

Wikipedia のアクセスログ情報を使用し、サイトの読者が好む記事を発見できます。

## Wikipedia ログ解析システムでできること

#### 1. 最もビュー数の多い記事を、指定した記事数分だけビュー数が多い順にソートし、ドメインコードとページタイトル、ビュー数を提示します。

- 例）コマンドライン上で `2` 記事と指定した場合、下記を表示します。

```bash
”en”, “Main_Page”, 120
”en”, ”Wikipedia:Umnyango_wamgwamanda”, 112
```

#### 2. 指定したドメインコードに対して、人気順にソートし、ドメインコード名と合計ビュー数を提示します。

- （例）コマンドライン上で `en de` と指定した場合、下記を表示します。

```bash
”en”, 10700
”de”, 5300
```

## 必要な環境

[Docker](https://docs.docker.com/get-docker/) を使用します。

## スタートガイド

### アクセスログデータの準備

- ダウンロード

  - データは下記 URL からダウンロードできます。どれか 1 つをご使用ください。
    https://dumps.wikimedia.org/other/pageviews/

- ダウンロードしたデータを解凍します。

- `databases` ディレクトリを作成し、解凍したデータを保存します。

- 解凍したデータは `page_views` にファイル名を変更してください。これは以降に作成するテーブル名と名称を合わせるためです。

### 環境変数の作成

- `docker/db/` の下に `db-variables.env` を作成します。

```shell
# db-variables.env への記述例
MYSQL_DATABASE=log_analysis
MYSQL_PASSWORD=pass
MYSQL_ROOT_PASSWORD=pass
MYSQL_USER=user
```

- `src/` の下に `.env` を作成します。

```shell
# .env への記述例
DB_DATABASE="mysql:host=log_analysis-db-1;dbname=log_analysis;charset=utf8mb4"
DB_USERNAME="user"
DB_PASSWORD="pass"
```

### 環境構築

コマンドを実行して環境構築を行います。

#### Docker

1. Docker コンテナを起動します。

```bash
docker compose up -d --build
```

2. Docker コンテナを立ち上げた後に Composer をインストールします。

```bash
docker compose exec app composer init
```

3. [phpdotenv](https://github.com/vlucas/phpdotenv) をインストールします。

```bash
docker compose exec app composer require vlucas/phpdotenv
```

#### MySQL

1. 下記のコマンドを実行し、MySQL コンテナの中に入って作業します。

```bash
docker compose exec db mysql -p
```

2. 下記が表示されるので`pass`と入力し Enter を押します。

```sql
mysql: [Warning] World-writable config file '/etc/mysql/my.cnf' is ignored.
Enter password:
```

3. 下記のステートメントを実行し、ファイル権限を付与します。

```sql
GRANT FILE ON `log_analysis`.* TO 'user'@'%';
```

4. 下記のコマンドを実行し、MySQL を終了します。

```sql
exit;
```

### DB テーブルの作成

下記のコマンドを実行し、テーブルを作成します。これには少々時間がかかります。

```bash
docker compose exec app php database/initialize_table.php
```

#### テーブル内容

| domain_code | page_title           | count_views | total_response_size |
| ----------: | -------------------- | ----------- | ------------------- |
|          aa | Main_Page            | 4           | 0                   |
|          aa | Wikipedia            | 1           | 0                   |
|          aa | Wikipedia:Statistics | 1           | 0                   |
|        aa.b | Main_Page            | 1           | 0                   |
|        aa.d | Main_Page            | 4           | 0                   |

### ログ解析を実行する

下記のコマンドを実行し、ログ解析を開始します。

```bash
docker compose exec app php log_analysis.php
```

#### 実行サンプル

```bash
★Wikipediaのアクセスログを分析します。

1: 最もビュー数の多い記事を表示
2: 人気記事の合計ビュー数を表示
9: 終了する

上記から選択してください(1, 2, 9を選択): 1

★最もビュー数の多い記事を表示します
記事数を１以上の整数で指定してください: 5
----------------------------------------
”en.m”, ”Main_Page”, 122058
”en”, ”Main_Page”, 69181
”en”, ”Special:Search”, 26630
”de”, ”Wikipedia:Hauptseite”, 20739
”en.m”, ”Special:Search”, 19119
----------------------------------------

★Wikipediaのアクセスログを分析します。

1: 最もビュー数の多い記事を表示
2: 人気記事の合計ビュー数を表示
9: 終了する

上記から選択してください(1, 2, 9を選択): 2

★指定したドメインコードに対して、人気順にソートし、ドメインコード名と合計ビュー数を提示します
ドメインコードを指定してください(例: en de): en de
----------------------------------------
”en”, 69181
”de”, 20739
----------------------------------------
```

### ログ解析を終了する

下記のコマンドを実行し、Docker コンテナを停止します。

```bash
docker compose stop
```

## アクセスログデータについて

- データは下記 URL からダウンロードできます。どれか 1 つをご使用ください。
  https://dumps.wikimedia.org/other/pageviews/

- データについての全体の解説は、下記 URL にて行われています。
  https://dumps.wikimedia.org/other/pageviews/readme.html

- ダウンロードしたデータのテーブル定義は下記 URL で解説されています。
  https://wikitech.wikimedia.org/wiki/Analytics/Data_Lake/Traffic/Pageviews

## その他

このログ解析システムは、[独学エンジニア](https://dokugaku-engineer.com/)の課題として作成されています。
