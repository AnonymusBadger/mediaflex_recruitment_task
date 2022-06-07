<?php

namespace App\Repository;

use App\Entity\Application;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use function PHPUnit\Framework\isInstanceOf;

/**
 * @extends ServiceEntityRepository<Application>
 *
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    public function add(Application $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Application $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function appHasUser(Application | string $app, User | string $user)
    {
        $appName = is_string($app) ? $app : $app->getName();
        $userEmail = is_string($user) ? $user : $user->getEmail();

        $dql = sprintf(
            "
            SELECT u.role
            FROM %s a
            JOIN a.users u
            WHERE a.name = '%s' AND u.email = '%s'
            ",
            Application::class,
            $appName,
            $userEmail,
        );

        $role = $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getOneOrNullResult();

        return [
            'hasAccess' => $role ? true : false,
            'role' => $role ? $role['role'] : null,
        ];
    }

    public function findByName(string $name)
    {
        $dql = sprintf(
            "
            SELECT a
            FROM %s a
            WHERE a.name = '%s'
            ",
            Application::class,
            $name

        );

        return $this
            ->getEntityManager()
            ->createQuery($dql)
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Application[] Returns an array of Application objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Application
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
