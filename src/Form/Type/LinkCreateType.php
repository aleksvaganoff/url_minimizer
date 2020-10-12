<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Url;

class LinkCreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', TextType::class, [
                'label' => 'Url',
                'constraints' => [
                    new Url(),
                    new NotBlank()
                ]
            ])
            ->add('lifetime', TextType::class, [
                'label' => 'Lifetime (hours)',
                'constraints' => [
                    new Positive(),
                    new NotBlank()
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Minimize Url'
            ]);
    }
}