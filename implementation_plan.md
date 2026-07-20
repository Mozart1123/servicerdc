# Implémentation du Flux de Paiement et de Commission (Missions)

Ce document décrit la mise en œuvre du flux de paiement via K-PAY, du calcul des commissions, et de la gestion des litiges, en s'appuyant sur les colonnes nouvellement ajoutées (`payment_channel`, `commission_amount`, `commission_status`, `contact_unlocked_at` sur `missions`, et `ticket_type`, `resolution` sur `support_tickets`).

## Réponses aux questions préalables
1. **Système de Paiement Intégré** : L'application utilise **K-PAY** (`App\Services\KpayService`), un agrégateur qui supporte divers opérateurs Mobile Money en Afrique (M-Pesa, Airtel Money, Orange Money, etc.). Les paiements par carte (Visa/Mastercard) semblent également être prévus via cette passerelle dans l'existant.
2. **Mécanisme de Commission** : L'API K-PAY ne gère pas de fonction native de "split payment" (prélèvement de commission à la source avec redirection automatique du reste vers l'artisan). Lors d'un paiement (`/payments/init`), 100% du montant est transféré vers le compte/portefeuille principal de la plateforme ProConnect. La commission est donc **prélevée à la source** du point de vue de l'application : l'argent est chez ProConnect, et le solde dû à l'artisan (montant payé - commission) devra lui être reversé séparément (soit manuellement, soit via un retrait automatisé via `/payments/withdraw`).

## User Review Required

> [!IMPORTANT]
> **Flux de paiement K-PAY** : Lors du paiement "via plateforme", l'utilisateur sera redirigé vers une vue où il entrera son numéro de téléphone, puis un push USSD (Mobile Money) sera initié via `KpayService::initiatePayment`. Le webhook de K-PAY confirmera ensuite le paiement. Confirmez-vous cette cinématique sans redirection externe ?

> [!WARNING]
> **Reversement à l'artisan (Payout)** : Pour l'instant, je vais implémenter le marquage de la commission (`commission_status = 'pending' -> 'paid'`) au moment où le client paie la plateforme. Souhaitez-vous que je code également l'interface pour que l'artisan demande le **retrait** de son argent vers son Mobile Money, ou cela fera-t-il l'objet d'une autre tâche/un autre ticket ? 

## Proposed Changes

---

### Missions & Paiement

#### [NEW] `app/Http/Controllers/User/MissionPaymentController.php`
- Création d'un contrôleur dédié au paiement des missions.
- **`checkout(Mission $mission)`** : Affiche l'écran "Payer via ProConnect" (Mobile Money) ou "Payer en Cash".
- **`process(Request $request, Mission $mission)`** :
  - Si "cash" : Met à jour `payment_channel = 'cash'`, `contact_unlocked_at = now()`, `status = 'in_progress'`. Redirige vers la mission avec un message informatif ("Le paiement cash n'ouvre pas droit à garantie ni à avis automatique.").
  - Si "platform" : Initie la transaction K-PAY. Met la transaction en base (une table `transactions` ou via un job en attente du Webhook).

#### [MODIFY] `app/Http/Controllers/Webhook/KpayWebhookController.php`
- Mise à jour du gestionnaire de webhook pour traiter le paiement des missions.
- Si le paiement réussit :
  - Met à jour `payment_channel = 'platform'`, `status = 'in_progress'`, `contact_unlocked_at = now()`.
  - Calcule : `commission_amount = amount * 0.05` (5%).
  - Définit `commission_status = 'paid'` (puisque l'argent est déjà arrivé sur la plateforme).

#### [MODIFY] `resources/views/user/missions/show.blade.php`
- Si la mission est en attente de validation par le client (`status == 'pending'`), ajout du bouton "Valider & Payer".
- Ajout du bouton "Signaler un problème" (Litige) pour le client si la mission est en cours/terminée.
- Conditionner l'affichage du bouton "Laisser un avis" uniquement si `payment_channel == 'platform'` (le cash n'ouvre pas droit à l'avis automatique).

#### [NEW] `resources/views/user/missions/checkout.blade.php`
- Vue présentant les deux options (Radio buttons : Cash vs ProConnect).
- Si ProConnect : champ numéro de téléphone Mobile Money pour initier le push USSD K-PAY.

---

### Litiges (Disputes)

#### [MODIFY] `app/Http/Controllers/User/SupportTicketController.php`
- Création d'une méthode pour gérer la création d'un ticket de litige rattaché à une mission.
- Fixer automatiquement `ticket_type = 'dispute'` et `mission_id`.

#### [MODIFY] `app/Http/Controllers/Admin/SupportTicketController.php` (ou similaire)
- Dans la vue de traitement d'un ticket admin :
  - Si `ticket_type == 'dispute'`, afficher un panneau spécifique "Infos de la Mission" (Montant, canal de paiement, statut de la commission).
  - Ajouter les actions de résolution : "Rembourser le client" (`resolution = 'refund'`) et "Clore sans remboursement" (`resolution = 'no_refund'`).
  - Si `payment_channel == 'cash'`, désactiver le bouton de remboursement technique et afficher une alerte claire.

#### [MODIFY] `resources/views/admin/tickets/show.blade.php`
- UI pour les agents traitant le ticket, intégrant la lecture seule de la mission et des messages associés, et le formulaire de résolution (`resolution`, `refund_amount`).

## Verification Plan

### Manual Verification
1. **Paiement Cash** : Connecté en client, j'accepterai une mission en choisissant "Cash". Je vérifierai que le statut passe en `in_progress`, que le contact est débloqué, et qu'aucun avis n'est permis à la fin.
2. **Paiement Plateforme** : Toujours en client, je choisirai "ProConnect" pour une autre mission. J'utiliserai un numéro de test K-PAY pour simuler le succès. Je vérifierai que la commission (5%) est bien enregistrée et que l'avis est permis à la fin.
3. **Litiges** : En tant que client, je créerai un litige. En tant qu'admin, je vérifierai l'impossibilité de rembourser une mission cash, et la possibilité d'émettre un remboursement partiel/total sur une mission plateforme.
