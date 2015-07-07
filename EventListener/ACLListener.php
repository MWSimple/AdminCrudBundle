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
        $entity  = $args->getEntity();

        $aclConf = $this->container->hasParameter('mw_simple_admin_crud.acl') ?
            $this->container->getParameter('mw_simple_admin_crud.acl') : null;
        if ($aclConf['use']) {
            if ($this->isInstanceOf($entity, $aclConf['entities'])) {
                $aclManager = $this->container->get('mws_acl_manager');
                //control ACL
                $aclManager->controlACL($entity, 'DELETE', $aclConf['exclude_role']);
                //elimino ACL
                $aclManager->deleteACL($entity);
            }
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