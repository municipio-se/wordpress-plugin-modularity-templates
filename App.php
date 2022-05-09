<?php

namespace Municipio\WP\ModularityTemplates\Initializer;

class App {
  public function __construct() {
    new \Municipio\WP\ModularityTemplates\App\CloneModulesLink();
    new \Municipio\WP\ModularityTemplates\App\DuplicatePostCloneModulesActions();
    new \Municipio\WP\ModularityTemplates\App\SetModuleType();
  }
}
