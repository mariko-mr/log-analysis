# Wikipedia ログ解析システム

Wikipedia のアクセスログ情報を使用し、サイトの読者が好む記事を発見できます。

## Wikipedia ログ解析システムでできること

#### 1. 最もビュー数の多い記事を、指定した記事数分だけビュー数が多い順にソートし、ドメインコードとページタイトル、ビュー数を提示します。

- 例）コマンドライン上で 2 記事と指定した場合、下記を表示します。
  - ”en”, “Main_Page”, 120
  - ”en”, ”Wikipedia:Umnyango_wamgwamanda”, 112

#### 2. 指定したドメインコードに対して、人気順にソートし、ドメインコード名と合計ビュー数を提示します。

- （例）コマンドライン上で「en de」と指定した場合、下記を表示します。
  - ”en”, 10700
  - ”de”, 5300

## 必要な環境

[Docker](https://docs.docker.com/get-docker/) を使用します。

## スタートガイド
### 環境構築
- Docker コンテナを起動します。
```
# Dockerの起動
$ docker compose up -d --build
```

- Composer をインストールします。
```
#Dockerコンテナを立ち上げた後にComposerをインストール
$ docker compose exec app composer init
```

- [phpdotenv](https://github.com/vlucas/phpdotenv) をインストールします。
```
$ docker compose exec app composer require vlucas/phpdotenv
```

### 環境変数の作成

- docker/db/db-variables.env を作成します。
```
# db-variables.env への記述例
MYSQL_DATABASE=log_analysis
MYSQL_PASSWORD=pass
MYSQL_ROOT_PASSWORD=pass
MYSQL_USER=user
```

- src/.env を作成します。
```
DB_DATABASE="mysql:host=log_analysis-db-1;dbname=log_analysis;charset=utf8mb4"
DB_USERNAME="user"
DB_PASSWORD="pass"
```

### アクセスログデータの準備

- ダウンロード
  - データは下記 URL からダウンロードできます。どれか 1 つをご使用ください。
https://dumps.wikimedia.org/other/pageviews/

- ダウンロードしたデータを解凍します。

- databases ディレクトリを作成し、解凍したデータを保存します。

- 解凍したデータは page_views にファイル名を変更してください。これは以降に作成するテーブル名と名称を合わせるためです。

### DB データベースとテーブルの作成

下記のコマンドを実行しデータベースとテーブルを作成します。
```
docker compose exec app php database/initialize_table.php
```

### ログ解析を起動する

```
docker compose exec app php log_analysis.php
```

#### 実行サンプル

![使用画面](image.png)

### ログ解析を終了する

```
# Dockerコンテナを停止
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
