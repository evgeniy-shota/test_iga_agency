<?php

namespace App\Actions;

class SpreadSheetService
{
    public function getAll(int $userId) {}

    public function get(int $id, int $userId) {}

    public function create(array $data, int $userId) {}

    public function update(int $id, array $data, int $userId) {}

    public function delete(int $id, int $userId) {}

    public function massCreate(array $data, int $userId, int $count = 1000) {}
}
