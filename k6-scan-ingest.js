import http from 'k6/http';
import { check, sleep } from 'k6';

export const options = {
  vus: 10,
  duration: '30s',
};

export default function () {
  const payload = JSON.stringify({
    scan_id: `scan_${__VU}_${__ITER}`,
    session_id: 'sess_1',
    operator_id: 'op_1',
    partner_id: 'p1',
    device_id: 'd1',
    action: 'receive',
    gps_lat: 28.61,
    gps_lng: 77.20,
    gps_accuracy: 5.2,
    app_version: '1.0.0',
    device_timestamp: new Date().toISOString(),
  });

  const res = http.post('http://127.0.0.1:8000/api/scan/ingest', payload, {
    headers: { 'Content-Type': 'application/json' },
  });

  check(res, {
    'status is expected': (r) => [201, 202, 409, 422].includes(r.status),
  });

  sleep(0.1);
}