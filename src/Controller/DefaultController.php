<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\Page;
use App\Entity\User;
use App\Entity\Person;
use App\Repository\BlogRepository;
use App\Repository\PageRepository;
use App\Repository\PersonRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DefaultController extends AbstractController
{
    /**
     * @return Response
     * @Route("/", name="home")
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
           ->add('content', TextareaType::class, [
               'attr'=>[
                 'class'=> 'ckeditor'
                ]
              ])
           ->add('photo', FileType::class)
           ->add('preview', TextType::class)
           ->getForm();

       $form->handleRequest($request); // a chaque methode il y a toujours un retour ...

        if ($form->isSubmitted() && $form->isValid()) {
            $page->setBlog($blog); // setter le blog
            $page->setUser($this->getUser()); // setter l'utilisateur

            $file = $page->getPhoto();
            $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
            $page->setPhoto($fileName);

            $manager->persist($page); // prepare pour envoyer a la bdd
            $manager->flush(); // et je l'envoie

            try {
                $file->move(
                    $this->getParameter('photo'),
                    $fileName
                );
            } catch (FileException $exception) {
                echo $exception->getCode() . ': ' . $exception->getMessage();
            }

            return $this->redirectToRoute('blog_show', [ // ont redirige vers la liste des pages
                'id' => $id //on redirige dans le blog
            ]);
        }

        return $this->render('default/create_page.html.twig', [
            'form_create'=>$form->createView() // rendu du formulaire
        ]);
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
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
            ->add('email', EmailType::class, [
              'constraints' => [
                new Assert\Email([
                'message' => 'Votre email "{{ value }}" est invalide.',
                'checkMX' => true,

              ])
            ],
            'translation_domain' => 'messages',
            'label_format' => 'register.%name%',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe doit être le même.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'register.password'],
                'second_options' => ['label' => 'register.repeat_password'],
                'translation_domain' => 'messages',
                //'label_format' => 'register.%name%',
            ])
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
     * @Route("/contact", name="contact", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     *
     * @return Response
     */
    public function contact(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createFormBuilder([])
            ->add('nom', TextType::class, [
                'translation_domain' => 'messages',
                'label_format' => 'contact.%name%',
            ])
            ->add('mail', EmailType::class, [
                'translation_domain' => 'messages',
                'label_format' => 'contact.%name%',
            ])
            ->add('phone', TelType::class, [
                'translation_domain' => 'messages',
                'label_format' => 'contact.%name%',
            ])
            ->add('message', TextareaType::class, [
                'translation_domain'=> 'messages',
                'label_format' => 'contact.%name%',

            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $subject = 'Prise de contact';
            $destinataire = 'anisidrenmouche@gmail.com';
            $message = $form['message']->getData();

            $message = (new \Swift_Message($subject))
                ->setFrom($form['mail']->getData())
                ->setTo($destinataire)
                ->setBody($message);

            $mailer->send($message);

            return $this->redirectToRoute('home');

        }

        return $this->render('default/contact.html.twig', [
            'contact_form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account", name="account", methods={"GET", "POST"})
     */
    public function editAccount(
      Request $request,
      EntityManagerInterface $manager,
      PersonRepository $repository
      )
      {
        if (is_null($this->getUser())) {
          throw new \Exception("Vous devez vous connecter", 1);
        }
        $person = $repository->findOneBy(['user' => $this->getUser()]);

        if ($person->getImage()) {
            $person->setImage(
                new File($this->getParameter('photo') . '/' . $person->getImage())
            );
        }

        $form = $this->createFormBuilder($person)
            ->add('FirstName', TextType::class, [
                'translation_domain' => 'messages',
                'label_format' => 'account.%name%',
                'required' => false,
            ])
            ->add('LastName', TextType::class, [
                'translation_domain' => 'messages',
                'label_format' => 'account.%name%',
                'required' => false,
            ])
            ->add('image', FileType::class, [
                'translation_domain' => 'messages',
                'label_format' => 'account.%name%',
                'required' => false,
            ])
            ->add('description', TextAreaType::class, [
                'translation_domain' => 'messages',
                'label_format' => 'account.%name%',
                'required' => false,
                    'attr'=>[
                      'class'=> 'ckeditor'
                    ]
            ])
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
              if($form['image']->getData()){
                $file = $form['image']->getData();
                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                $person->setImage($fileName);

                try {
                    $file->move(
                        $this->getParameter('photo'),
                        $fileName
                    );
                } catch (FileException $exception) {
                    echo $exception->getCode() . ': ' . $exception->getMessage();
                }
              }

                $manager->persist($person); // prepare pour envoyer a la bdd
                $manager->flush();

                return $this->redirectToRoute('home');

            }

            return $this->render('default/account.html.twig', [
                'account_form' => $form->createView(),
            ]);
      }

      public function  getProfile(PersonRepository $repository)
      {
          $person = $repository->findOneBy(['user'=>$this->getUser()]);
          return $this->render(
            'default/person.html.twig',
            ['person' => $person]
        );

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
