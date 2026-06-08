<?php

namespace RebelCode\Spotlight\Instagram\Wp;

use WP_Error;
use WP_Post;
use WP_Post_Type;

/**
 * Represents WordPress post type configuration in an immutable struct-like form.
 *
 * @since 0.1
 */
class PostType
{
    /**
     * @since 0.1
     *
     * @var string
     */
    protected $slug;

    /**
     * @since 0.1
     *
     * @var array
     */
    protected $args;

    /**
     * @since 0.1
     *
     * @var MetaField[]
     */
    protected $fields;

    /**
     * Constructor.
     *
     * @since 0.1
     *
     * @param string      $slug   The slug of the post type.
     * @param array       $args   Registration arguments for the post type. See {@link register_post_type()}.
     * @param MetaField[] $fields Optional meta fields to register with this post type.
     */
    public function __construct(string $slug, array $args, array $fields = [])
    {
        $this->slug = $slug;
        $this->args = $args;
        $this->fields = $fields;
    }

    /**
     * @since 0.1
     *
     * @return string
     */
    public function getSlug() : string
    {
        return $this->slug;
    }

    /**
     * @since 0.1
     *
     * @return array
     */
    public function getArgs() : array
    {
        return $this->args;
    }

    /**
     * @since 0.1
     *
     * @return MetaField[]
     */
    public function getMetaFields() : array
    {
        return $this->fields;
    }

    /**
     * Queries the post type for posts.
     *
     * If the $query contains a `fields` entry, the return type may change.
     *
     * @since 0.1
     *
     * @see   https://developer.wordpress.org/reference/functions/get_posts/#comment-3553 Return type information.
     *
     * @param array $query The query. See {@link get_posts()}.
     * @param null  $num   The number of posts to return. Overrides `posts_per_page` in $query.
     * @param int   $page  The page to return. Overrides `page` in $query.
     *
     * @return WP_Post[] An array of {@link WP_Post} instances.
     */
    public function query($query = [], $num = null, $page = 1)
    {
        return get_posts(static::queryArgs($this->slug, $num, $page, $query));
    }

    /**
     * Retrieves a specific post by ID.
     *
     * This method will **not** return posts whose post type does not match the instance's slug. A post MAY exist for
     * the given ID but may belong to a different post type. In such an event, this method will return null.
     *
     * @since 0.1
     *
     * @param string|int $id The ID of the post to retrieve.
     *
     * @return WP_Post|null The post instance, or null if no post for the given ID was found or the found post belongs
     *                      to a different post type.
     */
    public function get($id)
    {
        $posts = $this->query(['p' => $id], 1, 1);

        return count($posts) === 0 ? null : $posts[0];
    }

    /**
     * Inserts a new post into the database using the given data.
     *
     * @since 0.1
     *
     * @see   wp_insert_post() For accepted values in the $data paramater.
     *
     * @param array $data The post data. See {@link wp_insert_post()}. If the array contains an `ID` entry, the
     *                    {@link PostType::update()} method will be used instead.
     *
     * @return int|WP_Error The ID of the inserted post, or a {@link WP_Error} instance on failure.
     */
    public function insert(array $data)
    {
        $data['post_type'] = $this->slug;

        if (isset($data['ID'])) {
            return $this->update($data['ID'], $data);
        }

        return wp_insert_post($data, true);
    }

    /**
     * Updates a post with a specific ID using the given data.
     *
     * @since 0.1
     *
     * @see   wp_update_post() For accepted values in the $data paramater.
     *
     * @param int|string $id   The ID of the post to update.
     * @param array      $data The data to update the post with. See {@link wp_update_post()}.
     *
     * @return int|WP_Error The updated post's ID, or a {@link WP_Error} instance on failure.
     */
    public function update($id, array $data)
    {
        if ($id === null) {
            unset($data['ID']);

            return $this->insert($data);
        }

        $data['ID'] = $id;
        $data['post_type'] = $this->slug;

        return wp_update_post($data, true);
    }

    /**
     * Sends a post to the trash.
     *
     * This does not remove the post from the database, but instead changes its status.
     *
     * @since 0.1
     *
     * @param int|string $id The ID of the post to send to the trash.
     *
     * @return WP_Post|false The post instance on success, false on failure.
     */
    public function trash($id)
    {
        $ret = wp_trash_post($id);

        return ($ret instanceof WP_Post) ? $ret : false;
    }

    /**
     * Deletes a post.
     *
     * This does not move the post to the trash, but removes it completely from the database along with any associated
     * meta data.
     *
     * @since 0.1
     *
     * @param int|string $id The ID of the post to delete.
     *
     * @return WP_Post|false The post instance on success, false on failure.
     */
    public function delete($id)
    {
        $ret = wp_delete_post($id, true);

        return ($ret instanceof WP_Post) ? $ret : false;
    }

    /**
     * Deletes all posts for this post type.
     *
     * @return bool|int The number of deleted posts, or false on error.
     */
    public function deleteAll()
    {
        global $wpdb;

        $query = sprintf(
            'DELETE post, meta
            FROM %s as post
            LEFT JOIN %s as meta on post.ID = meta.post_id
            WHERE post.post_type = \'%s\'',
            $wpdb->posts,
            $wpdb->postmeta,
            $this->slug
        );

        return $wpdb->query($query);
    }

    /**
     * Retrieves the total number of posts for this post type, disregarding of filters.
     *
     * @since 0.4.2
     *
     * @return int The total number of posts.
     */
    public function getTotalNum()
    {
        $counts = wp_count_posts($this->slug);
        $counts = (array) $counts;

        return array_reduce($counts, function ($total, $count) {
            return $total + $count;
        }, 0);
    }

    /**
     * Prepares query arguments.
     *
     * @since 0.1
     *
     * @param string   $slug  The post type slug.
     * @param int|null $num   Optional number of posts.
     * @param int      $page  Optional page number. Integers less than 1 will be treated as 1.
     * @param array    $extra Optional extra arguments to add to the query.
     *
     * @return array The prepared query arguments.
     */
    public static function queryArgs(string $slug, ?int $num = null, int $page = 1, array $extra = [])
    {
        $base = [
            'post_type' => $slug,
            'posts_per_page' => $num ?? -1,
            'page' => max(1, $page),
            'orderby' => 'ID',
            'order' => 'ASC',
        ];

        return array_merge($base, $extra);
    }

    /**
     * Registers a post type with WordPress.
     *
     * @since 0.1
     *
     * @param PostType $postType The post type to register.
     */
    public static function register(PostType $postType)
    {
        if (!post_type_exists($postType->slug)) {
            register_post_type($postType->slug, $postType->args);
        }

        foreach ($postType->fields as $field) {
            MetaField::registerFor($field, $postType->slug);
        }
    }

    /**
     * Retrieves all post types, or post types that match a given set of properties.
     *
     * @since 0.3
     *
     * @param string $relation The logical relational check to use when comparing post types to the given $props.
     * @param array  $props    A mapping of post type properties, that correspond to the same arguments given to the
     *                         {@link register_post_type()} function, that post types must match to be returned.
     *
     * @return WP_Post_Type[] The post type objects.
     */
    public static function getPostTypes($relation = 'and', array $props = [])
    {
        return array_values(get_post_types($props, 'objects', $relation));
    }
}
