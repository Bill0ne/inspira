#!/usr/bin/env bash
set -euo pipefail

# ---- CONFIG ----
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
# --------------

echo "==> Hourly Pricing Installer (sed/awk only) startet…"

for p in "$PLUG" "$MODELS_DIR" "$ENUMS_DIR" "$CONTROLLERS_DIR" "$REQUESTS_DIR" "$MIGR_DIR"; do
  [ -d "$p" ] || { echo "FEHLER: Verzeichnis fehlt: $p"; exit 1; }
done

mkdir -p "$BACKUP_DIR"

backup_file () {
  local f="$1"
  [ -f "$f" ] && cp -f "$f" "$BACKUP_DIR/${f//\//__}" && echo "Backup: $f"
}

# 1) Migration anlegen
if [ ! -f "$MIGR_FILE" ]; then
  echo "Lege Migration an: $MIGR_FILE"
  mkdir -p "$MIGR_DIR"
  cat > "$MIGR_FILE" <<'PHP'
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ht_booking_rooms')) {
            Schema::table('ht_booking_rooms', function (Blueprint $table) {
                if (!Schema::hasColumn('ht_booking_rooms', 'start_at')) {
                    $table->dateTime('start_at')->nullable()->after('start_date');
                }
                if (!Schema::hasColumn('ht_booking_rooms', 'end_at')) {
                    $table->dateTime('end_at')->nullable()->after('end_date');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ht_booking_rooms')) {
            Schema::table('ht_booking_rooms', function (Blueprint $table) {
                if (Schema::hasColumn('ht_booking_rooms', 'start_at')) {
                    $table->dropColumn('start_at');
                }
                if (Schema::hasColumn('ht_booking_rooms', 'end_at')) {
                    $table->dropColumn('end_at');
                }
            });
        }
    }
};
PHP
else
  echo "Migration existiert bereits: $MIGR_FILE"
fi

# 2) BookingRoom.php (fillable + casts)
BR="$MODELS_DIR/BookingRoom.php"
if [ -f "$BR" ]; then
  backup_file "$BR"

  # fillable ergänzen, wenn nicht vorhanden
  if ! grep -q "['\"]start_at['\"]" "$BR"; then
    awk '
      BEGIN{infill=0}
      /protected[ \t]+\$fillable[ \t]*=[ \t]*\[/ {infill=1}
      {print}
      infill && /\]/ && !printed {
        print "        ,'\''start_at'\'','\''end_at'\''"
        printed=1; infill=0
      }
    ' "$BR" > "$BR.new" && mv "$BR.new" "$BR"
    echo "BookingRoom.php: fillable erweitert (start_at, end_at)"
  else
    echo "BookingRoom.php: fillable bereits ok"
  fi

  # casts setzen/erweitern
  if grep -q "protected[ \t]+\$casts" "$BR"; then
    if ! grep -q "['\"]start_at['\"][ \t]*=>[ \t]*['\"]datetime['\"]" "$BR"; then
      awk '
        BEGIN{incasts=0}
        /protected[ \t]+\$casts[ \t]*=[ \t]*\[/ {incasts=1}
        {print}
        incasts && /\]/ && !printed {
          print "        ,'\''start_at'\'': '\''datetime'\'','\''end_at'\'': '\''datetime'\'''"
          printed=1; incasts=0
        }
      ' "$BR" > "$BR.new" && mv "$BR.new" "$BR"
      echo "BookingRoom.php: casts erweitert (start_at, end_at)"
    else
      echo "BookingRoom.php: casts bereits ok"
    fi
  else
    # neuen casts-Block nach Klassendeklaration einfügen
    awk '
      BEGIN{done=0}
      /^class[ \t]+BookingRoom/ && done==0 {
        print; print "";
        print "    protected $casts = ["
        print "        '\''start_at'\'': '\''datetime'\'',"
        print "        '\''end_at'\'': '\''datetime'\''," 
        print "    ];"
        next
      }
      {print}
    ' "$BR" > "$BR.new" && mv "$BR.new" "$BR"
    echo "BookingRoom.php: casts-Block hinzugefügt"
  fi
else
  echo "WARNUNG: BookingRoom.php nicht gefunden"
fi

