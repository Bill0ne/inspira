# Checkout Review: Courses & Customer Cards

## Scope
- Course booking checkout (`platform/plugins/courses`)
- Customer card purchase checkout (`platform/plugins/hotel`)

## Course Checkout Findings
1. **Session token handles all booking data without expiry**
   - Booking initialization stores the entire request payload under a random token in the session and reuses it later, but no TTL or integrity marker exists. A stale token can silently revive outdated course/session/price data if a user returns much later, potentially colliding with changed availability or pricing.【F:platform/plugins/courses/src/Http/Controllers/PublicController.php†L159-L332】
2. **Coupon usage can be over-counted on external redirects**
   - When an off-site payment checkout URL is returned, the system increments `total_used` for the coupon before the payment succeeds. Failed or abandoned payments therefore consume coupon quota incorrectly and can block future valid uses.【F:platform/plugins/courses/src/Http/Controllers/PublicController.php†L644-L677】
3. **Minimal payment verification on callbacks/finalization**
   - `CourseBookingService::processBooking` only maps payment status to booking status based on the latest payment record; it does not verify that the captured amount matches the booking totals or that the payment currency aligns with the order. This leaves room for underpayment or currency mismatches to mark bookings as completed.【F:platform/plugins/courses/src/Services/CourseBookingService.php†L24-L148】
4. **Customer card application risks double-handling**
   - Card usage is finalized separately after payment completion. If a zero-amount booking uses a card, a synthetic payment record is emitted and both `PAYMENT_ACTION_PAYMENT_PROCESSED` and `finalizeCustomerCardUsage` may run, opening the door to multiple finalize attempts if events race or callbacks repeat. Pending flags exist but the flow still triggers card consumption without explicit idempotency tokens.【F:platform/plugins/courses/src/Http/Controllers/PublicController.php†L562-L605】【F:platform/plugins/courses/src/Services/CourseBookingService.php†L151-L240】
5. **Seat availability only checked at commit time**
   - Seats are validated during checkout submission with a DB lock, but no temporary reservation exists between `postCourseBooking` (entry) and `postCourseCheckout`. High-concurrency scenarios could allow many users into the form even when limited seats remain, creating a race to the final step and a poor UX for those rejected late.【F:platform/plugins/courses/src/Http/Controllers/PublicController.php†L159-L416】

## Customer Card Purchase Findings
1. **Order reuse without state isolation**
   - A pending customer-card order is reused and its amount overwritten when a new checkout is started, but the controller never revalidates the template price or customer eligibility at that moment. Price changes or template deactivation between attempts could be bypassed by resurrecting the pending order with stale data.【F:platform/plugins/hotel/src/Http/Controllers/Front/CustomerDashboardController.php†L185-L220】
2. **Payment result trust without amount validation**
   - Payment handling completes or fails orders solely from payment status, without checking the paid amount/currency against the order. Underpayments could still mark an order as completed and mint a card.【F:platform/plugins/hotel/src/Services/CustomerCardPurchaseService.php†L71-L155】
3. **No retry-safety for finalize**
   - `finalizeOrder` is called directly on completed/zero-payment flows and via callbacks, but lacks idempotency guards beyond a simple status check. Duplicate callbacks could try to reassign or duplicate card issuance if status is toggled unexpectedly.【F:platform/plugins/hotel/src/Services/CustomerCardPurchaseService.php†L133-L155】
4. **User session state left dirty on failures**
   - On checkout errors, the controller marks the order failed but leaves `selected_payment_method` and `order_type` in the session, which can leak into subsequent checkouts and confuse gateway selection flows.【F:platform/plugins/hotel/src/Http/Controllers/Front/CustomerDashboardController.php†L222-L296】

## Optimization & Hardening Concept
- **Introduce checkout state expiration & integrity checks**: Timestamp session tokens and invalidate them after a short window; store a hash of course/session IDs and expected prices to detect stale or tampered state before rendering or submitting the checkout form.
- **Enforce amount & currency validation on callbacks**: Compare gateway payment amounts/currencies against booking or order totals before accepting completion for both courses and customer cards; reject or flag discrepancies for manual review.
- **Idempotent finalization**: Add unique idempotency keys (per booking/order) around card usage and card issuance so repeated callbacks or synthetic events cannot double-consume cards or mint duplicates.
- **Coupon usage accounting after confirmation**: Move coupon `total_used` increments to post-payment success hooks (or roll back on failure) to prevent quota drift when users abandon external payments.
- **Seat or inventory holds**: Implement short-lived reservations when entering checkout to throttle form access once seat counts are low, reducing late-stage failures.
- **Session cleanup on error paths**: Clear `selected_payment_method`, `order_type`, and related checkout artifacts when marking customer-card orders as failed to avoid cross-checkout leakage.
- **Price revalidation on reused orders**: When reusing pending customer-card orders, re-fetch the template and recompute the purchase price/eligibility before allowing payment to proceed.
