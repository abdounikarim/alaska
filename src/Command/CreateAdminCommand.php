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
        $user->setImage('jeanforteroche.png');
        $user->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus et enim risus. Etiam euismod, justo ac blandit dapibus, odio risus pharetra odio, eu gravida turpis nulla ut leo. Nullam dignissim, nibh at fringilla auctor, nibh turpis tempus neque, a egestas tellus augue eget urna. Nullam a dui velit. Nulla eget ultricies urna. Integer sagittis imperdiet tincidunt. Suspendisse tristique mollis enim sodales blandit. Mauris non turpis a nulla venenatis faucibus sed quis est. Proin lacinia finibus arcu at varius. Pellentesque blandit ullamcorper odio, vel tempus tellus imperdiet interdum. Sed dignissim sem a lectus facilisis, ac porttitor ipsum tempor. Integer vel justo eget tellus placerat viverra. Mauris vel orci in odio congue vestibulum. Sed mattis, felis feugiat pulvinar commodo, nibh urna elementum augue, nec feugiat quam massa ac elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;

Mauris aliquam massa non tortor lobortis iaculis. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Duis porttitor risus ligula, id semper ex tincidunt sed. Nam vitae quam vel mauris sodales interdum at ut lectus. Sed vehicula nisl eget turpis interdum, et laoreet purus vehicula. Aliquam ullamcorper suscipit nulla in venenatis. Duis vitae pharetra nisi. Ut pharetra velit vitae dui auctor, et ultricies diam hendrerit. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Curabitur malesuada, quam sit amet scelerisque vehicula, erat metus finibus urna, eu tempor augue odio eu neque. Pellentesque posuere, felis consectetur euismod facilisis, lacus risus congue ligula, eu gravida quam ligula in velit. Etiam sit amet sem laoreet, luctus velit nec, lobortis lectus. Aliquam feugiat tortor non ultricies semper. Suspendisse potenti.

Phasellus non lacus arcu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Fusce vitae aliquam ligula. Mauris convallis venenatis arcu, sed iaculis enim blandit non. Maecenas ac lectus urna. Cras et posuere tellus, a molestie lorem. Etiam et ultricies odio.

Duis eget felis nibh. Etiam pulvinar, turpis in gravida ullamcorper, nisl nulla tempus odio, sit amet mattis metus lorem sed lectus. Nullam quam lectus, lobortis in finibus vitae, commodo a purus. Sed vel augue tellus. Sed laoreet ac enim nec pharetra. Donec viverra auctor libero, scelerisque rutrum mi vehicula nec. Fusce mattis quam tortor, eu tincidunt purus efficitur a.

Suspendisse consequat erat risus, ac fermentum enim volutpat id. Aliquam in erat urna. Phasellus pharetra velit ut est placerat, vitae rutrum neque tempor. Duis justo turpis, auctor sit amet leo vitae, ultrices scelerisque eros. Sed pellentesque neque sit amet finibus dapibus. Ut finibus mauris blandit velit placerat luctus. Curabitur nec felis aliquam, dictum nulla id, malesuada sapien. Mauris consequat nisl a arcu pharetra, vel ultricies velit tincidunt. Vestibulum sed ante erat.

');
        $this->em->persist($user);
        $this->em->flush();
    }
}
