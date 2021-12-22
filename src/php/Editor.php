<?php

namespace Municipio\WP\ModularityTemplates\App;

class Editor extends \Modularity\Editor
{
    public static $isEditing = null;

    public function __construct()
    {
        global $post;

    }



    public static function pageForPostTypeTranscribe($postId)
    {
        if (is_numeric($postId) && $postType = \Modularity\Editor::isPageForPostType($postId)) {
            $postId = 'archive-' . $postType;
        }

        if (substr($postId, 0, 8) === 'archive-') {
            $postType = str_replace('archive-', '', $postId);
            $pageForPostType = get_option('page_for_' . $postType);
            $contentFromPage = get_option('page_for_' . $postType . '_content');

            if ($contentFromPage) {
                $postId = (int) $pageForPostType;
            }
        }

        return $postId;
    }

    /**
     * Get modules added to a specific post
     * @param  integer $postId The post id
     * @return array           The modules on the post
     */
    public static function getPostModules($postId)
    {

        //Declarations
        $modules = array();
        $retModules = array();

        //Get current post id
        $postId = self::pageForPostTypeTranscribe($postId);

        // Get enabled modules
        $available = \Modularity\ModuleManager::$available;
        $enabled = \Modularity\ModuleManager::$enabled;

        // Get modules structure
        $moduleIds = array();
        $moduleSidebars = null;

        if (is_numeric($postId)) {
            $moduleSidebars = get_post_meta($postId, 'modularity-modules', true);
        } else {
            $moduleSidebars = get_option('modularity_' . $postId . '_modules');
        }

        //Create array of visible modules
        if (!empty($moduleSidebars)) {
            foreach ($moduleSidebars as $sidebar) {
                foreach ($sidebar as $module) {
                    if (!isset($module['postid'])) {
                        continue;
                    }

                    $moduleIds[] = $module['postid'];
                }
            }
        }

        //Get allowed post statuses
        $postStatuses = array('publish');
        if (is_user_logged_in()) {
            $postStatuses[] = 'private';
        }

        // Get module posts
        $modulesPosts = get_posts(array(
            'posts_per_page' => -1,
            'post_type' => $enabled,
            'include' => $moduleIds,
            'post_status' => $postStatuses
        ));

        // Add module id's as keys in the array
        if (!empty($modulesPosts)) {
            foreach ($modulesPosts as $module) {
                $modules[$module->ID] = $module;
            }
        }

        // Create an strucural correct array with module post data
        if (!empty($moduleSidebars)) {
            foreach ($moduleSidebars as $key => $sidebar) {
                $retModules[$key] = array(
                    'modules' => array(),

                    // Todo: This will duplicate for every sidebar, move it to top level of array(?)
                    // Alternatively only fetch options for the current sidebar (not all like now)
                    'options' => get_post_meta($postId, 'modularity-sidebar-options', true)
                );

                $arrayIndex = 0;

                foreach ($sidebar as $moduleUid => $module) {
                    if (!isset($module['postid'])) {
                        continue;
                    }
                    $moduleId = $module['postid'];

                    if (!isset($modules[$moduleId])) {
                        continue;
                    }

                    $moduleObject = self::getModule($moduleId, $module);
                    if (!$moduleObject) {
                        continue;
                    }

                    $retModules[$key]['modules'][$arrayIndex] = $moduleObject;

                    $arrayIndex++;
                }
            }
        }

        return $retModules;
    }

  

}
