<?php

namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of adminPlaylistsController
 *
 * @author CYTech Student
 */
class adminPlaylistsController extends AbstractController
{
    private const VIEW_PLAYLISTS = "admin/admin.playlists.html.twig";

    /**
     * 
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
    
    function __construct(PlaylistRepository $playlistRepository,
            CategorieRepository $categorieRepository,
            FormationRepository $formationRespository) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRespository;
    }
    
    /**
     * @Route("/playlists", name="playlists")
     * @return Response
     * 
     */

    #[Route('/admin/playlists', name: 'admin.playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderByName('ASC');
        
        $categories = $this->categorieRepository->findAll();
        return $this->render('/admin/admin.playlists.html.twig', [
            'playlists' => $playlists,
            'categories' => $categories
        ]); 
    }
        
    #[Route('/admin/playlists/delete/{id}', name:'delete.playlist')]
    public function delete(int $id): Response
    {
        $formation=$this->formationRepository->findBy(['playlist'=>$id]);
        
        foreach($formation as $formation){
            $this->formationRepository->remove($formation);
        }
        
        $playlist= $this->playlistRepository->find($id);
        //dd($playlist->getFormations());
        if($playlist){
            $this->playlistRepository->remove($playlist);
            
        }
        return $this->redirectToRoute ('admin.playlists');
    }
    
    #[Route('/admin/playlists/edit/{id}', name:'edit.playlist')]
        public function edit(int $id, Request $request): Response
        {
            $playlist = $this->playlistRepository->find($id);
            $formPlaylist = $this->createForm(PlaylistType::class, $playlist);

            $formPlaylist->handleRequest($request);
            if($formPlaylist->isSubmitted() && $formPlaylist->isValid())
            {
                $this->playlistRepository->add($playlist);
                return $this->redirectToRoute('admin.playlists');
            }
            
            return $this->render("admin/edit.playlist.html.twig", [
                'playlist' => $playlist,
                'formplaylist' => $formPlaylist->createView()
            ]);
        }
    
    #[Route('/admin/playlists/add/', name:'add.playlist')]
    public function add(Request $request): Response
    {
        $playlist = new Playlist;
        $formPlaylist = $this->createForm(PlaylistType::class, $playlist);
        
        $formPlaylist->handleRequest($request);
        if($formPlaylist->isSubmitted() && $formPlaylist->isValid())
        {
            $this->playlistRepository->add($playlist);
            return $this->redirectToRoute('admin.playlists');
        }
        return $this->render("admin/add.playlist.html.twig", [
            'playlist' => $playlist,
            'formplaylist' => $formPlaylist->createView()
        ]);
    }   

    #[Route('/admin/playlists/tri/{champ}/{ordre}', name: 'adminplaylists.sort')]
    public function sort($champ, $ordre): Response{
        switch($champ){
            case "name":
                $playlists = $this->playlistRepository->findAllOrderByName($ordre);
                break;
            
            case "NbFormations":       
                $playlists = $this->playlistRepository->findAllOrderByNbFormations($ordre);
                break;
            
            case "":
                $playlists = $this->playlistRepository->findAllOrderBy($ordre);
                break;
            default:
            $playlists = $this->playlistRepository->findAll();
            break;
                
        }
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VIEW_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories            
        ]);
    }          

    #[Route('/admin/playlists/recherche/{champ}/{table}', name: 'adminplaylists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response{
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VIEW_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,            
            'valeur' => $valeur,
            'table' => $table
        ]);
    }  

    #[Route('/admin/playlists/playlist/{id}', name: 'adminplaylists.showone')]
    public function showOne($id): Response{
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("admin/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);        
    }
        
}
