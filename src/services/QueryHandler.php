<?php

namespace chasegiunta\craftjs\services;

use Craft;
use yii\base\Component;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\elements\User;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Tag;
use craft\elements\GlobalSet;
use craft\elements\MatrixBlock;
use craft\elements\Address;

use craft\fields\Matrix;

// use craft\commerce\elements\Variant;
// use craft\commerce\elements\Product;
// use craft\commerce\elements\Order;
// use craft\commerce\elements\Subscription;
// use craft\commerce\elements\ShippingCategory;
// use craft\commerce\elements\TaxCategory;
// use craft\commerce\elements\ShippingMethod;
// use craft\commerce\elements\PaymentGateway;
// use craft\commerce\elements\Discount;
// use craft\commerce\elements\Email;
// use craft\commerce\elements\Customer;
// use craft\commerce\elements\Donation;
// use craft\commerce\elements\TaxRate;
// use craft\commerce\elements\SubscriptionPlan;

// For Pagination
use craft\db\Paginator;
use craft\web\twig\variables\Paginate;

// For Caching
use yii\caching\TagDependency;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use craft\fields\BaseRelationField;

use craft\web\twig\variables\CraftVariable;

use Illuminate\Support\Collection;

use chasegiunta\craftjs\helpers\PruneHelper;

class QueryHandler extends Component
{
  protected array $params = [];
  protected bool $cache = false;
  protected ?string $cacheKey = null;
  protected $craftElementClass = null;
  protected $customClass = null;
  protected bool|int $paginate = false;

  public function handleSingleQuery(array $params)
  {
    $this->params = $params;
    $this->cache = $this->params['cache'] ?? false;

    $cachedResponse = $this->checkForCache();

    if ($cachedResponse !== null) {
      return $cachedResponse;
    }

    if (isset($this->params['paginate'])) {
      $this->paginate = $this->params['paginate'];
    }

    $this->craftElementClass = $this->checkForCraftElementClass();
    $this->customClass = $this->checkForCustomClass();

    if (!$this->craftElementClass && !$this->customClass) {
      return [
        'success' => false,
        'message' => 'Missing elementType or custom class',
        'request' => $this->params,
      ];
    }

    /** @var ElementQueryInterface|null $queryBuilder */
    $queryBuilder = null;

    if ($this->craftElementClass !== null) {
      $queryBuilder = $this->craftElementClass::find();
    } else if ($this->customClass !== null) {
      $queryBuilder = new $this->customClass;
    }

    $queryBuilder = $this->processQueryBuilderParams($queryBuilder);

    if (isset($this->params['select'])) {
      $select = explode(',', $this->params['select']);
      $select[] = 'sectionId';
      $this->params['select'] = implode(',', $select);

      $queryBuilder->select($this->params['select']);
      // if (is_object($queryBuilder)) {
      //   $queryBuilder->select($this->params['select']);
      // }
    }

    if (isset($this->params['with'])) {
      $with = explode(',', $this->params['with']);
      $queryBuilder->with($with);
    }

    if ($this->craftElementClass) {
      if ($this->paginate) {
        [$data, $paginationInfo] = $this->handleElementQueryPagination($queryBuilder, $this->paginate);
      } else {
        $executeMethod = $params['executeMethod'] ?? 'all';
        $data = $this->executeQuery($queryBuilder, $executeMethod);
      }

      $this->handleCache($data);
    } else {
      $data = $queryBuilder;
    }

    // Prune $data to only return the columns we selected
    if (isset($params['prune'])) {
      $pruneHelper = new PruneHelper;
      $data = $pruneHelper->pruneData($data, $params['prune']);
    } else {
      if (is_array($data) && count($data) > 0) {
        // If $data is an array of objects, convert each object to an array
        foreach ($data as $key => $element) {
          $data[$key] = $element->toArray();
        }
      }
    }

    $responseData = [
      'success' => true,
      'message' => 'CraftController actionIndex()',
      'request' => $this->params,
      'data' => $data,
      'cached' => false,
    ];

    if ($this->paginate) {
      $responseData['pagination'] = $paginationInfo;
    }

    return $responseData;
  }

