# Указываем базовый образ
FROM php:8.2-fpm

# Устанавливаем необходимые расширения PHP
RUN docker-php-ext-install pdo_mysql

# Указываем рабочую директорию
WORKDIR /var/www/html

# Установка утилиты wait-for-it
ADD https://github.com/vishnubob/wait-for-it/raw/master/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

# Указываем команду запуска контейнера
CMD ["wait-for-it", "rabbitmq:5672", "--", "php", "yii", "sender/run"]