<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\AdminController;
use Application\Model\Table\QuizzesTable;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class AdminControllerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName ,array $options = null)
	{
		return new AdminController(
			$container->get(QuizzesTable::class)
		);
	}
}
