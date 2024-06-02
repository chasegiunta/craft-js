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

  public function pruneData($data, $pruneDefinition, $nested = false)
  {
    if (!is_array($data) || !count($data) > 0) {
      $data = [$data];
    }

    $pruneDefinition = $this->normalizePruneDefinition($pruneDefinition);

    $prunedData = [];
    $craftNamespace = 'craft\\elements\\';

    foreach ($data as $index => $object) {
      $prunedData[$index] = $this->pruneObject($object, $index, $pruneDefinition, $nested);
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
      if (is_bool($value)) {
        continue;
      }
      $pruneDefinition[$key] = $this->normalizePruneDefinition($value);
    }

    return $pruneDefinition;
  }

  public function pruneObject($object, $index, $pruneDefinition, $nested = false) {
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
          $objectReturn[$definitionHandle] = $this->getProperty($object, $index, $definitionHandle, $definitionValue, $nested);
        }
      }
    } else {
      foreach ($pruneDefinition as $definitionHandle => $definitionValue) {
        $objectReturn[$definitionHandle] = $this->getProperty($object, $index, $definitionHandle, $definitionValue, $nested);
      }
    }

    return $objectReturn;
  }

  private function getProperty($object, $index, $definitionHandle, $definitionValue, $nested = false) {
    
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
      
      // if ($object->$definitionHandle) {
      //   $fieldValue = $object->$definitionHandle;
      // } else {
      //   $fieldValue = $object->all();
      // }

      $fieldValue = $object->$definitionHandle->all();
      // else if ($isElementQuery && $object->type($fieldDefinition)) {
      //   $fieldValue = $object->type($fieldDefinition);
    } else if (isset($object, $definitionHandle)) {
      $fieldValue = $object->$definitionHandle;
    }
    $fieldValueType = gettype($fieldValue);

    // if (isset($nestedPropertyKeys)) {
    //   // Loop through [nested, property, keys]
    //   foreach ($nestedPropertyKeys as $nestedPropertyKey) {
    //     $prunedData[$index][$fieldDefinition][$nestedPropertyKey] = $this->pruneData($fieldValue, "\"$nestedPropertyKey\"", true);
    //     continue;
    //   }
    // }

    if ($fieldValue) {
      if ($fieldValueType == NULL) {
        return null;
        // $nested ?
        //   $prunedData[$definitionHandle] = null :
        //   $prunedData[$index][$definitionHandle] = null;
        // continue;
      }
      if (in_array($fieldValueType, ['string', 'integer', 'boolean', 'double'])) {
        if ($nested) {
          $prunedData = $fieldValue;
        } else {
          return $fieldValue;
        }
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


        if ($nested) {
          $prunedData[$definitionHandle] = $fieldValue;
        } else {
          return $fieldValue;
        }
      }

      if ($fieldValue instanceof Element) {
        return $this->pruneObject($fieldValue, $index, $definitionValue);
      }

      if ($fieldValue instanceof ElementQuery) {

        // if (isset($nestedPropertyKeys)) {
        //   // Loop through [nested, property, keys]
        //   foreach ($nestedPropertyKeys as $nestedPropertyKey) {
        //     $prunedData[$index][$definitionHandle][$nestedPropertyKey] = $this->pruneData($fieldValue, "\"$nestedPropertyKey\"", true);
        //     continue;
        //   }
        //   continue;
        // }

        // $relatedElementObject = $this->getRelatedElementData($fieldValue);
        $relatedElementObjectPruneDefinition = array();

        $definitionValueType = gettype($definitionValue);
        if (in_array($definitionValueType, ['array'])) {
          foreach ($definitionValue as $key => $nestedPropertyKey) {
            $relatedElementObjectPruneDefinition[$nestedPropertyKey] = true;
          }
        } else {
          $relatedElementObjectPruneDefinition[$definitionValue] = true;
        }
      
        return $this->pruneObject($fieldValue, $index, $relatedElementObjectPruneDefinition, $nested);
      }

      if ($fieldValue instanceof MatrixBlockQuery) {
        $matrixBlocks = $fieldValue->all();
        foreach ($matrixBlocks as $i => $block) {
          $blockType = $block->getType();
          $blockFieldValues = $block->getFieldValues();
          foreach ($blockFieldValues as $key => $blockFieldValue) {
            if ($blockFieldValue instanceof ElementQuery) {
              $prunedData[$index][$definitionHandle][$blockType->handle][$key] = $this->getRelatedElementData($blockFieldValue);
            } else {
              $prunedData[$index][$definitionHandle][$blockType->handle][$key] = $blockFieldValue;
            }
          }
        }
      }
      
      if ($nested) {
        $prunedData[$definitionHandle] = $fieldValue;
      } else {
        return $fieldValue;
      }
    }
  }

  private function getRelatedElementData($field, $isNested = false)
  {
    if ($this->relatedElementDepthCount >= $this->relatedElementDepthLimit) {
      return null;
    }
    $relatedElements = $field->all();
    $relatedElementFieldValues = [];
    foreach ($relatedElements as $i => $relatedElement) {

      $relatedElementNativeFieldValues = $relatedElement->getAttributes();
      $relatedElementCustomFieldValues = $relatedElement->getFieldValues();
      $relatedElementFieldValues = array_merge($relatedElementNativeFieldValues, $relatedElementCustomFieldValues);

      continue;
      foreach ($relatedElementFieldValues as $key => $value) {
        if ($value instanceof ElementQuery) {
          // if ($isNested) {
          $this->relatedElementDepthCount++;
          // }
          $relatedElementFieldValues[$key] = $this->getRelatedElementData($value, true);
          // if ($isNested) {
          $this->relatedElementDepthCount--;
          // }
        }
      }
    }
    return $relatedElementFieldValues;
  }

  function isArrayAssociative($arr) {
    if (is_array($arr) === false) return false;
    if ([] == $arr) return true;
    return array_keys($arr)!== range(0, count($arr) - 1);
  }
}
