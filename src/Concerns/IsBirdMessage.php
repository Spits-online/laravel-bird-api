<?php

namespace Spits\Bird\Concerns;

use Spits\Bird\Models\Contact;

interface IsBirdMessage
{
    public function toContact(Contact $contact);
}
