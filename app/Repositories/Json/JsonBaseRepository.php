<?php

namespace App\Repositories\Json;

use App\Entities\User\UserEntity;
use App\Entities\User\UserJsonEntity;
use App\Repositories\Contracts\RepositoryInterface;

class JsonBaseRepository implements RepositoryInterface
{
    protected string $jsonModel;

    public function store(array $data)
    {
        if (file_exists($this->jsonModel)) {
            $users = json_decode(file_get_contents($this->jsonModel), true);
            $data['id'] = rand(1, 1000);
            array_push($users, $data);
            file_put_contents($this->jsonModel, json_encode($users));
        } else {
            $users = [];
            $data['id'] = rand(1, 1000);
            array_push($users, $data);
            file_put_contents($this->jsonModel, json_encode($users));
        }
        return $data;
    }

    public function update(int $id, array $data)
    {
        $users = json_decode(file_get_contents($this->jsonModel), true);

        foreach ($users as $key => $user) {
            if ($user['id'] == $id) {
                $user['full_name'] = $data['full_name'] ?? $user['full_name'];
                $user['mobile'] = $data['mobile'] ?? $user['mobile'];
                $user['email'] = $data['email'] ?? $user['email'];
                $user['password'] = $data['password'] ?? $user['password'];

                unset($users[$key]);
                array_push($users,$user);

                if(file_exists($this->jsonModel)){
                    unlink($this->jsonModel);
                }

                file_put_contents($this->jsonModel,json_encode($users));
                break;
            }

        }
    }

    public function paginate(int $page, int $pageSize = 20, string $search = null)
    {
        $users = json_decode(file_get_contents(base_path() . '/users.json'), true);

        if(!is_null($search)){
            foreach($users as $key => $user){
                if(array_search($search,$user)){
                    return $users[$key];
                }
            }
        }

        $totalRecords = count($users);
        $totalPages = ceil($totalRecords / $pageSize);

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        if ($page < 1) {
            $page = 1;
        }

        $offset = ($page - 1) * $pageSize;

        return array_slice($users,$offset,$pageSize);
    }

    public function all(array $where)
    {
        // TODO: Implement all() method.
    }

    public function find(int $id)
    {
        $file = base_path($this->jsonModel);
        $users = json_decode(file_get_contents($file),true);

        foreach($users as $user){
            if($user['id'] == $id){
                return $user;
            }
        }
        return [];
    }

    public function delete(int $id)
    {
        $file = $this->jsonModel;
        $users = json_decode(file_get_contents($file),true);
        foreach($users as $key => $user){
            if($user['id'] == $id){

                unset($users[$key]);

                if(file_exists($file)){
                    unlink($file);
                }

                file_put_contents($file,json_encode($users));
                break;
            }

        }
    }

    public function deleteBy(array $where)
    {
        // TODO: Implement deleteBy() method.
    }
}
