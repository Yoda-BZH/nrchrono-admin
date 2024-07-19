<?php

namespace App\Form;

use App\Entity\Racer;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class RacerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname')
            ->add('nickname')
            ->add('timingMin', Datetype::class, ['widget' => 'simple_text', 'format' => 'i:s'])
            ->add('timingMax', Datetype::class, ['widget' => 'simple_text', 'format' => 'i:s'])
            ->add('timingAvg', Datetype::class, ['widget' => 'simple_text', 'format' => 'i:s'])
            ->add('position')
            ->add('paused')
            ->add('idTeam', EntityType::class, [
                'class' => Team::class,
'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Racer::class,
        ]);
    }
}
