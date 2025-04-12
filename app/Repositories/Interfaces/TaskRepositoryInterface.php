<?php

namespace App\Repositories\Interfaces;

interface TaskRepositoryInterface
{
    /* The `TaskRepositoryInterface` defines a contract for classes that will implement it. It
    specifies three methods that any class implementing this interface must provide: */
    public function create(array $data);

    public function update(int $id, array $data);
    
    public function delete(int $id);
}
    