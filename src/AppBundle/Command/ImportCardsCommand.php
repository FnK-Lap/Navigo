<?php

namespace AppBundle\Command;

use AppBundle\Entity\Card;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;



class ImportCardsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navigo:cards:import')
            ->setDescription('Import cards file')
            ->addArgument('file', InputArgument::REQUIRED, 'File with cards')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Import <fg=cyan>cards</fg=cyan>');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(null);


        $file = $input->getArgument('file');

        $handle = @fopen($file, "r");
        if ($handle) {
            $output->writeln('<fg=green>File found !</fg=green>');

            $index = 0;
            $total = 0;
            $date = new \DateTime;
            
            $progress = new ProgressBar($output, 111111111);
            $progress->setFormat('Insert <fg=green>%message%</fg=green> cards | time: %elapsed:6s% | memory: %memory:6s%');
            $progress->setMessage($total);
            $progress->start();

            while (($buffer = fgets($handle, 4096)) !== false) {
                if ($index == 0) {
                    $sql = 'INSERT INTO card (serial_number, is_active, created_at, updated_at) VALUES ("'.rtrim($buffer).'", 0, "'.$date->format('Y-m-d H:I:s').'", "'.$date->format('Y-m-d H:I:s').'")'.PHP_EOL;
                    $index++;
                    $total++;
                } elseif ($index == 9999) {
                    $sql .= ', ("'.rtrim($buffer).'", 0, "'.$date->format('Y-m-d H:I:s').'", "'.$date->format('Y-m-d H:I:s').'")'.PHP_EOL;
                    $index++;
                    $total++;
                    $result = $em->getConnection()->query($sql);
                    $progress->setMessage($total);
                    $progress->advance();
                    $index = 0;
                } else {
                    $sql .= ', ("'.rtrim($buffer).'", 0, "'.$date->format('Y-m-d H:I:s').'", "'.$date->format('Y-m-d H:I:s').'")'.PHP_EOL;
                    $index++;
                    $total++;
                }
            }

            $result = $em->getConnection()->query($sql);
            $progress->setMessage($total);
            $progress->advance();

            fclose($handle);

        } else {
            $output->writeln('<fg=red>No file found !</fg=red>');
        }

        $output->writeln('');
        $output->writeln('Done');
    }
}