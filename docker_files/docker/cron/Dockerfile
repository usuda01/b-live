# PHP-FPMのイメージをベースに構築する
FROM php:7.3.29-fpm

# cronインストール
RUN apt-get update \
    && apt-get install -y cron

RUN docker-php-ext-install pdo_mysql

# cron設定を配置する
COPY ./docker/cron/cron.root /etc/cron.d/cron

# ログを標準出力へ出力できるようにシンボリックリンクを作成する
RUN chmod 0644 /etc/cron.d/cron && ln -sf /proc/1/fd/1 /var/log/cron.log

# フォアグラウンドでcronを起動する
CMD cron -f
