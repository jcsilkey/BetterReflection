<?php

declare(strict_types=1);

namespace Roave\BetterReflection\SourceLocator\Stubber;

use ReflectionClass as CoreReflectionClass;
use ReflectionFunction as CoreReflectionFunction;
use function file_get_contents;
use function is_file;
use function is_readable;
use function preg_match;

/**
 * Class that generates a stub source from a given reflection instance using internal
 * source stubs.
 *
 * @internal
 */
final class BetterReflectionStubber implements Stubber
{
    public function findClassStub(CoreReflectionClass $classReflection) : ?string
    {
        return $this->getStub($classReflection->getName());
    }

    public function findFunctionStub(CoreReflectionFunction $functionReflection) : ?string
    {
        return null;
    }

    /**
     * Get the stub source code for an internal class.
     *
     * Returns null if nothing is found.
     *
     * @param string $className Should only contain [A-Za-z]
     */
    private function getStub(string $className) : ?string
    {
        if (! $this->hasStub($className)) {
            return null;
        }

        return "<?php\n\n" . file_get_contents($this->buildStubName($className));
    }

    /**
     * Determine the stub name
     */
    private function buildStubName(string $className) : ?string
    {
        if (! preg_match('/^[a-zA-Z_][a-zA-Z_\d]*$/', $className)) {
            return null;
        }

        return __DIR__ . '/../../../stub/' . $className . '.stub';
    }

    /**
     * Determine if a stub exists for specified class name
     */
    public function hasStub(string $className) : bool
    {
        $expectedStubName = $this->buildStubName($className);

        if ($expectedStubName === null) {
            return false;
        }

        return is_file($expectedStubName) && is_readable($expectedStubName);
    }
}
