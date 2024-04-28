<?php

namespace App\Controller;

use App\Command\CreateContactsFromCsvFileCommand;
use App\Entity\Contact;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ContactsService;

class ContactController extends AbstractController
{
    private $contact;

    public function __construct(ContactsService $contact)
    {
        $this->contact = $contact;
    }


    #[Route('/contact', name: 'contact')]
    public function getAllContact(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();


        return $this->render('contact/contact.html.twig', ['contacts' => $contacts,]);
    }


    #[Route('/updateContact', name: 'updateContact')]
    public function updateContact(Request $request, ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();
        $newContact = new Contact();
        $UpdateContact = $this->createForm(ContactType::class, $newContact);
        $UpdateContact->handleRequest($request);
        return $this->render('contact/updateContact.html.twig', [
            'controller_name' => 'ContactController',
            'contacts' => $contacts,
            // 'UpdateContact' => $UpdateContact,
        ]);
    }

    #[Route('/updateContactValidation', name: 'updateContactValidation')]
    public function updateContactValidation(Request $request, ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();
        // Récupérer les données POST
        $formData = $request->request->all();
        // Vérifier si les données sont présentes
        if ($formData) {
            // Récupérer les valeurs des champs du formulaire
            $contactId = intval($formData['contactId']);
            $nom = $formData['nom'];
            $prenom = $formData['prenom'];
            $email = $formData['email'];
            $adresse = $formData['adresse'];
            // Appeler la méthode updateContact du repository pour mettre à jour le contact
            $updatedContact = $contactRepository->updateContact($contactId, $email, $nom, $prenom, $adresse);
            // Vérifier si le contact a été mis à jour avec succès
            if ($updatedContact) {
                // Répondre avec un message de succès
                return $this->render('contact/contact.html.twig', [
                    'controller_name' => 'ContactController',
                    'contacts' => $contacts,

                ]);
            } else {
                // Répondre avec un message d'erreur si le contact n'a pas été trouvé
                return new JsonResponse(['success' => false, 'message' => 'Contact non trouvé'], 404);
            }
        } else {
            // Répondre avec un message d'erreur si les données ne sont pas présentes
            return new JsonResponse(['success' => false, 'message' => 'Données du formulaire non trouvées'], 400);
        }
    }

    #[Route('/addContact', name: 'addContact')]
    public function new(Request $request, ContactRepository $contactRepository, EntityManagerInterface $entityManager, CreateContactsFromCsvFileCommand $createContactsFromCsvFileCommand): Response
    {
        $contacts = $contactRepository->findAll();
        $newContact = new Contact();
        $form = $this->createForm(ContactType::class, $newContact);
        $form->handleRequest($request);
        if ($newContact->email !== null) {

            // On verifie si le mail est déja en bdd
            $emailContact = $newContact->getEmail();
            $existingContact = $contactRepository->findOneByEmail($emailContact);
            //Si le contact n'existe pas on le rentre en Bdd
            if ($existingContact !== null && $existingContact->getEmail() === $emailContact) {
                $this->addFlash('error', 'Le contact existe déjà !');
            } else {
                $newContact = $contactRepository->createContact($newContact->email, $newContact->nom, $newContact->prenom, $newContact->adresse);
                $this->addFlash('success', 'Le contact a été ajouté avec succès !');
                return $this->redirectToRoute('contact');
            };
        }

        return $this->render('contact/addContact.html.twig', [
            'controller_name' => 'ContactController',
            'contacts' => $contacts,
            'AddContact' => $form,
            'form' => $form->createView()
        ]);
    }

    #[Route('/deleteContact/{id}', name: 'delete_contact', methods: ['DELETE'])]
    public function deleteContact(int $id, ContactRepository $contactRepository): Response
    {
        // Supprimer le contact par son ID
        $deletedContact = $contactRepository->deleteContactById($id);

        // Vérifier si le contact a été supprimé avec succès
        if ($deletedContact) {
            // Répondre avec un message de succès
            return new JsonResponse(['success' => true, 'message' => 'Le contact a été supprimé avec succès']);
        } else {
            // Répondre avec un message d'erreur si le contact n'a pas été trouvé
            return new JsonResponse(['success' => false, 'message' => 'Contact non trouvé'], 404);
        }
    }

    public function telechargerCSV(ContactRepository $contactRepository): Response
    {
        // Appel de la méthode du service pour obtenir les données du fichier CSV
        $fichierCSV = $this->contact->getDataFromFile();

        // Vérification du format du fichier CSV
        if (!$fichierCSV) {
            // Gérer l'erreur si le fichier CSV est vide ou incorrectement formaté
            return new Response('Le fichier CSV est vide ou incorrectement formaté', Response::HTTP_BAD_REQUEST);
        }

        // Récupérer les contacts
        $contacts = $contactRepository->findAll();

        // Renvoyer la vue contactCSV.html.twig avec les données
        return $this->render('contact/contactCSV.html.twig', [
            'contacts' => $contacts,
            'fichierCSV' => $fichierCSV,
        ]);
    }
}