# 3) Enum: PER_HOUR Konstante hinzufügen
ENUM="$ENUMS_DIR/ServicePriceTypeEnum.php"
if [ -f "$ENUM" ]; then
  backup_file "$ENUM"
  if ! grep -q "PER_HOUR" "$ENUM"; then
    # nach PER_DAY Konstante eine neue Zeile einfügen
    sed -i "/public[ \t]*const[ \t]*PER_DAY[ \t]*=[ \t]*'per_day';/a\    public const PER_HOUR = 'per_hour';" "$ENUM"
    echo "ServicePriceTypeEnum.php: PER_HOUR hinzugefügt"
  else
    echo "ServicePriceTypeEnum.php: PER_HOUR bereits vorhanden"
  fi
else
  echo "WARNUNG: ServicePriceTypeEnum.php nicht gefunden"
fi

# 4) Room.php: Carbon use + Methode einfügen
ROOM="$MODELS_DIR/Room.php"
if [ -f "$ROOM" ]; then
  backup_file "$ROOM"

  # use Carbon\Carbon; importieren
  if ! grep -q "^use[ \t]\+Carbon\\\\Carbon;" "$ROOM"; then
    sed -i "0,/^namespace/s//&\
use Carbon\\\\Carbon;/" "$ROOM"
    echo "Room.php: Carbon importiert"
  fi

  # Methode hinzufügen, falls nicht vorhanden
  if ! grep -q "function[ \t]\+getRoomTotalPriceByHours" "$ROOM"; then
    awk '
      BEGIN{inserted=0}
      {
        line[NR]=$0
      }
      END{
        # finde letzte Zeile mit nur } und füge davor die Methode ein
        last=NR
        for (i=NR; i>=1; i--) {
          if (match(line[i], /^[ \t]*}\s*$/)) { last=i; break }
        }
        for (i=1; i<=NR; i++) {
          if (i==last) {
            print "    /**"
            print "     * Stundenbasierte Preisberechnung (interpretiert $this->price als Preis pro Stunde)"
            print "     */"
            print "    public function getRoomTotalPriceByHours(string|\\DateTimeInterface $startAt, string|\\DateTimeInterface $endAt, int $rooms = 1): float|int"
            print "    {"
            print "        $rooms = max(1, (int) $rooms);"
            print "        $start = $startAt instanceof \\DateTimeInterface ? Carbon::instance($startAt) : Carbon::parse($startAt);"
            print "        $end   = $endAt   instanceof \\DateTimeInterface ? Carbon::instance($endAt)   : Carbon::parse($endAt);"
            print "        $hours = max(1, $start->diffInHours($end));"
            print "        return (float) $this->price * $hours * $rooms;"
            print "    }"
          }
          print line[i]
        }
      }
    ' "$ROOM" > "$ROOM.new" && mv "$ROOM.new" "$ROOM"
    echo "Room.php: getRoomTotalPriceByHours() hinzugefügt"
  else
    echo "Room.php: Methode bereits vorhanden"
  fi
else
  echo "WARNUNG: Room.php nicht gefunden"
fi

# 5) Request: start_time / end_time Regeln ergänzen
REQ="$REQUESTS_DIR/CalculateBookingAmountRequest.php"
if [ -f "$REQ" ]; then
  backup_file "$REQ"
  if ! grep -q "'start_time'" "$REQ"; then
    sed -i "0,/return[ \t]*\[/s//&\
            'start_time' => ['nullable', 'date_format:H:i'],\
            'end_time'   => ['nullable', 'date_format:H:i'],/" "$REQ"
    echo "CalculateBookingAmountRequest.php: Regeln ergänzt (start_time, end_time)"
  else
    echo "CalculateBookingAmountRequest.php: Regeln bereits vorhanden"
  fi
else
  echo "WARNUNG: CalculateBookingAmountRequest.php nicht gefunden"
fi

