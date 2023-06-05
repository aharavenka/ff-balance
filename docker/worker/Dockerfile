# Используем базовый образ с PHP CLI
FROM php:8.2-cli

# Устанавливаем необходимые расширения PHP
RUN docker-php-ext-install pdo_mysql

# Устанавливаем Composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Устанавливаем рабочую директорию
WORKDIR /app

# Копирование конфигурационных файлов
#COPY .env.example .env
#COPY yii /usr/local/bin/yii
#COPY config/ /app/config/
#COPY commands/ /app/commands/
#COPY models/ /app/models/

# Установка утилиты wait-for-it
ADD https://github.com/vishnubob/wait-for-it/raw/master/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod +x /usr/local/bin/wait-for-it

ENV QUEUE_NAME=default

# Команда для запуска воркера
CMD wait-for-it rabbitmq:5672 -- php yii worker/run $QUEUE_NAME
