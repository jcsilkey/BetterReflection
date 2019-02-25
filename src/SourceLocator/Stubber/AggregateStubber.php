<?php

declare(strict_types=1);

namespace Roave\BetterReflection\SourceLocator\Stubber;

use ReflectionClass as CoreReflectionClass;
use ReflectionFunction as CoreReflectionFunction;

final class AggregateStubber implements Stubber
{
    /** @var array|Stubber[] */
    private $stubbers;

    public function __construct(Stubber ...$stubbers)
    {
        $this->stubbers = $stubbers;
    }

    public function findClassStub(CoreReflectionClass $classReflection) : ?string
    {
        foreach ($this->stubbers as $stubber) {
            $stub = $stubber->findClassStub($classReflection);

            if ($stub !== null) {
                return $stub;
            }
        }

        return null;
    }

    public function findFunctionStub(CoreReflectionFunction $functionReflection) : ?string
    {
        foreach ($this->stubbers as $stubber) {
            $stub = $stubber->findFunctionStub($functionReflection);

            if ($stub !== null) {
                return $stub;
            }
        }

        return null;
    }
}
