<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Application\Model\Table\AnswersTable;
use Application\Model\Table\CategoriesTable;
use Application\Model\Table\QuizzesTable;
use Application\Model\Table\TalliesTable;
use Application\Form\Quiz\CreateForm;
use Laminas\Db\Adapter\Adapter;
use Laminas\ServiceManager\Factory\InvokableFactory;

class Module
{
    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
    	return [
    		'factories' => [
    			AnswersTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new AnswersTable($dbAdapter);
    			},
    			CategoriesTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new CategoriesTable($dbAdapter);
    			},
    			QuizzesTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new QuizzesTable($dbAdapter);
    			},
    			TalliesTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new TalliesTable($dbAdapter);
    			},
    		]
    	];
    }

    public function getFormElementConfig()
    {
        return [
            'factories' => [
                CreateForm::class => function($sm) {
                    $categoriesTable = $sm->get(CategoriesTable::class);
                    return new CreateForm($categoriesTable);
                }
            ]
        ];
    }
}
