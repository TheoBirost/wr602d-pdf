<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class SplitPdfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', FileType::class, [
                'label' => 'Sélectionnez le PDF à diviser',
                'attr' => [
                    'accept' => 'application/pdf',
                ],
                'constraints' => [
                    new NotBlank(),
                    new File([
                        'maxSize' => '10240k',
                        'mimeTypes' => ['application/pdf', 'application/x-pdf'],
                        'mimeTypesMessage' => 'Veuillez sélectionner un fichier PDF valide.',
                    ])
                ]
            ])
            ->add('ranges', TextType::class, [
                'label' => 'Pages à extraire',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: 1-3, 5, 8-10',
                ],
                'constraints' => [
                    new NotBlank(),
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Diviser le PDF',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
