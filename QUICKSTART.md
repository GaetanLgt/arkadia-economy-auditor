# ⚡ Quick Start Guide

> 🚀 Mise en place en 5 minutes chrono

## 🎯 Ce que tu vas faire

1. Cloner le repo
2. Installer les dépendances
3. Configurer ton token Nitrado
4. Tester la connexion
5. Lancer ton premier audit

---

## 📝 Les 5 étapes

### 1️⃣ Clone et installe

```bash
git clone https://github.com/GaetanLgt/arkadia-economy-auditor.git
cd arkadia-economy-auditor
composer install
```

### 2️⃣ Configure ton token

```bash
# Créé le fichier de config
cp .env .env.local

# Édite-le
nano .env.local
```

Ajoute ces 2 lignes (remplace avec tes vraies valeurs) :

```bash
NITRADO_API_TOKEN=ton_token_ici
NITRADO_SERVICE_ID=123456
```

**💡 Comment obtenir ton token ?**
1. Va sur https://server.nitrado.net/deu/developer/tokens
2. Clique "Create new token"
3. Coche : `gameserver:read`, `gameserver:file:read`, `gameserver:stats:read`
4. Copie le token

**💡 Où trouver le Service ID ?**
Dans l'URL de ton serveur Nitrado :
```
https://server.nitrado.net/deu/gameserver/9876543
                                            ^^^^^^^ 
                                        C'EST ÇA !
```

### 3️⃣ Configure la base de données

```bash
# Créé la base
php bin/console doctrine:database:create

# Créé les tables
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

### 4️⃣ Teste la connexion

```bash
php bin/console ark:test:nitrado
```

✅ Si tu vois :
```
✅ Connexion réussie !
Nom du serveur: ARK: Survival Ascended
```

**C'EST BON ! Continue.**

### 5️⃣ Fais ton premier audit

```bash
php bin/console ark:audit:economy --export-json
```

Le fichier JSON sera dans : `var/audits/economy_audit_YYYY-MM-DD_HHMMSS.json`

---

## 🎉 C'est tout !

Tu peux maintenant :

```bash
# Audit complet avec sauvegarde DB
php bin/console ark:audit:economy --export-json --save-db

# Automatiser avec cron
crontab -e
# Ajoute : 0 3 * * * cd /var/www/arkadia-economy-auditor && php bin/console ark:audit:economy --export-json --save-db
```

---

## ⚠️ IMPORTANT : Ajouter le code PHP

**Le repo contient la structure, mais tu dois ajouter les fichiers PHP.**

👉 **Lis le fichier [CODE_TO_ADD.md](CODE_TO_ADD.md)** qui contient :
- La liste de tous les fichiers à créer
- Les instructions pour les copier
- Les liens vers le code complet

Tous les fichiers PHP sont dans **notre conversation précédente** dans cette session Claude.

**Ou bien :**

Tu peux me demander de créer un script qui génère tous les fichiers automatiquement.

---

## 🐛 Problèmes courants

### "Class not found"

```bash
composer dump-autoload
php bin/console cache:clear
```

### "Invalid token"

Vérifie que ton token est bien dans `.env.local` :

```bash
cat .env.local | grep NITRADO
```

### "Database connection failed"

Vérifie `DATABASE_URL` dans `.env.local`

---

## 📚 Documentation complète

- **[README.md](README.md)** - Vue d'ensemble du projet
- **[INSTALL.md](INSTALL.md)** - Guide d'installation détaillé
- **[CODE_TO_ADD.md](CODE_TO_ADD.md)** - Code PHP à copier

---

## 🆘 Besoin d'aide ?

1. Vérifie les docs ci-dessus
2. Regarde les exemples dans notre conversation
3. Ouvre une issue GitHub

---

**Prêt ? Clone et lance ! 🚀**

```bash
git clone https://github.com/GaetanLgt/arkadia-economy-auditor.git && cd arkadia-economy-auditor && composer install
```
