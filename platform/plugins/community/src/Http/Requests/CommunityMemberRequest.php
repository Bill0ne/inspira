<?php

namespace Botble\Community\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Hotel\Models\Customer;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CommunityMemberRequest extends Request
{
    public function rules(): array
    {
        $member = $this->route('community');
        $memberId = is_object($member) ? $member->getKey() : $member;

        return [
            'customer_id' => [
                'required',
                'integer',
                'exists:ht_customers,id',
                Rule::unique('community_members', 'customer_id')->ignore($memberId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'string', 'max:255'],
            'short_description' => ['nullable', 'string', 'max:400'],
            'quote' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.unique' => trans('plugins/community::community.form.customer') . ': '
                . trans('core/base::notices.error'),
        ];
    }

    protected function prepareForValidation(): void
    {
        // Name & Bild aus dem gewählten Benutzer vorbefüllen, wenn der Admin sie
        // leer gelassen hat – beides bleibt editierbar (überschriebene Werte
        // werden respektiert).
        $customerId = (int) $this->input('customer_id');

        if (! $customerId) {
            return;
        }

        $needsName = ! trim((string) $this->input('name'));
        $needsPhoto = ! trim((string) $this->input('photo'));

        if (! $needsName && ! $needsPhoto) {
            return;
        }

        $customer = Customer::query()->find($customerId);

        if (! $customer) {
            return;
        }

        $merge = [];

        if ($needsName) {
            $merge['name'] = $customer->name;
        }

        if ($needsPhoto && ! empty($customer->getAttributes()['avatar'] ?? null)) {
            $merge['photo'] = $customer->getAttributes()['avatar'];
        }

        if ($merge) {
            $this->merge($merge);
        }
    }
}
