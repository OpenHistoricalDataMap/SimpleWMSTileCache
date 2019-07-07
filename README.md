# Demo
https://ohdmcache.f4.htw-berlin.de/demo/cached.html

# Setup
1. Webserver der Wahl installieren - am besten einen der http2 unterstützt
2. PHP7+ mit GD2, BCMath und cURL als Modpackage
3. Dateien hochladen
4. temp und cache Ordner anlegen und beschreibbar für den Webserver machen
5. WMS-Layer, Backend-URL, lokale URL und lokale Ordner in der config.php eintragen
6. ggfls. zusätzliche URL-Parameter für die Caching-Keygeneration eintragen

# ToDo

## Reines Caching
- [x] schneller Cache für WMS-Tiles
- [x] voller Satz WMS-Parameter als Cache-Identifier
- [x] Zusätzliche Parameter konfigurierbar
  - [x] insbesondere das Date
  - [x] GET/COOKIES ?
- [x] Gültigkeitsdauer? --> Aktuell unendlich
  - [ ] das mal kritisch hinterfragen
- [x] Gleiche Tiles nicht doppelt speichern

## Tileprerendering / GroupTiles
- [x] Endstellen definieren
- [x] Layer für Endstelle definieren
- [x] alle Layer für Tile holen + mergen
  - [x] Performance! GD2?
- [x] Ab in den Cache

## Ideen / Ausblick

#### PreRendering / Cachewarming
- [ ] Bereiche als “relevant” definierbar machen
- [ ] Tiles / GroupTiles vorgenerieren
- [ ] Nach Ablauf der Gültigkeit neu generieren?

#### Predictive Preloading
- [ ] Wenn Tile X,Y geladen wurde, wird vermutlich auch Tile X-1,Y X+1,Y etc bald geladen
- [ ] Wenn Zusammenhang Zoomstufen/BoundingBox des Kartenfrontend bekannt: Rein/Rauszoomen der Tiles könnte vorgeladen werden

#### "Intelligentes" Caching
- [ ] Regeln erstellbar machen, unter welchen Umständen nicht-passende Tiles geliefert werden können
  - [ ] Im Jahr 100 war nur Wald, im Jahr 200 auch, also ist sehr wahrscheinlich im Jahr 150 auch Wald gewesen
    - [ ] Liefere Wald, prüfe das Backend aber trotzdem und aktualisiere den Cache
  - [ ] Links ist Wald, Rechts ist Wald, in der Mitte ist vermutlich auch Wald
    - [ ] Achtung: Irgendeine Logik zum nachladen des echten Inhalts ist notwendig!

# LICENSE

Siehe LICENSE-File. Die Files unter /demo/ sind u.U. tlw. geistiges Eigentum der Open Source Geospatial Foundation oder anderer Autoren.
Entsprechende Lizenzhinweise sind zu beachten.
