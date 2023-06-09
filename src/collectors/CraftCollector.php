<?php

namespace chasegiunta\craftjs\collectors;

use ReflectionClass;
use Spatie\TypeScriptTransformer\Structures\TransformedType;
use Spatie\TypeScriptTransformer\TypeReflectors\ClassTypeReflector;
use Spatie\TypeScriptTransformer\Collectors\DefaultCollector;

class CraftCollector extends DefaultCollector
{

  public $craftClasses = [
    'craft\elements\Address',
    'craft\elements\Asset',
    'craft\elements\Category',
    'craft\elements\db\AddressQuery',
    'craft\elements\db\AssetQuery',
    'craft\elements\db\CategoryQuery',
    'craft\elements\db\EagerLoadPlan',
    'craft\elements\db\ElementQuery',
    'craft\elements\db\ElementQueryInterface',
    'craft\elements\db\ElementRelationParamParser',
    'craft\elements\db\EntryQuery',
    'craft\elements\db\GlobalSetQuery',
    'craft\elements\db\MatrixBlockQuery',
    'craft\elements\db\TagQuery',
    'craft\elements\db\UserQuery',
    'craft\elements\ElementCollection',
    'craft\elements\Entry',
    'craft\elements\GlobalSet',
    'craft\elements\MatrixBlock',
    'craft\elements\Tag',
    'craft\elements\User',
  ];

  public function getTransformedType(ReflectionClass $class): ?TransformedType
  {
    if (!in_array($class->getName(), $this->craftClasses)) {
      return null;
    }

    $reflector = ClassTypeReflector::create($class);

    $transformedType = $reflector->getType()
      ? $this->resolveAlreadyTransformedType($reflector)
      : $this->resolveTypeViaTransformer($reflector);

    return $transformedType;
  }
}
