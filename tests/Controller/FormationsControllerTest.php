<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;



/**
 * Description of FormationsControllerTest
 *
 * @author CYTech Student
 */
class FormationsControllerTest extends WebTestCase
{
    public function testAccesPage()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
    
    public function testContenuPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        $this->assertSelectorTextContains('h1', 'Liste des formations');
        $this->assertCount(5,$crawler->filter('th'));
        //$this->assertSelectorTextContains('h5', 'Liste des formations');
    }

    public function testLinkFormation()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations');
        
        //clic sur un lien portant ce texte
        $link = $crawler->selectLink('Eclipse n°4 : WindowBuilder')->link();
        $client->click($link);
        

        // Vérifie que l'URL de redirection est correcte
             
        $suri = $client->getRequest()->getRequestUri();
        $this->assertStringStartsWith('/formations/formation', $suri);

    }
    
    public function testFiltreTitreFormation()
    {
        $client = static::createClient();
        $client->request('GET', '/formations');
        //simulation de la soumission du formulaire
        $crawler = $client->submitForm('filtrer', [
            'recherche' => 'Eclipse'
        ]);
        
        
        //verifie le nbre de lignes obtenues
        $this->assertCount(5, $crawler->filter('h5'));
        //vérifie si la formation correspond à la recherche
        $this->assertSelectorTextContains('h5', 'Eclipse n°4 : WindowBuilder');

    }
    
    


}
