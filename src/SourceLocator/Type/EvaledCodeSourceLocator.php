<?php

declare(strict_types=1);

namespace Roave\BetterReflection\SourceLocator\Type;

use InvalidArgumentException;
use ReflectionClass;
use Roave\BetterReflection\Identifier\Identifier;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Exception\InvalidFileLocation;
use Roave\BetterReflection\SourceLocator\Located\EvaledLocatedSource;
use Roave\BetterReflection\SourceLocator\Located\LocatedSource;
use Roave\BetterReflection\SourceLocator\StubLocator\AggregateStubLocator;
use Roave\BetterReflection\SourceLocator\StubLocator\BetterReflectionStubLocator;
use Roave\BetterReflection\SourceLocator\StubLocator\CoreReflectionStubLocator;
use Roave\BetterReflection\SourceLocator\StubLocator\StubLocator;
use function class_exists;
use function file_exists;
use function interface_exists;
use function trait_exists;

final class EvaledCodeSourceLocator extends AbstractSourceLocator
{
    /** @var StubLocator */
    private $stubLocator;

    public function __construct(Locator $astLocator, ?StubLocator $stubLocator = null)
    {
        parent::__construct($astLocator);

        $this->stubLocator = $stubLocator ??
             new AggregateStubLocator(
                 new BetterReflectionStubLocator(),
                 new CoreReflectionStubLocator()
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
        $classReflection = $this->getInternalReflectionClass($identifier);

        if ($classReflection === null) {
            return null;
        }

        $stub = $this->stubLocator->findClassStub($classReflection);

        return $stub === null ? null : new EvaledLocatedSource($stub);
    }

    private function getInternalReflectionClass(Identifier $identifier) : ?ReflectionClass
    {
        if (! $identifier->isClass()) {
            return null;
        }

        $name = $identifier->getName();

        if (! (class_exists($name, false) || interface_exists($name, false) || trait_exists($name, false))) {
            return null; // not an available internal class
        }

        $reflection = new ReflectionClass($name);
        $sourceFile = $reflection->getFileName();

        return $sourceFile && file_exists($sourceFile)
            ? null : $reflection;
    }
}
