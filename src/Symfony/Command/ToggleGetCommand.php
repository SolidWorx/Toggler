<?php

declare(strict_types=1);

/*
 * This file is part of the Toggler package.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SolidWorx\Toggler\Symfony\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ToggleGetCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('toggler:get')
            ->setDescription('Get the status of a specific feature')
            ->addArgument('feature', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The feature to get the status')
            ->addOption('context', 'c', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Add context to the feature check')
            ->setHelp(<<<HELP
Get the status of a specific feature:

    <info>$ bin/console %command.name% feature</info>
    
To get the status of multiple features, you can pass more than one feature name:

    <info>$ bin/console %command.name% featurea featureb</info> 

To add context to the feature, you can use the --context (or -c shortcut) flag.
Context must be in the format "key=value". Multiple values can be passed

    <info>$ bin/console %command.name% feature -c userid=123 -c foo=bar </info>

<comment>Note: If you pass multiple features and context values, the context will be used to check against each feature</comment>
HELP
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $toggle = $container->get('toggler.toggle');

        $features = $input->getArgument('feature');

        $context = [];

        foreach ($input->getOption('context') as $parameter) {
            if (false === strpos($parameter, '=')) {
                throw new \Exception(sprintf('The context "%s" is invalid. The format needs to be key=value', $parameter));
            }

            [$key, $value] = explode('=', $parameter);

            $context[$key] = $value;
        }

        $table = new Table($output);

        $headers = ['Feature', 'Status'];

        if (!empty($context)) {
            $headers[] = 'Context';
        }

        $table->setHeaders($headers);

        foreach ($features as $feature) {
            $active = $toggle->isActive($feature, $context);

            $row = [
                $feature,
                sprintf('<%1$s>%2$s</%1$s>', $active ? 'info' : 'error', $active ? 'Active' : 'Not-Active'),
            ];

            if (!empty($context)) {
                $row[] = json_encode($context);
            }

            $table->addRow($row);
        }

        $table->render();
    }
}
