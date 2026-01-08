<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Events;

use Modules\Core\Events\DomainEvent;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;

/**
 * Integration event - template has been rendered with variables and watermark.
 * Crosses from Templates module to Deliveries module.
 */
final class TemplateRenderedIntegration extends DomainEvent implements IntegrationEvent
{
    public function __construct(
        public readonly string $deliveryId,
        public readonly string $templateId,
        public readonly string $workspaceId,
        public readonly string $subject,
        public readonly string $htmlBody,
        public readonly ?string $textBody,
    ) {
        parent::__construct($this->deliveryId, 'NotificationDelivery');
    }

    public function key(): string
    {
        return 'template.rendered';
    }

    public function toPayload(): array
    {
        return [
            'delivery_id' => $this->deliveryId,
            'template_id' => $this->templateId,
            'workspace_id' => $this->workspaceId,
            'subject' => $this->subject,
            'html_body' => $this->htmlBody,
            'text_body' => $this->textBody,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            deliveryId: $payload['delivery_id'],
            templateId: $payload['template_id'],
            workspaceId: $payload['workspace_id'],
            subject: $payload['subject'],
            htmlBody: $payload['html_body'],
            textBody: $payload['text_body'],
        );
    }
}
