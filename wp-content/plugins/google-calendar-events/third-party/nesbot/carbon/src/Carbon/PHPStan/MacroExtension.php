<?php

namespace SimpleCalendar\plugin_deps\Carbon\PHPStan;

use SimpleCalendar\plugin_deps\PHPStan\Reflection\ClassReflection;
use SimpleCalendar\plugin_deps\PHPStan\Reflection\MethodReflection;
use SimpleCalendar\plugin_deps\PHPStan\Reflection\MethodsClassReflectionExtension;
use SimpleCalendar\plugin_deps\PHPStan\Reflection\Php\PhpMethodReflectionFactory;
use SimpleCalendar\plugin_deps\PHPStan\Type\TypehintHelper;
/**
 * Class MacroExtension.
 *
 * @codeCoverageIgnore Pure PHPStan wrapper.
 */
final class MacroExtension implements MethodsClassReflectionExtension
{
    /**
     * @var PhpMethodReflectionFactory
     */
    protected $methodReflectionFactory;
    /**
     * @var MacroScanner
     */
    protected $scanner;
    /**
     * Extension constructor.
     *
     * @param PhpMethodReflectionFactory $methodReflectionFactory
     */
    public function __construct(PhpMethodReflectionFactory $methodReflectionFactory)
    {
        $this->scanner = new \SimpleCalendar\plugin_deps\Carbon\PHPStan\MacroScanner();
        $this->methodReflectionFactory = $methodReflectionFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function hasMethod(ClassReflection $classReflection, string $methodName) : bool
    {
        return $this->scanner->hasMethod($classReflection->getName(), $methodName);
    }
    /**
     * {@inheritdoc}
     */
    public function getMethod(ClassReflection $classReflection, string $methodName) : MethodReflection
    {
        $builtinMacro = $this->scanner->getMethod($classReflection->getName(), $methodName);
        return $this->methodReflectionFactory->create($classReflection, null, $builtinMacro, $classReflection->getActiveTemplateTypeMap(), [], TypehintHelper::decideTypeFromReflection($builtinMacro->getReturnType()), null, null, $builtinMacro->isDeprecated()->yes(), $builtinMacro->isInternal(), $builtinMacro->isFinal(), $builtinMacro->getDocComment());
    }
}
