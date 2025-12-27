# 💻 Code PHP à ajouter au projet

> ⚠️ **Ce fichier contient TOUT le code PHP nécessaire pour le système d'audit.**  
> Copie-colle chaque section dans les fichiers correspondants.

---

## 📁 Structure des fichiers à créer

```
src/
├── ArkAuditor/
│   ├── Client/
│   │   └── NitradoApiClient.php
│   ├── Command/
│   │   ├── AuditEconomyCommand.php
│   │   └── TestNitradoCommand.php
│   ├── DTO/
│   │   ├── AuditResult.php
│   │   ├── WealthDistribution.php
│   │   ├── DinoDistribution.php
│   │   ├── InflationData.php
│   │   ├── PlayerActivity.php
│   │   └── ResourceFlow.php
│   ├── Entity/
│   │   └── EconomySnapshot.php
│   ├── Exception/
│   │   └── NitradoApiException.php
│   ├── Repository/
│   │   └── EconomySnapshotRepository.php
│   └── Service/
│       ├── EconomyAuditor.php
│       ├── WealthAnalyzer.php
│       ├── DinoAnalyzer.php
│       ├── InflationCalculator.php
│       └── PlayerActivityAnalyzer.php
└── Kernel.php

config/
├── services.yaml
└── routes/
    └── console.yaml
```

---

## ⚙️ ÉTAPE 0 : Créer la structure minimale Symfony

Avant de copier le code, créé les dossiers de base :

```bash
# Créé les dossiers
mkdir -p src/ArkAuditor/{Client,Command,DTO,Entity,Exception,Repository,Service}
mkdir -p config/packages
mkdir -p config/routes
mkdir -p bin
mkdir -p public
mkdir -p var/{cache,log,audits}
```

---

## 🔹 FICHIER 1 : `bin/console`

```php
#!/usr/bin/env php
<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

if (!is_file(dirname(__DIR__).'/vendor/autoload_runtime.php')) {
    throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);

    return new Application($kernel);
};
```

**Puis rends-le exécutable :**
```bash
chmod +x bin/console
```

---

## 🔹 FICHIER 2 : `src/Kernel.php`

```php
<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
```

---

## 🔹 FICHIER 3 : `config/services.yaml`

```yaml
parameters:
    env(NITRADO_API_TOKEN): ''
    env(NITRADO_SERVICE_ID): ''
    ark.audit.output_path: '%kernel.project_dir%/var/audits'

services:
    _defaults:
        autowire: true
        autoconfigure: true

    App\\:
        resource: '../src/'
        exclude:
            - '../src/Entity/'
            - '../src/DTO/'
            - '../src/Kernel.php'

    App\\ArkAuditor\\Client\\NitradoApiClient:
        arguments:
            $apiToken: '%env(NITRADO_API_TOKEN)%'

    App\\ArkAuditor\\Service\\EconomyAuditor:
        arguments:
            $serviceId: '%env(NITRADO_SERVICE_ID)%'

    App\\ArkAuditor\\Command\\AuditEconomyCommand:
        arguments:
            $auditOutputPath: '%ark.audit.output_path%'
```

---

## 🔹 FICHIER 4 : `config/packages/framework.yaml`

```yaml
framework:
    secret: '%env(APP_SECRET)%'
    http_method_override: false
    handle_all_throwables: true
    php_errors:
        log: true
```

---

## 🔹 FICHIER 5 : `config/packages/doctrine.yaml`

```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        profiling_collect_backtrace: '%kernel.debug%'
    orm:
        auto_generate_proxy_classes: true
        enable_lazy_ghost_objects: true
        report_fields_where_declared: true
        validate_xml_mapping: true
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true
        mappings:
            App:
                type: attribute
                is_bundle: false
                dir: '%kernel.project_dir%/src/ArkAuditor/Entity'
                prefix: 'App\\ArkAuditor\\Entity'
                alias: App
```

---

## 📌 Instructions pour copier le reste du code

Les fichiers PHP complets sont disponibles dans notre conversation précédente. Pour chaque fichier :

1. **NitradoApiClient.php** → Client API Nitrado avec gestion d'erreurs
2. **EconomyAuditor.php** → Service principal d'orchestration
3. **WealthAnalyzer.php** → Analyse distribution richesse + Gini
4. **DinoAnalyzer.php** → Analyse distribution dinos
5. **InflationCalculator.php** → À implémenter (placeholder pour l'instant)
6. **PlayerActivityAnalyzer.php** → À implémenter (placeholder)
7. **AuditEconomyCommand.php** → Command Symfony avec options export
8. **TestNitradoCommand.php** → Command pour tester la connexion API
9. **EconomySnapshot.php** → Entity Doctrine pour persistance
10. **DTOs** (AuditResult, WealthDistribution, etc.) → Structures de données typées

---

## 🚀 Guide rapide de mise en place

### Option 1 : Tu as déjà tout le code de notre conversation

Remonte dans notre conversation et copie-colle chaque fichier PHP que je t'ai donné dans les bons emplacements.

### Option 2 : Je peux te créer une archive

Dis-moi et je te crée un fichier compressé avec tout le code prêt à l'emploi.

### Option 3 : Clone le repo et ajoute les fichiers manuellement

```bash
git clone https://github.com/GaetanLgt/arkadia-economy-auditor.git
cd arkadia-economy-auditor
composer install
# Puis ajoute les fichiers PHP un par un
```

---

## ✅ Vérification finale

Une fois tous les fichiers ajoutés :

```bash
# 1. Vérifier l'autoload
composer dump-autoload

# 2. Vider le cache
php bin/console cache:clear

# 3. Tester la connexion
php bin/console ark:test:nitrado

# 4. Premier audit
php bin/console ark:audit:economy
```

---

## 🆘 En cas d'erreur "Class not found"

```bash
composer dump-autoload
php bin/console cache:clear
```

Vérifie que :
- Les namespaces sont corrects
- Les fichiers sont bien dans `src/ArkAuditor/`
- Le `composer.json` a bien `"App\\": "src/"`

---

**Prêt à commencer ? Clone le repo et ajoute les fichiers PHP ! 🚀**
