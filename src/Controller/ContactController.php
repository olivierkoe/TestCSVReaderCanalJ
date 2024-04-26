<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function getAllContact(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();

        return $this->render('contact/contact.html.twig', ['contacts' => $contacts,]);
    }

    #[Route('/addContact', name: 'addContact')]
    public function new(Request $request, ContactRepository $contactRepository, EntityManagerInterface $entityManager): Response
    {
        $contacts = $contactRepository->findAll();
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($contact->email !== null) {
            $emailContact = $contact->getEmail();
            $existingContact = $contactRepository->findOneByEmail($emailContact);

            // if ($form->isSubmitted() && $form->isValid()) {

            if ($existingContact !== null && $existingContact->getEmail() === $emailContact) {
                // var_dump('echouer');
                $this->addFlash('error', 'Le contact existe déjà !');
            } else {
                $contact->setEmail($emailContact)
                    ->setNom($contact->nom)
                    ->setPrenom($contact->prenom)
                    ->setAdresse($contact->adresse);
                $entityManager->persist($contact);
                $entityManager->flush();
                // $this->addFlash('success', 'Le contact a été ajouté avec succès !');
                return $this->redirectToRoute('contact');
            };
        }

        // }
        return $this->render('contact/addContact.html.twig', [
            'controller_name' => 'ContactController',
            'contacts' => $contacts,
            'AddContact' => $form,
            'form' => $form->createView()
        ]);
    }
}
