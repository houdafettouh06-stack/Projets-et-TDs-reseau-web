# Projet 2 : Système de Notification d'Absences (Version 1.0)

Cette application permet de gérer les absences étudiants et de notifier les parents via **Email** (Gmail SMTP) et **WhatsApp** (Redirection Web).

## Fonctionnalités (v1.0)
- **Import Smart CSV** : Détection automatique des colonnes (Nom, Prénom, Classe, Contact...).
- **Notifications Email Réelles** : Configuration SMTP (Gmail) fonctionnelle avec gestion des erreurs 535 (nettoyage mdp).
- **Notifications WhatsApp** : Génération de liens `wa.me` pré-remplis pour envoi rapide via WhatsApp Web.
- **Failover** : Si l'envoi email échoue, le système bascule en simulation pour ne pas bloquer l'usage.
- **Logs** : Suivi des envois dans `envois.log`.

## Installation
1. Configurer la base de données via `database.sql`.
2. Vérifier `config/db.php`.
3. Pour les emails : configurez `api/send_notifications.php` avec vos identifiants Gmail (App Password).

## Structure
- `/core` : Logique métier (Importateur CSV, Mailer SMTP).
- `/public/api` : Endpoints AJAX scindés (Upload, Save, Send).
- `/views` : Interface utilisateur.

_Version sauvegardée le 07/01/2026_
