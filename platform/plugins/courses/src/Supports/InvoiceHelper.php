<?php

namespace Botble\Courses\Supports;

use Barryvdh\DomPDF\PDF as PDFHelper;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Supports\Pdf;
use Botble\Hotel\Enums\InvoiceStatusEnum;
use Botble\Courses\Models\CourseBooking;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Models\Invoice;
use Botble\Hotel\Models\InvoiceItem;
use Botble\Media\Facades\RvMedia;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Response;

class InvoiceHelper
{
    public function store(CourseBooking $courseBooking): Invoice
    {
        if ($courseBooking->invoice()->exists()) {
            return $courseBooking->invoice;
        }

        $bookingInformation = $courseBooking->address;

        $invoiceData = [
            'reference_id' => $courseBooking->getKey(),
            'reference_type' => CourseBooking::class,
            'customer_id' => $courseBooking->customer_id,
            'customer_name' => $bookingInformation->first_name . $bookingInformation->last_name ??  $courseBooking->customer->first_name . $courseBooking->customer->last_name,
            'customer_email' => $bookingInformation->email ?? $courseBooking->customer->email,
            'customer_phone' => $bookingInformation->phone ?? $courseBooking->customer->phone,
            'customer_address' => $bookingInformation->full_address ?? '',
            'payment_id' => null,
            'status' => InvoiceStatusEnum::COMPLETED,
            'paid_at' => Carbon::now(),
            'sub_total' => $courseBooking->sub_total,
            'tax_amount' => $courseBooking->tax_amount,
            'discount_amount' => $courseBooking->coupon_amount ?: 0,
            'amount' => $courseBooking->amount,
            'description' => $courseBooking->requests,
        ];

        if (is_plugin_active('payment')) {
            switch ($courseBooking->payment->status) {
                case PaymentStatusEnum::COMPLETED:
                    $invoiceData['status'] = InvoiceStatusEnum::COMPLETED;

                    break;
                case PaymentStatusEnum::PENDING:
                    $invoiceData['status'] = InvoiceStatusEnum::PENDING;

                    break;
                case PaymentStatusEnum::FAILED:
                case PaymentStatusEnum::FRAUD:
                case PaymentStatusEnum::REFUNDED:
                case PaymentStatusEnum::REFUNDING:
                    $invoiceData['status'] = InvoiceStatusEnum::CANCELED;

                    break;
            }

            $invoiceData = array_merge($invoiceData, [
                'payment_id' => $courseBooking->payment->id,
                'paid_at' => $courseBooking->payment->status == PaymentStatusEnum::COMPLETED ? Carbon::now() : null,
            ]);
        }

        $invoice = new Invoice($invoiceData);
        $invoice->created_at = $courseBooking->created_at;



        $invoice->save();

        if ($course = $courseBooking->course) {
            $invoice->items()->create([
                'name' => $course->name,
                'description' => null,
                'qty' => 1,
                'sub_total' => $course->price,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'amount' => $course->price,
            ]);
        }

        return $invoice;
    }

    public function getVariables(): array
    {
        return [
            'invoice.*' => __('Invoice information from database, ex: invoice.code, invoice.amount, ...'),
            'account.*' => __('Bill payment user account information, ex: account.name, account.email, ...'),
            'payment_method' => __('Payment method'),
            'payment_status' => __('Payment status'),
            'payment_description' => __('Payment description'),
            'settings.using_custom_font_for_invoice' => __('Check site is using custom font for invoice or not'),
            'settings.font_family' => __('The font family of invoice template'),
            'settings.enable_invoice_stamp' => __('Check have enabled the invoice stamp'),
            'settings.company_name_for_invoicing' => __('The company name of invoice'),
            'settings.company_address_for_invoicing' => __('The company address of invoice'),
            'settings.company_email_for_invoicing' => __('The company email of invoice'),
            'settings.company_phone_for_invoicing' => __('The company phone number of invoice'),
        ];
    }

