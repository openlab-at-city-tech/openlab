<?php

declare(strict_types=1);

namespace RebelCode\Spotlight\Instagram\Utils;

class DbQueries
{
    /**
     * Creates a query that deletes posts by their type.
     *
     * @param string[] $postTypes A list of CPT slugs.
     * @param int|null $limit Optional delete limit.
     *
     * @return string|null The query, or null if the list of post types is empty.
     */
    public static function deletePostsByType(array $postTypes, ?int $limit = null): ?string
    {
        global $wpdb;

        if (count($postTypes) === 0) {
            return null;
        }

        if (count($postTypes) > 1) {
            $postTypeList = Arrays::join($postTypes, ',', function ($postType) {
                return "'$postType'";
            });
            $where = "IN ($postTypeList)";
        } else {
            $where = "= '{$postTypes[0]}'";
        }

        if ($limit !== null && $limit > 0) {
            // Explanation of strange limit strategy:
            // MySQL does not allow LIMIT in DELETE queries that delete from multiple joined tables.
            // The workaround is to JOIN the posts table with a sub-query of itself that incorporates the LIMIT.
            // The sub-query, aliased as "lim", resolves to a subset of the "posts" table. When joined with "posts",
            // the "posts" table is restricted to those posts only. The WHERE clause is be repeated here and in the main
            // query, JUST IN CASE. We DON'T want to delete all of the posts on the website.
            $limJoin = sprintf(
                'JOIN (
                    SELECT ID
                    FROM %s
                    WHERE post_type %s
                    LIMIT %s
                ) AS lim on lim.ID = posts.ID',
                $wpdb->posts,
                $where,
                $limit
            );
        } else {
            $limJoin = '';
        }

        return sprintf(
        /** @lang text */
            "DELETE posts, meta
            FROM %s as posts
            %s
            JOIN %s as meta ON posts.ID = meta.post_id
            WHERE posts.post_type %s",
            $wpdb->posts,
            $limJoin,
            $wpdb->postmeta,
            $where
        );
    }
}
