<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function sendEmail(MailerInterface $mailer, Request $request)
    {
        $contact = new Contact();
        
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new Email())
                ->from($contact->getEmail())
                ->to('contact@gloriaadonsou.com')
                ->subject('Message du site de formations')
                ->html($this->renderView(
                        'pages/_email.html.twig', [
                            'contact' => $contact
                        ]
                    )
                   
                )
            ;
            $mailer->send($email);
            $this->addFlash('success', 'Votre message a été envoyé avec succès!');
            
            // Rediriger ou afficher un message de confirmation
            return $this->redirectToRoute('app_contact');
            
        }
        
        // Rendre le formulaire dans la vue
        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
