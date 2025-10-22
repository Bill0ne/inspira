<?php

namespace Botble\Hotel\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;
use Botble\Hotel\Models\Customer;

class CustomerEditRequest extends Request
{
    protected function currentCustomerId(): ?int
    {
        if ($id = $this->route('id')) {
            return (int) $id;
        }

        $routeCustomer = $this->route('customer');
        if ($routeCustomer instanceof Customer) {
            return (int) $routeCustomer->getKey();
        }
        if (is_numeric($routeCustomer)) {
            return (int) $routeCustomer;
        }

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
            'email'      => [
                'required', 'max:60', 'min:6', 'email',
                Rule::unique('ht_customers', 'email')->ignore($id),
            ],

            'phone' => ['nullable', 'string', ...explode('|', BaseHelper::getPhoneValidationRule())],
            'customer_category_id' => ['nullable'],
        ];
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
