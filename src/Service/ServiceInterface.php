<?php

namespace App\Service;

interface ServerceInterface {
  public function create(Object $objet);
  public function update(Object $Objet);
  public function delete(int $id);
  public function findOneBy(int $id);
  public function findAll(): array;
}
