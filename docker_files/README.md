# docker-compose.ymlを調整
platformの値は環境に応じて変更する

# コンテナ起動コマンド
$docker-compose up -d

# コンテナ停止コマンド
$docker-compose stop

# コンテナ削除コマンド
$docker-compose down

# phpの中に入る
$docker-compose exec php bash

## 初回のみマイグレーションが必要
$php artisan migrate:fresh --seed

# nginxの中に入る
$docker-compose exec nginx bash

