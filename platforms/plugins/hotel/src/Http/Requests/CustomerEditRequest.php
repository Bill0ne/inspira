<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Botble\Hotel\Models\Customer;

class CustomerEditRequest extends Request
{
    /**
     * Leeres Select => STANDARD, sonst trim + Uppercase.
     */
    protected function prepareForValidation(): void
    {
        $raw = $this->input('customer_category');

        $this->merge([
            'customer_category' => empty($raw)
                ? 'STANDARD'
                : strtoupper(trim((string) $raw)),
        ]);
    }

    /**
     * ID des aktuellen Kunden robust ermitteln – unabhängig vom Routen-Namen.
     */
    protected function currentCustomerId(): ?int
    {
        // 1) Standard: id
        if ($id = $this->route('id')) {
            return (int) $id;
        }

        // 2) Model-Binding: customer
        $routeCustomer = $this->route('customer');
        if ($routeCustomer instanceof Customer) {
            return (int) $routeCustomer->getKey();
        }
        if (is_numeric($routeCustomer)) {
            return (int) $routeCustomer;
        }

        // 3) Letzter Versuch: alle Parameter durchsuchen
        $route = $this->route();
        if (method_exists($route, 'parameters')) {
            foreach ($route->parameters() as $param) {
                if ($param instanceof Customer) {
                    return (int) $param->getKey();
                }
            }
        }

        return null;
    }

    public function rules(): array
    {
        $id = $this->currentCustomerId();
        $creating = empty($id);

        $rules = [
            'first_name' => ['required', 'max:60', 'min:2'],
            'last_name'  => ['required', 'max:60', 'min:2'],

            // Unique mit Ignore auf die eigene ID (funktioniert für Create & Edit)
            'email'      => [
                'required', 'max:60', 'min:6', 'email',
                Rule::unique('ht_customers', 'email')->ignore($id),
            ],

            'phone' => ['nullable', 'string', ...explode('|', BaseHelper::getPhoneValidationRule())],

            // darf leer sein; prepareForValidation setzt STANDARD
            'customer_category' => ['nullable', 'string', 'max:191'],
        ];

        // Passwort-Regeln: beim Erstellen zwingend, beim Edit optional per Toggle
        if ($creating) {
            $rules['password'] = 'required|string|min:6';
            $rules['password_confirmation'] = 'required|same:password';
        } elseif ($this->boolean('is_change_password')) {
            $rules['password'] = 'required|string|min:6';
            $rules['password_confirmation'] = 'required|same:password';
        }

        return $rules;
    }
}