  /**
   * Check if cache parameters are set in $params and retrieve data from cache if available.
   *
   * @return array|null The cache key and cached data if available, otherwise null.
   */
  private function checkForCache()
  {
    if (!$this->cache) {
      return null;
    }

    $this->cacheKey = 'query_' . md5(json_encode($this->params));
    $data = Craft::$app->getCache()->get($this->cacheKey);

    if ($data) {
      return [
        'success' => true,
        'message' => 'CraftController handleSingleQuery()',
        'request' => $this->params,
        'data' => $data,
        'cached' => true,
      ];
    }
    // If data is not found in cache, return null
    return null;
  }

  /**
   * Checks for a Craft element class in the given parameters.
   * It iterates over a class map array and checks if any of the keys in the parameters match the keys in the class map.
   * If a match is found, it sets the craftElementClass variable to the corresponding class and removes the key from the parameters array.
   * Finally, it returns an array with the craftElementClass and the updated parameters array.
   */
  private function checkForCraftElementClass()
  {
    $classMap = [
      'entries' => Entry::class,
      'users' => User::class,
      'assets' => Asset::class,
      'categories' => Category::class,
      'tags' => Tag::class,
      'globals' => GlobalSet::class,
      'matrixBlocks' => MatrixBlock::class,
      'addresses' => Address::class,
    ];

    foreach ($classMap as $param => $class) {
      if (isset($this->params[$param])) {
        $craftElementClass = $class;
        unset($this->params[$param]);
        return $craftElementClass;
      }
    }

    return null;
  }

  /**
   * Checks if any of the parameters match any of the components in the CraftVariable class.
   * If a match is found, it returns the corresponding custom class and removes the matched parameter from the params array.
   */
  private function checkForCustomClass()
  {
    if ($this->craftElementClass) {
      return null;
    }

    $craftVariable = new CraftVariable();
    $components = $craftVariable->components;

    // If any of the params match any of the components, return that component
    foreach (array_keys($components) as $component) {
      if (in_array($component, array_keys($this->params))) {
        $customClass = $components[$component];
        unset($this->params[$component]);
        return $customClass;
      }
    }

    return null;
  }

  private function processQueryBuilderParams($queryBuilder)
  {
    foreach ($this->params as $param => $value) {
      if (in_array($param, ['select', 'with', 'prune', 'asArray', 'paginate', 'one', 'executeMethod'])) {
        continue;
      }

      /**
       * Casts the value to a numeric type by adding 0.
       * This ensures the value passed is numeric.
       */
      if (is_numeric($value)) {
        $value = $value + 0;
      }

      if ($value) {
        /**
         * Parses a comma-separated string into an array of values.
         * Trims whitespace and converts special values like 'null', 'true', 'false' 
         * to their actual types. 
         * If a single non-array value is provided, puts it into an array.
         */
        if (is_string($value)) {
          $values = explode(',', $value);
          $values = array_map(function ($item) {
            $trimmedItem = trim($item, " '\"");
            if (strtolower($trimmedItem) === 'null') {
              return null;
            } elseif (is_numeric($trimmedItem)) {
              return $trimmedItem + 0;
            } elseif (strtolower($trimmedItem) === 'true') {
              return true;
            } elseif (strtolower($trimmedItem) === 'false') {
              return false;
            } else {
              return $trimmedItem;
            }
          }, $values);
        } else {
          $values = [$value];
        }

        /**
         * Checks if the given query builder parameter is a callable method, 
         * calls it with the given values, and reassigns the result back to 
         * the query builder. If not callable, calls the parameter as a 
         * method with the given values.
         */
        if (is_callable([$queryBuilder, $param])) {
          if (is_object($queryBuilder)) {
            $queryBuilder = $queryBuilder->$param(...$values);
          } else {
            throw new \Exception("Expected \$queryBuilder to be an object.");
          }
        } else {
          $queryBuilder = $queryBuilder->$param(...$values);
        }
      } else {
        /**
         * Tries to call the $param() method on the $queryBuilder. 
         * If it throws an exception, calls $param() again with null as the argument.
         */
        try {
          $queryBuilder = $queryBuilder->$param();
        } catch (\Throwable $th) {
          $queryBuilder = $queryBuilder->$param(null);
        }
      }
    }
    return $queryBuilder;
  }

