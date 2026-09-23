<?php

namespace App\Services\Facades;

use App\Services\Interfaces\BaseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class BaseFacade implements BaseInterface
{

    private $_Model;
    private array $_Columns = [],
        $_Where = [],
        $_Rules = [];
    private string $_OrderBy = 'asc',
        $_OrderColumn = "created_at",
        $_HashColumn = "password",
        $_UniqueColumn = "email";
    private bool
        $_Unique = false,
        $_Hash = false;

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->_Model;
    }

    /**
     * @param mixed $Model
     */
    public function setModel($Model): void
    {
        $this->_Model = $Model;
    }

    /**
     * @return array
     */
    public function getColumns(Request $request): array
    {
        $columns = [];
        $all = $request->all();
        foreach ($all as $key => $item) {
            if (($k = array_search($key, $this->_Columns)) !== false) {
                $columns = array_merge($columns, [$key => $item]);
            }
        }
        return $columns;
    }

    /**
     * @param array $Columns
     */
    public function setColumns(array $Columns): void
    {
        $this->_Columns = $Columns;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->_Where;
    }

    /**
     * @param array $Where
     */
    public function setWhere(array $Where): void
    {
        $this->_Where = $Where;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->_Rules;
    }

    /**
     * @param array $Rules
     */
    public function setRules(array $Rules): void
    {
        $this->_Rules = $Rules;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->_OrderBy;
    }

    /**
     * @param string $OrderBy
     */
    public function setOrderBy(string $OrderBy): void
    {
        $this->_OrderBy = $OrderBy;
    }

    /**
     * @return string
     */
    public function getOrderColumn(): string
    {
        return $this->_OrderColumn;
    }

    /**
     * @param string $OrderColumn
     */
    public function setOrderColumn(string $OrderColumn): void
    {
        $this->_OrderColumn = $OrderColumn;
    }

    /**
     * @return string
     */
    public function getHashColumn(): string
    {
        return $this->_HashColumn;
    }

    /**
     * @param string $HashColumn
     */
    public function setHashColumn(string $HashColumn): void
    {
        $this->_HashColumn = $HashColumn;
    }

    /**
     * @return string
     */
    public function getUniqueColumn(): string
    {
        return $this->_UniqueColumn;
    }

    /**
     * @param string $UniqueColumn
     */
    public function setUniqueColumn(string $UniqueColumn): void
    {
        $this->_UniqueColumn = $UniqueColumn;
    }

    /**
     * @return bool
     */
    public function isUnique(): bool
    {
        return $this->_Unique;
    }

    /**
     * @param bool $Unique
     */
    public function setUnique(bool $Unique): void
    {
        $this->_Unique = $Unique;
    }

    /**
     * @return bool
     */
    public function isHash(): bool
    {
        return $this->_Hash;
    }

    /**
     * @param bool $Hash
     */
    public function setHash(bool $Hash): void
    {
        $this->_Hash = $Hash;
    }

    function _getInstance(): Model
    {
        return new $this->_Model;
    }

    function paginate(Request $request): Collection|array
    {
        return $this->_getInstance()->query()
            ->where($this->_Where)
            ->orderBy($this->_OrderColumn, $this->_OrderBy)
            ->get();
    }

    function store(Request $request)
    {
        $request->validate($this->_Rules);
        if ($this->checkDuplicate($request)) {
            return [false, null];//bool,object
        }
        $object = $this->_getInstance()->query()->create($this->_Columns);
        if ($object) {
            return [true, $object];
        }
    }

    public function checkDuplicate(Request $request, $isEditing = false, $id = null): bool
    {
        if (!$this->_Unique) return false;
        $where = [
            $this->_UniqueColumn => $request->input($this->_UniqueColumn)
        ];
        if ($isEditing) {
                $id ?? $where[] = ["id" => $id];
        }
        return (bool)$this->getOneByColumns($where);
    }

    function update(Request $request, $id): array
    {
        $object = $this->getOneByColumns(["id", $id]);
        if ($object) {
            if ($this->checkDuplicate($request, true, $object->id)) {
                return [false, null];//bool,object
            }
            $request->validate($this->_Rules);
            $this->_getInstance()->query()->update($this->_Columns);
            return [true, $object];
        }
        return [true, null];
    }

    function getBuilder($columns): Builder
    {
        return $this->_getInstance()->query()->where($columns);
    }

    function getListByColumns($columns): Collection|array
    {
        return $this->_getInstance()->query()->where($columns)->get();
    }

    function getOneByColumns($columns): Model|null
    {
        return $this->_getInstance()->query()->where($columns)->first();
    }

    function delete($id)
    {
        return $this->_getInstance()->query()->where([
            "id" => $id
        ])->delete();
    }
}
