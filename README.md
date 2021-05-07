# Custom LMS Joomla - GGallery

CHANGELOG 

F:Frontend, B:Backend, D:Database, G:Generale

4.1.0
-D: aggiunta della colonna usa_coupon alla tabella gg_unit

4.0.9
- D: modificata colonna id_evento delle tabella gg_zoom_report

4.0.8
- D: aggiunta tabella gg_zoom
- D: aggiunta tabella gg_zoom_events
- D: aggiunta della colonna data_primo_accesso nella tabella gg_report
- D: aggiunta della colonna on_sale alla tabella gg_unit
- D: aggiunta della colonna disponibile_dal alla tabella gg_unit
- D: aggiunta della colonna disponibile_al alla tabella gg_unit
- D: aggiunta della colonna prezzo alla tabella gg_unit
- D: aggiunta della colonna sc_a_data alla tabella gg_unit
- D: aggiunta della colonna sc_valore_data alla tabella gg_unit
- D: aggiunta della colonna sc_a_data_gruppi alla tabella gg_unit
- D: aggiunta della colonna sc_valore_data_gruppi alla tabella gg_unit
- D: aggiunta della colonna sc_a_gruppi alla tabella gg_unit
- D: aggiunta della colonna sc_valore_gruppi alla tabella gg_unit
- D: aggiunta della colonna sc_da_data alla tabella gg_unit
- D: aggiunta della colonna sc_custom_cb alla tabella gg_unit
- D: aggiunta della colonna sc_semaforo_custom_cb alla tabella gg_unit
- D: aggiunta della colonna sc_valore_custom_cb alla tabella gg_unit
- G: aggiunta integrazione report Zoom per Meeting e Webinar

4.0.7
- D: aggiunta della colonna data_inizio_extra nella vista _view_report
- D: aggiunta della colonna data_fine_extra nella vista _view_report

4.0.6
- D: aggiunta della colonna data_extra nella tabella gg_report
- D: aggiunta della colonna data_inizio_extra nella tabella gg_view_stato_user_unita
- D: aggiunta della colonna data_fine_extra nella tabella gg_view_stato_user_unita
- D: aggiunta della colonna data_inizio_extra nella tabella gg_view_stato_user_corso
- D: aggiunta della colonna data_fine_extra nella tabella gg_view_stato_user_corso

4.0.5
- F: aggiunta tipologia coupon per la gestione di template customizzati in Genera coupon
- D: aggiunta della colonna tipologia_coupon nella tabella gg_coupon
- D: aggiunta della colonna template nella tabella gg_coupon
- D: aggiunta della colonna attestato nella tabella gg_unit
- D: aggiunta della colonna path_pdf nella tabella gg_contenuti
- D: modifica della colonna messaggio in text nella tabella gg_error_log

4.0.4 
 - G: aggiunta vista webinar
 - G: aggiunto Coupon Dispenser

4.0.3 
 - G: aggiunti campi custom report selezionabili da backend.

4.0.1
 - B: ordinamento unità backend
 
4.0.1
 - G: coupon trial

4.0.0 
  - G: accesso gruppi, ottimizzazione report, aggiunto monitora coupon, form genera coupon frontend, tutor aziendali, tutor piattaforma, venditori, società figlie di piattaforma
  
3.8.50
  - F: gestita iscrizione tramite coupon non solo al gruppo-corso, ma anche al gruppo-azienda

3.8.48
  - B: Aggiornato pannello generazione coupon
  - G: Aggiunta parametrizzazione schermate coupon

3.8.47
  - G: Aggiunta modalita coupon to group per accesso al corso - da migliorare
  
3.8.46
  - F: Tolto redirect automatico alla pagina per corsi con accesso a coupon. 

3.8.45
  - D: aggiunta della colonna timestamp in quiz_deluxe_student_quiz
  - F: implementazione dell'uso del timestamp come riferimento per la data di aggiornamento report 

3.8.44
  - D: aggiunta colonna attestato_path in gg_contenuti
  - B: gestione path-scorm vs path-attestato

3.8.43

  - D: aggiunta colonna orientamento in contenuti
  - B: gestione dell'orientamento attestato
  - F: orienamento attestato

3.8.42
  - G Tracciamento risposte in modalità SCORM (bisogna riscaricare e sostituire cartella scorm) 

3.8.42
  - F inseriti filtri speciali sulla visibilità dei corsi in report
  - F inseriti filtri speciali sulla visibilità dei corsi in report 3.8.41
  - F inserita vista attestati residenziali utente 3.8.40

3.8.42
  - F inseriti filtri speciali sulla visibilità dei corsi in report
  - F inseriti filtri speciali sulla visibilità dei corsi in report 3.8.41
  - F inserita vista attestati residenziali utente 3.8.40

3.8.42
  - B gestione pacchetti multiscorm

3.8.41
  - F inserita vista attestati residenziali utente

3.8.40
  - G FIX bug durata unita
  
3.8.39
  - B implementata funzione duplica contenuto
  
3.8.36
  - G: aggiunto switch per attivare/disattivare la label durata su box unita

3.8.36
  - B: implementata nel back-end la funzione di duplicazione di un corso
  
3.8.35
  - F: implementata e di nuovo attivo il pulsante di scaricamento report in CSV, con le funzioni precedeni di selezione colonne da BE

3.8.34
  - F: inserita vista per stato corso personale, con scaricamento attestato, con relativo controller e model 

3.8.33
  - B: inserito controller per invio mail di alert

3.8.32
   - B: modificata la data di attribuzione del contenuto completato allo timestamp

3.8.31
   - B: modificata la procedura di identificazione del corso completato, con ritorno al contenuto di completamento

3.8.30
   - B: sviluppati model e controller per il caricamento della vista necessaria al csv quotidiano 
   - D: creata la tabella  carige_batch per la vista necessaria al csv quotidiano 

3.8.29
  - F: implemenato il nuovo report appoggiato al modello viste precaricate

3.8.28
  - B: aggiunte le colonne data_inizio e data_fine alle due nuove viste 

3.8.27
  - B: implementata la coppia controller/model syncviewstatouser per la creazione delle viste per il stato di completamento unità e corso da parte dell'utente
       E' prevista l'esecuzione tramite task e tramite script
  - D: create le due tabelle di vista per stato corso  unità dell'utente     

3.8.26
  - B: Abilitato il metodo sync() in modo da poter utilizzare il cron
  - F: Abilitato BookMark per contenuti solo video e videoslide

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
