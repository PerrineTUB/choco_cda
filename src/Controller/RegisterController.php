<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use App\Service\EmailService;
use App\Service\FileUploadService;
use App\Service\UserService;


class RegisterController extends AbstractController {

    public function __construct(
        private readonly UserPasswordHasherInterface $hash,
        private readonly EmailService $es,
        private readonly FileUploadService $fus,
        private readonly UserService $us
    ) {
    }

    #[Route('/register', name: 'app_register_create')]
    public function index(Request $request,): Response {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(["ROLE_USER"]);
            $user->setStatus(false);
            $user->setPassword($this->hash->hashPassword($user, $user->getPassword()));

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $imageFileName = $this->fus->upload($imageFile);
                $user->setImage($imageFileName);
                $body = $this->render('email/activation.html.twig', ['id' => $user->getId()]);
                $this->es->SendEmail($user->getEmail(), 'activation du compte', $body->getContent());
            }
            $this->us->create($user);
        }
        return $this->render('register/index.html.twig', [
            'formulaire' => $form->createView(),
        ]);
    }
}
