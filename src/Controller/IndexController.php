<?php

namespace App\Controller;

use App\Entity\SportsEvent;
use App\Entity\User;
use App\Form\SportsEventType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class IndexController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('index.html.twig', [
            'hello' => 'World!'
        ]);
    }

    #[Route(path: '/form', name: 'form', methods: ['GET', 'POST'])]
    public function form(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Utwórz obiekt SportsEvent
        $sportsEvent = new SportsEvent();

        // Utwórz formularz na podstawie klasy SportsEventType
        $form = $this->createForm(SportsEventType::class, $sportsEvent);

        // Obsłuż żądanie formularza
        $form->handleRequest($request);

        // Jeśli formularz został wysłany i jest poprawny
        if ($form->isSubmitted() && $form->isValid()) {
            // Zapisz do bazy danych lub wykonaj inne operacje

            // Przykład: Zapisz do bazy danych
            $entityManager->persist($sportsEvent);
            $entityManager->flush();

            // Przekieruj użytkownika na inną stronę po zapisaniu formularza
            return $this->redirectToRoute('index');
        }

        // Renderuj widok Twig z przekazaniem formularza jako zmiennej
        return $this->render('form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/register', name: 'register', methods: ['GET'])]
    public function register(
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
    ): Response
    {
        $user = new User();
        $plaintextPassword = 'haslo';

        $user
            ->setEmail('to nie jest email!!!11');

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        $user->setPassword($hashedPassword);

        $errors = $validator->validate($user);

        throw new \Exception("Błąd. Skąd on się wziął?");


        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }



            return new Response(implode("<br>", $errorMessages), Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response("Registered!");

    }
}