# 6) PublicController: Hourly-Block, Amount-Override, PER_HOUR-Services, hours in Response
CTRL="$CONTROLLERS_DIR/PublicController.php"
if [ -f "$CTRL" ]; then
  backup_file "$CTRL"

  # Carbon import
  if ! grep -q "^use[ \t]\+Carbon\\\\Carbon;" "$CTRL"; then
    sed -i "0,/^namespace/s//&\
use Carbon\\\\Carbon;/" "$CTRL"
    echo "PublicController.php: Carbon importiert"
  fi

  # Hourly-Startblock in calculateBookingAmount()
  if ! grep -q "\[HourlyPricingStart\]" "$CTRL"; then
    awk '
      /function[ \t]+calculateBookingAmount[ \t]*\(/ && open==0 { open=1; print; next }
      open==1 && /\{/ && !printed { 
        printed=1; 
        print "{"
        print "        // [HourlyPricingStart]"
        print "        $startDate = $request->input('\''start_date'\'');"
        print "        $endDate   = $request->input('\''end_date'\'');"
        print "        $startTime = $request->input('\''start_time'\'');"
        print "        $endTime   = $request->input('\''end_time'\'');"
        print "        $defaultStartTime = '\''09:00'\'';"
        print "        $defaultEndTime   = '\''17:00'\'';"
        print "        $startAt = trim(((string)$startDate) . '\'' '\'' . ((string)$startTime ?: $defaultStartTime));"
        print "        $endAt   = trim(((string)$endDate)   . '\'' '\'' . ((string)$endTime   ?: $defaultEndTime));"
        print "        try { $startAtC = Carbon::parse($startAt); $endAtC = Carbon::parse($endAt); }"
        print "        catch (\\Exception $e) { $startAtC = Carbon::now()->startOfDay()->setTime(9,0); $endAtC = (clone $startAtC)->setTime(17,0); }"
        print "        if ($endAtC->lessThanOrEqualTo($startAtC)) { $endAtC = (clone $startAtC)->addHour(); }"
        print "        $hours = max(1, $startAtC->diffInHours($endAtC));"
        print "        // [HourlyPricingEnd]"
        next 
      }
      { print }
    ' "$CTRL" > "$CTRL.new" && mv "$CTRL.new" "$CTRL"
    echo "PublicController.php: Hourly-Block injiziert"
  else
    echo "PublicController.php: Hourly-Block bereits vorhanden"
  fi

  # Amount-Override: vor erster $amount = $room->... Zeile unsere stundenbasierte setzen
  if ! grep -q "getRoomTotalPriceByHours" "$CTRL"; then
    awk '
      BEGIN{done=0}
      /\$amount[ \t]*=/ && /\\$room->/ && done==0 {
        print "        // Stundenbasis (override)"
        print "        $amount = $room->getRoomTotalPriceByHours($startAtC, $endAtC, (int) ($request->input('\''number_of_rooms'\'') ?: 1));"
        print "        // (Originalzeile folgt)"
        done=1
      }
      {print}
    ' "$CTRL" > "$CTRL.new" && mv "$CTRL.new" "$CTRL"
    echo "PublicController.php: Amount-Override gesetzt"
  else
    echo "PublicController.php: Amount-Override bereits vorhanden"
  fi

  # PER_HOUR Services: nach PER_DAY Zweig einfügen, falls noch nicht da
  if ! grep -q "PER_HOUR" "$CTRL"; then
    sed -i "/ServicePriceTypeEnum::PER_DAY/ a\            } elseif (\$service->price_type == ServicePriceTypeEnum::PER_HOUR) {\n                \$serviceAmount += (float) \$service->price * (int) (\$hours);" "$CTRL"
    echo "PublicController.php: PER_HOUR Services ergänzt"
  else
    echo "PublicController.php: PER_HOUR Services bereits vorhanden"
  fi

  # 'hours' in Response aufnehmen (einfach nach erstem return [ einsetzen)
  if ! grep -q "'hours'" "$CTRL"; then
    sed -i "0,/return[ \t]*\[/s//&\
            'hours' => (int) (\$hours),/" "$CTRL"
    echo "PublicController.php: hours ins Response aufgenommen"
  else
    echo "PublicController.php: hours bereits im Response"
  fi

else
  echo "WARNUNG: PublicController.php nicht gefunden"
fi

echo "==> artisan migrate & Cache-Clears…"
php "$ARTISAN" migrate --force || echo "WARNUNG: migrate fehlgeschlagen – bitte prüfen."
php "$ARTISAN" cache:clear || true
php "$ARTISAN" config:clear || true
php "$ARTISAN" view:clear || true

echo
echo "===================================================="
echo "Fertig (sed/awk). Backups: $BACKUP_DIR"
echo "Wenn etwas nicht greift, bitte $BACKUP_DIR und die geänderten Dateien diffen."
echo "===================================================="
