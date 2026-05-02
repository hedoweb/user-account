# CLAUDE.md — User Account Plugin

Ce fichier sert de référence complète pour Claude Code. Lis-le entièrement avant de commencer à coder.

---

## Contexte du projet

**Nom du plugin :** User Account
**Slug :** `user-account`
**Text domain :** `user-account`
**Version de départ :** `1.0.0`
**Auteur :** Stef

Plugin WordPress qui fournit une page "Mon compte" propre et légère pour les utilisateurs connectés, sans dépendre de WooCommerce, BuddyPress ou Ultimate Member.

**Philosophie :** simple, extensible, orienté développeur. Le plugin pose le cadre, le dev personnalise via des hooks.

---

## Profil développeur

- Développeur WordPress indépendant
- Très bonne maîtrise de PHP, HTML, CSS
- Bases en JavaScript (jQuery maîtrisé)
- Hébergement sur o2switch (CloudLinux partagé)
- Utilise Bricks Builder et ACF sur ses projets
- Développe via des plugins personnalisés, jamais via le thème

---

## Ce que fait le plugin (périmètre v1.0 — version gratuite)

### Fonctionnalités incluses

**Page Mon compte**
- Créée automatiquement à l'activation du plugin
- Shortcode `[user_account]` pour l'afficher n'importe où
- Redirection automatique vers la page de connexion si l'utilisateur n'est pas connecté
- Redirection automatique vers la page Mon compte après connexion

**Onglet Tableau de bord**
- Message de bienvenue personnalisable depuis le back-office
- Raccourcis vers des pages du site (max 3, configurables en back-office)

**Onglet Mon profil**
- Modifier prénom, nom, email, bio
- Changer son avatar (upload direct ou Gravatar)
- Changer son mot de passe
- Sauvegarde via AJAX

**Back-office (Settings > User Account)**
- Titre de la page Mon compte
- Message de bienvenue
- Gestion des raccourcis (titre + URL)
- Activer/désactiver les onglets natifs

**API pour les développeurs**
- Système d'onglets extensible via filtre `ua_tabs`
- Hooks avant/après chaque section
- Code propre, sans dépendances externes

### Fonctionnalités explicitement hors périmètre v1

- Notifications
- Messagerie privée
- Gestion d'abonnements ou paiements
- Gestion avancée des rôles
- Intégration WooCommerce

---

## Architecture des fichiers

```
user-account/
├── user-account.php              # Fichier principal, headers du plugin
├── uninstall.php                 # Nettoyage BDD à la désinstallation
├── readme.txt                    # Pour WordPress.org
│
├── includes/
│   ├── class-ua-core.php         # Initialisation, chargement des dépendances
│   ├── class-ua-page.php         # Création et détection de la page Mon compte
│   ├── class-ua-shortcode.php    # Enregistrement et rendu du shortcode
│   ├── class-ua-tabs.php         # Gestionnaire d'onglets (register, get, render)
│   ├── class-ua-profile.php      # Logique onglet profil (lecture/écriture user meta)
│   ├── class-ua-dashboard.php    # Logique onglet tableau de bord
│   └── class-ua-ajax.php         # Handlers AJAX (save profile, upload avatar)
│
├── admin/
│   ├── class-ua-admin.php        # Enregistrement menu et settings WP
│   └── views/
│       └── settings-page.php     # Template HTML de la page de réglages
│
├── public/
│   ├── views/
│   │   ├── account-wrapper.php   # Template principal (onglets + contenu)
│   │   ├── tab-dashboard.php     # Vue HTML du tableau de bord
│   │   └── tab-profile.php       # Vue HTML du formulaire profil
│   ├── css/
│   │   └── ua-public.css         # Styles front-office
│   └── js/
│       └── ua-public.js          # JS front (navigation onglets, AJAX submit)
│
└── languages/
    └── user-account.pot          # Fichier de traduction
```

---

## Constantes du plugin

```php
define( 'UA_VERSION', '1.0.0' );
define( 'UA_PATH', plugin_dir_path( __FILE__ ) );
define( 'UA_URL', plugin_dir_url( __FILE__ ) );
define( 'UA_PAGE_OPTION', 'ua_page_id' ); // option WP qui stocke l'ID de la page Mon compte
```

---

## Conventions de code

- Préfixe de toutes les classes : `UA_`
- Préfixe de toutes les fonctions globales : `ua_`
- Préfixe de toutes les options WordPress : `ua_`
- Préfixe de toutes les user meta : `ua_`
- Préfixe des hooks (actions/filtres) : `ua_`
- Standard de code : WordPress Coding Standards (WPCS)
- Échappement systématique en sortie : `esc_html()`, `esc_attr()`, `esc_url()`
- Sanitisation systématique en entrée : `sanitize_text_field()`, `sanitize_email()`, etc.
- Nonces sur tous les formulaires et requêtes AJAX
- Pas de `echo` brut sans échappement
- Commentaires en anglais dans le code

