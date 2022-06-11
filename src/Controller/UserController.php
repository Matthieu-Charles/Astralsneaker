<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(UserRepository $useRepo, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();

        $form = $this->createForm(UserFormType::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form['password']->getData();

            $user->setPassword($passwordHasher->hashPassword($user, $password));

             $useRepo->add($user, true);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/index.html.twig', [
            'form_user' => $form->createView()
        ]);
    }
}
