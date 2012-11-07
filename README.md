Slice Status Addon (aka Slice On/Off) für REDAXO 4
===================================================

Dieses REDAXO-Addon fügt einen On/Offline-Schalter für Artikelblöcke (Slices) hinzu. 
Es ist der Nachfolger des slice_onoff Addons und wurde von Grund auf neu geschrieben.

Download/Installation
---------------------

* Download hier: https://github.com/RexDude/slice_status/downloads
* ZIP-Archiv entpacken und in `/redaxo/include/addons` kopieren, im Backend Addon dann installieren und aktivieren

Features
--------

* Fügt einen Button zum on/offline schalten von Blöcken (Slices) hinzu.
* Offline Blöcke werden im BAckend mit anderer Farbe und geringerer Opacity dargestellt
* Komplett neuer und vereinfachter Code

Änderungen gegenüber Vorversion (slice_onoff 0.3)
-------------------------------------------------

* On/Offline schalten mit Ajax entfernt
* Radiobuttons innerhalb der Blöcke (rechts unten) entfernt
* Es wird nur noch eine If-Abfrage pro Offline-Slice generiert

Hinweise
--------

* Getestet mit REDAXO 4.4.1
* Addon-Ordner lautet: `slice_status`
* Momentan kann Farbe/Opacity der Offline-Slices nur über die `/files/addons/slice_status/functions.js` geändert werden
* Alte Version wurde als Branch archiviert

Todo's
------

* Eigene Backend-Seite mit Einstellungsmöglichkeiten
* Einstellung: Farbe/Opacity der Offline-Slices
* Einstellung: Icon und/oder nur Textlink anzeigen
* Einstellung: Ajax on/off
* Benutzern nur mit Artikel-Schreibrechten On/Offline-Status setzten lassen