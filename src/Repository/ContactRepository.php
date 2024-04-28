<?php

namespace App\Repository;

use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Contact[]    createContact()
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    //    /**
    //     * @return Contact[] Returns an array of Contact objects
    //     */
    public function findByExampleField($value): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }

    public function findOneBySomeField($value): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByEmail(string $email): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByName(string $name): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.name= :name')
            ->setParameter('email', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneBySurname(string $surname): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.surname= :surname')
            ->setParameter('email', $surname)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByadresse(string $adresse): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.adresse= :adresse')
            ->setParameter('email', $adresse)
            ->getQuery()
            ->getOneOrNullResult();
    }


    public function findOneById(int $id): ?Contact
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.id= :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function createContact(string $email, string $nom, string $prenom, string $adresse): Contact
    {
        $contact = new Contact();
        $contact->setEmail($email);
        $contact->setNom($nom);
        $contact->setPrenom($prenom);
        $contact->setAdresse($adresse);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        return $contact;
    }

    public function updateContact(int $Id, string $email, string $name, string $prenom, string $adresse): ?Contact
    {
        // Récupérer le contact correspondant à l'ID
        $contact = $this->findOneById($Id);
        if (!$contact) {
            // Si le contact n'existe pas, retourner null ou lever une exception selon vos besoins
            return null;
        }

        // Appliquer les modifications
        $contact->setEmail($email);
        $contact->setNom($name);
        $contact->setPrenom($prenom);
        $contact->setAdresse($adresse);

        // Sauvegarder les modifications
        $entityManager = $this->getEntityManager();
        $entityManager->persist($contact);
        $entityManager->flush();

        return $contact;
    }

    public function deleteContactById(int $id): ?Contact
    {
        $contact = $this->findOneById($id);

        if ($contact) {
            $entityManager = $this->getEntityManager();
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $contact;
    }
}