---

## Hooks disponibles (API développeur)

### Actions

```php
do_action( 'ua_before_account' );                    // Avant tout le wrapper
do_action( 'ua_after_account' );                     // Après tout le wrapper
do_action( 'ua_before_tab', $tab_id );               // Avant le contenu d'un onglet
do_action( 'ua_after_tab', $tab_id );                // Après le contenu d'un onglet
do_action( 'ua_before_save_profile', $user_id, $data ); // Avant sauvegarde profil
do_action( 'ua_after_save_profile', $user_id, $data );  // Après sauvegarde profil
```

### Filtres

```php
apply_filters( 'ua_tabs', $tabs );           // Modifier/ajouter des onglets
apply_filters( 'ua_profile_fields', $fields ); // Modifier les champs du profil
apply_filters( 'ua_redirect_url', $url );    // Modifier l'URL de redirection post-login
```

### Exemple d'ajout d'un onglet custom

```php
add_filter( 'ua_tabs', function( $tabs ) {
    $tabs['my_orders'] = array(
        'label'    => 'Mes commandes',
        'callback' => 'my_plugin_render_orders',
        'order'    => 30,
    );
    return $tabs;
});
```

---

## Système d'onglets

Les onglets sont gérés par `UA_Tabs`. Chaque onglet est un tableau associatif :

```php
array(
    'label'    => string,   // Texte affiché dans la navigation
    'callback' => callable, // Fonction ou méthode qui rend le contenu
    'order'    => int,      // Ordre d'affichage (défaut : 50)
)
```

Les onglets natifs :
- `dashboard` — order 10
- `profile` — order 20

---

## Gestion AJAX

Toutes les actions AJAX sont préfixées `ua_` et vérifiées avec un nonce `ua_nonce`.

Les variables JS sont passées via `wp_localize_script` dans l'objet `ua_vars` :
```javascript
ua_vars.ajax_url  // URL admin-ajax.php
ua_vars.nonce     // Nonce de sécurité
```

---

## Données utilisateur stockées

Toutes les données custom passent par les user meta WordPress :

| Meta key | Contenu |
|---|---|
| `ua_avatar` | URL de l'avatar uploadé |
| `ua_bio` | Biographie (texte libre) |

Les champs natifs WordPress (`first_name`, `last_name`, `user_email`) sont mis à jour via `wp_update_user()`.

---

## Options WordPress stockées

| Option key | Contenu |
|---|---|
| `ua_page_id` | ID de la page Mon compte créée à l'activation |
| `ua_welcome_message` | Message de bienvenue (back-office) |
| `ua_shortcuts` | Tableau sérialisé des raccourcis |
| `ua_active_tabs` | Tableau des onglets actifs |

---

## Ordre de développement recommandé

1. `user-account.php` — fichier principal + constantes
2. `uninstall.php` — nettoyage basique
3. `includes/class-ua-core.php` — initialisation, hooks, chargement
4. `includes/class-ua-page.php` — création page à l'activation, détection
5. `includes/class-ua-shortcode.php` + `public/views/account-wrapper.php` — afficher quelque chose
6. `includes/class-ua-tabs.php` — système d'onglets
7. `includes/class-ua-dashboard.php` + `public/views/tab-dashboard.php`
8. `includes/class-ua-profile.php` + `public/views/tab-profile.php`
9. `includes/class-ua-ajax.php` — sauvegarde profil + upload avatar
10. `admin/class-ua-admin.php` + `admin/views/settings-page.php`
11. `public/css/ua-public.css` + `public/js/ua-public.js`
12. `readme.txt` + `languages/user-account.pot`

---

## Compatibilité cible

- WordPress 6.0+
- PHP 7.4+
- Compatible avec le shortcode dans Bricks Builder
- Pas de dépendance à WooCommerce, ACF ou tout autre plugin tiers

---

## Premier prompt pour démarrer

> Je veux créer un plugin WordPress from scratch. Voici le fichier CLAUDE.md qui décrit le projet en détail, lis-le entièrement avant de commencer.
>
> Commence par créer les fichiers suivants dans cet ordre exact :
> 1. `user-account.php` — le fichier principal avec les headers du plugin et le chargement de UA_Core
> 2. `uninstall.php` — supprime les options `ua_*` et les user meta `ua_*` à la désinstallation
> 3. `includes/class-ua-core.php` — singleton, charge toutes les dépendances, enregistre les hooks principaux
> 4. `includes/class-ua-page.php` — crée la page à l'activation, stocke son ID dans `ua_page_id`, fournit une méthode statique `is_account_page()`
>
> Respecte scrupuleusement les conventions définies dans CLAUDE.md : préfixes, standards WPCS, échappement, nonces. Ne crée pas encore les onglets ni le shortcode, on fera ça à l'étape suivante.
