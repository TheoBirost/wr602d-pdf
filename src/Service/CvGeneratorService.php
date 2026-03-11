<?php

namespace App\Service;

class CvGeneratorService
{
    public function generate(array $data): string
    {
        $name = $data['name'] ?? 'le candidat';
        $contact = $data['contact'] ?? 'non spécifiées';
        $summary = $data['summary'] ?? 'non spécifié';
        $experience = $data['experience'] ?? 'non spécifiée';
        $education = $data['education'] ?? 'non spécifiée';
        $skills = $data['skills'] ?? 'non spécifiées';
        $languages = $data['languages'] ?? 'non spécifiées';
        $interests = $data['interests'] ?? 'non spécifiés';

        $prompt = "
        **Objectif :** Générer un CV professionnel en français, bien structuré et formaté en Markdown.

        **Instructions :**
        1.  Crée un CV clair et concis pour **{$name}**.
        2.  Utilise un style de mise en page professionnel et moderne.
        3.  Organise le CV en sections distinctes :
            *   En-tête (Nom, Coordonnées)
            *   Résumé de profil
            *   Expérience Professionnelle
            *   Formation
            *   Compétences
            *   Langues
            *   Centres d'intérêt (si pertinent)
        4.  Pour chaque expérience et formation, assure-toi que le format est cohérent (par exemple : **Poste**, *Entreprise*, Date - *Lieu*).
        5.  Le résultat final doit être uniquement le CV au format Markdown, sans aucun texte ou commentaire supplémentaire avant ou après.

        ---

        **Informations à utiliser :**

        *   **Nom complet :** {$name}
        *   **Coordonnées :** {$contact}
        *   **Résumé de profil :**
            {$summary}
        *   **Expérience professionnelle :**
            {$experience}
        *   **Formation :**
            {$education}
        *   **Compétences :**
            {$skills}
        *   **Langues :**
            {$languages}
        *   **Centres d'intérêt :**
            {$interests}
        ";

        return $prompt;
    }
}
