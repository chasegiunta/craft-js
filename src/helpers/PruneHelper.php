<?php

namespace chasegiunta\craftjs\helpers;

use craft\base\Element;
use craft\elements\db\ElementQuery;
use craft\htmlfield\HtmlFieldData;

class PruneHelper
{
  public function pruneData($data, $pruneDefinition)
  {
    if (!is_array($data) || !count($data) > 0) {
      $data = [$data];
    }

    $pruneDefinition = $this->normalizePruneDefinition($pruneDefinition);
    $prunedData = [];

    foreach ($data as $index => $object) {
      // Step into each element (or object) and prune it according to the $pruneDefinition
      $prunedData[$index] = $this->pruneObject($object, $pruneDefinition);
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
    if (is_array($pruneDefinition) && !$this->isAssociativeArray($pruneDefinition)) {
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

  public function pruneObject($object, $pruneDefinition) {
    if (!is_object($object)) {
      return ['error' => '$object is not an object'];
    }

    // Extract specials from pruneDefinition
    list($pruneDefinition, $specials) = $this->extractSpecials($pruneDefinition);

    // For ElementQuery, handle all elements returned by the query
    if ($object instanceof ElementQuery) {
      return $this->processElementQuery($object, $pruneDefinition, $specials);
    }

    // For other objects, handle them directly
    return $this->processPruneDefinition($object, $pruneDefinition);
  }

  private function extractSpecials($pruneDefinition) {
    // If $pruneDefinition is not an array, return it as-is
    if (!is_array($pruneDefinition)) return [$pruneDefinition, []];

    $specials = [];
    foreach ($pruneDefinition as $key => $value) {
        if (strpos($key, '$') === 0) {  // Special keys start with '$'
            $specials[substr($key, 1)] = $value;
            unset($pruneDefinition[$key]);
        }
    }
    return [$pruneDefinition, $specials];
  }

  private function processElementQuery($elementQuery, $pruneDefinition, $specials = []) {
    $result = [];
    foreach ($elementQuery->all() as $element) {
      $result[] = $this->processPruneDefinition($element, $pruneDefinition);
    }
    return $result;
  }

  private function processPruneDefinition($object, $pruneDefinition) {
    $result = [];

    foreach ($pruneDefinition as $field => $details) {
      // Extract specials from pruneDefinition
      list($details, $specials) = $this->extractSpecials($details);
      $result[$field] = $this->getProperty($object, $field, $details, $specials);
    }
    return $result;
  }

  private function getProperty($object, $definitionHandle, $definitionValue, $specials = []) {
    if ($definitionValue == false) return;
    if (!is_object($object)) return ['error' => 'Not an object'];

    $fieldValue = $this->getFieldValue($object, $definitionHandle, $specials);

    if (is_scalar($fieldValue) || is_null($fieldValue)) {
      return $fieldValue;
    }

    if (is_array($fieldValue)) {
      // If all the array items of $fieldValue are instance of Element, prune the object
      $isArrayOfElements = array_reduce($fieldValue, function($carry, $item) {
          return $carry && $item instanceof Element;
      }, true);

      if ($isArrayOfElements) {
        foreach ($fieldValue as $key => $element) {
          if ($this->isAssociativeArray($definitionValue) && $this->allArrayKeysAreUnderscored($definitionValue)) {
            // Assume associative array is keyed by entry (matrix block) types
            foreach ($definitionValue as $underscoredElementType => $typePruneDefinition) {
              if ($element->type->handle === ltrim($underscoredElementType, '_')) {
                $fieldValue[$key] = $this->pruneObject($element, $definitionValue[$underscoredElementType]);
              }
            }
          } else {
            $fieldValue[$key] = $this->pruneObject($element, $definitionValue);
          }
        }
        return $fieldValue;
      }

      return $fieldValue;
    }

    if ($fieldValue instanceof Element) {
      return $this->pruneObject($fieldValue, $definitionValue);
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
    
      return $this->pruneObject($fieldValue, $relatedElementObjectPruneDefinition);
    }

    if (is_object($fieldValue)) {
      if ($fieldValue instanceof HtmlFieldData) {
        return $fieldValue;
      }
      return $this->pruneObject($fieldValue, $definitionValue);
    }

    return $fieldValue;
  }

  function getFieldValue($object, $definitionHandle, $specials = []) {

    $fieldValue = null;

    if (is_object($object) && isset($object->$definitionHandle)) {
      $fieldValue = $object->$definitionHandle;
    } else {
      $fieldValue = $object[$definitionHandle];
    }

    if (($fieldValue instanceof Element) && $object->canGetProperty($definitionHandle)) {
      $fieldValue = $object->$definitionHandle;
    } else if ($fieldValue instanceof ElementQuery) {
      $methodCall = $object->$definitionHandle;
      $methodCall = $this->applySpecials($methodCall, $specials);
      $fieldValue = $methodCall->all();
    } else if (isset($object, $definitionHandle)) {
      $fieldValue = $object->$definitionHandle;
    }
    return $fieldValue;
  }

  function isAssociativeArray($arr) {
    if (is_array($arr) === false) return false;
    if ([] == $arr) return true;
    if (array_keys($arr) !== range(0, count($arr) - 1)) return true;
    foreach ($arr as $value) {
        if (is_array($value) && $this->isAssociativeArray($value)) return true;
    }
    return false;
  }

  private function allArrayKeysAreUnderscored($arr) {
    $keys = array_keys($arr);
    foreach ($keys as $key) {
      if (strpos($key, '_')!== 0) {
        return false;
      }
    }
    return true;
  }

  private function applySpecials($methodCall, $specials) {
    foreach ($specials as $specialHandle => $specialValue) {
      $methodCall = $methodCall->$specialHandle($specialValue);
    }
    return $methodCall;
  }
}
