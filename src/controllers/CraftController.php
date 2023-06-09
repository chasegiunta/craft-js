<?php

namespace chasegiunta\craftjs\controllers;

use Craft;
use craft\web\Controller;
use yii\web\Response;
use yii\web\BadRequestHttpException;

use chasegiunta\craftjs\services\QueryHandler;

/**
 * Craft controller
 */
class CraftController extends Controller
{
    /**
     * Specifies the default action to be executed when no action is specified in the request.
     */
    public $defaultAction = 'index';

    /**
     * Specifies whether the controller allows anonymous access or requires authentication.
     */
    protected array|int|bool $allowAnonymous = self::ALLOW_ANONYMOUS_LIVE;

    /**
     * Handles a single query by calling the `handleSingleQuery` method of the `QueryHandler` class and returns the result as a JSON response.
     *
     * @return Response
     */
    public function actionIndex(): Response
    {
        $this->requireAcceptsJson();

        $request = Craft::$app->getRequest();
        $response = Craft::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        // Get params from request
        $params = $request->getQueryParams();

        $queryHandler = new QueryHandler();
        $response->data = $queryHandler->handleSingleQuery($params);
        return $response;
    }

    /**
     * Handles multiple queries by iterating over the queries, calling the `handleSingleQuery` method of the `QueryHandler` class for each query, and returns an array of results as a JSON response.
     *
     * @return Response
     */
    public function actionBatched(): Response
    {
        $this->requireAcceptsJson();

        $request = Craft::$app->getRequest();
        $response = Craft::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        // Since it's POST, we will get params from the request body
        $queries = $request->getBodyParams();

        if (!is_array($queries)) {
            throw new BadRequestHttpException('Invalid request body');
        }

        $results = [];
        $queryHandler = new QueryHandler();
        foreach ($queries as $singleQueryParams) {
            $results[] = $queryHandler->handleSingleQuery($singleQueryParams);
        }

        $response->data = $results;
        return $response;
    }
}
