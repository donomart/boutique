<?php

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ServiceMail extends AbstractController
{
    public function sendMail($dest_mail, $dest_name, $content)
    {
        $mj = new Client(
            $this->getParameter('mailjet_public_key'),
            $this->getParameter('mailjet_private_key'),
            true,
            ['version' => 'v3.1']
        );
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => $this->getParameter('webmaster_mail'),
                        'Name' => "Webmaster"
                    ],
                    'To' => [
                        [
                            'Email' => $dest_mail,
                            'Name' => $dest_name
                        ]
                    ],
                    'TemplateID' => 3708692,
                    'TemplateLanguage' => true,
                    'Variables' => [
                        'content' => $content
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && dd($response->getData());
    }
}
