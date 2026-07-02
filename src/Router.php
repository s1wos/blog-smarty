<?php

declare(strict_types=1);

namespace App;

final class Router
{
    public static function dispatch(string $uri): array
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        return match (true) {
            $path === '/' => ['route' => 'home', 'params' => []],
            preg_match('#^/category/([a-z0-9-]+)$#', $path, $matches) === 1 => [
                'route' => 'category',
                'params' => ['slug' => $matches[1]],
            ],
            preg_match('#^/post/([a-z0-9-]+)$#', $path, $matches) === 1 => [
                'route' => 'post',
                'params' => ['slug' => $matches[1]],
            ],
            default => ['route' => '404', 'params' => []],
        };
    }
}
