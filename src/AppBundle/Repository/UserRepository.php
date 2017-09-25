<?php

namespace AppBundle\Repository;


use Doctrine\ORM\Query;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{

    /**
     * @param string $sortField
     * @param bool $isAscending
     * @param array $filters
     * @param int $offset
     * @param int $itemsPerPage
     * @return array
     */
    public function getUsersList(string $sortField, bool $isAscending, array $filters, int $offset, int $itemsPerPage): array
    {
        $query = 'SELECT u FROM AppBundle:User u';
        return $this->getSortedAndFilteredUsers($query, $sortField, $isAscending, $filters)
            ->setFirstResult($offset)
            ->setMaxResults($itemsPerPage)
            ->getResult();
    }

    /**
     * @param string $sortField
     * @param bool $isAscending
     * @param array $filters
     * @return int
     */
    public function getUsersCount(string $sortField, bool $isAscending, array $filters): int
    {
        $query = 'SELECT COUNT(u) FROM AppBundle:User u';
        return $this->getSortedAndFilteredUsers($query, $sortField, $isAscending, $filters)
            ->getSingleScalarResult();
    }

    /**
     * @param string $query
     * @param string $sortField
     * @param bool $isAscending
     * @param array $filters
     * @return Query
     */
    private function getSortedAndFilteredUsers(string $query, string $sortField, bool $isAscending, array $filters): Query
    {
        $query .= $this->getDQLWithFilters($filters);
        $query .= ' ORDER BY u.' . $sortField . ' ' . ($isAscending ? 'ASC' : 'DESC');

        $temp = [];
        for ($i = 0; $i < count($filters); $i++) {
            $temp[$i] = $filters[$i][1];
        }

        return $this->getEntityManager()
            ->createQuery($query)
            ->setParameters($temp);
    }

    /**
     * @param array $filters
     * @return string
     */
    private function getDQLWithFilters(array $filters): string
    {
        $result = '';
        if (key_exists(0, $filters)) {
            $result = ' WHERE u.' . $filters[0][0] . ' = ?0';
            for ($i = 1; $i < count($filters); $i++) {
                $result .= ' AND u.' . $filters[$i][0] . ' = ' . '?' . $i;
            }
        }
        return $result;
    }
}


