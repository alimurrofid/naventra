<?php

namespace App\Modules\System\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $user = auth('api')->user();
        
        $menu = [
            [
                'label' => 'Dashboard',
                'icon' => 'pi pi-home',
                'route' => '/dashboard',
                'permission' => 'dashboard.view'
            ],
            [
                'label' => 'Accounting',
                'icon' => 'pi pi-wallet',
                'children' => [
                    [
                        'label' => 'Master',
                        'icon' => 'pi pi-folder',
                        'children' => [
                            [
                                'label' => 'Chart of Accounts',
                                'icon' => 'pi pi-list',
                                'route' => '/accounting/m_coa',
                                'permission' => 'accounting.m_coa.read'
                            ]
                        ]
                    ],
                    [
                        'label' => 'Transactions',
                        'icon' => 'pi pi-receipt',
                        'children' => [
                            [
                                'label' => 'Kas/Bank Keluar',
                                'icon' => 'pi pi-money-bill',
                                'route' => '/accounting/t_kbk',
                                'permission' => 'accounting.t_kbk.read'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return response()->json($menu);
    }
}
