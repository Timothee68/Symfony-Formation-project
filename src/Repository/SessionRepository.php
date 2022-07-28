<?php

namespace App\Repository;

use App\Entity\Session;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\BinaryOp\Concat;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Session::class);
    }

    public function add(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    // fonction DQL pour récupérer les dates de fin de session entérieur a la date actuel
    public function findPastSession()
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('s')
                    ->andWhere('s.dateEnd < :val')
                    ->setParameter('val',$now)
                    ->orderBy('s.dateStart', 'ASC')
                    ->getQuery()
                    ->getResult()
                    ;
    }
    // fonction DQL pour récupérer les dates de debut de session supérieur a la date actuel
    public function findFuturSession()
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('s')
                    ->andWhere('s.dateStart > :val')
                    ->setParameter('val',$now)
                    ->orderBy('s.dateStart', 'ASC')
                    ->getQuery()
                    ->getResult()
                    ;
    }
    // fonction DQL pour récupérer les dates de debut de session inférieur et dont la date de fin est supérieur a la date actuel
    public function findProgressSession()
    {
        $now = new \DateTime();
        return $this->createQueryBuilder('s')
                    ->andWhere('s.dateStart < :val AND s.dateEnd > :val')
                    ->setParameter('val',$now)
                    ->orderBy('s.dateStart', 'ASC')
                    ->getQuery()
                    ->getResult()
                    ;
    }

    // fonction pour retrouver les stagiaires qui ne sont pas dans la session en cour 
    public function findInternNotInSession(int $id)
    {
        // on ce connecte a l'entité 
        $em = $this->getEntityManager();
        //on crée une requête que $sub prend pour valeur 
        $sub = $em->createQueryBuilder();
        // $qb prend la meme valeur que $sub pour faire une sous requête préparer
        $qb = $sub; 
        // on fait une première requête qui dit select all from intern left join intern_session where id = id envoyer
        // on récupère donc tout les stagiaire qui sont dans la session 
            $qb->select('i')
                ->from('App\Entity\Intern', 'i')
                // ici on passe par sessions délcarer dans l'entité Intern pour faire la liaison via la relation ManyToMany 
                ->leftJoin('i.sessions', 's')
                ->where('s.id = :id');
                
            $sub = $em->createQueryBuilder();
            // la sous requete reprend la selection il faut changer l'alias sinon il y a un soucis de compréhension 
            //on selectionne tout les stagiaires 
            // select all from intern 
            $sub->select('e')
                ->from('App\Entity\Intern', 'e')     
                // where ( on récupère tout les stagiaires ) not in ( 1er requête qui contient ceux dans la session )         
                ->where($sub->expr()->notIn('e.id', $qb->getDQL()))
                // on prend en paramêtre de comparaison les id 
                ->setParameter('id',$id)
                // on ordonne par nom 
                ->orderBy('e.name');

        $query = $sub->getQuery() ;
        return $query->getResult();       
    }

//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
