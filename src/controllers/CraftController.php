<?php

namespace chasegiunta\craftjs\controllers;

use Craft;
use craft\elements\Entry;
use craft\elements\db\EntryQuery;
use craft\elements\db\ElementQuery;
use craft\web\Controller;
use yii\web\Response;

/**
 * Craft controller
 */
class CraftController extends Controller
{
    public $defaultAction = 'index';
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_LIVE;

    /**
     * craft-js/craft action
     */
    public function actionIndex(): Response
    {
        $this->requireAcceptsJson();

        $request = Craft::$app->getRequest();
        $response = Craft::$app->getResponse();

        $response->format = Response::FORMAT_JSON;

        // Get params from request
        $params = $request->getQueryParams();

        if (!isset($params['elementType'])) {
            $response->data = [
                'success' => false,
                'message' => 'Missing elementType',
                'request' => $request->getQueryParams(),
            ];
            // $entries->section($params['section']);
        } else {
            switch ($params['elementType']) {
                case 'entries':
                    $elementType = Entry::class;
                    break;

                default:
                    break;
            }
        }

        $elementType = $elementType::find();

        foreach ($params as $param => $value) {
            if (in_array($param, ['elementType', 'select', 'with'])) {
                continue;
            }

            if (is_numeric($value)) {
                $value = $value + 0;
            }

            if ($value) {
                $elementType->$param($value);
            } else {
                $elementType->$param();
            }
        }

        if (isset($params['select'])) {
            $select = explode(',', $params['select']);
            $select[] = 'sectionId';
            $params['select'] = implode(',', $select);
            $elementType->select($params['select']);
        }

        if (isset($params['with'])) {
            $with = explode(',', $params['with']);
            $elementType->with($with);
        }

        // $elementType->cache();

        $data = $elementType->all();

        // $data = array_map(function ($entry) {
        //     return $entry->toArray();
        // }, $data);

        // ray($data);

        // // Prune $data to only return the columns we selected
        // if (isset($params['select'])) {
        //     $select = explode(',', $params['select']);

        //     // Convert entry to array
        //     $data = array_map(function ($entry) {
        //         return $entry->toArray();
        //     }, $data);

        //     foreach ($data as $key => $entry) {
        //         $data[$key] = array_intersect_key($entry, array_flip($select));
        //     }
        // }


        $response->data = [
            'success' => true,
            'message' => 'CraftController actionIndex()',
            'request' => $request->getQueryParams(),
            'data' => $data,
        ];

        return $response;
    }
}
