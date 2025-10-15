<?php

namespace App\Enums;

enum UserManagementAction: string
{
    case ImpersonationStarted = 'impersonation_started';
    case ImpersonationStopped = 'impersonation_stopped';
    case RoleUpdated = 'role_updated';
    case QueuedCommand = 'queued_command';
}
