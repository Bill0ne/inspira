# Customer card finalize debug notes

## Current log availability
The local repository does not contain runtime application logs (for example `storage/logs/laravel.log` is missing), so no concrete booking-level log lines could be reviewed in this environment. Please provide the Laravel log from the environment where the booking was created so we can reference the recorded `[CustomerCardFinalize]` entries.

## How the finalize call behaves
* The centralized finalize call now runs when the booking information page is rendered (`PublicController::checkoutCourseSuccess`).【platform/plugins/courses/src/Http/Controllers/PublicController.php†L606-L633】
* For paid bookings, finalize skips and marks the booking as `customer_card_finalize_pending` when the related payment is not `COMPLETED` yet.【platform/plugins/courses/src/Services/CourseBookingService.php†L152-L163】
* Finalization will also stop if no payment exists for a paid booking.【platform/plugins/courses/src/Services/CourseBookingService.php†L172-L182】
* Additional early exits occur when no `customer_card_units_used` are stored or the assigned card cannot be found/mismatches the customer.【platform/plugins/courses/src/Services/CourseBookingService.php†L190-L220】【platform/plugins/courses/src/Services/CourseBookingService.php†L245-L279】

## What to look for in the logs
Search for `[CustomerCardFinalize]` entries around the transaction ID. Common reasons for skipping consumption include:
1. Payment pending: "Payment not completed yet, skipping" (booking stays pending until a completed payment event retriggers finalize).
2. Missing payment on paid booking: "Booking missing completed payment, skipping".
3. Missing units: "missing units_used, skipping".
4. Card mismatch or insufficient units: "Card not found or mismatched" or "requires X units but card ... has Y remaining".

Collecting these lines from the production log will pinpoint why the units were not reduced for the provided booking screenshot.
