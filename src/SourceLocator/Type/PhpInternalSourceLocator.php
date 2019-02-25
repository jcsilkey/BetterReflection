<?php

declare(strict_types=1);

namespace Roave\BetterReflection\SourceLocator\Type;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionFunction;
use Reflector;
use Roave\BetterReflection\Identifier\Identifier;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Exception\InvalidFileLocation;
use Roave\BetterReflection\SourceLocator\Located\InternalLocatedSource;
use Roave\BetterReflection\SourceLocator\Located\LocatedSource;
use Roave\BetterReflection\SourceLocator\Stubber\AggregateStubber;
use Roave\BetterReflection\SourceLocator\Stubber\BetterReflectionStubber;
use Roave\BetterReflection\SourceLocator\Stubber\CoreReflectionStubber;
use Roave\BetterReflection\SourceLocator\Stubber\Stubber;
use function class_exists;
use function function_exists;
use function interface_exists;
use function trait_exists;

final class PhpInternalSourceLocator extends AbstractSourceLocator
{
    /** @var Stubber */
    private $stubber;

    public function __construct(Locator $astLocator, ?Stubber $stubber = null)
    {
        parent::__construct($astLocator);

        $this->stubber = $stubber ??
             new AggregateStubber(
                 new BetterReflectionStubber(),
                 new CoreReflectionStubber()
             );
    }

    /**
     * {@inheritDoc}
     *
     * @throws InvalidArgumentException
     * @throws InvalidFileLocation
     */
    protected function createLocatedSource(Identifier $identifier) : ?LocatedSource
    {
        $reflection = $this->getInternalReflection($identifier);

        if ($reflection === null) {
            return null;
        }

        $stub = $this->findStub($reflection);

        return $stub === null ? null :
            new InternalLocatedSource($stub, $reflection->getExtensionName());
    }

    private function findStub(Reflector $reflection) : ?string
    {
        if ($reflection instanceof ReflectionClass) {
            return $this->stubber->findClassStub($reflection);
        }

        if ($reflection instanceof ReflectionFunction) {
            return $this->stubber->findFunctionStub($reflection);
        }

        return null;
    }

    private function getInternalReflection(Identifier $identifier) : ?Reflector
    {
        $name = $identifier->getName();

        if (! (class_exists($name, false) || interface_exists($name, false) ||
            trait_exists($name, false) || function_exists($name))
        ) {
            return null; // not an available internal class or function
        }

        if ($identifier->isClass()) {
            $reflection = new ReflectionClass($name);
        }

        if ($identifier->isFunction()) {
            $reflection = new ReflectionFunction($name);
        }

        return $reflection->isInternal() ? $reflection : null;
    }
}
