<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RankingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('position')
            ->add('createdAt')
            ->add('time')
            ->add('tour')
            //->add('ecart')
            ->add('distance')
            ->add('speed')
            ->add('bestlap')
            ->add('poscat')
            ->add('team')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Ranking'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_ranking';
    }
}
