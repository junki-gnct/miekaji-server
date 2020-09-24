# 見え家事 APIサーバー用スクリプト
procon31/自由部門/見え家事のAPIサーバースクリプトです。
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
- Windows 10 Pro 64-bit

## APIリファレンス
[Wiki](https://github.com/junki-gnct/miekaji-server/wiki)をご覧ください。
