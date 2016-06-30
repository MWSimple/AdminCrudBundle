<?php

namespace MWSimple\Bundle\AdminCrudBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\GenerateDoctrineCrudCommand;
use MWSimple\Bundle\AdminCrudBundle\Generator\MWSimpleCrudGenerator;
use MWSimple\Bundle\AdminCrudBundle\Generator\MWSimpleFormGenerator;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
//Interact
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Sensio\Bundle\GeneratorBundle\Command\AutoComplete\EntitiesAutoCompleter;

/**
 * Generates a CRUD for a Doctrine entity.
 *
 * @author Gonzalo Alonso <gonkpo@gmail.com>
 */
class MWSimpleCrudCommand extends GenerateDoctrineCrudCommand
{
    protected $formGenerator;

    protected function configure()
    {
        parent::configure();

        $this->setName('mwsimple:generate:admincrud');
        $this->setAliases(array('mwsimple:generate:admincrud'));
        $this->setDescription('Generates a ADMINCRUD and paginator based on a Doctrine entity');
    }

    protected function createGenerator($bundle = null)
    {
        $crudGenerator = new MWSimpleCrudGenerator(
            $this->getContainer()->get('filesystem'),
            $this->getContainer()->getParameter('kernel.root_dir')
        );
        //Agregado por Tecspro para setear el servicio
        $crudGenerator->setMwsTecsproComun($this->getContainer()->get('mws.tecspro.comun'));

        return $crudGenerator;
    }

    protected function getFormGenerator($bundle = null)
    {
        if (null === $this->formGenerator) {
            $this->formGenerator = new MWSimpleFormGenerator($this->getContainer()->get('filesystem'));
            //Agregado por Tecspro para setear el servicio
            $this->formGenerator->setMwsTecsproComun($this->getContainer()->get('mws.tecspro.comun'));
            $this->formGenerator->setSkeletonDirs($this->getSkeletonDirs($bundle));
        }

        return $this->formGenerator;
    }

    protected function getSkeletonDirs(BundleInterface $bundle = null)
    {
        $skeletonDirs = array();

        if (isset($bundle) && is_dir($dir = $bundle->getPath().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        if (is_dir($dir = $this->getContainer()->get('kernel')->getRootdir().'/Resources/SensioGeneratorBundle/skeleton')) {
            $skeletonDirs[] = $dir;
        }

        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@MWSimpleAdminCrudBundle/Resources/skeleton');
        $skeletonDirs[] = $this->getContainer()->get('kernel')->locateResource('@MWSimpleAdminCrudBundle/Resources');

        return $skeletonDirs;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questionHelper = $this->getQuestionHelper();
        $questionHelper->writeSection($output, 'Admin CRUD Generator');

        parent::interact($input, $output);
    }
}