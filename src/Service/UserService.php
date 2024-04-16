<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserService implements ServerceInterface {

  public function __construct(
    private readonly UserRepository $ur,
    private readonly EntityManagerInterface $em
  ) {
  }

  public function create(Object $object) {
    //Tester si l'objet existe
    if ($this->ur->findOneBy(['email' => $object->getEmail()])) {
      $this->em->persist($object);
      $this->em->flush();
    }
    return throw new \Exception("Le compte existe déjà");
  }

  public function update(Object $object) {
    if ($this->ur->findOneBy(['email' => $object->getEmail()])) {
      $this->em->persist($object);
      $this->em->flush();
    }
    return throw new \Exception("Le compte n'existe pas");
  }

  public function delete(int $id) {

    if ($this->ur->find($id)) {
      $this->em->remove($this->ur->find($id));
      $this->em->flush();
    }
    throw new \Exception("Le compte n'existe pas");
  }

  public function findOneBy(int $id) {
    return $this->ur->find($id) ?? throw new \Exception("Le compte existe déjà");
  }

  public function findAll(): array {
    return $this->ur->findAll() ?? throw new \Exception("Le compte existe déjà");
  }
}
