<?php

namespace Estiam\BlogBundle\Form\Type;

use Estiam\BlogBundle\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => false
            ))
            ->add('description', TextareaType::class, array(
                'attr' => array(
                    'class' => 'form-control'
                ),
                'label' => false
            ))
            ->add('state', ChoiceType::class, array(
                'choices' => array(
                    'Brouillon' => 0,
                    'Publié' => 1,
                    'Terminé' => 2
                ),
                'attr' => array(
                    'class' => 'custom-select form-control'
                ),
                'label' => false
            ))
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Post::class,
        ));
    }
}