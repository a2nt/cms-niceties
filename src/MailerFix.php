<?php

namespace A2nt\CMSNiceties;

use RuntimeException;
use SilverStripe\Control\Director;
use SilverStripe\Control\Email\Email;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email as MimeEmail;

class MailerFix extends Email
{
    private $args;

    public function __construct(
        $from = null,
        $to = null,
        $subject = null,
        $body = null,
        $cc = null,
        $bcc = null,
        $returnPath = null
    ) {
        $this->args = func_get_args();
        parent::__construct($from, $to, $subject, $body, $cc, $bcc, $returnPath);
    }

    private static function convertVars($mails)
    {
        return is_array($mails) ? implode(',', $mails) : $mails;
    }

    private function loadDetails()
    {
        $fields = [
            'From',
            'To',
            'Subject',
            'Body',
            'CC',
            'BCC',
            'ReturnPath',
        ];

        $i = 0;
        foreach ($fields as $f) {
            $func = 'get'.$f;

            $v = $this->$func();

            if ($v) {
                $this->args[$i] = is_array($v) ? array_keys($v) : $v;
            }

            $i++;
        }
    }

    public function send()
    {
        $transport = Transport::fromDsn('native://default');//smtp://localhost
        $mailer = new Mailer($transport);

        $this->loadDetails();
        $this->render();

        $body = $this->getBody();
        $to = self::convertVars($this->args[1]);

        $email = (new MimeEmail())
            ->to($to)
            //->priority(Email::PRIORITY_HIGH)
            ->subject($this->args[2])
            ->text(strip_tags($body, []))
            ->html($body);

        $from = self::convertVars($this->args[0]);
        $from = $from ? $from : self::getDefaultFrom();
        if ($from) {
            $email->from($from);
        }

        $cc = isset($this->args[4]) ? self::convertVars($this->args[4]) : null;
        if ($cc) {
            $email->cc($cc);
        }

        $bcc = isset($this->args[5]) ? self::convertVars($this->args[5]) : null;
        if ($bcc) {
            $email->bcc($bcc);
        }

        $reply = isset($this->args[6]) ? self::convertVars($this->args[6]) : null;
        if ($reply) {
            $email->replyTo($reply);
        }

        return $mailer->send($email);
        //parent::send();
    }

    private function getDefaultFrom(): string
    {
        // admin_email can have a string or an array config
        // https://docs.silverstripe.org/en/4/developer_guides/email/#administrator-emails
        $adminEmail = Email::config()->get('admin_email');
        if (is_array($adminEmail) && count($adminEmail ?? []) > 0) {
            $defaultFrom = array_keys($adminEmail)[0];
        } else {
            if (is_string($adminEmail)) {
                $defaultFrom = $adminEmail;
            } else {
                $defaultFrom = '';
            }
        }
        if (empty($defaultFrom)) {
            $host = Director::host();
            if (empty($host)) {
                throw new RuntimeException('Host not defined');
            }
            $defaultFrom = sprintf('noreply@%s', $host);
        }

        return $defaultFrom;
    }
}
