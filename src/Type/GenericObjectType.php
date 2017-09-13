<?php declare(strict_types = 1);

namespace PHPStan\Type;

class GenericObjectType extends ObjectType
{

	/** @var \PHPStan\Type\Type */
	private $genericType;

	public function __construct(string $class, Type $genericType)
	{
		parent::__construct($class);
		$this->genericType = $genericType;
	}

	public function getGenericType(): Type
	{
		return $this->genericType;
	}

}
