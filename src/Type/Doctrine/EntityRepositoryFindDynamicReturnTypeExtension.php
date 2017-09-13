<?php declare(strict_types = 1);

namespace PHPStan\Type\Doctrine;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\GenericObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

class EntityRepositoryFindDynamicReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{

	public static function getClass(): string
	{
		return \Doctrine\ORM\EntityRepository::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		return in_array($methodReflection->getName(), [
			'find',
			'findOneBy',
		], true);
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		$repositoryType = $scope->getType($methodCall->var);
		if (!($repositoryType instanceof GenericObjectType)) {
			return $methodReflection->getReturnType();
		}

		$type = TypeCombinator::addNull($repositoryType->getGenericType());

		return $type;
	}

}
