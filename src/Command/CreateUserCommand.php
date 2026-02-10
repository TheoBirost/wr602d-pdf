<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Plan;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of the user.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = $input->getArgument('email');
        $password = $input->getArgument('password');

        // Find or create a default plan
        $planRepository = $this->entityManager->getRepository(Plan::class);
        $defaultPlan = $planRepository->findOneBy(['name' => 'Default Plan']);

        if (!$defaultPlan) {
            $output->writeln('Default plan not found, creating one...');
            $defaultPlan = new Plan();
            $defaultPlan->setName('Default Plan');
            $defaultPlan->setDescription('Default plan for new users.');
            $defaultPlan->setLimitGeneration(10);
            $defaultPlan->setPrice(0);
            $defaultPlan->setActive(true);
            $defaultPlan->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($defaultPlan);
        }

        $user = new User();
        $user->setEmail($email);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setLastname('test');
        $user->setFirstname('test');
        $user->setPlan($defaultPlan); // Assign the plan

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('User created successfully!');

        return Command::SUCCESS;
    }
}
