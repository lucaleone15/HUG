<?php

return [
    // ─── Existants ──────────────────────────────────────────
    'generated_at'       => 'Généré le',
    'report_title'       => 'Rapport de participation',
    'report_subtitle'    => 'Campagne Don du Sang 2026',
    'section_company'    => 'Entreprise',
    'contact_name'       => 'Responsable',
    'contact_email'      => 'Email',
    'employee_count'     => 'Effectif',
    'employees'          => 'employés',
    'section_participation' => 'Participation',
    'quiz_started'       => 'Quiz démarrés',
    'quiz_completed'     => 'Quiz complétés',
    'eligible'           => 'Éligibles',
    'rdv_clicked'        => 'RDV cliqués',
    'section_rates'      => 'Taux clés',
    'participation_rate' => 'Taux de participation',
    'eligibility_rate'   => "Taux d'éligibilité",
    'conversion_rate'    => 'Taux de conversion',
    'avg_duration'       => 'Durée moyenne du quiz',
    'abandon_by_question'=> 'Abandons par question',
    'abandons'           => 'abandons',

    // ─── KPI band ───────────────────────────────────────────
    'kpi_start_point'    => '100% · point de départ',
    'kpi_completion'     => 'de complétion',
    'kpi_eligibility_sub'=> "d'éligibilité",
    'kpi_conversion_sub' => 'de conversion',
    'kpi_e2e'            => 'Bout-en-bout',

    // ─── Badges & performance ────────────────────────────────
    'badge_excellent'    => 'Excellent',
    'badge_bon'          => 'Bon',
    'badge_moyen'        => 'Moyen',
    'badge_faible'       => 'Faible',
    'perf_excellent'     => 'Excellente',
    'perf_bon'           => 'Bonne',
    'perf_moyen'         => 'Modérée',
    'perf_faible'        => 'Faible',
    'performance'        => 'Performance',

    // ─── Durée ──────────────────────────────────────────────
    'dur_very_fast'      => 'Très rapide — réviser la pertinence des questions',
    'dur_concise'        => 'Concis — expérience utilisateur optimale',
    'dur_optimal'        => 'Optimal — participants très impliqués dans la démarche',
    'dur_long'           => "Long — risque d'abandon en cours de route",

    // ─── Recommandations taux ────────────────────────────────
    'reco_part_low'      => 'Renforcer la communication interne',
    'reco_part_med'      => 'Bon taux — maintenir la visibilité',
    'reco_part_high'     => 'Excellent engagement des collaborateurs',
    'reco_elig_low'      => 'Cibler des profils plus compatibles au don',
    'reco_elig_med'      => 'Conforme à la moyenne nationale (60-70%)',
    'reco_elig_high'     => 'Excellente adéquation des participants',
    'reco_conv_low'      => 'Optimiser le parcours CTA post-quiz',
    'reco_conv_med'      => 'Bonne conversion — optimiser le rappel',
    'reco_conv_high'     => 'Conversion remarquable — modèle à reproduire',

    // ─── Diagnostics ─────────────────────────────────────────
    'section_diagnostics'   => 'Diagnostics',
    'diag_part_low_t'       => 'Participation insuffisante (:pct%)',
    'diag_part_low_x'       => ':started sur :emp collaborateurs. Une campagne ciblée peut tripler ce taux. Objectif recommandé : 25%.',
    'diag_part_ok_t'        => 'Participation satisfaisante (:pct%)',
    'diag_part_ok_x'        => ':started collaborateurs engagés. Maintenir la visibilité du programme pour fidéliser les participants.',
    'diag_elig_ok_t'        => 'Excellente éligibilité (:pct%)',
    'diag_elig_ok_x'        => 'Dépasse la moyenne nationale (60-70%). Population active en très bonne santé.',
    'diag_elig_low_t'       => "Éligibilité à améliorer (:pct%)",
    'diag_elig_low_x'       => ':nonElig% non éligibles. Envisager une communication préventive sur les critères de don.',
    'diag_conv_ok_t'        => 'Conversion RDV exemplaire (:pct%)',
    'diag_conv_ok_x'        => ':rdv RDV sur :eligible éligibles. Parmi les meilleurs résultats de la campagne SANG-26.',
    'diag_score_t'          => 'Score global campagne : :score%',
    'diag_score_x'          => 'Performance :perf — moyenne des trois taux clés. Marge sur la conversion RDV (:conv% vs 70% cible).',

    // ─── Recommandations section ─────────────────────────────
    'section_reco'          => 'Recommandations prioritaires',
    'reco_session_title'    => 'Actions pour la prochaine session',
    'reco_1_low'            => "Lancer une campagne interne : affichage, email ciblé, présentation en réunion (cible 25%).",
    'reco_1_ok'             => 'Maintenir la visibilité et partager les résultats pour fidéliser les participants.',
    'reco_2'                => 'Revoir la Q:q (:n abandons) — reformuler ou déplacer en fin de parcours.',
    'reco_2_default'        => 'Revoir la question principale — reformuler ou déplacer en fin de parcours.',
    'reco_3'                => 'Fixer un objectif de :part participants pour ~:elig éligibles et ~:rdv RDV.',

    // ─── Objectifs ───────────────────────────────────────────
    'section_objectives'    => 'Objectifs campagne 2027',
    'obj_indicator'         => 'Indicateur',
    'obj_current'           => 'Actuel',
    'obj_target'            => 'Objectif 2027',
    'obj_participation'     => 'Participation',
    'obj_participants'      => 'Participants',
    'obj_eligible_pl'       => 'Éligibles',
    'obj_rdv'               => 'RDV',
    'obj_note'              => 'Objectifs basés sur 25% de participation et les taux observés. Estimation non contractuelle.',

    // ─── Entonnoir ───────────────────────────────────────────
    'funnel_completion'     => 'Complétion',
    'funnel_eligibility'    => 'Éligibilité',
    'funnel_conversion'     => 'Conversion RDV',
    'funnel_pct_of_total'   => '% du total',

    // ─── Comportement ────────────────────────────────────────
    'section_behavior_dur'  => 'Comportement — durée moyenne',
    'abandon_questions'     => 'questions',
    'abandon_most_blocked'  => 'question la plus bloquante',
    'abandon_reco_early'    => "Abandon précoce — revoir l'accroche initiale",
    'abandon_reco_late'     => 'Abandon tardif — simplifier la conclusion',
    'abandon_reco_mid'      => 'Abandon à mi-parcours — clarifier les questions centrales',
    'section_behavior_analysis' => 'Analyse comportementale',
    'behavior_interp_title' => 'Interprétation des abandons',
    'behavior_interp_early' => "Q:q concentre :n des :total abandons (:pct%). Abandon précoce — question perçue comme trop intrusive.",
    'behavior_interp_hint_early' => 'Reformuler en question générale ou rassurer sur la confidentialité.',
    'behavior_interp_other' => 'Abandons répartis sur :q questions — quiz bien structuré avec quelques frictions.',
    'behavior_interp_hint_other' => 'Simplifier les questions intermédiaires qui concentrent les abandons.',

    // ─── Benchmark ───────────────────────────────────────────
    'section_benchmark'     => 'Benchmark sectoriel',
    'bench_sector_avg'      => 'Moy. secteur',
    'bench_top'             => 'Top performer',
    'bench_note'            => "Données benchmark basées sur la cohorte d'entreprises partenaires SANG-26 (n=24 entreprises genevoises).",

    // ─── Méthodologie ────────────────────────────────────────
    'methodology_title'     => 'Méthodologie',
    'methodology_text'      => "Le quiz d'éligibilité SANG-26 couvre les critères médicaux de la Croix-Rouge suisse : âge, poids, santé générale, voyages récents et traitements en cours.",
    'methodology_note'      => "Les taux sont calculés sur les soumissions complètes (:sub pour :name). Les abandons en cours ne sont pas comptabilisés dans le taux d'éligibilité.",

    // ─── Synthèse (col. sombre) ──────────────────────────────
    'synth_campaign'        => 'Synthèse campagne',
    'synth_soumissions'     => 'Soumissions',
    'synth_rdv_taken'       => 'RDV pris',

    // ─── Simulation ──────────────────────────────────────────
    'sim_title'             => 'Simulation — 25% de participation',
    'sim_participants'      => 'participants',
    'sim_eligible_lbl'      => 'éligibles',
    'sim_gain'              => '+:gain éligibles · +:rdvGain RDV supplémentaires estimés',
    'sim_note'              => "Estimation basée sur les taux observés (:elig% éligibilité · :conv% conversion)",

    // ─── Impact ──────────────────────────────────────────────
    'impact_title'          => 'Impact don du sang',
    'impact_sub'            => 'vies potentiellement sauvées grâce aux :rdv donneurs de cette campagne',
    'impact_eq'             => '1 donneur peut sauver jusqu\'à 3 vies · :rdv × 3 = :lives',

    // ─── Ratio ───────────────────────────────────────────────
    'ratio_title'           => "Ratio d'éligibilité",
    'ratio_eligible'        => 'éligibles',
    'ratio_submitted'       => 'soumis',

    // ─── Le saviez-vous & Contact ────────────────────────────
    'didyouknow_title'      => 'Le saviez-vous ?',
    'didyouknow_1'          => "Une poche de sang se conserve 42 jours. Les besoins sont constants toute l'année.",
    'didyouknow_2'          => 'Chaque don peut servir jusqu\'à 3 receveurs selon le fractionnement (plasma, plaquettes, globules rouges).',
    'contact_title'         => 'Contact campagne',
    'contact_org'           => 'Centre de Transfusion Sanguine HUG',
    'contact_email_label'   => 'Email',
    'contact_phone_label'   => 'Tél. direct',
    'contact_email_val'     => 'cts@hug.ch',
    'contact_phone_val'     => '+41 22 372 61 00',

    // ─── Footer ──────────────────────────────────────────────
    'footer_hug'            => 'HUG – Hôpitaux Universitaires de Genève · Centre de Transfusion Sanguine',
    'footer_site'           => 'donnez-votre-sang.ch',
    'vbl_part' => 'Partic.',
    'vbl_elig' => 'Éligib.',
    'vbl_conv' => 'Conv.',
    'vbl_e2e'  => 'E2E',
];
