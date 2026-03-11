<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CvGeneratorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom complet',
                'attr' => ['placeholder' => 'Ex: Jean Dupont']
            ])
            ->add('contact', TextType::class, [
                'label' => 'Coordonnées',
                'attr' => ['placeholder' => 'Ex: jean.dupont@email.com, 0612345678, Paris']
            ])
            ->add('summary', TextareaType::class, [
                'label' => 'Résumé de profil',
                'attr' => ['rows' => 3, 'placeholder' => 'Rédigez une courte phrase d\'accroche qui résume votre profil et votre objectif.']
            ])
            ->add('experience', TextareaType::class, [
                'label' => 'Expérience professionnelle',
                'attr' => ['rows' => 6, 'placeholder' => 'Listez vos postes, entreprises, dates et missions principales...']
            ])
            ->add('education', TextareaType::class, [
                'label' => 'Formation',
                'attr' => ['rows' => 4, 'placeholder' => 'Indiquez vos diplômes, écoles, et dates d\'obtention...']
            ])
            ->add('skills', TextareaType::class, [
                'label' => 'Compétences',
                'attr' => ['rows' => 5, 'placeholder' => 'Listez vos compétences techniques et humaines. Ex: Gestion de projet, Python, Suite Adobe, Anglais C1...']
            ])
            ->add('languages', TextType::class, [
                'label' => 'Langues',
                'attr' => ['placeholder' => 'Ex: Français (Natif), Anglais (C1), Espagnol (B2)'],
                'required' => false,
            ])
            ->add('interests', TextType::class, [
                'label' => 'Centres d\'intérêt',
                'attr' => ['placeholder' => 'Ex: Photographie, Randonnée, Bénévolat'],
                'required' => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Générer le prompt pour mon CV']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
