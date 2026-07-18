<?php

namespace Botble\Contact\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Contact\Enums\CustomFieldType;
use Botble\Contact\Events\SentContactEvent;
use Botble\Contact\Forms\Fronts\ContactForm;
use Botble\Contact\Http\Requests\ContactRequest;
use Botble\Contact\Http\Requests\RoomContactRequest;
use Botble\Contact\Models\Contact;
use Botble\Contact\Models\CustomField;
use Botble\Hotel\Models\Room;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PublicController extends BaseController
{
    public function postSendContact(ContactRequest $request)
    {
        return $this->sendContact($request);
    }

    public function postSendRoomRequest(RoomContactRequest $request)
    {
        $roomName = (string) Room::query()->whereKey($request->integer('room_id'))->value('name');
        $roomName = $roomName ?: (string) $request->input('room_name');

        $timezone = config('app.timezone') ?: 'UTC';
        $from = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $request->input('time_from'), $timezone);
        $to = CarbonImmutable::createFromFormat('Y-m-d\TH:i', $request->input('time_to'), $timezone);

        $customFields = array_filter([
            (string) __('Raum') => $roomName,
            (string) __('Firma') => trim((string) $request->input('company')),
            (string) __('Anzahl Personen') => (string) $request->integer('persons'),
            (string) __('Zeit von') => $from->translatedFormat('d.m.Y H:i'),
            (string) __('Zeit bis') => $to->translatedFormat('d.m.Y H:i'),
        ], fn ($value) => $value !== '' && $value !== null);

        $userMessage = trim((string) $request->input('content'));

        $request->merge([
            'room_name' => $roomName,
            'subject' => __('Raumanfrage: :room', ['room' => $roomName]),
            'content' => $userMessage,
        ]);

        return $this->sendContact($request, $customFields);
    }

    protected function sendContact(ContactRequest $request, array $extraCustomFields = [])
    {
        $blacklistDomains = setting('blacklist_email_domains');

        if ($blacklistDomains) {
            $emailDomain = Str::after(strtolower($request->input('email')), '@');

            $blacklistDomains = collect(json_decode($blacklistDomains, true))->pluck('value')->all();

            if (in_array($emailDomain, $blacklistDomains)) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(__('Your email is in blacklist. Please use another email address.'));
            }
        }

        $blacklistWords = trim(setting('blacklist_keywords', ''));

        if ($blacklistWords) {
            $content = strtolower($request->input('content'));

            $badWords = collect(json_decode($blacklistWords, true))
                ->filter(function ($item) use ($content) {
                    $matches = [];
                    $pattern = '/\b' . preg_quote($item['value'], '/') . '\b/iu';

                    return preg_match($pattern, $content, $matches, PREG_UNMATCHED_AS_NULL);
                })
                ->pluck('value')
                ->all();

            if (! empty($badWords)) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(__('Your message contains blacklist words: ":words".', ['words' => implode(', ', $badWords)]));
            }
        }

        do_action('form_extra_fields_validate', $request, ContactForm::class);

        $receiverEmails = null;

        if ($receiverEmailsSetting = setting('receiver_emails', '')) {
            $receiverEmails = trim($receiverEmailsSetting);
        }

        if ($receiverEmails) {
            $receiverEmails = collect(json_decode($receiverEmails, true))
                ->pluck('value')
                ->all();
        }

        if (is_array($receiverEmails)) {
            $receiverEmails = array_filter($receiverEmails);

            if (count($receiverEmails) === 1) {
                $receiverEmails = Arr::first($receiverEmails);
            }
        }

        try {
            $form = ContactForm::create();

            $form->saving(function (ContactForm $form) use ($receiverEmails, $extraCustomFields): void {
                $data = $form->getRequestData();

                if (Arr::has($data, 'contact_custom_fields')) {
                    $customFields = CustomField::query()
                        ->wherePublished()
                        ->with('options')
                        ->get();

                    $data['custom_fields'] = collect($data['contact_custom_fields'])
                        ->mapWithKeys(function ($item, $id) use ($customFields) {
                            $field = $customFields->firstWhere('id', $id);
                            $options = $field->options->firstWhere('value', $item);

                            if (! $field) {
                                return [];
                            }

                            $value = match ($field->type->getValue()) {
                                CustomFieldType::CHECKBOX => $item ? __('Yes') : __('No'),
                                CustomFieldType::RADIO, CustomFieldType::DROPDOWN => $options?->label,
                                default => $item,
                            };

                            return [$field->name => $value];
                        })->all();
                }

                if (! empty($extraCustomFields)) {
                    $data['custom_fields'] = array_merge(
                        (array) ($data['custom_fields'] ?? []),
                        $extraCustomFields
                    );
                }

                /**
                 * @var Contact $contact
                 */
                $contact = $form->getModel();

                $contact->fill($data)->save();

                event(new SentContactEvent($contact));

                $args = [];

                if ($contact->name && $contact->email) {
                    $args = ['replyTo' => [$contact->name => $contact->email]];
                }

                $emailHandler = EmailHandler::setModule(CONTACT_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'contact_name' => $contact->name,
                        'contact_subject' => $contact->subject,
                        'contact_email' => $contact->email,
                        'contact_phone' => $contact->phone,
                        'contact_address' => $contact->address,
                        'contact_content' => $contact->content,
                        'contact_custom_fields' => $data['custom_fields'] ?? [],
                    ]);

                // 'notice' geht bewusst an den Betreiber: sind keine receiver_emails
                // konfiguriert, wird explizit die Admin-Adresse aufgelöst (der zentrale
                // EmailHandler-Guard bricht targeted Sends mit leerem Empfänger sonst ab).
                $emailHandler->sendUsingTemplate('notice', $receiverEmails ?: get_admin_email()->all(), $args);

                $args = ['replyTo' => is_array($receiverEmails) ? Arr::first($receiverEmails) : $receiverEmails];

                $emailHandler->sendUsingTemplate('sender-confirmation', $contact->email, $args);
            }, true);

            return $this
                ->httpResponse()
                ->setMessage(__('Send message successfully!'));
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            BaseHelper::logError($exception);

            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__("Can't send message on this time, please try again later!"));
        }
    }

}
