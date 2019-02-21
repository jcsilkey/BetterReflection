<?php

declare(strict_types=1);

namespace Roave\BetterReflection\SourceLocator\StubLocator;

use ReflectionClass as CoreReflectionClass;
use ReflectionFunction as CoreReflectionFunction;

/**
 * Interface for PHP stub source locators
 *
 * @internal
 */
interface StubLocator
{
    public function findClassStub(CoreReflectionClass $classReflection) : ?string;

    public function findFunctionStub(CoreReflectionFunction $functionReflection) : ?string;
}
