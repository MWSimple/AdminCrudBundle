<?php
namespace MWSimple\Bundle\AdminCrudBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class ACLListener
{
	private $container;

	public function __construct($container)
    {
        $this->container = $container;
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $control = false;
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        // si es la entity entra.
        $control = $this->isInstanceOf($entity, $aclConf['entities']);
        if ($control) {
            $aclManager = $this->container->get('mws_acl_manager');
            //control ACL
            $aclManager->controlACL($entity, 'DELETE');
            //elimino ACL
            $aclManager->deleteACL($entity);
        }
    }

    protected function isInstanceOf($object, Array $classnames)
    {
        foreach($classnames as $classname) {
            if($object instanceof $classname){
                return true;
            }
        }
        return false;
    }
}