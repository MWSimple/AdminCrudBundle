<?php

namespace MWSimple\Bundle\AdminCrudBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class MWSimpleMenuCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mwsimple:menu:additem')
            ->setAliases(array('mwsimple:menu:additem'))
            ->setDescription('Agrega Item al Menu, por el momento solamente configuracion en .yml')
            ->addArgument('name', InputArgument::REQUIRED, 'Nombre Label para el item por ejemplo: Admin')
            ->addArgument('url', InputArgument::REQUIRED, 'nombre de la ruta al index por ejemplo: admin_inicio')
            ->addArgument('config', InputArgument::OPTIONAL, 'path a config.yml por ejemplo: app/config/config.yml?')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->updateConfig($input, $output);
    }

    private function updateConfig(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $url = $input->getArgument('url');
        $configPath = $input->getArgument('config');

        $configs = Yaml::parse(file_get_contents($this->getContainer()->getParameter('kernel.root_dir').'/../'.$configPath));
        foreach ($configs as $key => $value) {
            $output->writeln($key);
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        // name
        $name = $input->getArgument('name');

        $default = 'Admin';
        $questionString = sprintf('<info>Ingrese el Nombre o Label para el item</info> [<comment>%s</comment>]', $default);
        $question = new Question($questionString, $default);

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

        $default = 'admin_inicio';
        $questionString = sprintf('<info>Ingrese el Nombre de la ruta al action</info> [<comment>%s</comment>]', $default);
        $question = new Question($questionString, $default);

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
        
        $default = 'app/config/config.yml';
        $questionString = sprintf('<info>El path a config.yml donde se incluye mw_simple_admin_crud?</info> [<comment>%s</comment>]', $default);
        $question = new Question($questionString, $default);

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