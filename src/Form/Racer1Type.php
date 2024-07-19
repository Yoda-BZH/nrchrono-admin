<?php

namespace App\Form;

use App\Entity\Racer;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class Racer1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname')
            ->add('lastname', TextType::class, ['required' => false, 'empty_data' => ''])
            ->add('nickname')
            ->add('timingMin', Timetype::class, ['widget' => 'single_text', 'input_format' => 'i:s', 'html5' => false])
            ->add('timingMax', Timetype::class, ['widget' => 'single_text', 'input_format' => 'i:s', 'html5' => false])
            ->add('timingAvg', Timetype::class, ['widget' => 'single_text', 'input_format' => 'i:s', 'html5' => false])
            ->add('position')
            ->add('paused')
            ->add('team', EntityType::class, [
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
