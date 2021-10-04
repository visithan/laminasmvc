<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Application;

use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            /**
             * Routes work such that the one placed last will be outputted first (Last In First Out - LIFO)
             *
             */
            'admin_quiz' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/admin/quiz[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'id' => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AdminController::class,
                    ],
                ],
            ],
            'help' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/help[/:action]',
                    'constraints' => [
                        'action' => '(contact|privacy|terms)',
                    ],
                    'defaults' => [
                        'controller' => Controller\HelpController::class,
                    ],
                ],
            ],
            'quiz' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/quiz[/:action[/:id[/:slug]]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]+',
                        'id' => '[0-9]+',
                        'slug' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\QuizController::class,
                    ],
                ],
            ],
            /*'application' => [
                'type' => Segment::class,
                'options' => [
                    'route' => '/application[/:action]',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],*/
            'home' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\AdminController::class => Controller\Factory\AdminControllerFactory::class,
            Controller\HelpController::class  => InvokableFactory::class,
            Controller\IndexController::class => Controller\Factory\IndexControllerFactory::class,
            Controller\QuizController::class  => Controller\Factory\QuizControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
            # admin template map
            'admin/index' => __DIR__ . '/../view/application/admin/index.phtml',
            # index template map
            'index/index' => __DIR__ . '/../view/application/index/index.phtml',
            # help template map
            'help/contact' => __DIR__ . '/../view/application/help/contact.phtml',
            'help/privacy' => __DIR__ . '/../view/application/help/privacy.phtml',
            'help/terms' => __DIR__ . '/../view/application/help/terms.phtml',
            # quiz template map
            'quiz/index' => __DIR__ . '/../view/application/quiz/index.phtml',
        ],
        'template_path_stack' => [
            'application' => __DIR__ . '/../view',
        ],
    ],
];
