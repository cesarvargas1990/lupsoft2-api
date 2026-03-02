#!/usr/bin/env bash
set -euo pipefail

BASE_URL="${BASE_URL:-http://147.93.1.252:8002}"
ADMIN_EMAIL="${ADMIN_EMAIL:-admin@admin.com}"
ADMIN_PASSWORD="${ADMIN_PASSWORD:-password}"
REPORT_FILE="${REPORT_FILE:-logs/api_real_query_tests_report.txt}"

if ! command -v curl >/dev/null 2>&1; then
  echo "curl is required"
  exit 1
fi

if ! command -v jq >/dev/null 2>&1; then
  echo "jq is required"
  exit 1
fi

mkdir -p "$(dirname "$REPORT_FILE")"
: > "$REPORT_FILE"

pass=0
fail=0
TOKEN=""

log() {
  printf "%s\n" "$1" | tee -a "$REPORT_FILE"
}

run_case() {
  local name="$1"
  local method="$2"
  local path="$3"
  local expected_status="$4"
  local body="${5:-}"
  local auth_mode="${6:-auth}"
  local assert_expr="${7:-}"

  local url="${BASE_URL}${path}"
  local tmp_body tmp_headers
  tmp_body="$(mktemp)"
  tmp_headers="$(mktemp)"

  local -a headers
  headers=(-H "Accept: application/json")
  if [[ -n "$body" ]]; then
    headers+=(-H "Content-Type: application/json")
  fi
  if [[ "$auth_mode" == "auth" && -n "$TOKEN" ]]; then
    headers+=(-H "Authorization: Bearer $TOKEN")
  fi

  local status
  if [[ -n "$body" ]]; then
    status="$(curl -sS -D "$tmp_headers" -o "$tmp_body" -X "$method" "$url" "${headers[@]}" -d "$body" -w "%{http_code}")"
  else
    status="$(curl -sS -D "$tmp_headers" -o "$tmp_body" -X "$method" "$url" "${headers[@]}" -w "%{http_code}")"
  fi

  local body_one_line
  body_one_line="$(tr '\n' ' ' < "$tmp_body" | tr -s ' ' | cut -c1-260)"

  local ok=true
  if [[ "$status" != "$expected_status" ]]; then
    ok=false
  fi

  if [[ "$ok" == true && -n "$assert_expr" ]]; then
    if ! jq -e "$assert_expr" "$tmp_body" >/dev/null 2>&1; then
      ok=false
    fi
  fi

  if [[ "$ok" == true ]]; then
    pass=$((pass+1))
    log "[PASS] $name | $method $path | status=$status"
  else
    fail=$((fail+1))
    log "[FAIL] $name | $method $path | expected=$expected_status got=$status | body=$body_one_line"
  fi

  rm -f "$tmp_body" "$tmp_headers"
}

# Login success and save token
login_body="{\"email\":\"$ADMIN_EMAIL\",\"password\":\"$ADMIN_PASSWORD\"}"
login_resp="$(mktemp)"
login_status="$(curl -sS -o "$login_resp" -X POST "$BASE_URL/auth/login" -H "Content-Type: application/json" -d "$login_body" -w "%{http_code}")"
if [[ "$login_status" == "200" ]]; then
  TOKEN="$(jq -r '.access_token // empty' "$login_resp")"
fi
if [[ -z "$TOKEN" ]]; then
  fail=$((fail+1))
  login_preview="$(tr '\n' ' ' < "$login_resp" | cut -c1-260)"
  log "[FAIL] login bootstrap | expected=200 with access_token | got=$login_status | body=$login_preview"
  rm -f "$login_resp"
  log "Summary: pass=$pass fail=$fail"
  exit 1
fi
pass=$((pass+1))
log "[PASS] login bootstrap | token acquired"
rm -f "$login_resp"

# Auth negative cases
run_case "login invalid credentials" POST "/auth/login" "401" '{"email":"admin@admin.com","password":"wrong_password"}' "noauth" '.message == "Unauthorized"'
run_case "login missing password" POST "/auth/login" "422" '{"email":"admin@admin.com"}' "noauth" '.message != null'
run_case "unauthorized access blocked" GET "/profile" "401" "" "noauth"

# Success query/read cases
run_case "profile success" GET "/profile" "200" "" "auth" '.user.email == "admin@admin.com"'
run_case "users list success" GET "/users" "200" "" "auth" '.users | type == "array"'
run_case "cobradores success" GET "/cobradores/1" "200" "" "auth" 'type == "array"'
run_case "tipos doc success" GET "/pstipodocidenti" "200" "" "auth" 'type == "array"'
run_case "lista clientes success" GET "/listadoclientes/1" "200" "" "auth" 'type == "array"'
run_case "consulta tipo doc plantilla success" POST "/consultaTipoDocPlantilla" "200" '{"id_empresa":1}' "auth" 'type == "array"'
run_case "calcular cuotas success" POST "/calcularCuotas" "200" '{"id_periodo_pago":1,"id_sistema_pago":1,"numcuotas":12,"porcint":2.5,"valorpres":1000000}' "auth" 'type == "array"'
run_case "listado prestamos success" POST "/listadoPrestamos" "200" '{"id_empresa":1}' "auth" 'type == "array"'
run_case "prestamos cliente success" POST "/prestamosCliente" "200" '{"id_empresa":1,"id_cliente":1}' "auth" 'type == "array"'
run_case "render templates success" POST "/renderTemplates" "200" '{"id_empresa":1,"id_prestamo":1}' "auth" 'type == "array"'
run_case "totales dashboard success" POST "/totales_dashboard" "200" '{"id_empresa":1,"fecha":"2026-02-27"}' "auth" '.total_prestado != null'
run_case "total interes success" POST "/totalinteres" "201" '{"id_empresa":1}' "auth"

# Negative/validation cases for query endpoints
run_case "cobradores invalid id type" GET "/cobradores/abc" "422" "" "auth" '.message == "Route params validation failed"'
run_case "consulta tipo doc plantilla missing field" POST "/consultaTipoDocPlantilla" "422" '{}' "auth" '.message == "Request body validation failed"'
run_case "consulta tipo doc plantilla invalid type" POST "/consultaTipoDocPlantilla" "422" '{"id_empresa":"empresa"}' "auth" '.message == "Request body validation failed"'
run_case "calcular cuotas missing numcuotas" POST "/calcularCuotas" "422" '{"id_periodo_pago":1,"porcint":2.5,"valorpres":1000000}' "auth" '.message == "Request body validation failed"'
run_case "calcular cuotas invalid type" POST "/calcularCuotas" "422" '{"id_periodo_pago":"abc","numcuotas":12,"porcint":2.5,"valorpres":1000000}' "auth" '.message == "Request body validation failed"'
run_case "listado prestamos missing id_empresa" POST "/listadoPrestamos" "422" '{}' "auth" '.message == "Request body validation failed"'
run_case "prestamos cliente missing id_cliente" POST "/prestamosCliente" "422" '{"id_empresa":1}' "auth" '.message == "Request body validation failed"'
run_case "render templates invalid id_prestamo" POST "/renderTemplates" "422" '{"id_empresa":1,"id_prestamo":"abc"}' "auth" '.message == "Request body validation failed"'
run_case "totales dashboard missing fecha" POST "/totales_dashboard" "422" '{"id_empresa":1}' "auth" '.message == "Request body validation failed"'

# Clean logout
run_case "logout success" POST "/auth/logout" "200" "" "auth" '.message != null'

log "Summary: pass=$pass fail=$fail"

if [[ "$fail" -gt 0 ]]; then
  exit 1
fi
