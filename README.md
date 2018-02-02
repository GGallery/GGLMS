# Custom LMS Joomla - GGallery

CHANGELOG 

F:Frontend, B:Backend, D:Database, G:Generale

3.8.25
  - F: Ottimizzato integrazione SCORM
  - B: aggiunta ricerca su campo id utente

3.8.24
  - F: tolto il check sul tempo per l'aggiornamento della tabella Report
  - B: inserita nel caricamento della tabella report la procedura di caricamento degli iscritti al corso ma privi di azioni
       reso effetivo e funzionante  il caricamento nella tabella error_log dei log (non errori) durante la procedura di caricamento della tabella report

3.8.23
  - B: creata la gestione drag&drop dell'ordinamento dei contenuti da backend

3.8.22
  - D: aggiunte le colonne dei tempi nella tabella csv_report
  - B: creata nel tab report la gestione da background della visibilità nel report delle colonne tempi
  - F: inserite nel report le due colonne dei tempi impiegati, riprodotte anche nel CSV
3.8.21
  - B inserito nel back end la possibilità di scegliere quali colonne aggiungere alla creazione del csv da report

3.8.20
  -F tolto commento da pubblicato=1 dl model di unita

3.8.19
  - B inserita in modalità admin scelta colonna nome e cognome per le diverse configurazioni anagrafiche, corretto bug limit su symc_report

3.8.18
  - B aggiunte nel pulsante di scaricamento tabelle report funzione pulizia tabelle scormvars e unit_map

3.8.17
  - D: creata la tabella gg_log
  - B: inserita registrazione log utente, con gestione admin
       inserita possibilità assenza modulo quiz-deluxe    

3.8.16
  - F: inserito tooltip per libretto in report
  - B: corretto bug blocco caricamento a 400

3.8.15
  - B: aggiunta vista Libretto Formativo, ottenibile anche da report, con relativo pdf.  

3.8.14
  - B: non vengono più caricati a report i corsi non pubblicati

3.8.13
  - B: reso default gruppo registered su configurazione administrator
  
3.8.12
  - F: aggiunta vista dettagli corso
       aggiunto alert scadenza
       aggiunto pulsante mail individuale
       aggiunto pulsante mail complessiva
       aggiunto pulsante caricameto tabella report
       filtro effettivo su gruppi utenti scelti da admin
  - B: aggiornata logica popolamento report
       introduzione Jquery.Get per popolamento tabella report
       aggiornata logica popolamento csv
       agganciata logica ricerca per data a data di completamento
       inseriti per admin giorni alter e secondi minimi per caricamento report
  - D: aggiunta tabella #__gg_csv_report
       aggiunte due colonne alla #__gg_unit      
       

3.8.11
   - F: Aggiunto auto sync report
   - B: Aggiunto auto sync report
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