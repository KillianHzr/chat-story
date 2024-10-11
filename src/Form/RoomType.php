<?php

namespace App\Form;

use App\Entity\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('timer')
            ->add('createdAt', DateTimeType::class, [
                'widget' => 'single_text',
                'disabled' => true,
            ])
            ->add('updatedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'disabled' => true,
            ])
            ->add('userNumber', IntegerType::class, [
                'disabled' => true,
            ])
            ->add('isActive');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
        ]);
    }
}
