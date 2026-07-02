<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use App\Model\Category;
use App\Model\Post;
use App\Router;
use Dotenv\Dotenv;
use Smarty\Smarty;

$root = dirname(__DIR__);

if (file_exists($root . '/.env')) {
    Dotenv::createImmutable($root)->load();
}

$smarty = new Smarty();
$smarty->setTemplateDir($root . '/templates');
$smarty->setCompileDir($root . '/templates_c');
$smarty->setCacheDir($root . '/cache');
$smarty->setEscapeHtml(true);

$dispatch = Router::dispatch($_SERVER['REQUEST_URI'] ?? '/');
$route = $dispatch['route'];
$params = $dispatch['params'];

switch ($route) {
    case 'home':
        $categories = Category::allWithPosts();
        $homeData = [];

        foreach ($categories as $category) {
            $homeData[] = [
                'category' => $category,
                'posts' => Category::latestPosts((int) $category['id'], 3),
            ];
        }

        $latestPosts = Post::latest(1);

        $smarty->assign('featuredPost', $latestPosts[0] ?? null);
        $smarty->assign('sections', $homeData);
        $smarty->display('home.tpl');
        break;

    case 'category':
        $category = Category::findBySlug($params['slug']);

        if ($category === null) {
            http_response_code(404);
            $smarty->display('404.tpl');
            break;
        }

        $sort = ($_GET['sort'] ?? 'date') === 'views' ? 'views' : 'date';
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 6;
        $total = Post::countByCategory((int) $category['id']);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $totalPages);
        $offset = ($page - 1) * $perPage;

        $smarty->assign('category', $category);
        $smarty->assign('posts', Post::byCategory((int) $category['id'], $sort, $perPage, $offset));
        $smarty->assign('sort', $sort);
        $smarty->assign('page', $page);
        $smarty->assign('totalPages', $totalPages);
        $smarty->assign('total', $total);
        $smarty->display('category.tpl');
        break;

    case 'post':
        $post = Post::findBySlug($params['slug']);

        if ($post === null) {
            http_response_code(404);
            $smarty->display('404.tpl');
            break;
        }

        Post::incrementViews((int) $post['id']);
        $post['views'] = (int) $post['views'] + 1;

        $categories = Post::categoriesOf((int) $post['id']);
        $categoryIds = array_map(static fn (array $item): int => (int) $item['id'], $categories);

        $smarty->assign('post', $post);
        $smarty->assign('categories', $categories);
        $smarty->assign('relatedPosts', Post::related((int) $post['id'], $categoryIds, 3));
        $smarty->display('post.tpl');
        break;

    default:
        http_response_code(404);
        $smarty->display('404.tpl');
        break;
}
