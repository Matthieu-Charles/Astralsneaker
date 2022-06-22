<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/createAccount', name: 'add_user')]
    public function addUser(UserRepository $useRepo, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        $user = new User();

        $form = $this->createForm(UserFormType::class, $user);

        $handle =  UserController::handleRequest($form, $request, $user, $useRepo, $passwordHasher);

        if ($handle) return $handle;

        return $this->render('user/index.html.twig', [
            'form_user' => $form->createView(),
            'title' => 'Créer un compte',
            'user' => ''
        ]);
    }

    #[Route('/profile/updateInformations', name: 'update_user')]
    public function updateUser(UserRepository $useRepo, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $this->getUser();

        $user = $useRepo->findOneBy(['email' => $user->getUserIdentifier()]);

        $form = $this->createForm(UserFormType::class, $user);

        $handle =  UserController::handleRequest($form, $request, $user, $useRepo, $passwordHasher);

        if ($handle) return $handle;

        return $this->render('user/index.html.twig', [
            'form_user' => $form->createView(),
            'title' => 'Modifier mes informations',
            'user' => $user
        ]);
    }

    private function handleRequest(FormInterface $form, Request $request, User $user, UserRepository $useRepo, UserPasswordHasherInterface $passwordHasher): RedirectResponse | false
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form['password']->getData();

            $user->setPassword($passwordHasher->hashPassword($user, $password));

            $useRepo->add($user, true);

            return $this->redirectToRoute('app_login');
        }
        return false;
    }


    #[Route('/user/contact', name: 'contact')]
    public function mymap(): Response
    {
        return $this->render('user/contact.html.twig');
    }
}
