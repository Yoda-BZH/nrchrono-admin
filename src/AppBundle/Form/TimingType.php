<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use AppBundle\Entity\Timing;

class TimingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timing', 'datetime', array(
                'with_seconds' => true,
            ))
            ->add('createdAt', 'datetime', array(
                'with_seconds' => true,
            ))
            ->add('clock', 'datetime', array(
                'with_seconds' => true,
            ))
            ->add('isRelay')
            ->add('idRacer', 'entity', array(
                'property' => 'nickname',
                'class'    => 'AppBundle\Entity\Racer',
                'group_by' => 'timing.idRacer.idTeam',
            ))
            ->add('type', 'choice', array(
                'choices' => array(
                    Timing::AUTOMATIC => 'Automatique',
                    Timing::MANUAL => 'Manual',
                    Timing::PREDICTION => 'Prediction',
                ),
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
