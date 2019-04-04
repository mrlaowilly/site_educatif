<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\User;
use App\Repository\BlogRepository;
use App\Repository\PageRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     */
    public function blog(PageRepository $repository, $id) // recupere le repositoy pour faire de la requette SQL
    {
        $pages = $repository->FindByBlogId($id);
        return $this->render('default/blog.html.twig', [
            'pages'=>$pages // les clef et la valeur
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



}
