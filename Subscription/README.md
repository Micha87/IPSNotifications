# Subscription
Dieses Modul kann Nachrichten von StatusMessage-Modulen empfangen und sendet eine Benachrichtigung per Email / Push Notification / WebFront Notification /WebFront Audionotification  oder führt ein Script aus.
Die Nachrichten der einzelnen StatusMessage-Module können dabei selektiv "abonniert" werden. Hierzu steht im WebFront eine Liste der verfügbaren StatusMessage-Module zur Verfügung

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Abonnieren von Nachrichten der StatusMessage-Module via WebFront
* Senden der Statusnachrichten an:
** Email
** WebFront Push
** WebFront Notification
** WebFront Audionitification
** Skript


### 2. Voraussetzungen

- IP-Symcon ab Version 4.1

### 3. Software-Installation

Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/roastedelectrons/IPSNotifications.git`  

### 4. Einrichten der Instanzen in IP-Symcon

- Unter "Instanz hinzufügen" ist das 'NotificationSubscription'-Modul unter dem Hersteller '(Sonstige)' aufgeführt.  

__Konfigurationsseite__:

Name                    | Beschreibung
----------------------- | ---------------------------------
Typ                     | Versandweg: Email, Push, WebFront, Skript 
InstanzID               | ID der Sende-Instantz: SMTP (Email), WebFront Konfigurator (Push, WebFront), SkriptID (Skript)
Timeout                 | Nur bei WebFront Notification: Zeit in Sekunden, nachdem die Benachrichtigung ausgelendet wird


Wird der Typ *Skript* ausgewält stehen im aufgerufenen Skript folgende Variablen zum auslösenden Ereignis bereit:

Variable                | Bedeutung                                     | Beispiel
----------------------- | ----------------------------------------------- | ----------------
    $_IPS[Title]        | Titel der auslösenden StatusMessage           | Batteriewächter
    $_IPS[InstanceID]   | ID der auslösenden StatusMessage Instanz      | 23297
    $_IPS[Icon]         | Icon der auslösenden Variable                 | Battery
    $_IPS[VariableID]   | ID der auslösenden Variable                   | 43768
    $_IPS[VariableValue]| Wert der auslösenden Variable                 | 1
    $_IPS[Text]         | Text zusammengesetzt aus: *Pfad-Variablenname: Wert* | Haus - 1. Etage - Schlafzimmer - Heizung - Batterie: Batterie schwach
    $_IPS[VariableValueFomatted] | Wert gemäß Variablenprofil der auslösenden Variablen | Batterie schwach
    $_IPS[MediaID]      | ID zur MediaDatei für Soundausgabe |          12345



### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen

Name         | Typ       | Beschreibung
------------ | --------- | ----------------
Benachrichtigungen | Boolean | De-/Aktivieren der Benachrichtigung. Ist der Wert auf "Aus" gesetzt, werden keine Nachrichten versandt, auch wenn Statusmeldungen abonniert sind.
Abonnements      | Intger | Aktualisiert die Liste der verfügbaren Statusmeldungen.
StatusMessage       | Boolean   | De-/Aktiviert das Senden von Benachrichtigung der jeweiligen StausMessage-Module

##### Profile:

Subscription.Active

Subscription.Update

### 6. WebFront

Über das WebFront kann die Versendung von einzelnen oder allen Benachrichtigungen de-/aktiviert werden.  
Es werden zusätzlich alle verfügbaren StausMeldungs-Module angezeigt.

### 7. PHP-Befehlsreferenz

`boolean SUBSCR_UpdateSubscriptions(integer $InstanzID);`  
Aktualisiert die Liste der verfügbaren StatusMessage-Module. 
Die Funktion liefert keinerlei Rückgabewert.  
Beispiel:  
`SUBSCR_UpdateSubscriptions(12345);`
