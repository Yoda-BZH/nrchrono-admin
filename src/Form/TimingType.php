<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Timing;
use App\Entity\Racer;
use Symfony\Component\Form\Extension\Core\Type\TimeType;


class TimingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timing', TimeType::class, array(
                'with_seconds' => true,
            ))
            ->add('createdAt', TimeType::class, array(
                'with_seconds' => true,
            ))
            ->add('clock', TimeType::class, array(
                'with_seconds' => true,
            ))
            ->add('isRelay')
            ->add('racer', EntityType::class, array(
                'choice_label' => 'nickname',
                'class'    => Racer::class,
                #'group_by' => 'timing.racers.team',
            ))
            ->add('type', ChoiceType::class, array(
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
