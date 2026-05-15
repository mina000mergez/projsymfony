<?php
namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResetPasswordController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/forgot-password', name: 'reset_password')]
    public function index(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        
        if ($this->getUser()) {
            return $this->redirectToRoute('app_patient_index'); // adapte selon ta route home
        }

        if ($request->request->has('email')) {
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $request->request->get('email')]);

            if ($user) {
               
                $resetPassword = new ResetPassword();
                $resetPassword->setUser($user)
                    ->setToken(uniqid())
                    ->setCreatedAt(new \DateTimeImmutable());

                $this->entityManager->persist($resetPassword);
                $this->entityManager->flush();

                
                $url = $this->generateUrl('update_password', [
                    'token' => $resetPassword->getToken()
                ]);

                $userEmail = $request->request->get('email');
                $content  = "Bonjour " . $userEmail . "<br>";
                $content .= "Pour réinitialiser votre mot de passe, cliquez ici :<br>";
                $content .= "<a href='http://127.0.0.1:8000" . $url . "'>Réinitialiser mon mot de passe</a>";

                $mail = new Mail();
                $mail->send($userEmail, null, 'Réinitialisation mot de passe', $content);

                $this->addFlash('notice', 'Un email vous a été envoyé !');
            } else {
                $this->addFlash('notice', 'Cet email n\'existe pas !');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    #[Route('/update-password/{token}', name: 'update_password')]
    public function reset($token, Request $request, UserPasswordHasherInterface $encoder)
    {
        $resetPassword = $this->entityManager->getRepository(ResetPassword::class)
            ->findOneBy(['token' => $token]);

        if (!$resetPassword) {
            return $this->redirectToRoute('reset_password');
        }

        // Vérifier expiration (30 minutes)
        if ($resetPassword->getCreatedAt()->modify('+ 30 minute') < new \DateTime()) {
            $this->addFlash('notice', 'Votre demande a expiré, veuillez recommencer.');
            return $this->redirectToRoute('reset_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('new_password')->getData();

            // Hasher et sauvegarder le nouveau mot de passe
            $resetPassword->getUser()->setPassword(
                $encoder->hashPassword($resetPassword->getUser(), $newPassword)
            );

            $this->entityManager->flush();

            $this->addFlash('notice', 'Votre mot de passe a été mis à jour !');
            return $this->redirectToRoute('login');
        }

        return $this->render('reset_password/reset.html.twig', [
            'form' => $form->createView()
        ]);
    }
}