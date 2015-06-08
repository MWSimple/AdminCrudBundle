<?php

namespace MWSimple\Bundle\AdminCrudBundle\Form\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Lexik\Bundle\FormFilterBundle\Event\GetFilterConditionEvent;
use Symfony\Component\HttpFoundation\Request;

class FilterSubscriber implements EventSubscriberInterface {

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents() {
        return array(
            'lexik_form_filter.apply.orm.filter_text_like' => array('filterTextLike'),
            'lexik_form_filter.apply.dbal.filter_text_like' => array('filterTextLike'),
            'lexik_form_filter.apply.orm.select2' => array('filterTextEntity'),
            'lexik_form_filter.apply.dbal.select2' => array('filterTextEntity'),
        );
    }

    /**
     * Apply a filter for a filter_locale type.
     *
     * This method should work whih both ORM and DBAL query builder.
     */
    public function filterTextLike(GetFilterConditionEvent $event) {
        $qb = $event->getQueryBuilder();
        $expr = $event->getFilterQuery()->getExpressionBuilder();
        $values = $event->getValues();

        if ('' !== $values['value'] && null !== $values['value']) {
            if (isset($values['condition_pattern'])) {
                $qb->andWhere($expr->stringLike($event->getField(), "%" . $values['value'] . "%", $values['condition_pattern']));
            } else {
                $qb->andWhere($expr->stringLike($event->getField(), "%" . $values['value'] . "%"));
            }
        }
    }

    public function filterTextEntity(GetFilterConditionEvent $event) {
        $qb = $event->getQueryBuilder();
        $values = $event->getValues();
        $alias = $values["alias"];
        $select = $qb->getDQLPart("select")[0]->getParts();
        if (!empty($values['value'])) {
            $qb->select($select)
                    //   ->leftJoin($field, $alias)
                    ->where($alias . '.id = :id')
                    ->setParameter('id', $values['value']->getId())
                    ->orderBy($alias . '.id', 'DESC')
                    ->getQuery()
            ;
        }
    }

}
