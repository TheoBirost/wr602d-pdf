<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MarkdownToPdfType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('markdown', TextareaType::class, [
                'label' => 'Votre texte en Markdown',
                'required' => true,
                'attr' => [
                    'rows' => 15,
                    'placeholder' => '# Exemple de titre' . "\n\n" . '- Une liste' . "\n" . '- D\'éléments',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Convertir en PDF',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
