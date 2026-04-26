## 1) Overview
This project implements a scan ingestion and query service for field scan events.  
Focus areas are ingestion correctness, alerting, and fast stats access.

## 2) Data Model
Core tables: `scan_events`, `alerts`, and `daily_stats`.  
`scan_events` stores event details, `alerts` stores anomaly records, and `daily_stats` stores aggregated counters.

## 3) Ingestion Flow
Request hits `POST /api/scan/ingest` and passes request validation.  
Business checks run in service, then valid events are stored.

## 4) Action Rules
Supported action lifecycle: `receive -> dispatch -> verify -> return`.  
Out-of-sequence actions are rejected as invalid.

## 5) Alerts
Duplicate action for same `scan_id` is marked as `duplicate`.  
Invalid lifecycle action is marked as `invalid_action`.

## 6) Stats Logic
Daily counters are updated through queue job payloads.  
Counters track scans, duplicates, and invalid actions.

## 7) API List
`POST /api/scan/ingest` for ingest processing.  
`GET /api/stats` for latest stats snapshot.

## 8) Error Handling
Standard response shape uses `success`, `message`, `data`, `errors`.  
Main error codes: `VALIDATION_ERROR`, `DUPLICATE_SCAN`, `INVALID_SEQUENCE`, `INTERNAL_ERROR`.

## 9) Queue & Reliability
Queue processing is configured with retries and timeout.  
Failed jobs are tracked using Laravel failed job handling.

## 10) Scale Approach
Processing is separated into synchronous decision + asynchronous stat update.  
Data model and indexing strategy support higher ingest volume.

## 11) Test Summary
Manual tests verified valid flow, duplicate flow, invalid sequence flow, and validation failures.  
Stats endpoint behavior was verified after queue processing.

## 12) AI Usage Notes
AI was used for scaffolding, refactor suggestions, and cleanup proposals.  
Business rule validation and runtime corrections were manually reviewed and adjusted.

## 13) Next Improvements
Add status tracking endpoint for queued processing visibility.  
Add automated feature tests for core scenarios.
Add load-test tuning based on k6 results (worker scaling, DB/index optimization).

## 14) Production Hardening
Add stronger observability and request correlation IDs.  
Harden deployment config for queue workers and retry policies.

## 15) Monitoring & Observability
Track queue depth, job failure trends, and processing latency.  
Set alerts for failure spikes and queue lag thresholds.