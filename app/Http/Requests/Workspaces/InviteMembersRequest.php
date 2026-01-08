<?php

declare(strict_types=1);

namespace App\Http\Requests\Workspaces;

use Illuminate\Foundation\Http\FormRequest;

final class InviteMembersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'invitations' => ['required', 'array', 'min:1'],
            'invitations.*.email' => ['required', 'email'],
            'invitations.*.role' => ['required', 'string', 'in:administrator,collaborator'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'invitations.*.email' => 'invitation email',
            'invitations.*.role' => 'member role',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'invitations.required' => 'Please provide at least one invitation.',
            'invitations.min' => 'Please provide at least one invitation.',
            'invitations.*.email.required' => 'Each invitation must have an email address.',
            'invitations.*.email.email' => 'Please provide a valid email address.',
            'invitations.*.role.required' => 'Each invitation must have a role assigned.',
            'invitations.*.role.in' => 'The role must be either administrator or collaborator.',
        ];
    }
}
