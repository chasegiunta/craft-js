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

    $expressionLanguage = new ExpressionLanguage();
    $pruneDefinition = json_decode($pruneDefinition, true);

    if (!is_array($pruneDefinition) || !count($pruneDefinition) > 0) {
      $pruneDefinition = [$pruneDefinition];
    }

    $prunedData = [];
    $craftNamespace = 'craft\\elements\\';

    foreach ($data as $index => $object) {

      $isElement = false;
      if ($object instanceof Element || $object instanceof ElementQuery) {
        $isElement = true;
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

          // Loop through [nested, property, keys]
          foreach ($nestedPropertyKeys as $nestedPropertyKey) {
            if (($isElement && $object->hasProperty($fieldDefinition)) || $object && property_exists($object, $fieldDefinition)) {
              $field = $object->$fieldDefinition;

              // $prunedData[$elementIndex][$fieldDefinition] = [];
              $prunedData[$index][$fieldDefinition][$nestedPropertyKey] = $this->pruneData($field, "\"$nestedPropertyKey\"", true);
              continue;
            }
          }
          continue;
        }

        // if $object is not an object return error
        if (!is_object($object)) {
          return [
            'error' => 'Element is not an object'
          ];
        }

        if (($isElement && $object->hasProperty($fieldDefinition)) || $object && property_exists($object, $fieldDefinition)) {
          $field = $object->$fieldDefinition;
          $fieldValueType = gettype($field);

          if ($fieldValueType == NULL) {
            $nested ?
              $prunedData[$fieldDefinition] = null :
              $prunedData[$index][$fieldDefinition] = null;
            continue;
          }
          if (in_array($fieldValueType, ['string', 'integer', 'boolean', 'double'])) {
            $nested ?
              $prunedData = $field :
              $prunedData[$index][$fieldDefinition] = $field;
            continue;
          }
          if (is_array($field)) {
            $nested ?
              $prunedData[$fieldDefinition] = $field :
              $prunedData[$index][$fieldDefinition] = $field;
            continue;
          }

          if ($field instanceof MatrixBlockQuery) {
            $matrixBlocks = $field->all();
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

          if ($field instanceof ElementQuery) {
            $prunedData[$index][$fieldDefinition] = $this->getRelatedElementData($field);
            continue;
          }

          $nested ? $prunedData[$fieldDefinition] = $field : $prunedData[$index][$fieldDefinition] = $field;
        }
      }
    }

    return $prunedData;

    // Loop through prune array and note items that contain parentheses
    // These items will need to be handled differently
    // $parenthesesItems = [];
    // foreach ($prune as $key => $value) {
    //   if (strpos($value, '(') !== false) {
    //     $parenthesesItems[] = $value;
    //   }
    // }
    // Remove parentheses items from prune array
    // $prune = array_diff($prune, $parenthesesItems);

    // $nestedProperties = [];

    // // Given that each item in $parentheses is a string that has an $element property followed by a method call
    // // We need to loop through each item and get the nested property on the $element property
    // foreach ($parenthesesItems as $item) {
    //   $item = explode('(', $item);
    //   $elementProperty = trim($item[0]);
    //   $methods = trim($item[1], ')');
    //   $methods = explode('.', $methods);
    //   $methods = explode(',', $methods[0]);

    //   //remove whitespace in $methods
    //   $methods = array_map('trim', $methods);

    //   foreach ($data as $key => $el) {

    //     // TODO: Determine if we're accessing SuperTable, Matrix, or some other element
    //     // Assume we're accessing SuperTable
    //     $elements = $el[$elementProperty]->all();

    //     foreach ($elements as $element) {
    //       if (!is_object($element)) {
    //         continue;
    //       }
    //       $craftNamespace = 'craft\\elements\\'; // Specify the namespace you want to check

    //       $className = get_class($element);
    //       if (strpos($className, $craftNamespace) === 0) {
    //         // This is a Craft element
    //         foreach ($methods as $method) {
    //           $value = $element->$method;
    //           // add to $nestedProperties
    //           $nestedProperties[$key][$elementProperty][$method] = $value;
    //         }
    //       } else if ($element instanceof \verbb\supertable\elements\SuperTableBlockElement) {
    //         // If $element is an instance of SuperTableBlockElement
    //         // Loop through each field in the SuperTable field layout
    //         $values = [];
    //         foreach ($element->getFieldLayout()->getCustomFields() as $field) {
    //           // if $field->handle is in $methods, get the value
    //           if (in_array($field->handle, $methods)) {
    //             $value = $element->getFieldValue($field->handle);
    //             // add to $nestedProperties
    //             $nestedProperties[$key][$elementProperty][$field->handle] = $value;

    //             // $data[$key][$entryProperty][$field->handle] = $value;
    //           }

    //           //     // $data[$key][$entryProperty][$method] = $value;
    //         }
    //       }
    //     }
    //   }
    // }

    // if (is_array($data) && count($data) > 0) {
    //   // .all() returns an array of objects
    //   // Loop through data and prune each entry
    //   foreach ($data as $key => $entry) {
    //     $entry = $entry->toArray();
    //     $data[$key] = array_intersect_key($entry, array_flip($prune));
    //   }
    // } else {
    //   // .one() returns a single object
    //   $data = array_intersect_key($data->toArray(), array_flip($prune));
    // }

    // // Merge $nestedProperties to $data
    // foreach ($nestedProperties as $key => $value) {
    //   $data[$key] = array_merge($data[$key], $value);
    // }

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
