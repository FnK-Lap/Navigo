<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;



class ImportUsersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navigo:users:import')
            ->setDescription('Import users file')
            ->addArgument('file', InputArgument::REQUIRED, 'File with users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Import <fg=cyan>users</fg=cyan>');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        gc_enable();


        $file = $input->getArgument('file');

        $handle = @fopen($file, "r");
        if ($handle) {
            $output->writeln('<fg=green>File found !</fg=green>');

            $index = 0;
            $total = 0;
            $date = new \DateTime;
            
            $progress = new ProgressBar($output, 111111111);
            $progress->setFormat('Insert <fg=green>%message%</fg=green> users | time: %elapsed:6s% | memory: %memory:6s%');
            $progress->setMessage($total);
            $progress->start();

            while (($buffer = fgets($handle, 4096)) !== false) {
                $firstname = strstr(rtrim($buffer), ' ');
                $lastname = strstr(rtrim($buffer), ' ', true);

                if ($index == 0) {
                    $sql = 'INSERT INTO user (firstname, lastname, created_at, updated_at, email) VALUES ("'.$firstname.'", "'.$lastname.'", "'.$date->format('Y-m-d H:I:s').'", "'.$date->format('Y-m-d H:I:s').'", "'.$total.'@gmail.com")'.PHP_EOL;
                    $index++;
                    $total++;
                } elseif ($index == 9999) {
                    $sql .= ', ("'.$firstname.'", "'.$lastname.'", "'.$date->format('Y-m-d H:I:s').'", "'.$date->format('Y-m-d H:I:s').'", "'.$total.'@gmail.com")'.PHP_EOL;
                    $index++;
                    $total++;
                    gc_collect_cycles();
                    $result = $em->getConnection()->query($sql);
                    $progress->setMessage($total);
                    $progress->advance();
                    $index = 0;
                    $sql = '';
                } else {
                    $sql .= ', ("'.$firstname.'", "'.$lastname.'", "'.$date->format('Y-m-d H:I:s').'", "'.$date->format('Y-m-d H:I:s').'", "'.$total.'@gmail.com")'.PHP_EOL;
                    $index++;
                    $total++;
                }

                if ($total > 989900) {
                    $output->writeln($total);
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