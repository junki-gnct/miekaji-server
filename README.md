# 見え家事 APIサーバー用スクリプト
## 環境構築方法
1. Docker 環境を構築する
1. docker-compose.sample.ymlをdocker-compose.ymlにリネームする。
1. docker-compose.ymlファイルのMYSQL_ROOT_PASSWORD, MYSQL_PASSWORDを任意の値に変更する。
1. www/config.sample.phpをwww/config.phpにリネームする
1. www/config.phpファイルのdb_passwordを先ほど決めたMYSQL_PASSWORDの値に変更する。
1. 下記サーバー起動コマンドを実行する。

## コマンド
```
# サーバーの起動
$ docker-compose up -d

# サーバーの停止
$ docker-compose stop

```

## 動作確認環境
- Docker 19.03.12
- Nginx
- PHP 7.2
- MySQL Server 5.7

## APIリファレンス
[Wiki](https://github.com/junki-gnct/miekaji-server/wiki)をご覧ください。