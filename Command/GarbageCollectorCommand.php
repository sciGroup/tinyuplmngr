<?php
/**
 * (c) Artem Ostretsov <artem@ostretsov.ru>
 * Created at 10.07.2015 18:07
 */

namespace SciGroup\TinymcePluploadFileManagerBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GarbageCollectorCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('scigroup:tpfm:garbage-collector')
            ->setDescription('Remove old garbage files')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('sci_group.tpfm.content_file_manager')->removeGarbageFiles();
    }
}