<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'create:admin';
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userPasswordEncoder;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    private $error;

    private $errorMessages;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ValidatorInterface $validator, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $em)
    {
        parent::__construct();
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->validator = $validator;
        $this->em = $em;
    }

    protected function configure()
    {
        $this
            ->setDescription('Create a new administrator with the ROLE_ADMIN')
            ->setHelp('This command allows you to create an administrator')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $email = $io->ask('Veuillez saisir un email');

        $io->success('Email : '.$email);
        $pseudo = $io->ask('Veuillez saisir un pseudo');
        $io->success('Pseudo : '.$pseudo);
        $pass = $io->askHidden('Veuillez saisir un mot de passe');
        $pass2 = $io->askHidden('Veuillez confirmer votre mot de passe');
        if($pass === $pass2) {
            $io->success('Mot de passe');
            //Vérification des champs
            $io->note('Vérification des champs en cours');
            $message = $this->checkUser($email, $pseudo, $pass);
            if($message === null) {
                $confirm = $io->confirm('Confirmez-vous la création de l\'administrateur avec l\'email : '.$email.', le pseudo : '.$pseudo.' et le mot de passe associé ?');
                if($confirm) {
                    $this->createAdmin($email, $pseudo, $pass);
                    return $io->success('Le nouvel administrateur a bien été créé');
                }
                else {
                    return $io->error('Confirmation annulée');
                }
            }
            else {
                foreach ($this->errorMessages as $errorMessage){
                    $io->error($errorMessage);
                }
                return $io->error('Veuillez recommencer');
            }
        } else {
            return $io->error('Les mots de passe ne correspondent pas');
        }
    }

    private function checkUser($email, $pseudo, $pass)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($pseudo);
        $user->setPlainPassword($pass);
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->errorMessages[] = $error->getMessage();
            }
            $errorsString = (string) $errors;
            $this->error = true;
            return $errorsString;
        }
        $this->error = false;
    }

    private function createAdmin($email, $pseudo, $pass)
    {
        $user = new User();
        $password = $this->userPasswordEncoder->encodePassword($user, $pass);
        $user->setEmail($email);
        $user->setUsername($pseudo);
        $user->setPassword($password);
        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);
        $this->em->flush();
    }
}
