<?php

namespace App\Controller;

use DateTime;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use App\Repository\ResetPasswordRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     */
    public function index(Request $request, UserRepository $repo, EntityManagerInterface $manager): Response
    {
        $email = $request->get('email');

        if ($email) {
            $user = $repo->findOneByEmail($email);

            if (!$user) {
                $this->addFlash('danger', 'Aucun compte trouvé avec l\'addresse mail ' . $email . '.');
                return $this->redirectToRoute('reset_password');
            }

            $reset_password = new ResetPassword();

            $reset_password->setUser($user)
                ->setToken(uniqid())
                ->setCreatedAt(new DateTime());

            $manager->persist($reset_password);
            $manager->flush();


            // Send verification email
            $content_mail = 'Un lien de réinitialisation de mot de passe à été demandé pour votre compte.
            <br>
            Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien suivant:
            <br>
            <a href="https://'
                . $_SERVER['HTTP_HOST']
                . '/modifier-mot-de-passe/'
                . $reset_password->getToken()
                . '">
                Réinitialiser mon mot de passe
            </a>
            <br>
            <br>
            Si vous n\'êtes pas à l\'origine de cette requête, vous pouvez ignorer cet email.';

            //$mail->sendMail($user->getEmail(), 'Réinitialisation mot de passe', $content_mail);

            $this->addFlash('success', 'Un lien de réinitialisation vous a été envoyé par email!');
        }


        return $this->render('reset_password/index.html.twig', []);
    }



    /**
     * @Route("/modifier-mot-de-passe/{token}", name="update_password")
     */
    public function update(Request $request, ResetPasswordRepository $repo, EntityManagerInterface $manager, $token, UserPasswordHasherInterface $passwordHasher): Response
    {
        $reset_password = $repo->findOneByToken($token);

        if (!$reset_password) {
            $this->addFlash('danger', 'Lien de réinitialisation incorrect.');
            return $this->redirectToRoute('app_login');
        }

        $now = new DateTime();

        if ($now->diff($reset_password->getCreatedAt())->h > 3) {
            $this->addFlash('danger', 'Lien de réinitialisation expiré.');
            return $this->redirectToRoute('reset_password');
        }

        $user = $reset_password->getUser();
        $form = $this->createForm(ResetPasswordType::class, $reset_password->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword(
                $user,
                $user->getNewPassword()
            ));

            $manager->flush();

            $this->addFlash('success', 'Le mot de passe a été modifié avec succès!');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('reset_password/update.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
