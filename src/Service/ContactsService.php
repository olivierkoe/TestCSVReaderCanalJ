<?php

namespace App\Service;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class ContactsService extends Command
{
    private EntityManagerInterface $entityManager;


    private string $dataDirectory;
    private $projectDir;

    private SymfonyStyle $io;

    private ContactRepository $contactRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        string $projectDir,
        ContactRepository $contactRepository
    ) {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->projectDir = $projectDir;
        $this->entityManager = $entityManager;
        $this->contactRepository = $contactRepository;
    }

    protected static $defaultName = 'app:create-contacts-from-file';

    protected function configure(): void
    {
        $this->setDescription('Importer des donner CSV');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createContacts();

        return Command::SUCCESS;
    }

    // public function getDataFromFile(): array
    // {
    //     $file = $this->dataDirectory . 'contact.csv';

    //     $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

    //     $normalizers = [new ObjectNormalizer()];

    //     $encoders = [
    //         new CsvEncoder(),
    //         new XmlEncoder(),
    //         new YamlEncoder()
    //     ];

    //     $serializer = new Serializer($normalizers, $encoders);

    //     /** @var string  $fileString */
    //     $fileString = file_get_contents($file);

    //     $data = $serializer->decode($fileString, $fileExtension);

    //     if (array_key_exists('nom;prenom;email;adresse', $data)) {
    //         return $data['nom;prenom;email;adresse'];
    //     }
    //     return $data;
    // }
    public function getDataFromFile(): array
    {
        $file = $this->dataDirectory . 'contact.csv';

        // Lecture du contenu du fichier CSV
        $fileContents = file_get_contents($file);

        // Découpage du contenu en lignes
        $lines = explode("\n", $fileContents);

        // Initialisation du tableau pour stocker les données formatées
        $formattedData = [];

        // Parcourir chaque ligne à partir de la deuxième ligne
        foreach ($lines as $key => $line) {
            // Ignorer la première ligne
            if ($key === 0) {
                continue;
            }
            // Séparer la ligne en éléments séparés par un point-virgule
            $elements = explode(';', $line);

            // Vérifier si la ligne contient quatre éléments
            if (count($elements) !== 4) {
                throw new \Exception('Chaque ligne doit contenir quatre éléments séparés par un point-virgule.');
            }

            // Créer un tableau associatif avec les en-têtes comme clés
            $rowData = [
                'nom' => $elements[0],
                'prenom' => $elements[1],
                'email' => $elements[2],
                'adresse' => $elements[3],
            ];

            // Ajouter les données formatées au tableau final
            $formattedData[] = $rowData;
        }
        // var_dump($formattedData);
        // exit;
        return $formattedData;
    }

    public function createContacts(): void
    {
        // $this->io->section('CREATION DES UTILISATEURS A PARTIR DU FICHIER');

        $contactsCreated = 0;
        $contactsData = [];
        foreach ($this->getDataFromFile() as $key => $row) {
            // Données CSV
            $csvData = $row;

            // Initialisation du tableau associatif
            $csvArray = [];

            foreach ($csvData as $header => $line) {
                // Séparer les données en utilisant le point-virgule comme délimiteur
                $fields = explode(';', $line);

                // Séparer les champs et les clés
                $keys = explode(';', $header);

                $EmailKey = $keys[2];
                $contactPrenom = $fields[0];;
                $contactNom = $fields[1];
                $contactEmail = $fields[2];
                $contactAdresse = $fields[3];

                $existingContact = $this->contactRepository->findOneByEmail($contactEmail);

                if ($EmailKey === "email" && empty($existingContact)) {

                    if (!$csvArray) {
                        $contact = new Contact();
                        $contact->setEmail($contactEmail)
                            ->setNom($contactNom)
                            ->setPrenom($contactPrenom)
                            ->setAdresse($contactAdresse);

                        $this->entityManager->persist($contact);
                        $contactsCreated++;
                    }


                    $this->entityManager->flush();
                }
            }
        }
        if ($contactsCreated > 1) {
            $string = "{$contactsCreated} Utilisateurs ajoutées à la Base De Données.";
        } elseif ($contactsCreated === 1) {
            $string = '1 Contact à été ajoutéer en Base De Données.';
        } else {
            $string = 'Aucun contact ajoutées.';
        }
    }
}
