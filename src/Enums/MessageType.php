<?php

namespace Spits\Bird\Enums;

enum MessageType: string
{
    case TEXT = 'text';
    case IMAGE = 'image';
    case FILE = 'file';
    case LIST = 'list';
    case CAROUSEL = 'carousel';
    case TEMPLATE = 'template';
}
