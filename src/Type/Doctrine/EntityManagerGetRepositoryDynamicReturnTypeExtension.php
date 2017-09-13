<?php declare(strict_types = 1);

namespace PHPStan\Type\Doctrine;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\GenericObjectType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class EntityManagerGetRepositoryDynamicReturnTypeExtension implements \PHPStan\Type\DynamicMethodReturnTypeExtension
{

	public static function getClass(): string
	{
		return \Doctrine\ORM\EntityManager::class;
	}

	public function isMethodSupported(MethodReflection $methodReflection): bool
	{
		$supported = in_array($methodReflection->getName(), [
			'getRepository',
		], true);
		return $supported;
	}

	public function getTypeFromMethodCall(
		MethodReflection $methodReflection,
		MethodCall $methodCall,
		Scope $scope
	): Type
	{
		if (count($methodCall->args) !== 1) {
			return $methodReflection->getReturnType();
		}

		$arg = $methodCall->args[0]->value;
		if (!($arg instanceof \PhpParser\Node\Expr\ClassConstFetch)) {
			return $methodReflection->getReturnType();
		}

		$entityClass = $arg->class;
		if (!($entityClass instanceof \PhpParser\Node\Name)) {
			return $methodReflection->getReturnType();
		}

		$entityClass = (string) $entityClass;

		$repositoryClass = $methodReflection->getReturnType()->getClass();
		if ($repositoryClass === null) {
			return $methodReflection->getReturnType();
		}

		$entityType = new ObjectType($entityClass);
		$type = new GenericObjectType($repositoryClass, $entityType);

		return $type;
	}

}
