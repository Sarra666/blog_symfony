<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function __construct(
        private readonly Security $security
    ){

    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
            //On récupère l'utilisateur qui va être modifié par le formulaire
            $user = $event->getData();
            // On récupère l'objet du formulaire
            $form= $event->getForm();
            //On récupère l'uutilisateur connecté actuellement
            $userAuth = $this->security->getUser();

            if($user == $userAuth){
                $form
                    ->add('email', EmailType::class, [
                        'label' => 'Email:',
                        'attr' => [
                            'placeholder' => 'john@exemple.com'
                        ],
                        'required' => true,
                    ])
                    ->add('firstName', TextType::class, [
                        'label' => 'Prénom:',
                        'required' => true,
                        'attr' => [
                            'placeholder' => 'John',
                        ]
                    ])
                    ->add('lastName', TextType::class, [
                        'label' => 'Nom:',
                        'required' => true,
                        'attr' => [
                            'placeholder' => 'Doe',
                        ],
                    ]);
            }

            if($this->security->isGranted('ROLE_ADMIN')){
                $form->add('roles', ChoiceType::class, [
                    'label' =>'Roles :',
                    'required' => false,
                    'choices' => [
                        'Utilisateur' => 'ROLE_USER',
                        'Editeur'=> 'ROLE_EDITOR',
                        'Administrateur' => 'ROLE_ADMIN',

                    ],
                    'expanded'=> true,
                    'multiple' =>true,
                ]);
            }
        });
       // $builder
       //     ->add('email')
       //     ->add('roles')
       //     ->add('password')
       //     ->add('firstName')
       //     ->add('lastName')
       // ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
