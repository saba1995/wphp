<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Format;
use AppBundle\Entity\Title;
use Doctrine\ORM\EntityRepository;
use Nines\UserBundle\Entity\User;

/**
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FormatRepository extends EntityRepository {

    /**
     * Do a name search for a typeahead query.
     *
     * @param string $q
     *
     * @return mixed
     */
    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere("e.name LIKE :q");
        $qb->orderBy('e.name');
        $qb->setParameter('q', "{$q}%");
        return $qb->getQuery()->execute();
    }

    /**
     * Count the titles in a format.
     *
     * @param Format $format
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countTitles(Format $format, User $user = null) {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('count(title.id)');
        $qb->andWhere('title.format = :format');
        if( ! $user) {
            $qb->andWhere('title.finalattempt = 1 OR title.finalcheck = 1');
        }
        $qb->setParameter('format', $format);
        $qb->from(Title::class, 'title');
        return $qb->getQuery()->getSingleScalarResult();
    }

}
