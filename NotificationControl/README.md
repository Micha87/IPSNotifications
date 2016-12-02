# Notification Controller
Dieses Modul ist empfängt die Ereignisse der Statusmeldung-Module und leitet diese an die Abonnenten-Module weiter. Sobald eine Instanz vom Typ Abonnement oder Statusmeldung erstellt wird, wird auch ein NotificationController erstellt (sofern noch nicht vorhanden).

### Inhaltverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [WebFront](#6-webfront)
7. [PHP-Befehlsreferenz](#7-php-befehlsreferenz)

### 1. Funktionsumfang

* Ereignis Log für alle von Statusmeldungen empfangenen Ereignisse.
* Ein-/Ausschaltbarkeit der Benachrichtigung via WebFront-Button oder Skript-Funktion.

### 2. Voraussetzungen

- IP-Symcon ab Version 4.1

### 3. Software-Installation

Über das Modul-Control folgende URL hinzufügen.  
`https://github.com/roastedelectrons/IPSNotifications.git`   

### 4. Einrichten der Instanzen in IP-Symcon

- Das Modul wird automatisch unter Kern Instanzen erstellt, wenn ein NotificationSubscription oder StatusMessage Modul erstellt wird.

### 5. Statusvariablen

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

##### Statusvariablen

Name         | Typ       | Beschreibung
------------ | --------- | ----------------
Ereignisse   | String    | Wenn eine Benachrichtigung von Statusmeldung-Modulen empfangenen wird, wird der Text des Ereignisses in die Variable geschrieben. Das Logging dieser Variable wird automatisch aktivert, sodass über das "Graphen" Symbol alle vergangenen Ereignisse angezeigt werden können.
Active       | Boolean   | De-/Aktiviert die Benachrichtigung. Werden die Benachrichtigungen deaktiviert, so werden keine Benachrichtigungen an die Abonneneten weitergeleitet.

##### Profile:

Es werden keine zusätzlichen Profile hinzugefügt

### 6. WebFront

Über das WebFront kann die Benachrichtigung de-/aktiviert werden.
Im Ereignis Log wird immer die letzte Meldung angezeigt. Über die Graphen-Funktion können alle vorheigen Ereignisse angezeigt werden.  


### 7. PHP-Befehlsreferenz


`boolean NCTL_SetActive(integer $InstanzID, boolean $Value);`
Schaltet die Benachrichtigungen mit der InstanzID $InstanzID  auf den Wert $Value (true = An; false = Aus).  
Die Funktion liefert keinerlei Rückgabewert.  
`NCTL_SetActive(12345, true);`
