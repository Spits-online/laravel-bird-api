<?php

namespace Spits\Bird\Enums;

enum ChannelType
{
    case WHATSAPP;
    case SMS;
    case RCS;
    case EMAIL;
    case LINE;
    case TELEGRAM;
}
