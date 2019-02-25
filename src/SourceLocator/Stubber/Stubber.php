<?php

declare(strict_types=1);

namespace Roave\BetterReflection\SourceLocator\Stubber;

use ReflectionClass as CoreReflectionClass;
use ReflectionFunction as CoreReflectionFunction;

/**
 * Interface for PHP stub source locators
 *
 * @internal
 */
interface Stubber
{
    public function findClassStub(CoreReflectionClass $classReflection) : ?string;

    public function findFunctionStub(CoreReflectionFunction $functionReflection) : ?string;
}
