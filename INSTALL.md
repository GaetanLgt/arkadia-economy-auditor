# 🚀 Guide d'Installation Complet

## 📋 Prérequis

- **PHP 8.3+** avec extensions : `php-cli`, `php-mbstring`, `php-xml`, `php-curl`, `php-pgsql` (ou `php-mysql`)
- **Composer** 2.x
- **PostgreSQL 14+** ou **MySQL 8.0+**
- **Git**
- **Token API Nitrado** (voir ci-dessous)

---

## 🔧 Étape 1 : Cloner le repo

```bash
cd /var/www  # ou ton répertoire de projets
git clone https://github.com/GaetanLgt/arkadia-economy-auditor.git
cd arkadia-economy-auditor
```

---

## 📦 Étape 2 : Installer Composer

```bash
composer install
```

---

## ⚙️ Étape 3 : Configuration

### 3.1 Créer .env.local

```bash
cp .env .env.local
nano .env.local
```

### 3.2 Remplir tes credentials Nitrado

```bash
# .env.local
APP_ENV=dev

# Base de données
DATABASE_URL="postgresql://ton_user:ton_password@127.0.0.1:5432/arkadia_audit?serverVersion=16&charset=utf8"

# API Nitrado (REMPLACE avec tes vraies valeurs)
NITRADO_API_TOKEN=ton_token_nitrado_ici
NITRADO_SERVICE_ID=123456
```

**Où trouver ces infos ?**

#### Token Nitrado :
1. Va sur https://server.nitrado.net/deu/developer/tokens
2. Crée un nouveau token
3. Permissions nécessaires :
   - ✅ `gameserver:read`
   - ✅ `gameserver:file:read`
   - ✅ `gameserver:stats:read`
4. Copie le token

#### Service ID :
Dans l'URL de ton serveur Nitrado :
```
https://server.nitrado.net/deu/gameserver/9876543
                                            ^^^^^^^ 
                                        TON SERVICE ID
```

---

## 🗄️ Étape 4 : Base de données

### 4.1 Créer la base

```bash
php bin/console doctrine:database:create
```

### 4.2 Créer la migration

```bash
php bin/console make:migration
```

### 4.3 Appliquer la migration

```bash
php bin/console doctrine:migrations:migrate
```

---

## ✅ Étape 5 : Tester la connexion

```bash
php bin/console ark:test:nitrado
```

✅ Si ça affiche :
```
✅ Connexion réussie !
Nom du serveur: ARK: Survival Ascended
Statut: started
```

**C'est bon ! Tu peux continuer.**

---

## 📊 Étape 6 : Premier audit

```bash
php bin/console ark:audit:economy --export-json
```

Le fichier JSON sera créé dans : `/var/audits/economy_audit_YYYY-MM-DD_HHMMSS.json`

---

## ⏰ Étape 7 : Automatisation (optionnel)

### Via Cron

```bash
crontab -e
```

Ajoute :
```
# Audit quotidien à 3h du matin
0 3 * * * cd /var/www/arkadia-economy-auditor && php bin/console ark:audit:economy --export-json --save-db >> /var/log/ark-audit.log 2>&1
```

### Via n8n

1. Importe le workflow dans `/docs/n8n-workflow.json` (à créer)
2. Configure l'URL de ton projet

---

## 🐛 Dépannage

### Erreur : "Class not found"

```bash
composer dump-autoload
php bin/console cache:clear
```

### Erreur : "Database connection failed"

Vérifie `DATABASE_URL` dans `.env.local`

```bash
# Test connexion PostgreSQL
psql -U ton_user -d arkadia_audit -h 127.0.0.1

# Test connexion MySQL
mysql -u ton_user -p arkadia_audit
```

### Erreur : "Invalid Nitrado token"

```bash
# Vérifie que le token est bien chargé
php bin/console debug:container --env-vars | grep NITRADO
```

Doit afficher :
```
NITRADO_API_TOKEN    abc123...
NITRADO_SERVICE_ID   123456
```

---

## 📁 Structure finale attendue

```
arkadia-economy-auditor/
├── bin/
│   └── console
├── config/
│   ├── packages/
│   ├── routes/
│   └── services.yaml
├── migrations/
│   └── Version20251227123456.php
├── src/
│   ├── ArkAuditor/
│   │   ├── Client/
│   │   ├── Command/
│   │   ├── DTO/
│   │   ├── Entity/
│   │   ├── Exception/
│   │   └── Service/
│   └── Kernel.php
├── var/
│   ├── audits/          # Fichiers JSON exportés
│   ├── cache/
│   └── log/
├── vendor/
├── .env
├── .env.local           # TON FICHIER AVEC VRAIS CREDENTIALS
├── .gitignore
├── composer.json
└── README.md
```

---

## 🎯 Prochaines étapes après installation

1. **Teste avec un audit simple** : `php bin/console ark:audit:economy`
2. **Examine le JSON** : `cat var/audits/economy_audit_*.json`
3. **Configure le cron** pour automatiser
4. **(Optionnel) Crée un dashboard** pour visualiser les données

---

## 🆘 Besoin d'aide ?

- Issues GitHub : https://github.com/GaetanLgt/arkadia-economy-auditor/issues
- Documentation API Nitrado : https://doc.nitrado.net/

---

## ⚠️ Important : Sécurité

- **Ne commit JAMAIS `.env.local`** dans Git
- Garde ton token Nitrado secret
- Change `APP_SECRET` en production
- Utilise HTTPS en production

---

**Installation terminée ! 🎉**

Reviens au [README.md](README.md) pour voir toutes les commandes disponibles.