  private function handleElementQueryPagination($queryBuilder, $paginate)
  {
    $paginator = new Paginator((clone $queryBuilder)->limit(null), [
      'currentPage' => $paginate,
      'pageSize' => $queryBuilder->limit ?: 100,
    ]);

    $paginationInfo = Paginate::create($paginator);
    $data = $paginator->getPageResults();

    return [$data, $paginationInfo];
  }

  private function handleCache($data)
  {
    if ($this->cacheKey) {
      // Create a dependency on the 'entry_updated' tag
      // In order for this to work, we need to invalidate the cache tag on entry save (TagDependency::invalidate(Craft::$app->getCache(), 'entry_updated'))
      $dependency = new TagDependency(['tags' => 'entry_updated']);
      // Set the result to cache for 1 hour (3600 seconds)
      Craft::$app->getCache()->set($this->cacheKey, $data, 3600, $dependency);
    }
  }

  // private function pruneData($data)
  // {
  //   $prune = json_decode($this->params['prune']);

  //   // Loop through prune array and note items that contain parentheses
  //   // These items will need to be handled differently
  //   $parenthesesItems = [];
  //   foreach ($prune as $key => $value) {
  //     if (strpos($value, '(') !== false) {
  //       $parenthesesItems[] = $value;
  //     }
  //   }
  //   // Remove parentheses items from prune array
  //   $prune = array_diff($prune, $parenthesesItems);

  //   $nestedProperties = [];

  //   // Given that each item in $parentheses is a string that has an $element property followed by a method call
  //   // We need to loop through each item and get the nested property on the $element property
  //   foreach ($parenthesesItems as $item) {
  //     $item = explode('(', $item);
  //     $elementProperty = trim($item[0]);
  //     $methods = trim($item[1], ')');
  //     $methods = explode('.', $methods);
  //     $methods = explode(',', $methods[0]);

  //     //remove whitespace in $methods
  //     $methods = array_map('trim', $methods);

  //     foreach ($data as $key => $el) {

  //       // TODO: Determine if we're accessing SuperTable, Matrix, or some other element
  //       // Assume we're accessing SuperTable
  //       $elements = $el[$elementProperty]->all();

  //       foreach ($elements as $element) {
  //         if (!is_object($element)) {
  //           continue;
  //         }
  //         $craftNamespace = 'craft\\elements\\'; // Specify the namespace you want to check

  //         $className = get_class($element);
  //         if (strpos($className, $craftNamespace) === 0) {
  //           // This is a Craft element
  //           foreach ($methods as $method) {
  //             $value = $element->$method;
  //             // add to $nestedProperties
  //             $nestedProperties[$key][$elementProperty][$method] = $value;
  //           }
  //         } else if ($element instanceof \verbb\supertable\elements\SuperTableBlockElement) {
  //           // If $element is an instance of SuperTableBlockElement
  //           // Loop through each field in the SuperTable field layout
  //           $values = [];
  //           foreach ($element->getFieldLayout()->getCustomFields() as $field) {
  //             // if $field->handle is in $methods, get the value
  //             if (in_array($field->handle, $methods)) {
  //               $value = $element->getFieldValue($field->handle);
  //               // add to $nestedProperties
  //               $nestedProperties[$key][$elementProperty][$field->handle] = $value;

  //               // $data[$key][$entryProperty][$field->handle] = $value;
  //             }

  //             //     // $data[$key][$entryProperty][$method] = $value;
  //           }
  //         }
  //       }
  //     }
  //   }

  //   if (is_array($data) && count($data) > 0) {
  //     // .all() returns an array of objects
  //     // Loop through data and prune each entry
  //     foreach ($data as $key => $entry) {
  //       $entry = $entry->toArray();
  //       $data[$key] = array_intersect_key($entry, array_flip($prune));
  //     }
  //   } else {
  //     // .one() returns a single object
  //     $data = array_intersect_key($data->toArray(), array_flip($prune));
  //   }

  //   // Merge $nestedProperties to $data
  //   foreach ($nestedProperties as $key => $value) {
  //     $data[$key] = array_merge($data[$key], $value);
  //   }

  //   return $data;
  // }

  function executeQuery($query, $method)
  {
    switch ($method) {
      case 'one':
        return $query->one();
      case 'collect':
        return $query->collect();
      case 'exists':
        return $query->exists();
      case 'count':
        return $query->count();
      case 'ids':
        return $query->ids();
      case 'column':
        return $query->column();
      default:
        return $query->all();
    }
  }
}
