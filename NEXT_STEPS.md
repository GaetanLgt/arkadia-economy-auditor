# ✅ PROCHAINES ÉTAPES - Le code est prêt !

> 🎉 **Tous les fichiers PHP sont maintenant dans le repo !**

## 📦 Ce qui a été créé

✅ **Structure Symfony complète**
✅ **Client API Nitrado** avec gestion d'erreurs
✅ **Services d'analyse** (Wealth, Dinos, Inflation, Activity)
✅ **Commands Symfony** (test + audit)
✅ **DTOs typés** pour structure de données
✅ **Entity Doctrine** pour persistance
✅ **Migration** de base de données
✅ **Configuration** complète

---

## 🚀 ÉTAPE 1 : Clone le repo

```bash
cd /var/www  # ou ton dossier de projets
git clone https://github.com/GaetanLgt/arkadia-economy-auditor.git
cd arkadia-economy-auditor
```

---

## 📥 ÉTAPE 2 : Install Composer

```bash
composer install
```

**Si erreur "composer not found"** :
```bash
# Installer Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 🔐 ÉTAPE 3 : Configure ton token (IMPORTANT !)

### 3.1 Créé ton fichier `.env.local`

```bash
cp .env.local.template .env.local
nano .env.local
```

### 3.2 Remplis avec tes VRAIES valeurs

```bash
# .env.local
APP_ENV=dev
APP_SECRET=ChangeMeToASecretValue123

# Base de données (PostgreSQL recommandé)
DATABASE_URL="postgresql://ton_user:ton_password@127.0.0.1:5432/arkadia_audit?serverVersion=16&charset=utf8"

# API Nitrado (REMPLACE AVEC TES VRAIES VALEURS)
NITRADO_API_TOKEN=ton_nouveau_token_ici
NITRADO_SERVICE_ID=ton_service_id
```

**💡 Comment obtenir ces infos ?**

#### Token Nitrado :
1. Va sur https://server.nitrado.net/deu/developer/tokens
2. Clique "Create new token"
3. Nom : `ARKADIA_AUDIT`
4. Permissions :
   - ✅ `gameserver:read`
   - ✅ `gameserver:file:read`
   - ✅ `gameserver:stats:read`
5. Copie le token → colle dans `.env.local`

#### Service ID :
Dans l'URL Nitrado :
```
https://server.nitrado.net/deu/gameserver/9876543
                                            ^^^^^^^ 
                                        C'EST ÇA !
```

---

## 🗄️ ÉTAPE 4 : Base de données

### 4.1 Créé la base

```bash
php bin/console doctrine:database:create
```

### 4.2 Applique la migration

```bash
php bin/console doctrine:migrations:migrate
```

Réponds **yes** quand demandé.

---

## ✅ ÉTAPE 5 : Test de connexion

```bash
php bin/console ark:test:nitrado
```

**✅ Si ça marche, tu verras :**
```
✅ Connexion réussie !
Nom du serveur: ARK: Survival Ascended
Statut: started
IP: xxx.xxx.xxx.xxx
```

**❌ Si erreur :**

### Erreur "Invalid token"
```bash
# Vérifie que le token est bien chargé
php bin/console debug:container --env-vars | grep NITRADO
```

### Erreur "Service not found"
Vérifie ton Service ID dans `.env.local`

### Erreur "Database connection failed"
```bash
# Test connexion PostgreSQL
psql -U ton_user -d arkadia_audit -h 127.0.0.1

# Ou créé la base si elle existe pas
createdb arkadia_audit
```

---

## 🎯 ÉTAPE 6 : Premier audit !

```bash
php bin/console ark:audit:economy --export-json
```

Le fichier JSON sera créé dans : `var/audits/economy_audit_YYYY-MM-DD_HHMMSS.json`

### Commandes disponibles :

```bash
# Audit simple (affichage console)
php bin/console ark:audit:economy

# Audit + export JSON
php bin/console ark:audit:economy --export-json

# Audit + sauvegarde DB
php bin/console ark:audit:economy --save-db

# Tout en un
php bin/console ark:audit:economy --export-json --save-db
```

---

## ⏰ ÉTAPE 7 : Automatisation (optionnel)

### Via Cron

```bash
crontab -e
```

Ajoute :
```
# Audit quotidien à 3h du matin
0 3 * * * cd /var/www/arkadia-economy-auditor && php bin/console ark:audit:economy --export-json --save-db >> /var/log/ark-audit.log 2>&1
```

---

## 🐛 Dépannage commun

### "Class not found"

```bash
composer dump-autoload
php bin/console cache:clear
```

### Permissions fichiers

```bash
chmod +x bin/console
chmod 600 .env.local
chmod -R 777 var/
```

### Réinstaller dépendances

```bash
rm -rf vendor/
composer install
```

---

## 📊 Ce que tu peux faire maintenant

✅ **Analyser ton serveur ARK** avec des métriques précises
✅ **Détecter les hoarders** (joueurs avec >80 dinos)
✅ **Calculer le Gini coefficient** (inégalité richesse)
✅ **Exporter en JSON** pour d'autres outils
✅ **Sauvegarder en DB** pour historique
✅ **Automatiser** avec cron

---

## 🎓 Prochaines améliorations possibles

- [ ] Parser fichiers .ark natifs (sans API)
- [ ] Dashboard web (Symfony UX + Chart.js)
- [ ] Alertes Discord via webhook
- [ ] Comparaison historique des audits
- [ ] Export PDF rapports
- [ ] API REST pour consultation

---

## 📚 Documentation complète

- **[README.md](README.md)** - Vue d'ensemble
- **[INSTALL.md](INSTALL.md)** - Guide détaillé
- **[QUICKSTART.md](QUICKSTART.md)** - Démarrage rapide
- **[CODE_TO_ADD.md](CODE_TO_ADD.md)** - Référence code

---

## 🆘 Besoin d'aide ?

1. ✅ Vérifie que tu as suivi TOUTES les étapes ci-dessus
2. 📖 Lis les docs dans le repo
3. 🐛 Ouvre une issue GitHub : https://github.com/GaetanLgt/arkadia-economy-auditor/issues

---

## 🎉 Félicitations !

Tu as maintenant un système d'audit économique professionnel pour ton serveur ARK !

**Prêt à auditer ARKADIA FRANCE ? 🚀**

```bash
php bin/console ark:audit:economy --export-json --save-db
```
