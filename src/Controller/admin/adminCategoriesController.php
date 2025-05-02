<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Description of adminCategoriesController
 *
 * @author CYTech Student
 */
class adminCategoriesController extends AbstractController
{
    private $categorieRepository;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    #[Route('/admin/categories', name: 'admin.categories')]
    public function index(): Response
    {
          
       // $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render("admin/admin.categories.html.twig", [
            //'formations' => $formations,
            'categories' => $categories
        ]);
        

       
    }
    
    

    #[Route('/admin/categories/delete/{id}', name: 'delete.categorie')]
    public function delete(int $id): Response
    {
        $categorie = $this->categorieRepository->find($id);
        //S’il y a bien une catégorie, et qu’elle n’est liée à aucune formation, alors...
        if ($categorie && count($categorie->getFormations()) === 0) {
            $this->categorieRepository->remove($categorie);
            
        } else {
            $this->addFlash('error', 'Impossible de supprimer une catégorie liée à des formations.');
        }
        return $this->redirectToRoute('admin.categories');
    }
    
    #[Route('/admin/categories/add', name: 'add.categorie')]
    public function add(Request $request): Response
    {
       
        $categorie = new Categorie();
        $formCategorie = $this->createForm(CategorieType::class, $categorie);
        $formCategorie->handleRequest($request);

        if ($formCategorie->isSubmitted() && $formCategorie->isValid()) 
        {
            // Vérifier si le nom existe déjà
            $exist = $this->categorieRepository->findOneBy(['name' => $categorie->getName()]);
            if (!$exist) {
                $this->categorieRepository->add($categorie);
                $this->addFlash('success', 'Catégorie ajoutée.');
                return $this->redirectToRoute('admin.categories');
            } else {
                $formCategorie->get('name')->addError(new \Symfony\Component\Form\FormError('Ce nom de catégorie existe déjà.'));
            }
        }

        $categories = $this->categorieRepository->findAll();
        
        return $this->render('admin/add.categorie.html.twig', [
            'categories' => $categories,
            'formcategorie' => $formCategorie->createView()
        ]);
    }
}
