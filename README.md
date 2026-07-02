# Smarty PHP Blog

Простой блог на чистом PHP 8.1+ без фреймворков: Smarty, MySQL, PDO, SCSS и Docker (`php:8.1-fpm` + Nginx).

## Запуск

1. Соберите и запустите контейнеры:

```bash
docker compose up -d --build
```

2. Установите PHP-зависимости:

```bash
docker compose exec php composer install
```

3. Создайте таблицы и наполните базу тестовыми данными:

```bash
docker compose exec php php seed.php
```

4. Откройте блог:

- блог: `http://localhost:8080`
- phpMyAdmin: `http://localhost:8081`
- MySQL с хоста: `localhost:3307`

## SCSS

Исходники лежат в `scss/`, готовый файл подключается из `public/css/style.css`.

Если установлен Sass, пересобрать CSS можно командой:

```bash
sass scss/style.scss public/css/style.css
```

## Структура

- `public/index.php` - точка входа, инициализация Smarty, роутинг.
- `src/Database.php` - PDO-синглтон с настройками безопасности.
- `src/Model/Category.php` и `src/Model/Post.php` - запросы к данным.
- `templates/` - Smarty-шаблоны с наследованием от `layout.tpl`.
- `db/schema.sql` - схема MySQL.
- `seed.php` - CLI-сидер с транзакцией.
