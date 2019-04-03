<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\BlogRepository;
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
    public function blog()
    {
        return $this->render('default/blog.html.twig');
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
}
