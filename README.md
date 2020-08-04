# MU plugins pour Wordpress

Collection de mu-plugins pour Wordpress

Attention: c'est librairie n'ajoutent pas automatiquement les mu-plugins dans le dossier `wp-content/mu-plugins`, vous devez donc le créer vous-même.

## Installation

```bash
composer require dbout/wp-mu-plugins
```

Puis créez un mu-plugin dans le dossier `wp-content/mu-plugins` et ajoutez-y les différentes classes :

```php
/**
 * Plugin Name:     Reset Wordpress
 * Version:         1.0.0
 * License:         GPL2
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 */

\Dbout\WpMuPlugins\CleanHead::clean();
new \Dbout\WpMuPlugins\DisableComment();
...
```

## Listes des mu-plugins

### [CleanHead](src/CleanHead.php)

Supprime les metas dans le \<head\>.

```php
\Dbout\WpMuPlugins\CleanHead::clean();
```

### [DisableComment](src/DisableComment.php)

Désactive les commentaires, en front comme en back.

```php
new \Dbout\WpMuPlugins\DisableComment();
```

### [DisableRestApi](src/DisableRestApi.php)

Désactive l'API rest pour les utilisateurs non connectés.

```php
\Dbout\WpMuPlugins\DisableRestApi::disabled();
```

### [RemoveEmojiSupport](src/RemoveEmojiSupport.php)

Désactiver les emojis intégrés qui chargent de gros fichiers JavaScript, CSS et images.

```php
\Dbout\WpMuPlugins\RemoveEmojiSupport::remove();
```

### [RemoveGenetatorMetas](src/RemoveGenetatorMetas.php)

Supprime les numéros de version de Wordpress et des plugins (comme Woocommerce) dans le \<head\>.

```php
\Dbout\WpMuPlugins\RemoveGenetatorMetas::remove();
```

### [RemoveH1WpEditor](src/RemoveH1WpEditor.php)

Supprime la balise \<h1\> de l'éditeur.

```php
\Dbout\WpMuPlugins\RemoveH1WpEditor::remove();
```

### [UserLastLogin](src/UserLastLogin.php)

Sauvegarde la date de la dernière connexion d'un utilisateur et l'affiche dans la liste des utilisateurs.

```php
(new \Dbout\WpMuPlugins\UserLastLogin())->register();
```