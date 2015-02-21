<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timing')
            ->add('createdAt')
            ->add('isRelay')
            ->add('idRacer', 'entity', array(
                'property' => 'nickname',
                'class'    => 'AppBundle\Entity\Racer',
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Timing'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_timing';
    }
}
