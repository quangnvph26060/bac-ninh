<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class BaseService
{
    public function __construct(public Model $model)
    {
        $this->model = $model;
    }

    // public function create(array $data)
    // {
    //     return $this->model->create($data);
    // }

    public function updateData(int $id, array $payload)
    {
        $record = $this->findById($id);
        if ($record->update($payload)) {
            return $record;
        }
        return false;
    }

    // public function destroy(int $id)
    // {
    //     $record = $this->model->findOrFail($id);
    //     return $record->delete();
    // }

    public function firstdByWhere(array $columns = ['*'], array $wheres = [], array $relations = [])
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($wheres as $condition) {
            if (count($condition) === 3) {
                $query->where($condition[0], $condition[1], $condition[2]);
            } elseif (count($condition) === 2) {
                $query->where($condition[0], $condition[1]);
            }
        }

        return $query->firstOrFail($columns);
    }

    public function all(
        array $columns = ['*'],
        array $relations = [],
        array $wheres = [],
        array $whereHasConditions = [],
        array $withCount = [],
        array $order = [],
    ) {
        $conditions = $this->payload();

        $query =  $this->model->select($columns);

        if (count($relations) > 0) {
            $query->with($relations);
        }

        if ($withCount) {
            $query->withCount($withCount);
        }

        foreach ($wheres as $condition) {
            if (count($condition) === 3) {
                $query->where($condition[0], $condition[1], $condition[2]);
            } elseif (count($condition) === 2) {
                $query->where($condition[0], $condition[1]);
            }
        }

        foreach ($whereHasConditions as $relation => $column) {
            $query->when(!empty($conditions[$column]), function ($q) use ($relation, $column, $conditions) {
                $q->whereHas($relation, function ($query) use ($column, $conditions) {
                    $query->where($column, $conditions[$column]);
                });
            });
        }

        if ($order) {
            $query->orderBy($order[0], $order[1]);
        }

        $query->when(empty($conditions['order']), fn($q) => $q->latest('id'));

        Log::info($query->toRawSql());

        return $query->get();
    }

    public function findById(string $id, array $columns = ['*'], array $relations = [], array $wheres = [], array $order = [])
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($wheres as $condition) {
            if (count($condition) === 3) {
                $query->where($condition[0], $condition[1], $condition[2]);
            } elseif (count($condition) === 2) {
                $query->where($condition[0], $condition[1]);
            }
        }

        if ($order) {
            $query->orderBy($order[0], $order[1]);
        }

        return $query->findOrFail($id, $columns);
    }

    protected function payload()
    {
        return request()->toArray();
    }

    public function pluck(array $columns = [], array $relations = [], array $where = [], array $order = [])
    {
        $query = $this->model->query();

        if (!empty($relations)) {
            $query->with($relations);
        }

        foreach ($where as $condition) {
            if (count($condition) === 3) {
                $query->where($condition[0], $condition[1], $condition[2]);
            } elseif (count($condition) === 2) {
                $query->where($condition[0], $condition[1]);
            }
        }

        if (!empty($order)) {
            $query->orderBy($order[0], $order[1]);
        }

        Log::info($query->toSql());

        return $query->pluck(...$columns)->toArray();
    }

    public function queryBuilder(
        array $columns = ['*'],
        array $relations = [],
        bool  $all = false,
        array $filters = [],
        array $wheres = [],
        array $whereHasConditions = [],
        array $withCount = [],
        array $order = [],
    ) {
        $conditions = $this->payload();

        $query =  $this->model->select($columns);

        if (count($relations) > 0) {
            $query->with($relations);
        }

        if ($withCount) {
            $query->withCount($withCount);
        }

        foreach ($wheres as $condition) {
            if (count($condition) === 3) {
                $query->where($condition[0], $condition[1], $condition[2]);
            } elseif (count($condition) === 2) {
                $query->where($condition[0], $condition[1]);
            }
        }

        foreach ($whereHasConditions as $relation => $column) {
            $query->when(!empty($conditions[$column]), function ($q) use ($relation, $column, $conditions) {
                $q->whereHas($relation, function ($query) use ($column, $conditions) {
                    $query->where($column, $conditions[$column]);
                });
            });
        }

        foreach ($filters as $filter) {
            $query->when(!empty($conditions[$filter]), fn($q) => $q->where($filter, $conditions[$filter]));
        }

        if ($order) {
            $query->orderBy($order[0], $order[1]);
        }

        $query->when(empty($conditions['order']), fn($q) => $q->latest('id'));

        Log::info($query->toRawSql());

        return $all ? $query->get() : $query;
    }
}
