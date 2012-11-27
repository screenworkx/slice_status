Slice Status Addon (aka Slice On/Off) für REDAXO 4
===================================================

Dieses REDAXO-Addon fügt einen On/Offline-Schalter für Artikelblöcke (Slices) hinzu. 
Es ist der Nachfolger des slice_onoff Addons und wurde von Grund auf neu geschrieben.

Download/Installation
---------------------

* Download unter _Download Packages_: https://github.com/RexDude/slice_status/downloads
* ZIP-Archiv entpacken und in `/redaxo/include/addons` kopieren, im Backend Addon dann installieren und aktivieren

Features
--------

* Fügt einen Button zum on/offline schalten von Blöcken (Slices) hinzu.
* Offline Blöcke werden im Backend mit anderer Farbe und geringerer Opacity dargestellt
* Aussehen kann komplett über CSS geändert werden
* AJAX Modus ein/ausschaltbar (in der config.inc.php)
* Komplett neuer und vereinfachter Code

Änderungen gegenüber Vorversion (slice_onoff 0.3)
-------------------------------------------------

* Läuft auch ohne AJAX
* Radiobuttons innerhalb der Blöcke (rechts unten) entfernt
* On/Off Button hinter die Move Up/Down Buttons gesetzt
* Es wird nur noch eine If-Abfrage pro Offline-Slice generiert
* Kompletter Code Rewrite

Wechsel von slice_onoff auf slice_status
----------------------------------------

1. `slice_status` installieren. Es werden automatisch die Daten von `slice_onoff` importiert.
2. `slice_onoff` deinstallieren/löschen.

Will man keinen Import so deinstalliert man `slice_onoff` zuvor.

Hinweise
--------

* Getestet mit REDAXO 4.4.1
* Addon-Ordner lautet: `slice_status`
* AJAX Modus auschaltbar über die config.inc.php
* Farbe/Opacity der Offline-Slices änderbar in `/files/addons/slice_status/slice_status.css`
* Alte Version wurde als Branch archiviert

Todo's
------

* Eigene Backend-Seite mit Einstellungsmöglichkeiten
* Benutzern nur mit Artikel-Schreibrechten On/Offline-Status setzten lassen
* Online von/bis Funktionalität hinzufügen: https://github.com/RexDude/slice_status/issues/2

Icons
-----

Die Icons sind den <a href="http://www.famfamfam.com/lab/icons/silk/">FamFamFam Silk Icons</a> entnommen.
