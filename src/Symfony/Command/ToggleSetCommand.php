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

use SolidWorx\Toggler\Storage\PersistenStorageInterface;
use SolidWorx\Toggler\Storage\StorageInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ToggleSetCommand extends ContainerAwareCommand
{
    private $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('toggler:set')
            ->setDescription('Change the status of a specific feature')
            ->addArgument('feature', InputArgument::REQUIRED, 'The feature to change the status')
            ->addArgument('value', InputArgument::REQUIRED, 'The status to set the feature to (can be either true|false or 1|0)')
            ->setHelp(<<<HELP
Set the status of a specific feature:

    <info>$ bin/console %command.name% feature on</info>

The accepted value can be any of the following values:

* (boolean) true|false
* (int)     1|0
* (string) '1'|'0'
* (string) 'on'|'off'
* (string) 'true'|'false
HELP
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->storage instanceof PersistenStorageInterface) {
            throw new \Exception(sprintf('The storage class %s does not support updating a value.', get_class($this->storage)));
        }

        $feature = $input->getArgument('feature');

        $this->storage->set($feature, $this->isTruthy($input->getArgument('value')));

        $output->writeln(sprintf('<info>Feature %s updated</info>', $feature));
    }

    private function isTruthy($value): bool
    {
        if (is_bool($value)) {
            return true === $value;
        }

        if (is_int($value)) {
            return 1 === $value;
        }

        if (is_string($value)) {
            if ((int) $value > 0) {
                return 1 === (int) $value;
            }

            return in_array(strtolower($value), ['on', 'true'], true);
        }

        return false;
    }
}
