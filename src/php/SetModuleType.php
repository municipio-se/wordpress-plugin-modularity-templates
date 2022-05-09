<?php

namespace Municipio\WP\ModularityTemplates\App;

use Municipio\WP\ModularityTemplates\App\Editor;
use  Municipio\WP\ModularityTemplates\App\ModuleManager;

class SetModuleType
{
    public function __construct()
    {
        $this->modularity_editor = new Editor();
        $this->module_manager = new ModuleManager();

        add_action('admin_enqueue_scripts', [$this, 'set_module_type_scripts'], 960);
    }

    public function set_module_type_scripts()
    {
        global $current_screen;
        global $post;

        if (!self::is_edit_module_view($current_screen->id) || !$post) return;

        $module_areas_modules = $this->modularity_editor->getPostModules($post->ID);

        if (empty($module_areas_modules)) return;

        $modules = [];

        foreach ($module_areas_modules as $module_area) {
            foreach ($module_area['modules'] as $module) {
                $modules[]['id'] = $module->ID;
            }
        }


        foreach ($modules as $index => $module) {
            $modules[$index]['usage'] = $this->module_manager->getModuleUsage($module['id']);
        }

        $labels = [
            'shared' => __('Shared', 'modularity-templates'),
            'local' => '',
        ];

        $labels = apply_filters('modularity-templates/set-module-type/labels', $labels);

        /* enqueue scripts*/
        wp_enqueue_script(
            'modularity-templates-scripts',
            plugin_dir_url(__DIR__) . 'js/Index.js',
            '',
            '1.0',
            true
        );
        wp_localize_script('modularity-templates-scripts', 'moduleData', $modules);
        wp_localize_script('modularity-templates-scripts', 'labels', $labels);

        /* enqueue styles */
        wp_enqueue_style('modularity-templates-style', plugin_dir_url(__DIR__) . 'css/Index.css');
    }

    private static function is_edit_module_view($current_view)
    {
        $current_id = preg_match('/^(edit-mod-)+\w*/', $current_view) ? $current_view : '';
        $screen_ids = [
            'admin_page_modularity-editor',
            $current_id
        ];
        return in_array($current_view, $screen_ids);
    }
}
