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

use Exception;
use SolidWorx\Toggler\Storage\StorageInterface;
use SolidWorx\Toggler\ToggleInterface;
use Symfony\Component\Console\Input\InputOption;
use function explode;
use function sprintf;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function strpos;

#[AsCommand(name: 'toggler:list', description: 'List all the configured features')]
class ToggleListCommand extends Command
{
    protected static $defaultName = 'toggler:list';

    /**
     * @var ToggleInterface
     */
    private $toggle;

    /**
     * @var StorageInterface
     */
    private $toggleStorage;

    public function __construct(ToggleInterface $toggle, StorageInterface $toggleStorage)
    {
        $this->toggle = $toggle;
        $this->toggleStorage = $toggleStorage;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('List all the configured features')
            ->addOption('context', 'c', InputOption::VALUE_IS_ARRAY | InputOption::VALUE_REQUIRED, 'Add context to the feature check')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = [];

        foreach ((array) $input->getOption('context') as $parameter) {
            if (false === strpos(strval($parameter), '=')) {
                throw new Exception(sprintf('The context "%s" is invalid. The format needs to be key=value', strval($parameter)));
            }

            [$key, $value] = explode('=', strval($parameter));

            $context[$key] = $value;
        }

        $table = new Table($output);

        $headers = ['Feature', 'Status'];

        if ([] !== $context) {
            $headers[] = 'Context';
        }

        $table->setHeaders($headers);

        foreach ($this->toggleStorage->all() as $feature) {
            $active = $this->toggle->isActive(strval($feature), $context);

            $row = [
                $feature,
                sprintf('<%1$s>%2$s</%1$s>', $active ? 'info' : 'error', $active ? 'Active' : 'Not-Active'),
            ];

            if ([] !== $context) {
                $row[] = json_encode($context, JSON_THROW_ON_ERROR);
            }

            $table->addRow($row);
        }

        $table->render();

        return 0;
    }
}
