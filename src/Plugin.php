<?php

namespace chasegiunta\craftjs;

use Craft;
use chasegiunta\craftjs\services\QueryHandler;
use craft\base\Plugin as BasePlugin;

/**
 * Craft JS plugin
 *
 * @method static Plugin getInstance()
 * @author Chase Giunta <me@chasegiunta.com>
 * @copyright Chase Giunta
 * @license MIT
 * @property-read QueryHandler $queryHandler
 */
class Plugin extends BasePlugin
{
    public string $schemaVersion = '1.0.0';

    public static function config(): array
    {
        return [
            'components' => ['queryHandler' => QueryHandler::class],
        ];
    }

    public function init()
    {
        parent::init();

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
            // ...
        });
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
    }
}
