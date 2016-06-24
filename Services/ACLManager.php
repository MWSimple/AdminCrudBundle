<?php
namespace MWSimple\Bundle\AdminCrudBundle\Services;
//ACL
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ACLManager
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function userCreateACL($entity, $user) {
        // creating the ACL
        $aclProvider = $this->container->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $acl = $aclProvider->createAcl($objectIdentity);

        // retrieving the security identity of the user parameter
        $securityIdentity = UserSecurityIdentity::fromAccount($user);

        // grant owner access
        $acl->insertObjectAce($securityIdentity, MaskBuilder::MASK_OPERATOR);
        $aclProvider->updateAcl($acl);
    }

    public function createACL($entity) {
        // retrieving the security identity of the currently logged-in user
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        $this->userCreateACL($entity, $user);
    }
    
    public function deleteACL($entity) {
        $aclProvider = $this->container->get('security.acl.provider');
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $aclProvider->deleteAcl($objectIdentity);
    }

    public function controlACL($entity, $permiso, $exclude_role) {
        $securityContext = $this->container->get('security.context');
        // check $exclude_role false or example ROLE_SUPER_ADMIN
        if (false === $exclude_role || false === $securityContext->isGranted($exclude_role)) {
            // check access
            if (false === $securityContext->isGranted($permiso, $entity)) {
                throw new AccessDeniedException();
            }
        }
    }
}