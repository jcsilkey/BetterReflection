<?php

declare(strict_types=1);

namespace Roave\BetterReflection\SourceLocator\StubLocator;

use ReflectionClass as CoreReflectionClass;
use ReflectionFunction as CoreReflectionFunction;

final class AggregateStubLocator implements StubLocator
{
    /** @var array|StubLocator[] */
    private $locators;

    public function __construct(StubLocator ...$locators)
    {
        $this->locators = $locators;
    }

    public function findClassStub(CoreReflectionClass $classReflection) : ?string
    {
        foreach ($this->locators as $locator) {
            $stub = $locator->findClassStub($classReflection);

            if ($stub !== null) {
                return $stub;
            }
        }

        return null;
    }

    public function findFunctionStub(CoreReflectionFunction $functionReflection) : ?string
    {
        foreach ($this->locators as $locator) {
            $stub = $locator->findFunctionStub($functionReflection);

            if ($stub !== null) {
                return $stub;
            }
        }

        return null;
    }
}
