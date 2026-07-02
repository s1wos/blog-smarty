<?php

declare(strict_types=1);

namespace App\Model;

use App\Database;
use PDO;

final class Category
{
    public static function allWithPosts(): array
    {
        $pdo = Database::pdo();
        $sql = 'SELECT c.id, c.name, c.slug, c.description
                FROM categories c
                WHERE EXISTS (
                    SELECT 1 FROM post_category pc WHERE pc.category_id = c.id
                )
                ORDER BY c.name ASC';

        return $pdo->query($sql)->fetchAll();
    }

    public static function findBySlug(string $slug): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('SELECT id, name, slug, description FROM categories WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function latestPosts(int $categoryId, int $limit = 3): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'SELECT p.id, p.title, p.slug, p.description, p.image, p.views, p.created_at
             FROM posts p
             INNER JOIN post_category pc ON pc.post_id = p.id
             WHERE pc.category_id = :category_id
             ORDER BY p.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
