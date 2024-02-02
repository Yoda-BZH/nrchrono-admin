<?php

namespace App\Form;

use App\Entity\Pause;
use App\Entity\Racer;
use App\Entity\RacerPause;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use App\Repository\RacerRepository;
use Doctrine\ORM\QueryBuilder;

class RacerPauseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pause', EntityType::class, [
                'class' => Pause::class,
                'choice_label' => 'name',
            ])
            ->add('porder')
            ->add('racer', EntityType::class, [
                'class' => Racer::class,
                'choice_label' => 'nickname',
                'query_builder' => function (RacerRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('r')
                        ->leftJoin('r.team', 't')
                        ->orderBy('r.firstname', 'ASC');
                },
                'group_by' => function($choice, $key, $value)
                {
                    return $choice->getTeam()->getName();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RacerPause::class,
        ]);
    }
}
