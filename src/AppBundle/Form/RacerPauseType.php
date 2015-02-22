<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RacerPauseType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('porder')
            ->add('idRacer', 'entity', array(
                'property' => 'nickname',
                'class'    => 'AppBundle\Entity\Racer',
            ))
            ->add('idPause', 'entity', array(
                'property' => 'name',
                'class'    => 'AppBundle\Entity\Pause',
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\RacerPause'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_racerpause';
    }
}
