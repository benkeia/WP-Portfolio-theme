# Page About - Documentation

## 📖 Vue d'ensemble

La page About est une page personnalisable avec animation de révélation au scroll, conçue pour présenter votre profil professionnel de manière moderne et engageante.

## 🎨 Sections disponibles

### 1. **Hero avec Animation de Révélation**
Animation inspirée de l'effet "Girl with a Pearl Earring" :
- Le nom se sépare en deux parties qui s'écartent au scroll
- Le sous-titre apparaît au centre avec un effet de scale
- L'image d'arrière-plan se scale progressivement

**Champs ACF :**
- `about_name_part_1` : Première partie du nom (ex: "Baptiste")
- `about_name_part_2` : Deuxième partie du nom (ex: "Saegaert")
- `about_subtitle` : Sous-titre (ex: "Développeur Full Stack")
- `about_hero_image` : Image de fond (1920x1080px recommandé)

### 2. **Section Introduction**
Contenu principal de la page utilisant l'éditeur WordPress natif.
Placez votre biographie et présentation ici.

### 3. **Section Compétences & Technologies**
Présente vos compétences organisées par catégories avec des cartes modernes.

**Champs ACF :**
- `show_skills_section` : Activer/désactiver la section
- `skills_title` : Titre de la section
- `skills_categories` (repeater) :
  - `category_icon` : Emoji représentant la catégorie (💻, ⚙️, 🛠️)
  - `category_name` : Nom de la catégorie (Frontend, Backend, etc.)
  - `skills_list` (repeater) :
    - `skill_name` : Nom de la compétence (React, Vue.js, etc.)

**Exemple de structure :**
```
💻 Frontend
  → React
  → Vue.js
  → Tailwind CSS
  → GSAP

⚙️ Backend
  → PHP
  → WordPress
  → Node.js
  → MySQL
```

### 4. **Section Parcours / Timeline**
Timeline chronologique de votre parcours professionnel avec une ligne verticale élégante.

**Champs ACF :**
- `show_timeline_section` : Activer/désactiver la section
- `timeline_title` : Titre de la section
- `timeline_items` (repeater) :
  - `date` : Période (ex: "2023 - Présent")
  - `title` : Titre du poste
  - `company` : Nom de l'entreprise
  - `description` : Description de la mission

**Design :**
- Alternance gauche/droite sur desktop
- Affichage linéaire sur mobile
- Points blancs sur la timeline pour chaque étape

### 5. **Section Valeurs & Approche**
Présente vos valeurs professionnelles et votre approche du travail.

**Champs ACF :**
- `show_values_section` : Activer/désactiver la section
- `values_title` : Titre de la section
- `values_items` (repeater) :
  - `value_icon` : Emoji représentant la valeur
  - `value_title` : Titre de la valeur
  - `value_description` : Description

**Exemple de valeurs :**
```
🎯 Qualité du code
💡 Innovation constante
🤝 Collaboration
🚀 Performance
```

### 6. **Section CTA Contact**
Appel à l'action pour encourager la prise de contact.

**Champs ACF :**
- `show_cta_section` : Activer/désactiver la section
- `cta_title` : Titre principal
- `cta_description` : Texte de description
- `cta_primary_button_text` : Texte du bouton principal
- `cta_primary_button_link` : Lien du bouton principal
- `cta_secondary_button_text` : Texte du bouton secondaire
- `cta_secondary_button_link` : Lien du bouton secondaire

### 7. **Sections Additionnelles**
Champ repeater flexible pour ajouter des sections personnalisées.

**Champs ACF :**
- `about_additional_sections` (repeater) :
  - `section_title` : Titre de la section
  - `section_content` : Contenu (éditeur WYSIWYG)

## 🚀 Installation

### 1. Importer les champs ACF

1. Allez dans **ACF → Outils** dans l'admin WordPress
2. Cliquez sur **Importer**
3. Sélectionnez le fichier `acf-about-page.json`
4. Cliquez sur **Importer**

### 2. Créer la page About

