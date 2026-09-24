# Planning du projet — Clean Elephant (E5 BTS SIO)

> ⚠️ Dates indicatives à ajuster une fois la date de soutenance connue. Le planning couvre au minimum M+1 comme demandé par le référentiel.

## Vue d'ensemble (diagramme de Gantt)

```mermaid
gantt
    title Planning projet E5 - Clean Elephant
    dateFormat  YYYY-MM-DD
    axisFormat  %d/%m
    todayMarker on

    section 1. Idéation
    Cahier des charges              :done, 2026-09-01, 2026-09-24
    Repository GitHub               :done, 2026-09-24, 1d
    Ajout cahier des charges au repo:done, 2026-09-24, 1d
    Architecture du SI              :active, 2026-09-25, 8d
    Schéma réseau                   :active, 2026-09-25, 8d
    Planning du projet              :active, 2026-09-24, 3d

    section 2. Design (Figma)
    Moodboard                       :2026-10-05, 5d
    Charte graphique                :2026-10-10, 5d
    Wireframe                       :2026-10-15, 5d
    Maquettes graphiques            :2026-10-20, 9d
    Prototype interactif            :2026-10-29, 5d
    Test utilisateur du prototype   :2026-11-03, 3d

    section 3. Développement
    Site vitrine                    :2026-11-06, 15d
    Application devis (BDD + email) :2026-11-21, 11d

    section 4. Infrastructure
    Active Directory                :2026-12-02, 10d
    Réseau (VLAN / routage)         :2026-12-02, 12d
    Serveur GLPI                    :2026-12-12, 7d
    Supervision Checkmk             :2026-12-19, 6d

    section 5. Sécurité & continuité
    Sécurité (droits, mdp, RGPD)    :2027-01-05, 10d
    Sauvegarde & restauration       :2027-01-15, 7d
    Test de reprise documenté       :2027-01-22, 3d

    section 6. Validation
    Tests & recette                 :2027-01-25, 10d
    Mise en production (VPS)        :2027-02-04, 5d
    Documentation finale            :2027-02-09, 10d
    Accompagnement des utilisateurs :2027-02-19, 5d

    section 7. Soutenance
    Préparation soutenance          :2027-03-01, 15d
```

## Jalons clés

| Jalon | Date estimée |
|---|---|
| Livrable 1 (Idéation) | Fin septembre 2026 |
| Maquette Figma validée | Fin octobre 2026 |
| Site vitrine développé | Mi-novembre 2026 |
| Infrastructure (AD/GLPI/Checkmk) opérationnelle | Fin décembre 2026 |
| Sauvegarde + test de reprise documenté | Fin janvier 2027 |
| Mise en production | Début février 2027 |
| Documentation finale | Mi-février 2027 |
| Soutenance | Mars 2027 |

## Équipe

- **Harouna Diakité**
- **Noah Fine**
