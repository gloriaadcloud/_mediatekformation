<?php

namespace App\Tests\Validations;

use App\Entity\Formation;
use App\Entity\Playlist;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Description of FormationValiationsTest
 *
 * @author CYTech Student
 */
class FormationValidationsTest extends KernelTestCase 
{
    public function getFormation(): Formation
    {
        $playlist = new Playlist();
        $playlist->setName("Nom de la playlist de test");
        
        return (new Formation())
                ->setTitle("Titre de formation")
                ->setPublishedAt(new DateTime("2024-04-30"))
                ->setVideoId("Id video")
                ->setPlaylist($playlist);
               
    }
    
    public function assertValidationErrors(Formation $formation, int $expectedErrorCount, string $message = '')
    {
        self::bootKernel();
        /**@var ValidatorInterface $validator */
        $validator = self::getContainer()->get(ValidatorInterface::class);
        $errors = $validator->validate($formation);
        $this->assertCount($expectedErrorCount, $errors, $message);
    }
    
    public function testFormationValide()
    {
        $formation = $this->getFormation();
        $this->assertValidationErrors($formation, 0, "Une formation correctement remplie ne doit pas produire d'erreurs.");
    }

    public function testTitreVide()
    {
        $formation = $this->getFormation()->setTitle('');
        $this->assertValidationErrors($formation, 1, "Un titre vide doit produire une erreur.");
    }
    
    public function testDateFuture()
    {
        
        $formation = $this->getFormation()->setPublishedAt(new \DateTime('2025-12-01'));
        $this->assertValidationErrors($formation, 1, "La date de publication ne doit pas être dans le futur.");
    }

    public function testPlaylistVide()
    {
        $formation = $this->getFormation()->setPlaylist(null);
        $this->assertValidationErrors($formation, 1, "La playlist est obligatoire.");
    }

}
