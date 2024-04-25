<?php

namespace App\Service;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\ConsoleLeague\Csv;
use League\Csv\Reader;

class ImportContactsService
{
    public function __construct(
        private ContactRepository $contactRepository,
        private EntityManagerInterface $em
    ) {
    }

    public function ImportContacts(SymfonyStyle $io): void
    {
        $io->title('Importation des contacts SERVICES');

        $contacts = $this->readCsvFile();

        // $io->progressStart(count($contacts));

        foreach ($contacts as $key => $arraycontact) {
            var_dump($contacts);
            // var_dump('dans premier foreach');
            // dd($arraycontact);

            // $io->progressAdvance();

            foreach ($arraycontact as $value) {
                var_dump('dans second foreach');
                var_dump($value);
                // dd($value);
                exit;
            }
            // $contact = $this->createOrUpdateContact($arraycontact);
            // $this->em->persist($contact);

            // $io->writeln($contact['nom']);
        }
        // $this->em->flush();
        // $io->progressFinish();
        // $io->success('Importation terminée');
    }

    private function readCsvFile(): Reader
    {
        $csv = Reader::createFromPath('./public/datas/contact.csv', 'r');

        $csv->setHeaderOffset(0);

        return $csv;
    }

    // private function createOrUpdateContact(array $arraycontact): Contact
    // {
    //     $contact = $this->contactRepository->findOneBy(['email' => $arraycontact['email']]);
    //     if (!$contact) {
    //         $contact = new Contact();
    //     }
    //     $contact->setEmail($arraycontact['email'])
    //         ->setAdresse($arraycontact['adresse'])
    //         ->setNom($arraycontact['nom'])
    //         ->setPrenom($arraycontact['prenom']);

    //     return $contact;
    // }
}
