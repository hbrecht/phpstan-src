<?php declare(strict_types = 1);

namespace PHPStan\Type\Php;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Analyser\SpecifiedTypes;
use PHPStan\Analyser\TypeSpecifier;
use PHPStan\Analyser\TypeSpecifierAwareExtension;
use PHPStan\Analyser\TypeSpecifierContext;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\FloatType;
use PHPStan\Type\FunctionTypeSpecifyingExtension;

class IsFloatFunctionTypeSpecifyingExtension implements FunctionTypeSpecifyingExtension, TypeSpecifierAwareExtension
{

	private \PHPStan\Analyser\TypeSpecifier $typeSpecifier;

	public function isFunctionSupported(FunctionReflection $functionReflection, FuncCall $node, TypeSpecifierContext $context): bool
	{
		return in_array(strtolower($functionReflection->getName()), [
			'is_float',
			'is_double',
			'is_real',
		], true)
			&& !$context->null();
	}

	public function specifyTypes(FunctionReflection $functionReflection, FuncCall $node, Scope $scope, TypeSpecifierContext $context): SpecifiedTypes
	{
		if (!isset($node->getArgs()[0])) {
			return new SpecifiedTypes();
		}
		if ($context->null()) {
			throw new \PHPStan\ShouldNotHappenException();
		}

		return $this->typeSpecifier->create($node->getArgs()[0]->value, new FloatType(), $context, false, $scope);
	}

	public function setTypeSpecifier(TypeSpecifier $typeSpecifier): void
	{
		$this->typeSpecifier = $typeSpecifier;
	}

}
