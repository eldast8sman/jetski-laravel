<?php

namespace App\Repositories;

use App\Repositories\Interfaces\AbstractRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;

class AbstractRepository implements AbstractRepositoryInterface
{
    protected $model;
    public $error_msg;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function create($data){
        if(!$model = $this->model->create($data)){
            $this->error_msg = "Creation Failed";
            return false;
        }
        return $model;
    }

    public function all($orderBy=[], $limit=null, $count=false){
        if(!empty($orderBy)){
            $models = $this->model;
            foreach($orderBy as $order){
                $models = $models->orderBy($order[0], $order[1]);
            }

            if($count == true){
                return $models->count();
            } elseif(!empty($limit)){
                return $models->paginate($limit);
            } else {
                return $models->get();
            }
        } else {
            if($count == true){
                return $this->model->count();
            } elseif(!empty($limit)){
                return $this->model->paginate($limit);
            } else {
                return $this->model->all();
            }
        }
    }

    public function find(int $id){
        try {
            $data = $this->model->find($id);
            return $data;
        } catch(Exception $e){
            return false;
        }
    }

    public function findByUuid($uuid)
    {
        try {
            $data = $this->model->where('uuid', $uuid)->first();
            return $data;
        } catch(Exception $e){
            return false;
        }
    }

    public function findBy(array $criteria, $orderBy=[], $limit=null, $count=false){
        try {
            if(empty($criteria)){
                return $this->all($orderBy, $limit, $count);  
            }

            $data = $this->model->where($criteria);
            if(!empty($orderBy)){
                foreach($orderBy as $order){
                    $data = $data->orderBy($order[0], $order[1]);
                }
            }
    
            if($count == true){
                $data = $data->count();
            } elseif(isset($limit)){
                $data = $data->paginate($limit);
            } else {
                $data = $data->get();
            }
    
            return $data;
        } catch(Exception $e){
            $this->error_msg = $e->getMessage();
            return false;
        }
    }

    public function findFirstBy(array $criteria, $orderBy=[])
    {
        try {
            if(empty($criteria)){
                return false;
            }
            
            $data = $this->model->where($criteria);
            if(!empty($orderBy)){
                foreach($orderBy as $order){
                    $data = $data ->orderBy($order[0], $order[1]);
                }
            }
            
            return $data->first();
        } catch(Exception $e){
            return false;
        }
    }

    public function findByOr(array $criteria, $orderBy=[], $limit=null, $count=false)
    {
        try {
            if(empty($criteria)){
                return false;
            }

            $first = array_shift($criteria);
            $data = $this->model->where(key($first), reset($first));
            if(!empty($criteria)){
                foreach($criteria as $key=>$value){
                    $data = $data->orWhere($key, $value);
                }
            }
            if(!empty($orderBy)){
                foreach($orderBy as $order){
                    $data = $data->orderBy($order[0], $order[1]);
                }
            }
            if($count == true){
                $data = $data->count();
            } elseif(isset($limit)){
                $data = $data->paginate($limit);
            } else {
                $data = $data->get();
            }

            return $data;
        } catch(Exception $e){
            return false;
        }
    }

    public function findByOrFirst(array $criteria, $orderBy=[])
    {
        try {
            if(empty($criteria)){
                return false;
            }

            $first = array_shift($criteria);
     
            $data = $this->model->where(key($first), reset($first));
            if(!empty($criteria)){
                foreach($criteria as $criterion){
                    $data = $data->orWhere(key($criterion), reset($criterion));
                }
            }
            if(!empty($orderBy)){
                foreach($orderBy as $order){
                    $data = $data->orderBy($order[0], $order[1]);
                }
            }
            
            return $data->first();
            
        } catch(Exception $e){
            return false;
        }
    }

    public function paginate($limit){
        return $this->model->paginate($limit);
    }

    public function update($id, $data=[]){
        try {
            if(!$model = $this->find($id)){
                $this->error_msg = "Empty Model";
                return false;
            }
    
            $model->update($data);
            return $model;
        } catch(Exception $e){
            $this->error_msg = $e->getMessage();
            return false;
        }
    }

    public function save(Model $model){
        $model = $model->save();
    }

    public function delete(Model $model){
        $model->delete();
        return true;
    }
}