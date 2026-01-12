<?php

namespace App\Form;

use App\Entity\Category; // J'ai mis la Majuscule ici (Important !)
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('price')
            ->add('image')
            ->add('description')
            ->add('category', EntityType::class, [
                'class' => Category::class, // Majuscule ici aussi
                'choice_label' => 'name',   // <--- C'EST LA CORRECTION MAGIQUE (id -> name)
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
