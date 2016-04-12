<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
     {

         return array(
             'factories' => array(
                 'Application\Model\PostTable' =>  function($sm) {
                     $tableGateway = $sm->get('PostTableGateway');
                     $table = new \Application\Model\PostTable($tableGateway);
                     return $table;
                 },
                 'PostTableGateway' => function ($sm) {
                     $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                     $resultSetPrototype = new \Zend\Db\ResultSet\ResultSet();
                     $resultSetPrototype->setArrayObjectPrototype(new \Application\Model\Post());
                     return new \Zend\Db\TableGateway\TableGateway('post', $dbAdapter, null, $resultSetPrototype);
                 },
             ),
         );
     }
}
