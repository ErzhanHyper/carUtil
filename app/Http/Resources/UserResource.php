<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $role_title = match ($this->role) {
            'admin' => 'Админ',
            'callcenter' => 'Колцентр',
            'mio-manager' => 'Менеджер МИО',
            'moderator' => 'Модератор',
            'accountant' => 'Бухгалтер',
            'dealer-chief' => 'Руководитель дилерского центра (для отчетов)',
            'dealer-light' => 'Дилер (для погашений)',
            'operator' => 'Региональный менеджер',
            'operator-chief' => 'Руководитель регионального менеджера (для отчетов)',
            default => '',
        };

        return [
            'id' => $this->id,
            'login' => $this->login,
            'phone' => $this->phone,
            'email' => $this->email,
            'title' => $this->title,
            'role' => $this->role,
            'role_title' => $role_title,
            'bin' => $this->bin,
            'base' => $this->base,
            'for_docs' => $this->for_docs,
            'custom_1' => $this->custom_1,
            'custom_2' => $this->custom_2,
            'custom_3' => $this->custom_3,
            'custom_4' => $this->custom_4,
            'gp_id' => $this->gp_id,
            'factory' => $this->factory,
            'manufacture' => $this->manufacture,
            'region' => $this->region,
            'active' => ($this->password === 'disabled') ? false : true
        ];
    }
}
