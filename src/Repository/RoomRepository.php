<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }


    public function checkRoomAvailability($room_id, $date_start, $date_final)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();


        $qb = $em->createQueryBuilder();

        $nots = $em->createQuery("
        SELECT COUNT(b) FROM App:Reservation b
            WHERE NOT (b.date_out   < '$date_start'
               OR
               b.date_in > '$date_final')
            AND b.room = $room_id
               
        ")->getSingleScalarResult();

        try {

            return $nots;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getAvailableRooms($date_start, $date_final)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();


        $qb = $em->createQueryBuilder();

        $nots = $em->createQuery("
        SELECT IDENTITY(b.room) FROM App:Reservation b
            WHERE NOT (b.date_out   < '$date_start'
               OR
               b.date_in > '$date_final')
        ");

        $dql_query = $nots->getDQL();
        $qb->resetDQLParts();


        $query = $qb->select('r')
                    ->from('App:Room', 'r')
                    ->where($qb->expr()->notIn('r.id', $dql_query ))
                    ->getQuery()
                    ->getResult();

        try {

            return $query;
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    // /**
    //  * @return Room[] Returns an array of Room objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Room
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
