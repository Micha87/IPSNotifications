# IPSNotifications
Diese Bibliothek stellt Module für die Visualisierung und das Senden von Statusmeldungen bereit. 

Das **StatusMessage Modul** überwacht einzelne Variablen oder alle Variablen mit einem bestimmten Variablen-Profil unterhalb einer bestimmten Kategorie (mit Unterkategorien/-Instanzen). Alle überwachten Variablen werden im WebFront als Links unter dieser Instanz visualisert. Links können auch ausgeblendet werden, wenn eine definierte Bedingung nicht erfüllt ist.
Sobald der Wert der beobachteten Variablen eine Bedingung erfüllt oder nicht mehr erfüllt, kann eine Statusnachricht an das Notification Control gesandt werden.

Das **Notification Control** ist der Vermittler zwischen Sender und Empfänger. Es empfängt die Nachrichten aller StatusMessage Module und leitet diese dann an die Subsription Module weiter. Außerdem enthält es einen Ereignis Log, der alle Statusmeldungen speichert und im WebFront visualisiert.

Die **Subscription** Module dienen dazu, bestimmten Statusmeldungen zu abonnieren. Im jeweiligen Subscription Modul kann dann der gewünschte Benachrichtigungs Typ eingestellt werden. Auf diese Weise kann jeder Empänger verschiedene Benachrichtigungen komfortabel über das WebFront abonnieren.
Wenn eine Statusmeldung empfangen wird, sendet das Subscription Modul eine Benachrichtigung an das entsprechende Endgerät/Instanz. Zur Verfügung stehen:

* Email (Versand erfolgt über SMTP-Modul)
* Push (Versand erfolgt über WebFront)
* WebFront Notification
* WebFront Audionotification
* Skript (Dem Skript werden die Daten der Statusmeldung als $data Array übergeben. Diese können dann im Skript beliebig weiterverarbeitet werden.)

## Inhaltverzeichnis

1. [Module](#Module)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Installation](#Installation)
4. [Anwendungsbeispiele](#Anwendungsbeispiele)


## Module

### Status Message Modul
* Überwachung von Variablen
    * Alle Variablen, die ein definierbares Variablen-Profil haben
    * Einzelne Variablen nach ID
* Statusanzeige von überwachten Variablen im Webfront
    * Variablen, die die Bedingung nicht erfüllen, können versteckt werden (z.B. Batterie OK)
* Benachrichtigungen senden
    * wenn Bedingung erfüllt und/oder nicht mehr erfüllt ist

[**Dokumentation StatusMessage**](StatusMessage)

### Subscription Modul
* Abonnieren von Nachrichten der StatusMessage-Module via WebFront
* Senden der Statusnachrichten an:
    * Email
    * WebFront Push
    * WebFront Notification
    * WebFront Audionitification
    * Skript

[**Dokumentation Notification Subscription**](NotificationSubscription)

### Notification Control
* Empfängt Nachrichten der Status Module und leitet diese an die Abonnenten (Notification Subscription) weiter
* Ereignis Log für alle Statusmeldungen
* Ein-/Ausschaltbarkeit der aller Benachrichtigung via WebFront-Button oder Skript-Funktion.
* Instanz-Typ: (Kern)

[**Dokumentation Notification Control**](NotificationControl)

## Voraussetzungen

- IP-Symcon ab Version 4.1

## Installation

Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/roastedelectrons/IPSNotifications.git`  

## Anwendungsbeispiele
### Batterieüberwachung
Es wird ein StatusMessage Modul angelegt mit dem Typ *Variablenprofil*. Als Variablenprofil wird *~Battery* ausgewählt und als Bedingung *Batterie schwach*.
Das Modul legt nun automatisch Links zu allen Batterie-Variablen an und versteckt automatisch alle Variablen, bei denen der Batterie Wert *OK* ist.

Zusätzlich kann eine Benachrichtigung gesendet werden, sobald eine Variable den Wert *Batterie schwach* annimmt. Um die Benachrichtigung z.B. per Email zu empfangen, wird ein Notification Subscription Modul erstellt.
In der Modulkonfiguration kann nun die SMTP Instanz ausgewählt werden, über die Email versendet werden soll. Außerdem wird die Empänger-Adresse angeben. Im WebFront kann nun für dieses Modul die Benachrichtigung für das entsprechende Batterie Status Modul abonniert werden.

### Audioansage an Multiroom-System
Über die *Skript* Funktion des NotificationSubscription Moduls können beliebige andere Empfänger angesteuert werden. Dem Skript wird ein Array mit allen Daten zur Statusmeldung übergeben und kann diese dann weiterverarbeiten.

### Push Benachrichtigung bei Rachmelderalarm

### Statusanzeige für Waschmaschine und anschließende Fertigmeldung

### Anzeige aller offenen Fenster im WebFront
