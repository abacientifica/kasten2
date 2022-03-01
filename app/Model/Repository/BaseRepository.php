<?php

namespace App\Model\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BaseRepository 
{
    protected $model;

    public function __construct(Model $model){
        $this->model = $model;
    }

    public function getQuery()
    {
        return $this->model->query();
    }

    public function factory(): \Illuminate\Database\Eloquent\Factories\Factory
    {
        return $this->model::factory();
    }

    public function getDbQuery()
    {
        return DB::connection($this->model->getConnectionName())->table($this->model->getTable());
    }

    public function first()
    {
        return $this->getQuery()->first();
    }

    public function all()
    {
        return $this->getQuery()->get();
    }

    public function count()
    {
        return $this->getQuery()->count();
    }

    public function paginate($limit = 10)
    {
        return $this->getQuery()->paginate($limit);
    }

    public function find($id, $withTrash = false)
    {
        if ($withTrash) {
            return $this->getQuery()->withTrashed()->find($id);
        }

        return $this->getQuery()->find($id);
    }

    public function dbSelect($select){
        return DB::select($select);
    }

    public function where($column, $id, $first = false)
    {
        $query = $this->getQuery()->where($column, $id);

        return ($first) ? $query->first() : $query->get();
    }

    public function create(array $request)
    {
        return $this->getQuery()->create($request);
    }

    public function with($relation)
    {
        return $this->getQuery()->with($relation);
    }

    public function update($id, array $request, $withTrash = false)
    {
        if ($withTrash) {
            $app = $this->getQuery()->withTrashed()->find($id);
        } else {
            $app = $this->getQuery()->find($id);
        }

        $app->update($request);

        return $app;
    }
  
    public function delete($id)
    {
        return $this->getQuery()->find($id)->delete();
    }
}
