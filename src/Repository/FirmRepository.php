<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\Firm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * FirmRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class FirmRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Firm::class);
    }

    /**
     * Do a name search for a typeahead widget.
     *
     * @param string $q
     *
     * @return mixed
     */
    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.name LIKE :q');
        $qb->orWhere('e.id = :id');
        $qb->orderBy('e.name');
        $qb->setParameter('id', $q);
        $qb->setParameter('q', "%{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * Build a full text, complex search query and return it. Takes all the
     * parameters from the firm search and does smart things with them.
     *
     * @param array $data The search form's data from $form->getData().
     *
     * @return Query
     */
    public function buildSearchQuery($data) {
        $qb = $this->createQueryBuilder('e');
        $qb->orderBy('e.name');
        $qb->addOrderBy('e.startDate');
        if (isset($data['name']) && $data['name']) {
            $qb->add('where', 'MATCH (e.name) AGAINST(:name BOOLEAN) > 0');
            $qb->setParameter('name', $data['name']);
        }
        if (isset($data['order']) && $data['order']) {
            switch ($data['order']) {
                case 'name_asc':
                    $qb->orderBy('e.name', 'ASC');
                    $qb->addOrderBy('e.startDate');

                    break;
                case 'name_desc':
                    $qb->orderBy('e.name', 'DESC');
                    $qb->addOrderBy('e.startDate');

                    break;
                case 'city_asc':
                    $qb->innerJoin('e.city', 'c');
                    $qb->orderBy('c.name', 'ASC');
                    $qb->addOrderBy('e.name', 'ASC');
                    $qb->addOrderBy('e.startDate');

                    break;
                case 'city_desc':
                    $qb->innerJoin('e.city', 'c');
                    $qb->orderBy('c.name', 'DESC');
                    $qb->addOrderBy('e.name', 'ASC');
                    $qb->addOrderBy('e.startDate');

                    break;
                case 'start_asc':
                    $qb->orderBy('e.startDate');
                    $qb->addOrderBy('e.name', 'ASC');

                    break;
                case 'start_desc':
                    $qb->orderBy('e.startDate', 'DESC');
                    $qb->addOrderBy('e.name', 'ASC');

                    break;
                case 'end_asc':
                    $qb->orderBy('e.endDate', 'ASC');
                    $qb->addOrderBy('e.name', 'ASC');

                    break;
                case 'end_desc':
                    $qb->orderBy('e.endDate', 'DESC');
                    $qb->addOrderBy('e.name', 'ASC');

                    break;
            }
        }
        if (isset($data['id']) && $data['id']) {
            $qb->andWhere('e.id = :id');
            $qb->setParameter('id', $data['id']);
        }
        if (isset($data['gender']) && $data['gender']) {
            $genders = [];
            if (in_array('M', $data['gender'], true)) {
                $genders[] = 'M';
            }
            if (in_array('F', $data['gender'], true)) {
                $genders[] = 'F';
            }
            if (in_array('U', $data['gender'], true)) {
                $genders[] = 'U';
            }
            $qb->andWhere('e.gender in (:genders)');
            $qb->setParameter('genders', $genders);
        }
        if (isset($data['address']) && $data['address']) {
            $qb->andWhere('MATCH (e.streetAddress) AGAINST(:address BOOLEAN) > 0');
            $qb->setParameter('address', $data['address']);
        }
        if (isset($data['city']) && $data['city']) {
            if ( ! $data['order'] || ('city_asc' !== $data['order'] && 'city_desc' !== $data['order'])) {
                $qb->innerJoin('e.city', 'c');
            }
            $qb->andWhere('MATCH(c.alternatenames, c.name) AGAINST(:cname BOOLEAN) > 0');
            $qb->setParameter('cname', $data['city']);
        }
        if (isset($data['start']) && $data['start']) {
            $m = [];
            if (preg_match('/^\s*[0-9]{4}\s*$/', $data['start'])) {
                $qb->andWhere('YEAR(e.startDate) = :yearb');
                $qb->setParameter('yearb', $data['start']);
            } elseif (preg_match('/^\s*(\*|[0-9]{4})\s*-\s*(\*|[0-9]{4})\s*$/', $data['start'], $m)) {
                $from = ('*' === $m[1] ? -1 : $m[1]);
                $to = ('*' === $m[2] ? 9999 : $m[2]);
                $qb->andWhere(':fromb <= YEAR(e.startDate) AND YEAR(e.startDate) <= :tob');
                $qb->setParameter('fromb', $from);
                $qb->setParameter('tob', $to);
            }
        }

        if (isset($data['end']) && $data['end']) {
            $m = [];
            if (preg_match('/^\s*[0-9]{4}\s*$/', $data['end'])) {
                $qb->andWhere('YEAR(e.endDate) = :yeare');
                $qb->setParameter('yeare', $data['end']);
            } elseif (preg_match('/^\s*(\*|[0-9]{4})\s*-\s*(\*|[0-9]{4})\s*$/', $data['end'], $m)) {
                $from = ('*' === $m[1] ? -1 : $m[1]);
                $to = ('*' === $m[2] ? 9999 : $m[2]);
                $qb->andWhere(':frome <= YEAR(e.endDate) AND YEAR(e.endDate) <= :toe');
                $qb->setParameter('frome', $from);
                $qb->setParameter('toe', $to);
            }
        }

        return $qb->getQuery();
    }

    /**
     * Find and return $limit firms, selected at random.
     *
     * @param int $limit
     *
     * @return Collection
     */
    public function random($limit) {
        $qb = $this->createQueryBuilder('e');
        $qb->orderBy('RAND()');
        $qb->setMaxResults($limit);

        return $qb->getQuery()->execute();
    }
}
