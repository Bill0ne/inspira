#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
ARTISAN="$ROOT_DIR/artisan"

if [[ ! -f "$ARTISAN" ]]; then
  echo "Fehler: artisan nicht gefunden unter $ARTISAN" >&2
  exit 1
fi

MIGRATIONS=(
  "platform/plugins/hotel/database/migrations/2025_11_10_000001_create_ht_customer_cards_table.php"
  "platform/plugins/hotel/database/migrations/2025_11_10_000002_create_ht_customer_card_usages_table.php"
  "platform/plugins/hotel/database/migrations/2025_11_10_000003_add_accept_customer_card_to_courses_table.php"
  "platform/plugins/hotel/database/migrations/2025_11_10_000004_add_customer_card_columns_to_bookings_table.php"
  "platform/plugins/hotel/database/migrations/2025_11_10_000005_update_ht_customer_cards_assigned_to_foreign.php"
)

usage() {
  cat <<USAGE
Verwendung: ${0##*/} [install|rollback]

Ohne Parameter wird 'install' ausgeführt.
  install   - führt alle Kundenkarten-Migrationen aus
  rollback  - macht die Kundenkarten-Migrationen in umgekehrter Reihenfolge rückgängig
USAGE
}

action=${1:-install}
case "$action" in
  install|up)
    action="install"
    ;;
  rollback|down|uninstall)
    action="rollback"
    ;;
  -h|--help)
    usage
    exit 0
    ;;
  *)
    echo "Unbekannte Option: $action" >&2
    usage
    exit 1
    ;;
esac

for path in "${MIGRATIONS[@]}"; do
  if [[ ! -f "$ROOT_DIR/$path" ]]; then
    echo "Fehler: Migration nicht gefunden: $path" >&2
    exit 1
  fi
  if [[ ! -r "$ROOT_DIR/$path" ]]; then
    echo "Fehler: Keine Leserechte für Migration: $path" >&2
    exit 1
  fi
fi

echo "==> Kundenkarten-Migrationen ($action)"

if [[ "$action" == "install" ]]; then
  for path in "${MIGRATIONS[@]}"; do
    echo "-- php artisan migrate --path=$path"
    php "$ARTISAN" migrate --force --path="$path"
  done
else
  for (( idx=${#MIGRATIONS[@]}-1 ; idx>=0 ; idx-- )); do
    path="${MIGRATIONS[idx]}"
    echo "-- php artisan migrate:rollback --path=$path --step=1"
    php "$ARTISAN" migrate:rollback --force --path="$path" --step=1 || true
  done
fi

echo "==> Cache wird geleert"
php "$ARTISAN" cache:clear >/dev/null 2>&1 || true
php "$ARTISAN" config:clear >/dev/null 2>&1 || true
php "$ARTISAN" view:clear >/dev/null 2>&1 || true

if [[ "$action" == "install" ]]; then
  echo "Fertig: Kundenkarten-Migrationen wurden installiert."
else
  echo "Fertig: Kundenkarten-Migrationen wurden zurückgesetzt."
fi
