<?php

namespace Runalyze\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Runalyze\Bundle\CoreBundle\Entity\Route;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Runalyze\Calculation\Route\GeohashLine;


/**
 * refactoring geohashes in route table
 */
class Version20161224173160 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface|null */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function isTransactional()
    {
        return false;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->getConnection()->getConfiguration()->setSQLLogger(null);

        $prefix = $this->container->getParameter('database_prefix');
        /** @var EntityManager $em */
        $repo = $em->getRepository('CoreBundle:Route');

        $countLockedRoutes = $em->createQueryBuilder()
            ->select('count(route.id)')
            ->where('route.lock = 1')
            ->from('CoreBundle:Route','route');
        $numberLockedRoutes= $countLockedRoutes->getQuery()->getSingleScalarResult();
        while($numberLockedRoutes > 0) {
            $lockedRoutes = $repo->createQueryBuilder('r')
                ->select('r')
                ->where('r.lock = 1')
                ->setMaxResults(100)
                ->getQuery();

            $batchSize = 100;
            $i = 0;
            $iterableResult= $lockedRoutes->iterate();
            foreach ($iterableResult as $row) {
                $route = $row[0];
                $route->setLock(0);
                $route->setGeohashesWithoutMinMaxRecalculation( implode('|', GeohashLine::shorten( explode('|', $route->getGeohashes()) )) );
                $em->persist($route);
                if (($i % $batchSize) === 0) {
                    $em->flush();
                    $em->clear();
                    gc_collect_cycles();
                }
                ++$i;
            }
            $em->flush();
            $numberLockedRoutes = $numberLockedRoutes-100;
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {

    }
}
