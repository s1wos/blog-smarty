<?php

declare(strict_types=1);

use App\Database;
use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    Dotenv::createImmutable(__DIR__)->load();
}

$pdo = Database::pdo();
$schema = file_get_contents(__DIR__ . '/db/schema.sql');

if ($schema === false) {
    throw new RuntimeException('Cannot read database schema.');
}

$categories = [
    ['name' => 'Дизайн', 'slug' => 'design', 'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
    ['name' => 'Маркетинг', 'slug' => 'marketing', 'description' => 'Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'],
    ['name' => 'Программирование', 'slug' => 'programming', 'description' => 'Ut enim ad minim veniam, quis nostrud exercitation ullamco.'],
    ['name' => 'Аналитика', 'slug' => 'analytics', 'description' => 'Duis aute irure dolor in reprehenderit in voluptate velit esse.'],
    ['name' => 'Менеджмент', 'slug' => 'management', 'description' => 'Excepteur sint occaecat cupidatat non proident.'],
];

$posts = [
    ['title' => 'Lorem ipsum dolor sit amet consectetur', 'slug' => 'lorem-ipsum-dolor-sit-amet-consectetur'],
    ['title' => 'Consectetur adipiscing elit sed do eiusmod', 'slug' => 'consectetur-adipiscing-elit-sed-do-eiusmod'],
    ['title' => 'Tempor incididunt ut labore et dolore', 'slug' => 'tempor-incididunt-ut-labore-et-dolore'],
    ['title' => 'Magna aliqua ut enim ad minim', 'slug' => 'magna-aliqua-ut-enim-ad-minim'],
    ['title' => 'Veniam quis nostrud exercitation ullamco', 'slug' => 'veniam-quis-nostrud-exercitation-ullamco'],
    ['title' => 'Laboris nisi ut aliquip ex ea', 'slug' => 'laboris-nisi-ut-aliquip-ex-ea'],
    ['title' => 'Commodo consequat duis aute irure', 'slug' => 'commodo-consequat-duis-aute-irure'],
    ['title' => 'Dolor in reprehenderit in voluptate', 'slug' => 'dolor-in-reprehenderit-in-voluptate'],
    ['title' => 'Velit esse cillum dolore eu fugiat', 'slug' => 'velit-esse-cillum-dolore-eu-fugiat'],
    ['title' => 'Nulla pariatur excepteur sint occaecat', 'slug' => 'nulla-pariatur-excepteur-sint-occaecat'],
    ['title' => 'Cupidatat non proident sunt in culpa', 'slug' => 'cupidatat-non-proident-sunt-in-culpa'],
    ['title' => 'Qui officia deserunt mollit anim', 'slug' => 'qui-officia-deserunt-mollit-anim'],
    ['title' => 'Id est laborum sed ut perspiciatis', 'slug' => 'id-est-laborum-sed-ut-perspiciatis'],
    ['title' => 'Unde omnis iste natus error', 'slug' => 'unde-omnis-iste-natus-error'],
    ['title' => 'Sit voluptatem accusantium doloremque laudantium', 'slug' => 'sit-voluptatem-accusantium-doloremque-laudantium'],
    ['title' => 'Totam rem aperiam eaque ipsa', 'slug' => 'totam-rem-aperiam-eaque-ipsa'],
    ['title' => 'Quae ab illo inventore veritatis', 'slug' => 'quae-ab-illo-inventore-veritatis'],
    ['title' => 'Et quasi architecto beatae vitae', 'slug' => 'et-quasi-architecto-beatae-vitae'],
    ['title' => 'Dicta sunt explicabo nemo enim', 'slug' => 'dicta-sunt-explicabo-nemo-enim'],
    ['title' => 'Ipsam voluptatem quia voluptas sit', 'slug' => 'ipsam-voluptatem-quia-voluptas-sit'],
];

try {
    $pdo->exec($schema);
    $pdo->beginTransaction();

    $pdo->exec('DELETE FROM post_category');
    $pdo->exec('DELETE FROM posts');
    $pdo->exec('DELETE FROM categories');

    $categoryStmt = $pdo->prepare(
        'INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)'
    );

    $categoryIds = [];
    foreach ($categories as $category) {
        $categoryStmt->execute($category);
        $categoryIds[] = (int) $pdo->lastInsertId();
    }

    $postStmt = $pdo->prepare(
        'INSERT INTO posts (title, slug, description, content, image, views, created_at)
         VALUES (:title, :slug, :description, :content, :image, :views, :created_at)'
    );
    $linkStmt = $pdo->prepare(
        'INSERT INTO post_category (post_id, category_id) VALUES (:post_id, :category_id)'
    );

    foreach ($posts as $index => $post) {
        $number = $index + 1;
        $description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore.';
        $content = '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer nec odio. Praesent libero. Sed cursus ante dapibus diam.</p><p>Sed nisi. Nulla quis sem at nibh elementum imperdiet. Duis sagittis ipsum. Praesent mauris.</p><p>Fusce nec tellus sed augue semper porta. Mauris massa. Vestibulum lacinia arcu eget nulla.</p><p>Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>';

        $postStmt->execute([
            'title' => $post['title'],
            'slug' => $post['slug'],
            'description' => $description,
            'content' => $content,
            'image' => sprintf('https://picsum.photos/seed/blog-%d/800/480', $number),
            'views' => random_int(10, 500),
            'created_at' => (new DateTimeImmutable(sprintf('-%d days', 20 - $index)))->format('Y-m-d H:i:s'),
        ]);

        $postId = (int) $pdo->lastInsertId();
        $assigned = [$categoryIds[$index % count($categoryIds)]];

        if ($index % 2 === 0) {
            $assigned[] = $categoryIds[($index + 1) % count($categoryIds)];
        }

        foreach (array_unique($assigned) as $categoryId) {
            $linkStmt->execute([
                'post_id' => $postId,
                'category_id' => $categoryId,
            ]);
        }
    }

    $pdo->commit();
    fwrite(STDOUT, "Database seeded successfully.\n");
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, 'Seed failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
