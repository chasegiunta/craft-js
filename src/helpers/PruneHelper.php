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

    $pruneDefinition = json_decode($pruneDefinition, true);

    if (!is_array($pruneDefinition) || !count($pruneDefinition) > 0) {
      $pruneDefinition = [$pruneDefinition];
    }

    $prunedData = [];
    $craftNamespace = 'craft\\elements\\';

    foreach ($data as $index => $object) {

      $isElement = false;
      $isElementQuery = false;
      if ($object instanceof Element) {
        $isElement = true;
      } elseif ($object instanceof ElementQuery) {
        $isElementQuery = true;
      }

      foreach ($pruneDefinition as $fieldDefinition) {
        // /**
        //  * Parses the field definition to check for a relationship depth limit in parentheses. 
        //  * If found, sets the relatedElementDepthLimit property to that limit.
        //  * Also removes the limit from the field definition string.
        //  */
        // if (strpos($fieldDefinition, '(') !== false) {
        //   list($fieldDefinition, $limit) = explode('(', $fieldDefinition);
        //   $limit = str_replace(')', '', $limit);
        //   $this->relatedElementDepthLimit = (int) $limit;
        //   $fieldDefinition = trim($fieldDefinition);
        // }

        $nestedPropertyKey = null;
        $fieldValue = null;

        if (strpos($fieldDefinition, '(') !== false) {
          // Get "(nested property string)"
          preg_match('/\(([^()]|(?R))*\)/', $fieldDefinition, $matches);
          $nestedPropertyString = $matches[0] ?? null;

          // Remove "(nested property string)" from $fieldDefinition
          $fieldDefinition = str_replace($nestedPropertyString, '', $fieldDefinition);
          $fieldDefinition = trim($fieldDefinition);

          // Remove parentheses from "(nested property string)"
          if ($nestedPropertyString[0] == '(' && $nestedPropertyString[strlen($nestedPropertyString) - 1] == ')') {
            $nestedPropertyString = substr($nestedPropertyString, 1, -1);
          }

          // Get [nested, property, keys] from "nested property string"
          $nestedPropertyKeys = explode(',', $nestedPropertyString);
          $nestedPropertyKeys = array_map('trim', $nestedPropertyKeys);
        }

        if (($isElement) && $object->canGetProperty($fieldDefinition)) {
          $fieldValue = $object->$fieldDefinition;
        } else if ($isElementQuery) {
          if ($object->$fieldDefinition) {
            $fieldValue = $object->$fieldDefinition;
          } else {
            $fieldValue = $object->$fieldDefinition->all();
          }
          // $fieldValue = $object->$fieldDefinition->all();
          // else if ($isElementQuery && $object->type($fieldDefinition)) {
          //   $fieldValue = $object->type($fieldDefinition);
        } else if (property_exists($object, $fieldDefinition)) {
          $fieldValue = $object->$fieldDefinition;
        }
        $fieldValueType = gettype($fieldValue);

        // if (isset($nestedPropertyKeys)) {
        //   // Loop through [nested, property, keys]
        //   foreach ($nestedPropertyKeys as $nestedPropertyKey) {
        //     $prunedData[$index][$fieldDefinition][$nestedPropertyKey] = $this->pruneData($fieldValue, "\"$nestedPropertyKey\"", true);
        //     continue;
        //   }
        // }

        // if $object is not an object return error
        if (!is_object($object)) {
          return [
            'error' => 'Element is not an object'
          ];
        }

        if ($fieldValue) {
          if ($fieldValueType == NULL) {
            $nested ?
              $prunedData[$fieldDefinition] = null :
              $prunedData[$index][$fieldDefinition] = null;
            continue;
          }
          if (in_array($fieldValueType, ['string', 'integer', 'boolean', 'double'])) {
            $nested ?
              $prunedData = $fieldValue :
              $prunedData[$index][$fieldDefinition] = $fieldValue;
            continue;
          }
          if (is_array($fieldValue)) {
            $nested ?
              $prunedData[$fieldDefinition] = $fieldValue :
              $prunedData[$index][$fieldDefinition] = $fieldValue;
            continue;
          }

          if ($fieldValue instanceof ElementQuery) {

            if (isset($nestedPropertyKeys)) {
              // Loop through [nested, property, keys]
              foreach ($nestedPropertyKeys as $nestedPropertyKey) {
                $prunedData[$index][$fieldDefinition][$nestedPropertyKey] = $this->pruneData($fieldValue, "\"$nestedPropertyKey\"", true);
                continue;
              }
              continue;
            }

            $prunedData[$index][$fieldDefinition] = $this->getRelatedElementData($fieldValue);
            continue;
          }

          if ($fieldValue instanceof MatrixBlockQuery) {
            $matrixBlocks = $fieldValue->all();
            foreach ($matrixBlocks as $i => $block) {
              $blockType = $block->getType();
              $blockFieldValues = $block->getFieldValues();
              foreach ($blockFieldValues as $key => $blockFieldValue) {
                if ($blockFieldValue instanceof ElementQuery) {
                  $prunedData[$index][$fieldDefinition][$blockType->handle][$key] = $this->getRelatedElementData($blockFieldValue);
                } else {
                  $prunedData[$index][$fieldDefinition][$blockType->handle][$key] = $blockFieldValue;
                }
              }
            }
            continue;
          }

          $nested ? $prunedData[$fieldDefinition] = $fieldValue : $prunedData[$index][$fieldDefinition] = $fieldValue;
        }
      }
    }

    return $prunedData;
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
}
