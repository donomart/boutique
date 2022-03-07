<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Services\ServiceMail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, EntityManagerInterface $manager, ServiceMail $mail): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            ));

            $user->setActive(false);

            $manager->persist($user);
            $manager->flush();

            $token = sha1($user->getEmail());
            $content_mail = 'Merci de votre inscription, veuillez cliquer sur le lien suivant pour activer votre compte:
            <br>
            <a href="https://'
                . $_SERVER['HTTP_HOST']
                . '/inscription/'
                . $user->getEmail()
                . '|'
                . $token
                . '"></a>
            ';
            //$mail->sendMail($user->getEmail(), $user->getFullName(), $content_mail);

            $this->addFlash('success', 'Le compte à été créé et un email de vérification vous a été envoyé!');

            return $this->redirectToRoute('home');
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/inscription/{email}|{token}", name="register_activation")
     */
    public function activate_account(User $user, EntityManagerInterface $manager, $email, $token): Response
    {
        if (sha1($user->getEmail()) !== $token || $user->getActive()) {
            $this->addFlash('danger', 'Le lien d\'activation est incorrect.');
            return $this->redirectToRoute('home');
        }

        $user->setActive(true);
        $manager->flush();

        $this->addFlash('success', 'Compte activé avec succès!');
        return $this->redirectToRoute('app_login');
    }
}
