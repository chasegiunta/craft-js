<?php

namespace chasegiunta\craftjs\controllers;

use Craft;
use craft\elements\Entry;
use craft\elements\db\EntryQuery;
use craft\elements\db\ElementQuery;
use craft\web\Controller;
use yii\web\Response;

use craft\web\twig\variables\CraftVariable;

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

        $craftElementClass = null;
        $customClass = null;

        if (isset($params['elementType'])) {
            switch ($params['elementType']) {
                case 'entries':
                    $craftElementClass = Entry::class;
                    break;
                default:
                    break;
            }
        }

        if ($craftElementClass == null) {
            // If no element query is defined, see if trying to access Craft variable
            $craftVariable = new CraftVariable();
            $components = $craftVariable->components;

            // If any of the params match any of the components, return that component
            foreach (array_keys($components) as $component) {
                if (in_array($component, array_keys($params))) {
                    $customClass = $components[$component];
                    unset($params[$component]);
                    break;
                }
            }
        }

        if (!isset($craftElementClass) && !isset($customClass)) {
            $response->data = [
                'success' => false,
                'message' => 'Missing elementType or custom class',
                'request' => $request->getQueryParams(),
            ];
        }

        if ($craftElementClass) {
            $craftElementClass = $craftElementClass::find();
            $queryBuilder = $craftElementClass;
        } else if ($customClass) {
            $queryBuilder = new $customClass;
        }



        foreach ($params as $param => $value) {
            if (in_array($param, ['elementType', 'select', 'with', 'prune', 'asArray'])) {
                continue;
            }

            if (is_numeric($value)) {
                $value = $value + 0;
            }

            if ($value) {
                $queryBuilder->$param($value);
            } else {
                $queryBuilder = $queryBuilder->$param();
            }
        }

        if (isset($params['select'])) {
            $select = explode(',', $params['select']);
            $select[] = 'sectionId';
            $params['select'] = implode(',', $select);
            $queryBuilder->select($params['select']);
        }

        if (isset($params['with'])) {
            $with = explode(',', $params['with']);
            $queryBuilder->with($with);
        }

        if ($craftElementClass) {
            $data = $queryBuilder->all();
        } else {
            $data = $queryBuilder;
        }

        // $data = array_map(function ($entry) {
        //     return $entry->toArray();
        // }, $data);

        // ray($data);
        // // Prune $data to only return the columns we selected
        if ($craftElementClass && isset($params['prune'])) {
            $prune = preg_split('/,(?![^\(]*\))/', $params['prune']);

            // Loop through prune array and note items that contain parentheses
            // These items will need to be handled differently
            $parenthesesItems = [];
            foreach ($prune as $key => $value) {
                if (strpos($value, '(') !== false) {
                    $parenthesesItems[] = $value;
                }
            }
            // Remove parentheses items from prune array
            $prune = array_diff($prune, $parenthesesItems);

            $nestedProperties = [];

            // Given that each item in $parentheses is a string that has an $entry property followed by a method call
            // We need to loop through each item and get the nested property on the $entry property
            foreach ($parenthesesItems as $item) {
                $item = explode('(', $item);
                $entryProperty = trim($item[0]);
                $methods = trim($item[1], ')');
                $methods = explode('.', $methods);
                $methods = explode(',', $methods[0]);

                //remove whitespace in $methods
                $methods = array_map('trim', $methods);

                foreach ($data as $key => $entry) {

                    // TODO: Determine if we're accessing SuperTable, Matrix, or some other element

                    // Assume we're accessing SuperTable
                    $blocks = $entry[$entryProperty]->all();
                    foreach ($blocks as $block) {
                        // ray($block);
                        $values = [];
                        foreach ($block->getFieldLayout()->getCustomFields() as $field) {
                            // if $field->handle is in $methods, get the value
                            if (in_array($field->handle, $methods)) {
                                $value = $block->getFieldValue($field->handle);
                                // add to $nestedProperties
                                $nestedProperties[$key][$entryProperty][$field->handle] = $value;

                                // $data[$key][$entryProperty][$field->handle] = $value;
                            }

                            //     // $data[$key][$entryProperty][$method] = $value;
                        }
                    }
                }
            }


            // Loop through data and prune each entry
            foreach ($data as $key => $entry) {
                $entry = $entry->toArray();
                $data[$key] = array_intersect_key($entry, array_flip($prune));
            }

            // Merge $nestedProperties to $data
            foreach ($nestedProperties as $key => $value) {
                $data[$key] = array_merge($data[$key], $value);
            }
        }


        $response->data = [
            'success' => true,
            'message' => 'CraftController actionIndex()',
            'request' => $request->getQueryParams(),
            'data' => $data,
        ];

        return $response;
    }
}
