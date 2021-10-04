<?php

declare(strict_types=1);

namespace User;

use Laminas\Db\Adapter\Adapter;
use User\Model\Table\ForgotTable;
use User\Model\Table\RolesTable;
use User\Model\Table\UsersTable;
use User\Plugin\AuthPlugin;
use User\Plugin\Factory\AuthPluginFactory;
use User\View\Helper\AuthHelper;

class Module
{
	public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig(): array 
    {
    	return [
    		'factories' => [
                ForgotTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new ForgotTable($dbAdapter);
                },
    			UsersTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new UsersTable($dbAdapter);
    			},
                RolesTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new RolesTable($dbAdapter);
                }
    		]
    	];
    }

    # let the framework know about your plugin
    public function getControllerPluginConfig()
    {
        return [
            'aliases' => [
                'authPlugin' => AuthPlugin::class,
            ],
            'factories' => [
                AuthPlugin::class => AuthPluginFactory::class
            ],
        ];
    }

    # let the service_manager know about your helper
    public function getViewHelperConfig()
    {
        return [
            'aliases' => [
                'authHelper' => AuthHelper::class,
            ],
            'factories' => [
                AuthHelper::class => AuthPluginFactory::class
            ]
        ];
    }
}
