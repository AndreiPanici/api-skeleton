<?php declare(strict_types=1);

namespace App\Search;

abstract class BaseSearch
{

    /**
     * @var integer
     */
    public const DEFAULT_RESULTS = 10;

    /**
     * @var string
     */
    public const ORDER_DIRECTION_ASC = 'ASC';

    /**
     * @var string
     */
    public const ORDER_DIRECTION_DESC = 'DESC';

    /**
     * @var integer
     */
    protected $page;

    /**
     * @var integer|null
     */
    protected $offset;

    /**
     * @var integer|null
     */
    protected $limit;

    /**
     * @var string|null
     */
    protected $orderBy;

    /**
     * @var string|null
     */
    protected $orderDirection;

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return void
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     */
    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    /**
     * @param string|null $orderBy
     */
    public function setOrderBy(?string $orderBy): void
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return string|null
     */
    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    /**
     * @param string|null $orderDirection
     */
    public function setOrderDirection(?string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }
}
