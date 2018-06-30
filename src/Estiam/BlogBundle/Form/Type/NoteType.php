<?php

namespace Estiam\BlogBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Estiam\BlogBundle\Entity\Commentary;
use Estiam\BlogBundle\Entity\Note;
use Estiam\BlogBundle\Entity\Post;
use Estiam\BlogBundle\Form\EntityToNumberTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * JustAFormType constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('note', ChoiceType::class, array(
                'choices' => array(
                    '0' => 0,
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ),
                'attr' => array(
                    'class' => 'custom-form form-control'
                ),
                'label' => false
            ))
            ->add('id_author', HiddenType::class)
            ->add('commentary', HiddenType::class)
            ->add('post', HiddenType::class);
        $builder
            ->get('commentary')
            ->addModelTransformer(new EntityToNumberTransformer(
                $this->getObjectManager(),
                Commentary::class,
                'id'
            ));
        $builder->get('post')
            ->addModelTransformer(new EntityToNumberTransformer(
                $this->getObjectManager(),
                Post::class,
                'id'
            ))
            ->getForm();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Note::class,
        ));
    }
}