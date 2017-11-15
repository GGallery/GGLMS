# Custom LMS Joomla - GGallery

CHANGELOG 

F:Frontend, B:Backend, D:Database, G:Generale

3.8.10
   - F: Aggiunta vista Coupon
   - G: Folder Scorm convertita a zip scaricabile

3.8.7/8/9
   - F: FIX bug frontend

3.8.6
 - F: Controllo accesso al corso mediante appartenenza al gruppo
 - B: Controllo accesso al corso mediante appartenenza al gruppo
 - B: Scelta gruppi utilizzabili nell'accesso ai corso anche per dashboard in frontend
 - D: Aggiunta tabella #__gg_usergroup_map

3.8.5
 - F: Layout VideoSlide tolto margine in css a boxvideo e boxslide

3.8.4
 - F: Corretto baco per il quale l'accesso al corso in modalità coupon_eb non teneva conto del campo corsi_abilitati

3.8.3
 - B: Sistemato bug prerequisiti
 - F: Aggiunto scroll su jumper

3.8.2
 - B: Aggiunto tab attestato in configuration

3.8.1
 - F: Sistemato visualizzazione slide 

3.8.0
 - B: Sistemata paginazione utenti e unita
 - F: Creata vista Report

3.7.11
 - G: sistemato UploadHandler - Rimosse chiamate FireBug e require_once
 - F: implementati Report
 - D: Aggiornate manualmente tabella scormars con l'aggiunta della colonna timestamp
 - D: Creata tabella report

3.7.10
 - G: integrato CB

3.7.8/9
 - F: Sistemato controllo sulla modalità di accesso in unità Root

3.7.7
 - D: Rimosso Trigger per proteggere unita 1 e allineamento DB - Faceva casino perchè i DB non erano uguali

3.7.6
 - D: Aggiunto Trigger per proteggere unita 1

3.7.5
 - BF: Aggiunta classe DEBUGG
 - B: Auto Alias
 - G: Aggiunte immagini temporane se mancanti
 - G: Definizione tipologia UNITA - CORSO
 - F: Aggiunto CSS Custom
 - F: Aggiunta visualizzazione file allegati in vista Contenuto Testuale
 - D: Variazioni Tabella Unit: aggiunto campo is_corso, id_contenuto_completamento

3.7.4
 - B: Schermate Unita e Contenuti, campi compilabili solo dopo salvataggio del titolo

3.7.3 
 - D: Aggiunta colonna alla tabella unit