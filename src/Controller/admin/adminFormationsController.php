<?php

namespace App\Controller\admin;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * Description of adminFormationController
 *
 * @author CYTech Student
 */
class adminFormationsController extends AbstractController {
private const VIEW_FORMATIONS = "admin/admin.formations.html.twig";

    /**
     * 
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     * 
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository) 
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    #[Route('/admin', name: 'admin.formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/admin.formations.html.twig", [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }
        

    #[Route('/admin/delete/{id}', name: 'delete.formation')]
    public function delete(int $id): Response 
    {
        $formation = $this->formationRepository->find($id);
        if ($formation){
            $this->formationRepository->remove($formation);
        }
        return $this->redirectToRoute('admin.formations');

    }
    
    #[Route('/admin/edit/{id}', name: 'edit.formation')]
    public function edit(int $id, Request $request): Response 
    {
        $formation = $this->formationRepository->find($id);
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid())
        {
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/edit.formation.html.twig", [
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);

    }
    
    #[Route('/admin/add/', name: 'add.formation')]
    public function add(Request $request): Response
    {
        $formation= new Formation();
        $formFormation = $this->createForm(FormationType::class, $formation);
        
        $formFormation->handleRequest($request);
        if($formFormation->isSubmitted() && $formFormation->isValid())
        {
          
            $this->formationRepository->add($formation);
            return $this->redirectToRoute('admin.formations');
        }
        return $this->render("admin/add.formation.html.twig", [
            'formation' => $formation,
            'formformation' => $formFormation->createView()
        ]);
        
        
    }

        #[Route('/admin/formations/tri/{champ}/{ordre}/{table}', name: 'adminformations.sort')]
    public function sort($champ, $ordre, $table=""): Response{
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        
        return $this->render(SELF::VIEW_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }     

    #[Route('/admin/formations/recherche/{champ}/{table}', name: 'adminformations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(SELF::VIEW_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }  

    #[Route('/admin/formations/formation/{id}', name: 'adminformations.showone')]
    public function showOne($id): Response{
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);        
    }   
    
    
    
}
