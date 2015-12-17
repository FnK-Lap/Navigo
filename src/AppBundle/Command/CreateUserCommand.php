<?php

namespace AppBundle\Command;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class CreateUserCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('navigo:user:create')
            ->setDescription('Creates a new user')
            ->addArgument('email',null, InputArgument::REQUIRED, 'Specify email')
            ->addArgument('password',null, InputArgument::REQUIRED, 'Specify password')
            ->setHelp(
                <<<EOT
                    The <info>%command.name%</info>command creates a new user.

<info>php %command.full_name% email password</info>

EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userManager = $this->getContainer()->get('doctrine.orm.entity_manager');
        $encoderService =  $encoderService = $this->getContainer()->get('security.encoder_factory');

        $user = new User();
        $user->setFirstname('Admin')
            ->setLastname('Admin')
            ->setAddress('Admin')
            ->setZipcode('00000')
            ->setCreatedAt(new \Datetime())
            ->setUpdatedAt(new \Datetime())
        ;

        $user->setEmail($input->getArgument('email'));

        $user->setSalt(md5(uniqid()));

        $encoder = $encoderService->getEncoder($user);
        $user->setPassword($encoder->encodePassword($input->getArgument('password'), $user->getSalt()));

        $userManager->persist($user);
        $userManager->flush();
        $output->writeln(
            sprintf(
                'Added a new user with email <info>%s</info>',
                $user->getEmail()
            )
        );
    }
}