<?php

namespace MWSimple\Bundle\AdminCrudBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $filter = $this->em->getFilters()->enable('logical_erasing_filter');
    }
}
