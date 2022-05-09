<?php

namespace Municipio\WP\ModularityTemplates\App;

class ModuleManager extends \Modularity\ModuleManager {
  public function __construct() {
  }

  public static function getModuleUsage($id, $limit = false) {
    global $wpdb;

    // Normal modules
    $query = "
            SELECT
                {$wpdb->postmeta}.post_id,
                {$wpdb->posts}.post_title,
                {$wpdb->posts}.post_type
            FROM {$wpdb->postmeta}
            LEFT JOIN
                {$wpdb->posts} ON ({$wpdb->postmeta}.post_id = {$wpdb->posts}.ID)
            WHERE
                {$wpdb->postmeta}.meta_key = 'modularity-modules'
                AND ({$wpdb->postmeta}.meta_value REGEXP '.*\"postid\";s:[0-9]+:\"{$id}\".*')
                AND {$wpdb->posts}.post_type != 'revision'
            ORDER BY {$wpdb->posts}.post_title ASC
        ";

    $modules = $wpdb->get_results($query, OBJECT);

    // Shortcode modules
    $query = "
            SELECT
                {$wpdb->posts}.ID AS post_id,
                {$wpdb->posts}.post_title,
                {$wpdb->posts}.post_type
            FROM {$wpdb->posts}
            WHERE
                ({$wpdb->posts}.post_content REGEXP '([\[]modularity.*id=\"{$id}\".*[\]])')
                AND {$wpdb->posts}.post_type != 'revision'
            ORDER BY {$wpdb->posts}.post_title ASC
        ";

    $shortcodes = $wpdb->get_results($query, OBJECT);

    $result = array_merge($modules, $shortcodes);

    if (is_numeric($limit)) {
      if (count($result) > $limit) {
        $sliced = array_slice($result, $limit);
      } else {
        $sliced = $result;
      }

      return (object) [
        "data" => $sliced,
        "more" =>
          count($result) > 0 && count($sliced) > 0
            ? count($result) - count($sliced)
            : 0,
      ];
    }

    return $result;
  }
}
