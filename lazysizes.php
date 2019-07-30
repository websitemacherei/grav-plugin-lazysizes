<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Grav;
use RocketTheme\Toolbox\Event\Event;

use \Grav\Common\Page\Medium\ImageMedium;

class LazysizesPlugin extends Plugin {

    public static function getSubscribedEvents() {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    public static function getImageMagicActions() {
        
        $actions = [];

        foreach(ImageMedium::$magic_actions as $action) {
            $actions[$action] = $action;
        }

        return $actions;
    }

    public function onPluginsInitialized() {

        if ($this->isAdmin()) {
            return;
        }

        $this->enable([
            'onMarkdownInitialized' => ['onMarkdownInitialized', 0]
        ]);
    }

    public function onMarkdownInitialized($e) {

        require_once __DIR__ . '/src/functions.php';

        $page = $e['page'];
        $markdown = $e['markdown'];

        $images = $page->media()->images();
        $config = $this->config->get('plugins.lazysizes');
        $widths = getConfigWidths($config['widths']);

        $this->grav['debugger']->addMessage($config);

        // include js file
        if(!isset($config['include_js']) || $config['include_js']) {
            $this->grav['assets']->addJs('plugin://lazysizes/js/lazysizes.min.js');
        }
        
        // add image derivates
        foreach($images as $image) {
            $image->derivatives($widths);
        }

        $markdown->addInlineType('!', 'ImageExtended', 0);

        $imageExtended = function($Excerpt) use ($images, $config) {

            $Inline = $this->inlineImage($Excerpt);
            $InlineOriginal = parent::inlineImage($Excerpt);

            $imageData = getOriginalImage($InlineOriginal['element']['attributes']['src'], $images);

            return manipulateElement($Inline, $imageData, $config);
        };  

        $markdown->inlineImageExtended = $imageExtended->bindTo($markdown, $markdown);        
    }
}
