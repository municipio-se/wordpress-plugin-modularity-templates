<?php

namespace Municipio\WP\ModularityTemplates\App;

use Yoast\WP\Duplicate_Post\UI\Link_Builder;

class CloneModulesLink {
  private $enabled_post_types = [];

  public function __construct() {
    $this->link_builder = new Link_Builder();

    add_action("init", [$this, "add_duplicate_post_with_modules_link"]);
  }

  public function add_duplicate_post_with_modules_link() {
    $this->enabled_post_types = get_option("duplicate_post_types_enabled");

    if (empty($this->enabled_post_types)) {
      return;
    }
    if (!$this->link_builder) {
      return;
    }

    add_filter(
      "post_row_actions",
      function ($actions, $post) {
        if (
          !in_array($post->post_type, $this->enabled_post_types) ||
          preg_match("/^(mod-)/", $post->post_type)
        ) {
          return $actions;
        }

        $link_text = __("Clone with editable modules", "modularity-templates");
        $link_text = apply_filters(
          "modularity-templates/clone_with_modules_link_label",
          $link_text,
        );

        $actions["clone_modules"] = "<a href=\"{$this->link_builder->build_link(
          $post,
          "display",
          "duplicate_post_clone_modules",
        )}\" aria-label=\"{$link_text} - ”{$post->title}”\">{$link_text}</a>";

        // Sort The array
        ksort($actions);

        return $actions;
      },
      10,
      2,
    );
  }
}
