# Statusmeldung
Das Modul überwacht Variablen und zeigt Links zu den Variablen je nach Status im WebFront an.
Wenn die überwachten Variablen die definierte Bedingung erfüllen und/oder nicht erfüllen, wird eine Benachrichtigung an den Notification Controller versandt.
Die Nachrichten können dann mit den Abonnement Modulen "abonniert" werden und Benachrichtigungen per Email, WebFront Notification, Push oder an an Skript senden.

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Überwachung von Variablen
    * Alle Variablen, die ein definierbares Variablen-Profil haben
    * Einzelne Variablen nach ID
* Statusanzeige von überwachten Variablen im Webfront
    * Variablen, die die Bedingung nicht erfüllen, können versteckt werden (z.B. Batterie OK)
* Benachrichtigungen senden
    * wenn Bedingung erfüllt und/oder nicht mehr erfüllt ist


### 2. Voraussetzungen

- IP-Symcon ab Version 4.1

### 3. Software-Installation

Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/roastedelectrons/IPSNotifications.git`  

### 4. Einrichten der Instanzen in IP-Symcon

- Unter "Instanz hinzufügen" ist das 'StatusMessage'-Modul unter dem Hersteller '(Sonstige)' aufgeführt.  

__Konfigurationsseite__:

Name                    | Beschreibung
----------------------- | ---------------------------------
**Typ**                     | Auswahl der zu überwachenden Variablen. Mögliche Typen: Variable, Variablenprofil
**Typ Variablenprofil**
* Kategorie              | Kategorie unter der (auch Unterkategorien) alle Variablen mit definiertem VariablenProfil überwacht werden.
* Profil                  | Alle Variablen mit dem ausgewählten Profil werden überwacht.
**Typ Variable**
* Variable               | Variable, die überwacht werden soll
**Bedingung**           
* Bedingung               | Operator, der den Wert der Variablen mit dem Bedingungs-Wert vergleicht.
* Wert                    | Wert der Bedingung
* Checkbox                | Wenn ausgewält, werden alle Links zu überwachten Variablen versteckt, wenn die Bedingung nicht erfüllt ist.
**Benachrichtigung**
* Checkboxen | Wenn ausgewählt, wird eine Benachrichtigung versandt, sobald die Bedingung erfüllt ist bzw. nicht mehr erfüllt ist. Benachrichtigungen werden nur bei Änderung des Status versandt.
* Titel                   | Titel der Benachrichtigung
* Audiodatei              | MediaID, die eine Audio-Datei enthält. Diese kann z.B. für WebFront Audionotifications genutzt werden.


### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen

Name         | Typ       | Beschreibung
------------ | --------- | ----------------
Variablen    | Link      | Links zu überwachten Variablen. Links können versteckt werden, wenn die Bedingung nicht erfüllt ist.
Trigger      | Event     | Trigger, der bei Änderungen von Variablen die Statusanzeige akualisiert bzw. Benachrichtigungen sendet.


##### Profile:

Es werden keine zusätzlichen Profile hinzugefügt

### 6. WebFront

Über das WebFront können alle überwachten Variablen angezeigt werden. Sofern in der Modul-Konfiguration eingestellt, werden Links zu Variablen, die die Bedingung nicht erfüllen, ausgeblendet.

### 7. PHP-Befehlsreferenz

`boolean STATEMSG_Update(integer $InstanzID);` 
 
Aktualisiert die Liste der zu überwachenden Variablen und erstellt Trigger für Variablenänderungen.
Die Funktion liefert keinerlei Rückgabewert.  



`boolean STATEMSG_GetVariableList(integer $InstanzID);` 
 
Gibt ein Array mit allen überwachten Variablen zurück. 

