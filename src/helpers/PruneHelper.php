<?php

namespace chasegiunta\craftjs\helpers;

use Craft;
use craft\base\Element;
use \craft\elements\db\ElementQuery;
use craft\fields\BaseRelationField;
use craft\helpers\StringHelper;
use craft\elements\db\MatrixBlockQuery;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Throwable;

class PruneHelper
{

  private $relatedElementDepthCount = 0;
  private $relatedElementDepthLimit = 1;

  public function pruneData($data, $pruneDefinition)
  {
    if (!is_array($data) || !count($data) > 0) {
      $data = [$data];
    }

    $pruneDefinition = $this->normalizePruneDefinition($pruneDefinition);
    $prunedData = [];

    foreach ($data as $index => $object) {
      $prunedData[$index] = $this->pruneObject($object, $index, $pruneDefinition);
    }

    return $prunedData;
  }

  private function normalizePruneDefinition($pruneDefinition) {
    // If $pruneDefinition is a string, convert it to an array
    if (is_string($pruneDefinition)) {
      $pruneDefinition = json_decode($pruneDefinition, true);
    }

    // If $pruneDefinition is a non-associative array,
    // Convert it to an associative array with { item: true }
    if (is_array($pruneDefinition) && !$this->isArrayAssociative($pruneDefinition)) {
      $pruneDefinition = array_fill_keys($pruneDefinition, true);
    } else if (!is_array($pruneDefinition) || !count($pruneDefinition) > 0) {
      $pruneDefinition = [$pruneDefinition];
    }

    // Loop over each item in $pruneDefinition and recursively normalize each item
    foreach ($pruneDefinition as $key => $value) {
      if (is_bool($value) || is_int($value) || is_string($value) || is_null($value) || is_float($value)) {
        continue;
      }
      if (is_array($value) || is_object($value)) {
        $pruneDefinition[$key] = $this->normalizePruneDefinition($value);
      } else {
        throw new \Exception('Prune definition values must be an array, object, integer, string, or null.');
      }
    }

    return $pruneDefinition;
  }

  public function pruneObject($object, $index, $pruneDefinition) {
    $objectIsElement = false;
    $objectIsElementQuery = false;
    if ($object instanceof Element) {
      $objectIsElement = true;
    } elseif ($object instanceof ElementQuery) {
      $objectIsElementQuery = true;
    }

    $objectReturn = [];

    if ($objectIsElementQuery) {
      foreach ($object->all() as $key => $element) {
        foreach ($pruneDefinition as $definitionHandle => $definitionValue) {
          $objectReturn[$definitionHandle] = $this->getProperty($object, $index, $definitionHandle, $definitionValue, $specials);
        }
      }
    } else {
      foreach ($pruneDefinition as $definitionHandle => $definitionValue) {
        $specials = [];

        if (is_array($definitionValue)) {
          // if any keys in $pruneDefinition begin with dollar sign ($),
          // collect those keys & values into $specials
          foreach ($definitionValue as $handle => $value) {
            if (StringHelper::startsWith($handle, '$')) {
              $specials[substr($handle, 1)] = $value;
              unset($definitionValue[$handle]);
            }
          }
        }
        $objectReturn[$definitionHandle] = $this->getProperty($object, $index, $definitionHandle, $definitionValue, $specials);
      }
    }

    return $objectReturn;
  }

  private function getProperty($object, $index, $definitionHandle, $definitionValue, $specials = []) {
    
    if ($definitionValue == false) {
      return;
    }

    // if $object is not an object return error
    if (!is_object($object)) {
      return [
        'error' => 'Element is not an object'
      ];
    }

    if (!isset($object[$definitionHandle])) {
      return null;
    }

    $fieldValue = null;

    $isElement = false;
    $isElementQuery = false;
    if ($object[$definitionHandle] instanceof Element) {
      $isElement = true;
    } elseif ($object[$definitionHandle] instanceof ElementQuery) {
      $isElementQuery = true;
    }

    if (($isElement) && $object->canGetProperty($definitionHandle)) {
      $fieldValue = $object->$definitionHandle;
    } else if ($isElementQuery) {

      $methodCall = $object->$definitionHandle;
      // $specials array has any items, loop over
      if (count($specials) > 0) {
        foreach ($specials as $specialHandle => $specialValue) {
          if ($specialHandle == 'limit') {
            $call = $methodCall->limit($specialValue);
            continue;
          }
          if ($specialHandle == 'offset') {
            $call = $methodCall->offset($specialValue);
            continue;
          }
          if ($specialHandle == 'order') {
            $call = $methodCall->order($specialValue);
            continue;
          }
          if ($specialHandle == 'where') {
            $call = $methodCall->where($specialValue);
            continue;
          }
          if ($specialHandle == 'whereIn') {
            $call = $methodCall->whereIn($specialValue);
            continue;
          }
          if ($specialHandle == 'type') {
            $call = $methodCall->type($specialValue);
            continue;
          }
        }
      }

      $fieldValue = $methodCall->all();
    } else if (isset($object, $definitionHandle)) {
      $fieldValue = $object->$definitionHandle;
    }
    $fieldValueType = gettype($fieldValue);

    if ($fieldValue) {
      if ($fieldValueType == NULL) {
        return null;
      }
      if (in_array($fieldValueType, ['string', 'integer', 'boolean', 'double'])) {
        return $fieldValue;
      }
      if (is_array($fieldValue)) {
        // If all the array items of $fieldValue are instance of Element, prune the object
        $isArrayOfElements = array_reduce($fieldValue, function($carry, $item) {
            return $carry && $item instanceof Element;
        }, true);

        if ($isArrayOfElements) {
          foreach ($fieldValue as $key => $item) {
            $fieldValue[$key] = $this->pruneObject($item, $index, $definitionValue);
          }
          return $fieldValue;
        }

        return $fieldValue;
      }

      if ($fieldValue instanceof Element) {
        return $this->pruneObject($fieldValue, $index, $definitionValue);
      }

      if ($fieldValue instanceof ElementQuery) {
        $relatedElementObjectPruneDefinition = array();

        $definitionValueType = gettype($definitionValue);
        if (in_array($definitionValueType, ['array'])) {
          foreach ($definitionValue as $key => $nestedPropertyKey) {
            $relatedElementObjectPruneDefinition[$nestedPropertyKey] = true;
          }
        } else {
          $relatedElementObjectPruneDefinition[$definitionValue] = true;
        }
      
        return $this->pruneObject($fieldValue, $index, $relatedElementObjectPruneDefinition);
      }
      
      return $fieldValue;
    }
  }

  function isArrayAssociative($arr) {
    if (is_array($arr) === false) return false;
    if ([] == $arr) return true;
    return array_keys($arr)!== range(0, count($arr) - 1);
  }
}
