<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Events;

use Modules\Core\Events\DomainEvent;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;
use Modules\Workflows\Domain\Models\WorkflowExecution;

/**
 * Integration event for channel delivery requests.
 *
 * This event crosses bounded context boundaries, allowing the Channels module
 * to handle delivery without coupling to the Workflows domain.
 */
final class ChannelDeliveryRequestedIntegration extends DomainEvent implements IntegrationEvent
{
    public function __construct(
        public readonly string  $executionId,
        public readonly string  $workspaceId,
        public readonly string  $workflowId,
        public readonly string  $receivedEventId,
        public readonly string  $channel,
        public readonly ?string $providerId,
        public readonly ?string $templateId,
        public readonly ?string $recipient,
        public readonly array   $context,
        public readonly string  $eventName,
        public readonly ?string $userId,
    ) {
        parent::__construct($executionId, WorkflowExecution::class);
    }

    public function key(): string
    {
        return 'channel.delivery_requested';
    }

    public function toPayload(): array
    {
        return [
            'execution_id' => $this->executionId,
            'workspace_id' => $this->workspaceId,
            'workflow_id' => $this->workflowId,
            'event_id' => $this->receivedEventId,
            'channel' => $this->channel,
            'provider_id' => $this->providerId,
            'template_id' => $this->templateId,
            'recipient' => $this->recipient,
            'context' => $this->context,
            'event_name' => $this->eventName,
            'user_id' => $this->userId,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            executionId: $payload['execution_id'],
            workspaceId: $payload['workspace_id'],
            workflowId: $payload['workflow_id'],
            receivedEventId: $payload['event_id'],
            channel: $payload['channel'],
            providerId: $payload['provider_id'] ?? null,
            templateId: $payload['template_id'],
            recipient: $payload['recipient'],
            context: $payload['context'],
            eventName: $payload['event_name'],
            userId: $payload['user_id'],
        );
    }
}
