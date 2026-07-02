<?php

declare(strict_types=1);

namespace App\Model;

use App\Database;
use PDO;

final class Post
{
    private const SORT_COLUMNS = [
        'date' => 'p.created_at DESC',
        'views' => 'p.views DESC, p.created_at DESC',
    ];

    public static function byCategory(int $categoryId, string $sort, int $limit, int $offset): array
    {
        $orderBy = self::SORT_COLUMNS[$sort] ?? self::SORT_COLUMNS['date'];
        $pdo = Database::pdo();
        $sql = "SELECT p.id, p.title, p.slug, p.description, p.image, p.views, p.created_at
                FROM posts p
                INNER JOIN post_category pc ON pc.post_id = p.id
                WHERE pc.category_id = :category_id
                ORDER BY {$orderBy}
                LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue('category_id', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function latest(int $limit = 1): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'SELECT id, title, slug, description, image, views, created_at
             FROM posts
             ORDER BY created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function countByCategory(int $categoryId): int
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM post_category WHERE category_id = :category_id'
        );
        $stmt->execute(['category_id' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }

    public static function findBySlug(string $slug): ?array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'SELECT id, title, slug, description, content, image, views, created_at
             FROM posts WHERE slug = :slug LIMIT 1'
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function incrementViews(int $id): void
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function categoriesOf(int $postId): array
    {
        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'SELECT c.id, c.name, c.slug
             FROM categories c
             INNER JOIN post_category pc ON pc.category_id = c.id
             WHERE pc.post_id = :post_id
             ORDER BY c.name ASC'
        );
        $stmt->execute(['post_id' => $postId]);

        return $stmt->fetchAll();
    }

    public static function related(int $postId, array $categoryIds, int $limit = 3): array
    {
        if ($categoryIds === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $pdo = Database::pdo();
        $sql = "SELECT DISTINCT p.id, p.title, p.slug, p.description, p.image, p.views, p.created_at
                FROM posts p
                INNER JOIN post_category pc ON pc.post_id = p.id
                WHERE pc.category_id IN ({$placeholders})
                  AND p.id != ?
                ORDER BY p.created_at DESC
                LIMIT ?";

        $stmt = $pdo->prepare($sql);
        $index = 1;
        foreach ($categoryIds as $categoryId) {
            $stmt->bindValue($index++, (int) $categoryId, PDO::PARAM_INT);
        }
        $stmt->bindValue($index++, $postId, PDO::PARAM_INT);
        $stmt->bindValue($index, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
