<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;

use function PHPUnit\Framework\containsOnly;

class ContactController extends AbstractController
{

    #[Route('/contact', name: 'contact')]
    public function getAllContact(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();

        return $this->render('contact/contact.html.twig', ['contacts' => $contacts,]);
    }

    #[Route('/addContact', name: 'addContact')]
    public function new(Request $request, SluggerInterface $slugger, ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        return $this->render('contact/addContact.html.twig', [
            'controller_name' => 'ContactController',
            'contacts' => $contacts,
            'form' => $form->createView()
        ]);
    }
}
