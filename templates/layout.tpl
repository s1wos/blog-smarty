<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{block name="title"}Blog{/block}</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="top">
        <div class="wrap top-line">
            <a class="logo" href="/">Blog</a>
        </div>
    </header>

    <main class="wrap">
        {block name="content"}{/block}
    </main>

    <footer class="footer">
        <div class="wrap footer-grid">
            <a class="footer-logo" href="/">Blog</a>
            <p>Минимальный блог на PHP 8.1, Smarty и MySQL.</p>
        </div>
    </footer>
</body>
</html>
