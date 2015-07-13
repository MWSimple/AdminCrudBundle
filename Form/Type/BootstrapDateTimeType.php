<?php
namespace MWSimple\Bundle\AdminCrudBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use MWSimple\Bundle\AdminCrudBundle\Form\DataTransformer\BootstrapDateTimeTransformer;

class BootstrapDateTimeType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $transformer = new BootstrapDateTimeTransformer($options['widget_type']);
        $builder->addViewTransformer($transformer, true);
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['widget_type'] = $options['widget_type'];
        $view->vars['options'] = $this->createDisplayOptions($options);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'widget' => 'single_text',
            'format' => 'yyyy-MM-dd HH:mm',
            'widget_type' => 'date',
            'language' => 'es',
            'minute_step' => 5,
            'start_view' => 4,
            'start_date' => '1900-01-01',
            'end_date' => '2020-01-01',
            'disabled_days' => array(),
            'autoclose' => true,
            'today_highlight' => true,
            'days_of_week_disabled' => array(),
        ));

        $resolver->setAllowedValues(
                array(
                    'widget' => array('single_text'),
                    'widget_type' => array('both', 'date', 'time', 'month', 'day'),
                    'start_view' => array(0, 1, 2, 3, 4),
                )
        );
    }

    public function getParent() {
        return 'datetime';
    }

    public function getName() {
        return 'bootstrapdatetime';
    }

    private function createDisplayOptions($options = array()) {
        $displayOptions = array();
        $displayOptions['linkFormat'] = 'yyyy-mm-dd hh:ii';
        $displayOptions['autoclose'] = $options['autoclose'];
        $displayOptions['startView'] = $options['start_view'];
        $displayOptions['daysOfWeekDisabled'] = $options['days_of_week_disabled'];
        $displayOptions['disabledDays'] = $options['disabled_days'];
        $displayOptions['startDate'] = $options['start_date'];
        $displayOptions['todayHighlight'] = $options['today_highlight'];
        $displayOptions['endDate'] = $options['end_date'];
        $displayOptions['language'] = $options['language'];
        $displayOptions['minuteStep'] = (integer) $options['minute_step'];
        switch ($options['widget_type']) {
            case 'both':
                $displayOptions['format'] = 'dd/mm/yyyy hh:ii';
                $displayOptions['minView'] = 0;
                break;
            case 'date':
                $displayOptions['format'] = 'dd/mm/yyyy';
                $displayOptions['minView'] = 2;
                break;
            case 'month':
                $displayOptions['format'] = 'mm/yyyy';
                $displayOptions['minView'] = 3;
                $displayOptions['maxView'] = 4;
                break;
            case 'time':
                $displayOptions['format'] = 'hh:ii';
                $displayOptions['startView'] = 1;
                $displayOptions['minView'] = 0;
                $displayOptions['maxView'] = 1;
                break;
            case 'day':
                $displayOptions['format'] = 'hh:ii';
                $displayOptions['startView'] = 1;
                $displayOptions['minView'] = 1;
                $displayOptions['maxView'] = 1;
                $displayOptions['initialDate'] = "00";
                break;
        }

        return json_encode($displayOptions);
    }

}
