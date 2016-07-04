<?php

namespace MWSimple\Bundle\AdminCrudBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class MWSimpleMenuCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('mwsimple:menu:additem')
            ->setAliases(array('mwsimple:menu:additem'))
            ->setDescription('Agrega Item al Menu')
            ->addArgument('name', InputArgument::REQUIRED, 'Nombre Label para el item por ejemplo: Admin')
            ->addArgument('url', InputArgument::REQUIRED, 'nombre de la ruta al index por ejemplo: admin_inicio')
            ->addArgument('config', InputArgument::OPTIONAL, 'path a config.yml por ejemplo: app/config/config.yml?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $url = $input->getArgument('url');
        $config = $input->getArgument('config');

        //$targetRoutingPath = $this->getContainer()->getParameter('kernel.root_dir').'/config/routing.yml';


        $output->writeln($name);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        // name
        $name = $input->getArgument('name');
        $output->writeln(array(
            '',
            '<info>Ingrese el Nombre o Label para el item</info>',
            '',
        ));
        $question = new Question('[<comment>Admin</comment>]: ', $name);
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('Ingrese un valor');
            }

            return $value;
        });
        $name = $helper->ask($input, $output, $question);

        $input->setArgument('name', $name);
        // url
        $url = $input->getArgument('url');
        $output->writeln(array(
            '',
            '<info>Ingrese el Nombre de la ruta al action</info>',
            '',
        ));
        $question = new Question('[<comment>admin_inicio</comment>]: ', $url);
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('Ingrese un valor');
            }

            return $value;
        });
        $url = $helper->ask($input, $output, $question);

        $input->setArgument('url', $url);
        // config
        $config = $input->getArgument('config');
        $output->writeln(array(
            '',
            '<info>Ingrese el path a config.yml</info>',
            '',
        ));
        $question = new Question('Enter por defecto [<comment>app/config/config.yml</comment>]: ', $config);
        $question->setValidator(function ($value) {
            if (trim($value) == '') {
                throw new \Exception('Ingrese un valor');
            }

            return $value;
        });
        $config = $helper->ask($input, $output, $question);

        $input->setArgument('config', $config);
    }
}