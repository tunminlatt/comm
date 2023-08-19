<?php

namespace App\Http\Resources\API\v1;

use Illuminate\Http\Resources\Json\JsonResource;

class StationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $image = resolve('App\Helpers\Image');
        $id = $this->id;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $image->getByApi('stations/'. $id),
            'profile_image' => $image->getByApi('stations/profile/'. $id),
            'programme_count' => $this->programmes_count,
            'contact' => [
                'phone' => $this->phone,
                'facebook_link' => $this->facebook_link,
                'messenger_link' => $this->messenger_link,
                'email_link' => $this->email,
                'signal' => $this->signal,
                'viber' => $this->viber,
                'whats_app' => $this->whats_app
            ],
            'is_active' => isset($this->deleted_at) ? false : true,
            'public_key' => '-----BEGIN PUBLIC KEY-----
            MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA38Wa14BwkPcw8ljH6JkL
            e74k6HDRpJh/eFPF/b64TW++jMwao3EM5LhBwap8uL/BsSKypith2ZeFBOTqH6H1
            PubO8r/8PDqWzRHMEcSeJ+qzOPPrE5mYZf4yayiVo3izWrsa2Sw/nKBY06eItrty
            a1yJp9PLc7tPk1XpDlZHtjg2/AmI+Er7SuRlTzwZMIvvXU44foU8aM/LJxyxvVqi
            KjjMfVcQ5YkmEWW5rPffSlV7C+zXCWQKQyosv2fSW4GmwH+5UKzQcon/Aza/nRPG
            Bawn9agoRZ6oTmydZZgZGujGpqscwu0f4JvpTtp6Cv15UEb0rsootIb+RdvOPPD0
            gwIDAQAB
            -----END PUBLIC KEY-----',
            'is_public' => $this->is_public ? true: false,
            'updated_at' => $this->updated_at,
        ];
    }
}