    protected function getDataForInvoiceTemplate(Invoice $invoice): array
    {

        return [
            'invoice' => $invoice,
            'logo_full_path' => RvMedia::getImageUrl(
                setting('hotel_company_logo_for_invoicing') ?: theme_option('logo')
            ),
            'site_title' => theme_option('site_title'),
            'customer' => $invoice->customer,
            'payment_method' => $this->resolvePaymentMethodLabel(
                $invoice->payment,
                $invoice->reference?->payment_method
            ),
            'payment_status' => $invoice->payment?->status->label(),
            'payment_description' => ($invoice->payment?->payment_channel == PaymentMethodEnum::BANK_TRANSFER && $invoice->payment?->status == PaymentStatusEnum::PENDING)
                ? BaseHelper::clean(get_payment_setting('description', $invoice->payment?->payment_channel))
                : null,
            'settings' => [
                'using_custom_font_for_invoice' => setting('hotel_using_custom_font_for_invoice', false),
                'font_family' => setting('hotel_using_custom_font_for_invoice', 0) == 1
                    ? setting('hotel_invoice_font_family', '')
                    : 'DejaVu Sans',
                'enable_invoice_stamp' => setting('hotel_enable_invoice_stamp', true),
                'company_name_for_invoicing' => setting('hotel_company_name_for_invoicing') ?: theme_option(
                    'site_title'
                ),
                'company_address_for_invoicing' => setting('hotel_company_address_for_invoicing'),
                'company_email_for_invoicing' => setting('hotel_company_email_for_invoicing'),
                'company_phone_for_invoicing' => setting('hotel_company_phone_for_invoicing'),
                'hotel_invoice_footer' => apply_filters('hotel_invoice_footer', null, $invoice),
            ],
        ];
    }

    protected function resolvePaymentMethodLabel(?Payment $payment, mixed $referenceMethod): mixed
    {
        $paymentMethod = $payment?->payment_channel;

        if ($paymentMethod instanceof PaymentMethodEnum) {
            return $paymentMethod->label();
        }

        if ($referenceMethod instanceof PaymentMethodEnum) {
            return $referenceMethod->label();
        }

        if (is_string($referenceMethod) && $referenceMethod === 'customer_card') {
            return trans('plugins/payment::payment.methods.customer_card');
        }

        return $referenceMethod;
    }

    public function getDataForPreview(): Invoice
    {
        $invoice = new Invoice([
            'code' => 'INV-1',
            'status' => InvoiceStatusEnum::PENDING,
        ]);

        $items = [];

        foreach (range(1, 5) as $i) {
            $amount = rand(10, 1000);
            $qty = rand(1, 10);

            $items[] = new InvoiceItem([
                'name' => "Item $i",
                'description' => "Description of item $i",
                'amount' => $amount,
                'qty' => $qty,
            ]);

            $invoice->amount += $amount * $qty;
            $invoice->sub_total = $invoice->amount;
        }

        $payment = new Payment([
            'payment_channel' => PaymentMethodEnum::BANK_TRANSFER,
            'status' => PaymentStatusEnum::PENDING,
        ]);

        $account = new Customer([
            'company' => 'My Company',
            'first_name' => 'John',
            'last_name' => 'Smith',
            'email' => 'example@mail.com',
            'phone' => '0123456789',
        ]);

        $invoice->setRelation('payment', $payment);
        $invoice->setRelation('items', $items);
        $invoice->setRelation('account', $account);

        return $invoice;
    }

    public function downloadInvoice($invoice): Response
    {
        return $this->makeInvoice($invoice)->download('invoice-' . $invoice->code . '.pdf');
    }

    public function streamInvoice(Invoice $invoice): Response
    {
        return $this->makeInvoice($invoice)->stream();
    }

    public function makeInvoice(Invoice $invoice): PDFHelper
    {

        $data = $this->getDataForInvoiceTemplate($invoice);



        return (new Pdf())
            ->templatePath($this->getInvoiceTemplatePath())
            ->destinationPath($this->getInvoiceTemplateCustomizedPath())
            ->supportLanguage($this->getLanguageSupport())
            ->paperSizeA4()
            ->data($data)
            ->twigExtensions([new TwigExtension()])
            ->compile();
    }

    public function getInvoiceTemplate(): string
    {
        return (new Pdf())->getContent($this->getInvoiceTemplatePath(), $this->getInvoiceTemplateCustomizedPath());
    }

    public function getInvoiceTemplatePath(): string
    {
        return plugin_path('hotel/resources/templates/invoice.tpl');
    }

    public function getInvoiceTemplateCustomizedPath(): string
    {
        return storage_path('app/templates/invoice.tpl');
    }

    public function getLanguageSupport(): string
    {
        $languageSupport = setting('hotel_invoice_language_support');

        if (! empty($languageSupport)) {
            return $languageSupport;
        }

        if (setting('hotel_invoice_support_arabic_language', false)) {
            return 'arabic';
        }

        if (setting('hotel_invoice_support_bangladesh_language', false)) {
            return 'bangladesh';
        }

        return '';
    }
}
