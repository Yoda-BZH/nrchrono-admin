<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Timing;
use App\Entity\Racer;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class TimingFixType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('timing',  TimeType::class, array(
                'with_seconds' => true,
            ))
            ->add('racer', EntityType::class, array(
                'choice_label' => 'nickname',
                'class'    => Racer::class,
                'group_by' => 'team',
            ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Timing',
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
