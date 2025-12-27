# 🔍 ARKADIA Economy Auditor

> Système d'audit économique automatisé pour serveur ARK Survival Ascended via API Nitrado

[![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-8.0-000000?logo=symfony)](https://symfony.com/)

## 📋 Fonctionnalités

- **📊 Analyse de la distribution de richesse** (Coefficient de Gini, Top 10%, Médiane)
- **🦖 Distribution des dinos** (Par joueur, espèce, niveau, détection hoarders)
- **💰 Calcul d'inflation** (Évolution des ressources sur 30 jours)
- **👥 Activité joueurs** (Sessions, durée moyenne, heures de pointe)
- **📦 Flux de ressources** (Entrées/Sorties/Balance)
- **💾 Export JSON** + **Sauvegarde base de données**
- **🔄 Automatisation** (Cron, n8n compatible)

## 🚀 Installation rapide

```bash
# 1. Clone le repo
git clone https://github.com/GaetanLgt/arkadia-economy-auditor.git
cd arkadia-economy-auditor

# 2. Installer les dépendances
composer install

# 3. Configuration
cp .env .env.local
nano .env.local
# Remplis tes credentials Nitrado :
# NITRADO_API_TOKEN=ton_token_ici
# NITRADO_SERVICE_ID=ton_service_id

# 4. Base de données
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. Test de connexion
php bin/console ark:test:nitrado
```

## 🔑 Obtenir ton token Nitrado

1. Va sur https://server.nitrado.net/deu/developer/tokens
2. Crée un nouveau token avec les permissions :
   - ✅ `gameserver:read`
   - ✅ `gameserver:file:read`
   - ✅ `gameserver:stats:read`
3. Copie le token
4. Colle-le dans `.env.local`

⚠️ **SÉCURITÉ** : Ne commit JAMAIS `.env.local` dans Git !

## 📖 Utilisation

```bash
# Audit simple
php bin/console ark:audit:economy

# Audit + Export JSON
php bin/console ark:audit:economy --export-json

# Audit + Sauvegarde DB
php bin/console ark:audit:economy --save-db

# Tout en un
php bin/console ark:audit:economy --export-json --save-db
```

## ⏰ Automatisation (Cron)

```bash
crontab -e
```

Ajoute :

```
# Audit quotidien à 3h du matin
0 3 * * * cd /var/www/arkadia-economy-auditor && php bin/console ark:audit:economy --export-json --save-db
```

## 📊 Exemple de sortie JSON

```json
{
  "meta": {
    "version": "1.0.0",
    "timestamp": "2025-12-27T15:30:00+01:00",
    "server_id": "arkadia_france_001"
  },
  "wealth_distribution": {
    "statistics": {
      "gini_coefficient": 0.4235,
      "top_10_percent_wealth": 54.3,
      "median_wealth": 890,
      "total_players": 47
    }
  },
  "dino_distribution": {
    "total_dinos": 3421,
    "statistics": {
      "median_dinos_per_player": 22,
      "hoarders": {
        "76561198087654321": 127
      }
    }
  }
}
```

## 🏗️ Architecture

```
src/ArkAuditor/
├── Client/
│   └── NitradoApiClient.php          # Client API Nitrado
├── Service/
│   ├── EconomyAuditor.php            # Orchestrateur principal
│   ├── WealthAnalyzer.php            # Analyse richesse
│   ├── DinoAnalyzer.php              # Analyse dinos
│   ├── InflationCalculator.php       # Calcul inflation
│   └── PlayerActivityAnalyzer.php    # Analyse activité
├── Command/
│   ├── AuditEconomyCommand.php       # Command principale
│   └── TestNitradoCommand.php        # Test connexion
├── Entity/
│   └── EconomySnapshot.php           # Entité Doctrine
├── DTO/
│   ├── AuditResult.php
│   ├── WealthDistribution.php
│   └── DinoDistribution.php
└── Exception/
    └── NitradoApiException.php
```

## 🐛 Dépannage

### Erreur "Invalid token"

```bash
php bin/console debug:container --env-vars | grep NITRADO
```

### Le token ne se charge pas

```bash
php bin/console cache:clear
chmod 600 .env.local
```

## 📝 Roadmap

- [ ] Parser fichiers .ark natifs
- [ ] Dashboard web (Symfony UX)
- [ ] Alertes Discord
- [ ] Export PDF rapports

## 📄 License

MIT

## 👤 Auteur

**Gaëtan** - [GaetanLgt](https://github.com/GaetanLgt)

🎮 ARKADIA FRANCE - Communauté ARK Survival Ascended
