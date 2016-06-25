<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RacerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array(
                'required' => false,
            ))
            ->add('lastname', 'text', array(
                'required' => false,
            ))
            ->add('nickname', 'text', array(
                'required' => false,
            ))
            ->add('paused')
            ->add('timingMin')
            ->add('timingMax')
            ->add('timingAvg')
            ->add('position')
            ->add('idTeam')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Racer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_racer';
    }
}
