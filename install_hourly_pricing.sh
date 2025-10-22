#!/usr/bin/env bash
set -euo pipefail

PLUG="platform/plugins/hotel"
MIGR_DIR="$PLUG/database/migrations"
MODELS_DIR="$PLUG/src/Models"
ENUMS_DIR="$PLUG/src/Enums"
HTTP_DIR="$PLUG/src/Http"
CONTROLLERS_DIR="$HTTP_DIR/Controllers"
REQUESTS_DIR="$HTTP_DIR/Requests"
MIGR_FILE="${MIGR_DIR}/2025_09_24_000001_add_start_end_at_to_ht_booking_rooms.php"
BACKUP_DIR="backup_hourly_installer_$(date +%Y%m%d_%H%M%S)"
ARTISAN="./artisan"

echo "==> Hourly Pricing Installer startet…"
for p in "$PLUG" "$MODELS_DIR" "$ENUMS_DIR" "$CONTROLLERS_DIR" "$REQUESTS_DIR" "$MIGR_DIR"; do
  [ -d "$p" ] || { echo "FEHLER: Verzeichnis fehlt: $p"; exit 1; }
done
mkdir -p "$BACKUP_DIR"

backup_file () { [ -f "$1" ] && cp -f "$1" "$BACKUP_DIR/${1//\//__}" && echo "Backup: $1"; }

# Migration
if [ ! -f "$MIGR_FILE" ]; then
mkdir -p "$MIGR_DIR"
cat > "$MIGR_FILE" <<'PHP'
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('ht_booking_rooms')) {
            Schema::table('ht_booking_rooms', function (Blueprint $table) {
                if (!Schema::hasColumn('ht_booking_rooms', 'start_at')) $table->dateTime('start_at')->nullable()->after('start_date');
                if (!Schema::hasColumn('ht_booking_rooms', 'end_at'))   $table->dateTime('end_at')->nullable()->after('end_date');
            });
        }
    }
    public function down(): void {
        if (Schema::hasTable('ht_booking_rooms')) {
            Schema::table('ht_booking_rooms', function (Blueprint $table) {
                if (Schema::hasColumn('ht_booking_rooms', 'start_at')) $table->dropColumn('start_at');
                if (Schema::hasColumn('ht_booking_rooms', 'end_at'))   $table->dropColumn('end_at');
            });
        }
    }
};
PHP
fi

# BookingRoom.php
BR="$MODELS_DIR/BookingRoom.php"
if [ -f "$BR" ]; then
  backup_file "$BR"
  perl -0777 -pe "s/(protected\s+\$fillable\s*=\s*\[[^\]]*)\]/\1,\n        'start_at',\n        'end_at'\n    ]/s" -i "$BR" || true
  if grep -q "protected\s+\$casts" "$BR"; then
    perl -0777 -pe "s/(protected\s+\$casts\s*=\s*\[[^\]]*)\]/\1,\n        'start_at' => 'datetime',\n        'end_at' => 'datetime'\n    ]/s" -i "$BR" || true
  else
    perl -0777 -pe "s/(class\s+BookingRoom[^{]*\{)/\1\n\n    protected \$casts = [\n        'start_at' => 'datetime',\n        'end_at'   => 'datetime',\n    ];\n/s" -i "$BR" || true
  fi
fi

# Enum
ENUM="$ENUMS_DIR/ServicePriceTypeEnum.php"
if [ -f "$ENUM" ]; then
  backup_file "$ENUM"
  perl -0777 -pe "s@(\*\s*\@method\s+static\s+ServicePriceTypeEnum\s+PER_DAY\(\))@\1\n * @method static ServicePriceTypeEnum PER_HOUR()@s" -i "$ENUM" || true
  perl -0777 -pe "s/(public\s+const\s+PER_DAY\s*=\s*'per_day';)/\1\n    public const PER_HOUR = 'per_hour';/s" -i "$ENUM" || true
fi

