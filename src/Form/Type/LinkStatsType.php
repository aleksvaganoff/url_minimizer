<?php

namespace App\Form\Type;

use App\Entity\ShortLink;
use App\Repository\ShortLinkRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class LinkStatsType extends AbstractType
{

    private $shortLinkReposytory;

    public function __construct(ShortLinkRepository $shortLinkRepository)
    {
        $this->shortLinkReposytory = $shortLinkRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setMethod('GET')
            ->add('short_url', TextType::class, [
                'label' => 'Short Url',
                'constraints' => [
                    new Url(),
                    new NotBlank(),
                    new Callback(['callback' => function ($value, ExecutionContextInterface $context) {
                        $hash = substr($value, -6);
                        $shortLink = $this->shortLinkReposytory->findActiveByHash($hash);
                        if (!$shortLink) {
                            $context->buildViolation("The URL doesn't exist")
                                ->atPath('short_url')
                                ->addViolation();
                        }
                    }])
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Show clicks'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}