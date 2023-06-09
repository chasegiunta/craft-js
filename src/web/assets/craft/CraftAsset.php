<?php

namespace chasegiunta\craftjs\web\assets\craft;

use Craft;
use craft\web\AssetBundle;

/**
 * Craft asset bundle
 */
class CraftAsset extends AssetBundle
{
    public $sourcePath = __DIR__ . '/dist';
    public $depends = [];
    public $js = [];
    public $css = [];
}
