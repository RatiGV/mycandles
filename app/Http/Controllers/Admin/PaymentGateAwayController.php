<?php

namespace App\Http\Controllers\Admin;

use App\Models\PaymentGateaway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentGateAwayController extends BaseController
{
    private function gatewayDefinitions(): array
    {
        return [
            'bog_online' => [
                'db_name' => 'bog_online',
                'bank' => 'bog',
                'type' => 'online',
                'label' => 'BOG Online Payment',
            ],
            'bog_installment' => [
                'db_name' => 'bog_installment',
                'bank' => 'bog',
                'type' => 'installment',
                'label' => 'BOG Installment',
            ],
            'tbc_online' => [
                'db_name' => 'tbc_online',
                'bank' => 'tbc',
                'type' => 'online',
                'label' => 'TBC Online Payment',
            ],
            'tbc_installment' => [
                'db_name' => 'tbc_installment',
                'bank' => 'tbc',
                'type' => 'installment',
                'label' => 'TBC Installment',
            ],
            'flitt_online' => [
                'db_name' => 'flitt_online',
                'bank' => 'flitt',
                'type' => 'online',
                'label' => 'Flitt Online Payment',
            ],
        ];
    }

    public function index()
    {
        $definitions = $this->gatewayDefinitions();
        $dbNames = array_column($definitions, 'db_name');
        $gatewaysByDbName = PaymentGateaway::whereIn('name', $dbNames)->get()->keyBy('name');

        foreach ($definitions as $definition) {
            $dbName = $definition['db_name'];
            if (!$gatewaysByDbName->has($dbName)) {
                $gatewaysByDbName[$dbName] = PaymentGateaway::create([
                    'name' => $dbName,
                    'status' => 0,
                    'client' => null,
                    'secret' => null,
                    'merchant_key' => null,
                ]);
            }
        }

        $gateways = [];
        foreach ($definitions as $key => $definition) {
            $gateways[$key] = $gatewaysByDbName[$definition['db_name']];
        }

        $banks = [
            'bog' => [
                'title' => 'Bank of Georgia',
                'logo' => '/assets/images/bog_eng_vertical.png',
                'sections' => [
                    'online' => __('admin.Online Payment'),
                    'installment' => __('admin.Installment Payment'),
                ],
                'gateways' => [
                    'online' => 'bog_online',
                    'installment' => 'bog_installment',
                ],
            ],
            'tbc' => [
                'title' => 'TBC Bank',
                'logo' => '/assets/images/Tbc-logo-ka_GE.svg',
                'sections' => [
                    'online' => __('admin.Online Payment'),
                    'installment' => __('admin.Installment Payment'),
                ],
                'gateways' => [
                    'online' => 'tbc_online',
                    'installment' => 'tbc_installment',
                ],
            ]
        ];

        return view('Administrator.payment-gateaway.index', compact('gateways', 'banks'));
    }

    public function update(Request $request)
    {
        $definitions = $this->gatewayDefinitions();

        $validated = $request->validate([
            'gateways' => ['required', 'array'],
            'gateways.*.status' => ['required', 'boolean'],
            'gateways.*.client' => ['nullable', 'string', 'max:255'],
            'gateways.*.secret' => ['nullable', 'string', 'max:255'],
            'gateways.*.merchant_key' => ['nullable', 'string', 'max:255'],
        ]);

        $payload = $validated['gateways'];

        DB::transaction(function () use ($payload, $definitions) {
            foreach ($definitions as $key => $definition) {
                if (!array_key_exists($key, $payload)) {
                    continue;
                }

                $data = $payload[$key];
                $dbName = $definition['db_name'];

                $gateway = PaymentGateaway::firstOrCreate(
                    ['name' => $dbName],
                    ['status' => 0, 'client' => null, 'secret' => null, 'merchant_key' => null]
                );

                $gateway->status = (int) ($data['status'] ?? 0);
                $gateway->client = ($data['client'] ?? '') !== '' ? $data['client'] : null;
                $gateway->merchant_key = ($data['merchant_key'] ?? '') !== '' ? $data['merchant_key'] : null;

                $secret = $data['secret'] ?? null;
                if ($secret !== null && $secret !== '') {
                    $gateway->secret = $secret;
                }

                $gateway->save();
            }
        });

        return redirect()->back()->with('success', true);
    }
}
