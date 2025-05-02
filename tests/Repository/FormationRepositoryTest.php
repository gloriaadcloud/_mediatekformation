<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Repository\FormationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Description of FormationRepositoryTest
 *
 * @author CYTech Student
 */
class FormationRepositoryTest extends KernelTestCase 
{
    private function recupRepository(): FormationRepository
    {
        self::bootKernel();
        $repository = self::getContainer()->get(FormationRepository::class);
        return $repository;
    }
    
    public function testNbFormations(): void
    {
        $repository = $this->recupRepository();
        $nbFormations = $repository->count([]);
        $this->assertEquals(238, $nbFormations); // à adapter selon ta BDD test
    }
    
    private function newFormation(): Formation
    {
        $formation = (new Formation())
            ->setTitle("Formation test")
            ->setDescription("Description test")
            ->setVideoId("video_test")
            ->setPublishedAt(new DateTime("now"));
        return $formation;
    }
    
    public function testAddFormation(): void
    {
        $repository = $this->recupRepository();
        $nbFormations = $repository->count([]);
        $formation = $this->newFormation();

        $repository->add($formation, true); 

        $this->assertEquals($nbFormations + 1, $repository->count([]), "Erreur lors de l'ajout d'une formation");
    }

    public function testRemoveFormation(): void
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);

        $nbFormations = $repository->count([]);
        $repository->remove($formation, true);

        $this->assertEquals($nbFormations - 1, $repository->count([]), "Erreur lors de la suppression d'une formation");
    }

    
    public function testFindByEqualValue(): void
    {
        $repository = $this->recupRepository();
        $formation = $this->newFormation();
        $repository->add($formation, true);

        $formations = $repository->findByEqualValue("title", "Formation test");
        $nbFormations = count($formations);
        $this->assertEquals(1, $nbFormations);
        $this->assertEquals("Formation test", $formations[0]->getTitle());
    }
    
}
    
    

