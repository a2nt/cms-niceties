<?php

namespace A2nt\CMSNiceties\Forms;

use SilverStripe\Security\MemberAuthenticator\MemberAuthenticator;

class Authenticator extends MemberAuthenticator
{
    public function getLoginHandler($link)
    {
        return LoginHandler::create($link, $this);
    }
}
