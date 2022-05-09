<?php

namespace Municipio\WP\ModularityTemplates\App;

use Municipio\WP\ModularityTemplates\App\Link_Handler;
use Yoast\WP\Duplicate_Post\Post_Duplicator;
use Yoast\WP\Duplicate_Post\Permissions_Helper;

class DuplicatePostCloneModulesActions {
  public function __construct() {
    add_action("init", [$this, "register_clone_modules_link_action_handler"]);
    add_action(
      "duplicate_post_post_copy",
      [$this, "duplicate_post_modules"],
      10,
      4,
    );
  }

  public function register_clone_modules_link_action_handler() {
    $link_handler = new Link_Handler(
      new Post_Duplicator(),
      new Permissions_Helper(),
    );

    $link_handler->register_hooks();
  }

  public function duplicate_post_modules(
    $new_post_id,
    $post,
    $status,
    $parent_id
  ) {
    // Check if action is valid
    if (
      !isset($_REQUEST["action"]) ||
      empty($_REQUEST["action"]) ||
      $_REQUEST["action"] !== "duplicate_post_clone_modules"
    ) {
      return;
    }

    /* We return this function if the post is a module */
    if (preg_match("/(^mod-)/", $post->post_type)) {
      return;
    }

    $post_meta_keys = get_post_custom_keys($post->ID);

    if (empty($post_meta_keys)) {
      return;
    }

    foreach ($post_meta_keys as $meta_key) {
      $meta_values = get_post_custom_values($meta_key, $post->ID);

      foreach ($meta_values as $meta_value) {
        $meta_value = maybe_unserialize($meta_value);

        /* we delete post meta */
        delete_post_meta($new_post_id, $meta_key, $meta_value);

        if ($meta_key === "modularity-modules" && !empty($meta_value)) {
          $meta_value = $this->get_modularity_new_meta_value($meta_value);
        }
        // we put back the updated post meta
        add_post_meta(
          $new_post_id,
          $meta_key,
          duplicate_post_wp_slash($meta_value),
        );
      }
    }
  }

  public function get_modularity_new_meta_value($module_areas) {
    $meta_data = [];

    foreach ($module_areas as $modules_index => $modules) {
      $meta_data[$modules_index] = [];

      foreach ($modules as $module_index => $module) {
        $new_module_index = $module_index;

        $new_module_id = (string) duplicate_post_create_duplicate(
          get_post($module["postid"]),
        );

        if ($new_module_id) {
          /* Update module post status to publish. As it is draft by default */
          wp_update_post([
            "ID" => $new_module_id,
            "post_status" => "publish",
          ]);

          $meta_data[$modules_index][$new_module_index]["columnWidth"] =
            $module["columnWidth"];
          $meta_data[$modules_index][$new_module_index][
            "postid"
          ] = $new_module_id;
          $meta_data[$modules_index][$new_module_index]["hidden"] =
            $module["hidden"];
        }
        add_post_meta($new_module_id, "original_postid", $module["postid"]);
      }
    }
    return $meta_data;
  }
}
