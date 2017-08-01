<?php

namespace Cruster\Factory\Objects;

use Nette\PhpGenerator\GlobalFunction;

class Factory {

    static $postTypes = [];

    static function postType($name, $options) {
        $postType = new PostType($name, $options);
        array_push(self::$postTypes, $postType);
        return $postType;
    }

    static function generateIncludePostTypesMethod() {
        $function = new GlobalFunction('cruster_include_post_types');
        foreach (self::$postTypes as $postType) {
            $function->addBody('require_once get_template_directory() . \'/inc/' . $postType->fileName . '\';');
        }
        return $function;
    }

    static function generateAddPostTypeMetaBoxesMethod() {
        $function = new GlobalFunction('cruster_add_meta_boxes');
        foreach (self::$postTypes as $postType) {
            $function->addBody('cruster_' . $postType->id . '_meta_boxes();');
        }
        return $function;
    }

    static protected function createBasicFiles() {
        if (!file_exists('inc')) {
            mkdir('inc');
        }
        touch('index.php');
        touch('page.php');
        touch('single.php');
        touch('archive.php');
        touch('header.php');
        touch('footer.php');
        touch('404.php');
        touch('style.css');
    }

    static function generateFunctions() {
        $fileName = 'functions.php';
        touch($fileName);

        $content = "<?php\n\n";
        $content .= 'add_action(\'init\', \'cruster_init\');' . PHP_EOL;
        $content .= 'add_action(\'add_meta_boxes\',\'cruster_add_meta_boxes\');' . PHP_EOL;
        $content .=  PHP_EOL;

        $content .= self::generateInitFunction();
        $content .= PHP_EOL.PHP_EOL;
        $content .= self::generateIncludePostTypesMethod();
        $content .= PHP_EOL.PHP_EOL;
        $content .= self::generateAddPostTypeMetaBoxesMethod();

        file_put_contents($fileName, $content);
    }

    static private function generateInitFunction() {
        $function = new GlobalFunction('cruster_init');
        $function->addBody('cruster_include_post_types();');
        return $function;
    }

    static function generate() {
        self::createBasicFiles();
        self::generateFunctions();

        foreach (self::$postTypes as $postType) {
            $postType->generate();
        }
    }
}