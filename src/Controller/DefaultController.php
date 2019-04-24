<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Page;
use App\Entity\User;
use App\Repository\BlogRepository;
use App\Repository\PageRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     * @Route("/home", name="home")
     */
    public function index(BlogRepository $repository)
    {
        $blogs = $repository->findAll(); // on demande a la base de données tous les blogs

        return $this->render('default/index.html.twig', [
            'blogs' => $blogs
        ]);
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     * @return Response
     *
     * recupere le repositoy pour faire de la requette SQL;
     * symfony, parle le biais de l'id, récupere l'entité concernée (le blog)
     *
     */
    public function blog(Blog $blog,PageRepository $repository, $id)
    {
        $pages = $repository->FindByBlogId($id);
        return $this->render('default/blog.html.twig', [
            'pages'=>$pages,  // les clef et la valeur
            'blog_name'=>$blog->getTitle(),
            'blog_id'=> $blog->getId()
        ]);
    }


    //<!-- create de la méthode show_page avec sa route  -->
    /**
     * @Route("/blog/page/{id}", name="blog_page_show")
     */
    public function showPage(Page $page)
    {
        return $this->render('default/page.html.twig',[
            'page' => $page
        ]);
    }

    /**
     * @Route("/blog/page/create/{id}", name="blog_page_create")
     */
    public function createPage(
        Request $request,
        EntityManagerInterface $manager,
        $id,
        BlogRepository $repository
    ) {
       $blog = $repository->find($id);
       $page = new Page();

       $form = $this->createFormBuilder($page)
           ->add('title', TextType::class)
           ->add('content', TextareaType::class)
           ->getForm();

       $form->handleRequest($request); // a chaque methode il y a toujours un retour ...

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setBlog($blog); // setter le blog
            $page->setUser($this->getUser()); // setter l'utilisateur

            $manager->persist($page); // prepare pour envoyer a la bdd
            $manager->flush(); // et je l'envoie

            return $this->redirectToRoute('blog_show', [ // ont redirige vers la liste des pages
                'id' => $id //on redirige dans le blog
            ]);
        }

        return $this->render('default/create_page.html.twig', [
            'form_create'=>$form->createView() // rendu du formulaire
        ]);

    }

    /**
     * @Route("/register", name="register")
     */
    public function register(
        Request $request,
        EntityManagerInterface $manager,
        UserPasswordEncoderInterface $encoder
        ) {
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);

            $encoded = $encoder->encodePassword($user, $form['password']->getData());
            $user->setPassword($encoded);

            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('default/register.html.twig', [
            'register_form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {
        return $this->render('default/contact.html.twig');
    }

    /**
     * @Route("/apropos", name="apropos")
     */
    public function apropos()
    {
        return $this->render('default/apropos.html.twig');
    }

    /**
     * @Route("/reference", name="reference")
     */
    public function reference()
    {
        return $this->render('default/reference.html.twig');
    }


}
