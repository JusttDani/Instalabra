<?php

namespace App\Controller;

use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResetPasswordController extends AbstractController
{
    #[Route('/reset-password', name: 'app_forgot_password_request')]
    public function request(Request $request, UsuarioRepository $usuarioRepository, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $usuarioRepository->findOneBy(['email' => $email]);

            if ($user) {
                $resetToken = bin2hex(random_bytes(32));
                $user->setResetToken($resetToken);
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
                $entityManager->flush();

                // Simulación de envío de correo electrónico
                // Muestra un enlace temporal en pantalla para probar la recuperación
                $resetUrl = $this->generateUrl('app_reset_password', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL);

                // Usamos un mensaje flash para que resalte y se vea bien para las pruebas
                $this->addFlash('success', 'Email enviado (Simulación). Haz click aquí para resetear: <a href="' . $resetUrl . '">Resetear Contraseña</a>');
            } else {
                // Mensaje explícito para facilitar el desarrollo
                $this->addFlash('error', 'No se encontró un usuario con ese email.');
            }
        }

        return $this->render('reset_password/request.html.twig');
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(
        string $token,
        Request $request,
        UsuarioRepository $usuarioRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = $usuarioRepository->findOneBy(['resetToken' => $token]);

        if (!$user || $user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('error', 'El enlace de reseteo es inválido o ha expirado.');
            return $this->redirectToRoute('app_forgot_password_request');
        }

        if ($request->isMethod('POST')) {
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password_confirm');

            if ($password === $passwordConfirm) {
                // Limpiamos el token de recuperación
                $user->setResetToken(null);
                $user->setResetTokenExpiresAt(null);

                // Y guardamos la contraseña nueva
                $hashedPassword = $passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $entityManager->flush();

                $this->addFlash('success', 'Tu contraseña ha sido actualizada exitosamente.');
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('error', 'Las contraseñas no coinciden.');
            }
        }

        return $this->render('reset_password/reset.html.twig', [
            'token' => $token
        ]);
    }
}
