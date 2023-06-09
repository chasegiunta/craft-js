<?php

namespace chasegiunta\craftjs\transformers;

use phpDocumentor\Reflection\Type;
use Spatie\TypeScriptTransformer\Actions\TranspileTypeToTypeScriptAction;
use Spatie\TypeScriptTransformer\Transformers\DtoTransformer;
use Spatie\TypeScriptTransformer\Structures\MissingSymbolsCollection;

class CallableTransformer extends DtoTransformer
{
  protected function typeToTypeScript(
    Type $type,
    MissingSymbolsCollection $missingSymbolsCollection,
    ?string $currentClass = null,
  ): string {

    $transpiler = new TranspileTypeToTypeScriptAction(
      $missingSymbolsCollection,
      $currentClass,
    );

    $originalType = $type;
    $overriddenType = $this->checkForTypeOverride($type);
    $return = $this->checkForTypeOverride($type) ?? $transpiler->execute($type);
    echo $originalType . ' => ' . $return . PHP_EOL;
    return $return;
  }

  protected function checkForTypeOverride(Type $type)
  {
    $overideTypeDetection = false;
    $type = explode('|', $type);
    foreach ($type as $key => $value) {
      if ($value == 'callable') {
        $overideTypeDetection = true;
        unset($type[$key]);
      }
      // if (strpos($value, 'craft\\') === 1) {
      //   $overideTypeDetection = true;
      //   unset($type[$key]);
      // }
    }
    if ($overideTypeDetection == true) {
      return implode('|', $type);
    } else {
      return null;
    }
  }
}
