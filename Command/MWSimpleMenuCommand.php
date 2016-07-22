<?php

namespace MWSimple\Bundle\AdminCrudBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
        $name_min = strtolower($name);
        $url = $input->getArgument('url');
        $configPath = $input->getArgument('config');

        $file = $this->getContainer()->getParameter('kernel.root_dir').'/../'.$configPath;
        // if the config.yml file doesn't exist, don't even try.
        if (!file_exists($file)) {
            throw new \RuntimeException(sprintf('The target config file %s does not exist', $file));
        }

        $currentContents = file_get_contents($file);
        $configs = Yaml::parse($currentContents);

        // Add if exist config
        if (!array_key_exists('mw_simple_admin_crud', $configs)) {
            throw new \RuntimeException(sprintf(
                'The %s configuration file from %s is not exist',
                $configPath,
                'mw_simple_admin_crud:'
            ));
        }
        if (!array_key_exists('menu', $configs['mw_simple_admin_crud'])) {
            throw new \RuntimeException(sprintf(
                'The %s configuration file from %s is not exist',
                $configPath,
                'menu:'
            ));
        }
        foreach ($configs['mw_simple_admin_crud']['menu'] as $key => $child) {
            if (strtolower($key) === $name_min) {
                throw new \RuntimeException(sprintf(
                    'In menu: already defined index: %s',
                    $name_min
                ));
            }
        }
        // find menu:
        $importsPosition = strpos($currentContents, 'menu:');
        // find the last url entry
        $lastImport = end($configs['mw_simple_admin_crud']['menu']);
        if (!isset($lastImport['url'])) {
            $lastImportedPath = false;
        } else {
            $lastImportedPath = $lastImport['url'];
        }
        if (!$lastImportedPath) {
            throw new \RuntimeException(sprintf('Could not find the url key in %s', $configPath));
        }
        // find the last url
        $lastImportPosition = strpos($currentContents, $lastImportedPath, $importsPosition);
        // find the line break after the last import
        $targetLinebreakPosition = strpos($currentContents, "\n", $lastImportPosition);

        $code = sprintf("        %s: { name: %s, url: %s, id: %s, icon: glyphicon glyphicon-home }", $name_min, $name, $url, $name_min);

        $newContents = substr($currentContents, 0, $targetLinebreakPosition)."\n".$code.substr($currentContents, $targetLinebreakPosition);
        if (false === file_put_contents($file, $newContents)) {
            throw new \RuntimeException(sprintf('Could not write file %s ', $file));
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
