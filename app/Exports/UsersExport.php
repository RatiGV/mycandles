<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $users = User::whereNotNull('id');

        $first_name = $this->request->first_name;
        $last_name = $this->request->last_name;
        $phone = $this->request->phone;
        $email = $this->request->email;
        $from = $this->request->from;
        $to = $this->request->to;

        if ($first_name) {
            $users->where('first_name', 'like', "%$first_name%");
        }

        if ($last_name) {
            $users->where('last_name', 'like', "%$last_name%");
        }

        if ($phone) {
            $users->where('phone', 'like', "%$phone%");
        }

        if ($email) {
            $users->where('email', 'like', "%$email%");
        }

        if ($from) {
            $users->where('created_at', '>', $from);
        }

        if ($to) {
            $users->where('created_at', '<', $to);
        }

        return $users->select([
            'first_name',
            'last_name',
            'phone',
            'email',
        ])->orderby('id', 'DESC')->get();
    }

    public function headings(): array
    {
        return [
            trans('admin.first_name'),
            trans('admin.last_name'),
            trans('admin.phone'),
            trans('admin.email'),
        ];
    }
}
