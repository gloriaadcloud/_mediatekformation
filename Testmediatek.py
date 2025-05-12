import time
import unittest
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException
from webdriver_manager.chrome import ChromeDriverManager

class MediatekFormationTest(unittest.TestCase):
    """Test de navigation pour le site MediatekFormation"""
    
    def setUp(self):
        """Configuration du navigateur avant chaque test"""
        chrome_options = Options()
        # Décommentez la ligne suivante pour exécuter en mode headless (sans interface graphique)
        # chrome_options.add_argument("--headless")
        chrome_options.add_argument("--window-size=1920,1080")
        
        # Initialisation du driver
        self.driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=chrome_options)
        self.driver.maximize_window()
        self.wait = WebDriverWait(self.driver, 10)
        
        # URL de base
        self.base_url = "https://mediatekformationprod.gloriaadonsou.com"
    
    def tearDown(self):
        """Nettoyage après chaque test"""
        self.driver.quit()
    
    def test_navigation_complete(self):
        """Test de navigation complet sur le site"""
        # 1. Accéder à la page d'accueil
        self.driver.get(self.base_url)
        print("Page d'accueil chargée")
        
        # Vérifier que la page d'accueil est bien chargée avec le message de bienvenue
        welcome_element = self.wait.until(
            EC.presence_of_element_located((By.XPATH, "//h3[contains(text(), 'Bienvenue sur le site de MediaTek86 consacré aux formations en ligne')]"))
        )
        self.assertTrue(welcome_element.is_displayed())
        
        # Capture d'écran de la page d'accueil
        self.driver.save_screenshot("accueil.png")
        
        # 2. Naviguer vers la page des formations
        formations_link = self.wait.until(
            EC.element_to_be_clickable((By.XPATH, "//a[contains(@href, '/formations')]"))
        )
        formations_link.click()
        print("Navigation vers la page des formations")
        
        # Vérifier que nous sommes sur la page des formations
        self.wait.until(EC.url_contains("/formations"))
        
        # Capture d'écran de la page des formations
        self.driver.save_screenshot("formations.png")
        
        # 3. Tester la fonctionnalité de recherche (si disponible)
        try:
            search_input = self.wait.until(
                EC.presence_of_element_located((By.CSS_SELECTOR, "input[type='text']"))
            )
            search_input.clear()
            search_input.send_keys("Python")
            print("Recherche effectuée avec le mot-clé 'Python'")
            
            # Laisser le temps au résultat de s'afficher
            time.sleep(2)
            
            # Capture d'écran des résultats de recherche
            self.driver.save_screenshot("recherche.png")
        except TimeoutException:
            print("Champ de recherche non trouvé, test de recherche ignoré")
        
        # 4. Ouvrir les détails d'une formation (cliquer sur la première formation disponible)
        try:
            first_formation = self.wait.until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, ".card-img-top, .formation-item"))
            )
            first_formation.click()
            print("Ouverture des détails d'une formation")
            
            # Vérifier que la page de détail est chargée
            time.sleep(2)
            
            # Capture d'écran de la page de détail
            self.driver.save_screenshot("detail_formation.png")
            
            # Vérifier si une vidéo est présente
            try:
                video_element = self.driver.find_element(By.TAG_NAME, "video, iframe")
                print("Vidéo trouvée sur la page de détail")
            except:
                print("Aucune vidéo trouvée sur la page de détail")
                
            # Retour à la page des formations
            self.driver.back()
            print("Retour à la page des formations")
            
        except TimeoutException:
            print("Aucune formation trouvée pour afficher les détails")
        
        # 5. Naviguer vers la page des playlists
        playlists_link = self.wait.until(
            EC.element_to_be_clickable((By.XPATH, "//a[contains(@href, '/playlists')]"))
        )
        playlists_link.click()
        print("Navigation vers la page des playlists")
        
        # Vérifier que nous sommes sur la page des playlists
        self.wait.until(EC.url_contains("/playlists"))
        
        # Capture d'écran de la page des playlists
        self.driver.save_screenshot("playlists.png")
        
        
        
        # 7. Retour à la page d'accueil
        home_link = self.wait.until(
            EC.element_to_be_clickable((By.XPATH, "//a[contains(@href, '/')]"))
        )
        home_link.click()
        print("Retour à la page d'accueil")
        
        # Vérifier que nous sommes bien de retour sur la page d'accueil
        self.wait.until(EC.url_contains(self.base_url))
        
        print("Test de navigation terminé avec succès!")


# Exécuter les tests si le script est exécuté directement
if __name__ == "__main__":
    unittest.main()