# Room.php
ROOM="$MODELS_DIR/Room.php"
if [ -f "$ROOM" ]; then
  backup_file "$ROOM"
  grep -q "use Carbon\\Carbon;" "$ROOM" || perl -0777 -pe "s/(namespace\s+[^;]+;)/\1\nuse Carbon\\\\Carbon;/" -i "$ROOM"
  grep -q "getRoomTotalPriceByHours" "$ROOM" || perl -0777 -pe "s@(\}\s*\Z)@    public function getRoomTotalPriceByHours(string|\\DateTimeInterface \$startAt, string|\\DateTimeInterface \$endAt, int \$rooms = 1): float|int\n    {\n        \$rooms = max(1, (int) \$rooms);\n        \$start = \$startAt instanceof \\DateTimeInterface ? Carbon::instance(\$startAt) : Carbon::parse(\$startAt);\n        \$end   = \$endAt   instanceof \\DateTimeInterface ? Carbon::instance(\$endAt)   : Carbon::parse(\$endAt);\n        \$hours = max(1, \$start->diffInHours(\$end));\n        return (float) \$this->price * \$hours * \$rooms;\n    }\n\n}\n@s" -i "$ROOM"
fi

# Request
REQ="$REQUESTS_DIR/CalculateBookingAmountRequest.php"
if [ -f "$REQ" ]; then
  backup_file "$REQ"
  grep -q "'start_time'" "$REQ" || perl -0777 -pe "s@(return\s*\[)@\1\n            'start_time' => ['nullable', 'date_format:H:i'],\n            'end_time'   => ['nullable', 'date_format:H:i'],@s" -i "$REQ"
fi

# Controller
CTRL="$CONTROLLERS_DIR/PublicController.php"
if [ -f "$CTRL" ]; then
  backup_file "$CTRL"
  grep -q "use Carbon\\Carbon;" "$CTRL" || perl -0777 -pe "s/(namespace\s+[^;]+;)/\1\nuse Carbon\\\\Carbon;/" -i "$CTRL"
  grep -q "\[HourlyPricingStart\]" "$CTRL" || perl -0777 -pe "s@(function\s+calculateBookingAmount\s*\([^)]*\)\s*\{)@\1\n        // [HourlyPricingStart]\n        \$startDate = \$request->input('start_date');\n        \$endDate   = \$request->input('end_date');\n        \$startTime = \$request->input('start_time');\n        \$endTime   = \$request->input('end_time');\n        \$defaultStartTime = '09:00';\n        \$defaultEndTime   = '17:00';\n        \$startAt = trim(((string)\$startDate) . ' ' . ((string)\$startTime ?: \$defaultStartTime));\n        \$endAt   = trim(((string)\$endDate)   . ' ' . ((string)\$endTime   ?: \$defaultEndTime));\n        try { \$startAtC = Carbon::parse(\$startAt); \$endAtC = Carbon::parse(\$endAt); } catch (\\Exception \$e) { \$startAtC = Carbon::now()->startOfDay()->setTime(9,0); \$endAtC = (clone \$startAtC)->setTime(17,0); }\n        if (\$endAtC->lessThanOrEqualTo(\$startAtC)) { \$endAtC = (clone \$startAtC)->addHour(); }\n        \$hours = max(1, \$startAtC->diffInHours(\$endAtC));\n        // [HourlyPricingEnd]\n@s" -i "$CTRL"
  grep -q "getRoomTotalPriceByHours" "$CTRL" || perl -0777 -pe "s@(\$amount\s*=\s*\$room->[^;]+;)@// Stundenbasis:\n        \$amount = \$room->getRoomTotalPriceByHours(\$startAtC, \$endAtC, (int) (\$request->input('number_of_rooms') ?: 1));\n        // \1@ss" -i "$CTRL"
  grep -q "PER_HOUR" "$CTRL" || perl -0777 -pe "s@(ServicePriceTypeEnum::PER_DAY[^;]+;)@\${0}\n            } elseif (\$service->price_type == ServicePriceTypeEnum::PER_HOUR) {\n                \$serviceAmount += (float) \$service->price * (int) (\$hours);@s" -i "$CTRL"
  grep -q "'hours'" "$CTRL" || perl -0777 -pe "s@(return\s*\[)@\1\n            'hours' => (int) (\$hours),@s" -i "$CTRL"
fi

echo "==> artisan migrate & Caches…"
php "$ARTISAN" migrate --force || echo "WARNUNG: migrate fehlgeschlagen – bitte prüfen."
php "$ARTISAN" cache:clear || true
php "$ARTISAN" config:clear || true
php "$ARTISAN" view:clear || true

echo "Fertig. Backups unter: $BACKUP_DIR"
