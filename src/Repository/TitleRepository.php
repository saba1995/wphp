<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Repository;

use App\Entity\TitleSource;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Title Repository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TitleRepository extends ServiceEntityRepository {
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Title::class);
    }

    /**
     * Do a name search for a typeahead query.
     *
     * @param string $q
     *
     * @return mixed
     */
    public function typeaheadQuery($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.title LIKE :q');
        $qb->orWhere('e.id = :id');
        $qb->orderBy('e.title');
        $qb->setParameter('id', $q);
        $qb->setParameter('q', "%{$q}%");

        return $qb->getQuery()->execute();
    }

    /**
     * Build a complex search query from form data.
     *
     * @param array $data
     * @param null|mixed $user
     *
     * @return Query
     */
    public function buildSearchQuery($data = [], $user = null) {
        $qb = $this->createQueryBuilder('e');
        $qb->orderBy('e.pubdate');
        $qb->addOrderBy('e.title');
        if (isset($data['title']) && $data['title']) {
            $qb->andWhere('MATCH (e.title) AGAINST (:title BOOLEAN) > 0');
            $qb->setParameter('title', $data['title']);
        }
        if (isset($data['order']) && $data['order']) {
            switch ($data['order']) {
                case 'title_asc':
                    $qb->orderBy('e.title', 'ASC');
                    $qb->addOrderBy('e.pubdate');

                    break;
                case 'title_desc':
                    $qb->orderBy('e.title', 'DESC');
                    $qb->addOrderBy('e.pubdate');

                    break;
                case 'pubdate_asc':
                    $qb->orderBy('e.pubdate', 'ASC');
                    $qb->addOrderBy('e.title');

                    break;
                case 'pubdate_desc':
                    $qb->orderBy('e.pubdate', 'DESC');
                    $qb->addOrderBy('e.title');

                    break;
                case 'first_pubdate_asc':
                    $qb->orderBy('e.dateOfFirstPublication', 'ASC');
                    $qb->addOrderBy('e.title');

                    break;
                case 'first_pubdate_desc':
                    $qb->orderBy('e.dateOfFirstPublication', 'DESC');
                    $qb->addOrderBy('e.title');

                    break;
                case 'edition_asc':
                    $qb->orderBy('e.editionNumber', 'ASC');
                    $qb->orderBy('e.edition', 'ASC');
                    $qb->addOrderBy('e.title');

                    break;
                case 'edition_desc':
                    $qb->orderBy('e.editionNumber', 'DESC');
                    $qb->orderBy('e.edition', 'DESC');
                    $qb->addOrderBy('e.title');

                    break;
            }
        }
        if (isset($data['id']) && $data['id']) {
            $qb->andWhere('e.id = :id');
            $qb->setParameter('id', $data['id']);
        }
        if (isset($data['editionNumber']) && $data['editionNumber']) {
            $qb->andWhere('e.editionNumber = :editionNumber');
            $qb->setParameter('editionNumber', $data['editionNumber']);
        }
        if (isset($data['volumes']) && $data['volumes']) {
            $qb->andWhere('e.volumes = :volumes');
            $qb->setParameter('volumes', $data['volumes']);
        }
        if (isset($data['sizeW']) && $data['sizeW']) {
            $qb->andWhere('e.sizeW = :sizeW');
            $qb->setParameter('sizeW', $data['sizeW']);
        }
        if (isset($data['sizeL']) && $data['sizeL']) {
            $qb->andWhere('e.sizeL = :sizeL');
            $qb->setParameter('sizeL', $data['sizeL']);
        }
        if ($user) {
            if (isset($data['checked'])) {
                $qb->andWhere('e.checked = :checked');
                $qb->setParameter('checked', 'Y' === $data['checked']);
            }
            if (isset($data['finalcheck'])) {
                $qb->andWhere('e.finalcheck = :finalcheck');
                $qb->setParameter('finalcheck', 'Y' === $data['finalcheck']);
            }
            if (isset($data['finalattempt'])) {
                $qb->andWhere('e.finalattempt = :finalattempt');
                $qb->setParameter('finalattempt', 'Y' === $data['finalattempt']);
            }
        } else {
            $qb->andWhere('e.finalcheck = 1 OR e.finalattempt = 1');
        }
        if (isset($data['pubdate']) && $data['pubdate']) {
            $m = [];
            if (preg_match('/^\s*[0-9]{4}\s*$/', $data['pubdate'])) {
                $qb->andWhere("YEAR(STRTODATE(e.pubdate, '%Y')) = :year");
                $qb->setParameter('year', $data['pubdate']);
            } elseif (preg_match('/^\s*(\*|[0-9]{4})\s*-\s*(\*|[0-9]{4})\s*$/', $data['pubdate'], $m)) {
                $from = ('*' === $m[1] ? -1 : $m[1]);
                $to = ('*' === $m[2] ? 9999 : $m[2]);
                $qb->andWhere(":from <= YEAR(STRTODATE(e.pubdate, '%Y')) AND YEAR(STRTODATE(e.pubdate, '%Y')) <= :to");
                $qb->setParameter('from', $from);
                $qb->setParameter('to', $to);
            }
        }
        if (isset($data['date_of_first_publication']) && $data['date_of_first_publication']) {
            $m = [];
            if (preg_match('/^\s*[0-9]{4}\s*$/', $data['date_of_first_publication'])) {
                $qb->andWhere("YEAR(STRTODATE(e.dateOfFirstPublication, '%Y')) = :year");
                $qb->setParameter('year', $data['date_of_first_publication']);
            } elseif (preg_match('/^\s*(\*|[0-9]{4})\s*-\s*(\*|[0-9]{4})\s*$/', $data['date_of_first_publication'], $m)) {
                $from = ('*' === $m[1] ? -1 : $m[1]);
                $to = ('*' === $m[2] ? 9999 : $m[2]);
                $qb->andWhere(":from <= YEAR(STRTODATE(e.dateOfFirstPublication, '%Y')) AND YEAR(STRTODATE(e.dateOfFirstPublication, '%Y')) <= :to");
                $qb->setParameter('from', $from);
                $qb->setParameter('to', $to);
            }
        }
        if (isset($data['location']) && $data['location']) {
            $qb->innerJoin('e.locationOfPrinting', 'g');
            $qb->andWhere('MATCH(g.alternatenames, g.name) AGAINST (:location BOOLEAN) > 0');
            $qb->setParameter('location', $data['location']);
        }
        if (isset($data['format']) && is_array($data['format']) && count($data['format'])) {
            $qb->andWhere('e.format IN (:formats)');
            $qb->setParameter('formats', $data['format']);
        }
        if (null !== $data['price_filter']['price_pound'] ||
            null !== $data['price_filter']['price_shilling'] ||
            null !== $data['price_filter']['price_pence']) {
            $total = $data['price_filter']['price_pound'] * 240
                + $data['price_filter']['price_shilling'] * 12
                + $data['price_filter']['price_pence'];
            $operator = '<';
            switch ($data['price_filter']['price_comparison']) {
                case 'eq':
                    $operator = '=';

                    break;
                case 'lt':
                    $operator = '<';

                    break;
                case 'gt':
                    $operator = '>';

                    break;
            }
            $qb->andWhere("e.totalPrice {$operator} :total");
            $qb->andWhere('e.totalPrice > 0');
            $qb->setParameter('total', $total);
        }
        if (isset($data['genre']) && is_array($data['genre']) && count($data['genre'])) {
            $qb->andWhere('e.genre IN (:genres)');
            $qb->setParameter('genres', $data['genre']);
        }
        if (isset($data['signed_author']) && $data['signed_author']) {
            $qb->andWhere('MATCH (e.signedAuthor) AGAINST(:signedAuthor BOOLEAN) > 0');
            $qb->setParameter('signedAuthor', $data['signed_author']);
        }
        if (isset($data['imprint']) && $data['imprint']) {
            $qb->andWhere('MATCH (e.imprint) AGAINST(:imprint BOOLEAN) > 0');
            $qb->setParameter('imprint', $data['imprint']);
        }
        if (isset($data['colophon']) && $data['colophon']) {
            $qb->andWhere('MATCH (e.colophon) AGAINST(:colophon BOOLEAN) > 0');
            $qb->setParameter('colophon', $data['colophon']);
        }
        if (isset($data['pseudonym']) && $data['pseudonym']) {
            $qb->andWhere('MATCH (e.pseudonym) AGAINST (:pseudonym BOOLEAN) > 0');
            $qb->setParameter('pseudonym', $data['pseudonym']);
        }
        if (isset($data['shelfmark']) && $data['shelfmark']) {
            $qb->andWhere('MATCH (e.shelfmark) AGAINST (:shelfmark BOOLEAN) > 0');
            $qb->setParameter('shelfmark', $data['shelfmark']);
        }
        if (isset($data['notes']) && $data['notes']) {
            $qb->andWhere('MATCH (e.notes) AGAINST (:notes BOOLEAN) > 0');
            $qb->setParameter('notes', $data['notes']);
        }
        if (isset($data['self_published']) && $data['self_published']) {
            switch ($data['self_published']) {
                case 'Y':
                    $qb->andWhere('e.selfpublished = 1');

                    break;
                case 'N':
                    $qb->andWhere('e.selfpublished = 0');

                    break;
                case 'U':
                    $qb->andWhere('e.selfpublished is null');

                    break;
            }
        }

        // only add the title filter query parts if the subform has data.
        if (isset($data['person_filter']) && count(array_filter($data['person_filter']))) {
            $filter = $data['person_filter'];
            $idx = '00';
            $trAlias = 'tr_' . $idx;
            $pAlias = 'p_' . $idx;
            $qb->innerJoin('e.titleRoles', $trAlias)->innerJoin("{$trAlias}.person", $pAlias);
            if (isset($filter['name']) && $filter['name']) {
                $qb->andWhere("MATCH ({$pAlias}.lastName, {$pAlias}.firstName, {$pAlias}.title) AGAINST(:{$pAlias}_name BOOLEAN) > 0");
                $qb->setParameter("{$pAlias}_name", $filter['name']);
            }
            if (isset($filter['gender']) && $filter['gender']) {
                $genders = [];
                if (in_array('M', $filter['gender'], true)) {
                    $genders[] = 'M';
                }
                if (in_array('F', $filter['gender'], true)) {
                    $genders[] = 'F';
                }
                if (in_array('U', $filter['gender'], true)) {
                    $genders[] = '';
                }
                $qb->andWhere("{$pAlias}.gender in (:genders)");
                $qb->setParameter('genders', $genders);
            }

            if (isset($filter['person_role']) && count($filter['person_role']) > 0) {
                $qb->andWhere("{$trAlias}.role in (:roles_{$idx})");
                $qb->setParameter("roles_{$idx}", $filter['person_role']);
            }
        }

        // only add the firm filter query parts if the subform has data.
        if (isset($data['firm_filter']) && count(array_filter($data['firm_filter']))) {
            $filter = $data['firm_filter'];
            $idx = '01';
            $tfrAlias = 'tfr_' . $idx;
            $fAlias = 'f_' . $idx;
            $qb->innerJoin('e.titleFirmroles', $tfrAlias)->innerJoin("{$tfrAlias}.firm", $fAlias);
            if (isset($filter['firm_name']) && $filter['firm_name']) {
                $qb->andWhere("MATCH({$fAlias}.name) AGAINST(:{$fAlias}_name BOOLEAN) > 0");
                $qb->setParameter("{$fAlias}_name", $filter['firm_name']);
            }
            if (isset($filter['firm_role']) && count($filter['firm_role']) > 0) {
                $qb->andWhere("{$tfrAlias}.firmrole in (:firmroles_{$idx})");
                $qb->setParameter("firmroles_{$idx}", $filter['firm_role']);
            }
            if (isset($filter['firm_address']) && $filter['firm_address']) {
                $qb->andWhere("MATCH({$fAlias}.streetAddress) AGAINST(:{$fAlias}_address BOOLEAN) > 0");
                $qb->setParameter("{$fAlias}_address", $filter['firm_address']);
            }
            if (isset($filter['firm_gender']) && $filter['firm_gender']) {
                $genders = [];
                if (in_array('M', $filter['firm_gender'], true)) {
                    $genders[] = 'M';
                }
                if (in_array('F', $filter['firm_gender'], true)) {
                    $genders[] = 'F';
                }
                if (in_array('U', $filter['firm_gender'], true)) {
                    $genders[] = 'U';
                }
                $qb->andWhere("{$fAlias}.gender in (:genders)");
                $qb->setParameter('genders', $genders);
            }
        }

        if (isset($data['titlesource_filter']) && $data['titlesource_filter']) {
            /** @var TitleSource $filter */
            $filter = $data['titlesource_filter'];
            $qb->innerJoin('e.titleSources', 'ts');
            if ($filter->getSource()) {
                $qb->andWhere('ts.source = :source');
                $qb->setParameter('source', $filter->getSource());
            }
            if ($filter->getIdentifier()) {
                $qb->andWhere('MATCH(ts.identifier) AGAINST(:identifier BOOLEAN) > 0');
                $qb->setParameter('identifier', $filter->getIdentifier());
            }
        }

        return $qb->getQuery();
    }

    /**
     * Find and return $limit random titles.
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