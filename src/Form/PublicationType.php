<?php

namespace App\Form;

use App\Entity\Publication;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                "attr" => [ "placeholder" => "Une pensée ?"]
            ])
            ->add('message', TextareaType::class, [
                'attr' => array('rows' => '4',
                                'maxlength' =>'500',
                                'style' => 'resize:none',
                                'placeholder' => 'Partagez-la.')
            ])
            ->add('publier', SubmitType::class, [
                'attr' => ["class" => "w-100 btn btn-primary"]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
