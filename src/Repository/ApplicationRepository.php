<?php

namespace App\Repository;

use App\Entity\Application;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
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

    public function getRole(Application | string $app, User | string $user)
    {
        $appName = is_string($app) ? $app : $app->getName();
        $userEmail = is_string($user) ? $user : $user->getEmail();

        $sql = sprintf(
            "
            SELECT u.roles
            FROM user u
            JOIN user_application ua ON u.id = ua.user_id
            JOIN application a ON ua.application_id = a.id
            WHERE  a.name = '%s' AND u.email = '%s';
            ",
            $appName,
            $userEmail
        );


        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('roles', 'roles');

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        $result = $query->getScalarResult();

        if (empty($result)) return null;

        return json_decode($result[0]['roles'])[0];
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
}
