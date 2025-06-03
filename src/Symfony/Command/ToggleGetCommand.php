<?php

declare(strict_types=1);

/*
 * This file is part of SolidWorx Toggler project.
 *
 * (c) SolidWorx <open-source@solidworx.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace SolidWorx\Toggler\Symfony\Command;

use Exception;
use JsonException;
use SolidWorx\Toggler\ToggleInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function explode;
use function sprintf;

#[AsCommand(name: 'toggler:get', description: 'Get the status of a specific feature')]
class ToggleGetCommand extends Command
{
    protected static $defaultName = 'toggler:get';

    private ToggleInterface $toggle;

    public function __construct(ToggleInterface $toggle)
    {
        $this->toggle = $toggle;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Get the status of a specific feature')
            ->addArgument('feature', InputArgument::REQUIRED | InputArgument::IS_ARRAY, 'The feature to get the status')
            ->addOption('context', 'c', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Add context to the feature check')
            ->setHelp(
                <<<'HELP'
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
     * @throws JsonException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $features = (array) $input->getArgument('feature');

        $context = [];

        /** @var string $parameter $parameter */
        foreach ((array) $input->getOption('context') as $parameter) {
            if (! str_contains($parameter, '=')) {
                throw new Exception(sprintf('The context "%s" is invalid. The format needs to be key=value', $parameter));
            }

            [$key, $value] = explode('=', $parameter);

            $context[$key] = $value;
        }

        $table = new Table($output);

        $headers = ['Feature', 'Status'];

        if ($context !== []) {
            $headers[] = 'Context';
        }

        $table->setHeaders($headers);

        /** @var string $feature */
        foreach ($features as $feature) {
            $active = $this->toggle->isActive($feature, $context);

            $row = [
                $feature,
                sprintf('<%1$s>%2$s</%1$s>', $active ? 'info' : 'error', $active ? 'Active' : 'Not-Active'),
            ];

            if ($context !== []) {
                $row[] = json_encode($context, JSON_THROW_ON_ERROR);
            }

            $table->addRow($row);
        }

        $table->render();

        return 0;
    }
}