1. Allez dans **Pages → Ajouter**
2. Donnez un titre à votre page (ex: "À propos")
3. Dans **Attributs de page → Modèle**, sélectionnez **About Page**
4. Publiez la page

### 3. Configurer le contenu

1. Remplissez les champs ACF selon vos besoins
2. Activez/désactivez les sections avec les toggles
3. Ajoutez votre contenu principal dans l'éditeur WordPress
4. Enregistrez les modifications

## 🎨 Design & Style

### Couleurs utilisées
- Background principal : `bg-neutral-900` (noir/gris très foncé)
- Texte principal : `text-white`
- Texte secondaire : `text-neutral-300`
- Texte tertiaire : `text-neutral-400`
- Bordures : `border-neutral-700`, `border-neutral-800`
- Cards : `bg-neutral-800/50` avec backdrop blur

### Responsive
- **Desktop** : Layout en grille, timeline alternée
- **Tablet** : Grilles adaptées (2 colonnes)
- **Mobile** : Layout en colonne simple, timeline linéaire

### Animations
- Animation de révélation au scroll (GSAP + ScrollTrigger)
- Smooth scroll avec Lenis
- Transitions hover sur les cards
- Effets de scale et opacity

## 💡 Conseils d'utilisation

### Images
- **Hero image** : Utilisez une image haute qualité (1920x1080px minimum)
- Format recommandé : JPG ou WebP pour de meilleures performances
- Optimisez vos images avant upload (< 500KB si possible)

### Contenu
- **Biographie** : 2-3 paragraphes maximum dans l'introduction
- **Compétences** : Limitez à 5-8 skills par catégorie
- **Timeline** : 4-6 entrées maximum pour une lecture facile
- **Valeurs** : 4 valeurs principales (multiples de 2 pour le grid)

### Performance
- L'animation utilise CSS custom properties pour optimiser les performances
- GPU acceleration activée avec `will-change` et `backface-visibility`
- Smooth scroll géré par Lenis

### Personnalisation
- Tous les textes par défaut peuvent être modifiés via les champs ACF
- Si ACF n'est pas configuré, des valeurs par défaut s'affichent
- Les sections peuvent être activées/désactivées individuellement

## 🔧 Modifications avancées

### Changer les couleurs
Modifiez les classes Tailwind dans [page-about.php](page-about.php):
```php
// Exemple : changer la couleur de fond
bg-neutral-900 → bg-slate-900
```

### Modifier l'animation
Le code d'animation se trouve dans :
- JavaScript : [resources/js/about-reveal.js](resources/js/about-reveal.js)
- CSS : [resources/css/about-reveal.css](resources/css/about-reveal.css)

### Ajouter une nouvelle section
1. Ajoutez le HTML dans `page-about.php`
2. Créez les champs ACF correspondants
3. Ajoutez un toggle pour activer/désactiver

## 📱 Compatibilité

- ✅ Chrome, Firefox, Safari, Edge (dernières versions)
- ✅ Responsive (mobile, tablet, desktop)
- ✅ Optimisé pour les performances
- ✅ Accessible (sémantique HTML correcte)

## 🐛 Troubleshooting

### L'animation ne fonctionne pas
- Vérifiez que GSAP et Lenis sont bien chargés
- Vérifiez la console pour les erreurs JavaScript
- Assurez-vous que l'ID `about-reveal` est présent

### Les champs ACF n'apparaissent pas
- Vérifiez que le template "About Page" est bien sélectionné
- Réimportez le fichier JSON ACF
- Videz le cache si vous utilisez un plugin de cache

### Les styles ne s'appliquent pas
- Vérifiez que Tailwind CSS est bien compilé
- Relancez le build : `npm run dev` ou `npm run build`
- Videz le cache du navigateur

## 📞 Support

Pour toute question ou suggestion d'amélioration, n'hésitez pas à créer une issue ou à me contacter.

---

**Version** : 1.0  
**Dernière mise à jour** : Février 2026  
**Auteur** : Baptiste Saegaert
